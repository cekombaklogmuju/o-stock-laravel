<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kategori')->constrained('kategori_produks')->cascadeOnDelete();
            $table->foreignId('id_supplier')->constrained('suppliers')->cascadeOnDelete();
            $table->string('kode_produk', 100)->unique();
            $table->string('barcode', 100)->nullable()->index();
            $table->string('nama', 255);
            $table->string('slug', 255)->nullable();
            $table->decimal('harga_jual', 15, 2)->default(0);
            $table->integer('stok')->default(0);
            $table->string('status', 20)->default('aktif');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};
