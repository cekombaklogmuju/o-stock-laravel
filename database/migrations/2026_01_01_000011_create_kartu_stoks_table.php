<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kartu_stoks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_produk')->constrained('produks')->cascadeOnDelete();
            $table->foreignId('id_cabang')->constrained('kantors')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('no_bukti', 100);
            $table->string('keterangan', 255);
            $table->integer('stok_masuk')->default(0);
            $table->integer('stok_keluar')->default(0);
            $table->integer('saldo_akhir')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kartu_stoks');
    }
};
