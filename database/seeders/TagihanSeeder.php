<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TagihanSeeder extends Seeder
{
    public function run(): void
    {
        $penghunis = DB::table('penghunis')->where('status_sewa', 'Aktif')->get();
        $transaksis = DB::table('transaksis')->whereNotNull('penghuni_id')->get(); // Transaksi yang punya penghuni

        $tagihans = [];
        $invoiceCounter = 1;

        // ----------------------------------------------------
        // BAGIAN 1: TAGIHAN SUDAH LUNAS (5 DATA)
        // Dibuat berdasarkan Transaksi yang ada
        // ----------------------------------------------------
        foreach ($transaksis as $transaksi) {
            $tagihans[] = [
                'penghuni_id' => $transaksi->penghuni_id,
                'kamar_id' => $transaksi->kamar_id,
                'transaksi_id' => $transaksi->id, // Terhubung ke Transaksi
                'nomor_tagihan' => 'INV-LUNAS-' . $invoiceCounter++,
                'deskripsi' => $transaksi->deskripsi . ' (Lunas)',
                'jumlah' => $transaksi->jumlah,
                'jatuh_tempo' => Carbon::parse($transaksi->tanggal_transaksi)->subDays(10)->toDateString(),
                'status' => 'Lunas',
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        // ----------------------------------------------------
        // BAGIAN 2: TAGIHAN BELUM LUNAS & JATUH TEMPO (5 DATA)
        // Dibuat untuk 5 penghuni aktif
        // ----------------------------------------------------
        foreach ($penghunis->take(5) as $penghuni) {
            // Kamar yang terkait
            $kamar = DB::table('kamars')->where('id', $penghuni->kamar_id)->first();

            // Tagihan Bulan Berikutnya (Belum Lunas/Dibuat)
            $tagihans[] = [
                'penghuni_id' => $penghuni->id,
                'kamar_id' => $penghuni->kamar_id,
                'transaksi_id' => null,
                'nomor_tagihan' => 'INV-DRAFT-' . $invoiceCounter++,
                'deskripsi' => 'Sewa Bulan ' . Carbon::parse($penghuni->masa_berakhir_sewa)->addDay()->format('M Y'),
                'jumlah' => $kamar->harga_bulanan,
                'jatuh_tempo' => Carbon::parse($penghuni->masa_berakhir_sewa)->addDay()->toDateString(), // Jatuh tempo setelah masa sewa berakhir
                'status' => 'Belum Lunas',
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        DB::table('tagihans')->insert($tagihans);
    }
}
