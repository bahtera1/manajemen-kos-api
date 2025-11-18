<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KamarController;
use App\Http\Controllers\PenghuniController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\TagihanController;


Route::post('/login', [AuthController::class, 'login']);
// Rute TERLINDUNGI (Membutuhkan header Authorization: Bearer <token>)
Route::middleware('auth:sanctum')->group(function () {

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
    // Endpoint Khusus: Checkout Penghuni
    Route::post('penghunis/{penghuni}/checkout', [PenghuniController::class, 'checkout']);

    Route::post('penghunis/{penghuni}/reassign', [PenghuniController::class, 'reassign']);
    Route::get('penghunis/{id}/tagihans', [PenghuniController::class, 'getDetailAndTagihans']);
    Route::get('tagihans/{id}/kuitansi-data', [TagihanController::class, 'getKuitansiData']);
    Route::post('tagihans/{tagihan}/update-status', [TagihanController::class, 'updateStatus']);

    // 3. TRANSAKSI (CRUD & Pelaporan)
    Route::apiResource('transaksis', TransaksiController::class);
    // Endpoint Khusus: Laporan Laba/Rugi
    Route::get('reports/laba-rugi', [TransaksiController::class, 'reportLabaRugi']);
    Route::get('reports/due-soon', [TransaksiController::class, 'dueSoonReport']);
    Route::get('reports/transaction-summary', [TransaksiController::class, 'transactionSummaryExport']);
});
