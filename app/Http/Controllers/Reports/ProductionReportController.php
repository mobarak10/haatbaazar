<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Production;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductionReportController extends Controller
{
    protected string $permission_for = 'report';

    /**
     * purchase report
     * @return \Inertia\Response
     */
    public function index()
    {
        $this->hasPermission('production_report');
        $records = [];

        if(\request()->search) {
            $records['productions'] = Production::query();
            if (request()->from_date) {
                $records['productions'] = $records['productions']->whereBetween('date', [request()->from_date, request()->to_date]);
            }

            if (\request()->showroom_id) {
                $records['productions'] = $records['productions']
                    ->where('from_showroom_id', \request()->showroom_id)
                    ->orWhere('to_showroom_id', \request()->showroom_id);
            }

            if (\request()->product_id) {
                $records['productions'] = $records['productions']->with(['productionDetails' => function($query){
                    $query->where('product_id', request()->product_id);
                }])
                    ->whereHas('productionDetails', function ($query){
                        $query->where('product_id', request()->product_id);
                    });
            }

            $records['productions'] = $records['productions']->with('productionDetails')->orderByDesc('id')->paginate(30)->withQueryString();
        }

        $records['showrooms'] = \DB::table('showrooms')->select('id', 'name')->get();
        $records['products'] = \DB::table('products')->select('id', 'name')->get();
        return Inertia::render('Report/Production/Index', [
            'records' => $records,
        ]);
    }
}
