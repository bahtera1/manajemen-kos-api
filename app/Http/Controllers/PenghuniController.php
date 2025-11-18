<?php

namespace App\Http\Controllers;

use App\Models\Penghuni;
use App\Models\Kamar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;


class PenghuniController extends Controller
{
    // READ ALL: Mengambil daftar semua penghuni
    public function index()
    {
        $penghunis = Penghuni::with('kamar')
            ->orderBy('status_sewa', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json(['data' => $penghunis], 200);
    }

    // Fungsi Pembantu untuk menghitung tanggal akhir sewa secara fleksibel
    private function calculateEndDate(Carbon $startDate, $duration, $unit)
    {
        $endDate = $startDate->copy();

        switch ($unit) {
            case 'day':
                $endDate->addDays($duration);
                break;
            case 'week':
                $endDate->addWeeks($duration);
                break;
            case 'month':
                $endDate->addMonths($duration);
                break;
            case 'year':
                $endDate->addYears($duration);
                break;
            default:
                // Fallback default
                $endDate->addMonths($duration);
        }
        return $endDate->toDateString();
    }

    // CREATE: Menyimpan data penghuni baru
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kamar_id' => 'required|exists:kamars,id',
            'nama_lengkap' => 'required|string|max:255',
            'no_ktp' => 'required|string|max:50|unique:penghunis,no_ktp',
            'tanggal_masuk' => 'required|date',
            'pic_emergency' => 'required|string|max:255',
            'no_hp' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'pekerjaan' => 'nullable|string|max:100',
            'catatan' => 'nullable|string',
            'initial_duration' => 'nullable|integer|min:1',
            'duration_unit' => 'required|in:day,week,month,year',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $kamar = Kamar::find($request->kamar_id);
        if (!$kamar->is_available) {
            return response()->json(['message' => 'Gagal. Kamar ini sudah terisi.'], 409);
        }

        $duration = $request->initial_duration ?? 1;
        $unit = $request->duration_unit;

        $startDate = Carbon::parse($request->tanggal_masuk);
        $initialEndDate = $this->calculateEndDate($startDate, $duration, $unit);

        $penghuni = Penghuni::create($request->all() + [
            'status_sewa' => 'Aktif',
            'masa_berakhir_sewa' => $initialEndDate,
            'durasi_bayar_terakhir' => $duration,
            'unit_bayar_terakhir' => $unit,
        ]);

        $kamar->update(['is_available' => false]);

        return response()->json([
            'message' => 'Penghuni berhasil ditambahkan.',
            'data' => $penghuni->load('kamar')
        ], 201);
    }

    // READ ONE: Menampilkan satu penghuni
    public function show(Penghuni $penghuni)
    {
        return response()->json(['data' => $penghuni->load('kamar')], 200);
    }

    // 🔧 UPDATE: Memperbarui data penghuni (DENGAN RECALCULATE)
    public function update(Request $request, Penghuni $penghuni)
    {
        $validator = Validator::make($request->all(), [
            'no_ktp' => 'required|string|max:50|unique:penghunis,no_ktp,' . $penghuni->id,
            'nama_lengkap' => 'required|string|max:255',
            'no_hp' => 'required|string|max:50',
            'pic_emergency' => 'required|string|max:255',
            'tanggal_masuk' => 'required|date',
            'email' => 'nullable|email|max:255',
            'kamar_id' => 'required|exists:kamars,id',
            'pekerjaan' => 'nullable|string|max:100',
            'catatan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $oldKamarId = $penghuni->kamar_id;
        $newKamarId = $request->kamar_id;

        // 🔧 CEK: Apakah tanggal_masuk berubah?
        $tanggalMasukLama = $penghuni->tanggal_masuk;
        $tanggalMasukBaru = $request->tanggal_masuk;
        $tanggalMasukBerubah = $tanggalMasukLama !== $tanggalMasukBaru;

        // 1. Logic Pindah Kamar
        if ($oldKamarId != $newKamarId) {
            $newKamar = Kamar::find($newKamarId);

            if ($newKamar->is_available === false && $newKamarId != $oldKamarId) {
                return response()->json(['message' => 'Kamar tujuan sudah terisi oleh penghuni lain.'], 409);
            }

            $oldKamar = Kamar::find($oldKamarId);
            if ($oldKamar) {
                $oldKamar->update(['is_available' => true]);
            }

            $newKamar->update(['is_available' => false]);
        }

        // 2. Update data penghuni
        $penghuni->update($request->except(['status_sewa', 'masa_berakhir_sewa', 'durasi_bayar_terakhir', 'unit_bayar_terakhir']));

        // 🔧 3. RECALCULATE masa_berakhir_sewa jika tanggal_masuk berubah DAN status Aktif
        if ($tanggalMasukBerubah && $penghuni->status_sewa === 'Aktif') {
            $durasiTerakhir = $penghuni->durasi_bayar_terakhir ?? 1;
            $unitTerakhir = $penghuni->unit_bayar_terakhir ?? 'month';

            $startDate = Carbon::parse($request->tanggal_masuk);
            $masaBerakhirBaru = $this->calculateEndDate($startDate, $durasiTerakhir, $unitTerakhir);

            $penghuni->update([
                'masa_berakhir_sewa' => $masaBerakhirBaru
            ]);

            \Log::info('Recalculated masa_berakhir_sewa:', [
                'penghuni_id' => $penghuni->id,
                'tanggal_masuk_lama' => $tanggalMasukLama,
                'tanggal_masuk_baru' => $tanggalMasukBaru,
                'durasi' => $durasiTerakhir,
                'unit' => $unitTerakhir,
                'masa_berakhir_baru' => $masaBerakhirBaru
            ]);
        }

        return response()->json([
            'message' => 'Data penghuni dan kamar berhasil diperbarui.',
            'data' => $penghuni->fresh(['kamar'])
        ], 200);
    }

    // PAYMENT: Mencatat pembayaran dan extend masa sewa
    public function recordPayment(Request $request, $penghuniId)
    {
        // 1. 🚨 PERBAIKAN VALIDASI: Gunakan 'duration' dan 'unit' baru
        $validator = Validator::make($request->all(), [
            'duration' => 'required|integer|min:1',
            'unit' => 'required|in:day,week,month,year',
            'metode_pembayaran' => 'nullable|string',
            // Anda mungkin perlu menambahkan validasi untuk mencatat Transaksi di sini
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $penghuni = Penghuni::findOrFail($penghuniId);

        if ($penghuni->status_sewa !== 'Aktif') {
            return response()->json([
                'message' => 'Tidak dapat mencatat pembayaran untuk penghuni yang tidak aktif.'
            ], 400);
        }

        $duration = $request->duration; // ✅ Ambil durasi angka
        $unit = $request->unit;         // ✅ Ambil unit waktu (day, month, etc.)

        // Extend dari masa berakhir sewa yang ada
        $currentEnd = $penghuni->masa_berakhir_sewa
            ? Carbon::parse($penghuni->masa_berakhir_sewa)
            : Carbon::parse($penghuni->tanggal_masuk);

        // 2. ✅ LOGIKA: Gunakan calculateEndDate dengan unit yang diterima
        $newEnd = $this->calculateEndDate($currentEnd, $duration, $unit);

        // 3. ✅ PERBARUI: Simpan durasi dan unit yang fleksibel ke database
        $penghuni->update([
            'durasi_bayar_terakhir' => $duration,
            'unit_bayar_terakhir' => $unit,
            'masa_berakhir_sewa' => $newEnd,
        ]);

        // CATATAN: Ini adalah tempat yang tepat untuk membuat entri Transaksi Pemasukan
        // Transaksi::create(['jumlah' => $jumlah, 'penghuni_id' => $penghuniId, ...]);

        \Log::info('Payment recorded:', [
            'penghuni_id' => $penghuni->id,
            'duration' => $duration,
            'unit' => $unit,
            'new_end_date' => $newEnd
        ]);

        return response()->json([
            'message' => 'Pembayaran berhasil dicatat.',
            'data' => $penghuni->fresh(['kamar'])
        ], 200);
    }

    // CHECKOUT: Endpoint khusus untuk menonaktifkan penghuni
    public function checkout(Request $request, Penghuni $penghuni)
    {
        if ($penghuni->status_sewa === 'Nonaktif') {
            return response()->json(['message' => 'Penghuni ini sudah nonaktif.'], 400);
        }

        $validator = Validator::make($request->all(), [
            'tanggal_keluar' => 'required|date|after_or_equal:tanggal_masuk',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $penghuni->update([
            'status_sewa' => 'Nonaktif',
            'tanggal_keluar' => $request->tanggal_keluar ?? Carbon::today(),
        ]);

        // Update Kamar menjadi tersedia
        $kamar = Kamar::find($penghuni->kamar_id);
        if ($kamar) {
            $kamar->update(['is_available' => true]);
        }

        return response()->json(['message' => 'Penghuni berhasil di-checkout.'], 200);
    }

    // DELETE: Hapus data penghuni
    public function destroy(Penghuni $penghuni)
    {
        if ($penghuni->transaksis()->exists()) {
            return response()->json(['message' => 'Penghuni tidak dapat dihapus karena sudah memiliki riwayat transaksi. Harap nonaktifkan saja.'], 409);
        }

        if ($penghuni->status_sewa === 'Aktif') {
            $kamar = Kamar::find($penghuni->kamar_id);
            if ($kamar) {
                $kamar->update(['is_available' => true]);
            }
        }

        $penghuni->delete();

        return response()->json(['message' => 'Penghuni berhasil dihapus.'], 200);
    }

    // REASSIGN: Mengaktifkan kembali penghuni ke kamar baru/lama
    public function reassign(Request $request, Penghuni $penghuni)
    {
        $request->validate([
            'new_kamar_id' => 'required|exists:kamars,id',
            'tanggal_masuk_baru' => 'required|date',
            'initial_duration' => 'required|integer|min:1',
            'duration_unit' => 'required|in:day,week,month,year',
        ]);

        if ($penghuni->status_sewa === 'Aktif') {
            return response()->json(['message' => 'Penghuni sudah aktif. Gunakan tombol Edit atau Bayar.'], 400);
        }

        $newKamar = Kamar::find($request->new_kamar_id);
        if (!$newKamar || !$newKamar->is_available) {
            return response()->json(['message' => 'Kamar tujuan tidak tersedia.'], 409);
        }

        $duration = $request->initial_duration;
        $unit = $request->duration_unit;
        $newStartDate = Carbon::parse($request->tanggal_masuk_baru);

        $newEndDate = $this->calculateEndDate($newStartDate, $duration, $unit);

        $penghuni->update([
            'kamar_id' => $request->new_kamar_id,
            'tanggal_masuk' => $request->tanggal_masuk_baru,
            'tanggal_keluar' => null,
            'status_sewa' => 'Aktif',
            'masa_berakhir_sewa' => $newEndDate,
            'durasi_bayar_terakhir' => $duration,
            'unit_bayar_terakhir' => $unit,
        ]);

        $newKamar->update(['is_available' => false]);

        return response()->json([
            'message' => "Penghuni berhasil diaktifkan kembali dan dipindahkan ke kamar {$newKamar->nama_kamar}.",
            'data' => $penghuni->fresh(['kamar'])
        ], 200);
    }

    // ENDPOINT UNTUK DETAIL PENGHUNI DAN DAFTAR TAGIHAN
    public function getDetailAndTagihans($id)
    {
        $penghuni = Penghuni::with([
            'kamar',
            'tagihans' => function ($query) {
                $query->orderBy('jatuh_tempo', 'desc');
            }
        ])->findOrFail($id);

        return response()->json([
            'data' => $penghuni
        ], 200);
    }
}
