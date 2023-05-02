<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\IncomeRecord;
use App\Models\IncomeSector;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Inertia\Inertia;

class IncomeReportController extends Controller
{
    protected string $permission_for = 'report';

    /**
     * get income report data
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $this->hasPermission('income_report');
        $incomeDetails = [];
        $months = config('haatbaazar.single_months');
//        return \request()->all();
        if (request()->search) {
            $incomeDetails = IncomeSector::query()
                ->with([
                    'incomeRecords' => function (HasMany $query) {
                        $query->select('income_sector_id', 'date', 'amount')
                            ->whereYear('date', request()->year);
                    },
                ])
                ->get()
                ->map(function (IncomeSector $sector) {
                    $records = $sector->incomeRecords->map(function (IncomeRecord $record) {
                        $record['month'] = $record->date->format('M');

                        return $record;
                    })->groupBy('month')->map(function ($item) {
                        return $item->sum('amount');
                    });

                    $sector['sum_of_each_month'] = $records;

                    return $sector;
                });
        }

//        return $incomeDetails;

        return Inertia::render('Report/IncomeReport/Index', compact(
            'incomeDetails',
            'months'
        ));
    }
}
