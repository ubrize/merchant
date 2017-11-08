<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderDetailsToOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merchant_orders', function (Blueprint $table) {
            $table->string('owner_type')->default('');
            $table->integer('owner_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('merchant_orders', function (Blueprint $table) {
            $table->dropColumn('owner_type');
            $table->dropColumn('owner_id');
        });
    }
}
