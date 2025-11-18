<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KamarController extends Controller
{
    // READ ALL: Mengambil daftar semua kamar
    public function index()
    {
        $kamars = Kamar::with('penghuni')->orderBy('id', 'desc')->get();

        \Log::info('Kamars fetched:', [
            'count' => $kamars->count(),
            'sample' => $kamars->first() ? [
                'id' => $kamars->first()->id,
                'nama' => $kamars->first()->nama_kamar,
                'fasilitas_raw' => $kamars->first()->getAttributes()['deskripsi_fasilitas'],
                'fasilitas_cast' => $kamars->first()->deskripsi_fasilitas,
                'fasilitas_type' => gettype($kamars->first()->deskripsi_fasilitas)
            ] : null
        ]);

        return response()->json([
            'message' => 'Daftar kamar berhasil diambil.',
            'data' => $kamars
        ], 200);
    }

    // CREATE: Menyimpan data kamar baru
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_kamar' => 'required|string|max:100|unique:kamars,nama_kamar',
            'harga_bulanan' => 'required|numeric|min:0',
            'luas_kamar' => 'required|string|max:50',
            'blok' => 'required|string|max:10',
            'lantai' => 'required|integer|min:1',
            'type' => 'required|integer|min:1',
            'deskripsi_fasilitas' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 🔧 PERBAIKAN: Log input yang diterima
        \Log::info('Creating kamar with input:', [
            'all_input' => $request->all(),
            'deskripsi_fasilitas_raw' => $request->input('deskripsi_fasilitas'),
            'deskripsi_fasilitas_type' => gettype($request->input('deskripsi_fasilitas'))
        ]);

        // 🔧 PERBAIKAN: Validasi JSON string
        $fasilitasString = $request->input('deskripsi_fasilitas');

        // Cek apakah JSON valid
        if ($fasilitasString) {
            $decoded = json_decode($fasilitasString, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'message' => 'Format deskripsi_fasilitas tidak valid',
                    'error' => json_last_error_msg()
                ], 422);
            }
            \Log::info('Decoded facilities:', ['decoded' => $decoded]);
        }

        // Mass Assignment
        $kamar = Kamar::create($request->all());

        // 🔧 Log hasil setelah create
        \Log::info('Kamar created:', [
            'id' => $kamar->id,
            'fasilitas_saved_raw' => $kamar->getAttributes()['deskripsi_fasilitas'],
            'fasilitas_saved_cast' => $kamar->deskripsi_fasilitas,
            'type' => gettype($kamar->deskripsi_fasilitas)
        ]);

        // Refresh untuk memastikan cast bekerja
        $kamar = $kamar->fresh();

        return response()->json([
            'message' => 'Kamar baru berhasil ditambahkan.',
            'data' => $kamar
        ], 201);
    }

    // READ ONE: Menampilkan satu kamar berdasarkan ID
    public function show(Kamar $kamar)
    {
        \Log::info('Showing kamar:', [
            'id' => $kamar->id,
            'fasilitas_raw' => $kamar->getAttributes()['deskripsi_fasilitas'],
            'fasilitas_cast' => $kamar->deskripsi_fasilitas,
            'type' => gettype($kamar->deskripsi_fasilitas)
        ]);

        return response()->json([
            'message' => 'Detail kamar berhasil diambil.',
            'data' => $kamar
        ], 200);
    }

    // UPDATE: Memperbarui data kamar
    public function update(Request $request, Kamar $kamar)
    {
        $validator = Validator::make($request->all(), [
            'nama_kamar' => 'required|string|max:100|unique:kamars,nama_kamar,' . $kamar->id,
            'harga_bulanan' => 'required|numeric|min:0',
            'luas_kamar' => 'required|string|max:50',
            'blok' => 'required|string|max:10',
            'lantai' => 'required|integer|min:1',
            'type' => 'required|integer|min:1',
            'deskripsi_fasilitas' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 🔧 PERBAIKAN: Log input yang diterima
        \Log::info('Updating kamar:', [
            'kamar_id' => $kamar->id,
            'all_input' => $request->all(),
            'deskripsi_fasilitas_input' => $request->input('deskripsi_fasilitas'),
            'current_fasilitas' => $kamar->deskripsi_fasilitas
        ]);

        // 🔧 PERBAIKAN: Validasi JSON string
        $fasilitasString = $request->input('deskripsi_fasilitas');

        if ($fasilitasString) {
            $decoded = json_decode($fasilitasString, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'message' => 'Format deskripsi_fasilitas tidak valid',
                    'error' => json_last_error_msg()
                ], 422);
            }
            \Log::info('Decoded facilities for update:', ['decoded' => $decoded]);
        }

        $kamar->update($request->all());

        // 🔧 Log hasil setelah update
        \Log::info('Kamar updated:', [
            'id' => $kamar->id,
            'fasilitas_after_raw' => $kamar->getAttributes()['deskripsi_fasilitas'],
            'fasilitas_after_cast' => $kamar->fresh()->deskripsi_fasilitas
        ]);

        return response()->json([
            'message' => 'Data kamar berhasil diperbarui.',
            'data' => $kamar->fresh()
        ], 200);
    }

    // DELETE: Menghapus data kamar
    public function destroy(Kamar $kamar)
    {
        // Cek apakah kamar ini memiliki penghuni aktif
        if ($kamar->penghuni) {
            return response()->json([
                'message' => 'Kamar tidak dapat dihapus karena masih ditempati penghuni.'
            ], 409);
        }

        $kamar->delete();

        return response()->json(['message' => 'Kamar berhasil dihapus.'], 200);
    }
}
