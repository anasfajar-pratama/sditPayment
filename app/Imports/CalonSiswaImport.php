<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\Tagihan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class CalonSiswaImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    public array $errors = [];
    public int $imported = 0;

    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            $this->errors[] = 'File Excel kosong atau tidak memiliki data.';
            return;
        }

        if ($rows->count() > 500) {
            $this->errors[] = 'Maksimal 500 baris data per import. File Anda memiliki ' . $rows->count() . ' baris.';
            return;
        }

        // ── Validasi keberadaan kolom wajib ──
        $firstRow = $rows->first();
        $headers = array_keys($firstRow->toArray());
        $requiredColumns = ['nama', 'jenjang_pendidikan', 'tingkat', 'hportu', 'biayapendaftaran'];
        $missingColumns = array_diff($requiredColumns, $headers);
        if (! empty($missingColumns)) {
            $missingList = implode(', ', $missingColumns);
            $this->errors[] = "Kolom wajib tidak ditemukan: {$missingList}. Pastikan file Anda memiliki kolom: nama, jenjang_pendidikan, tingkat, hportu, biayapendaftaran.";
            return;
        }

        $jenjangMap = [
            'SD'   => 'sd',
            'SMP'  => 'smp',
            'DTA'  => 'dta',
            'PAUD' => 'paud',
            'TK'   => 'tk',
        ];

        $tingkatMapPaud = [
            'tk-a'             => 1,
            'tk-b'             => 2,
            'kelompok-bermain' => 3,
            'kb'               => 3,
        ];

        $tingkatRange = [
            'sd'  => ['min' => 1, 'max' => 6, 'label' => '1 – 6'],
            'smp' => ['min' => 7, 'max' => 9, 'label' => '7 – 9'],
            'dta' => ['min' => 1, 'max' => 4, 'label' => '1 – 4'],
        ];

        $validatedRows = [];

        foreach ($rows as $index => $row) {
            $rowNum = $index + 2;

            $nama = trim($row['nama'] ?? '');
            $jenjangInput = trim($row['jenjang_pendidikan'] ?? '');
            $tingkatInput = trim((string) ($row['tingkat'] ?? ''));
            $hportu = trim($row['hportu'] ?? '');
            $biaya = trim((string) ($row['biayapendaftaran'] ?? ''));

            // ── Validasi nama ──
            if (empty($nama)) {
                $this->errors[] = "Baris {$rowNum}: 'nama' wajib diisi.";
                continue;
            }
            if (strlen($nama) > 100) {
                $this->errors[] = "Baris {$rowNum}: 'nama' maksimal 100 karakter.";
                continue;
            }

            // ── Validasi jenjang_pendidikan ──
            if (empty($jenjangInput)) {
                $this->errors[] = "Baris {$rowNum}: 'jenjang_pendidikan' wajib diisi.";
                continue;
            }
            $jenjangKey = strtoupper($jenjangInput);
            if (! isset($jenjangMap[$jenjangKey])) {
                $this->errors[] = "Baris {$rowNum}: 'jenjang_pendidikan' tidak valid. Gunakan: SD, SMP, DTA, PAUD, atau TK.";
                continue;
            }
            $calonJenis = $jenjangMap[$jenjangKey];

            // ── Validasi tingkat ──
            if ($tingkatInput === '') {
                $this->errors[] = "Baris {$rowNum}: 'tingkat' wajib diisi.";
                continue;
            }

            if (in_array($calonJenis, ['paud', 'tk'])) {
                $normalizedTingkat = strtolower(trim($tingkatInput));
                $normalizedTingkat = preg_replace('/[–—_\s]+/', '-', $normalizedTingkat);

                if (! isset($tingkatMapPaud[$normalizedTingkat])) {
                    $allowed = 'TK-A, TK-B, Kelompok Bermain (KB)';
                    $this->errors[] = "Baris {$rowNum}: 'tingkat' untuk {$jenjangKey} harus: {$allowed}.";
                    continue;
                }
                $calonTingkat = $tingkatMapPaud[$normalizedTingkat];
            } else {
                if (! ctype_digit($tingkatInput)) {
                    $this->errors[] = "Baris {$rowNum}: 'tingkat' untuk {$jenjangKey} harus berupa angka.";
                    continue;
                }
                $tingkatVal = (int) $tingkatInput;
                $range = $tingkatRange[$calonJenis];
                if ($tingkatVal < $range['min'] || $tingkatVal > $range['max']) {
                    $this->errors[] = "Baris {$rowNum}: 'tingkat' untuk {$jenjangKey} harus {$range['label']}.";
                    continue;
                }
                $calonTingkat = $tingkatVal;
            }

            // ── Validasi hportu ──
            if (empty($hportu)) {
                $this->errors[] = "Baris {$rowNum}: 'hportu' (No HP Orang Tua) wajib diisi.";
                continue;
            }
            $hportuClean = preg_replace('/[^0-9]/', '', $hportu);
            if (empty($hportuClean)) {
                $this->errors[] = "Baris {$rowNum}: 'hportu' harus berisi nomor HP yang valid.";
                continue;
            }
            if (strlen($hportuClean) < 9 || strlen($hportuClean) > 15) {
                $this->errors[] = "Baris {$rowNum}: 'hportu' panjang nomor HP harus 9-15 digit.";
                continue;
            }

            // ── Validasi biayapendaftaran ──
            if ($biaya === '') {
                $this->errors[] = "Baris {$rowNum}: 'biayapendaftaran' wajib diisi.";
                continue;
            }
            $biayaClean = str_replace(['.', ','], ['', '.'], $biaya);
            if (! is_numeric($biayaClean)) {
                $this->errors[] = "Baris {$rowNum}: 'biayapendaftaran' harus berupa angka (contoh: 500000).";
                continue;
            }
            $biayaVal = (float) $biayaClean;
            if ($biayaVal < 0) {
                $this->errors[] = "Baris {$rowNum}: 'biayapendaftaran' tidak boleh negatif.";
                continue;
            }

            $validatedRows[] = [
                'nama'             => $nama,
                'calon_jenis'      => $calonJenis,
                'calon_tingkat'    => $calonTingkat,
                'no_hp_orang_tua'  => $hportuClean,
                'biaya_pendaftaran'=> $biayaVal,
            ];
        }

        if (! empty($this->errors)) {
            return;
        }

        // ── Proses insert dalam transaction ──
        DB::beginTransaction();
        try {
            foreach ($validatedRows as $data) {
                $siswa = Siswa::create([
                    'nama'            => $data['nama'],
                    'calon_jenis'     => $data['calon_jenis'],
                    'calon_tingkat'   => $data['calon_tingkat'],
                    'no_hp_orang_tua' => $data['no_hp_orang_tua'],
                    'is_calon'        => true,
                    'status_aktif'    => true,
                ]);

                Tagihan::create([
                    'siswa_id'            => $siswa->id,
                    'jenis_pembayaran_id' => 1,
                    'bulan'               => now()->format('m'),
                    'tahun'               => now()->format('Y'),
                    'nominal_tagihan'     => $data['biaya_pendaftaran'],
                    'status'              => 'belum_bayar',
                ]);

                $this->imported++;
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->errors[] = 'Terjadi kesalahan sistem saat menyimpan data: ' . $e->getMessage();
        }
    }
}
