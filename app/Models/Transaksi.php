<?php

namespace App\Models;

// 🎯 KOREKSI KRITIS: Tambahkan import ini!
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Penghuni; // Pastikan ini juga ada

class Transaksi extends Model
{
    use HasFactory; // Baris 7 yang menyebabkan error

    // Untuk menghindari Mass Assignment Error
    protected $guarded = ['id'];

    public function penghuni()
    {
        return $this->belongsTo(Penghuni::class);
    }

    public function kamar()
    {
        // Transaksi berhubungan ke Kamar melalui kolom kamar_id
        return $this->belongsTo(Kamar::class, 'kamar_id');
    }
}
