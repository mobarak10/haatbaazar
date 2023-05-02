<?php

namespace App\Http\Controllers;

use App\Http\Requests\DamageReturnRequest;
use App\Models\BankAccount;
use App\Models\Cash;
use App\Models\DamageReturn;
use App\Models\Party;
use App\Models\PartyDetails;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DamageReturnController extends Controller
{
    protected string $permission_for = 'damage_return';

    private $damage_return;
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $this->hasPermission('view');
        $damage_returns = DamageReturn::with('party')
            ->whereHas('party')
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->paginate(30);

        return Inertia::render('DamageReturn/Index', [
            'damage_returns' => $damage_returns,
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

        return Inertia::render('DamageReturn/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(DamageReturnRequest $request)
    {
        $this->hasPermission('create');
        $damage_return_data = $request->validated();
        $damage_return_data['user_id'] = Auth::user()->id;
        $damage_return_data['damage_return_no'] = 'DR-'.str_pad(DamageReturn::max('id') + 1, 8, 0, STR_PAD_LEFT);

        DB::transaction(function () use ($request, $damage_return_data) {
            $this->damage_return = DamageReturn::create($damage_return_data);
            $damage_return = $this->damage_return;

            $this->updatePartyBalance($request);
            $this->incrementCashBankBalance($request);
            $this->saveDamageReturnDetails($request, $damage_return);
        });
        if ($this->damage_return) {
            return redirect()->route('damage-return.show', $this->damage_return->id);
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
        $damage_return = DamageReturn::with('party', 'showrooms', 'details.product')->findOrFail($id);

        return Inertia::render('DamageReturn/Show', [
            'damage_return' => $damage_return,
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
        $oldDamageReturn = DamageReturn::with('details.product.unit')->findOrFail($id);

        return Inertia::render('DamageReturn/Create', compact('oldDamageReturn'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $this->hasPermission('update');
        DB::transaction(function () use ($request, $id) {
            //<!-- update previous record -->
            $oldDamageReturn = DamageReturn::findOrFail($id);
            $this->incrementPreviousQuantityAndDeleteDetails($oldDamageReturn);
            $this->decrementCashBankBalance($oldDamageReturn);
            $this->updateOldSupplierBalance($oldDamageReturn);

            $damage_return_data = $request->validated();
            $oldDamageReturn->update($damage_return_data);

            $this->damage_return = $oldDamageReturn;

            $party_balance = (-1 * $request->party_balance);
            // get party
            $party = Party::findOrFail($request->party_id);
            if ($request->party_id == $oldDamageReturn->party_id) {
                $party->decrement('balance', $party_balance);
            } else {
                $this->updatePartyBalance($request);
            }

            $this->incrementCashBankBalance($request);
            $this->saveDamageReturnDetails($request, $oldDamageReturn);
//          <!-- update previous record -->
        });
        if ($this->damage_return) {
            return redirect()->route('damage-return.show', $this->damage_return->id);
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
        //
    }

    /**
     * update party balance
     * @param $request
     * @return void
     */
    public function updatePartyBalance($request)
    {
        // get party
        $party = Party::findOrFail($request->party_id);
        $party->update(['balance' => ($request->party_balance)]);
    }

    /**
     * increment cash or bank balance
     * @param $request
     * @return void
     */
    public static function incrementCashBankBalance($request)
    {
        if ($request->paid_to == 'cash') {
            Cash::findOrFail($request->cash_id)->increment('balance', $request->paid);
        } else {
            BankAccount::findOrFail($request->bank_account_id)->increment('balance', $request->paid);
        }
    }

    public static function saveDamageReturnDetails($request, $damage_return)
    {
        foreach ($request->products as $product) {
            $_product = Product::findOrFail($product['id']);
            $details_data = [
                'product_id' => $product['id'],
                'purchase_price' => $product['purchase_price'],
                'quantity' => $product['quantity'],
                'quantity_in_unit' => $product['quantity_in_unit'],
                'line_total' => $product['line_total'],
            ];

            // create sale details
            $damage_return->details()
                ->create($details_data);

            //select business group
            $showroom = $_product->showrooms
                ->where('id', $damage_return->showroom_id)
                ->first();

            $showroom?->stock->decrement('damage_quantity', $product['quantity']);
        }
    }

    /**
     * update damage return old party balance when damage return update or delete
     *
     * @param $damage_return
     * @return void
     */
    public function updateOldSupplierBalance($damage_return)
    {
        $damage_return_due = ($damage_return->grand_total - $damage_return->previous_balance) - $damage_return->paid;
        Party::where('party_id', $damage_return->party_id)
            ->first()
            ->decrement('balance', $damage_return_due);
    }

    public function incrementPreviousQuantityAndDeleteDetails($damage_return)
    {
        if (count($damage_return->details) > 0) {
            foreach ($damage_return->details as $detail) {
                $_quantity = $detail->quantity;
                // get product
                $product = Product::findOrFail($detail->product_id);

                //select business group
                $business_group = $product->showrooms
                    ->where('id', $damage_return->showroom_id)
                    ->first();

                $business_group?->stock->increment('damage_quantity', $detail->quantity);

                $detail->delete();
            }
        }
    }

    /**
     * decrement cash or bank balance
     * @param $request
     * @return void
     */
    public static function decrementCashBankBalance($data)
    {
        if ($data->cash_id) {
            $data->cash()->decrement('balance', $data->paid);
        } else {
            $data->bankAccount()->decrement('balance', $data->paid);
        }
    }
}
