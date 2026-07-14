<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add a temporary new JSON column
        Schema::table('products', function (Blueprint $table) {
            $table->json('size_new')->nullable()->after('size');
        });

        // 2. Copy old string values into the new column as a JSON array
        //    e.g. "M" becomes ["M"]
        DB::table('products')->orderBy('id')->chunk(100, function ($products) {
            foreach ($products as $product) {
                DB::table('products')
                    ->where('id', $product->id)
                    ->update([
                        'size_new' => $product->size
                            ? json_encode([$product->size])
                            : json_encode([]),
                    ]);
            }
        });

        // 3. Drop the old column, then rename the new one to "size"
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('size');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('size_new', 'size');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('size_old')->nullable()->after('size');
        });

        DB::table('products')->orderBy('id')->chunk(100, function ($products) {
            foreach ($products as $product) {
                $sizes = json_decode($product->size, true);
                DB::table('products')
                    ->where('id', $product->id)
                    ->update([
                        'size_old' => $sizes[0] ?? null,
                    ]);
            }
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('size');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('size_old', 'size');
        });
    }
};