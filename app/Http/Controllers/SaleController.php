<?php

namespace App\Http\Controllers;

use App\Helpers\SMS;
use App\Http\Requests\SaleStoreRequest;
use App\Models\BankAccount;
use App\Models\Showroom;
use App\Models\Cash;
use App\Models\Party;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Rakibhstu\Banglanumber\NumberToBangla;

class SaleController extends Controller
{
    private $sale;
    protected string $permission_for = 'sale';

    public $sender_id, $api_key;
    public function __construct()
    {
        $this->sender_id = env('SMS_SENDER_ID');
        $this->api_key = env('SMS_API_KEY');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $this->hasPermission('view');

        $sales = Sale::query();
        if (request()->search) {
            if (request()->from_date) {
                $sales = $sales->whereBetween('date', [request()->from_date, request()->to_date]);
            }
            if (request()->invoice_no) {
                $sales = $sales->where('invoice_no', request()->invoice_no);
            }
        }
        $sales = $sales->addPartyName()
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->paginate(30)
            ->withQueryString();
        return Inertia::render('Sale/Index', [
            'sales' => $sales,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        $this->hasPermission('create');

        return Inertia::render('Sale/Sale');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SaleStoreRequest $request)
    {
        $this->hasPermission('create');

//        return $request->all();
        $sale_date = $request->validated();
        $sale_date['user_id'] = \Auth::user()->id;
        $sale_date['invoice_no'] = 'Invoice-' . str_pad(Sale::max('id') + 1, 8,0, STR_PAD_LEFT);

        DB::transaction(function () use($sale_date, $request) {
            if ($request->customer_type == 'oldCustomer') {
                $customer = Party::findOrFail($request->party_id);
                $sale_date['party_id'] = $request->party_id;
            }
            else{
                $customer_data = [
                    'name' => $request->party_name,
                    'phone' => $request->party_phone,
                    'address' => $request->party_address,
                    'genus' => 'customer',
                ];
                $customer = Party::create($customer_data);
                $sale_date['party_id'] = $customer->id;
            }

            $this->sale = Sale::create($sale_date);
            $sale = $this->sale;

            $this->updateCustomerBalance($request, $customer);
            $this->salePayment($request);
            $this->saleDetails($request, $sale);
            $this->smsDetails($sale, $request);
        });
        if ($this->sale) {
            return redirect()->route('sale.show', $this->sale->id);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Inertia\Response
     */
    public function show($id)
    {
        $this->hasPermission('show');

        $numto = new NumberToBangla();

        $sale = Sale::with('party.mokam', 'details.product.unit', 'showroom')->findOrFail($id);
        $sale['total_in_word'] = $numto->bnMoney($sale->grand_total);
        return Inertia::render('Sale/Show', [
            'sale' => $sale,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Inertia\Response
     */
    public function edit($id)
    {
        $this->hasPermission('update');
        $sale = Sale::with('details.product.unit')->findOrFail($id);
        return Inertia::render('Sale/Sale', [
            'sale' => $sale,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SaleStoreRequest $request, $id)
    {
        $this->hasPermission('update');
        DB::transaction(function () use($request, $id) {
            $oldSale = Sale::findOrFail($id);
//          <!-- update previous record -->
            $this->removePreviousProductUpdateQuantity($oldSale);
            $this->updateCashBankBalance($oldSale);
            $this->updateOldCustomerBalance($oldSale);
//          <!-- update previous record -->

            $sale_date = $request->validated();
            $customer = Party::findOrFail($request->party_id);
            $sale_date['party_id'] = $request->party_id;

            $this->sale = $oldSale;
            if ($request->party_id == $oldSale->party_id){
                $increment_balance = (($request->subtotal + $request->labour_cost + $request->transport_cost) - $request->discount) - $request->paid;
                $customer->increment('balance', $increment_balance);
            }else{
                $this->updateCustomerBalance($request, $customer);
            }
            $oldSale->update($sale_date);
            $this->salePayment($request);
            $this->saleDetails($request, $oldSale);
        });

        if ($this->sale) {
            return redirect()->route('sale.show', $this->sale->id);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->hasPermission('delete');
        DB::transaction(function () use($id) {
            $oldSale = Sale::findOrFail($id);
//          <!-- update previous record -->
            $this->removePreviousProductUpdateQuantity($oldSale);
            $this->updateCashBankBalance($oldSale);
            $this->updateOldCustomerBalance($oldSale);
//          <!-- update previous record -->

            $this->sale = $oldSale->delete();
        });

        if ($this->sale) {
            return redirect()->back();
        }
    }

    /**
     * update customer balance when sale create or delete
     * @param $request
     * @param $customer
     * @return void
     */
    public function updateCustomerBalance($request, $customer)
    {
        if ($request->due > 0) {
            $customer->balance = $request->due;
        }else{
            $customer->balance = (-1 * $request->customer_balance);
        }
        $customer->save();
    }

    /**
     * update cash or bank balance when sale create or update
     * @param $request
     * @return void
     */
    public function salePayment($request)
    {
        if ($request->payment_type == 'cash'){
            Cash::findOrFail($request->cash_id)->increment('balance', $request->paid);
        }else{
            BankAccount::findOrFail($request->bank_account_id)->increment('balance', $request->paid);
        }
    }

    /**
     * store sale details product
     * @param $request
     * @param $sale
     * @return void
     */
    public function saleDetails($request, $sale)
    {
        foreach ($request->products as $product) {
            $_product = Product::findOrFail($product['id']);
            $sale_details_data = [
                'product_id' => $product['id'],
                'showroom_id' => $request->showroom_id,
                'date' => $sale->date,
                'purchase_price' => $product['purchase_price'],
                'sale_price' => $product['sale_price'],
                'quantity' => $product['quantity'],
                'quantity_in_unit' => $product['quantity_in_unit'],
                'quantity_for_price' => $product['quantity_for_price'],
                'line_total' => $product['line_total'],
            ];

            // create sale details
            $sale->details()
                ->create($sale_details_data);

            // decrement product quantity
            $showroom = $_product->showrooms()
                ->find($request->showroom_id);

            // decrement quantity from showroom
            $showroom->stock->decrement('quantity', $product['quantity']);
        }
    }

    /**
     * remove sales product & update quantity
     * @param $sale
     * @return void
     */
    public function removePreviousProductUpdateQuantity($sale)
    {
        if (count($sale->details) > 0) {
            foreach ($sale->details as $detail) {
                $_quantity = $detail->quantity;
                // get product
                $product = Product::findOrFail($detail->product_id);
                // get showroom
                $_showroom = $product->showrooms->where('id', $sale->showroom_id)->first();
                if($_showroom) {
                    //get exists quantity
                    $previous_quantity = $_showroom->stock->quantity;
                    //update stocks
                    $product->showrooms()->updateExistingPivot($sale->showroom_id, [
                        'quantity' => $previous_quantity + $_quantity,
                    ]);
                }else{ // no previous showroom exists
                    //add new stock in for products
                    $product->showrooms()->attach([
                        $sale->showroom_id =>  [
                            'quantity' => $_quantity,
                            'average_purchase_price' => $product->purchase_price,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]
                    ]);
                }

                $detail->delete();
            }
        }
    }

    /**
     * update bank or cash balance when sale is deleted or updated
     * @param $sale
     * @return void
     */
    public function updateCashBankBalance($sale)
    {
        if ($sale->payment_type == 'cash') {
            $sale->cash()->decrement('balance', $sale->paid);
        }else{
            $sale->bankAccount()->decrement('balance', $sale->paid);
        }

        $sale->update([
            'cash_id' => null,
            'bank_account_id' => null,
        ]);
    }

    /**
     * update sale old customer balance when sale update or delete
     * @param $sale
     * @return void
     */
    public function updateOldCustomerBalance($sale)
    {
        $total_due = $sale->grand_total - $sale->total_paid;
        $sale->party()->decrement('balance', $total_due);
    }

    /**
     * sms details
     * @param $sale
     * @param $request
     * @return void
     */
    public function smsDetails($sale, $request)
    {
        $customer = Party::findOrFail($request->party_id);
        if ($request->sms) {
            $message = "আপনি পণ্য ক্রয় করেছেন " .
                $sale->grand_total .
                "৳, জমা দিয়েছেন " .
                $sale->paid.
                "৳, অবশিষ্ট বকেয়া " .
                $customer->balance;

            $message = $message . " " . config('sms.regards');
            $data = SMS::customSmsSend($this->sender_id, $this->api_key, $customer->phone, $message); //send sms
            if ($data == '202') {
                $sms_data = [
                    'number' => $customer->phone,
                    'message' => $message,
                    'character' => strlen($message),
                    'total_sms' => 2,
                    'status' => 'success',
                ];
                \App\Models\Sms::create($sms_data);
            }
        }
    }
}
