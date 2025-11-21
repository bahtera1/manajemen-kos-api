<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KamarSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('kamars')->insert([
            // A-21
            [
                'nama_kamar' => 'A-21',
                'blok' => 'A',
                'lantai' => 2,
                'harga_bulanan' => 550000,
                'is_available' => true, // Kosong
                'luas_kamar' => '5x4 (18m²)',
                'type' => 1,
                'deskripsi_fasilitas' => json_encode(['Listrik', 'Air Bersih', 'WiFi', 'Kasur', 'Lemari']),
                'created_at' => now(),
                'updated_at' => now()
            ],

            // A-14
            [
                'nama_kamar' => 'A-14',
                'blok' => 'A',
                'lantai' => 1,
                'harga_bulanan' => 600000,
                'is_available' => true, // Terisi
                'luas_kamar' => '10x4 (40m²)',
                'type' => 3,
                'deskripsi_fasilitas' => json_encode(['Air Bersih', 'WiFi']),
                'created_at' => now(),
                'updated_at' => now()
            ],

            // A-15
            [
                'nama_kamar' => 'A-15',
                'blok' => 'A',
                'lantai' => 1,
                'harga_bulanan' => 700000,
                'is_available' => true, // Terisi
                'luas_kamar' => '10x4 (40m²)',
                'type' => 3,
                'deskripsi_fasilitas' => json_encode(['Listrik', 'Air Bersih', 'WiFi', 'Kasur', 'Lemari', 'Kulkas', 'Meja', 'Kursi']),
                'created_at' => now(),
                'updated_at' => now()
            ],

            // B-21
            [
                'nama_kamar' => 'B-21',
                'blok' => 'B',
                'lantai' => 2,
                'harga_bulanan' => 700000,
                'is_available' => true, // Terisi
                'luas_kamar' => '6x4 (18m²)',
                'type' => 3,
                'deskripsi_fasilitas' => json_encode(['Listrik', 'Air Bersih', 'WiFi', 'Kasur', 'Lemari', 'Meja']),
                'created_at' => now(),
                'updated_at' => now()
            ],

            // B-22
            [
                'nama_kamar' => 'B-22',
                'blok' => 'B',
                'lantai' => 2,
                'harga_bulanan' => 700000,
                'is_available' => true, // Terisi
                'luas_kamar' => '6x4 (21m²)',
                'type' => 0,
                'deskripsi_fasilitas' => json_encode(['Listrik', 'Air Bersih', 'WiFi', 'Kasur', 'Lemari', 'Meja']),
                'created_at' => now(),
                'updated_at' => now()
            ],

            // B-23
            [
                'nama_kamar' => 'B-23',
                'blok' => 'B',
                'lantai' => 2,
                'harga_bulanan' => 650000,
                'is_available' => true, // Terisi
                'luas_kamar' => '6x4 (21m²)',
                'type' => 1,
                'deskripsi_fasilitas' => json_encode(['Listrik', 'Air Bersih', 'WiFi', 'Kasur', 'Lemari', 'Meja']),
                'created_at' => now(),
                'updated_at' => now()
            ],

            // B-24
            [
                'nama_kamar' => 'B-24',
                'blok' => 'B',
                'lantai' => 2,
                'harga_bulanan' => 650000,
                'is_available' => true, // Terisi
                'luas_kamar' => '6x4 (21m²)',
                'type' => 0,
                'deskripsi_fasilitas' => json_encode(['Listrik', 'Air Bersih', 'WiFi', 'Kasur', 'Lemari', 'Meja']),
                'created_at' => now(),
                'updated_at' => now()
            ],

            // B-25
            [
                'nama_kamar' => 'B-25',
                'blok' => 'B',
                'lantai' => 2,
                'harga_bulanan' => 600000,
                'is_available' => true, // Terisi
                'luas_kamar' => '6x4 (21m²)',
                'type' => 2,
                'deskripsi_fasilitas' => json_encode(['Listrik', 'Air Bersih', 'WiFi', 'Kasur', 'Lemari', 'Meja']),
                'created_at' => now(),
                'updated_at' => now()
            ],

            // B-26
            [
                'nama_kamar' => 'B-26',
                'blok' => 'B',
                'lantai' => 2,
                'harga_bulanan' => 0,
                'is_available' => true, // Terisi
                'luas_kamar' => '6x4 (21m²)',
                'type' => 4,
                'deskripsi_fasilitas' => json_encode(['Listrik', 'Air Bersih', 'WiFi', 'Kasur', 'Lemari', 'Meja']),
                'created_at' => now(),
                'updated_at' => now()
            ],

            // B-27
            [
                'nama_kamar' => 'B-27',
                'blok' => 'B',
                'lantai' => 2,
                'harga_bulanan' => 0,
                'is_available' => true, // Kosong
                'luas_kamar' => 'N/A',
                'type' => 0,
                'deskripsi_fasilitas' => json_encode(['Listrik', 'Air Bersih', 'WiFi', 'Kasur', 'Lemari']),
                'created_at' => now(),
                'updated_at' => now()
            ],

            // B-11
            [
                'nama_kamar' => 'B-11',
                'blok' => 'B',
                'lantai' => 1,
                'harga_bulanan' => 550000,
                'is_available' => true, // Kosong
                'luas_kamar' => '7x4 (28m²)',
                'type' => 1,
                'deskripsi_fasilitas' => json_encode(['Listrik', 'Air Bersih', 'WiFi', 'Kasur', 'Lemari']),
                'created_at' => now(),
                'updated_at' => now()
            ],

            // A-22
            [
                'nama_kamar' => 'A-22',
                'blok' => 'A',
                'lantai' => 2,
                'harga_bulanan' => 600000,
                'is_available' => true, // Terisi
                'luas_kamar' => '5x4 (18m²)',
                'type' => 1,
                'deskripsi_fasilitas' => json_encode(['Listrik', 'Air Bersih', 'WiFi', 'Kasur', 'Lemari']),
                'created_at' => now(),
                'updated_at' => now()
            ],

            // B-12
            [
                'nama_kamar' => 'B-12',
                'blok' => 'B',
                'lantai' => 1,
                'harga_bulanan' => 700000,
                'is_available' => true, // Terisi
                'luas_kamar' => '7x4 (24m²)',
                'type' => 3,
                'deskripsi_fasilitas' => json_encode(['Listrik', 'Air Bersih', 'WiFi', 'Kasur', 'Lemari', 'Meja']),
                'created_at' => now(),
                'updated_at' => now()
            ],

            // B-13
            [
                'nama_kamar' => 'B-13',
                'blok' => 'B',
                'lantai' => 1,
                'harga_bulanan' => 750000,
                'is_available' => true, // Terisi
                'luas_kamar' => '7x4 (24m²)',
                'type' => 3,
                'deskripsi_fasilitas' => json_encode(['Listrik', 'Air Bersih', 'WiFi', 'Kasur', 'Lemari', 'Kompor Gas', 'Kursi']),
                'created_at' => now(),
                'updated_at' => now()
            ],

            // B-14
            [
                'nama_kamar' => 'B-14',
                'blok' => 'B',
                'lantai' => 1,
                'harga_bulanan' => 500000,
                'is_available' => true, // Terisi
                'luas_kamar' => '4x4 (14m²)',
                'type' => 1,
                'deskripsi_fasilitas' => json_encode(['Listrik', 'Air Bersih', 'WiFi', 'Kasur', 'Lemari']),
                'created_at' => now(),
                'updated_at' => now()
            ],

            // B-15
            [
                'nama_kamar' => 'B-15',
                'blok' => 'B',
                'lantai' => 1,
                'harga_bulanan' => 700000,
                'is_available' => true, // Terisi
                'luas_kamar' => '4x4',
                'type' => 3,
                'deskripsi_fasilitas' => json_encode(['Listrik', 'Air Bersih', 'WiFi', 'Kasur', 'Lemari', 'Meja', 'Kursi']),
                'created_at' => now(),
                'updated_at' => now()
            ],

            // C-11
            [
                'nama_kamar' => 'C-11',
                'blok' => 'C',
                'lantai' => 1,
                'harga_bulanan' => 900000,
                'is_available' => true, // Terisi
                'luas_kamar' => 'N/A',
                'type' => 3,
                'deskripsi_fasilitas' => json_encode(['Listrik', 'Air Bersih', 'WiFi', 'Kasur', 'Lemari', 'TV', 'Kursi']),
                'created_at' => now(),
                'updated_at' => now()
            ],

            // C-12
            [
                'nama_kamar' => 'C-12',
                'blok' => 'C',
                'lantai' => 1,
                'harga_bulanan' => 600000,
                'is_available' => true, // Terisi
                'luas_kamar' => '6x3 (18m²)',
                'type' => 1,
                'deskripsi_fasilitas' => json_encode(['Listrik', 'Air Bersih', 'WiFi', 'Kasur', 'Lemari', 'Kulkas', 'Meja']),
                'created_at' => now(),
                'updated_at' => now()
            ],

            // C-13
            [
                'nama_kamar' => 'C-13',
                'blok' => 'C',
                'lantai' => 1,
                'harga_bulanan' => 1100000,
                'is_available' => true, // Terisi
                'luas_kamar' => '9x5 (45m²)',
                'type' => 2,
                'deskripsi_fasilitas' => json_encode(['Listrik', 'Air Bersih', 'WiFi', 'Kasur', 'Lemari', 'Kompor Gas', 'Tabung Gas', 'Kursi']),
                'created_at' => now(),
                'updated_at' => now()
            ],

            // C-14
            [
                'nama_kamar' => 'C-14',
                'blok' => 'C',
                'lantai' => 1,
                'harga_bulanan' => 800000,
                'is_available' => true, // Terisi
                'luas_kamar' => '5x3 (15m²)',
                'type' => 0,
                'deskripsi_fasilitas' => json_encode(['Listrik', 'Air Bersih', 'WiFi', 'Kasur', 'Lemari', 'Tabung Gas', 'Water Heater', 'Meja', 'Kursi']),
                'created_at' => now(),
                'updated_at' => now()
            ],

            // A-23
            [
                'nama_kamar' => 'A-23',
                'blok' => 'A',
                'lantai' => 2,
                'harga_bulanan' => 650000,
                'is_available' => true, // Terisi
                'luas_kamar' => '5x4 (18m²)',
                'type' => 2,
                'deskripsi_fasilitas' => json_encode(['Listrik', 'Air Bersih', 'WiFi', 'Kasur', 'Lemari']),
                'created_at' => now(),
                'updated_at' => now()
            ],

            // A-11
            [
                'nama_kamar' => 'A-11',
                'blok' => 'A',
                'lantai' => 1,
                'harga_bulanan' => 650000,
                'is_available' => true, // Terisi
                'luas_kamar' => '5x4 (18m²)',
                'type' => 2,
                'deskripsi_fasilitas' => json_encode(['Listrik', 'Air Bersih', 'WiFi', 'Kasur', 'Lemari']),
                'created_at' => now(),
                'updated_at' => now()
            ],

            // A-12
            [
                'nama_kamar' => 'A-12',
                'blok' => 'A',
                'lantai' => 1,
                'harga_bulanan' => 550000,
                'is_available' => true, // Terisi
                'luas_kamar' => '5x4 (18m²)',
                'type' => 1,
                'deskripsi_fasilitas' => json_encode(['Listrik', 'Air Bersih', 'WiFi', 'Kasur', 'Lemari']),
                'created_at' => now(),
                'updated_at' => now()
            ],

            // A-13
            [
                'nama_kamar' => 'A-13',
                'blok' => 'A',
                'lantai' => 1,
                'harga_bulanan' => 600000,
                'is_available' => true, // Terisi
                'luas_kamar' => '5x4 (18m²)',
                'type' => 1,
                'deskripsi_fasilitas' => json_encode(['Listrik', 'Air Bersih', 'WiFi', 'Kasur', 'Lemari', 'Meja']),
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
