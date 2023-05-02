<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('payment_type');
            $table->foreignId('party_id')
                ->comment('Customer id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->comment('logged user id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('showroom_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('cash_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('bank_account_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string("invoice_no",45);
            $table->date("date");
            $table->decimal("subtotal",12,2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->string('discount_type')->default('percentage')->comment('percentage/flat');
            $table->decimal('labour_cost', 10, 2)->default(0.00);
            $table->decimal('transport_cost', 10, 2)->default(0.00);
            $table->decimal("paid",12,2)->default(0.00);
            $table->decimal("due",12,2)->default(0.00);
            $table->decimal("change",10,2)->default(0.00);
            $table->decimal("previous_balance",12,2)
                ->comment('customer previous balance before completing sale')
                ->default(0.00);
            $table->text("note")->nullable();
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
        Schema::dropIfExists('sales');
    }
}
