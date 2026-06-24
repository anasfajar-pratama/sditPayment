<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->string('no_ref')->nullable()->after('status');
            $table->date('tgl_bayar_struk')->nullable()->after('no_ref');
            $table->decimal('potongan', 15, 2)->default(0)->after('tgl_bayar_struk');
            $table->string('bukti_bayar')->nullable()->after('potongan');
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropColumn(['no_ref', 'tgl_bayar_struk', 'potongan', 'bukti_bayar']);
        });
    }
};
