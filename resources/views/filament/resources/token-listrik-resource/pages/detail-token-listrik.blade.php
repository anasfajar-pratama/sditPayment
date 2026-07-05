<x-filament-panels::page>
<div style="display:flex;flex-direction:column;gap:1.5rem;">

    {{-- ══════════════════════════════════════════════════════════
         PROFIL TOKEN LISTRIK
    ══════════════════════════════════════════════════════════════ --}}
    <div style="background:#fff;border-radius:1rem;border:1px solid #f1f5f9;
                box-shadow:0 1px 4px rgba(0,0,0,0.06);padding:1.5rem;">

        <div style="display:flex;align-items:flex-start;justify-content:space-between;
                    flex-wrap:wrap;gap:1rem;">

            {{-- Ikon + Nama --}}
            <div style="display:flex;align-items:center;gap:1rem;">
                <div style="width:3rem;height:3rem;background:#fef9c3;border-radius:0.75rem;
                            display:flex;align-items:center;justify-content:center;">
                    <x-heroicon-o-bolt style="width:1.5rem;height:1.5rem;color:#a16207;" />
                </div>
                <div>
                    <h2 style="font-size:1.25rem;font-weight:800;color:#1f2937;margin:0;">
                        {{ $token->nama_ruangan }}
                    </h2>
                    @if ($token->nomor_meter)
                        <p style="font-size:0.8rem;color:#6b7280;margin:0.15rem 0 0;">
                            No. Meter: {{ $token->nomor_meter }}
                        </p>
                    @endif
                </div>
            </div>

            {{-- Status + Total --}}
            <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
                <div style="text-align:right;">
                    <div style="font-size:0.7rem;font-weight:600;color:#9ca3af;
                                text-transform:uppercase;letter-spacing:0.05em;">Total Pembelian</div>
                    <div style="font-size:1.25rem;font-weight:800;color:#1e3a8a;">
                        Rp {{ number_format($token->totalPembelian(), 0, ',', '.') }}
                    </div>
                </div>
                @if ($token->is_active)
                    <span style="padding:0.3rem 0.75rem;border-radius:1rem;font-size:0.75rem;
                                 font-weight:600;background:#dcfce7;color:#15803d;border:1px solid #bbf7d0;">
                        ● Aktif
                    </span>
                @else
                    <span style="padding:0.3rem 0.75rem;border-radius:1rem;font-size:0.75rem;
                                 font-weight:600;background:#fee2e2;color:#b91c1c;border:1px solid #fecaca;">
                        ✕ Nonaktif
                    </span>
                @endif
            </div>
        </div>

        @if ($token->keterangan)
            <div style="margin-top:0.75rem;padding:0.625rem 0.875rem;background:#f9fafb;
                        border-radius:0.5rem;font-size:0.8rem;color:#6b7280;border:1px solid #f1f5f9;">
                {{ $token->keterangan }}
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════
         FORM INPUT PEMBELIAN TOKEN BARU
    ══════════════════════════════════════════════════════════════ --}}
    <div style="background:#fff;border-radius:1rem;border:1px solid #f1f5f9;
                box-shadow:0 1px 4px rgba(0,0,0,0.06);overflow:hidden;">

        <div style="padding:0.875rem 1.5rem;background:#fef9c3;border-bottom:1px solid #fde68a;
                    display:flex;align-items:center;gap:0.5rem;">
            <x-heroicon-o-plus-circle style="width:1rem;height:1rem;color:#a16207;" />
            <h3 style="font-size:0.875rem;font-weight:700;color:#a16207;margin:0;">
                Catat Pembelian Token Baru
            </h3>
        </div>

        <div style="padding:1.5rem;">
            <form wire:submit.prevent="simpanPembelian">
                {{ $this->pembelianForm }}

                <div style="margin-top:1.25rem;display:flex;justify-content:flex-end;">
                    <button type="submit"
                        style="display:inline-flex;align-items:center;gap:0.5rem;
                               padding:0.625rem 1.75rem;border-radius:0.5rem;border:none;
                               background:#a16207;color:#fff;font-size:0.875rem;font-weight:700;
                               cursor:pointer;box-shadow:0 2px 4px rgba(0,0,0,0.12);"
                        onmouseover="this.style.background='#854d0e'"
                        onmouseout="this.style.background='#a16207'">
                        <x-heroicon-o-check style="width:1rem;height:1rem;" />
                        Simpan & Catat ke Kas Harian
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         HISTORY PEMBELIAN TOKEN
    ══════════════════════════════════════════════════════════════ --}}
    <div style="background:#fff;border-radius:1rem;border:1px solid #f1f5f9;
                box-shadow:0 1px 4px rgba(0,0,0,0.06);overflow:hidden;">

        <div style="padding:1rem 1.5rem;border-bottom:1px solid #f1f5f9;
                    display:flex;align-items:center;justify-content:space-between;">
            <h3 style="font-size:0.9rem;font-weight:700;color:#1f2937;margin:0;">
                History Pembelian Token
            </h3>
            <span style="font-size:0.75rem;color:#9ca3af;">
                {{ $this->historyPembelian->count() }} transaksi
            </span>
        </div>

        @if ($this->historyPembelian->isEmpty())
            <div style="padding:3rem 1.5rem;text-align:center;color:#9ca3af;">
                <x-heroicon-o-bolt style="width:2.5rem;height:2.5rem;margin:0 auto 0.75rem;opacity:0.4;" />
                <p style="font-size:0.875rem;">Belum ada history pembelian token.</p>
            </div>
        @else
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:0.8125rem;">
                    <thead>
                        <tr style="background:#1f2937;color:#fff;">
                            <th style="padding:0.75rem 1rem;text-align:center;font-size:0.7rem;
                                       font-weight:600;letter-spacing:0.05em;text-transform:uppercase;
                                       width:2.5rem;">No</th>
                            <th style="padding:0.75rem 1rem;text-align:left;font-size:0.7rem;
                                       font-weight:600;letter-spacing:0.05em;text-transform:uppercase;">
                                Tanggal</th>
                            <th style="padding:0.75rem 1rem;text-align:left;font-size:0.7rem;
                                       font-weight:600;letter-spacing:0.05em;text-transform:uppercase;">
                                Nomor Token</th>
                            <th style="padding:0.75rem 1rem;text-align:center;font-size:0.7rem;
                                       font-weight:600;letter-spacing:0.05em;text-transform:uppercase;
                                       width:7rem;">KWH</th>
                            <th style="padding:0.75rem 1rem;text-align:right;font-size:0.7rem;
                                       font-weight:600;letter-spacing:0.05em;text-transform:uppercase;
                                       width:10rem;">Nominal (Rp)</th>
                            <th style="padding:0.75rem 1rem;text-align:center;font-size:0.7rem;
                                       font-weight:600;letter-spacing:0.05em;text-transform:uppercase;
                                       width:4rem;">Bukti</th>
                            <th style="padding:0.75rem 1rem;text-align:left;font-size:0.7rem;
                                       font-weight:600;letter-spacing:0.05em;text-transform:uppercase;">
                                Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $total = 0; @endphp
                        @foreach ($this->historyPembelian as $i => $p)
                            @php $total += (float)$p->nominal; @endphp
                            <tr style="border-bottom:1px solid #f8fafc;background:#fff;"
                                onmouseover="this.style.background='#f9fafb'"
                                onmouseout="this.style.background='#fff'">

                                <td style="padding:0.75rem 1rem;text-align:center;
                                           color:#9ca3af;font-size:0.75rem;">{{ $i + 1 }}</td>

                                <td style="padding:0.75rem 1rem;">
                                    <div style="font-weight:600;color:#1f2937;">
                                        {{ $p->tanggal->format('d M Y') }}
                                    </div>
                                    <div style="font-size:0.7rem;color:#9ca3af;">
                                        {{ $p->tanggal->isoFormat('dddd') }}
                                    </div>
                                </td>

                                <td style="padding:0.75rem 1rem;">
                                    @if ($p->nomor_token)
                                        <span style="font-family:monospace;font-size:0.8rem;
                                                     background:#f3f4f6;padding:0.2rem 0.5rem;
                                                     border-radius:0.25rem;color:#1f2937;">
                                            {{ $p->nomor_token }}
                                        </span>
                                    @else
                                        <span style="color:#d1d5db;">—</span>
                                    @endif
                                </td>

                                <td style="padding:0.75rem 1rem;text-align:center;">
                                    @if ($p->kwh)
                                        <span style="background:#dbeafe;color:#1d4ed8;
                                                     padding:0.2rem 0.5rem;border-radius:0.75rem;
                                                     font-size:0.75rem;font-weight:600;">
                                            {{ number_format($p->kwh, 1) }} kWh
                                        </span>
                                    @else
                                        <span style="color:#d1d5db;">—</span>
                                    @endif
                                </td>

                                <td style="padding:0.75rem 1rem;text-align:right;
                                           font-weight:700;color:#1f2937;">
                                    Rp {{ number_format((int)$p->nominal, 0, ',', '.') }}
                                </td>

                                <td style="padding:0.75rem 1rem;text-align:center;">
                                    @if ($p->bukti)
                                        <button type="button"
                                            x-data
                                            x-on:click="
                                                $nextTick(() => {
                                                    $dispatch('open-bukti-token', { url: '{{ $p->bukti_url }}' });
                                                });
                                            "
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 border border-blue-200 transition cursor-pointer">
                                            <x-heroicon-o-eye class="w-4 h-4" />
                                        </button>
                                    @else
                                        <span style="color:#d1d5db;">—</span>
                                    @endif
                                </td>

                                <td style="padding:0.75rem 1rem;color:#6b7280;font-size:0.78rem;">
                                    {{ $p->note ?? '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background:#fef9c3;border-top:2px solid #fde68a;">
                            <td colspan="4"
                                style="padding:0.875rem 1rem;text-align:right;
                                       font-size:0.8rem;font-weight:600;color:#a16207;">
                                Total Pembelian
                            </td>
                            <td style="padding:0.875rem 1rem;text-align:right;
                                       font-size:0.875rem;font-weight:800;color:#92400e;">
                                Rp {{ number_format($total, 0, ',', '.') }}
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════
         CATATAN
    ══════════════════════════════════════════════════════════════ --}}
    <div style="padding:1rem 1.25rem;border-radius:0.75rem;background:#fef9c3;border:1px solid #fde68a;">
        <p style="font-size:0.75rem;color:#a16207;margin:0 0 0.25rem;font-weight:600;">
            💡 Catatan
        </p>
        <ul style="font-size:0.75rem;color:#a16207;margin:0;padding-left:1.1rem;
                   list-style-type:disc;display:flex;flex-direction:column;gap:0.2rem;">
            <li>Setiap pembelian token otomatis tercatat di <strong>Kas Harian</strong>
                sebagai pengeluaran (kredit) dengan akun TOKEN & PULSA.</li>
            <li><strong>Nomor Token</strong> adalah 20 digit angka yang tertera di struk pembelian PLN.</li>
            <li>Untuk menghapus transaksi, hapus data dari Kas Harian — posting akan ikut terhapus.</li>
        </ul>
    </div>

</div>

{{-- Popup preview bukti transaksi --}}
<div
    x-data="{ open: false, url: '' }"
    x-on:open-bukti-token.window="url = $event.detail.url; open = true"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-[999] flex items-center justify-center bg-black/60 p-4"
    x-on:click.self="open = false"
>
    <div class="relative w-full max-w-2xl max-h-[80vh] bg-white rounded-xl shadow-2xl overflow-auto">
        <button type="button"
            x-on:click="open = false"
            class="sticky top-2 z-10 ml-auto mr-2 block w-8 h-8 flex items-center justify-center rounded-full bg-black/40 text-white hover:bg-black/60 transition">
            <x-heroicon-o-x-mark class="w-5 h-5" />
        </button>
        <div class="p-4 pt-0 flex items-start justify-center">
            <img :src="url" alt="Bukti Transaksi Token" class="max-w-full h-auto rounded">
        </div>
    </div>
</div>
</x-filament-panels::page>
