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
        Schema::create('cashier_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cashier_id')->nullable()->constrained('cashiers');
            $table->foreignId('sale_detail_id')->nullable()->constrained('sale_details');
            $table->foreignId('service_id')->nullable()->constrained('services');
            $table->foreignId('reservation_detail_day_id')->nullable()->constrained('reservation_detail_days');
            $table->string('type')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('cash')->nullable()->default(1);
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
        Schema::dropIfExists('cashier_details');
    }
};
