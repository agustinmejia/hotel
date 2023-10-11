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
        Schema::create('product_branch_offices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_office_id')->nullable()->constrained('branch_offices');
            $table->foreignId('product_id')->nullable()->constrained('products');
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('quantity', 10, 2)->nullable();
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
        Schema::dropIfExists('product_branch_offices');
    }
};
