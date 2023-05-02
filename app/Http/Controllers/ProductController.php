<?php

namespace App\Http\Controllers;

use App\Helpers\QuantityHelper;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use App\Models\UserShowroom;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class ProductController extends Controller
{
    protected string $permission_for = 'brand';
    private $data = [];
    use QuantityHelper;
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
        $products = DB::table('products')->whereNull('products.deleted_at');
        if (request()->search) {
            if (request()->category_id){
                $products->where('category_id', request()->category_id);
            }
            if (request()->brand_id){
                $products->where('brand_id', request()->brand_id);
            }
            if (request()->name){
                $products->where('products.name', 'like', '%' . request()->name . '%');
            }
            if (request()->type){
                $products->where('type', request()->type);
            }
        }
        $products = $products->join('categories', 'products.category_id', '=', 'categories.id');
        $products = $products->join('brands', 'products.brand_id', '=', 'brands.id');
        $products = $products->join('units', 'products.unit_id', '=', 'units.id');
        $products = $products->select(
            'products.id',
            'products.name',
            'price_type',
            'purchase_price',
            'sale_price',
            'barcode',
            'categories.name as category_name',
            'brands.name as brand_name',
            'units.name as unit_name'
        )
            ->orderBy('name')
            ->paginate(30)
            ->withQueryString();

        return Inertia::render('Product/Index', [
            'categories' => $categories,
            'products' => $products,
            'brands' => $brands,
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
        $showroom_id = UserShowroom::where('user_id', Auth::id())->pluck('showroom_id');
        if (count($showroom_id) > 0) {
            $showrooms = DB::table('showrooms')->whereIn('id', $showroom_id)->get();
        }else{
            $showrooms = [];
        }

        return Inertia::render("Product/Create",[
            "categories" => Category::all(),
            'showrooms' => $showrooms,
            'brands' => Brand::all(),
            "units" => Unit::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ProductStoreRequest $request)
    {
        $this->hasPermission('create');
        $data = $request->validated();
        $unit = Unit::findOrFail($request->unit_id);
        $data['price_type'] = explode('/', $unit->label)[$request->price_type];
        $data['divisor_number'] = $this->getDivisorNumber($unit, $request->price_type);
        Product::create($data);

        return Redirect::route('product.index')->with('success', 'Product created successfully.');
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
     * @return \Inertia\Response
     */
    public function edit($id)
    {
        $this->hasPermission('update');
        return Inertia::render("Product/Edit",[
            "categories" => Category::all(),
            'product' => Product::findOrFail($id),
            'brands' => Brand::all(),
            "units" => Unit::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductUpdateRequest $request, $id)
    {
        $this->hasPermission('update');
        $data = $request->validated();

        $product = Product::findOrFail($id);
        $data['previous_purchase_price'] = $product->purchase_price;
        $product->update($data);

        return Redirect::route('product.index')->with('success', 'Product updated successfully.');
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
        Product::find($id)->delete();
        return redirect()
            ->back()
            ->with('success', 'Product delete successfully');
    }

}
