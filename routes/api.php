<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KamarController;
use App\Http\Controllers\PenghuniController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\TagihanController;

// ============================================
// 🔒 RUTE PUBLIK (Dengan Rate Limiting Ketat)
// ============================================

// Login: Maksimal 5 percobaan per menit untuk mencegah brute force attack
Route::middleware('throttle:5,1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

// ============================================
// 🔐 RUTE TERLINDUNGI (Auth + Rate Limiting)
// ============================================
// Membutuhkan header: Authorization: Bearer <token>
// Rate limit: 60 requests per menit per user

Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {

    // --- Autentikasi & Info User ---
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // 1. KAMAR (CRUD)
    Route::apiResource('kamars', KamarController::class);

    // 2. PENGHUNI (CRUD & Checkout)
    Route::apiResource('penghunis', PenghuniController::class);
    Route::post('penghunis/{penghuni}/payment', [PenghuniController::class, 'recordPayment']);
    Route::post('penghunis/{penghuni}/checkout', [PenghuniController::class, 'checkout']);
    Route::post('penghunis/{penghuni}/reassign', [PenghuniController::class, 'reassign']);
    Route::get('penghunis/{id}/tagihans', [PenghuniController::class, 'getDetailAndTagihans']);

    // Tagihan/Kuitansi
    Route::get('tagihans/{id}/kuitansi-data', [TagihanController::class, 'getKuitansiData']);
    Route::get('/tagihans/draft/{penghuniId}', [TagihanController::class, 'getOrCreateDraft']);

    // 3. TRANSAKSI (CRUD)
    Route::apiResource('transaksis', TransaksiController::class);

    // ============================================
    // 📊 LAPORAN (Rate Limiting Lebih Longgar)
    // ============================================
    // Query berat, limit lebih rendah: 30 requests per menit
    Route::middleware('throttle:30,1')->group(function () {
        Route::get('reports/laba-rugi', [TransaksiController::class, 'reportLabaRugi']);
        Route::get('reports/due-soon', [TransaksiController::class, 'dueSoonReport']);
        Route::get('reports/transaction-summary', [TransaksiController::class, 'transactionSummaryExport']);
    });
});
