<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PenghuniSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil ID Kamar yang sudah terisi (ID 1 sampai 5)
        $kamarIds = DB::table('kamars')->where('is_available', false)->pluck('id');

        $penghunis = [
            [
                'kamar_id' => $kamarIds[0],
                'nama_lengkap' => 'Nasrul Abdul Aziz Ta\'ba',
                'no_ktp' => '3278xxxxxx',
                'tanggal_masuk' => '2025-08-01',
                'masa_berakhir_sewa' => Carbon::now()->addMonths(1)->endOfMonth()->toDateString(), // Jatuh Tempo Bulan Depan
                'status_sewa' => 'Aktif',
                'no_hp' => '0812xxxxxx',
                'email' => 'nasrul@contoh.com',
                'pekerjaan' => 'Mahasiswa',
                'pic_emergency' => 'Ibu Kandung',
                'durasi_bayar_terakhir' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kamar_id' => $kamarIds[1],
                'nama_lengkap' => 'Budi Santoso',
                'no_ktp' => '3278xxxxxy',
                'tanggal_masuk' => '2025-09-15',
                'masa_berakhir_sewa' => Carbon::now()->addMonths(2)->endOfMonth()->toDateString(), // Lunas 2 bulan ke depan
                'status_sewa' => 'Aktif',
                'no_hp' => '0813xxxxxx',
                'email' => 'budi@contoh.com',
                'pekerjaan' => 'Karyawan Swasta',
                'pic_emergency' => 'Adik',
                'durasi_bayar_terakhir' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kamar_id' => $kamarIds[2],
                'nama_lengkap' => 'Siti Aisyah',
                'no_ktp' => '3278xxxxzz',
                'tanggal_masuk' => '2025-07-20',
                'masa_berakhir_sewa' => Carbon::now()->subDays(5)->endOfMonth()->toDateString(), // SUDAH JATUH TEMPO
                'status_sewa' => 'Aktif',
                'no_hp' => '0814xxxxxx',
                'email' => 'siti@contoh.com',
                'pekerjaan' => 'PNS',
                'pic_emergency' => 'Suami',
                'durasi_bayar_terakhir' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kamar_id' => $kamarIds[3],
                'nama_lengkap' => 'Joko Susilo',
                'no_ktp' => '3278xxxxaa',
                'tanggal_masuk' => '2025-10-01',
                'masa_berakhir_sewa' => Carbon::now()->addMonths(1)->endOfMonth()->toDateString(),
                'status_sewa' => 'Aktif',
                'no_hp' => '0815xxxxxx',
                'email' => 'joko@contoh.com',
                'pekerjaan' => 'Wirausaha',
                'pic_emergency' => 'Ayah',
                'durasi_bayar_terakhir' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kamar_id' => $kamarIds[4],
                'nama_lengkap' => 'Retno Dewi',
                'no_ktp' => '3278xxxxbb',
                'tanggal_masuk' => '2025-06-10',
                'masa_berakhir_sewa' => Carbon::now()->subMonths(2)->endOfMonth()->toDateString(), // NONAKTIF LAMA
                'status_sewa' => 'Nonaktif',
                'no_hp' => '0816xxxxxx',
                'email' => 'retno@contoh.com',
                'pekerjaan' => 'Guru',
                'pic_emergency' => 'Kakak',
                'durasi_bayar_terakhir' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        DB::table('penghunis')->insert($penghunis);
    }
}
