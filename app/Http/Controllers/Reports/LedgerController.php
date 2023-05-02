<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\DueManage;
use App\Models\Mokam;
use App\Models\Party;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\Sale;
use App\Models\SaleReturn;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use ProtoneMedia\LaravelCrossEloquentSearch\Search;

class LedgerController extends Controller
{
    protected string $permission_for = 'ledger';

    public array $data = [];

    /**
     * customer ledger details
     *
     * @return \Inertia\Response
     */
    public function customerLedger()
    {
        $this->hasPermission('customer_ledger');
        $this->data['total_debit'] = 0;
        $this->data['total_credit'] = 0;
        $this->data['total_adjustment'] = 0;
        $this->data['total_discount'] = 0;

        if (\request('search')) {
            $this->data['party'] = Party::where('id', \request('party_id'))->first();
            $this->data['party_balance'] = $this->data['party']->balance;

            $sale_query = Sale::query()
                ->with('details')
                ->where('party_id', request('party_id'))
                ->selectRaw("*, 'sale' as 'type'");

            $sale_return_query = SaleReturn::query()
                ->with('details')
                ->where('party_id', request('party_id'))
                ->selectRaw("*, 'sale_return' as 'type'");

            $due_management_query = DueManage::query()
                ->where('party_id', request('party_id'))
                ->selectRaw("*, 'due_manage' as 'type'");

            if (\request('from_date')) {
                $sale_query->whereDate('date', '>=', \request('from_date'));
                $sale_return_query->whereDate('date', '>=', \request('from_date'));
                $due_management_query->whereDate('date', '>=', \request('from_date'));
            }

            if (\request('to_date')) {
                $sale_query->whereDate('date', '<=', \request('to_date'));
                $sale_return_query->whereDate('date', '<=', \request('to_date'));
                $due_management_query->whereDate('date', '<=', \request('to_date'));
            }

            $this->data['party_ledgers'] =
                Search::add($sale_query)
                    ->orderBy('date')
                    ->orderBy('created_at')
                    ->add($sale_return_query)
                    ->orderBy('date')
                    ->orderBy('created_at')
                    ->add($due_management_query)
                    ->orderBy('date')
                    ->orderBy('created_at')
                    ->get();

            foreach ($this->data['party_ledgers'] as $ledger) {
                $this->data['total_discount'] += $ledger->discount;
                $this->data['total_debit'] += ($ledger->grand_total + $ledger->return_paid);
                $this->data['total_credit'] += ($ledger->total_paid + $ledger->return_grand_total);
                if ($ledger->type == 'due_manage') {
                    $this->data['total_adjustment'] += $ledger->adjustment;
                    if ($ledger->amount <= 0) {
                        $this->data['total_debit'] += abs($ledger->amount);
                    } else {
                        $this->data['total_credit'] += $ledger->amount;
                    }
                }
            }
        }

        return Inertia::render('Report/Ledger/CustomerLedger', [
            'data' => $this->data,
        ]);
    }

    /**
     * supplier ledger details
     *
     * @return \Inertia\Response
     */
    public function supplierLedger()
    {
        $this->hasPermission('supplier_ledger');
        $parties = Party::supplier()->get();

        $this->data['total_debit'] = 0;
        $this->data['total_credit'] = 0;
        $this->data['total_adjustment'] = 0;
        $this->data['total_discount'] = 0;

        if (\request('search')){
            $this->data['party'] = Party::where('id', \request('party_id'))->first();
            $this->data['party_balance'] = $this->data['party']->balance;

            $purchase_query = Purchase::query()
                ->where('party_id', request('party_id'))
                ->selectRaw("*, 'purchase' as 'type'");

            $purchase_return_query = PurchaseReturn::query()
                ->where('party_id', request('party_id'))
                ->selectRaw("*, 'purchase_return' as 'type'");

            $supplier_due_management_query = DueManage::query()
                ->where('party_id', request('party_id'))
                ->selectRaw("*, 'due_manage' as 'type'");

            if (\request('from_date')) {
                $purchase_query->whereDate('date', '>=', \request('from_date'));
                $purchase_return_query->whereDate('date', '>=', \request('from_date'));
                $supplier_due_management_query->whereDate('date', '>=', \request('from_date'));
            }

            if (\request('to_date')) {
                $purchase_query->whereDate('date', '<=', \request('to_date'));
                $purchase_return_query->whereDate('date', '<=', \request('to_date'));
                $supplier_due_management_query->whereDate('date', '<=', \request('to_date'));
            }

            $this->data['party_ledgers'] =
                Search::add($purchase_query)
                    ->orderBy('date')
                    ->orderBy('created_at')
                    ->add($purchase_return_query)
                    ->orderBy('date')
                    ->orderBy('created_at')
                    ->add($supplier_due_management_query)
                    ->orderBy('date')
                    ->orderBy('created_at')
                    ->get();

            foreach ($this->data['party_ledgers'] as $ledger) {
                $this->data['total_discount'] += $ledger->discount;
                $this->data['total_debit'] += ($ledger->grand_total + $ledger->return_paid);
                $this->data['total_credit'] += ($ledger->paid + $ledger->purchase_return_total);

                if ($ledger->type == 'due_manage') {
                    $this->data['total_adjustment'] += $ledger->adjustment;
                    if ($ledger->amount >= 0) {
                        $this->data['total_debit'] += $ledger->amount;
                    } else {
                        $this->data['total_credit'] += abs($ledger->amount);
                    }
                }
            }
        }

        return Inertia::render('Report/Ledger/SupplierLedger', [
            'parties' => $parties,
            'data' => $this->data,
        ]);
    }

}
