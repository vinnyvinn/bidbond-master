<?php

use App\Models\Company;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->string('id')->unique();
            $table->string('name');
            $table->string('postal_address');
            $table->string('crp');
            $table->unsignedInteger('postal_code_id');
            $table->enum('type', Company::TYPES)->default('user');
            $table->decimal('limit', 18, 2)->default(0.00);
            $table->decimal('balance', 18, 2)->default(0.00);
            $table->timestamps();
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
        Schema::dropIfExists('companies');
    }
}
