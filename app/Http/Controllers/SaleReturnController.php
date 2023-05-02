<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaleReturnRequest;
use App\Models\BankAccount;
use App\Models\Cash;
use App\Models\Party;
use App\Models\Product;
use App\Models\SaleReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SaleReturnController extends Controller
{
    protected string $permission_for = 'sale_return';
    private $sale_return;
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $this->hasPermission('view');

        return Inertia::render('SaleReturn/Index', [
            'sale_returns' => SaleReturn::addPartyName()
                ->orderByDesc('date')
                ->orderByDesc('created_at')
                ->paginate(30),
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
        return Inertia::render('SaleReturn/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaleReturnRequest $request)
    {
        $this->hasPermission('create');
        $sale_return_date = $request->validated();
        $sale_return_date['user_id'] = \Auth::user()->id;
        $sale_return_date['return_no'] = 'Return-' . str_pad(SaleReturn::max('id') + 1, 8,0, STR_PAD_LEFT);

        DB::transaction(function () use($sale_return_date, $request) {
            $this->sale_return = SaleReturn::create($sale_return_date);
            $sale_return = $this->sale_return;
            $this->updateCustomerBalance($request);
            $this->updateCashBankBalance($request);
            $this->saveSaleReturnProduct($request, $sale_return);
        });
        if ($this->sale_return) {
            return redirect()->route('sale-return.show', $this->sale_return->id);
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

        $sale_return = SaleReturn::with('party', 'showroom', 'details.product.unit')->findOrFail($id);
        return Inertia::render('SaleReturn/Show', [
            'sale_return' => $sale_return,
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
        $old_sale_return = SaleReturn::with('details.product.unit')->findOrFail($id);
        return Inertia::render('SaleReturn/Create', [
            'old_sale_return' => $old_sale_return,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SaleReturnRequest $request, $id)
    {
        $this->hasPermission('update');
        DB::transaction(function () use($request, $id) {
            $old_sale_return = SaleReturn::findOrFail($id);
//          <!-- update previous record -->
            $this->removePreviousProductUpdateQuantity($old_sale_return);
            $this->updateOldCashBankBalance($old_sale_return);
            $this->updateOldCustomerBalance($old_sale_return);
//          <!-- update previous record -->
            $sale_return_date = $request->validated();
            $old_sale_return->update($sale_return_date);
            $this->sale_return = $old_sale_return;
            $this->updateCustomerBalance($request);
            $this->updateCashBankBalance($request);
            $this->saveSaleReturnProduct($request, $old_sale_return);
        });
        if ($this->sale_return) {
            return redirect()->route('sale-return.show', $this->sale_return->id);
        }
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
        DB::transaction(function () use($id) {
            $oldSaleReturn = SaleReturn::findOrFail($id);
//          <!-- update previous record -->
            $this->removePreviousProductUpdateQuantity($oldSaleReturn);
            $this->updateOldCashBankBalance($oldSaleReturn);
            $this->updateOldCustomerBalance($oldSaleReturn);
//          <!-- update previous record -->

            $this->sale_return = $oldSaleReturn->delete();
        });

        if ($this->sale_return) {
            return redirect()->back();
        }
    }

    /**
     * update customer balance for sale return
     * @param $request
     * @return void
     */
    public function updateCustomerBalance($request)
    {
        $customer = Party::findOrFail($request->party_id);

        if ($request->due > 0) {
            $customer->balance = -1 * $request->due;
        }else{
            $customer->balance = $request->party_balance;
        }
        $customer->save();
    }

    /**
     * update cash bank balance for sale return paid
     * @param $request
     * @return void
     */
    public function updateCashBankBalance($request)
    {
        if ($request->paid_from == 'cash'){
            Cash::findOrFail($request->cash_id)->decrement('balance', $request->paid);
        }else{
            BankAccount::findOrFail($request->bank_account_id)->decrement('balance', $request->paid);
        }
    }

    /**
     * save return product for sale return
     * @param $request
     * @param $sale_return
     * @return void
     */
    public function saveSaleReturnProduct($request, $sale_return)
    {
        foreach ($request->products as $product) {
            $_product = Product::findOrFail($product['id']);
            $sale_return_details_data = [
                'product_id' => $product['id'],
                'showroom_id' => $request->showroom_id,
                'date' => $sale_return->date,
                'purchase_price' => $product['purchase_price'],
                'return_price' => $product['return_price'],
                'quantity' => $product['quantity'],
                'quantity_in_unit' => $product['quantity_in_unit'],
                'quantity_for_price' => $product['quantity_for_price'],
                'line_total' => $product['line_total'],
            ];

            // create sale details
            $sale_return->details()
                ->create($sale_return_details_data);

            // decrement product quantity
            $showroom = $_product->showrooms()
                ->find($request->showroom_id);

            // increment quantity in showroom
            $showroom->stock->increment('quantity', $product['quantity']);
        }
    }

    /**
     * remove sale return product & update quantity
     * @param $sale_return
     * @return void
     */
    public function removePreviousProductUpdateQuantity($sale_return)
    {
        if (count($sale_return->details) > 0) {
            foreach ($sale_return->details as $detail) {
                $_quantity = $detail->quantity;
                // get product
                $product = Product::findOrFail($detail->product_id);
                // get showroom
                $_showroom = $product->showrooms->where('id', $sale_return->showroom_id)->first();
                if($_showroom) {
                    //get exists quantity
                    $previous_quantity = $_showroom->stock->quantity;
                    //update stocks
                    $product->showrooms()->updateExistingPivot($sale_return->showroom_id, [
                        'quantity' => $previous_quantity - $_quantity,
                    ]);
                }else{ // no previous showroom exists
                    //add new stock in for products
                    $product->showrooms()->attach([
                        $sale_return->showroom_id =>  [
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
     * update bank or cash balance when sale return is deleted or updated
     * @param $sale_return
     * @return void
     */
    public function updateOldCashBankBalance($sale_return)
    {
        if ($sale_return->cash_id) {
            $sale_return->cash()->increment('balance', $sale_return->paid);
        }else{
            $sale_return->bankAccount()->increment('balance', $sale_return->paid);
        }
    }

    /**
     * update sale return old customer balance when sale update or delete
     * @param $sale_return
     * @return void
     */
    public function updateOldCustomerBalance($sale_return)
    {
        if ($sale_return->due > 0) {
            $sale_return->party()->increment('balance', $sale_return->due);
        }else{
            $sale_return->party()->decrement('balance', ($sale_return->paid - $sale_return->return_grand_total));
        }
    }
}
