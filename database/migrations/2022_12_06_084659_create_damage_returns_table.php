<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('damage_returns', function (Blueprint $table) {
            $table->id();
            $table->string('damage_return_no')->unique();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('party_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('showroom_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('cash_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('bank_account_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->date('date');
            $table->decimal('subtotal', 12, 2)->default(0.00);
            $table->decimal('discount', 12, 2)->default(0.00);
            $table->decimal('paid', 12, 2)->default(0.00);
            $table->decimal('previous_balance', 12, 2)
                ->default(0.00)
                ->comment('party previous balance before this damage return');
            $table->enum('paid_to', ['cash', 'bank'])->comment('paid to cash or bank');
            $table->string('note')->nullable();
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
        Schema::dropIfExists('damage_returns');
    }
};
