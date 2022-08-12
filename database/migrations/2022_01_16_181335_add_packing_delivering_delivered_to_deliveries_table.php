<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPackingDeliveringDeliveredToDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn('packing');
            $table->dropColumn('pickup');
            $table->dropColumn('delivering');
            $table->dropColumn('delivered');
            $table->dropColumn('sold_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->string('packing')->after('address')->nullable();
            $table->string('pickup')->after('packing')->nullable();
            $table->string('delivering')->after('pickup')->nullable();
            $table->string('delivered')->after('delivering')->nullable();
            $table->integer('sold_by')->after('user_id')->nullable();
        });
    }
}
