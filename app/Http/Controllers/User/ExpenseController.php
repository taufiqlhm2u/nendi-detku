<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Wallet;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Image\Image;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return redirect()->route('history');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('user.create.expense');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'type' => 'required||in:shopping,snacks,personal needs,transportation,savings,bills,other',
            'date' => 'required|date',
            'note' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|max:3072',
        ], [
            'amount.required' => 'Jumlah transaksi wajib diisi.',
            'amount.numeric' => 'Jumlah transaksi harus berupa angka.',
            'amount.min' => 'Jumlah transaksi minimal Rp1.000',

            'type.required' => 'Jenis transaksi wajib dipilih.',
            'type.in' => 'Kategori yang dipilih tidak valid.',

            'date.required' => 'Tanggal transaksi wajib diisi.',
            'date.date' => 'Format tanggal tidak valid.',

            'note.string' => 'Catatan harus berupa teks.',
            'note.max' => 'Catatan maksimal 1000 karakter.',

            'photo.image' => 'File yang diunggah harus berupa gambar.',
            'photo.max' => 'Ukuran gambar maksimal 3 MB.',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $folder = storage_path('app/public/transaction/expense');
            if (!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }

            $fileName = uniqid() . '.jpg';
            $fullPath = $folder . '/' . $fileName;

            Image::load($request->file('photo')->getPathname())
                ->width(1200)
                ->quality(75)
                ->save($fullPath);

            $photoPath = 'transaction/expense/' . $fileName;
        }

        try {
            Expense::create([
                'user_id' => Auth::user()->id,
                'amount' => $validated['amount'],
                'type' => $validated['type'],
                'date' => $validated['date'],
                'note' => $validated['note'],
                'image' => $photoPath,
            ]);

            $wallet = Wallet::firstOrCreate(
                ['user_id' => Auth::user()->id],
                ['balance' => 0],
            );

            // if ($wallet->balance >= $validated['amount']) {
            $wallet->update([
                'balance' => $wallet->balance - $validated['amount'],
                'updated_at' => now(),
            ]);
            // } else {
            //     return back()->with('warning', 'Saldo didalam dompet kurang.');
            // }

            return redirect()->route('beranda')->with('success', 'Pengeluaran berhasil dicatat!');
        } catch (Exception) {
            return back()->with('error', 'Terjadi kesalahan, pengeluaran gagal disimpan.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $expense = Expense::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!$expense) {
            return back()->with('error', 'Data pengeluaran tidak ditemukan.');
        }

        return view('user.show.expense', compact('expense'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $expense = Expense::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (!$expense) {
            return back()->with('error', 'Data tidak ditemukan');
        }

        return view('user.edit.expense', compact('expense'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'type' => 'required||in:shopping,snacks,personal needs,transportation,savings,bills,other',
            'date' => 'required|date',
            'note' => 'nullable|string|max:1000',
            'photo' => 'nullable|image|max:3072',
        ], [
            'amount.required' => 'Jumlah transaksi wajib diisi.',
            'amount.numeric' => 'Jumlah transaksi harus berupa angka.',
            'amount.min' => 'Jumlah transaksi minimal Rp1.000',

            'type.required' => 'Jenis transaksi wajib dipilih.',
            'type.in' => 'Kategori yang dipilih tidak valid.',

            'date.required' => 'Tanggal transaksi wajib diisi.',
            'date.date' => 'Format tanggal tidak valid.',

            'note.string' => 'Catatan harus berupa teks.',
            'note.max' => 'Catatan maksimal 1000 karakter.',

            'photo.image' => 'File yang diunggah harus berupa gambar.',
            'photo.max' => 'Ukuran gambar maksimal 3 MB.',
        ]);

        $expense = Expense::where('user_id', Auth::user()->id)->where('id', $id)->first();

        if (!$expense) {
            return back()->with('error', 'Data pengeluaran tidak ditemukan.');
        }

        $selisih = $validated['amount'] - $expense->amount;

        $photoPath = $expense->image;
        if ($request->hasFile('photo')) {
            if ($expense->image) {
                Storage::disk('public')->delete($expense->image);
            }
            $folder = storage_path('app/public/transaction/expense');
            if (!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }

            $fileName = uniqid() . '.jpg';
            $fullPath = $folder . '/' . $fileName;

            Image::load($request->file('photo')->getPathname())
                ->width(1200)
                ->quality(75)
                ->save($fullPath);

            $photoPath = 'transaction/expense/' . $fileName;
        }

        try {
            $expense->update([
                'user_id' => Auth::user()->id,
                'amount' => $validated['amount'],
                'type' => $validated['type'],
                'date' => $validated['date'],
                'note' => $validated['note'],
                'image' => $photoPath,
            ]);

            $wallet = Wallet::firstOrCreate(
                ['user_id' => Auth::user()->id],
                ['balance' => 0],
            );

            $wallet->update([
                'balance' => $wallet->balance - $selisih,
                'updated_at' => now(),
            ]);

            return redirect()->route('beranda')->with('success', 'Data pengeluaran berhasil diperbarui.');
        } catch (Exception) {
            return back()->with('error', 'Terjadi kesalahan, pengeluaran gagal diperbarui.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $expense = Expense::where('user_id', Auth::user()->id)->where('id', $id)->first();

            if (!$expense) {
                return back()->with('error', 'Data pengeluaran tidak ditemukan.');
            }

            $wallet = Wallet::where('user_id', Auth::user()->id)->first();
            $wallet->update([
                'balance' => $wallet->balance + $expense->amount,
                'updated_at' => now(),
            ]);

            if ($expense->image) {
                Storage::disk('public')->delete($expense->image);
            }

            $expense->delete();
            return redirect()->route('history')->with('success', 'Pengeluaran berhasil dihapus.');
        } catch (Exception) {
            return back()->with('error', 'Terjadi kesalahan, pengeluaran gagal dihapus.');
        }
    }
}
