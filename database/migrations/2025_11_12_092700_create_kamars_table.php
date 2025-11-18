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
        Schema::create('kamars', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kamar', 100)->unique();
            $table->decimal('harga_bulanan', 10, 2);
            $table->string('luas_kamar', 50);
            $table->boolean('is_available')->default(true);
            $table->text('deskripsi_fasilitas')->nullable();
            $table->string('blok', 10);
            $table->unsignedSmallInteger('lantai');
            $table->unsignedSmallInteger('type');
            $table->index(['blok', 'lantai']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kamars');
    }
};
