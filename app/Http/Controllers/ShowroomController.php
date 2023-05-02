<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShowroomRequest;
use App\Models\Product;
use App\Models\Showroom;
use App\Models\Stock;
use App\Models\UserShowroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class ShowroomController extends Controller
{
    protected string $permission_for = 'showroom';
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $this->hasPermission('view');
        return Inertia::render('Showroom/Index',[
            'showrooms' => Showroom::all(),
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
        return Inertia::render('Showroom/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ShowroomRequest $request)
    {
        $this->hasPermission('create');
        $data = $request->validated();

        Showroom::create($data);
        return Redirect::route('showroom.index')->with('success', 'Showroom created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->hasPermission('show');
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
        return Inertia::render('Showroom/Edit', [
            'showroom' => Showroom::findOrFail($id),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ShowroomRequest $request, $id)
    {
        $this->hasPermission('update');
        $data = $request->validated();

        $showroom = Showroom::findOrFail($id);
        $showroom->update($data);

        return Redirect::route('showroom.index')->with('success', 'Showroom updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $this->hasPermission('delete');
        Showroom::find($id)->delete();
        return redirect()
            ->back()
            ->with('success', 'Showroom delete successfully');
    }

    public function allShowroom()
    {
        return response()->json(Showroom::all(), 200);
    }

    /**
     * get showroom wise product
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductForShowroom($id)
    {
        $products = DB::table('products')
             ->join('stocks', function ($join) use ($id) {
                 $join->on('products.id', '=', 'stocks.product_id')
                     ->where('stocks.showroom_id', '=', $id);
             })
            ->whereNull('products.deleted_at');
        $products = $products->join('units', 'products.unit_id', '=', 'units.id');
        $products = $products->select(
            'products.id',
            'products.name',
            'price_type',
            'products.divisor_number',
            'purchase_price',
            'sale_price',
            'barcode',
            'type',
            'units.label as unit_label',
            'units.relation as unit_relation',
        );
        $products = $products->addSelect([
            'quantity' => Stock::selectRaw("IF (ISNULL(SUM(quantity)), 0, SUM(quantity))")
                ->where('showroom_id', $id)
                ->whereColumn('product_id', 'products.id')
        ])
        ->orderBy('name')
        ->distinct()
        ->get();
        return response()->json($products, 200);
    }
}
