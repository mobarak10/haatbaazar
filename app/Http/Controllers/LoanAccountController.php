<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Inertia\Inertia;
use App\Models\LoanAccount;
use Illuminate\Http\Request;

class  LoanAccountController extends Controller
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
        $loanAccounts = LoanAccount::query()
            ->withCount('loans')
            ->addTotalLoan()
            ->addTotalPaid()
            ->addTotalAdjustment()
            ->addTotalDue()
            ->orderBy('name')
            ->get();

        return Inertia::render("LoanAccount/Index", [
            "loanAccounts" => $loanAccounts
        ]);
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
        $this->hasPermission('create');
        $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'address' => 'nullable|string'
        ]);

        LoanAccount::create($request->only('name', 'phone', 'address'));

        return redirect()->back()->with('success', 'Loan account created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LoanAccount  $loanAccount
     * @return \Illuminate\Http\Response
     */
    public function show(LoanAccount $loanAccount)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LoanAccount  $loanAccount
     * @return \Illuminate\Http\Response
     */
    public function edit(LoanAccount $loanAccount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LoanAccount  $loanAccount
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LoanAccount $loanAccount)
    {
        $this->hasPermission('update');
        $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'address' => 'nullable|string'
        ]);

        $loanAccount->update($request->only('name', 'phone', 'address'));

        return redirect()->back()->with('success', 'Loan account updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LoanAccount  $loanAccount
     * @return \Illuminate\Http\Response
     */
    public function destroy(LoanAccount $loanAccount)
    {
        $this->hasPermission('delete');
        if ($loanAccount->loans()->count()) {
            return redirect()->back()->with('errors', 'Failed to delete loan account. Loan account has some loans.');
        }

        $loanAccount->delete();

        return redirect()->back()->with('success', 'Loan account deleted successfully.');
    }
}
