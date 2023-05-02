<?php

namespace App\Http\Controllers;

use App\Http\Requests\DamageRequest;
use App\Models\Damage;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DamageController extends Controller
{
    protected string $permission_for = 'damage_stock';
    private $damage;
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $this->hasPermission('view');
        $damage_stocks = Damage::orderByDesc('date')
            ->orderByDesc('created_at')->paginate(30);
        return Inertia::render('DamageStock/Index', compact('damage_stocks'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        $this->hasPermission('create');
        return Inertia::render('DamageStock/Damage');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(DamageRequest $request)
    {
        $this->hasPermission('create');
        DB::transaction(function () use($request) {
            $data = $request->validated();
            $data['user_id'] = \Auth::id();
            $data['damage_no'] = 'Damage-' . str_pad(Damage::max('id') + 1, 8,0, STR_PAD_LEFT);
            $damage = Damage::create($data);
            $this->damage = $damage;

            $this->saveDamageDetails($request->products, $damage);
        });

        if ($this->damage) {
            return redirect()->route('damage-stock.show', $this->damage->id);
        }
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

        $damage = Damage::with('details.product.unit', 'showroom')->findOrFail($id);
        return Inertia::render('DamageStock/Show', compact('damage'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Inertia\Response
     */
    public function edit($id)
    {
        $this->hasPermission('create');
        $damage = Damage::with('details.product.unit')->findOrFail($id);
        return Inertia::render('DamageStock/Damage', compact('damage'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(DamageRequest $request, $id)
    {
        DB::transaction(function () use($request, $id) {
            $oldDamage = Damage::findOrFail($id);
            $data = $request->validated();
            $this->updateOldDamageDetails($oldDamage);

            $oldDamage->update($data);
            $this->damage = $oldDamage;
            $this->saveDamageDetails($request->products, $oldDamage);
        });

        if ($this->damage) {
            return redirect()->route('damage-stock.show', $this->damage->id);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::transaction(function () use($id) {
            $oldDamage = Damage::findOrFail($id);
            $this->updateOldDamageDetails($oldDamage);
            $oldDamage->delete();
        });
        return redirect()->back();
    }

    /**
     * save damage details product
     * @param $products
     * @param $damage
     * @return void
     */
    public function saveDamageDetails($products, $damage)
    {
        foreach ($products as $product) {
            $_product = Product::findOrFail($product['id']);
            $damage_data = [
                'product_id' => $product['id'],
                'showroom_id' => $damage->showroom_id,
                'date' => $damage->date,
                'purchase_price' => $product['purchase_price'],
                'quantity' => $product['quantity'],
                'quantity_in_unit' => $product['quantity_in_unit'],
                'quantity_for_price' => $product['quantity_for_price'],
            ];
            // create sale details
            $damage->details()
                ->create($damage_data);

            $showroom = $_product->showrooms()
                ->find(\request()->showroom_id);

            // increment damage quantity from showroom
            $showroom->stock->increment('damage_quantity', $product['quantity']);
            // decrement quantity from showroom
            $showroom->stock->decrement('quantity', $product['quantity']);
        }
    }

    /**
     *
     * @param $oldDamage
     * @return void
     */
    public function updateOldDamageDetails($oldDamage)
    {
        if (count($oldDamage->details) > 0) {
            foreach ($oldDamage->details as $detail) {
                // get product
                $product = Product::findOrFail($detail->product_id);
                // get showroom
                $_showroom = $product->showrooms()->where('showrooms.id', $oldDamage->showroom_id)->first();
                if($_showroom) {
                    // increment damage quantity from showroom
                    $_showroom->stock->decrement('damage_quantity', $detail->quantity);
                    // decrement quantity from showroom
                    $_showroom->stock->increment('quantity', $detail->quantity);
                }else{ // no previous showroom exists
                    //add new stock in for products
                    $product->showrooms()->attach([
                        $oldDamage->showroom_id =>  [
                            'quantity' => $detail->quantity,
                            'damage_quantity' => (-1 * $detail->quantity),
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
}
