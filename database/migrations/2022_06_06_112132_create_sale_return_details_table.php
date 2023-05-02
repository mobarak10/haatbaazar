<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleReturnDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_return_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_return_id');
            $table->foreignId('product_id');
            $table->foreignId('showroom_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->date('date')->nullable();
            $table->decimal('purchase_price', 10, 4)->default(0)->comment('per unit');
            $table->decimal('return_price', 10, 4)->default(0)->comment('per unit');
            $table->integer('quantity');
            $table->string('quantity_in_unit')->comment('user input quantity');
            $table->string('quantity_for_price')->comment('quantity which is calculated with price');
            $table->decimal('line_total')->default(0.00);
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
        Schema::dropIfExists('sale_return_details');
    }
}
