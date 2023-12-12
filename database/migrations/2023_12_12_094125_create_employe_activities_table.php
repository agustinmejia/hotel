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
        Schema::create('employe_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employe_id')->nullable()->constrained('employes');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('room_id')->nullable()->constrained('rooms');
            $table->text('description')->nullable();
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
        Schema::dropIfExists('employe_activities');
    }
};
