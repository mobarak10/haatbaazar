<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Inertia\Inertia;

class BankAccountController extends Controller
{
    protected string $permission_for = 'bank_account';
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $this->hasPermission('view');
        return Inertia::render('BankAccount/Index', [
            'bank_accounts' => BankAccount::with('bank')->orderBy('account_name')->paginate(25),
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
        return Inertia::render('BankAccount/Create', [
            'banks' => Bank::all(),
        ]);
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
        $data = $request->validate([
            'bank_id' => 'required|integer',
            'balance' => 'nullable|numeric',
            'account_name' => 'required|string',
            'account_number' => 'required|string',
            'branch' => 'required|string',
            'note' => 'nullable|string',
        ]);

        BankAccount::create($data);

        return Redirect::route('bank-account.index')->with('success', 'Bank account created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->hasPermission('show');
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
        return Inertia::render('BankAccount/Edit', [
            'banks' => Bank::all(),
            'bankAccount' =>BankAccount::findOrFail($id),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->hasPermission('update');
        $data = $request->validate([
            'bank_id' => 'required|integer',
            'balance' => 'nullable|numeric',
            'account_name' => 'required|string',
            'account_number' => 'required|string',
            'branch' => 'required|string',
            'note' => 'nullable|string',
        ]);
        $bank_account = BankAccount::findOrFail($id);
        $bank_account->update($data);

        return Redirect::route('bank-account.index')->with('success', 'Bank account update successfully.');
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
        $bankAccount = BankAccount::findOrFail($id);
        $bankAccount->delete();
        return Redirect::route('bank-account.index');
    }

    public function allBankAccount()
    {
        return BankAccount::with('bank')->get();
    }
}
