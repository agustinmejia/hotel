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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('person_id')->nullable()->constrained('people');
            $table->date('start')->nullable();
            $table->date('finish')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->decimal('discount', 10, 2)->nullable();
            $table->string('reason')->nullable();
            $table->smallInteger('quantity_people')->nullable();
            $table->text('observation')->nullable();
            $table->string('status')->nullable()->default('pendiente');
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
        Schema::dropIfExists('reservations');
    }
};
