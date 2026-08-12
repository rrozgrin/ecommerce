<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('products') && Schema::hasTable('produtcs')) {
            Schema::rename('produtcs', 'products');
        }

        if (! Schema::hasTable('products')) {
            return;
        }

        if (Schema::hasColumn('products', 'is_avaliable')) {
            Schema::table('products', function (Blueprint $table) {
                $table->renameColumn('is_avaliable', 'is_available');
            });
        }

        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->change();
            $table->decimal('discount', 10, 2)->nullable()->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('total_price', 10, 2)->change();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->change();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->double('price', 10, 2)->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->double('total_price')->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->double('price', 8, 2)->change();
            $table->double('discount', 8, 2)->nullable()->change();
        });
    }
};
