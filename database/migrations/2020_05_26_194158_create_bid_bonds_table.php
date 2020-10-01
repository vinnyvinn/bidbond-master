<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBidBondsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bidbonds', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tender_no');
            $table->text('purpose');
            $table->string('addressee')->nullable();
            $table->date('effective_date');
            $table->integer('amount');
            $table->string('period');
            $table->string('company_id');
            $table->unsignedInteger('counter_party_id');
            $table->decimal('charge', 16, 2);
            $table->string('template_secret');
            $table->string('secret')->unique();
            $table->string('reference')->nullable()->unique();
            $table->boolean('paid')->default(false);
            $table->string('agent_id')->nullable();
            $table->date('expiry_date');
            $table->dateTime('expired_at')->nullable();
            $table->date('deal_date')->nullable();
            $table->string('created_by');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('template_secret')->references('secret')->on('bid_bond_templates');
            $table->foreign('counter_party_id')->references('id')->on('counter_parties');
          //  $table->foreign('company_id')->references('id')->on('companies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bidbonds');
    }
}
