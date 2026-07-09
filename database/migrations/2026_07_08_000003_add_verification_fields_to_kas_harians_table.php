<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kas_harians', function (Blueprint $table) {
            $table->string('no_ref')->nullable()->after('source_id');
            $table->string('rekening_tujuan')->nullable()->after('no_ref');
            $table->string('nama_rekening_pengirim')->nullable()->after('rekening_tujuan');
            $table->datetime('verified_at')->nullable()->after('nama_rekening_pengirim');
            $table->foreignId('verified_by')->nullable()->constrained('users')->after('verified_at');
        });
    }

    public function down(): void
    {
        Schema::table('kas_harians', function (Blueprint $table) {
            $table->dropConstrainedForeignId('verified_by');
            $table->dropColumn(['no_ref', 'rekening_tujuan', 'nama_rekening_pengirim', 'verified_at']);
        });
    }
};