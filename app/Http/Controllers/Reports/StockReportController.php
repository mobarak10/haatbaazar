<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Showroom;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class StockReportController extends Controller
{
    protected string $permission_for = 'report';

    public function index()
    {
        $this->hasPermission('stock_report');
        $records = [];
        $from_date = request()->date ?? Carbon::today()->toDateString();
        $to_date = Carbon::today()->toDateString();
        $records['showrooms'] = Showroom::all();
        $records['products'] = DB::table('products')->select('id', 'name')->get();

        if (\request()->search){
            $records['search'] = true;
            $records['product_id'] = request()->product_id;
            $records['date'] = request()->date;
            $records['showroom_id'] = request()->showroom_id;
            $searchProducts = Product::query();
            if (\request()->product_id) {
                $searchProducts = $searchProducts->where('id', \request()->product_id);
            }
            if (\request()->showroom_id){
                $searchProducts = $searchProducts->with(['showrooms' => function ($query) {
                    $query->where('showroom_id', request()->showroom_id);
                }])
                    ->whereHas('showrooms', function ($query) {
                        $query->where('showroom_id', request()->showroom_id);
                    });
            }
            $records['searchProducts'] = $searchProducts->with(
                ['saleDetails' => function($query) use($from_date, $to_date) {
                    $query->whereBetween('date', [
                        $from_date,
                        $to_date
                    ]);
                    if (\request()->showroom_id) {
                        $query->where('showroom_id', request()->showroom_id);
                    }
                },
                    'saleReturnDetails' => function($query) use($from_date, $to_date) {
                        $query->whereBetween('date', [
                            $from_date,
                            $to_date
                        ]);
                        if (\request()->showroom_id) {
                            $query->where('showroom_id', request()->showroom_id);
                        }
                    },
                    'purchaseDetails' => function($query) use($from_date, $to_date) {
                        $query->whereBetween('date', [
                            $from_date,
                            $to_date
                        ]);
                        if (\request()->showroom_id) {
                            $query->where('showroom_id', request()->showroom_id);
                        }
                    },
                    'purchaseReturnDetails' => function($query) use($from_date, $to_date) {
                        $query->whereBetween('date', [
                            $from_date,
                            $to_date
                        ]);
                        if (\request()->showroom_id) {
                            $query->where('showroom_id', request()->showroom_id);
                        }
                    },
                    'damageDetails' => function($query) use($from_date, $to_date) {
                        $query->whereBetween('date', [
                            $from_date,
                            $to_date
                        ]);
                        if (\request()->showroom_id) {
                            $query->where('showroom_id', request()->showroom_id);
                        }

                    },
                    'productionDetails' => function($query) use($from_date, $to_date) {
                        $query->whereBetween('date', [
                            $from_date,
                            $to_date
                        ]);
                        if (\request()->showroom_id) {
                            $query->where('showroom_id', request()->showroom_id);
                        }

                    }],
            )
                ->with('unit')
                ->addTotalQuantity()
                ->paginate(25)
                ->withQueryString();
            $records['searchProducts']->makeHidden([
                'unit_code',
                'quantity',
                'product_unit_labels',
                'total_product_quantity',
                \request()->showroom_id ? '' : 'total_product_quantity_showroom_wise',
                'total_product_quantity_in_unit',
                'average_purchase_price',
            ]);
        }

        return Inertia::render('Report/Stock/Index', compact('records'));
    }
}
