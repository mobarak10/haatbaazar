<?php

namespace App\Http\Controllers;

use App\Http\Requests\SalaryRequest;
use App\Models\AdvancedSalary;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\Cash;
use App\Models\Salary;
use App\Models\Showroom;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SalaryController extends Controller
{
    protected string $permission_for = 'salary';

    private $salary;

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $this->hasPermission('view');
//        return \request()->all();
        $previous_month = Carbon::now()->subMonth()->format('Y-m').'-01';
        $request_month = Carbon::parse(\request()->salary_month)->format('Y-m-d');
        $salary_month = \request()->search ? $request_month : $previous_month;
        $month = \request()->search ? Carbon::parse(\request()->salary_month)->format('F Y') : Carbon::today()->subMonth()->format('F Y');
        $users = User::addTotalAdvancedPaidAmount()
            ->addTotalAdvancedReceiveAmount()
            ->with(['salaries' => function ($query) use ($salary_month) {
                $query->where('salary_month', $salary_month);
            }])
            ->get();

        return Inertia::render('Salary/Index', compact('users', 'month'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        $this->hasPermission('create');
        $user_id = \request()->id ?? null;
        $month = null;
        if (\request()->id) {
            $month = Carbon::parse(\request()->month);
            $month = $month->format('Y-m');
        }
        $users = User::addTotalAdvancedPaidAmount()
            ->addTotalAdvancedReceiveAmount()
            ->get();
        $cashes = Cash::all();
        $banks = Bank::with('bankAccounts')->get();
        $showrooms = Showroom::all();

        return Inertia::render('Salary/Salary', compact(
            'users',
            'showrooms',
            'cashes',
            'banks',
            'user_id',
            'month',
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SalaryRequest $request)
    {
        $this->hasPermission('create');
        $exists = Salary::where('user_id', $request->user_id)->where('salary_month', $request->salary_month)->first();
        if ($exists) {
            return \redirect()->back();
        }
        DB::transaction(function () use ($request) {
            $data = $request->validated();
            $data['salary_no'] = str_pad(Salary::max('id') + 1, 8, 0, STR_PAD_LEFT);
            $salary = Salary::create($data);
            $this->salary = $salary;
            // update cash or bank balance
            $this->updateCashBankBalance($request);
            $this->saveSalaryDetails($request, $salary);
        });

        if ($this->salary) {
            return \redirect()->route('salary.show', $this->salary->id);
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
        $salary = Salary::with('user', 'details')
            ->addTotalSalaryPaidAmount()
            ->findOrFail($id);

        return Inertia::render('Salary/Show', compact('salary'));
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
        $oldSalary = Salary::with('details', 'advancedSalary')->findOrFail($id);
        $month = Carbon::parse($oldSalary->salary_month)->format('Y-m');
        $users = User::all();
        $cashes = Cash::all();
        $banks = Bank::with('bankAccounts')->get();
        $showrooms = Showroom::all();

        return Inertia::render('Salary/Salary', compact(
            'users',
            'showrooms',
            'cashes',
            'oldSalary',
            'banks',
            'month',
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(SalaryRequest $request, $id)
    {
        $this->hasPermission('update');

        $exists = Salary::where('user_id', $request->user_id)
            ->where('salary_month', $request->salary_month)
            ->first();

        if ($exists && $exists->id != $id) {
            return \redirect()->back();
        }
        DB::transaction(function () use ($request, $id) {
            $oldSalary = Salary::AddTotalSalaryPaidAmount()->findOrFail($id);
            $data = $request->validated();
            $data['salary_no'] = str_pad(Salary::max('id') + 1, 8, 0, STR_PAD_LEFT);
            $oldSalary->update($data);
            $this->salary = $oldSalary;
            $this->updatePreviousSalaryDetail($oldSalary);
            // update cash or bank balance
            $this->updateCashBankBalance($request);
            $this->saveSalaryDetails($request, $oldSalary);
        });

        if ($this->salary) {
            return \redirect()->route('salary.show', $this->salary->id);
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
        $this->hasPermission('delete');
        DB::transaction(function () use ($id) {
            $oldSalary = Salary::AddTotalSalaryPaidAmount()->findOrFail($id);
//          <!-- update previous record -->
            $this->updatePreviousSalaryDetail($oldSalary);
//          <!-- update previous record -->

            $this->salary = $oldSalary->delete();
        });

        if ($this->salary) {
            return redirect()->back();
        }
    }

    /**
     * update cash or bank balance
     *
     * @param $request
     * @return void
     */
    public function updateCashBankBalance($request)
    {
        $total_paid = ($request->basic_salary + $request->advanced + $request->bonus + $request->deduction);

        if ($request->cash_id) {
            Cash::findOrFail($request->cash_id)->decrement('balance', $total_paid);
        } else {
            BankAccount::findOrFail($request->bank_account_id)->decrement('balance', $total_paid);
        }
    }

    /**
     * save salary details
     *
     * @param $request
     * @return void
     */
    public function saveSalaryDetails($request, $salary)
    {
        // if it has basic salary then save it
        if ($request->basic_salary) {
            $salary_details = [
                'purpose' => 'basic_salary',
                'amount' => $request->basic_salary,
            ];
            $salary->details()->create($salary_details);
        }
        // if it has bonus then save it
        if ($request->bonus) {
            $salary_details = [
                'purpose' => 'bonus',
                'amount' => $request->bonus,
            ];
            $salary->details()->create($salary_details);
        }
        // if it has advanced then save it
        if ($request->advanced) {
            AdvancedSalary::create([
                'showroom_id' => $request->showroom_id,
                'user_id' => $salary->user_id,
                'salary_id' => $salary->id,
                'date' => $request->given_date,
                'amount' => -1 * $request->advanced,
                'cash_id' => $request->cash_id,
                'bank_account_id' => $request->bank_account_id,
            ]);
            $salary_details = [
                'purpose' => 'advanced',
                'amount' => $request->advanced,
            ];
            $salary->details()->create($salary_details);
        }
        // if it has deduction then save it
        if ($request->deduction) {
            $salary_details = [
                'purpose' => 'deduction',
                'amount' => $request->deduction,
            ];
            $salary->details()->create($salary_details);
        }
    }

    public function updatePreviousSalaryDetail($oldSalary)
    {
        if ($oldSalary->cash_id) {
            Cash::findOrFail($oldSalary->cash_id)->increment('balance', $oldSalary->total_salary_paid);
        } else {
            BankAccount::findOrFail($oldSalary->cash_id)->increment('balance', $oldSalary->total_salary_paid);
        }
        if ($oldSalary->advancedSalary) {
            $oldSalary->advancedSalary->delete();
        }
        $oldSalary->details()->delete();
    }
}
