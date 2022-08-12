<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerDataToSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('customer_name')->after('user_id');
            $table->string('customer_tel')->after('customer_name');
            $table->integer('customer_city')->after('customer_tel');
            $table->text('customer_address')->after('customer_city');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('customer_name');
            $table->dropColumn('customer_tel');
            $table->dropColumn('customer_city');
            $table->dropColumn('customer_address');
        });
    }
}
