<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate();
        DB::table('kamars')->truncate();
        DB::table('penghunis')->truncate();
        DB::table('transaksis')->truncate();
        DB::table('tagihans')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Panggil Seeder
        $this->call([
            UserSeeder::class,
            KamarSeeder::class,
            PenghuniSeeder::class,
            TransaksiSeeder::class,
            TagihanSeeder::class,
        ]);
    }
}
