<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductTransferRequest;
use App\Models\Product;
use App\Models\ProductTransfer;
use App\Models\Showroom;
use App\Scopes\ShowroomScope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class ProductTransferController extends Controller
{
    private $product_transfer;
    protected string $permission_for = 'product_transfer';
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $this->hasPermission('view');

        $product_transfers = ProductTransfer::query();
        if (request()->search) {
            if (request()->from_date) {
                $product_transfers = $product_transfers->whereBetween('date', [request()->from_date, request()->to_date]);
            }
            if (request()->transfer_no) {
                $product_transfers = $product_transfers->where('transfer_no', request()->transfer_no);
            }
        }
        $product_transfers = $product_transfers->hasUserShowroom()
            ->userName()
            ->fromShowroomName()
            ->toShowroomName()
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->paginate(30)
            ->withQueryString();
        return Inertia::render('ProductTransfer/Index', [
            'product_transfers' => $product_transfers,
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
        return Inertia::render('ProductTransfer/Transfer', [
            'showrooms' => DB::table('showrooms')->select('id', 'name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ProductTransferRequest $request)
    {
        $this->hasPermission('create');
//        return $request->all();
        DB::transaction(function () use($request) {
            // validated request data
            $product_transfer_data = $request->validated();
            $product_transfer_data['transfer_no'] = 'PRDTRNS'. '-' . str_pad(ProductTransfer::max('id') + 1, 6, '0', STR_PAD_LEFT);
            $product_transfer_data['user_id'] = \Auth::id();

            // create new production
            $this->product_transfer = ProductTransfer::create($product_transfer_data);
            $product_transfer = $this->product_transfer;

            $this->saveProductTransferDetails($request, $product_transfer);
        });

        return Redirect::route('product-transfer.show', $this->product_transfer->id);
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
        return Inertia::render('ProductTransfer/Show', [
            'product_transfer' => ProductTransfer::with('productTransferDetails.product.unit')
                ->hasUserShowroom()
                ->userName()
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
        return Inertia::render('ProductTransfer/Transfer', [
            'showrooms' => DB::table('showrooms')->select('id', 'name')->get(),
            'oldProductTransfer' => ProductTransfer::with('productTransferDetails.product.unit')
                ->hasUserShowroom()
                ->findOrFail($id),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProductTransferRequest $request, $id)
    {
        $this->hasPermission('update');
        DB::transaction(function () use($request, $id) {
            $old_product_transfer = ProductTransfer::findOrFail($id);
            $this->updateOldProductTransferData($old_product_transfer);
            // validated request data
            $product_transfer_data = $request->validated();
            // create new production
            $old_product_transfer->update($product_transfer_data);
            $this->product_transfer = $old_product_transfer;

            $this->saveProductTransferDetails($request, $old_product_transfer);
        });

        return Redirect::route('product-transfer.show', $this->product_transfer->id);
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
            $old_product_transfer = ProductTransfer::findOrFail($id);
            $this->updateOldProductTransferData($old_product_transfer);

            $old_product_transfer->delete();
        });

        return redirect()->back();
    }

    /**
     * get all product
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllProduct(Request $request): JsonResponse
    {
        $showroom = Showroom::with('products')->findOrFail($request->showroom_id);
        $products = $showroom->products;
        return response()->json($products, 200);
    }

    /**
     * save production transfer product
     * @param $request
     * @param $product_transfer
     * @return void
     */
    public function saveProductTransferDetails($request, $product_transfer)
    {
        foreach ($request->cartProducts as $product) {
            $_product = Product::with([
                    'showrooms' => function($query) {
                        return $query->withoutGlobalScope(ShowroomScope::class);
                    }
                ])
                ->findOrFail($product['id']);

            $product_transfer_details_data = [
                'product_id' => $product['id'],
                'date' => $product_transfer->date,
                'quantity' => $product['quantity'],
                'quantity_in_unit' => $product['quantity_in_unit'],
            ];

            $product_transfer->productTransferDetails()->create($product_transfer_details_data);

            $this->updateProductQuantity($_product, $request, $product);
        }
    }

    /**
     * update quantity for product transfer
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

        //if exists showroom
        if ($from_showroom){
            //update stocks
            $quantity = $from_showroom->stock->quantity - $product['quantity'];
            $_product->showrooms()->updateExistingPivot($from_showroom->id, [
                'quantity' => $quantity,
            ]);
        }else{
            $_product->showrooms()->attach([
                $request->to_showroom_id =>  [
                    'quantity' => (-1 * $product['quantity']),
                    'average_purchase_price' => $_product->purchase_price,
                    'divisor_number' => $_product->divisor_number,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ]);
        }

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
    }

    /**
     * update quantity of old product transfer product
     * @param $oldProduction
     * @return void
     */
    public function updateOldProductTransferData($old_product_transfer)
    {
        /**
         * decrement product quantity from to showroom
         * increment product quantity from showroom
         */
        foreach ($old_product_transfer->productTransferDetails as $detail){
            $product = Product::with([
                    'showrooms' => function($query) {
                        return $query->withoutGlobalScope(ShowroomScope::class);
                    }
                ])
                ->findOrFail($detail->product_id);
            // get showroom
            $to_showroom = $product->showrooms->where('id', $old_product_transfer->to_showroom_id)->first();
            $from_showroom = $product->showrooms->where('id', $old_product_transfer->from_showroom_id)->first();
            //if exists to showroom
            if ($to_showroom){
                //update stocks
                $quantity = $to_showroom->stock->quantity - $detail->quantity;
                $product->showrooms()->updateExistingPivot($to_showroom->id, [
                    'quantity' => $quantity,
                ]);
            }else{
                $product->showrooms()->attach([
                    $old_product_transfer->to_showroom_id =>  [
                        'quantity' => (-1 * $detail->quantity),
                        'average_purchase_price' => $product->purchase_price,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                ]);
            }

            //if exists from showroom
            if ($from_showroom){
                //update stocks
                $quantity = $from_showroom->stock->quantity + $detail->quantity;
                $product->showrooms()->updateExistingPivot($from_showroom->id, [
                    'quantity' => $quantity,
                ]);
            }else{
                $product->showrooms()->attach([
                    $old_product_transfer->from_showroom_id =>  [
                        'quantity' => $detail->quantity,
                        'average_purchase_price' => $product->purchase_price,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                ]);
            }

            $detail->delete();
        }
    }
}
