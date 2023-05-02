<?php

namespace App\Http\Controllers;

use App\Helpers\QuantityHelper;
use App\Models\BankAccount;
use App\Models\Cash;
use App\Models\DamageDetails;
use App\Models\Expense;
use App\Models\Party;
use App\Models\Product;
use App\Models\ProductionDetails;
use App\Models\ProductTransferDetails;
use App\Models\Purchase;
use App\Models\PurchaseDetails;
use App\Models\PurchaseReturnDetail;
use App\Models\Sale;
use App\Models\SaleDetails;
use App\Models\SaleReturn;
use App\Models\SaleReturnDetails;
use App\Models\Stock;
use App\Models\UserShowroom;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    protected string $permission_for = 'dashboard';
    private array $data = [];

    /**
     * dashboard details
     */
    public function index()
    {
        $this->hasPermission('view');
        $today_date = date('Y-m-d');

        $this->data['cash_balance'] = Cash::all()->sum('balance');
        $this->data['bank_balance'] = BankAccount::all()->sum('balance');
        $products = Product::query();
        $products = $products->select(
            'products.id',
            'purchase_price',
            'sale_price',
            'products.divisor_number',
        )
        ->addTotalQuantity()
        ->addAveragePurchasePrice()
        ->get();

        $this->data['total_product_price'] = $products->map(function($product) {
                $product['price'] = ($product->total_quantity / $product->divisor_number) * $product->average_purchase_price;
                return $product;
            })->sum('price');

        $this->data['today_sale_amount'] = Sale::where('date', $today_date)->get()->sum('grand_total');
        $this->data['today_sale_return_amount'] = SaleReturn::where('date', $today_date)->get()->sum('return_grand_total');
        $this->data['customer_due'] = Party::customer()
            ->where('balance', '>', 0)
            ->get()
            ->sum('balance');
        $this->data['expense'] = Expense::where('date', $today_date)->get()->sum('amount');
        $this->data['purchase'] = Purchase::where('date', $today_date)->get()->sum('grand_total');
        $this->data['supplier_due'] = Party::supplier()
            ->where('balance', '<', 0)
            ->get()
            ->sum('balance');
        $this->data['purchase_monthly_data'] = $this->getMonthlyPurchaseData();
        $this->data['sale_monthly_data'] = $this->getMonthlySaleData();
        $this->data['daily_sale_data'] = $this->getDailySaleData();
        $this->data['daily_purchase_data'] = $this->getDailyPurchaseData();

        return Inertia::render('Dashboard', [
            'data' => $this->data,
        ]);
    }


    /**
     * get monthly purchase total data
     * @return array
     */
    public function getMonthlyPurchaseData()
    {
        $carbon = $this->getCarbonMonthlyData();
        $purchase_monthly_data = [];

        foreach (Carbon::parse($carbon['startOfYear'])->range($carbon['endOfYear'], $carbon['interval']) as $month) {
            // get every month data
            $purchase_monthly_data[$month->format('M')] = Purchase::whereMonth('date', $month)
                ->get()->sum('grand_total');
        }
        return $purchase_monthly_data;
    }

    /**
     * get monthly sale total data
     * @return array
     */
    public function getMonthlySaleData()
    {
        $carbon = $this->getCarbonMonthlyData();
        $sale_monthly_data = [];

        foreach (Carbon::parse($carbon['startOfYear'])->range($carbon['endOfYear'], $carbon['interval']) as $month) {
            // get every month data
            $sale_monthly_data[$month->format('M')] = sale::whereMonth('date', $month)
                ->get()->sum('grand_total');
        }
        return $sale_monthly_data;
    }

    /**
     * get daily sale data
     * @return array
     */
    public function getDailySaleData(): array
    {
        $carbon = $this->getCarbonDailyData();
        $daily_sale_chart_data = [];

        foreach (Carbon::parse($carbon['start'])->range($carbon['end'], $carbon['interval']) as $date) {
            // get every month data
            $daily_sale_chart_data[$date->format('j')] = sale::whereDate('date', $date)
                ->get()->sum('grand_total');

        }
        return $daily_sale_chart_data;
    }

    /**
     * get daily purchase data
     * @return array
     */
    public function getDailyPurchaseData(): array
    {
        $carbon = $this->getCarbonDailyData();
        $daily_purchase_chart_data = [];

        foreach (Carbon::parse($carbon['start'])->range($carbon['end'], $carbon['interval']) as $date) {
            $daily_purchase_chart_data[$date->format('j')] = Purchase::whereDate('date', $date)
                ->get()->sum('grand_total');

        }
        return $daily_purchase_chart_data;
    }

    /**
     * get carbon data
     * get start of month of a year
     * get end of month of a year
     * get interval between start of year & end of year
     * @return array
     */
    public function getCarbonMonthlyData(): array
    {
        $data = [];
        $now = Carbon::now()->toImmutable();
        $data['startOfYear'] = $now->startOfYear();
        $data['endOfYear'] = $now->endOfYear();
        $data['interval'] = CarbonInterval::months(1);

        return $data;
    }

    /**
     * get carbon daily data
     * @return array
     */
    public function getCarbonDailyData(): array
    {
        $data = [];
        $data['start'] = Carbon::now()->startOfMonth();
        $data['end'] = Carbon::now()->endOfMonth();
        $data['interval'] = CarbonInterval::day(1);

        return $data;
    }
}
