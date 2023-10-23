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
        Schema::create('reservation_detail_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_detail_id')->nullable()->constrained('reservation_details');
            $table->date('date')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
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
        Schema::dropIfExists('reservation_detail_days');
    }
};
