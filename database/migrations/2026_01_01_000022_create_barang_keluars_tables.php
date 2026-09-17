<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang_keluars', function (Blueprint $table) {
            $table->id();
            $table->string('no_keluar', 50)->unique();
            $table->date('tanggal');
            $table->string('tujuan', 150); // e.g. Pemakaian Operasional, Pengiriman Proyek, Rusak / Scrap
            $table->string('penerima', 100)->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('barang_keluar_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_barang_keluar')->constrained('barang_keluars')->cascadeOnDelete();
            $table->foreignId('id_produk')->constrained('produks')->cascadeOnDelete();
            $table->integer('jumlah');
            $table->string('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang_keluar_items');
        Schema::dropIfExists('barang_keluars');
    }
};
