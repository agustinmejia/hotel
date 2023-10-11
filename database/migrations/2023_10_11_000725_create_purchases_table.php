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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers');
            $table->date('date')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->decimal('discount', 10, 2)->nullable();
            $table->text('observations')->nullable();
            $table->string('status')->nullable()->default('pagada');
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
        Schema::dropIfExists('purchases');
    }
};
