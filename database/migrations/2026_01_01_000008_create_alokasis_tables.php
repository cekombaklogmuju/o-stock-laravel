<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alokasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_cabang')->constrained('kantors')->cascadeOnDelete();
            $table->string('no_alokasi', 50)->unique();
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('alokasi_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_alokasi')->constrained('alokasis')->cascadeOnDelete();
            $table->foreignId('id_produk')->constrained('produks')->cascadeOnDelete();
            $table->integer('jumlah');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alokasi_items');
        Schema::dropIfExists('alokasis');
    }
};
