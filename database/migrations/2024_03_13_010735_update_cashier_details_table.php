<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cashier_details', function(Blueprint $table){
            $table->foreignId('resort_register_id')->nullable()->constrained('resort_registers');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cashier_details', function (Blueprint $table) {
            $table->dropColumn(['resort_register_id']);
        });
    }
};
