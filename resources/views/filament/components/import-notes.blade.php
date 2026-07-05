<div class="space-y-3 rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm text-blue-900">
    <div class="flex items-start gap-2">
        <svg class="mt-0.5 h-5 w-5 shrink-0 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
        </svg>
        <div class="space-y-2">
            <p class="font-semibold">Aturan Input Import Excel Calon Siswa</p>
            <ul class="list-disc space-y-1 pl-4 marker:text-blue-500">
                <li><strong>nama</strong> — Nama calon siswa, maksimal 100 karakter.</li>
                <li><strong>jenjang_pendidikan</strong> — Pilih salah satu: <code>SD</code>, <code>SMP</code>, <code>DTA</code>, <code>PAUD</code>, atau <code>TK</code>.</li>
                <li><strong>tingkat</strong> — Angka atau teks sesuai jenjang:
                    <ul class="list-disc pl-4 marker:text-blue-400">
                        <li><strong>SD</strong> : 1 – 6</li>
                        <li><strong>SMP</strong> : 7 – 9</li>
                        <li><strong>DTA</strong> : 1 – 4</li>
                        <li><strong>PAUD</strong> : <code>TK-A</code>, <code>TK-B</code>, atau <code>Kelompok Bermain</code> (bisa ditulis <code>KB</code>)</li>
                        <li><strong>TK</strong> : <code>TK-A</code> atau <code>TK-B</code>
                            <br><span class="text-blue-500">Bisa ditulis dengan spasi, underscore, atau hyphen: <code>TK A</code>, <code>TK_A</code>, <code>TK-A</code> — semua diterima.</span>
                        </li>
                    </ul>
                </li>
                <li><strong>hportu</strong> — No HP Orang Tua / Wali, 9 – 15 digit angka.</li>
                <li><strong>biayapendaftaran</strong> — Nominal biaya pendaftaran, angka (contoh: <code>500000</code>).</li>
            </ul>
            <p class="text-blue-700">Setiap data yang valid akan membuat <strong>Calon Siswa</strong> baru + <strong>Tagihan Biaya Pendaftaran</strong> (belum bayar).</p>
        </div>
    </div>
    <div class="border-t border-blue-200 pt-2 text-center">
        <a href="{{ route('calon-siswa.template') }}"
           class="inline-flex items-center gap-1 text-blue-700 underline hover:text-blue-900"
           target="_blank">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
            </svg>
            Download Template Excel
        </a>
    </div>
</div>
