<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mokam_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->foreignId('showroom_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string("genus",45);
            $table->string("name");
            $table->string("company_name")->nullable();
            $table->text("description")->nullable();
            $table->string("phone",20)->nullable();
            $table->string("email")->nullable();
            $table->longText("address")->nullable();
            $table->text("photo")->nullable();
            $table->decimal("balance",10,2)->default(0)->comment('positive(+) balance means receivable and negative(-) is payable');
            $table->boolean("active")->default(true);
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
        Schema::dropIfExists('parties');
    }
}
