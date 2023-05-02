<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierRequest;
use App\Models\Party;
use App\Models\Showroom;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class SupplierController extends Controller
{
    protected string $permission_for = 'supplier';
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $this->hasPermission('view');
        $supplier_balance = 0;
        $suppliers = Party::supplier()
            ->orderby('name')
            ->paginate(30);
        foreach ($suppliers as $supplier) {
            $supplier_balance += min($supplier->balance, 0);
        }
        return Inertia::render("Supplier/Index", compact(
            'suppliers',
            'supplier_balance'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        $this->hasPermission('create');

        $showrooms = Showroom::select('id', 'name')->get();
        return Inertia::render('Supplier/Create', compact('showrooms'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SupplierRequest $request)
    {
        $this->hasPermission('create');
//         return response($request->all(), 200);
        $data = $request->validated();
        $data['balance'] = $this->balanceStatus($request);
        Party::create($data);
        return Redirect::route('supplier.index')->with('success', 'Supplier created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Inertia\Response
     */
    public function edit($id)
    {
        $this->hasPermission('update');

        $showrooms = Showroom::select('id', 'name')->get();
        return Inertia::render('Supplier/Edit', [
            'supplier' => Party::findOrFail($id),
            'showrooms' => $showrooms,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SupplierRequest $request, $id)
    {
        $this->hasPermission('update');

        $data = $request->validated();
        $data['balance'] = $this->balanceStatus($request);
        $party = Party::findOrFail($id);
        $party->update($data);

        return Redirect::route('supplier.index')->with('success', 'Supplier updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->hasPermission('delete');
        $party = Party::findOrFail($id);
        $party->delete();

        return \redirect()->back();
    }

    /*
     * get all active supplier
     */
    public function allActiveSuppliers()
    {
        return response(Party::supplier()->orderBy('created_at', 'DESC')->get(), 200);
    }

    /**
     * Get Party details
     * @param Request $request
     * @return ResponseFactory|Response
     */
    public function supplierDetails(Request $request)
    {
        return response(Party::find($request->id), 200);
    }

    /**
     * @param $request
     * return supplier balance positive or negative
     * @return float|int
     */
    public function balanceStatus($request)
    {
        if ($request->balance_status == 'receivable') {
            return abs($request->balance);
        }else{
            return -1 * abs($request->balance);
        }
    }
}
