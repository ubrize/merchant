<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('objectClass', 255);
            $table->integer('objectId');
            $table->tinyInteger('status');
            $table->string('gateway', 255);
            $table->text('options');
            $table->integer('amount');
            $table->string('tokenId', 70);
            $table->string('tokenReference', 150);
            $table->text('description');
            $table->text('error');
            $table->text('response');
            $table->string('languageCode');
            $table->string('currencyCode');
            $table->timestamps();

            //add indexes
            $table->unique('tokenId'); // unique per app
            $table->unique(['tokenReference', 'gateway']); //unique per gateway
            $table->index('objectId');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
