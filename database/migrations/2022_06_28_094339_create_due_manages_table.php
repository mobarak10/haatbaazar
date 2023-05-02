<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDueManagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('due_manages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('party_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnUpdate();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnUpdate();
            $table->foreignId('cash_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnUpdate();
            $table->foreignId('bank_account_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnUpdate();
            $table->foreignId('showroom_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('check_number')->nullable();
            $table->enum('type', ['supplier', 'customer']);
            $table->date('date');
            $table->decimal('amount', 12, 2)->default(0.00)->comment('positive amount in received & negetive amount is paid');
            $table->decimal('adjustment', 12, 2)->default(0.00);
            $table->text('note')->nullable();
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
        Schema::dropIfExists('due_manages');
    }
}
