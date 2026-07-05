# Ringkasan Perubahan — Branch `siswa_handlekenaikankelas`

## 1. Kenaikan Kelas (Bug Fix)
**File:** `app/Filament/Pages/KenaikanKelasPage.php`
- Tambah `->where('tahun_ajaran', $this->getTahunAjaranBerjalan())` di 5 query: `initKelasMapping()`, `jenisSekolahList()`, `kelasData()`, `siswaIds` duplicate check, inner `$histories` query.
- Tambah `$skipped` tracking + notifikasi untuk kelas yang dilewati karena target kosong di `prosesSemuaKenaikan()`.

## 2. Migration — Fix Duplicate `is_current`
**File:** `database/migrations/2026_07_04_230440_fix_duplicate_is_current_in_siswa_kelas_histories.php`
- Membersihkan duplikat `is_current=true` per siswa (menyisakan 1 record terbaru by id).

## 3. Tampilkan Data Historis di Halaman Jenjang/Kelas
**File:** `app/Filament/Resources/SiswaResource/Pages/ListSiswaByJenjang.php`
- `loadKelasData()` — pindah `->where('is_current', true)` ke dalam `else`; jika `filterTahunAjaran` aktif, filter pakai tahun ajaran bukan `is_current`.
- URL kartu kelas menyertakan `?tahun_ajaran=` jika filter aktif.

**File:** `resources/views/filament/resources/siswa-resource/pages/list-siswa-by-jenjang.blade.php`
- Label dinamis: "siswa" (tanpa "aktif") saat filter tahun terisi.

**File:** `app/Filament/Resources/SiswaResource/Pages/ListSiswaByKelas.php`
- Baca `?tahun_ajaran=` dari query string.
- `getTableQuery()` — pakai `kelasHistories` saat tahun ajaran dipilih, `kelasSaatIni` saat default.
- Breadcrumbs, title, tombol kembali dinamis sesuai konteks tahun.

## 4. Form Siswa — `tgl_lahir` & Hide `is_calon` di Edit
**File:** `database/migrations/2026_07_05_000636_add_tgl_lahir_to_siswa_table.php`
- Tambah kolom `tgl_lahir` (date, nullable) setelah `nama`.

**File:** `app/Models/Siswa.php`
- `tgl_lahir` di `$fillable` + `$casts` sebagai `'date'`.

**File:** `app/Filament/Resources/SiswaResource.php`
- `DatePicker::make('tgl_lahir')` — ditambahkan di Data Siswa section.
- Section `is_calon` + Toggle → `->hiddenOn('edit')` — hanya muncul saat create.
- Default `is_calon` = `true` (form create terbuka di mode Calon).

## 5. Calon Siswa — Tingkat Tujuan
**File:** `database/migrations/2026_07_05_005925_add_calon_tingkat_to_siswa_table.php`
- Tambah kolom `calon_tingkat` (tinyInteger, nullable) setelah `calon_jenis`.

**File:** `app/Models/Siswa.php`
- `calon_tingkat` di `$fillable` + `$casts` sebagai `'integer'`.
- Relasi `tagihanPendaftaran()` — hasOne Tagihan where `jenis_pembayaran_id = 1`.

**File:** `app/Filament/Resources/SiswaResource.php`
- Method `getCalonTingkatOptions()` — SD (1-6), SMP (7-9), DTA (1-4), PAUD/TK.
- Field `calon_tingkat` (Select, live, dinamis berdasarkan `calon_jenis`) di Data Calon section.

## 6. List Calon Siswa — Tabel & Proses Masuk Kelas
**File:** `app/Filament/Resources/SiswaResource/Pages/ListCalonSiswa.php`
- Tabel: Nama, Jenjang Pendidikan (badge), Tingkat Tujuan, No HP Orang Tua, Status Pembayaran Biaya Masuk (badge Lunas/Belum Bayar).
- **Button Hijau "Proses Masuk Kelas"** — modal dengan:
  - Select Jenjang → memicu `afterStateUpdated` untuk memuat calon berstatus Lunas.
  - Repeater: masing-masing calon menampilkan Nama + Select Kelas Tujuan (difilter sesuai tingkat, misal SD tingkat 1 → 1A,1B,1C,1D).
  - Submit: `is_calon=false` + buat `SiswaKelasHistory`.
  - Calon yang diproses otomatis hilang dari tabel (query `is_calon=1`).

## 7. Pembayaran Siswa — Potongan Lazy
**File:** `app/Filament/Pages/PembayaranSiswaPage.php`
- Field `potongan`: `->live()` → `->lazy()` — perhitungan hanya terjadi saat user klik di luar field (blur), bukan setiap kali mengetik.

## 8. Pengeluaran — Date Range Filter + Cetak PDF
**File:** `app/Filament/Pages/PengeluaranOperasionalPage.php`
- Filter: `filterBulan`/`filterTahun` → `filterStart`/`filterEnd` (date range).
- Query: `where('tahun')->where('bulan')` → `whereDate('tanggal', '>=', start) / <= end`.

**File:** `resources/views/filament/pages/pengeluaran-operasional-page.blade.php`
- Dropdown bulan/tahun → input date + tombol 📄 Cetak PDF.

**File:** `app/Filament/Pages/PengeluaranSosialPage.php`
- Sama: filter date range + tombol Cetak PDF.

**File:** `resources/views/filament/pages/pengeluaran-sosial-page.blade.php`
- Sama: input date + tombol Cetak PDF.

**File:** `app/Filament/Pages/PengeluaranUpahPage.php`
- Sama: filter date range + tombol Cetak PDF.
- `penerimas()` query pakai date range.
- `totalTahunPerPenerima()` dihapus.

**File:** `resources/views/filament/pages/pengeluaran-upah-page.blade.php`
- Sama: input date + tombol Cetak PDF.
- Referensi `totalTahunPerPenerima` dihapus.

## 9. Controller & Route & PDF Views
**File:** `app/Http/Controllers/OperasionalController.php`
- `cetakPdf()` — di-refactor pakai `buildData()`.
- `sosialPdf()` — baru.
- `upahPdf()` — baru (per penerima).

**File:** `routes/web.php`
- `GET /operasional/pdf` → `operasional.pdf`
- `GET /sosial/pdf` → `sosial.pdf`
- `GET /upah/pdf` → `upah.pdf`

**File:** `resources/views/pdf/operasional.blade.php`
- PDF landscape: header, tabel per kategori, grand total, page break.

**File:** `resources/views/pdf/sosial.blade.php`
- Sama struktur dengan tema pink.

**File:** `resources/views/pdf/upah.blade.php`
- Sama struktur dengan tema indigo, per penerima.
