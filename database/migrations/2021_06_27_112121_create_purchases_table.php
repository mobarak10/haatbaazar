<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('payment_type');
            $table->foreignId('party_id')
                ->comment('Supplier id')
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
            $table->string("voucher_no",45);
            $table->date("date");
            $table->decimal("subtotal",12,2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->string('discount_type')->default('percentage')->comment('percentage/flat');
            $table->decimal("paid",12,2)->default(0.00);
            $table->decimal("previous_balance",12,2)
                ->default(0.00)
                ->comment('party balance before completing purchase');
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
        Schema::dropIfExists('purchases');
    }
}
