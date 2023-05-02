<?php

namespace App\Http\Middleware;

use App\Helpers\QuantityHelper;
use App\Models\BankAccount;
use App\Models\Cash;
use App\Models\Party;
use App\Models\Product;
use Closure;
use Illuminate\Http\Request;

class DailyBalance
{
    use QuantityHelper;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $previous_date = date('Y-m-d',strtotime("-1 days"));
        $daily_balance = \App\Models\DailyBalance::where('date', $previous_date)->first();

        if (!$daily_balance) {
            $cash_balance = Cash::all()->sum('balance');
            $bank_balance = BankAccount::all()->sum('balance');
            $products = Product::with('stock')
                ->get()
                ->map(function($product) {
                    $product['stock_price'] = $this->priceQuantity($product, $product->stock->sum('quantity')) * $product->purchase_price;
                    $product['damage_price'] = $this->priceQuantity($product, $product->stock->sum('damage_quantity')) * $product->purchase_price;
                    return $product;
                });
            $total_stock = $products->sum('stock_price');
            $total_damage = $products->sum('damage_price');

            $customer_balance = Party::customer()->where('balance', '>', 0)->sum('balance');
            $supplier_balance = Party::supplier()->where('balance', '>', 0)->sum('balance');

            $total_balance = (
                $cash_balance +
                $bank_balance +
                $total_stock +
                $total_damage +
                $customer_balance +
                $supplier_balance
            );
            $data = [
                'date' => $previous_date,
                'amount' => $total_balance,
            ];

            \App\Models\DailyBalance::create($data);
        }
        return $next($request);
    }
}
