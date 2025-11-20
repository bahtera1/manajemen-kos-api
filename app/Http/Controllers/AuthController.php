<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Login admin dan membuat token Sanctum.
     */
    public function login(Request $request)
    {
        // 1. Validasi Input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 2. Mencoba Autentikasi
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Kredensial login tidak valid.'
            ], 401); // 401 Unauthorized
        }

        $token = $user->createToken('admin-token', ['*'], now()->addMinutes(config('sanctum.expiration')))->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',
            'user' => $user->only(['id', 'name', 'email']),
            'token' => $token,
            'token_expires_at' => now()->addMinutes(config('sanctum.expiration'))->timestamp
        ], 200);
    }

    /**
     * Logout admin dan menghapus token Sanctum.
     */
    public function logout(Request $request)
    {
        // Menghapus token yang sedang digunakan
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil. Token dihapus.'], 200);
    }
}
