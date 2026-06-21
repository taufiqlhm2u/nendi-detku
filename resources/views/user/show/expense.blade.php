<x-layout>
    <div class="bg-[#f9f9ff] text-[#191c21] min-h-screen pb-20">

        {{-- Top Bar --}}
        <div class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-base-200">
            <div class="max-w-xl mx-auto flex items-center gap-3 px-4 py-3">
                <a href="{{ route('history') }}"
                    class="btn btn-sm btn-ghost btn-circle">
                    <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                </a>
                <h1 class="text-base font-bold flex-1">Detail Pengeluaran</h1>
            </div>
        </div>

        <main class="px-4 pt-6 pb-32 max-w-lg mx-auto space-y-3">

            @php
                $config = \App\Models\Expense::$typeConfig[$expense->type]
                            ?? \App\Models\Expense::$typeConfig['other'];
            @endphp

            <!-- Transaction Header Card -->
            <div class="card bg-white border border-base-300/40 shadow-sm text-center">
                <div class="card-body gap-2 items-center">
                    <div class="badge gap-1 py-3 px-3 bg-red-100 text-red-700 border-red-200 font-semibold">
                        <span class="material-symbols-outlined text-[14px]">trending_down</span>
                        Pengeluaran
                    </div>
                    <h2 class="text-2xl font-extrabold tracking-tight text-red-500">-Rp {{ number_format($expense->amount, 0, ',', '.') }}</h2>
                    <p class="text-sm text-[#191c21]/50">{{ $config['label'] }}</p>
                </div>
            </div>

            <!-- Bento Grid: Kategori + Tanggal -->
            <div class="grid grid-cols-2 gap-3">

                <div class="card bg-white border border-base-300/40 shadow-sm">
                    <div class="card-body p-4 gap-2">
                        <p class="text-[10px] font-bold tracking-widest uppercase text-[#191c21]/40">Kategori</p>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg {{ $config['bg'] }} {{ $config['text'] }} flex items-center justify-center shrink-0">
                                {!! $config['icon'] !!}
                            </div>
                            <span class="font-semibold text-sm">{{ $config['label'] }}</span>
                        </div>
                    </div>
                </div>

                <div class="card bg-white border border-base-300/40 shadow-sm">
                    <div class="card-body p-4 gap-2">
                        <p class="text-[10px] font-bold tracking-widest uppercase text-[#191c21]/40">Tanggal</p>
                        <div>
                            <p class="font-bold text-sm">{{ \Carbon\Carbon::parse($expense->date)->translatedFormat('j M Y') }}</p>
                            {{-- <p class="text-xs text-[#191c21]/50">{{ \Carbon\Carbon::parse($expense->created_at)->format('H:i') }} WIB</p> --}}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Catatan -->
            <div class="card bg-white border border-base-300/40 shadow-sm">
                <div class="card-body p-4 gap-2">
                    <p class="text-[10px] font-bold tracking-widest uppercase text-[#191c21]/40">Catatan</p>
                    <p class="text-sm text-[#191c21]/70 leading-relaxed whitespace-pre-line">
                        {{ $expense->note ?? 'Tidak ada catatan' }}
                    </p>
                </div>
            </div>

            <!-- Attachment -->
            @if ($expense->image)
            <div onclick="image_modal.showModal()" class="relative group overflow-hidden rounded-2xl h-48 border border-base-300/40 shadow-sm cursor-pointer">
                <img
                    class="w-full h-full object-cover grayscale opacity-80 group-hover:grayscale-0 group-hover:opacity-100 transition-all duration-500"
                    src="{{ asset('storage/' . $expense->image) }}"
                    alt="Lampiran Struk"
                />
                <div class="absolute inset-0 bg-linear-to-t from-black/60 to-transparent flex items-end p-4">
                    <p class="text-white text-xs font-semibold flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">image</span>
                        Lampiran Struk (Klik untuk perbesar)
                    </p>
                </div>
            </div>

            <!-- Image Modal -->
            <dialog id="image_modal" class="modal">
                <div class="modal-box relative p-1 max-w-2xl bg-white/10 backdrop-blur-md rounded-2xl border border-white/20">
                    <form method="dialog">
                        <button class="btn btn-sm btn-circle absolute right-3 top-3 bg-black/40 text-white border-none hover:bg-black/60 z-10">✕</button>
                    </form>
                    <img src="{{ asset('storage/' . $expense->image) }}" alt="Lampiran Struk Full" class="w-full h-auto rounded-xl object-contain max-h-[80vh]" />
                </div>
                <form method="dialog" class="modal-backdrop bg-black/60">
                    <button>close</button>
                </form>
            </dialog>
            @endif

            <!-- Bottom Actions -->
            <div class="fixed bottom-0 left-0 right-0 max-w-lg mx-auto px-4 py-4 bg-[#f9f9ff]/80 backdrop-blur-md border-t border-base-300/30 z-40 flex gap-3">
                <a href="{{ route('expenses.edit', $expense->id) }}" class="btn btn-outline btn-primary flex-1 gap-2 rounded-xl">
                    <span class="material-symbols-outlined">edit</span>
                    Ubah
                </a>
                <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" class="flex-1 flex" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn bg-red-500 text-white w-full gap-2 rounded-xl shadow-lg shadow-error/20 hover:opacity-90">
                        <span class="material-symbols-outlined">delete</span>
                        Hapus
                    </button>
                </form>
            </div>

            <!-- Background decoration -->
            <div class="fixed inset-0 -z-10 pointer-events-none overflow-hidden">
                <svg class="absolute top-0 right-0 opacity-5 text-primary" width="400" height="400" viewBox="0 0 100 100">
                    <circle cx="100" cy="0" r="50" fill="currentColor"/>
                </svg>
                <svg class="absolute bottom-20 -left-12 opacity-5 text-[#4b5f80]" width="300" height="300" viewBox="0 0 100 100">
                    <rect x="0" y="0" width="100" height="100" fill="currentColor" transform="rotate(45)"/>
                </svg>
            </div>

        </main>
    </div>
</x-layout>
