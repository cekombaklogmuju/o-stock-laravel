<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kantors', function (Blueprint $table) {
            $table->id();
            $table->string('kode_cabang', 20)->unique();
            $table->string('tipe', 50)->default('Cabang'); // Pusat / Cabang
            $table->string('nama', 100);
            $table->string('slug', 120)->unique();
            $table->text('alamat')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kantors');
    }
};
