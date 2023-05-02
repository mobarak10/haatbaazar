<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Cash;
use App\Models\ClosingBalance;
use App\Models\DueManage;
use App\Models\Expense;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\Showroom;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CashBookController extends Controller
{
    protected string $permission_for = 'cash_book';

    /**
     * show cash book details
     * @return \Inertia\Response
     */
    public function index()
    {
        $this->hasPermission('view');

        if (\request('search')){
            $date = \request('date');
        }else{
            $date = date('Y-m-d');
        }
        $data = $this->cashBookData($date);
        $showrooms = Showroom::all();
        return Inertia::render('Report/CashBook/Index', [
            'data' => $data,
            'showrooms' => $showrooms,
        ]);
    }

    /**
     * get date wise data
     * @param $date
     * @return mixed
     */
    public function cashBookData($date)
    {
        $opening_date = '2022-01-01';
        $previous_day = date('Y-m-d', strtotime('-1 day', strtotime($date)));
        // get closing balance
        $this->data['opening_balance'] = ClosingBalance::where('date', $previous_day)->where('showroom_id', \request('showroom_id'))->first();

        if (!$this->data['opening_balance']){
            $this->data['opening_balance'] = ClosingBalance::whereBetween('date', [$opening_date, $previous_day])->where('showroom_id', \request('showroom_id'))->get()->last();
        }

        $this->data['cash_balance'] = Cash::where('showroom_id', \request('showroom_id'))
            ->get()
            ->sum('balance');

        $this->data['sales'] = Sale::addPartyName()
            ->where('showroom_id', \request('showroom_id'))
            ->where('paid', '>', 0)
            ->whereDate('date', $date)
            ->get();

        $this->data['total_sale'] = $this->data['sales']->sum('paid');

        $this->data['sale_returns'] = SaleReturn::addPartyName()
            ->where('showroom_id', \request('showroom_id'))
            ->whereDate('date', $date)
            ->get();

        $this->data['total_sale_return'] = $this->data['sale_returns']->sum('return_paid');

        $this->data['due_receives'] = DueManage::addPartyName()
            ->whereDate('date', $date)
            ->where('showroom_id', \request('showroom_id'))
            ->where('amount', '>', 0)
            ->get();

        $this->data['total_due_receive'] = $this->data['due_receives']->sum('amount');

        $this->data['expenses'] = Expense::with('costSubcategory.costCategory')
            ->where('showroom_id', \request('showroom_id'))
            ->whereDate('date', $date)
            ->get();

        $this->data['total_expense'] = $this->data['expenses']->sum('amount');

        $this->data['purchases'] = Purchase::addPartyName()
            ->where('showroom_id', \request('showroom_id'))
            ->where('paid', '>', 0)
            ->whereDate('date', $date)
            ->get();

        $this->data['total_purchase'] = $this->data['purchases']->sum('paid');

        $this->data['purchase_returns'] = PurchaseReturn::with('party.mokam')
            ->where('showroom_id', \request('showroom_id'))
            ->whereDate('date', $date)
            ->get();
        $this->data['total_purchase_return'] = $this->data['purchase_returns']->sum('return_paid');
        $this->data['due_payments'] = DueManage::with('party.mokam')->whereDate('date', $date)->where('amount', '<', 0)->get();
        $this->data['total_due_payment'] = $this->data['due_payments']->sum('amount');

        return $this->data;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric'
        ]);

        $exist = ClosingBalance::where('date', $request->date)->first();
        if ($exist){
            return redirect()->back()->withError('Closing balance already exist for this date');
        }else{
            ClosingBalance::create($data);
        }
        return redirect()->back()->withSuccess('Balance close successfully');
    }
}
