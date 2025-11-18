<?php

// app/Models/Tagihan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    use HasFactory;

    protected $fillable = [
        'penghuni_id',
        'kamar_id',
        'transaksi_id',
        'nomor_tagihan',
        'deskripsi',
        'jumlah',
        'jatuh_tempo',
        'status',
    ];

    // Relasi
    public function penghuni()
    {
        return $this->belongsTo(Penghuni::class);
    }

    public function kamar()
    {
        return $this->belongsTo(Kamar::class);
    }

    // Relasi ke transaksi (optional, hanya jika sudah dibayar)
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }
}
