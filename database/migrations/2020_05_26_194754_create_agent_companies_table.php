<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_companies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('agent_id')->index();
            $table->string('company_id')->index();
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->unique(['agent_id','company_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agent_companies');
    }
}
