<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branch_requests', function (Blueprint $table) {
            $table->id();
            $table->string('no_permintaan', 50)->unique();
            $table->dateTime('tanggal_permintaan');
            $table->foreignId('id_cabang_peminta')->constrained('kantors')->cascadeOnDelete();
            $table->foreignId('id_user_peminta')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected', 'partial'])->default('pending');
            $table->enum('prioritas', ['normal', 'urgent', 'critical'])->default('normal');
            $table->text('keterangan')->nullable();
            $table->dateTime('tanggal_diproses')->nullable();
            $table->unsignedBigInteger('diproses_oleh')->nullable();
            $table->text('catatan_proses')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('branch_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_branch_request')->constrained('branch_requests')->cascadeOnDelete();
            $table->foreignId('id_produk')->constrained('produks')->cascadeOnDelete();
            $table->integer('jumlah_diminta');
            $table->integer('jumlah_disetujui')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_request_items');
        Schema::dropIfExists('branch_requests');
    }
};
