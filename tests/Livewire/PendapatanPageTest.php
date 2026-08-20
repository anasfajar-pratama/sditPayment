<?php

uses(Tests\TestCase::class)->in(__DIR__);

use App\Filament\Pages\PendapatanPage;
use App\Filament\Pages\RingkasanKasHarianPage;
use App\Models\Akun;
use App\Models\KasHarian;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

function buildMinimalSchema(): void
{
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password');
        $table->rememberToken();
        $table->timestamps();
    });

    Schema::create('roles', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('guard_name');
        $table->timestamps();
        $table->unique(['name', 'guard_name']);
    });

    Schema::create('permissions', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('guard_name');
        $table->timestamps();
        $table->unique(['name', 'guard_name']);
    });

    Schema::create('model_has_roles', function (Blueprint $table) {
        $table->unsignedBigInteger('role_id');
        $table->string('model_type');
        $table->unsignedBigInteger('model_id');
        $table->index(['model_id', 'model_type']);
        $table->primary(['role_id', 'model_id', 'model_type']);
    });

    Schema::create('model_has_permissions', function (Blueprint $table) {
        $table->unsignedBigInteger('permission_id');
        $table->string('model_type');
        $table->unsignedBigInteger('model_id');
        $table->index(['model_id', 'model_type']);
        $table->primary(['permission_id', 'model_id', 'model_type']);
    });

    Schema::create('role_has_permissions', function (Blueprint $table) {
        $table->unsignedBigInteger('permission_id');
        $table->unsignedBigInteger('role_id');
        $table->primary(['permission_id', 'role_id']);
    });

    Schema::create('akun', function (Blueprint $table) {
        $table->id();
        $table->string('kode_akun');
        $table->string('nama_akun');
        $table->string('kelompok')->nullable();
        $table->string('sub_kelompok')->nullable();
        $table->string('tipe')->nullable();
        $table->boolean('is_active')->default(true);
        $table->text('keterangan')->nullable();
        $table->timestamps();
    });

    Schema::create('kas_harians', function (Blueprint $table) {
        $table->id();
        $table->date('tanggal');
        $table->string('uraian')->nullable();
        $table->string('sub_kategori')->nullable();
        $table->unsignedBigInteger('akun_id')->nullable();
        $table->decimal('debit', 15, 2)->nullable();
        $table->decimal('kredit', 15, 2)->nullable();
        $table->string('bukti')->nullable();
        $table->string('source')->nullable();
        $table->unsignedBigInteger('source_id')->nullable();
        $table->string('no_ref')->nullable();
        $table->string('rekening_tujuan')->nullable();
        $table->string('nama_rekening_pengirim')->nullable();
        $table->timestamp('verified_at')->nullable();
        $table->unsignedBigInteger('verified_by')->nullable();
        $table->string('bulan')->nullable();
        $table->string('tahun')->nullable();
        $table->unsignedBigInteger('created_by')->nullable();
        $table->timestamps();
    });
}

beforeEach(function () {
    buildMinimalSchema();

    $role = Role::create(['name' => 'admin', 'guard_name' => 'web']);
    $user = User::factory()->create();
    $user->assignRole('admin');
    $this->actingAs($user);
});

it('renders PendapatanPage dan menghitung pendapatan per akun', function () {
    $akunSpp    = Akun::create(['kode_akun' => '4101', 'nama_akun' => 'Pendapatan SPP Bulanan', 'kelompok' => 'Pendapatan']);
    $akunDonasi = Akun::create(['kode_akun' => '4104', 'nama_akun' => 'Pendapatan Donasi', 'kelompok' => 'Pendapatan']);
    $akunCater  = Akun::create(['kode_akun' => '4107', 'nama_akun' => 'Pendapatan CATERING', 'kelompok' => 'Pendapatan']);

    KasHarian::create(['tanggal' => '2026-08-01', 'uraian' => 'Bayar SPP A', 'akun_id' => $akunSpp->id, 'debit' => 100000, 'rekening_tujuan' => 'Cash']);
    KasHarian::create(['tanggal' => '2026-08-02', 'uraian' => 'Bayar SPP B', 'akun_id' => $akunSpp->id, 'debit' => 200000, 'rekening_tujuan' => 'Bank BCA']);
    KasHarian::create(['tanggal' => '2026-08-03', 'uraian' => 'Donasi', 'akun_id' => $akunDonasi->id, 'debit' => 50000, 'rekening_tujuan' => null]);
    KasHarian::create(['tanggal' => '2026-09-01', 'uraian' => 'SPP Sept (di luar range)', 'akun_id' => $akunSpp->id, 'debit' => 999999, 'rekening_tujuan' => 'Cash']);

    $component = Livewire::test(PendapatanPage::class)
        ->set('filterStart', '2026-08-01')
        ->set('filterEnd', '2026-08-31');

    $component->assertOk();

    // Mount default: aktifkan akun pertama (4101 SPP)
    expect($component->get('activeTab'))->toBe((string) $akunSpp->id);

    // Ringkasan per akun
    $ringkasan = $component->get('ringkasan');
    expect($ringkasan[$akunSpp->id])->toBe(300000.0);
    expect($ringkasan[$akunDonasi->id])->toBe(50000.0);
    expect($ringkasan[$akunCater->id])->toBe(0.0);

    // Grand total = 350.000
    expect($component->get('grandTotal'))->toBe(350000.0);

    // Akun list terurut kode_akun (3 akun pendapatan)
    $akunList = $component->get('akunList');
    expect(count($akunList))->toBe(3);
    expect($akunList[0]['kode'])->toBe('4101');

    // Entries tab SPP = 2 transaksi, urut kronologis
    $entries = $component->get('entriesPerTab');
    expect(count($entries))->toBe(2);
    expect($entries[0]['tanggal'])->toBe('01-Aug-26');
    expect($entries[1]['jumlah'])->toBe(200000.0);

    // Berpindah tab ke Donasi
    $component->call('setTab', (string) $akunDonasi->id);
    expect($component->get('activeTab'))->toBe((string) $akunDonasi->id);
    expect(count($component->get('entriesPerTab')))->toBe(1);

    // Tab CATERING kosong
    $component->call('setTab', (string) $akunCater->id);
    expect($component->get('entriesPerTab'))->toBe([]);
});

it('renders RingkasanKasHarianPage dan menghitung transfer/cash/pengeluaran/kas hari ini', function () {
    $akunSpp = Akun::create(['kode_akun' => '4101', 'nama_akun' => 'Pendapatan SPP Bulanan', 'kelompok' => 'Pendapatan']);
    $akunBeban = Akun::create(['kode_akun' => '6201', 'nama_akun' => 'Beban Operasional Sekolah', 'kelompok' => 'Beban']);

    // 2 cash masuk (100k + 200k), 1 transfer masuk (300k), 1 pengeluaran (75k)
    KasHarian::create(['tanggal' => '2026-08-01', 'uraian' => 'Cash A', 'akun_id' => $akunSpp->id, 'debit' => 100000, 'rekening_tujuan' => 'Cash']);
    KasHarian::create(['tanggal' => '2026-08-02', 'uraian' => 'Transfer B', 'akun_id' => $akunSpp->id, 'debit' => 300000, 'rekening_tujuan' => 'Bank BCA']);
    KasHarian::create(['tanggal' => '2026-08-03', 'uraian' => 'Cash C', 'akun_id' => $akunSpp->id, 'debit' => 200000, 'rekening_tujuan' => null]);
    KasHarian::create(['tanggal' => '2026-08-04', 'uraian' => 'Beli ATK', 'akun_id' => $akunBeban->id, 'kredit' => 75000, 'sub_kategori' => 'PERLENGKAPAN']);

    $component = Livewire::test(RingkasanKasHarianPage::class)
        ->set('filterStart', '2026-08-01')
        ->set('filterEnd', '2026-08-31');

    $component->assertOk();

    expect($component->get('totalTransfer'))->toBe(300000.0);
    expect($component->get('totalCash'))->toBe(300000.0);
    expect($component->get('totalKredit'))->toBe(75000.0);
    expect($component->get('kasHariIni'))->toBe(225000.0);

    expect(count($component->get('entriesTransfer')))->toBe(1);
    expect(count($component->get('entriesCash')))->toBe(2);
    expect(count($component->get('entriesKredit')))->toBe(1);

    // Kas Admin (cash - pengeluaran): 3 baris, saldo berjalan terakhir = kas hari ini
    $kasAdmin = $component->get('entriesKasHariIni');
    expect(count($kasAdmin))->toBe(3);
    expect(end($kasAdmin)['saldo'])->toBe(225000.0);

    // Gabungan (transfer + cash + pengeluaran): 4 baris, saldo berjalan terakhir = kas hari ini + transfer
    expect($component->get('totalGabungan'))->toBe(525000.0);
    $gabungan = $component->get('entriesGabungan');
    expect(count($gabungan))->toBe(4);
    expect($gabungan[0]['tipe'])->toBe('Masuk');
    expect(end($gabungan)['saldo'])->toBe(525000.0);
});