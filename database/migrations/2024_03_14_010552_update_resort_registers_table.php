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
        Schema::table('resort_registers', function(Blueprint $table){
            $table->foreignId('branch_office_id')->nullable()->constrained('branch_offices');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('resort_registers', function (Blueprint $table) {
            $table->dropColumn(['branch_office_id']);
        });
    }
};
