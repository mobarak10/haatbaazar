<?php

namespace App\Http\Controllers;

use App\Http\Requests\DueMangeRequest;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Cash;
use App\Models\DueManage;
use App\Models\Party;
use App\Models\Showroom;
use App\QueryFilter\FromToDateFilter\FromToDateQuery;
use App\QueryFilter\FromToDateFilter\PartyIdQuery;
use App\QueryFilter\FromToDateFilter\TodayQuery;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SupplierDueManageController extends Controller
{
    protected string $permission_for = 'due_management';
    private $due_manage;
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $this->hasPermission('view');
        $type = 'supplier';
        $party = Party::supplier()->get();
        $due_manages = app(Pipeline::class)
            ->send(DueManage::query())
            ->through([
                PartyIdQuery::class,
                FromToDateQuery::class,
            ])
            ->thenReturn()
            ->supplier()
            ->with('party')
            ->whereHas('party')
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->paginate(30)
            ->withQueryString();

        $total_amount = $due_manages->sum('amount');

        return Inertia::render('DueManage/Index', [
            'due_manages' => $due_manages,
            'type' => $type,
            'total_amount' => $total_amount,
            'party' => $party,
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
        $parties = Party::supplier()->orderBy('name')->get();
        $cashes = Cash::all();
        $showrooms = Showroom::select('name', 'id')->get();
        $banks = Bank::with('bankAccounts')->get();
        $type = 'supplier';

        return Inertia::render('DueManage/Create', [
            'parties' => $parties,
            'cashes' => $cashes,
            'banks' => $banks,
            'type' => $type,
            'showrooms' => $showrooms,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(DueMangeRequest $request)
    {
        $this->hasPermission('create');
        DB::transaction(function() use ($request) {
            $data = $request->validated();
            $data['user_id'] = Auth::id();
            $this->due_manage = DueManage::create($data);
            $due_manage = $this->due_manage;

            $this->updatePaymentDetails($request, $due_manage);
        });

        if ($this->due_manage) {
            return redirect()
                ->route('supplier-due-manage.index')
                ->with('success', 'Due manage created successfully.');
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

        $type = 'supplier';
        $due_manage = DueManage::with('party')->findOrFail($id);

        return Inertia::render('DueManage/Show', compact('type', 'due_manage'));
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
        $parties = Party::supplier()->get();
        $due_manage = DueManage::with('bankAccount')->findOrFail($id);
        $due_manage['formatted_date'] = $due_manage->date->format('Y-m-d');
        $cashes = Cash::all();
        $banks = Bank::with('bankAccounts')->get();
        $type = 'supplier';

        return Inertia::render('DueManage/Create', [
            'parties' => $parties,
            'old_due_manage' => $due_manage,
            'cashes' => $cashes,
            'banks' => $banks,
            'type' => $type,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(DueMangeRequest $request, $id)
    {
        $this->hasPermission('update');
        DB::transaction(function() use ($request, $id) {
            $old_due_manage = DueManage::findOrFail($id);
            $this->updatePreviousPartyBalance($old_due_manage);
            $this->updatePreviousCashBankBalance($old_due_manage);
            $data = $request->validated();
            $old_due_manage->update($data);
            $this->due_manage = $old_due_manage;

            $this->updatePaymentDetails($request, $old_due_manage);
        });

        if ($this->due_manage) {
            return redirect()
                ->route('supplier-due-manage.index')
                ->with('success', 'Due manage updated successfully.');
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
        DB::transaction(function() use ($id) {
            $due_manage = DueManage::findOrFail($id);
            $this->updatePreviousPartyBalance($due_manage);
            $this->updatePreviousCashBankBalance($due_manage);
            $due_manage->delete();
        });

        return redirect()->back()->with('success', 'Due manage delete successfully!');
    }

    /**
     * update due manage party balance,
     * update due manage cash or bank balance,
     * @param $request
     * @param $due_manage
     * @return void
     */
    public function updatePaymentDetails($request, $due_manage)
    {
        $total_balance = ($request->amount - $request->adjustment);
        $due_manage->party()->decrement('balance', $total_balance);
        if ($request->cash_id) {
            $due_manage->cash()->increment('balance', $request->amount);
        }else{
            $due_manage->bankAccount()->increment('balance', $request->amount);
        }
    }

    /**
     * update previous party balance when due manage edit or delete
     * @param $old_due_manage
     * @return void
     */
    public function updatePreviousPartyBalance($old_due_manage)
    {
        $total_balance = $old_due_manage->amount - $old_due_manage->adjustment;
        $old_due_manage->party->increment('balance', $total_balance);
    }

    /**
     * update previous cash or bank balance when due manage edit or delete
     * @param $old_due_manage
     * @return void
     */
    public function updatePreviousCashBankBalance($old_due_manage)
    {
        if ($old_due_manage->cash_id) {
            $old_due_manage->cash->decrement('balance', $old_due_manage->amount);
        }else{
            $old_due_manage->bankAccount->decrement('balance', $old_due_manage->amount);
        }
    }
}
