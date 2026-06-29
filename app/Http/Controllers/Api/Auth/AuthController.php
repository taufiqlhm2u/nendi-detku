<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $remember = $request->boolean('remember');
        $credential = ['email' => $validated['email'], 'password' => $validated['password']];

        if (!Auth::attempt($credential, $remember)) {
            return response()->json([
                'success' => false,
                'message' => 'Login gagal.',
                'errors' => ['email' => ['Email atau password salah.']]
            ], 422);
        }

        $user = User::find(Auth::user()->id);

        $token = $user->createToken('react-app')->plainTextToken;

        return response()->json([
            'message' => 'success',
            'user' => $user,
        ])->cookie(
            'auth_token',
            $token,
            60 * 1,
            '/',
            null,
            false,
            true,
            'Strict'
        );
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Berhasil keluar']);
    }
}
