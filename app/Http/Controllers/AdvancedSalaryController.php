<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdvancedSalaryRequest;
use App\Models\AdvancedSalary;
use App\Models\BankAccount;
use App\Models\Cash;
use App\Models\Showroom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AdvancedSalaryController extends Controller
{
    protected string $permission_for = 'advanced_salary';
    private $advanced_salary;
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $this->hasPermission('view');
        $users = User::addTotalAdvancedPaidAmount()
            ->addTotalAdvancedReceiveAmount()
            ->get();
        $cashes = Cash::all();
        $bankAccounts = BankAccount::with('bank')->orderBy('account_name')->get();
        $showrooms = Showroom::select('id', 'name')->orderBy('name')->get();

        return Inertia::render('AdvancedSalary/Index', compact(
            'users',
            'cashes',
            'showrooms',
            'bankAccounts')
        );
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
    public function store(AdvancedSalaryRequest $request)
    {
        $this->hasPermission('create');
        DB::transaction(function () use($request) {
            $data = $request->validated();
            $this->advanced_salary = AdvancedSalary::create($data);

            if ($request->cash_id) {
                Cash::findOrFail($request->cash_id)->increment('balance', $request->amount);
            }else{
                BankAccount::findOrFail($request->bank_account_id)->increment('balance', $request->amount);
            }
        });
        if ($this->advanced_salary) {
            return redirect()->back();
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
        $userAdvancedSalaries = AdvancedSalary::orderByDesc('id')
            ->where('user_id', $id)
            ->paginate(25);
        $users = User::addTotalAdvancedPaidAmount()
            ->addTotalAdvancedReceiveAmount()
            ->get();
        $cashes = Cash::all();
        $bankAccounts = BankAccount::with('bank')->get();
        $showrooms = Showroom::select('id', 'name')->get();

        return Inertia::render('AdvancedSalary/Show', compact(
            'userAdvancedSalaries',
                'users',
                'cashes',
                'bankAccounts',
                'showrooms'
            )
        );
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
     * @return \Illuminate\Http\Response
     */
    public function update(AdvancedSalaryRequest $request, $id)
    {
        $this->hasPermission('update');
        DB::transaction(function () use($request, $id) {
            $advanced_salary = AdvancedSalary::findOrFail($id);
            $data = $request->validated();
            $this->advanced_salary = $advanced_salary;
            $this->updatePreviousCashBankBalance($advanced_salary);
            $advanced_salary->update($data);

            if ($request->cash_id) {
                Cash::findOrFail($request->cash_id)->increment('balance', $request->amount);
            }else{
                BankAccount::findOrFail($request->bank_account_id)->increment('balance', $request->amount);
            }
        });
        if ($this->advanced_salary) {
            return redirect()->back();
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
        $advanced_salary = AdvancedSalary::findOrFail($id);
        DB::transaction(function () use($advanced_salary) {
            $this->updatePreviousCashBankBalance($advanced_salary);
            $advanced_salary->delete();
        });

        return redirect()->back();
    }

    /**
     * update previous advance cash or bank account balance
     * @param $advanced_salary
     * @return void
     */
    public function updatePreviousCashBankBalance($advanced_salary)
    {
        if ($advanced_salary->cash_id) {
            $advanced_salary->cash()->decrement('balance', $advanced_salary->amount);
        }else{
            $advanced_salary->bankAccount()->decrement('balance', $advanced_salary->amount);
        }
    }
}
