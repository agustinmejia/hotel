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
        Schema::create('reservation_detail_accessories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_detail_id')->nullable()->constrained('reservation_details');
            $table->foreignId('room_accessory_id')->nullable()->constrained('room_accessories');
            $table->decimal('price', 10, 2)->nullable();
            $table->date('start')->nullable();
            $table->date('finish')->nullable();
            $table->text('observations')->nullable();
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
        Schema::dropIfExists('reservation_detail_accessories');
    }
};
