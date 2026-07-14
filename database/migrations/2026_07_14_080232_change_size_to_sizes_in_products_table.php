<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->json('sizes')->nullable()->after('color');
        });

        // Agar purana data migrate karna hai (optional):
        // DB::table('products')->get()->each(function ($product) {
        //     if ($product->size) {
        //         DB::table('products')->where('id', $product->id)
        //             ->update(['sizes' => json_encode([$product->size])]);
        //     }
        // });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('size');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('size')->nullable()->after('color');
            $table->dropColumn('sizes');
        });
    }
};