<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseReturnRequest;
use App\Models\BankAccount;
use App\Models\Cash;
use App\Models\Party;
use App\Models\Product;
use App\Models\PurchaseReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PurchaseReturnController extends Controller
{
    protected string $permission_for = 'purchase_return';
    private $purchase_return;
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $this->hasPermission('view');
        $purchase_returns = PurchaseReturn::with('party')
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->whereHas('party')
            ->paginate(30);
        return Inertia::render('PurchaseReturn/Index', [
            'purchase_returns' => $purchase_returns,
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
        return Inertia::render('PurchaseReturn/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PurchaseReturnRequest $request)
    {
        $this->hasPermission('create');
        $purchase_return_data = $request->validated();
        $purchase_return_data['user_id'] = Auth::user()->id;
        $purchase_return_data['return_no'] = 'Return-' . str_pad(PurchaseReturn::max('id') + 1, 8,0, STR_PAD_LEFT);

        DB::transaction(function () use ($request, $purchase_return_data) {
            $this->purchase_return = PurchaseReturn::create($purchase_return_data);
            $purchase_return = $this->purchase_return;

            $this->updateSupplierBalance($request);
            $this->updateCashBankBalance($request);
            $this->savePurchaseReturnProduct($request, $purchase_return);
        });
        if ($this->purchase_return) {
            return redirect()->route('purchase-return.show', $this->purchase_return->id);
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
        $purchase_return = PurchaseReturn::with('party', 'showroom', 'details.product.unit')->findOrFail($id);

        return Inertia::render('PurchaseReturn/Show', [
            'purchase_return' => $purchase_return,
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
        $this->hasPermission('create');
        $oldPurchaseReturn = PurchaseReturn::with('details.product.unit')->findOrFail($id);
        return Inertia::render('PurchaseReturn/Create', compact('oldPurchaseReturn'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PurchaseReturnRequest $request, $id)
    {
        $this->hasPermission('update');
        DB::transaction(function () use($request, $id) {
            //<!-- update previous record -->
            $oldPurchaseReturn = PurchaseReturn::findOrFail($id);
            $this->removePreviousProductUpdateQuantity($oldPurchaseReturn);
            $this->updateOldCashBankBalance($oldPurchaseReturn);
            $this->updateOldSupplierBalance($oldPurchaseReturn);

            $purchase_return_data = $request->validated();
            $oldPurchaseReturn->update($purchase_return_data);

            $this->purchase_return = $oldPurchaseReturn;

            $this->updateSupplierBalance($request);
            $this->updateCashBankBalance($request);
            $this->savePurchaseReturnProduct($request, $oldPurchaseReturn);
//          <!-- update previous record -->
        });
        if ($this->purchase_return) {
            return redirect()->route('purchase-return.show', $this->purchase_return->id);
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
            //<!-- update previous record -->
            $oldPurchaseReturn = PurchaseReturn::findOrFail($id);
            $this->removePreviousProductUpdateQuantity($oldPurchaseReturn);
            $this->updateOldCashBankBalance($oldPurchaseReturn);
            $this->updateOldSupplierBalance($oldPurchaseReturn);

            $this->purchase_return = $oldPurchaseReturn->delete();
        });
        if ($this->purchase_return) {
            return redirect()->back();
        }
    }

    /**
     * remove purchase return product & update quantity
     * @param $purchase_return
     * @return void
     */
    public function removePreviousProductUpdateQuantity($purchase_return)
    {
        if (count($purchase_return->details) > 0) {
            foreach ($purchase_return->details as $detail) {
                $_quantity = $detail->quantity;
                // get product
                $product = Product::findOrFail($detail->product_id);
                // get showroom
                $_showroom = $product->showrooms->where('id', $purchase_return->showroom_id)->first();
                if($_showroom) {
                    //get exists quantity
                    $previous_quantity = $_showroom->stock->quantity;
                    //update stocks
                    $product->showrooms()->updateExistingPivot($purchase_return->showroom_id, [
                        'quantity' => $previous_quantity + $_quantity,
                    ]);
                }else{ // no previous showroom exists
                    //add new stock in for products
                    $product->showrooms()->attach([
                        $purchase_return->showroom_id =>  [
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
     * update bank or cash balance when purchase return is deleted or updated
     * @param $purchase_return
     * @return void
     */
    public function updateOldCashBankBalance($purchase_return)
    {
        if ($purchase_return->cash_id) {
            $purchase_return->cash()->decrement('balance', $purchase_return->paid);
        }else{
            $purchase_return->bankAccount()->decrement('balance', $purchase_return->paid);
        }
    }

    /**
     * update sale return old customer balance when sale update or delete
     * @param $purchase_return
     * @return void
     */
    public function updateOldSupplierBalance($purchase_return)
    {
        if ($purchase_return->return_due > 0) {
            $purchase_return->party()->decrement('balance', $purchase_return->return_due);
        }else{
            $purchase_return->party()->increment('balance', ($purchase_return->paid - $purchase_return->return_grand_total));
        }
    }

    /**
     * save supplier balance when purchase return
     * @param $request
     * @return void
     */
    public function updateSupplierBalance($request)
    {
        $supplier = Party::findOrFail($request->party_id);
        if ($request->due > 0) {
            $supplier->balance = $request->due;
        }else{
            $supplier->balance = 0;
        }
        $supplier->save();
    }

    /**
     * update cash bank balance when purchase return
     * @param $request
     * @return void
     */
    public function updateCashBankBalance($request)
    {
        $total_paid = $request->paid - $request->change;
        if ($request->paid_from == 'cash'){
            Cash::findOrFail($request->cash_id)->increment('balance', $total_paid);
        }else{
            BankAccount::findOrFail($request->bank_account_id)->increment('balance', $total_paid);
        }
    }

    public function savePurchaseReturnProduct($request, $purchase_return)
    {
        foreach ($request->products as $product) {
            $_product = Product::findOrFail($product['id']);
            $purchase_return_details_data = [
                'product_id' => $product['id'],
                'showroom_id' => $request->showroom_id,
                'date' => $purchase_return->date,
                'purchase_price' => $product['purchase_price'],
                'return_price' => $product['return_price'],
                'quantity' => $product['quantity'],
                'quantity_in_unit' => $product['quantity_in_unit'],
                'line_total' => $product['line_total'],
            ];

            // create sale details
            $purchase_return->details()
                ->create($purchase_return_details_data);

            // decrement product quantity
            $showroom = $_product->showrooms()
                ->find($request->showroom_id);

            // increment quantity in showroom
            $showroom->stock->decrement('quantity', $product['quantity']);
        }
    }
}
