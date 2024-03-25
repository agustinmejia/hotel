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
        Schema::create('person_defaulters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cashier_id')->nullable()->constrained('cashiers');
            $table->foreignId('person_id')->nullable()->constrained('people');
            $table->foreignId('reservation_detail_id')->nullable()->constrained('reservation_details');
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('type')->nullable();
            $table->text('observations')->nullable();
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
        Schema::dropIfExists('person_defaulters');
    }
};
