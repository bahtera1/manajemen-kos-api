<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TagihanSeeder extends Seeder
{
    public function run(): void
    {
        $tagihans = [];
        $invoiceCounter = 100001;

        // Ambil penghuni aktif yang punya kamar
        $penghunisAktif = DB::table('penghunis')
            ->where('status_sewa', 'Aktif')
            ->whereNotNull('kamar_id')
            ->inRandomOrder()
            ->get();

        if ($penghunisAktif->isEmpty()) {
            $this->command->warn('Gak ada penghuni aktif dengan kamar!');
            return;
        }

        // 1. TAGIHAN LUNAS → dari transaksi yang sudah ada
        $transaksiSample = DB::table('transaksis')
            ->where('tipe_transaksi', 'Pemasukan')
            ->whereNotNull('penghuni_id')
            ->whereNotNull('kamar_id')
            ->inRandomOrder()
            ->limit(20)
            ->get();

        foreach ($transaksiSample as $tr) {
            $tagihans[] = [
                'penghuni_id' => $tr->penghuni_id,
                'kamar_id' => $tr->kamar_id,
                'transaksi_id' => $tr->id,
                'nomor_tagihan' => 'INV-' . $invoiceCounter++,
                'deskripsi' => $tr->deskripsi,
                'jumlah' => $tr->jumlah,
                'jatuh_tempo' => Carbon::parse($tr->tanggal_transaksi)->subDays(random_int(5, 15))->format('Y-m-d'),
                'status' => 'Lunas', // PASTI ADA DI ENUM
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // 2. TAGIHAN BELUM LUNAS → untuk bulan depan
        $bulanDepan = Carbon::now()->addMonth()->startOfMonth();
        $jatuhTempoDepan = $bulanDepan->copy()->addDays(7);

        foreach ($penghunisAktif->take(12) as $p) {
            $kamar = DB::table('kamars')->find($p->kamar_id);
            if (!$kamar || !$kamar->harga_bulanan || $kamar->harga_bulanan <= 0)
                continue;

            $tagihans[] = [
                'penghuni_id' => $p->id,
                'kamar_id' => $p->kamar_id,
                'transaksi_id' => null,
                'nomor_tagihan' => 'INV-' . $invoiceCounter++,
                'deskripsi' => "Sewa Kamar {$kamar->nama_kamar} - Bulan " . $bulanDepan->translatedFormat('F Y'),
                'jumlah' => $kamar->harga_bulanan,
                'jatuh_tempo' => $jatuhTempoDepan->format('Y-m-d'),
                'status' => 'Belum Lunas', // PASTI ADA DI ENUM
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // 3. TAGIHAN TELAT BAYAR → status tetap 'Belum Lunas', tapi keterangannya "TELAT" + denda
        foreach ($penghunisAktif->random(min(6, $penghunisAktif->count())) as $p) {
            $kamar = DB::table('kamars')->find($p->kamar_id);
            if (!$kamar?->harga_bulanan || $kamar->harga_bulanan <= 0)
                continue;

            $jatuhTempoTelat = Carbon::now()->subDays(random_int(8, 55));
            $denda = 75000;

            $tagihans[] = [
                'penghuni_id' => $p->id,
                'kamar_id' => $p->kamar_id,
                'transaksi_id' => null,
                'nomor_tagihan' => 'INV-' . $invoiceCounter++,
                'deskripsi' => "TELAT - Sewa Kamar {$kamar->nama_kamar} " . $jatuhTempoTelat->translatedFormat('F Y') . " + Denda Rp " . number_format($denda, 0, ',', '.'),
                'jumlah' => $kamar->harga_bulanan + $denda,
                'jatuh_tempo' => $jatuhTempoTelat->format('Y-m-d'),
                'status' => 'Belum Lunas', // PAKAI YANG ADA DI ENUM!
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert semua
        if (!empty($tagihans)) {
            DB::table('tagihans')->insert($tagihans);
            $this->command->info("TagihanSeeder SELESAI! " . count($tagihans) . " tagihan berhasil dibuat (Lunas + Belum Lunas + Telat di deskripsi)");
        }
    }
}
