<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PenghuniSeeder extends Seeder
{
    public function run(): void
    {
        // Asumsi ID kamar dari 1 hingga 23 (berdasarkan seeder kamars sebelumnya)
        $kamarIds = range(1, 23);

        $penghuni = [
            [
                'kamar_id' => $kamarIds[0], // Menggunakan ID kamar secara berurutan, ulangi jika lebih dari 23
                'nama_lengkap' => 'Adiguna Hernara',
                'no_ktp' => '3278033006960007',
                'tanggal_masuk' => '2025-01-14',
                'masa_berakhir_sewa' => '2025-07-22', // Berdasarkan Tanggal Keluar
                'status_sewa' => 'Nonaktif',
                'no_hp' => '081313089197',
                'email' => '',
                'pekerjaan' => 'Karyawan Kofminko',
                'pic_emergency' => '',
                'durasi_bayar_terakhir' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kamar_id' => $kamarIds[1],
                'nama_lengkap' => 'Ade Sucipto',
                'no_ktp' => '0000000000000000',
                'tanggal_masuk' => '2025-02-09',
                'masa_berakhir_sewa' => '2025-07-09',
                'status_sewa' => 'Aktif',
                'no_hp' => '08121480879',
                'email' => '',
                'pekerjaan' => 'Polisi',
                'pic_emergency' => '',
                'durasi_bayar_terakhir' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kamar_id' => $kamarIds[2],
                'nama_lengkap' => 'Ocid',
                'no_ktp' => '0000000000000000',
                'tanggal_masuk' => '2025-05-04',
                'masa_berakhir_sewa' => '2025-07-04',
                'status_sewa' => 'Aktif',
                'no_hp' => '0895604065533',
                'email' => '',
                'pekerjaan' => 'Bangunan',
                'pic_emergency' => 'Ifah',
                'durasi_bayar_terakhir' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kamar_id' => $kamarIds[3],
                'nama_lengkap' => 'Veronika Manurung',
                'no_ktp' => '0000000000000000',
                'tanggal_masuk' => '2025-04-19',
                'masa_berakhir_sewa' => '2025-05-19',
                'status_sewa' => 'Nonaktif',
                'no_hp' => '081299373146',
                'email' => '',
                'pekerjaan' => 'Karyawan',
                'pic_emergency' => '',
                'durasi_bayar_terakhir' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kamar_id' => $kamarIds[4],
                'nama_lengkap' => 'Muhammad Faris Alghifari',
                'no_ktp' => '3273172009970001',
                'tanggal_masuk' => '2025-02-11',
                'masa_berakhir_sewa' => '2025-07-15',
                'status_sewa' => 'Aktif',
                'no_hp' => '085798437420',
                'email' => '',
                'pekerjaan' => 'Telkom',
                'pic_emergency' => '',
                'durasi_bayar_terakhir' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kamar_id' => $kamarIds[5],
                'nama_lengkap' => 'Ayesa Acep',
                'no_ktp' => '0000000000000000',
                'tanggal_masuk' => '2024-12-15',
                'masa_berakhir_sewa' => '2025-12-15',
                'status_sewa' => 'Aktif',
                'no_hp' => '089654961206',
                'email' => '',
                'pekerjaan' => 'BPRKS - Haneda',
                'pic_emergency' => '',
                'durasi_bayar_terakhir' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kamar_id' => $kamarIds[6],
                'nama_lengkap' => 'Sahla Audriya Fahiratunisa',
                'no_ktp' => '0000000000000000',
                'tanggal_masuk' => '2025-01-05',
                'masa_berakhir_sewa' => '2025-07-05',
                'status_sewa' => 'Aktif',
                'no_hp' => '083879042670',
                'email' => '',
                'pekerjaan' => 'Karyawan',
                'pic_emergency' => '',
                'durasi_bayar_terakhir' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kamar_id' => $kamarIds[7],
                'nama_lengkap' => 'Nur Janah / Fahira',
                'no_ktp' => '0000000000000000',
                'tanggal_masuk' => '2025-02-28',
                'masa_berakhir_sewa' => '2025-07-30',
                'status_sewa' => 'Nonaktif',
                'no_hp' => '085222570656',
                'email' => '',
                'pekerjaan' => 'Karyawan',
                'pic_emergency' => '',
                'durasi_bayar_terakhir' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kamar_id' => $kamarIds[8],
                'nama_lengkap' => 'Siti Sopariah',
                'no_ktp' => '0000000000000000',
                'tanggal_masuk' => '2022-01-11',
                'masa_berakhir_sewa' => '2025-06-25',
                'status_sewa' => 'Nonaktif',
                'no_hp' => '081223723121',
                'email' => '',
                'pekerjaan' => 'Mahasiswa',
                'pic_emergency' => '',
                'durasi_bayar_terakhir' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kamar_id' => $kamarIds[9],
                'nama_lengkap' => 'Gonggoman Yogo',
                'no_ktp' => '0000000000000000',
                'tanggal_masuk' => null,
                'masa_berakhir_sewa' => '2025-07-01',
                'status_sewa' => 'Aktif',
                'no_hp' => '089520087867',
                'email' => '',
                'pekerjaan' => 'Karyawan - Yakult',
                'pic_emergency' => '',
                'durasi_bayar_terakhir' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kamar_id' => $kamarIds[10],
                'nama_lengkap' => 'Ibu Reni',
                'no_ktp' => '0000000000000000',
                'tanggal_masuk' => '2025-08-15',
                'masa_berakhir_sewa' => '2025-07-12',
                'status_sewa' => 'Aktif',
                'no_hp' => '081224209291',
                'email' => '',
                'pekerjaan' => 'OTO - Finance',
                'pic_emergency' => '',
                'durasi_bayar_terakhir' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kamar_id' => $kamarIds[11],
                'nama_lengkap' => 'Dhani Wahyudin',
                'no_ktp' => '0000000000000000',
                'tanggal_masuk' => null,
                'masa_berakhir_sewa' => '2025-07-01',
                'status_sewa' => 'Aktif',
                'no_hp' => '085721219815',
                'email' => '',
                'pekerjaan' => 'Karyawan - Indofood',
                'pic_emergency' => '',
                'durasi_bayar_terakhir' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kamar_id' => $kamarIds[12],
                'nama_lengkap' => 'Ruslan Febrian',
                'no_ktp' => '0000000000000000',
                'tanggal_masuk' => '2025-05-05',
                'masa_berakhir_sewa' => '2025-10-04',
                'status_sewa' => 'Nonaktif',
                'no_hp' => '08986047986',
                'email' => '',
                'pekerjaan' => 'Karyawan - Mega Finance',
                'pic_emergency' => '',
                'durasi_bayar_terakhir' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kamar_id' => $kamarIds[13],
                'nama_lengkap' => 'Afriza',
                'no_ktp' => '0000000000000000',
                'tanggal_masuk' => null,
                'masa_berakhir_sewa' => null,
                'status_sewa' => 'Aktif',
                'no_hp' => '081290621907',
                'email' => '',
                'pekerjaan' => 'Karyawan',
                'pic_emergency' => 'Bu Erwan',
                'durasi_bayar_terakhir' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kamar_id' => $kamarIds[14],
                'nama_lengkap' => 'Leli',
                'no_ktp' => '0000000000000000',
                'tanggal_masuk' => '2025-08-25',
                'masa_berakhir_sewa' => '2026-08-26',
                'status_sewa' => 'Aktif',
                'no_hp' => '0882000969633',
                'email' => '',
                'pekerjaan' => 'Pedagang',
                'pic_emergency' => '',
                'durasi_bayar_terakhir' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kamar_id' => $kamarIds[15],
                'nama_lengkap' => 'Lisa / Shandy',
                'no_ktp' => '0000000000000000',
                'tanggal_masuk' => null,
                'masa_berakhir_sewa' => null,
                'status_sewa' => 'Aktif',
                'no_hp' => '089506868683',
                'email' => '',
                'pekerjaan' => 'Pedagang',
                'pic_emergency' => '',
                'durasi_bayar_terakhir' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kamar_id' => $kamarIds[16],
                'nama_lengkap' => 'Abang',
                'no_ktp' => '0000000000000000',
                'tanggal_masuk' => null,
                'masa_berakhir_sewa' => '2025-12-31',
                'status_sewa' => 'Nonaktif',
                'no_hp' => '082388759738',
                'email' => '',
                'pekerjaan' => 'Pedagang',
                'pic_emergency' => '',
                'durasi_bayar_terakhir' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kamar_id' => $kamarIds[17],
                'nama_lengkap' => 'Leni Lestari Rahayu',
                'no_ktp' => '32103137110870002',
                'tanggal_masuk' => '2025-06-09',
                'masa_berakhir_sewa' => '2025-08-02',
                'status_sewa' => 'Nonaktif',
                'no_hp' => '085215180680',
                'email' => '',
                'pekerjaan' => 'Pedagang',
                'pic_emergency' => '',
                'durasi_bayar_terakhir' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kamar_id' => $kamarIds[18],
                'nama_lengkap' => 'Rizal Slamet',
                'no_ktp' => '0000000000000000',
                'tanggal_masuk' => '2025-06-05',
                'masa_berakhir_sewa' => '2025-08-01',
                'status_sewa' => 'Nonaktif',
                'no_hp' => '085921351666',
                'email' => '',
                'pekerjaan' => 'Kontraktor',
                'pic_emergency' => '',
                'durasi_bayar_terakhir' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kamar_id' => $kamarIds[19],
                'nama_lengkap' => 'Mujahid Fathurrahman',
                'no_ktp' => '3207100606970001',
                'tanggal_masuk' => '2022-11-09',
                'masa_berakhir_sewa' => null,
                'status_sewa' => 'Nonaktif',
                'no_hp' => '083825906020',
                'email' => '',
                'pekerjaan' => 'Farmasi',
                'pic_emergency' => '',
                'durasi_bayar_terakhir' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kamar_id' => $kamarIds[20],
                'nama_lengkap' => 'Rahmat Nur Jaelani',
                'no_ktp' => '31750601019810003',
                'tanggal_masuk' => '2025-06-20',
                'masa_berakhir_sewa' => '2025-06-22',
                'status_sewa' => 'Nonaktif',
                'no_hp' => '082241478109',
                'email' => '',
                'pekerjaan' => 'Ayam Geprek',
                'pic_emergency' => '',
                'durasi_bayar_terakhir' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kamar_id' => $kamarIds[21],
                'nama_lengkap' => 'Halimah Sadiyah',
                'no_ktp' => '0000000000000000',
                'tanggal_masuk' => '2025-06-28',
                'masa_berakhir_sewa' => '2025-07-02',
                'status_sewa' => 'Nonaktif',
                'no_hp' => '087770022602',
                'email' => '',
                'pekerjaan' => '',
                'pic_emergency' => '',
                'durasi_bayar_terakhir' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kamar_id' => $kamarIds[22],
                'nama_lengkap' => 'Neneng Suryati',
                'no_ktp' => '3205014610880002',
                'tanggal_masuk' => '2025-06-02',
                'masa_berakhir_sewa' => null,
                'status_sewa' => 'Nonaktif',
                'no_hp' => '',
                'email' => '',
                'pekerjaan' => 'Karyawan Swasta',
                'pic_emergency' => '',
                'durasi_bayar_terakhir' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kamar_id' => $kamarIds[0], // Mulai ulangi dari ID 1
                'nama_lengkap' => 'Misno',
                'no_ktp' => '3304110903890002',
                'tanggal_masuk' => '2025-06-30',
                'masa_berakhir_sewa' => '2025-07-15',
                'status_sewa' => 'Nonaktif',
                'no_hp' => '081131153189',
                'email' => '',
                'pekerjaan' => 'Karyawan PT Mitosa Indonesia',
                'pic_emergency' => '',
                'durasi_bayar_terakhir' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kamar_id' => $kamarIds[1],
                'nama_lengkap' => 'Juli Rahardi',
                'no_ktp' => '3302240507940003',
                'tanggal_masuk' => '2025-06-30',
                'masa_berakhir_sewa' => '2025-07-15',
                'status_sewa' => 'Nonaktif',
                'no_hp' => '',
                'email' => '',
                'pekerjaan' => 'Karyawan PT Mitosa Indonesi',
                'pic_emergency' => '',
                'durasi_bayar_terakhir' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kamar_id' => $kamarIds[22],
                'nama_lengkap' => 'Nanang Budi Sopandi',
                'no_ktp' => '3205012108800080',
                'tanggal_masuk' => '2023-10-07',
                'masa_berakhir_sewa' => '2025-07-15',
                'status_sewa' => 'Aktif',
                'no_hp' => '082129639902',
                'email' => '',
                'pekerjaan' => 'Installatir - Internet XL',
                'pic_emergency' => '',
                'durasi_bayar_terakhir' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'kamar_id' => $kamarIds[0], // Mulai ulangi dari ID 1
                'nama_lengkap' => 'Kurnia Wiyati Pradwi',
                'no_ktp' => '0000000000000000',
                'tanggal_masuk' => '2025-04-14',
                'masa_berakhir_sewa' => '2025-07-14',
                'status_sewa' => 'Nonaktif',
                'no_hp' => '089502644147',
                'email' => '',
                'pekerjaan' => 'Karyawan',
                'pic_emergency' => 'Yuda Mulya Saputr',
                'durasi_bayar_terakhir' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];
        // Pastikan penghuni Nonaktif memiliki kamar_id null
        foreach ($penghuni as &$p) {
            if ($p['status_sewa'] === 'Nonaktif') {
                $p['kamar_id'] = null;
            }
        }
        unset($p);

        DB::table('penghunis')->insert($penghuni);
    }
}
