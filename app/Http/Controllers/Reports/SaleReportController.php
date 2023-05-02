<?php

namespace App\Http\Controllers\Reports;
use App\Http\Controllers\Controller;
use App\Models\Party;
use App\Models\Sale;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SaleReportController extends Controller
{
    protected string $permission_for = 'report';

    /**
     * get sale report details
     * @return mixed
     */
    public function index()
    {
        $this->hasPermission('sale_report');
        $today_date = date('Y-m-d');
        $records = [];
        $records['sale'] = Sale::query();
        if(\request()->search) {
            if (request()->from_date) {
                $records['sale'] = $records['sale']->whereBetween('date', [request()->from_date, request()->to_date]);
            }

            if (\request()->showroom_id) {
                $records['sale'] = $records['sale']->where('showroom_id', \request()->showroom_id);
            }

            if (\request()->mokam_id) {
                $customer_ids = Party::where('mokam_id', \request('mokam_id'))->pluck('id');
                $records['sale'] = $records['sale']->whereIn('party_id', $customer_ids);
            }

            if (\request()->party_id) {
                $records['sale'] = $records['sale']->where('party_id', \request()->party_id);
            }
        }else{
            $records['sale'] = $records['sale']->where('date', $today_date);
        }

        $records['sales'] = $records['sale']
            ->addTotalPurchasePrice()
            ->with('party.mokam')
            ->paginate(30)
            ->withQueryString();

        $total_loss_profit = ($records['sales']->sum('sale_amount') - $records['sales']->sum('total_purchase_price'));
        $total_sale_amount = $records['sales']->sum('sale_amount');

        return Inertia::render('Report/Sale/Index', [
            'records' => $records,
            'total_loss_profit' => $total_loss_profit,
            'total_sale_amount' => $total_sale_amount,
        ]);
    }
}
