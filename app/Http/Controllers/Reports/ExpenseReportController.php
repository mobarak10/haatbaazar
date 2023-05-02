<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\CostSubcategory;
use App\Models\Expense;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Inertia\Inertia;

class ExpenseReportController extends Controller
{
    protected string $permission_for = 'report';

    /**
     * get income report data
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $this->hasPermission('expense_report');
        $expenseDetails = [];
        $months = config('haatbaazar.single_months');
//        return \request()->all();
        if (request()->search) {
            $expenseDetails = CostSubcategory::query()
                ->with([
                    'expenses' => function (HasMany $query) {
                        $query->select('cost_subcategory_id', 'date', 'amount')
                            ->whereYear('date', request()->year);
                    },
                ])
                ->get()
                ->map(function (CostSubcategory $sector) {
                    $records = $sector->expenses->map(function (Expense $record) {
                        $record['month'] = $record->date->format('M');

                        return $record;
                    })->groupBy('month')->map(function ($item) {
                        return $item->sum('amount');
                    });

                    $sector['sum_of_each_month'] = $records;

                    return $sector;
                });
        }

        return Inertia::render('Report/ExpenseReport/Index', compact(
            'expenseDetails',
            'months'
        ));
    }
}
