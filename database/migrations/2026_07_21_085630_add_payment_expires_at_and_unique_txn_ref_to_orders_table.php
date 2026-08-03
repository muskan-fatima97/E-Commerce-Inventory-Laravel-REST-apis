<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('payment_expires_at')->nullable()->after('jazzcash_txn_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unique('txn_ref_no');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['txn_ref_no']);
            $table->dropColumn('payment_expires_at');
        });
    }
};