<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            $table->string('lokasi_rak', 100)->nullable()->after('barcode');
            $table->string('satuan', 30)->default('Pcs')->after('lokasi_rak');
            $table->integer('stok_minimum')->default(5)->after('stok');
            $table->text('spesifikasi')->nullable()->after('stok_minimum');
        });
    }

    public function down(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            $table->dropColumn(['lokasi_rak', 'satuan', 'stok_minimum', 'spesifikasi']);
        });
    }
};
