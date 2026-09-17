<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->string('no_opname', 50)->unique();
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('stock_opname_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_stock_opname')->constrained('stock_opnames')->cascadeOnDelete();
            $table->foreignId('id_produk')->constrained('produks')->cascadeOnDelete();
            $table->integer('stok_sistem');
            $table->integer('stok_fisik');
            $table->integer('selisih'); // stok_fisik - stok_sistem
            $table->string('alasan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opname_items');
        Schema::dropIfExists('stock_opnames');
    }
};
