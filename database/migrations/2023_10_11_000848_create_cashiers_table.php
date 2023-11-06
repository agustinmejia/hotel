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
        Schema::create('cashiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('branch_office_id')->nullable()->constrained('branch_offices');
            $table->text('observations')->nullable();
            $table->decimal('amount_total', 10, 2)->nullable();
            $table->decimal('amount_real', 10, 2)->nullable();
            $table->decimal('amount_surplus', 10, 2)->nullable();
            $table->decimal('amount_missing', 10, 2)->nullable();
            $table->string('status')->nullable()->default('abierta');
            $table->timestamps();
            $table->timestamp('closed_at')->nullable();
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
        Schema::dropIfExists('cashiers');
    }
};
