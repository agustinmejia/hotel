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
        Schema::create('person_defaulter_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_defaulter_id')->nullable()->constrained('person_defaulters');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->decimal('amount', 10, 2)->nullable();
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
        Schema::dropIfExists('person_defaulter_payments');
    }
};
