<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB; // Untuk agregasi Laba/Rugi
use App\Models\Penghuni;
use App\Models\Kamar;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    // READ ALL: Mengambil daftar semua transaksi
    public function index(Request $request)
    {
        $query = Transaksi::query();

        // 🚨 Tambahkan filter tanggal untuk index jika diperlukan di tempat lain
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal_transaksi', [$request->start_date, $request->end_date]);
        }


        // Muat data penghuni hanya untuk transaksi Pemasukan, dan kamar
        $transaksis = $query->with([
            'penghuni:id,nama_lengkap',
            'kamar:id,nama_kamar'
        ])
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        return response()->json(['data' => $transaksis], 200);
    }

    // CREATE: Menyimpan transaksi baru (Pemasukan atau Pengeluaran)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tipe_transaksi' => 'required|in:Pemasukan,Pengeluaran',
            'kategori' => 'required|string|max:100',
            'deskripsi' => 'required|string',
            'jumlah' => 'required|numeric|min:1',
            'tanggal_transaksi' => 'required|date',
            'kamar_id' => 'nullable|exists:kamars,id', // Tambahkan validasi kamar_id
            'metode_pembayaran' => 'nullable|string', // Tambahkan validasi metode_pembayaran
            'penghuni_id' => 'nullable|sometimes|exists:penghunis,id',
        ]);

        // Di TransaksiController.php, fungsi store()

        if ($validator->fails()) {
            // 🚨 TAMBAHKAN LOG INI
            \Log::error('Gagal Validasi Transaksi:', $validator->errors()->toArray());

            return response()->json(['errors' => $validator->errors()], 422);
        }

        $transaksi = Transaksi::create($request->all());

        return response()->json([
            'message' => 'Transaksi berhasil dicatat.',
            'data' => $transaksi
        ], 201);
    }

    // READ ONE: Menampilkan satu transaksi
    public function show(Transaksi $transaksi)
    {
        return response()->json(['data' => $transaksi->load('penghuni:id,nama_lengkap', 'kamar:id,nama_kamar')], 200);
    }

    // UPDATE: Memperbarui data transaksi
    public function update(Request $request, Transaksi $transaksi)
    {
        $validator = Validator::make($request->all(), [
            'tipe_transaksi' => 'required|in:Pemasukan,Pengeluaran',
            'kategori' => 'required|string|max:100',
            'deskripsi' => 'required|string',
            'jumlah' => 'required|numeric|min:1',
            'tanggal_transaksi' => 'required|date',
            'kamar_id' => 'nullable|exists:kamars,id', // Tambahkan validasi kamar_id
            'metode_pembayaran' => 'nullable|string', // Tambahkan validasi metode_pembayaran
            'penghuni_id' => 'required_if:tipe_transaksi,Pemasukan|nullable|exists:penghunis,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $transaksi->update($request->all());

        return response()->json([
            'message' => 'Transaksi berhasil diperbarui.',
            'data' => $transaksi
        ], 200);
    }

    // DELETE: Menghapus data transaksi
    public function destroy(Transaksi $transaksi)
    {
        $transaksi->delete();
        return response()->json(['message' => 'Transaksi berhasil dihapus.'], 200);
    }

    // PELAPORAN: Fitur Laba/Rugi (Endpoint Khusus) - RINGKASAN
    public function reportLabaRugi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $query = Transaksi::query();
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_transaksi', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_transaksi', '<=', $request->end_date);
        }

        $results = $query
            ->select('tipe_transaksi', DB::raw('SUM(jumlah) as total'))
            ->groupBy('tipe_transaksi')
            ->get()
            ->keyBy('tipe_transaksi');

        $pemasukan = $results['Pemasukan']->total ?? 0;
        $pengeluaran = $results['Pengeluaran']->total ?? 0;
        $laba_rugi = $pemasukan - $pengeluaran;

        return response()->json([
            'message' => 'Laporan Laba/Rugi berhasil dibuat.',
            'pemasukan' => (float) $pemasukan,
            'pengeluaran' => (float) $pengeluaran,
            'laba_rugi' => (float) $laba_rugi,
            'detail' => $results->toArray()
        ], 200);
    }

    // 🚨 FUNGSI BARU: EXPORT LABA RUGI (RINGKASAN + DETAIL)
    public function transactionSummaryExport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $start = $request->start_date;
        $end = $request->end_date;

        // --- 1. HITUNG RINGKASAN ---
        $summaryResults = Transaksi::whereBetween('tanggal_transaksi', [$start, $end])
            ->select('tipe_transaksi', DB::raw('SUM(jumlah) as total'))
            ->groupBy('tipe_transaksi')
            ->get()
            ->keyBy('tipe_transaksi');

        $totalIncome = $summaryResults['Pemasukan']->total ?? 0;
        $totalExpense = $summaryResults['Pengeluaran']->total ?? 0;
        $netProfit = $totalIncome - $totalExpense;

        // --- 2. AMBIL DETAIL PEMASUKAN ---
        $incomeDetails = Transaksi::where('tipe_transaksi', 'Pemasukan')
            ->whereBetween('tanggal_transaksi', [$start, $end])
            ->with(['penghuni:id,nama_lengkap', 'kamar:id,nama_kamar'])
            ->orderBy('tanggal_transaksi', 'asc')
            ->get();

        // --- 3. AMBIL DETAIL PENGELUARAN ---
        $expenseDetails = Transaksi::where('tipe_transaksi', 'Pengeluaran')
            ->whereBetween('tanggal_transaksi', [$start, $end])
            // Pengeluaran tidak perlu load penghuni/kamar
            ->orderBy('tanggal_transaksi', 'asc')
            ->get();


        // --- 4. RETURN DATA DALAM STRUKTUR EXPORT ---
        return response()->json([
            'summary' => [
                'total_income' => (float) $totalIncome,
                'total_expense' => (float) $totalExpense,
                'net_profit' => (float) $netProfit,
            ],
            'income' => $incomeDetails,
            'expense' => $expenseDetails,
        ], 200);
    }

    public function dueSoonReport()
    {
        $today = Carbon::today();
        $sevenDaysLater = Carbon::today()->addDays(7);

        $penghunisJatuhTempo = Penghuni::with('kamar')
            ->where('status_sewa', 'Aktif')
            ->where(function ($query) use ($today, $sevenDaysLater) {
                $query->whereDate('masa_berakhir_sewa', '<=', $sevenDaysLater)
                    ->whereDate('masa_berakhir_sewa', '>=', $today->copy()->subDays(30)); // Include yang sudah lewat hingga 30 hari
            })
            ->orderBy('masa_berakhir_sewa', 'asc')
            ->get();

        \Log::info('Due Soon Report:', [
            'today' => $today->toDateString(),
            'seven_days_later' => $sevenDaysLater->toDateString(),
            'count' => $penghunisJatuhTempo->count(),
            'sample' => $penghunisJatuhTempo->first() ? [
                'id' => $penghunisJatuhTempo->first()->id,
                'nama' => $penghunisJatuhTempo->first()->nama_lengkap,
                'masa_berakhir' => $penghunisJatuhTempo->first()->masa_berakhir_sewa,
                'status' => $penghunisJatuhTempo->first()->status_sewa
            ] : null
        ]);

        return response()->json([
            'count' => $penghunisJatuhTempo->count(),
            'data' => $penghunisJatuhTempo,
            'meta' => [
                'today' => $today->toDateString(),
                'range_end' => $sevenDaysLater->toDateString()
            ]
        ], 200);
    }
}
