<?php

namespace App\Http\Controllers;

use App\Models\Cash;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use App\Models\LoanInstallment;
use Illuminate\Support\Facades\DB;

class LoanInstallmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request->all();

        $data = $request->validate([
            'loan_id' => 'required|integer|exists:loans,id',
            'showroom_id' => 'required|integer|exists:showrooms,id',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'adjustment' => 'nullable|numeric',
            'note' => 'nullable|string',
        ]);
        // TODO validation for max allowed amount

        // identify payment method
        if ($request->payment_method == 'cash') {
            $trasactionable = Cash::find($request['transactionable']['id']);
        } else {
            $trasactionable = BankAccount::find($request['transactionable']['id']);
        }

        DB::transaction(function () use ($data, $request, $trasactionable) {
            $trasactionable->loanInstallments()->create($data);
            $trasactionable->increment('balance', $request->amount);
        });

        return redirect()->back()->with('success', 'Loan installment added successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LoanInstallment  $loanInstallment
     * @return \Illuminate\Http\Response
     */
    public function show(LoanInstallment $loanInstallment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LoanInstallment  $loanInstallment
     * @return \Illuminate\Http\Response
     */
    public function edit(LoanInstallment $loanInstallment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LoanInstallment  $loanInstallment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'loan_id' => 'required|integer|exists:loans,id',
            'date' => 'required|date',
            'amount' => 'required|numeric',
            'adjustment' => 'nullable|numeric',
            'note' => 'nullable|string',
        ]);

        $loan_installment = LoanInstallment::query()
            ->select('*')
            ->with('transactionable')
            ->addPaymentMethod()
            ->find($id);


        // check payment method is updated or not
        if ($loan_installment->payment_method == $request->payment_method) {
            // payment method is same
            // get the different
            $diff = $request->amount - $loan_installment->amount;

            // query safer
            DB::transaction(function () use ($diff, $loan_installment, $data) {
                // adjust the balance
                $loan_installment->transactionable->increment('balance', $diff);
                // update loan_installment
                $loan_installment->update($data);
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
            DB::transaction(function () use ($loan_installment, $trasactionable, $request, $data) {
                // add previous amount in previous payment method
                $loan_installment->transactionable->decrement('balance', $loan_installment->amount);

                // update payment method in loan_installment
                $loan_installment->update($data + [
                    'transactionable_type' => $trasactionable->getMorphClass(),
                    'transactionable_id' => $trasactionable->id,
                ]);

                // decrement amount from current transactionable
                $trasactionable->increment('balance', $request->amount);
            });
        }

        return redirect()->back()->with('success', 'Loan installment update successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LoanInstallment  $loanInstallment
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $loanInstallment = LoanInstallment::findOrFail($id);
        $loanInstallment->transactionable->decrement('balance', $loanInstallment->amount);
        $loanInstallment->delete();

        return redirect()->back()->with('success', 'Loan installment deleted successfully.');
    }
}
