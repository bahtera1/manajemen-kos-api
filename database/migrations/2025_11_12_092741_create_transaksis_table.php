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
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            // Foreign Key
            $table->foreignId('penghuni_id')->nullable()->constrained('penghunis')->onDelete('set null');

            $table->enum('tipe_transaksi', ['Pemasukan', 'Pengeluaran']);
            $table->string('kategori', 100);
            $table->text('deskripsi');
            $table->decimal('jumlah', 10, 2);
            $table->date('tanggal_transaksi');
            $table->string('url_kuitansi', 500)->nullable();
            $table->timestamps();
            $table->foreignId('kamar_id')->nullable()->constrained('kamars')->onDelete('set null');
            $table->string('metode_pembayaran', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
