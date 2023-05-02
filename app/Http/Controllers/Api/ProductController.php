<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Traits\HttpResponses;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use HttpResponses;
    protected string $permission_for = 'product';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $this->hasPermission('view');
        $products = Product::query();
        if (request()->has('category_id') and !is_numeric(\request('category_id'))){
            return $this->emptyData([]);
        }
        $products = $products->when(\request()->has('category_id'), function (Builder $query) {
            $query->where('category_id', \request('category_id'));
        });

        if (request()->has('brand_id') and !is_numeric(\request('brand_id'))){
            return $this->emptyData([]);
        }
        $products = $products->when(\request()->has('brand_id'), function (Builder $query) {
            $query->where('brand_id', \request('brand_id'));
        });

        $products = $products->when(\request()->has('name'), function (Builder $query) {
            $query->where('name', 'like', '%' . request()->name . '%');
        });

        $products = $products->with('category', 'brand', 'unit')
            ->whereHas('category')
            ->whereHas('brand')
            ->whereHas('unit')
            ->paginate(30)
            ->withQueryString();

        return ProductResource::collection($products);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return ProductResource
     */
    public function show(Product $product)
    {
        $this->hasPermission('show');
        return new ProductResource($product);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
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
        //
    }
}
