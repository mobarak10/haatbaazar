<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('unit_id')->constrained("units");
            $table->foreignId('category_id')->constrained("categories");
            $table->foreignId('brand_id')->constrained("brands");
            $table->string('barcode')->nullable();
            $table->decimal('sale_price', 10, 2)->default(0.00);
            $table->decimal('purchase_price', 10, 2)->default(0.00);
            $table->string('price_type')->comment('product sale or purchase price type');
            $table->integer('divisor_number');
            $table->integer('stock_alert')->default(0)->nullable();
            $table->string('quantity_in_unit')->nullable();
            $table->enum('type', ['raw_material', 'finish_product', 'bag']);
            $table->boolean('status')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
