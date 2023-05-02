<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Cash;
use App\Models\Showroom;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class TransactionController extends Controller
{
    protected string $permission_for = 'transaction';
    private $transaction;
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $this->hasPermission('view');
        $transactions = Transaction::orderByDesc('date')
            ->orderByDesc('created_at')
            ->paginate(30);

        return Inertia::render('Transaction/Index', compact('transactions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        $banks = Bank::with('bankAccounts')->get();
        $cashes = Cash::all();
        $showrooms = Showroom::select('id', 'name')->get();

        return Inertia::render('Transaction/Transaction', compact('banks', 'cashes', 'showrooms'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(TransactionRequest $request)
    {
        $this->hasPermission('create');
        \DB::transaction(function() use($request) {
            $data = $request->validated();
            $data['user_id'] = \Auth::id();
            $this->updateFromPaymentMethodBalance($request);
            $this->updateToPaymentMethodBalance($request);

            $this->transaction = Transaction::create($data);
        });

        if ($this->transaction) {
            return Redirect::route('transaction.index')->with('success', 'Transaction created successfully.');
        }
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
     * @return \Inertia\Response
     */
    public function edit($id)
    {
        $this->hasPermission('update');
        $oldTransaction = Transaction::findOrFail($id);
        $banks = Bank::with('bankAccounts')->get();
        $cashes = Cash::all();
        $showrooms = \DB::table('showrooms')->get();

        return Inertia::render('Transaction/Transaction', compact('banks', 'cashes', 'showrooms', 'oldTransaction'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(TransactionRequest $request, $id)
    {
        $this->hasPermission('update');

        \DB::transaction(function() use($request, $id) {
            $oldTransaction = Transaction::findOrFail($id);
            $data = $request->validated();
            $data['user_id'] = \Auth::id();
            $this->updateOldFromPaymentMethodBalance($oldTransaction);
            $this->updateOldToPaymentMethodBalance($oldTransaction);
            $this->updateFromPaymentMethodBalance($request);
            $this->updateToPaymentMethodBalance($request);
            $oldTransaction->update($data);
            $this->transaction = $oldTransaction;
        });

        if ($this->transaction) {
            return Redirect::route('transaction.index')->with('success', 'Transaction updated successfully.');
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
        \DB::transaction(function () use ($id) {
            $transaction = Transaction::findOrFail($id);
            $this->updateOldFromPaymentMethodBalance($transaction);
            $this->updateOldToPaymentMethodBalance($transaction);

            $this->transaction = $transaction->delete();
        });
        if ($this->transaction) {
            return redirect()->back();
        }
    }

    /**
     * decrement from payment method balance. // cash/bank account
     * @param $request
     * @return void
     */
    public function updateFromPaymentMethodBalance($request)
    {
        if ($request->transaction_from == 'cash') {
            Cash::findOrFail($request->transaction_from_id)->decrement('balance', $request->amount);
        }else{
            BankAccount::findOrFail($request->transaction_from_id)->decrement('balance', $request->amount);
        }
    }

    /**
     * increment to payment method balance
     * @param $request
     * @return void
     */
    public function updateToPaymentMethodBalance($request)
    {
        if ($request->transaction_to == 'cash') {
            Cash::findOrFail($request->transaction_to_id)->increment('balance', $request->amount);
        }else{
            BankAccount::findOrFail($request->transaction_to_id)->increment('balance', $request->amount);
        }
    }

    /**
     * increment old from payment method balance
     * @param $oldTransaction
     * @return void
     */
    public function updateOldFromPaymentMethodBalance($oldTransaction)
    {
        $oldTransaction->from_transaction->increment('balance', $oldTransaction->amount);
    }

    /**
     * decrement old from payment method balance
     * @param $oldTransaction
     * @return void
     */
    public function updateOldToPaymentMethodBalance($oldTransaction)
    {
        $oldTransaction->to_transaction->decrement('balance', $oldTransaction->amount);
    }
}
