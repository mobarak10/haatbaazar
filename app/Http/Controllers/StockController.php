<?php

namespace App\Http\Controllers;

use App\Helpers\QuantityHelper;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Showroom;
use App\Models\UserShowroom;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class StockController extends Controller
{
    use QuantityHelper;
    protected string $permission_for = 'current_stock';
    private $data = [];
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $this->hasPermission('view');
        $categories = Category::all();
        $brands = Brand::all();
        $showrooms = Showroom::all();

        $products = Product::query();
        $products = $products->whereHas('showrooms', function (Builder $query) {
            if (request()->showroom_id and !empty(request()->showroom_id)) {
                $query->where('showrooms.id', request()->showroom_id);
            }else{
                $user_showroom_id = UserShowroom::where('user_id', Auth::id())->pluck('showroom_id');
                $query->whereIn('showrooms.id', $user_showroom_id);
            }
        });
        $products = $products->select(
            'products.id',
            'name',
            'category_id',
            'brand_id',
            'unit_id',
            'purchase_price',
            'sale_price',
            'products.divisor_number',
        );
        if (request()->category_id){
            $products->where('category_id', request()->category_id);
        }
        if (request()->brand_id){
            $products->where('brand_id', request()->brand_id);
        }
        if (request()->showroom_id and !empty(request()->showroom_id)) {
            $products->addTotalQuantity(request()->showroom_id);
            $products->addAveragePurchasePrice(request()->showroom_id);
        }else{
            $products->addTotalQuantity();
            $products->addAveragePurchasePrice();
        }
        if (request()->name){
            $products->where('name', 'like', '%' . request()->name . '%');
        }
        if (request()->type){
            $products->where('type', request()->type);
        }
        $products = $products
            ->orderBy('name')
            ->addUnitLabel()
            ->distinct()
            ->addUnitRelation()
            ->addBrandName()
            ->addCategoryName()
            ->paginate(30)
            ->withQueryString();

        $total_purchase_price = 0;
        foreach ($products as $product){
            $total_purchase_price += ($product->total_quantity / $product->divisor_number) * $product->average_purchase_price;
        }

        return Inertia::render('Stock/Index', [
            'categories' => $categories,
            'products' => $products,
            'brands' => $brands,
            'showrooms' => $showrooms,
            'data' => $this->data,
            'total_purchase_price' => $total_purchase_price,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->hasPermission('create');
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->hasPermission('create');
        //
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->hasPermission('update');
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->hasPermission('update');
        //
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
        //
    }
}
