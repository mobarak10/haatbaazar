<?php

namespace App\Http\Controllers\Reports;

use App\Helpers\QuantityHelper;
use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Cash;
use App\Models\DailyBalance;
use App\Models\Party;
use App\Models\Product;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OverallReportController extends Controller
{
    protected string $permission_for = 'report';
    private array $data = [];

    /**
     * get daily balance data
     * @return \Inertia\Response
     */
    public function index()
    {
        $this->hasPermission('overall_report');
        $this->data['cash_balance'] = Cash::all()->sum('balance');
        $this->data['bank_balance'] = BankAccount::all()->sum('balance');
        $this->data['customer_balance'] = Party::customer()
                                            ->where('balance', '>', 0)
                                            ->sum('balance');
        $this->data['supplier_balance'] = Party::supplier()
                                        ->where('balance', '>', 0)
                                        ->sum('balance');

        $products = Product::with(['unit' => function($query) {
            $query->select('id', 'label', 'relation');
        }, 'stock'])
            ->select(
                'id',
                'unit_id',
                'purchase_price',
            )
            ->get();
        $products = $products->makeHidden([
            'total_product_quantity_in_unit',
            'average_purchase_price',
            'quantity',
            'total_product_quantity',
            'total_product_quantity_showroom_wise'
        ]);
        $products = $products->map(function($product) {
            $product['total_quantity'] = $product->stock->sum('quantity');
            return $product;
        });
        $this->data['total_product_price'] = $products->map(function($product) {
            $product['stock_price'] = QuantityHelper::priceQuantity($product, $product->total_quantity) * $product->purchase_price;
            $product['damage_price'] = QuantityHelper::priceQuantity($product, $product->stock->sum('damage_quantity')) * $product->purchase_price;
            return $product;
        });

        $this->data['total_stock'] = $products->sum('stock_price');
        $this->data['total_damage'] = $products->sum('damage_price');
        $this->data['getDailyBalance'] = $this->getDailyBalance();

        return Inertia::render('Report/DailyBalance/Index', [
            'data' => $this->data,
        ]);
    }

    /**
     * get day wise all sum balance of customer, supplier, bank, cash
     * @return array
     */
    public function getDailyBalance(): array
    {
        $carbon = $this->getCarbonDailyData();
        $daily_balance = [];

        foreach (Carbon::parse($carbon['start'])->range($carbon['end'], $carbon['interval']) as $date) {
            // get every month data
            $daily_balance[$date->format('j')] = DailyBalance::whereDate('date', $date)
                ->get()->sum('amount');

        }
        return $daily_balance;
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
