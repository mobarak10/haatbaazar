<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseCostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->decimal('labour_cost', 10, 2)->default(0.00);
            $table->boolean('labour_cost_adjust_to_supplier')->default(true);
            $table->decimal('transport_cost', 10, 2)->default(0.00);
            $table->boolean('transport_cost_adjust_to_supplier')->default(true);
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
        Schema::dropIfExists('purchase_costs');
    }
}
