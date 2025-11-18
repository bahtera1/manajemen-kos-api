<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KamarSeeder extends Seeder
{
    public function run(): void
    {
        // 🚨 SET DATA FASILITAS LENGKAP (sesuai image_b26727.png)
        $fasilitasSetFull = [
            'Listrik',
            'Air Bersih',
            'WiFi',
            'Kasur',
            'Lemari',
            'Kompor Gas',
            'Tabung Gas',
            'TV',
            'Kulkas',
            'Water Heater',
            'Kipas Angin',
            'Meja',
            'Kursi',
            'Lain-Lain',
            'Motor',
            'Mobil',
            'Rental Mobil',
            'Rental Motor'
        ];

        // Set data yang lebih kecil untuk variasi
        $fasilitasSet1 = ['Listrik', 'Air Bersih', 'WiFi', 'Kasur', 'Lemari', 'Motor'];
        $fasilitasSet2 = ['Listrik', 'Air Bersih', 'WiFi', 'Kasur', 'TV', 'Kulkas', 'Mobil'];
        $fasilitasSet3 = ['Listrik', 'Air Bersih', 'Kipas Angin', 'Meja', 'Motor'];


        DB::table('kamars')->insert([
            // 🚨 KAMAR 1: MENGGUNAKAN FASILITAS FULL
            [
                'nama_kamar' => 'A-01',
                'blok' => 'A',
                'lantai' => 1,
                'harga_bulanan' => 650000,
                'is_available' => false,
                'luas_kamar' => '3x3',
                'type' => 1,
                // Gunakan set fasilitas lengkap
                'deskripsi_fasilitas' => json_encode($fasilitasSetFull),
                'created_at' => now(),
                'updated_at' => now()
            ],
            // Kamar 2: Kamar B-02 (Set 2)
            [
                'nama_kamar' => 'B-02',
                'blok' => 'B',
                'lantai' => 1,
                'harga_bulanan' => 700000,
                'is_available' => false,
                'luas_kamar' => '3x4',
                'type' => 2,
                'deskripsi_fasilitas' => json_encode($fasilitasSet2),
                'created_at' => now(),
                'updated_at' => now()
            ],
            // Kamar 3: Kamar C-03 (Set 3)
            [
                'nama_kamar' => 'C-03',
                'blok' => 'C',
                'lantai' => 2,
                'harga_bulanan' => 800000,
                'is_available' => false,
                'luas_kamar' => '4x4',
                'type' => 2,
                'deskripsi_fasilitas' => json_encode($fasilitasSet3),
                'created_at' => now(),
                'updated_at' => now()
            ],
            // Kamar 4: Kamar D-04 (Set 1)
            [
                'nama_kamar' => 'D-04',
                'blok' => 'D',
                'lantai' => 2,
                'harga_bulanan' => 600000,
                'is_available' => false,
                'luas_kamar' => '3x3',
                'type' => 1,
                'deskripsi_fasilitas' => json_encode($fasilitasSet1),
                'created_at' => now(),
                'updated_at' => now()
            ],
            // Kamar 5: Kamar E-05 (Set 2)
            [
                'nama_kamar' => 'E-05',
                'blok' => 'E',
                'lantai' => 3,
                'harga_bulanan' => 950000,
                'is_available' => false,
                'luas_kamar' => '5x4',
                'type' => 2,
                'deskripsi_fasilitas' => json_encode($fasilitasSet2),
                'created_at' => now(),
                'updated_at' => now()
            ],

            // 5 Kamar Kosong (Data sisa tetap sama)
            ['nama_kamar' => 'A-06', 'blok' => 'A', 'lantai' => 1, 'harga_bulanan' => 650000, 'is_available' => true, 'luas_kamar' => '3x3', 'type' => 1, 'deskripsi_fasilitas' => null, 'created_at' => now(), 'updated_at' => now()],
            ['nama_kamar' => 'B-07', 'blok' => 'B', 'lantai' => 1, 'harga_bulanan' => 700000, 'is_available' => true, 'luas_kamar' => '3x4', 'type' => 1, 'deskripsi_fasilitas' => null, 'created_at' => now(), 'updated_at' => now()],
            ['nama_kamar' => 'C-08', 'blok' => 'C', 'lantai' => 2, 'harga_bulanan' => 800000, 'is_available' => true, 'luas_kamar' => '4x4', 'type' => 2, 'deskripsi_fasilitas' => null, 'created_at' => now(), 'updated_at' => now()],
            ['nama_kamar' => 'D-09', 'blok' => 'D', 'lantai' => 2, 'harga_bulanan' => 600000, 'is_available' => true, 'luas_kamar' => '3x3', 'type' => 1, 'deskripsi_fasilitas' => null, 'created_at' => now(), 'updated_at' => now()],
            ['nama_kamar' => 'E-10', 'blok' => 'E', 'lantai' => 3, 'harga_bulanan' => 950000, 'is_available' => true, 'luas_kamar' => '5x4', 'type' => 2, 'deskripsi_fasilitas' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
