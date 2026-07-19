<x-filament-panels::page>
<div class="space-y-6">

    {{-- ══════════════════════════════════════════════════
         SECTION 1 — MASTER BIAYA
    ══════════════════════════════════════════════════ --}}
    <div style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:18px 20px;">
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:14px;">
            <div style="background:#eff6ff; border-radius:8px; padding:6px;">
                <x-heroicon-o-currency-dollar style="width:18px;height:18px;color:#2563eb;"/>
            </div>
            <div>
                <div style="font-size:13px; font-weight:700; color:#374151;">Master Biaya</div>
                <div style="font-size:11px; color:#9ca3af;">Atur nominal SPP dan Daftar Ulang per tahun ajaran</div>
            </div>
        </div>

        <div style="max-width:500px;">
            <form wire:submit="saveBiaya">
                {{ $this->form }}

                <div style="margin-top:12px;">
                    <x-filament::button type="submit" color="primary">
                        Simpan Biaya
                    </x-filament::button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         SECTION 2 — MASTER REKENING TUJUAN
    ══════════════════════════════════════════════════ --}}
    <div style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:18px 20px;">
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:14px;">
            <div style="background:#f0fdf4; border-radius:8px; padding:6px;">
                <x-heroicon-o-banknotes style="width:18px;height:18px;color:#16a34a;"/>
            </div>
            <div>
                <div style="font-size:13px; font-weight:700; color:#374151;">Master Rekening Tujuan</div>
                <div style="font-size:11px; color:#9ca3af;">Daftar rekening untuk pembayaran dan donasi</div>
            </div>
        </div>

        {{ $this->table }}
    </div>

</div>
</x-filament-panels::page>
