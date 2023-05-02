<?php

namespace App\Http\Controllers;

use App\Helpers\Converter;
use App\Helpers\QuantityHelper;
use App\Http\Requests\PurchaseStoreRequest;
use App\Models\BankAccount;
use App\Models\Cash;
use App\Models\Party;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\RawMaterial;
use App\Models\Showroom;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class PurchaseController extends Controller
{
    protected string $permission_for = 'purchase';
    private $purchase;
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $this->hasPermission('view');
//        return Purchase::with('details')->get();
        $purchases = Purchase::query();
        if (request()->search) {
            if (request()->from_date) {
                $purchases = $purchases->whereBetween('date', [request()->from_date, request()->to_date]);
            }
            if (request()->voucher_no) {
                $purchases = $purchases->where('voucher_no', request()->voucher_no);
            }
        }
        $purchases = $purchases
            ->addPartyName()
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->paginate(30)
            ->withQueryString();
        return Inertia::render('Purchase/Index', [
            'purchases' => $purchases,
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
        $products = Product::query()
            ->select(
                'id',
                'name',
                'unit_id',
                'sale_price',
                'purchase_price',
                'price_type',
                'barcode',
                'type',
                'divisor_number'
            )
            ->addUnitLabel()
            ->addTotalQuantity()
            ->addUnitRelation()
            ->orderBy('name')
            ->get();

        return Inertia::render('Purchase/Create', [
            'showrooms' => Showroom::select('id', 'name')->get(),
            'products' => $products,
            'suppliers' => Party::supplier()->select('id', 'name', 'balance', 'phone', 'address')->get(),
            'cashes' => Cash::all(),
            'bank_accounts' => BankAccount::with('bank')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PurchaseStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function store(PurchaseStoreRequest $request)
    {
        $this->hasPermission('create');
        DB::transaction(function () use ($request) {
            $purchase_data = $request->validated();
            $purchase_data['voucher_no'] = 'Voucher'.'-' . str_pad(Purchase::max('id') + 1, 8, '0', STR_PAD_LEFT);

            // get party
            $party = Party::findOrFail($request->party_id);
            $party->balance = (-1 * $request->party_balance);
            $party->save();

            // create new purchase
            $this->purchase = Purchase::create($purchase_data);
            $purchase = $this->purchase;
            // update cash or bank balance
            $this->updateCashBankBalance($request);
            // save purchase cost
            $this->savePurchaseCost($request, $purchase);
            // save purchase details
            $this->savePurchaseDetails($request, $purchase);
        });
        return Redirect::route('purchase.show', $this->purchase->id);
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
        $purchase = Purchase::with('party', 'details.product.unit', 'showroom', 'purchaseCost')->findOrFail($id);
        $purchase['formatted_date'] = $purchase->date->format('Y-m-d');
        return Inertia::render('Purchase/Show', [
            'purchase' => $purchase,
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
        $old_purchase = Purchase::with('details.product.unit', 'purchaseCost')->findOrFail($id);
        $old_purchase['formatted_date'] = $old_purchase->date->format('Y-m-d');
        $products = Product::query()
            ->select(
                'id',
                'name',
                'unit_id',
                'sale_price',
                'purchase_price',
                'price_type',
                'barcode',
                'type',
                'divisor_number'
            )
            ->addUnitLabel()
            ->addTotalQuantity()
            ->addUnitRelation()
            ->orderBy('name')
            ->get();

        return Inertia::render('Purchase/Create', [
            'oldPurchase' => $old_purchase,
            'showrooms' => Showroom::all(),
            'products' => $products,
            'suppliers' => Party::supplier()->get(),
            'cashes' => Cash::all(),
            'bank_accounts' => BankAccount::with('bank')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PurchaseStoreRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(PurchaseStoreRequest $request, $id)
    {
        $this->hasPermission('update');
//        return $request->all();
        DB::transaction(function () use ($request, $id) {
            $old_purchase = Purchase::findOrFail($id);
            // update old purchase product quantity
            $this->updateOldPurchaseProductQuantity($old_purchase);
            // update old purchase cash or bank balance
            $this->updateOldPurchaseCashBankBalance($old_purchase);
            // update old purchase supplier balance
            $this->updateOldPurchaseSupplierBalance($old_purchase);
            $purchase_data = $request->validated();

            $party_balance = (-1 * $request->party_balance);
            // get party
            $party = Party::findOrFail($request->party_id);
            if ($request->party_id == $old_purchase->party_id) {
                $party->increment('balance', $party_balance);
            }else{
                $party->balance = $party_balance;
                $party->save();
            }

            $this->purchase = $old_purchase;
            // update purchase
            $old_purchase->update($purchase_data);

            // update cash or bank balance
            $this->updateCashBankBalance($request);
            // save purchase cost
            $this->savePurchaseCost($request, $old_purchase);
            // save purchase details
            $this->savePurchaseDetails($request, $old_purchase);
        });
        return Redirect::route('purchase.show', $this->purchase->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $this->hasPermission('delete');
//        return $id;
        DB::transaction(function () use ($id) {
            $old_purchase = Purchase::findOrFail($id);
            // update old purchase product quantity
            $this->updateOldPurchaseProductQuantity($old_purchase);
            // update old purchase cash or bank balance
            $this->updateOldPurchaseCashBankBalance($old_purchase);
            // update old purchase supplier balance
            $this->updateOldPurchaseSupplierBalance($old_purchase);

            $old_purchase->delete();
        });
        return redirect()
            ->back()
            ->with('success', 'Purchase delete successfully');
    }

    /**
     * update quantity & average purchase price of existing product quantity
     * @param $product
     * @param $showroom
     * @param $products
     * @param $purchase_price
     * @return void
     */
    public function updateExistingQuantity($product, $showroom, $products, $purchase_price)
    {
        //get exists quantity
        $previous_quantity = $showroom->stock->quantity;
        // get total quantity
        $_total_quantity = $showroom->stock->quantity + $products['quantity'];

        // get percentage of previous quantity
        $percentage_of_previous_quantity = ($previous_quantity * 100) / $_total_quantity;

        // get percentage of present quantity
        $percentage_of_present_quantity = ($products['quantity'] * 100) / $_total_quantity;

        // get previous stock percentage price
        $previous_average_purchase_price = $percentage_of_previous_quantity * ($showroom->stock->average_purchase_price / 100);

        // get present stock percentage price
        $present_average_purchase_price = $percentage_of_present_quantity * ($purchase_price / 100);

        // total quantity purchase price
        $total_price = $previous_average_purchase_price + $present_average_purchase_price;

        //update stocks
        $product->showrooms()->updateExistingPivot($showroom->id, [
            'quantity' => $previous_quantity + $products['quantity'],
            'average_purchase_price' => $total_price,
        ]);
    }

    /**
     * update cash or bank balance for purchase paid amount
     * @param $request
     * @return void
     */
    public function updateCashBankBalance($request)
    {
        if ($request->payment_type === 'cash'){
            Cash::findOrFail($request->cash_id)->decrement('balance', $request->paid);
        }else{
            BankAccount::findOrFail($request->bank_account_id)->decrement('balance', $request->paid);
        }
    }

    /**
     * save purchase details
     * @param $request
     * @param $purchase
     * @return void
     */
    public function savePurchaseDetails($request, $purchase)
    {
        foreach ($request->products as $product) {
            $_product = Product::findOrFail($product['id']);

            $product_data = [
                'purchase_price' => $product['price']
            ];
            $_product->update($product_data);
            //select showroom
            $showroom = $_product->showrooms->where('id', $request->showroom_id)->first();

            //if exists showroom
            if($showroom){
                $this->updateExistingQuantity($_product, $showroom, $product, $product['price']);
            }else{ // no previous showroom exists
                //add new stock in for products
                $_product->showrooms()->attach([
                    $request->showroom_id =>  [
                        'quantity' => $product['quantity'],
                        'average_purchase_price' => $product['price'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                ]);
            }

            $purchase_details_data = [
                'product_id' => $product['id'],
                'showroom_id' => $request->showroom_id,
                'date' => $purchase->date,
                'purchase_price' => $product['price'],
                'quantity' => $product['quantity'],
                'quantity_in_unit' => $product['quantity_in_unit'],
                'line_total' => $product['line_total'],
            ];
            $purchase->details()->create($purchase_details_data);
        }
    }

    /**
     * save purchase cost
     * @param $request
     * @param $purchase
     * @return void
     */
    public function savePurchaseCost($request, $purchase)
    {
        $purchase_cost_data = $request->validate([
            'transport_cost' => 'nullable|numeric',
            'transport_cost_adjust_to_supplier' => 'required|boolean',
            'labour_cost' => 'nullable|numeric',
            'labour_cost_adjust_to_supplier' => 'required|boolean',
        ]);

        $purchase->purchaseCost()->create($purchase_cost_data);
    }

    /**
     * update old purchase quantity in showroom
     * @param $old_purchase
     * @return void
     */
    public function updateOldPurchaseProductQuantity($old_purchase)
    {
        if (count($old_purchase->details) > 0) {
            foreach ($old_purchase->details as $detail) {
                $_quantity = $detail->quantity;
                // get product
                $product = Product::findOrFail($detail->product_id);
                // get showroom
                $_showroom = $product->showrooms->where('id', $old_purchase->showroom_id)->first();
                if($_showroom) {
                    //get exists quantity
                    $previous_quantity = $_showroom->stock->quantity;
                    //update stocks
                    $product->showrooms()->updateExistingPivot($old_purchase->showroom_id, [
                        'quantity' => $previous_quantity - $_quantity,
                    ]);
                }else{ // no previous showroom exists
                    //add new stock in for products
                    $product->showrooms()->attach([
                        $old_purchase->showroom_id =>  [
                            'quantity' => (-1 * $_quantity),
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
     * update previously paid amount in cash or bank
     * @param $old_purchase
     * @return void
     */
    public function updateOldPurchaseCashBankBalance($old_purchase)
    {
        if ($old_purchase->payment_type == 'cash') {
            $old_purchase->cash()->increment('balance', $old_purchase->paid);
        }else{
            $old_purchase->bankAccount()->increment('balance', $old_purchase->paid);
        }
    }

    /**
     * update old supplier balance
     * @param $old_purchase
     * @return void
     */
    public function updateOldPurchaseSupplierBalance($old_purchase)
    {
        $purchase_due = ($old_purchase->grand_total - $old_purchase->previous_balance) - $old_purchase->paid;
        $old_purchase->party()->increment('balance', $purchase_due);
    }

}
