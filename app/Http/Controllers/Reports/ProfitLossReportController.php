<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\IncomeRecord;
use App\Models\Sale;
use App\Models\SaleReturn;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProfitLossReportController extends Controller
{
    protected string $permission_for = 'report';
    private array $data = [];

    /**
     * get profit loss report details
     */
    public function index()
    {
        $this->hasPermission('profit_loss_report');
        $this->data['total_sales_amount'] = $this->totalSalesAmount();
        $this->data['total_purchase_amount'] = $this->totalPurchasesAmount();
        $this->data['total_sales_return_purchase_amount'] = $this->totalSalesReturnPurchaseAmount();
        $this->data['total_sales_return_sale_amount'] = $this->totalSalesReturnSaleAmount();
        $this->data['total_expenses_amount'] = $this->totalExpensesAmount();
        $this->data['total_income_amount'] = $this->totalIncomeAmount();

        $this->data['showrooms'] = \DB::table('showrooms')
            ->select('id', 'name')
            ->get();
        return Inertia::render('Report/ProfitLoss/Index', [
            'data' => $this->data
        ]);
    }

    /**
     * sales data for request query
     * default data is running month data
     * @return mixed
     */
    public function saleQuery()
    {
        $from_date = \request()->from_date ?? Carbon::now()->startOfMonth();
        $to_date = \request()->to_date ?? Carbon::now()->endOfMonth();
        $showroom_id = \request()->showroom_id;
        $sales = Sale::query();
        if ($showroom_id) {
            $sales = $sales->where('showroom_id', $showroom_id);
        }
        if ($from_date){
            $sales = $sales->whereBetween('date', [$from_date, $to_date]);
        }
        return $sales->addTotalPurchasePrice()
            ->get();
    }

    /**
     * sale return data for request query
     * default data is running month data
     * @return mixed
     */
    public function saleReturnQuery()
    {
        $from_date = \request()->from_date ?? Carbon::now()->startOfMonth();
        $to_date = \request()->to_date ?? Carbon::now()->endOfMonth();
        $showroom_id = \request()->showroom_id;
        $sale_returns = SaleReturn::query();
        if ($showroom_id) {
            $sale_returns = $sale_returns->where('showroom_id', $showroom_id);
        }
        if ($from_date){
            $sale_returns = $sale_returns->whereBetween('date', [$from_date, $to_date]);
        }
        return $sale_returns->addTotalPurchasePrice()
            ->get();
    }

    /**
     * expense data for request query
     * default data is running month data
     * @return mixed
     */
    public function expenseQuery()
    {
        $from_date = \request()->from_date ?? Carbon::now()->startOfMonth();
        $to_date = \request()->to_date ?? Carbon::now()->endOfMonth();
        $showroom_id = \request()->showroom_id;
        $expenses = Expense::query();
        if ($showroom_id) {
            $expenses = $expenses->where('showroom_id', $showroom_id);
        }
        if ($from_date){
            $expenses = $expenses->whereBetween('date', [$from_date, $to_date]);
        }
        return $expenses->get();
    }

    /**
     * income data for request query
     * default data is running month data
     * @return mixed
     */
    public function incomeQuery()
    {
        $from_date = \request()->from_date ?? Carbon::now()->startOfMonth();
        $to_date = \request()->to_date ?? Carbon::now()->endOfMonth();
        $showroom_id = \request()->showroom_id;
        $expenses = IncomeRecord::query();
        if ($showroom_id) {
            $expenses = $expenses->where('showroom_id', $showroom_id);
        }
        if ($from_date){
            $expenses = $expenses->whereBetween('date', [$from_date, $to_date]);
        }
        return $expenses->get();
    }


    /**
     * get sales total amount for search data
     * default data is running month data
     * @return mixed
     */
    public function totalSalesAmount()
    {
        $sales = $this->saleQuery();
        return $sales->sum('sale_amount');
    }

    /**
     * get sales total purchase amount for search data
     * default data is running month data
     * @return mixed
     */
    public function totalPurchasesAmount()
    {
        $sales = $this->saleQuery();
        return $sales->sum('total_purchase_price');
    }

    /**
     * get sale return product purchase amount
     * default data is running month data
     * @return mixed
     */
    public function totalSalesReturnPurchaseAmount()
    {
        $sale_returns = $this->saleReturnQuery();
        return $sale_returns->sum('total_purchase_price');
    }

    /**
     * get sale return sale price amount
     * default data is running month data
     * @return mixed
     */
    public function totalSalesReturnSaleAmount()
    {
        $sale_returns = $this->saleReturnQuery();
        return $sale_returns->sum('return_grand_total');
    }

    /**
     * get expense amount
     * default data is running month data
     * @return mixed
     */
    public function totalExpensesAmount()
    {
        $expenses = $this->expenseQuery();
        return $expenses->sum('amount');
    }

    /**
     * get income amount
     * default data is running month data
     * @return mixed
     */
    public function totalIncomeAmount()
    {
        $incomes = $this->incomeQuery();
        return $incomes->sum('amount');
    }
}
