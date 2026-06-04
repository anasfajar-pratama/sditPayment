<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('token_pembelians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('token_listrik_id')->constrained('token_listrik')->cascadeOnDelete();
            $table->date('tanggal');
            $table->decimal('nominal', 15, 2);
            $table->string('nomor_token', 30)->nullable()->comment('Nomor token 20 digit pada struk');
            $table->decimal('kwh', 8, 2)->nullable()->comment('Jumlah KWH yang diperoleh');
            $table->text('note')->nullable();
            $table->char('bulan', 2);
            $table->char('tahun', 4);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('token_pembelians');
    }
};
