<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseStoreRequest;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Cash;
use App\Models\Showroom;
use Inertia\Inertia;
use App\Models\CostCategory;
use App\Models\CostSubcategory;
use App\Models\Expense;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    protected string $permission_for = 'expense';
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $this->hasPermission('view');
        $costCategories = CostCategory::query()
            ->with([
                'costSubcategories' => function (HasMany $query) {
                    $query->select('id', 'name', 'cost_category_id')
                        ->orderBy('name');
                }
            ])
            ->orderBy('name')
            ->get();

        $cashes = Cash::query()
            ->orderBy('title')
            ->get();

        $banks = Bank::query()
            ->with('bankAccounts')
            ->get();

        $showrooms = Showroom::select('id', 'name')->get();

        $expenses = Expense::query()
            ->addSelect([
                'cost_subcategory_name' => CostSubcategory::select('name')
                    ->whereColumn('cost_subcategory_id', 'cost_subcategories.id')
                    ->limit(1),
                'cost_subcategory_id' => CostSubcategory::select('id')
                    ->whereColumn('cost_subcategory_id', 'cost_subcategories.id')
                    ->limit(1),
            ])
            ->with('transactionable:id')
            ->addPaymentMethod()
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->paginate(30);

        return Inertia::render("Expense/Index", compact(
            "costCategories",
            "cashes",
            "banks",
            "expenses",
            'showrooms'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ExpenseStoreRequest $request)
    {
        $this->hasPermission('create');
        $data = $request->validated();

        $trasactionable = null;

        // identify payment method
        if ($request->payment_method == 'cash') {
            $trasactionable = Cash::find($request['transactionable']['id']);
        } else {
            $trasactionable = BankAccount::find($request['transactionable']['id']);
        }

        DB::transaction(function () use ($data, $request, $trasactionable) {
            $trasactionable->expenses()->create($data);
            $trasactionable->decrement('balance', $request->amount);
        });

        return redirect()->back()->with('success', 'Expense created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ExpenseStoreRequest $request, $id)
    {
        $this->hasPermission('update');
        $data = $request->validated();

        $expense = Expense::query()
            ->select('*')
            ->with('transactionable')
            ->addPaymentMethod()
            ->find($id);


        // check payment method is updated or not
        if ($expense->payment_method == $request->payment_method) {
            // payment method is same
            // get the different
            $diff = $expense->amount - $request->amount;

            // query safer
            DB::transaction(function () use ($diff, $expense, $data) {
                // adjust the balance
                $expense->transactionable->increment('balance', $diff);
                // update expense
                $expense->update($data);
            });
        } else {
            // payment method is not same

            // identify payment method
            if ($request->payment_method == 'cash') {
                $trasactionable = Cash::find($request['transactionable']['id']);
            } else {
                $trasactionable = BankAccount::find($request['transactionable']['id']);
            }

            // query safer
            DB::transaction(function () use ($expense, $trasactionable, $request, $data) {
                // add previous amount in previous payment method
                $expense->transactionable->increment('balance', $expense->amount);

                // update payment method in expense
                $expense->update($data + [
                    'transactionable_type' => $trasactionable->getMorphClass(),
                    'transactionable_id' => $trasactionable->id,
                ]);

                // decrement amount from current transactionable
                $trasactionable->decrement('balance', $request->amount);
            });
        }

        return redirect()
            ->back()
            ->with('success', 'Expense updated successfully.');
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
        $expense = Expense::query()
            ->with('transactionable')
            ->find($id);

        // restore balance
        $expense->transactionable->increment('balance', $expense->amount);

        $expense->delete();

        return back()->with('success', 'Expense deleted successfully.');
    }
}
