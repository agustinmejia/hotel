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
        Schema::create('reservation_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->nullable()->constrained('reservations');
            $table->foreignId('room_id')->nullable()->constrained('rooms');
            $table->decimal('price', 10, 2)->nullable();
            $table->text('observations')->nullable();
            $table->string('status')->nullable()->default('ocupada');
            $table->timestamps();
            $table->timestamp('unoccupied_at')->nullable();
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
        Schema::dropIfExists('reservation_details');
    }
};
