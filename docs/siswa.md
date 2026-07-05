# Siswa — Database Restructuring Plan

## Tujuan

Memisahkan data master siswa dari data akademik tahunan agar:
- Kenaikan kelas lebih mudah (tinggal INSERT history baru)
- Riwayat akademik siswa tercatat rapi per tahun ajaran
- Kolom `tingkat` benar-benar terpakai
- Support mutasi (naik/tinggal/mutasi/lulus)

## Skema Akhir

### `siswa` (master — data tetap)

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint PK | |
| `nis` | string unique nullable | NISN |
| `nama` | string | |
| `angkatan` | string nullable | Tahun masuk pertama, e.g. "2025" |
| `nama_orang_tua` | string nullable | |
| `no_hp_orang_tua` | string nullable | |
| `email_orang_tua` | string nullable | |
| `is_calon` | boolean | |
| `calon_jenis` | string nullable | |
| `status_aktif` | boolean | |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

### `siswa_kelas_histories` (transaksi — data per tahun ajaran)

| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| `id` | bigint PK | |
| `siswa_id` | bigint FK → siswa.id | |
| `kelas` | string | e.g. "1A", "2B" |
| `tingkat` | string nullable | e.g. "1", "2" |
| `jenis_sekolah` | string nullable | SD, SMP, DTA, PAUD |
| `tahun_ajaran` | string | e.g. "2025/2026" |
| `tahun_mulai` | unsignedSmallInteger | e.g. 2025 |
| `mutasi` | string default "naik" | naik, tinggal, mutasi_masuk, mutasi_keluar, lulus |
| `is_current` | boolean default true | Flag kelas aktif sekarang |
| `catatan` | text nullable | |
| `created_by` | bigint FK → users.id | |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

UNIQUE: `(siswa_id, tahun_ajaran)`

## Relasi Model

### `Siswa.php`

```php
public function kelasHistories(): HasMany
{
    return $this->hasMany(SiswaKelasHistory::class);
}

public function kelasSaatIni(): HasOne
{
    return $this->hasOne(SiswaKelasHistory::class)->where('is_current', true);
}

public function scopePerKelas($query, string $kelas, ?string $tahunAjaran = null): void
{
    $query->whereHas('kelasSaatIni', function ($q) use ($kelas, $tahunAjaran) {
        $q->where('kelas', $kelas);
        if ($tahunAjaran) $q->where('tahun_ajaran', $tahunAjaran);
    });
}

public function scopeJenjang($query, string $jenjang): void
{
    $query->whereHas('kelasSaatIni', fn($q) => $q->where('jenis_sekolah', strtoupper($jenjang)));
}
```

### `SiswaKelasHistory.php`

Relasi sudah ada: `siswa()`, `createdBy()`

## Alur Kenaikan Kelas

```
1. SELECT siswa_kelas_histories WHERE is_current=1 AND kelas IN (...)
2. For each:
   a. History lama → is_current = 0
   b. Baru → INSERT: kelas=target, tingkat=naik, mutasi='naik', is_current=1
   c. Jika lulus → siswa.status_aktif = false, history.mutasi = 'lulus', is_current=0
```

## Daftar File yang Diubah

| File | Perubahan |
|------|-----------|
| `app/Models/Siswa.php` | Relasi baru, scope baru, hapus `jenjang` via field |
| `app/Models/SiswaKelasHistory.php` | Tambah fillable |
| `app/Filament/Resources/SiswaResource.php` | Ganti sumber data kelas |
| `app/Filament/Resources/SiswaResource/Pages/ListSiswaByJenjang.php` | Query via kelasSaatIni |
| `app/Filament/Resources/SiswaResource/Pages/ListSiswaByKelas.php` | Query via kelasSaatIni |
| `app/Filament/Resources/SiswaResource/Pages/DetailSiswa.php` | Display dari kelasSaatIni |
| `app/Filament/Resources/TagihanResource/Pages/ListTagihans.php` | Filter kelas via kelasSaatIni |
| `app/Filament/Pages/KenaikanKelasPage.php` | Logika baru full separation |
| `app/Filament/Pages/PembayaranSiswaPage.php` | Display dari kelasSaatIni |
| `app/Filament/Pages/Dashboard.php` | Count via kelasSaatIni |
| `database/seeders/Siswa20262027Seeder.php` | Insert ke 2 tabel |
| `database/seeders/DatabaseSeeder.php` | Tambah class baru |

## Migration Steps

1. `add_fields_to_siswa_and_histories` — tambah kolom baru
2. `migrate_siswa_data_to_histories` — pindah data
3. `drop_academic_fields_from_siswa` — hapus kolom lama
