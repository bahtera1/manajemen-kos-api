<?php

namespace App\Http\Controllers;

use App\Models\Penghuni;
use App\Models\Kamar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PenghuniController extends Controller
{
    /**
     * GET /api/penghunis
     * Mengambil semua penghuni dengan relasi kamar
     */
    public function index(Request $request)
    {
        // 1. Inisialisasi query
        $query = Penghuni::query();

        // 2. Terapkan Filter (sekarang filter ini bekerja pada objek $query)
        if ($request->filled('status_sewa')) {
            $query->where('status_sewa', $request->status_sewa);
        }

        // 3. Tambahkan relasi dan urutan
        $query->with('kamar:id,nama_kamar,blok,lantai,is_available')
            ->orderBy('status_sewa', 'desc')
            ->orderBy('created_at', 'desc');

        // 4. Eksekusi query HANYA SEKALI
        $penghunis = $query->get();

        Log::info('Penghuni list fetched', [
            'count' => $penghunis->count(),
            'filtered_by' => $request->status_sewa ?? 'all'
        ]);

        return response()->json(['data' => $penghunis], 200);
    }

    /**
     * POST /api/penghunis
     * Membuat penghuni baru dan assign ke kamar
     */
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
            Log::error('Validasi gagal - Penghuni:', $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Cek ketersediaan kamar
        $kamar = Kamar::findOrFail($request->kamar_id);
        if (!$kamar->is_available) {
            Log::warning('Kamar tidak tersedia:', ['kamar_id' => $kamar->id]);
            return response()->json(['message' => 'Gagal. Kamar ini sudah terisi.'], 409);
        }

        // Hitung masa berakhir sewa
        $duration = $request->initial_duration ?? 1;
        $unit = $request->duration_unit;
        $startDate = Carbon::parse($request->tanggal_masuk);
        $endDate = $this->calculateEndDate($startDate, $duration, $unit);

        // Buat penghuni baru
        $penghuni = Penghuni::create($request->all() + [
            'status_sewa' => 'Aktif',
            'masa_berakhir_sewa' => $endDate,
            'durasi_bayar_terakhir' => $duration,
            'unit_bayar_terakhir' => $unit,
        ]);

        // Update status kamar
        $kamar->update(['is_available' => false]);

        Log::info('Penghuni created:', [
            'id' => $penghuni->id,
            'nama' => $penghuni->nama_lengkap,
            'kamar' => $kamar->nama_kamar,
            'masa_berakhir' => $endDate
        ]);

        return response()->json([
            'message' => 'Penghuni berhasil ditambahkan.',
            'data' => $penghuni->load('kamar')
        ], 201);
    }

    /**
     * GET /api/penghunis/{id}
     * Detail satu penghuni
     */
    public function show(Penghuni $penghuni)
    {
        return response()->json([
            'data' => $penghuni->load('kamar:id,nama_kamar,harga_bulanan')
        ], 200);
    }

    /**
     * PUT/PATCH /api/penghunis/{id}
     * Update data penghuni (dengan logic pindah kamar & recalculate)
     */
    public function update(Request $request, Penghuni $penghuni)
    {
        $validator = Validator::make($request->all(), [
            'no_ktp' => 'required|string|max:17',
            'nama_lengkap' => 'required|string|max:150',
            'no_hp' => 'required|string|max:16',
            'pic_emergency' => 'required|string|max:150',
            'tanggal_masuk' => 'required|date',
            'email' => 'nullable|email|max:150',
            'kamar_id' => 'required|exists:kamars,id',
            'pekerjaan' => 'nullable|string|max:100',
            'catatan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            Log::error('Validasi gagal - Update Penghuni:', $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $oldKamarId = $penghuni->kamar_id;
        $newKamarId = $request->kamar_id;
        $tanggalMasukBerubah = $penghuni->tanggal_masuk !== $request->tanggal_masuk;

        // 💡 PERBAIKAN KRITIS: Jika kamar tidak berubah, JANGAN lakukan cek ketersediaan.
        if ($oldKamarId != $newKamarId) {
            $newKamar = Kamar::findOrFail($newKamarId);

            // Cek ketersediaan kamar baru.
            // Jika is_available digunakan untuk menandai terisi/kosong, ini dicek.
            if ($newKamar->penghuni()->where('status_sewa', 'Aktif')->exists()) { // Cek relasi daripada is_available
                Log::warning('Pindah kamar gagal - sudah ditempati:', ['new_kamar' => $newKamarId]);
                return response()->json(['message' => 'Kamar tujuan sudah terisi oleh penghuni aktif lain.'], 409);
            }

            // [OPSIONAL] Update is_available di kamar lama (hanya jika Anda menggunakan field ini)
            if ($oldKamarId) {
                // Hapus penanda "terisi" di kamar lama jika penghuni pindah
                Kamar::where('id', $oldKamarId)->update(['is_available' => true]);
            }

            // [OPSIONAL] Update is_available di kamar baru (hanya jika Anda menggunakan field ini)
            $newKamar->update(['is_available' => false]);

            Log::info('Penghuni pindah kamar:', [
                'penghuni_id' => $penghuni->id,
                'old_kamar' => $oldKamarId,
                'new_kamar' => $newKamarId
            ]);
        }
        // 🚨 KASUS KAMAR TIDAK BERUBAH: Blok ini tidak dijalankan, tidak ada error validasi
        // karena kamar tidak pernah berubah dan validasi ketersediaan diabaikan.


        // Update data penghuni (kecuali field otomatis)
        $penghuni->update($request->except([
            'status_sewa',
            'masa_berakhir_sewa',
            'durasi_bayar_terakhir',
            'unit_bayar_terakhir'
        ]));

        // Recalculate masa berakhir jika tanggal masuk berubah dan status Aktif
        if ($tanggalMasukBerubah && $penghuni->status_sewa === 'Aktif') {
            $duration = $penghuni->durasi_bayar_terakhir ?? 1;
            $unit = $penghuni->unit_bayar_terakhir ?? 'month';
            $newStartDate = Carbon::parse($request->tanggal_masuk);
            $newEndDate = $this->calculateEndDate($newStartDate, $duration, $unit);

            $penghuni->update(['masa_berakhir_sewa' => $newEndDate]);

            Log::info('Recalculated masa berakhir:', [
                'penghuni_id' => $penghuni->id,
                'new_end_date' => $newEndDate
            ]);
        }

        return response()->json([
            'message' => 'Data penghuni berhasil diperbarui.',
            'data' => $penghuni->fresh(['kamar'])
        ], 200);
    }

    /**
     * POST /api/penghunis/{id}/payment
     * Mencatat pembayaran dan extend masa sewa
     */
    public function recordPayment(Request $request, $penghuniId)
    {
        $validator = Validator::make($request->all(), [
            'duration' => 'required|integer|min:1',
            'unit' => 'required|in:day,week,month,year',
            'metode_pembayaran' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            Log::error('Validasi gagal - Payment:', $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $penghuni = Penghuni::findOrFail($penghuniId);

        if ($penghuni->status_sewa !== 'Aktif') {
            Log::warning('Payment gagal - status tidak aktif:', ['penghuni_id' => $penghuniId]);
            return response()->json([
                'message' => 'Tidak dapat mencatat pembayaran untuk penghuni yang tidak aktif.'
            ], 400);
        }

        $duration = $request->duration;
        $unit = $request->unit;

        // Extend dari masa berakhir sewa yang ada
        $currentEnd = $penghuni->masa_berakhir_sewa
            ? Carbon::parse($penghuni->masa_berakhir_sewa)
            : Carbon::parse($penghuni->tanggal_masuk);

        $newEndDate = $this->calculateEndDate($currentEnd, $duration, $unit);

        // Update masa berakhir dan info pembayaran terakhir
        $penghuni->update([
            'durasi_bayar_terakhir' => $duration,
            'unit_bayar_terakhir' => $unit,
            'masa_berakhir_sewa' => $newEndDate,
        ]);

        Log::info('Payment recorded:', [
            'penghuni_id' => $penghuni->id,
            'duration' => $duration,
            'unit' => $unit,
            'new_end_date' => $newEndDate
        ]);

        return response()->json([
            'message' => 'Pembayaran berhasil dicatat.',
            'data' => $penghuni->fresh(['kamar'])
        ], 200);
    }

    /**
     * POST /api/penghunis/{id}/checkout
     * Menonaktifkan penghuni dan bebaskan kamar
     */
    public function checkout(Request $request, Penghuni $penghuni)
    {
        if ($penghuni->status_sewa === 'Nonaktif') {
            return response()->json(['message' => 'Penghuni ini sudah nonaktif.'], 400);
        }

        // Cek apakah penghuni saat ini benar-benar menempati kamar sebelum checkout
        if (!$penghuni->kamar_id) {
            return response()->json(['message' => 'Penghuni ini tidak terikat pada kamar manapun.'], 400);
        }

        $validator = Validator::make($request->all(), [
            'tanggal_keluar' => 'required|date|after_or_equal:' . $penghuni->tanggal_masuk,
        ]);

        if ($validator->fails()) {
            Log::error('Validasi gagal - Checkout:', $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $kamarIdToFree = $penghuni->kamar_id;

        // 💡 PERBAIKAN: Update status penghuni DAN LEPAS IKATAN KAMAR (kamar_id = NULL)
        $penghuni->update([
            'status_sewa' => 'Nonaktif',
            'tanggal_keluar' => $request->tanggal_keluar ?? Carbon::today(),
            'kamar_id' => null, // <--- KRITIS: Lepas ikatan kamar
        ]);

        // Bebaskan kamar di tabel kamars
        Kamar::where('id', $kamarIdToFree)->update(['is_available' => true]);

        Log::info('Penghuni checkout:', [
            'penghuni_id' => $penghuni->id,
            'kamar_yang_dilepas' => $kamarIdToFree,
            'tanggal_keluar' => $penghuni->tanggal_keluar
        ]);

        return response()->json(['message' => 'Penghuni berhasil di-checkout.'], 200);
    }

    /**
     * POST /api/penghunis/{id}/reassign
     * Mengaktifkan kembali penghuni nonaktif ke kamar baru/lama
     */
    public function reassign(Request $request, Penghuni $penghuni)
    {
        $validator = Validator::make($request->all(), [
            'new_kamar_id' => 'required|exists:kamars,id',
            'tanggal_masuk_baru' => 'required|date',
            'initial_duration' => 'required|integer|min:1',
            'duration_unit' => 'required|in:day,week,month,year',
        ]);

        if ($validator->fails()) {
            Log::error('Validasi gagal - Reassign:', $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($penghuni->status_sewa === 'Aktif') {
            return response()->json(['message' => 'Penghuni sudah aktif. Gunakan tombol Edit atau Bayar.'], 400);
        }

        // Cek ketersediaan kamar baru
        $newKamar = Kamar::findOrFail($request->new_kamar_id);
        if (!$newKamar->is_available) {
            Log::warning('Reassign gagal - kamar tidak tersedia:', ['kamar_id' => $newKamar->id]);
            return response()->json(['message' => 'Kamar tujuan tidak tersedia.'], 409);
        }

        // Hitung masa berakhir baru
        $duration = $request->initial_duration;
        $unit = $request->duration_unit;
        $newStartDate = Carbon::parse($request->tanggal_masuk_baru);
        $newEndDate = $this->calculateEndDate($newStartDate, $duration, $unit);

        // Update penghuni jadi aktif lagi
        $penghuni->update([
            'kamar_id' => $request->new_kamar_id,
            'tanggal_masuk' => $request->tanggal_masuk_baru,
            'tanggal_keluar' => null,
            'status_sewa' => 'Aktif',
            'masa_berakhir_sewa' => $newEndDate,
            'durasi_bayar_terakhir' => $duration,
            'unit_bayar_terakhir' => $unit,
        ]);

        // Update status kamar
        $newKamar->update(['is_available' => false]);

        Log::info('Penghuni reassigned:', [
            'penghuni_id' => $penghuni->id,
            'kamar_baru' => $newKamar->nama_kamar,
            'masa_berakhir' => $newEndDate
        ]);

        return response()->json([
            'message' => "Penghuni berhasil diaktifkan kembali dan dipindahkan ke kamar {$newKamar->nama_kamar}.",
            'data' => $penghuni->fresh(['kamar'])
        ], 200);
    }

    /**
     * DELETE /api/penghunis/{id}
     * Hapus penghuni (hanya jika tidak ada transaksi)
     */
    public function destroy(Penghuni $penghuni)
    {
        // Cek apakah ada riwayat transaksi
        if ($penghuni->transaksis()->exists()) {
            Log::warning('Gagal hapus penghuni - ada transaksi:', ['penghuni_id' => $penghuni->id]);
            return response()->json([
                'message' => 'Penghuni tidak dapat dihapus karena sudah memiliki riwayat transaksi. Harap nonaktifkan saja.'
            ], 409);
        }

        // Bebaskan kamar jika penghuni masih aktif
        if ($penghuni->status_sewa === 'Aktif') {
            Kamar::where('id', $penghuni->kamar_id)->update(['is_available' => true]);
        }

        $penghuniId = $penghuni->id;
        $penghuniNama = $penghuni->nama_lengkap;
        $penghuni->delete();

        Log::info('Penghuni deleted:', ['id' => $penghuniId, 'nama' => $penghuniNama]);

        return response()->json(['message' => 'Penghuni berhasil dihapus.'], 200);
    }

    /**
     * GET /api/penghunis/{id}/detail-tagihans
     * Mengambil detail penghuni beserta daftar tagihan
     */
    public function getDetailAndTagihans($id)
    {
        $penghuni = Penghuni::with([
            'kamar:id,nama_kamar,harga_bulanan',
            'tagihans' => function ($query) {
                $query->orderBy('jatuh_tempo', 'desc');
            }
        ])->findOrFail($id);

        return response()->json(['data' => $penghuni], 200);
    }

    /**
     * Helper: Menghitung tanggal akhir berdasarkan durasi dan unit
     */
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
                $endDate->addMonths($duration); // Fallback
        }

        return $endDate->toDateString();
    }
}
