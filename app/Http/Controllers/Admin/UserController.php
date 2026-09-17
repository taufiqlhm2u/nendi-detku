<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Income;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.user.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.user.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'password_confirmation' => 'required|same:password',
            'role' => 'required' 
        ], [
            'nama.required' => 'Nama wajib di isi.',
            'email.required' => 'Email wajib di isi.',
            'email.unique' => 'Email sudah terdaftar di sistem.',
            'password.required' => 'Password wajib di isi.',
            'password_confirmation.required' => 'Konfirmasi password wajib di isi.',
            'password_confirmation.same' => 'Konfirmasi password tidak sama.',
            'role.required' => 'Role wajib di isi.',
        ]);

        User::create([
            'name' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        return redirect()->route('admin.user.index')->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        return view('admin.user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'nama' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable',
            'password_confirmation' => 'required_with:password|same:password',
            'role' => 'required' 
        ], [
            'nama.required' => 'Nama wajib di isi.',
            'email.required' => 'Email wajib di isi.',
            'email.unique' => 'Email sudah terdaftar di sistem.',
            'password_confirmation.required_with' => 'Konfirmasi password wajib di isi jika mengubah password.',
            'password_confirmation.same' => 'Konfirmasi password tidak sama.',
            'role.required' => 'Role wajib di isi.',
        ]);

        $data = [
            'name' => $request->nama,
            'email' => $request->email,
            'role' => $request->role
        ];

        if($request->filled('password')){
            $data['password'] = Hash::make($request->password);
        }
        $user->update($data);

        return redirect()->route('admin.user.index')->with('success', 'User berhasil diubah.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::find($id);

        $income = Income::where('user_id', $user->id)->get();

        foreach ($income as $i) {
            if ($i->image) {
                Storage::disk('public')->delete($i->image);
            }
            $i->delete(); // Hapus data income dari database
        }

        $expense = Expense::where('user_id', $user->id)->get();

        foreach ($expense as $e) {
            if ($e->image) {
                Storage::disk('public')->delete($e->image);
            }
            $e->delete(); // Hapus data expense dari database
        }

        if ($user && $user->id == $id) {

            if ($user->photo_profile) {
                Storage::disk('public')->delete($user->photo_profile);
            }
            $user->delete();
            return redirect()->route('admin.user.index')->with('success', 'Akun User berhasil dihapus secara permanen.');
        }
        } catch (Exception $e){
            return back()->with('error', 'Ada kendala saat mencoba hapus akun user.');
        }
    }
}
