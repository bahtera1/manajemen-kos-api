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
        Schema::create('penghunis', function (Blueprint $table) {
            $table->id();

            // Foreign Key
            $table->foreignId('kamar_id')->constrained('kamars')->onDelete('restrict');

            $table->string('nama_lengkap', 255);
            $table->string('no_ktp', 50)->unique();
            $table->string('no_hp', 50);
            $table->string('email', 255)->nullable();
            $table->string('pekerjaan', 100)->nullable();
            $table->string('pic_emergency', 255);
            $table->date('tanggal_masuk');
            $table->date('tanggal_keluar')->nullable();
            $table->enum('status_sewa', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->date('masa_berakhir_sewa')->nullable();
            $table->unsignedSmallInteger('durasi_bayar_terakhir')->nullable();
            $table->string('unit_bayar_terakhir', 10)->default('month');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penghunis');
    }
};
