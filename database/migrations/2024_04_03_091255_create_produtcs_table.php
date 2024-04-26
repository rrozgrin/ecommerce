<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('produtcs', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('brand_id')->unsigned();
            $table->bigInteger('category_id')->unsigned();

            $table->string('name');
            $table->boolean('is_trendy')->default(0);
            $table->boolean('is_avaliable')->default(1);
            $table->double('price',8,2);
            $table->integer('amount');
            $table->double('discount',8,2)->nullable();
            $table->string('image');

            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produtcs');
    }
};
