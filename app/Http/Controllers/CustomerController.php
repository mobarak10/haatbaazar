<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Mokam;
use App\Models\Party;
use App\Models\Showroom;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class CustomerController extends Controller
{
    protected string $permission_for = 'customer';
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $this->hasPermission('view');

        $customers = Party::query();
        if (request()->search) {
            if (request()->name){
                $customers->where('name', 'like', '%' . request()->name . '%');
            }
            if (request()->mokam_id){
                $customers->where('mokam_id', request()->mokam_id);
            }
        }
        $customers = $customers->customer()
            ->orderby('name')
            ->addCustomName()
            ->paginate(30)
            ->withQueryString();
        $mokams = Mokam::all();
        $totalBalance = $customers->sum('balance');

        return Inertia::render('Customer/Index', [
            'customers' => $customers,
            'mokams' => $mokams,
            'totalBalance' => $totalBalance,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        $this->hasPermission('create');
        $mokams = DB::table('mokams')->select('id', 'name')->get();
        $showrooms = Showroom::select('id', 'name')->get();
        return Inertia::render('Customer/Create', compact('mokams', 'showrooms'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CustomerRequest $request)
    {
        $this->hasPermission('create');
        $data = $request->validated();

        $data['balance'] = $this->balanceStatus($request);

        Party::create($data);

        return Redirect::route('customer.index')->with('success', 'Customer created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
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
        $mokams = Mokam::all();
        $showrooms = Showroom::select('id', 'name')->get();
        return Inertia::render('Customer/Edit', [
            'customer' => Party::findOrFail($id),
            'mokams' => $mokams,
            'showrooms' => $showrooms,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(CustomerRequest $request, $id)
    {
        $this->hasPermission('update');
        $data = $request->validated();

        $data['balance'] = $this->balanceStatus($request);

        $party = Party::findOrFail($id);
        $party->update($data);

        return Redirect::route('customer.index')->with('success', 'Customer update successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $this->hasPermission('delete');
        $party = Party::findOrFail($id);
        $party->delete();

        return \redirect()->back();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function newCustomer(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:191',
            'phone'          => 'nullable|max:45',
            'address'        => 'nullable|string',
        ]);

        $data['genus'] = 'customer';

        $customer = Party::create($data);

        return response()->json($customer, 200);
    }

    /**
     * All active customers
     * @return ResponseFactory|Response
     */
    public function allActiveCustomers()
    {
        return response(Party::customer()->select('id', 'name', 'phone', 'address', 'balance', 'mokam_id')
            ->orderBy('name')
            ->get(), 200);
    }

    /**
     * @param $request
     * return customer balance positive or negative
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
