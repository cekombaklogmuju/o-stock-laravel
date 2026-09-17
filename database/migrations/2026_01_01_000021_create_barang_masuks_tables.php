<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang_masuks', function (Blueprint $table) {
            $table->id();
            $table->string('no_masuk', 50)->unique();
            $table->date('tanggal');
            $table->foreignId('id_supplier')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->string('no_surat_jalan', 100)->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('barang_masuk_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_barang_masuk')->constrained('barang_masuks')->cascadeOnDelete();
            $table->foreignId('id_produk')->constrained('produks')->cascadeOnDelete();
            $table->integer('jumlah');
            $table->string('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang_masuk_items');
        Schema::dropIfExists('barang_masuks');
    }
};
