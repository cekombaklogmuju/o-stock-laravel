<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualans', function (Blueprint $table) {
            $table->id();
            $table->string('no_invoice', 50)->unique();
            $table->date('tanggal');
            $table->foreignId('id_cabang')->constrained('kantors')->cascadeOnDelete();
            $table->foreignId('id_konsumen')->constrained('konsumens')->cascadeOnDelete();
            $table->foreignId('id_salesman')->constrained('salesmen')->cascadeOnDelete();
            $table->decimal('total_harga', 15, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('penjualan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_penjualan')->constrained('penjualans')->cascadeOnDelete();
            $table->foreignId('id_produk')->constrained('produks')->cascadeOnDelete();
            $table->decimal('harga', 15, 2);
            $table->integer('jumlah');
            $table->float('discount')->default(0);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualan_items');
        Schema::dropIfExists('penjualans');
    }
};
