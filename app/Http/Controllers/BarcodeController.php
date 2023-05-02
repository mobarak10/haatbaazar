<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BarcodeController extends Controller
{
    protected string $permission_for = 'barcode';

    public function batchBarcode()
    {
        $this->hasPermission('view');
        $records = [];
        if(request()->barcode) {
            $records['quantity'] = \request()->quantity;
            $records['barcode'] = \request()->barcode;
            $records['product'] = \DB::table('products')->where('barcode', request()->barcode)->first();
        }
        return Inertia::render('Barcode/Batch', compact('records'));
    }

    public function singleBarcode()
    {
        $this->hasPermission('view');
        $product = '';
        if(request()->barcode) {
            $product = \DB::table('products')->where('barcode', request()->barcode)->first();
        }
        return Inertia::render('Barcode/Single', compact('product'));
    }
}
