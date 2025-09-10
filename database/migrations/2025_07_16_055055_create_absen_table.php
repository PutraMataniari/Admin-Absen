<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('absen', function (Blueprint $table) {
            $table->id();
            $table->string('jenis');
            $table->string('nama');
            // $table->unsignedBigInteger('pegawai_id'); // ganti nama jadi foreign key
            $table->timestamp('waktu_absen')->unique();
            $table->string('lokasi');
            $table->string('gambar');
            $table->text('laporan_kinerja')->nullable();
            $table->string('bukti')->nullable();
            $table->timestamps();

            // foreign key ke tabel pegawai
            // $table->foreign('pegawai_id')->references('id')->on('pegawai')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absen');
    }
};
