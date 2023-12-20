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
        Schema::create('reservation_detail_food_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('food_type_id')->nullable()->constrained('food_types');
            $table->foreignId('reservation_detail_id')->nullable()->constrained('reservation_details');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reservation_detail_food_types');
    }
};
