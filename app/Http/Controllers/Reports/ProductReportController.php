<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\SaleDetails;
use App\Models\Showroom;
use App\Models\Unit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductReportController extends Controller
{
    /**
     * @return \Inertia\Response
     */
    public function index()
    {
        $records = [];
        $order_by = '';

        if(\request()->search) {
            $records['search_products'] = Product::query();

            if (\request('type') === 'sale') {
                $order_by = 'total_sale_quantity';
                if (\request('showroom_id')){
                    $records['search_products'] = $records['search_products']->with(['saleDetails' => function($query){
                        $query->where('showroom_id', request()->showroom_id);
                    }])
                    ->whereHas('saleDetails', function ($query){
                        $query->where('showroom_id', request()->showroom_id);
                    })
                    ->addTotalSaleQuantity();
                }

                if (\request('from_date')){
                    $records['search_products'] = $records['search_products']->with(['saleDetails' => function($query){
                        $query->whereBetween('date', [request()->from_date, request()->to_date]);
                    }])
                    ->whereHas('saleDetails', function ($query){
                        $query->whereBetween('date', [request()->from_date, request()->to_date]);
                    })
                    ->addTotalSaleQuantity(request()->from_date, request()->to_date);
                }

                if (\request('product_id')){
                    $records['search_products'] = $records['search_products']->with(['saleDetails' => function($query){
                        $query->where('product_id', \request('product_id'));
                    }])
                    ->whereHas('saleDetails', function ($query){
                        $query->where('product_id', \request('product_id'));
                    })
                    ->addTotalSaleQuantity();

                }
            }else{
                $order_by = 'total_purchase_quantity';
                if (\request('showroom_id')){
                    $records['search_products'] = $records['search_products']->with(['purchaseDetails' => function($query){
                        $query->where('showroom_id', request()->showroom_id);
                    }])
                    ->whereHas('purchaseDetails', function ($query){
                        $query->where('showroom_id', request()->showroom_id);
                    })
                    ->addTotalPurchaseQuantity();
                }

                if (\request('from_date')){
                    $records['search_products'] = $records['search_products']->with(['purchaseDetails' => function($query){
                        $query->whereBetween('date', [request()->from_date, request()->to_date]);
                    }])
                    ->whereHas('purchaseDetails', function ($query){
                        $query->whereBetween('date', [request()->from_date, request()->to_date]);
                    })
                    ->addTotalPurchaseQuantity(request()->from_date, request()->to_date);
                }

                if (\request('product_id')){
                    $records['search_products'] = $records['search_products']->with(['purchaseDetails' => function($query){
                        $query->where('product_id', \request('product_id'));
                    }])
                    ->whereHas('purchaseDetails', function ($query){
                        $query->where('product_id', \request('product_id'));
                    })
                    ->addTotalPurchaseQuantity();
                }
            }

            $records['search_products'] = $records['search_products']
                ->addBrandName()
                ->orderByDesc($order_by)
                ->paginate(30)
                ->withQueryString();
        }

        $records['showrooms'] = Showroom::select('id', 'name')->get();
        $records['units'] = Unit::all();
        $records['products'] = \DB::table('products')->select('id', 'name')->get();
        return Inertia::render('Report/Product/Index', [
            'records' => $records,
        ]);
    }
}
