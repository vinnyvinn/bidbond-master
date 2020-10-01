<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCounterPartiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('counter_parties', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('physical_address');
            $table->string('postal_address');
            $table->integer('postal_code_id')->unsigned()->index();
            $table->string('category_secret');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('category_secret')->references('secret')->on('categories');
            $table->foreign('postal_code_id')->references('id')->on('postal_codes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('counter_parties');
    }
}
