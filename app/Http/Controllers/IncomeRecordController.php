<?php

namespace App\Http\Controllers;

use App\Http\Requests\IncomeRecordRequest;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Cash;
use App\Models\IncomeRecord;
use App\Models\IncomeSector;
use App\Models\Showroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class IncomeRecordController extends Controller
{
    protected string $permission_for = 'income_record';
    private $income_record;
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $this->hasPermission('view');

        $income_records = IncomeRecord::with('incomeSector')
            ->whereHas('incomeSector')
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->paginate(30);
        $income_sectors = IncomeSector::all();
        $cashes = Cash::all();
        $banks = Bank::with('bankAccounts')->get();
        $showrooms = Showroom::select('id', 'name')->get();
        return Inertia::render('IncomeRecord/Index', [
            'income_records' => $income_records,
            'income_sectors' => $income_sectors,
            'cashes' => $cashes,
            'banks' => $banks,
            'showrooms' => $showrooms,
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
     * @param IncomeRecordRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(IncomeRecordRequest $request)
    {
        $this->hasPermission('create');
        $data = $request->validated();
        $data['user_id'] = Auth::user()->id;
        DB::transaction(function () use($request, $data) {
            $this->income_record = IncomeRecord::create($data);
            if ($request->payment_type == 'cash') {
                Cash::findOrFail($request->cash_id)->increment('balance', $request->amount);
            }else{
                BankAccount::findOrFail($request->bank_account_id)->increment('balance', $request->amount);
            }
        });

        if ($this->income_record){
            return redirect()->back()->with('success', 'Income record created successfully.');
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
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param IncomeRecordRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(IncomeRecordRequest $request, IncomeRecord $incomeRecord)
    {
        $this->hasPermission('update');
        $data = $request->validated();
        DB::transaction(function () use($request, $data, $incomeRecord) {
            $this->updatePreviousCashBankBalance($incomeRecord);
            $this->income_record = $incomeRecord->update($data);
            if ($request->payment_type == 'cash') {
                Cash::findOrFail($request->cash_id)->increment('balance', $request->amount);
            }else{
                BankAccount::findOrFail($request->bank_account_id)->increment('balance', $request->amount);
            }
        });

        if ($this->income_record){
            return redirect()->back()->with('success', 'Income record updated successfully.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(IncomeRecord $incomeRecord)
    {
        $this->updatePreviousCashBankBalance($incomeRecord);
        $incomeRecord->delete();
        return redirect()->back()->with('success', 'Income record delete successfully.');
    }

    public function updatePreviousCashBankBalance($incomeRecord)
    {
        if ($incomeRecord->cash_id){
            $incomeRecord->cash()->decrement('balance', $incomeRecord->amount);
        }else{
            $incomeRecord->bankAccount()->decrement('balance', $incomeRecord->amount);
        }
    }
}
