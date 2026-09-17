<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('konsumens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_cabang')->constrained('kantors')->cascadeOnDelete();
            $table->string('nama', 150);
            $table->string('slug', 180)->nullable();
            $table->text('alamat')->nullable();
            $table->string('kota', 100)->nullable();
            $table->string('no_telp', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('konsumens');
    }
};
