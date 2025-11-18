<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransaksiSeeder extends Seeder
{
    public function run(): void
    {
        // Transaksi hanya Pemasukan yang terkait dengan Tagihan (Lunas)
        // Kita butuh 5 transaksi awal untuk menguji Kuitansi

        // Ambil 5 ID penghuni pertama
        $penghuniIds = DB::table('penghunis')->take(5)->pluck('id');

        $transaksis = [];
        $baseDate = Carbon::now()->subMonths(1);

        foreach ($penghuniIds as $index => $penghuniId) {
            $kamar = DB::table('kamars')->join('penghunis', 'kamars.id', '=', 'penghunis.kamar_id')
                ->where('penghunis.id', $penghuniId)->first();

            if ($kamar) {
                $transaksis[] = [
                    'tipe_transaksi' => 'Pemasukan',
                    'kategori' => 'Pembayaran Sewa',
                    'jumlah' => $kamar->harga_bulanan,
                    'deskripsi' => 'Pembayaran Sewa Awal',
                    'tanggal_transaksi' => $baseDate->copy()->addDays($index)->toDateString(),
                    'penghuni_id' => $penghuniId,
                    'kamar_id' => $kamar->id,
                    'metode_pembayaran' => 'Transfer',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }

        DB::table('transaksis')->insert($transaksis);
    }
}
