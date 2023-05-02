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
        Schema::create('sms', function (Blueprint $table) {
            $table->id();
            $table->string('number')->comment('sms send number');
            $table->text('message');
            $table->integer('character')->comment('total number of sms send character');
            $table->integer('total_sms')->comment('how much sms is send in one time');
            $table->enum('status', ['error', 'success'])->comment('error means sms not send, success means sms is send');
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
        Schema::dropIfExists('sms');
    }
};
