<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoanStoreRequest;
use App\Models\Bank;
use App\Models\Cash;
use App\Models\Loan;
use App\Models\Showroom;
use Inertia\Inertia;
use App\Models\BankAccount;
use App\Models\LoanAccount;
use Illuminate\Http\Request;
use App\Models\LoanInstallment;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoanController extends Controller
{
    protected string $permission_for = 'loan';
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $this->hasPermission('view');
        $cashes = Cash::query()
            ->orderBy('title')
            ->get();

        $banks = Bank::query()
            ->with('bankAccounts')
            ->get();

        $loans = Loan::query()
            ->with('transactionable')
            ->withCount('loanInstallments')
            ->addLoanAccountName()
            ->addAdjustment()
            ->addPaid()
            ->addDue()
            ->addPaymentMethod()
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->get();

        $loanAccounts = LoanAccount::query()
            ->orderBy('name')
            ->get();

        $showrooms = Showroom::select('id', 'name')->get();

        return Inertia::render("Loan/Index", compact(
            'cashes',
            'banks',
            'loans',
            'loanAccounts',
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
    public function store(LoanStoreRequest $request)
    {
        $this->hasPermission('create');
        $data = $request->validated();

        // identify payment method
        if ($request->payment_method == 'cash') {
            $trasactionable = Cash::find($request['transactionable']['id']);
        } else {
            $trasactionable = BankAccount::find($request['transactionable']['id']);
        }

        DB::transaction(function () use ($data, $request, $trasactionable) {
            $trasactionable->loans()->create($data);
            $trasactionable->increment('balance', $request->amount);
        });

        return redirect()->back()->with('success', 'Loan created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Loan  $loan
     * @return \Inertia\Response
     */
    public function show($id)
    {
        $this->hasPermission('show');
        $cashes = Cash::query()
            ->orderBy('title')
            ->get();

        $banks = Bank::query()
            ->with('bankAccounts')
            ->get();

        $loan = Loan::query()
            ->with([
                'loanInstallments' => function (HasMany $query) {
                    $query->select('*')
                        ->addPaymentMethod()
                        ->latest('date');
                },
                'loanInstallments.transactionable',
                'loanAccount',
            ])
            ->addLoanAccountName()
            ->addAdjustment()
            ->addPaid()
            ->addDue()
            ->addPaymentMethod()
            ->findOrFail($id);

        return Inertia::render('Loan/Show', compact('loan', 'cashes', 'banks'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Loan  $loan
     * @return \Illuminate\Http\Response
     */
    public function edit(Loan $loan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Loan  $loan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(LoanStoreRequest $request, $id)
    {
        $this->hasPermission('update');
        $data = $request->validated();

        $loan = Loan::query()
            ->select('*')
            ->with('transactionable')
            ->addPaymentMethod()
            ->find($id);

        // check payment method is updated or not
        if ($loan->payment_method == $request->payment_method) {
            // payment method is same
            // get the different
            $diff = $request->amount - $loan->amount;

            // query safer
            DB::transaction(function () use ($diff, $loan, $data) {
                // adjust the balance
                $loan->transactionable->increment('balance', $diff);
                // update loan
                $loan->update($data);
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
            DB::transaction(function () use ($loan, $trasactionable, $request, $data) {
                // add previous amount in previous payment method
                $loan->transactionable->decrement('balance', $loan->amount);

                // update payment method in loan
                $loan->update($data + [
                    'transactionable_type' => $trasactionable->getMorphClass(),
                    'transactionable_id' => $trasactionable->id,
                ]);

                // decrement amount from current transactionable
                $trasactionable->increment('balance', $request->amount);
            });
        }

        return redirect()
            ->back()
            ->with('success', 'Loan updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Loan  $loan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $this->hasPermission('delete');
        $loan = Loan::query()
            ->with('transactionable')
            ->find($id);

        if ($loan->loanInstallments()->count()) {
            return redirect()->back()->with('errors', 'Failed to delete loan. Loan account has some installments.');
        }

        // restore balance
        $loan->transactionable->decrement('balance', $loan->amount);

        $loan->delete();

        return redirect()->back()->with('success', 'Loan deleted successfully.');
    }
}
