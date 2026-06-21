<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Income;
use App\Models\Wallet;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Image\Image;

class IncomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('user.create.income');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'type' => 'required||in:allowance,salary,freelance,bonus,investment,other',
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
            $folder = storage_path('app/public/transaction/income');

            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }

            $fileName = uniqid() . '.jpg';
            $fullPath = $folder . '/' . $fileName;

            Image::load($request->file('photo')->getPathname())
                ->width(1200)
                ->quality(75)
                ->save($fullPath);

            $photoPath = 'transaction/income/' . $fileName;
        }

        try {
            Income::create([
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
                'balance' => $wallet->balance + $validated['amount'],
                'updated_at' => now(),
            ]);

            return redirect()->route('beranda')->with('success', 'Pemasukan berhasil ditambahkan.');
        } catch (Exception) {
            return back()->with('error', 'Gagal saat menambahkan pemasukan.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $income = Income::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('user.show.income', compact('income'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
