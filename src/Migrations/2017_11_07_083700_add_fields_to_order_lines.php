<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToOrderLines extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('merchant_order_lines', function (Blueprint $table) {
            $table->text('object_options')->nullable();
            $table->text('summary')->nullable();
            $table->integer('total');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('merchant_order_lines', function (Blueprint $table) {
            $table->dropColumn('object_options');
            $table->dropColumn('summary');
            $table->dropColumn('total');
        });
    }
}
