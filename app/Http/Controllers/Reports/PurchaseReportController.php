<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PurchaseReportController extends Controller
{
    protected string $permission_for = 'report';

    /**
     * purchase report
     * @return \Inertia\Response
     */
    public function index()
    {
        $this->hasPermission('purchase_report');
        $records = [];

        if(\request()->search) {
            $records['purchases'] = Purchase::query();
            if (request()->from_date) {
                $records['purchases'] = $records['purchases']->whereBetween('date', [request()->from_date, request()->to_date]);
            }
            if (\request()->party_id) {
                $records['purchases'] = $records['purchases']->where('party_id', \request()->party_id);
            }

            if (\request()->showroom_id) {
                $records['purchases'] = $records['purchases']->where('showroom_id', \request()->showroom_id);
            }

            if (\request()->product_id) {
                $records['purchases'] = $records['purchases']->with(['details' => function($query){
                    $query->where('product_id', request()->product_id);
                }])
                ->whereHas('details', function ($query){
                    $query->where('product_id', request()->product_id);
                });
            }

            $records['purchases'] = $records['purchases']->with('party')->orderByDesc('id')->paginate(30)->withQueryString();
        }

        $records['showrooms'] = \DB::table('showrooms')->select('id', 'name')->get();
        $records['suppliers'] = \DB::table('parties')->where('genus', 'supplier')->select('id', 'name')->get();
        $records['products'] = \DB::table('products')->select('id', 'name')->get();
        return Inertia::render('Report/Purchase/Index', [
            'records' => $records,
        ]);
    }

}
