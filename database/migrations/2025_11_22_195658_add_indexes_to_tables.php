<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('penghunis', function (Blueprint $table) {
            $table->index('status_sewa');
            $table->index('kamar_id');
        });

        Schema::table('kamars', function (Blueprint $table) {
            $table->index('lantai');
            // is_available biasanya boolean, index mungkin kurang efektif jika kardinalitas rendah, tapi ok.
        });

        Schema::table('transaksis', function (Blueprint $table) {
            $table->index('tipe_transaksi');
            $table->index('tanggal_transaksi');
            $table->index('penghuni_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::table('penghunis', function (Blueprint $table) {
                $table->dropIndex(['status_sewa']);
                $table->dropIndex(['kamar_id']);
            });
        } catch (\Throwable $e) {
            // Index might not exist, ignore
        }

        try {
            Schema::table('kamars', function (Blueprint $table) {
                $table->dropIndex(['lantai']);
            });
        } catch (\Throwable $e) {
            // Index might not exist, ignore
        }

        try {
            Schema::table('transaksis', function (Blueprint $table) {
                $table->dropIndex(['tipe_transaksi']);
                $table->dropIndex(['tanggal_transaksi']);
                $table->dropIndex(['penghuni_id']);
            });
        } catch (\Throwable $e) {
            // Index might not exist, ignore
        }
    }
};
