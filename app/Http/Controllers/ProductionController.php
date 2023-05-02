<?php

namespace App\Http\Controllers;

use App\Helpers\Converter;
use App\Http\Requests\ProductionRequest;
use App\Models\Product;
use App\Models\Production;
use App\Models\ProductionProduct;
use App\Models\ProductionRawMaterial;
use App\Models\RawMaterial;
use App\Models\Showroom;
use App\Models\Stock;
use App\Models\Unit;
use App\Scopes\ShowroomScope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Helpers\QuantityHelper;

class ProductionController extends Controller
{
    use QuantityHelper;
    private $production;
    protected string $permission_for = 'production';
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $this->hasPermission('view');
//        return Purchase::with('details')->get();
        $productions = Production::query();
        if (request()->search) {
            if (request()->from_date) {
                $productions = $productions->whereBetween('date', [request()->from_date, request()->to_date]);
            }
            if (request()->production_no) {
                $productions = $productions->where('production_no', request()->production_no);
            }
        }
        $productions = $productions->hasUserShowroom()
            ->fromShowroomName()
            ->toShowroomName()
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->paginate(30)
            ->withQueryString();
        return Inertia::render('Production/Index', [
            'productions' => $productions,
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

        $products = $this->getProducts();
        return Inertia::render('Production/Create', [
            'showrooms' => DB::table('showrooms')->select('id', 'name')->get(),
            'finish_products' => $products,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ProductionRequest $request)
    {
        $this->hasPermission('create');
//        return $request->all();
        DB::transaction(function () use($request) {
            // validated request data
            $production_data = $request->validated();
            $production_data['production_no'] = 'PROD'. '-' . str_pad(Production::max('id') + 1, 6, '0', STR_PAD_LEFT);
            $production_data['user_id'] = \Auth::id();

            // create new production
            $this->production = Production::create($production_data);
            $production = $this->production;

            $this->saveProductionDetails($request, $production);
        });

        return Redirect::route('production.show', $this->production->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Inertia\Response
     */
    public function show($id)
    {
        $this->hasPermission('show');
        return Inertia::render('Production/Show', [
            'production' => Production::with('productionDetails.product.unit')
                ->hasUserShowroom()
                ->fromShowroomName()
                ->toShowroomName()
                ->find($id),
        ]);
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
        $products = $this->getProducts();

        return Inertia::render('Production/Create', [
            'oldProduction' => Production::with('productionDetails.product.unit')
                ->hasUserShowroom()
                ->findOrFail($id),
            'showrooms' => DB::table('showrooms')
                ->select('id', 'name')
                ->get(),
            'finish_products' => $products,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProductionRequest $request, $id)
    {
        $this->hasPermission('update');
        DB::transaction(function () use($request, $id) {
            // get old production data
            $oldProduction = Production::findOrFail($id);
            $this->updateOldProductionData($oldProduction);
            // validated request data
            $production_data = $request->validated();
            // create new production
            $oldProduction->update($production_data);
            $this->production = $oldProduction;

            $this->saveProductionDetails($request, $oldProduction);
        });

        return Redirect::route('production.show', $this->production->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $this->hasPermission('destroy');
        DB::transaction(function () use($id) {
            // get old production data
            $production = Production::findOrFail($id);
            $this->updateOldProductionData($production);

            $production->delete();
        });

        return redirect()->back()->with('success', 'Production delete successfully!');
    }


    public function saveProductionDetails($request, $production)
    {
        foreach ($request->cartProducts as $product) {
            $_product = Product::with([
                    'showrooms' => function($query) {
                        return $query->withoutGlobalScope(ShowroomScope::class);
                    }
                ])
                ->findOrFail($product['id']);

            $production_details_data = [
                'product_id' => $product['id'],
                'showroom_id' => $request->type === 'raw_product'
                    ? $request->from_showroom_id
                    : $request->to_showroom_id,
                'date' => $production->date,
                'quantity' => $product['quantity'],
                'quantity_in_unit' => $product['quantity_in_unit'],
                'type' => $product['type'],
            ];

            $production->productionDetails()->create($production_details_data);

            $this->updateProductQuantity($_product, $request, $product);
        }
    }

    /**
     * update finish product & raw product quantity when production
     * @param $_product
     * @param $request
     * @param $product
     * @return void
     */
    public function updateProductQuantity($_product, $request, $product)
    {
        // get to showroom
        $to_showroom = $_product->showrooms->where('id', $request->to_showroom_id)->first();
        // get from showroom
        $from_showroom = $_product->showrooms->where('id', $request->from_showroom_id)->first();

        /**
         * if product type is finish product then increment quantity in showroom
         * else decrement quantity from showroom
         */
        if ($product['type'] == 'finish_product') {
            //if exists showroom
            if ($to_showroom){
                //update stocks
                $quantity = $to_showroom->stock->quantity + $product['quantity'];
                $_product->showrooms()->updateExistingPivot($to_showroom->id, [
                    'quantity' => $quantity,
                ]);
            }else{
                $_product->showrooms()->attach([
                    $request->to_showroom_id =>  [
                        'quantity' => $product['quantity'],
                        'average_purchase_price' => $_product->purchase_price,
                        'divisor_number' => $_product->divisor_number,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                ]);
            }
        }else{
            //if exists showroom
            if ($from_showroom){
                //update stocks
                $quantity = $from_showroom->stock->quantity - $product['quantity'];
                $_product->showrooms()->updateExistingPivot($from_showroom->id, [
                    'quantity' => $quantity,
                ]);
            }else{
                $_product->showrooms()->attach([
                    $request->from_showroom_id =>  [
                        'quantity' => -$product['quantity'],
                        'average_purchase_price' => $_product->purchase_price,
                        'divisor_number' => $_product->divisor_number,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                ]);
            }
        }
    }

    /**
     * update quantity of old production product
     * @param $oldProduction
     * @return void
     */
    public function updateOldProductionData($oldProduction)
    {
        /**
         * if product type is finish product then decrement quantity in showroom
         * else increment quantity from showroom
         */
        foreach ($oldProduction->productionDetails as $detail){
            $product = Product::with([
                'showrooms' => function($query) {
                    return $query->withoutGlobalScope(ShowroomScope::class);
                }
            ])
                ->findOrFail($detail->product_id);
            // get showroom
            $to_showroom = $product->showrooms->where('id', $oldProduction->to_showroom_id)->first();
            $from_showroom = $product->showrooms->where('id', $oldProduction->from_showroom_id)->first();
            if ($detail->type == 'finish_product') {
                //if exists showroom
                if ($to_showroom){
                    //update stocks
                    $quantity = $to_showroom->stock->quantity - $detail->quantity;
                    $product->showrooms()->updateExistingPivot($to_showroom->id, [
                        'quantity' => $quantity,
                    ]);
                }else{
                    $product->showrooms()->attach([
                        $oldProduction->to_showroom_id =>  [
                            'quantity' => (-1 * $detail->quantity),
                            'average_purchase_price' => $product->purchase_price,
                            'divisor_number' => $product->divisor_number,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]
                    ]);
                }
            }else{
                //if exists showroom
                if ($from_showroom){
                    //update stocks
                    $quantity = $from_showroom->stock->quantity + $detail->quantity;
                    $product->showrooms()->updateExistingPivot($from_showroom->id, [
                        'quantity' => $quantity,
                    ]);
                }else{
                    $product->showrooms()->attach([
                        $oldProduction->from_showroom_id =>  [
                            'quantity' => $detail->quantity,
                            'average_purchase_price' => $product->purchase_price,
                            'divisor_number' => $product->divisor_number,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]
                    ]);
                }
            }

            $detail->delete();
        }
    }

    /**
     * get products
     * @return \Illuminate\Support\Collection
     */
    public function getProducts(): \Illuminate\Support\Collection
    {
        $products = DB::table('products')
            ->join('stocks', function ($join) {
                $join->on('products.id', '=', 'stocks.product_id');
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
        return $products->addSelect([
                'quantity' => Stock::selectRaw("IF (ISNULL(SUM(quantity)), 0, SUM(quantity))")
                    ->whereColumn('product_id', 'products.id')
            ])
            ->distinct()
            ->orderBy('name')
            ->get();
    }
}
