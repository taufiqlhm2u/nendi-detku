<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate(
            [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => [
                    'required',
                    'confirmed',
                    'string',
                    'min:8',
                    // Password::min(8)
                    //     ->mixedCase()
                    //     ->numbers()
                    //     ->symbols()
                ],
                'password_confirmation' => [
                    'required',
                    'same:password'
                ]
            ],
            [
                'name.required' => 'Nama wajib diisi.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.unique' => 'Email sudah digunakan.',
                'password.required' => 'Password wajib diisi.',
                'password.min' => 'Password minimal 8 karakter.',
                'password.confirmed' => 'Konfirmasi password tidak cocok.',
                'password_confirmation.required' => 'Konfirmasi password wajib diisi.',
                'password_confirmation.same' => 'Konfirmasi password tidak cocok.',
            ]
        );

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            Auth::login($user);

            $token = $user->createToken('react-app')->plainTextToken;

            return response()->json([
                'message' => 'Berhasil mendaftar.',
                'user' => $user
            ])->cookie('auth_token', $token, 60 * 1, '/', null, false, true, 'Strict');
        } catch (Exception) {
            return response()->json([
                'message' => 'Gagal mendaftar.',
            ]);
        }
    }
}
