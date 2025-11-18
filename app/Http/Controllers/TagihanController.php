<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tagihan;
use Carbon\Carbon;
use App\Models\Kamar;
use App\Models\Penghuni;
use Illuminate\Support\Facades\Validator;

class TagihanController extends Controller
{
    private function numberToText($number)
    {
        $num = floor($number);
        if ($num >= 1000000000)
            return "Satu Milyar Lebih Rupiah";
        if ($num >= 1000000)
            return "Jutaan Rupiah";
        if ($num >= 1000)
            return "Ratusan Ribu Rupiah";
        return "Jumlah Pembayaran";
    }

    /**
     * Mengambil data Tagihan untuk keperluan Kuitansi/Invoice (bisa Lunas atau Belum Lunas).
     * Dokumen yang dicetak sama, hanya statusnya yang berbeda.
     */
    public function getKuitansiData($tagihanId)
    {
        try {
            // 1. Eager Loading Data
            $tagihan = Tagihan::with(['penghuni.kamar', 'transaksi'])->findOrFail($tagihanId);

            $penghuni = $tagihan->penghuni;
            $kamar = $tagihan->penghuni->kamar;
            $transaksi = $tagihan->transaksi; // Bisa null

            // Cek jika relasi Kamar atau Penghuni hilang (Pencegahan Error 500)
            if (!$kamar || !$penghuni) {
                return response()->json(['message' => 'Data relasi (kamar/penghuni) tidak ditemukan.'], 500);
            }

            // 2. Logika Penentuan Data (Toleran terhadap data Transaksi yang null)
            $tanggal_transaksi_source = $transaksi ? $transaksi->tanggal_transaksi : $tagihan->jatuh_tempo;

            $tanggal_bayar = Carbon::parse($tanggal_transaksi_source)->toDateString();
            $nomor_dokumen = $tagihan->nomor_tagihan;

            // 🚨 Perbaikan Status: Hanya izinkan 'Lunas' atau 'Belum Lunas'
            $finalStatus = $tagihan->status;
            if ($finalStatus !== 'Lunas') {
                $finalStatus = 'Belum Lunas';
            }

            // 3. 🔧 PERBAIKAN: Decode JSON deskripsi_fasilitas menjadi array
            $fasilitasTersedia = [];

            if ($kamar->deskripsi_fasilitas) {
                // Jika sudah array (dari accessor/cast), gunakan langsung
                if (is_array($kamar->deskripsi_fasilitas)) {
                    $fasilitasTersedia = $kamar->deskripsi_fasilitas;
                }
                // Jika masih string JSON, decode dulu
                else if (is_string($kamar->deskripsi_fasilitas)) {
                    $decoded = json_decode($kamar->deskripsi_fasilitas, true);
                    $fasilitasTersedia = is_array($decoded) ? $decoded : [];
                }
            }

            $fasilitasFormatted = [];
            $parkirFormatted = [];
            $PARKIR_LIST = ['Motor', 'Mobil', 'Rental Motor', 'Rental Mobil'];

            foreach ($fasilitasTersedia as $item) {
                $key = strtolower(str_replace(' ', '_', $item));
                if (in_array($item, $PARKIR_LIST)) {
                    $parkirFormatted[$key] = true;
                } else {
                    $fasilitasFormatted[$key] = true;
                }
            }

            // 4. Struktur Data Output (KuitansiData)
            $kuitansiData = [
                // Data ID & Dokumen
                'id' => $tagihan->id,
                'transaksi_id' => $transaksi ? $transaksi->id : null,
                'kamar_id' => $kamar->id,
                'nomor_kuitansi' => $tagihan->status === 'Lunas'
                    ? sprintf('019/INV/%s/%s', Carbon::parse($tanggal_bayar)->format('m'), Carbon::parse($tanggal_bayar)->format('Y'))
                    : $nomor_dokumen,
                'nomor_tagihan' => $nomor_dokumen,

                // Data Waktu & Harga
                'tanggal_bayar' => $tanggal_bayar,
                'jatuh_tempo' => $tagihan->jatuh_tempo,
                'jumlah' => (float) $tagihan->jumlah,
                'terbilang' => $this->numberToText((float) $tagihan->jumlah),
                'deskripsi' => $tagihan->deskripsi,

                // Data Penghuni & Kamar
                'nama_penghuni' => $penghuni->nama_lengkap,
                'id_penghuni' => $penghuni->id,
                'nama_kamar' => $kamar->nama_kamar,
                'tarif_sewa' => $kamar->harga_bulanan,
                'periode_sewa' => $tagihan->deskripsi,

                // Data Pembayaran
                'jatuh_tempo_berikut' => $penghuni->masa_berakhir_sewa ? Carbon::parse($penghuni->masa_berakhir_sewa)->toDateString() : 'N/A',
                'status_pembayaran' => $finalStatus,
                'metode_pembayaran' => $transaksi ? $transaksi->metode_pembayaran : 'Transfer',

                'uang_muka' => (float) ($tagihan->uang_muka ?? 0),
                'pelunasan' => (float) ($tagihan->status === 'Lunas' ? $tagihan->jumlah : ($tagihan->pelunasan ?? 0)),
                'refund' => (float) ($tagihan->refund ?? 0),
                'lain_lain' => (float) ($tagihan->lain_lain ?? 0),

                // Fasilitas yang sudah diformat
                'fasilitas' => $fasilitasFormatted,
                'parkir' => $parkirFormatted,
            ];

            return response()->json(['data' => $kuitansiData], 200);

        } catch (\Exception $e) {
            // Log error untuk debugging
            \Log::error('Error in getKuitansiData: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'message' => 'Terjadi kesalahan saat memproses data kuitansi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, Tagihan $tagihan)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:Lunas,Belum Lunas',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $newStatus = $request->status;

        // Aksi ini tidak boleh mengubah Transaksi yang sudah ada di sistem,
        // melainkan hanya status Tagihan itu sendiri.

        // CATATAN: Dalam implementasi final, jika status diubah menjadi 'Belum Lunas',
        // Anda harus menghapus Transaksi terkait yang pernah dibuat.

        $tagihan->update(['status' => $newStatus]);

        return response()->json([
            'message' => "Status tagihan ID {$tagihan->id} berhasil diubah menjadi {$newStatus}.",
            'data' => $tagihan
        ], 200);
    }
}
