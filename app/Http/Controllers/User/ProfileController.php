<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Income;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user = User::findOrFail(Auth::user()->id);
        return view('user.profile.index', compact('user'));
    }

    public function edit()
    {
        $user = User::findOrFail(Auth::user()->id);
        return view('user.profile.edit', compact('user'));
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name'  => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $id, 'max:255'],
        ], [
            'name.required'  => 'Nama lengkap wajib diisi.',
            'name.min'       => 'Nama minimal 2 karakter.',
            'name.max'       => 'Nama tidak boleh lebih dari 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'email.max'      => 'Email tidak boleh lebih dari 255 karakter.',
        ]);

        try {
            $user = User::findOrFail(Auth::user()->id);
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

            return redirect()->route('profile.edit', $user->id)
                ->with('success', 'Profil berhasil diperbarui.');
        } catch (Exception $e) {
            return back()->with('error', 'Terjadi kesalahan, profil gagal diperbarui.');
        }
    }

    public function destroy(Request $request, string $id)
    {
        $user = User::find($id);

        $income = Income::where('user_id', $user->id)->get();

        foreach ($income as $i) {
            if ($i->image) {
                Storage::disk('public')->delete($i->image);
            }
        }

        $expense = Expense::where('user_id', $user->id)->get();

        foreach ($expense as $e) {
            if ($e->image) {
                Storage::disk('public')->delete($e->image);
            }
        }

        if ($user && $user->id == $id) {

            if ($user->photo_profile) {
                Storage::disk('public')->delete($user->photo_profile);
            }
            $user->delete();
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Akun Anda berhasil dihapus secara permanen.');
    }

    // password 

    public function editPassword()
    {
        return view('user.profile.password');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'old_password' => 'required|current_password',
            'new_password' => 'required|string|min:8',
            'new_password_confirmation' => 'required|same:new_password',
        ], [
            'old_password.required' => 'Password lama wajib diisi.',
            'old_password.current_password' => 'Password lama yang Anda masukkan tidak sesuai dengan password akun saat ini.',

            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru harus terdiri dari minimal 8 karakter.',

            'new_password_confirmation.required' => 'Konfirmasi password wajib diisi.',
            'new_password_confirmation.same' => 'Konfirmasi password tidak sesuai dengan password baru.',
        ]);

        try {
            $user = User::findOrFail(Auth::user()->id);
            $user->update([
                'password' => Hash::make($validated['new_password']),
            ]);

            return redirect()->route('password')
                ->with('success', 'Password berhasil diperbarui.');
        } catch (Exception $e) {
            return redirect()->route('password')
                ->with('error', 'Terjadi kesalahan, password gagal diperbarui.');
        }
    }
}
