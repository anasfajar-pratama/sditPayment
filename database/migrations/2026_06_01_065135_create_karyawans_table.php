<?php
// File: database/migrations/xxxx_create_karyawans_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();

            // ── Identitas ──────────────────────────────────────────────────
            $table->string('nama');
            $table->string('nik')->nullable()->unique()->comment('Nomor Induk Karyawan');
            $table->string('no_ktp')->nullable()->unique();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('email')->nullable();
            $table->string('foto')->nullable();

            // ── Pekerjaan ─────────────────────────────────────────────────
            /**
             * job  = kategori fungsi: guru | admin | operator | penjaga | kantin
             * jabatan = judul spesifik: "Kepala Sekolah", "Wali Kelas 1A", dst
             */
            $table->enum('job', ['guru', 'admin', 'operator', 'penjaga', 'kantin'])
                  ->default('guru');
            $table->string('jabatan')->nullable()->comment('Wali Kelas, Guru Mapel, Kepala Sekolah, dll');
            $table->string('mata_pelajaran')->nullable()->comment('Hanya untuk guru mapel');
            $table->string('kelas_ajar')->nullable()->comment('Misal: 1A, 2B, atau semua');

            // ── Status & Kepegawaian ───────────────────────────────────────
            $table->enum('status_kepegawaian', ['GTY', 'GTT', 'Honor', 'PNS', 'PPPK'])
                  ->nullable()->comment('GTY=Tetap Yayasan, GTT=Tidak Tetap');
            $table->enum('status', ['aktif', 'tidak_aktif', 'cuti', 'resign'])
                  ->default('aktif');
            $table->date('tanggal_masuk')->nullable();
            $table->date('tanggal_keluar')->nullable();

            // ── Penggajian ─────────────────────────────────────────────────
            $table->decimal('gaji_pokok', 12, 2)->nullable();

            // ── Rekening ─────────────────────────────────────────────────
            $table->string('nama_bank')->nullable();
            $table->string('no_rekening')->nullable();
            $table->string('atas_nama_rekening')->nullable();

            // ── Audit ─────────────────────────────────────────────────────
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('karyawans');
    }
};
