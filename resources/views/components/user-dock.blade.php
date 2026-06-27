<nav class="fixed bottom-0 left-0 right-0 bg-[#f9f9ff] border-t border-base-300 shadow-lg z-50">
    <div class="flex items-center justify-around px-4 py-2 max-w-2xl mx-auto">

        <!-- Beranda -->
        <a href="{{ route('beranda') }}"
            class="flex flex-col items-center justify-center gap-1 px-3 py-1 transition-colors {{ request()->routeIs('beranda') ? 'text-primary' : 'text-base-content/50 hover:text-primary' }}">
            <span class="material-symbols-outlined text-[26px]"
                style="{{ request()->routeIs('beranda') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">cottage</span>
            <span class="text-xs {{ request()->routeIs('beranda') ? 'font-semibold' : 'font-medium' }}">Beranda</span>
        </a>

        <!-- Riwayat -->
        <a href="{{ route('history') }}"
            class="flex flex-col items-center justify-center gap-1 px-3 py-1 transition-colors {{ request()->routeIs('history') ? 'text-primary' : 'text-base-content/50 hover:text-primary' }}">
            <span class="material-symbols-outlined text-[26px]"
                style="{{ request()->routeIs('history') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">receipt</span>
            <span class="text-xs {{ request()->routeIs('history') ? 'font-semibold' : 'font-medium' }}">Riwayat</span>
        </a>

        <!-- Tombol Tambah (tengah) -->
        <button onclick="my_modal_3.showModal()" class="flex flex-col items-center justify-center -mt-5">
            <div
                class="w-14 h-14 rounded-2xl bg-primary shadow-lg flex items-center justify-center active:scale-90 transition-transform duration-200">
                <span class="material-symbols-outlined text-[28px] text-white">add</span>
            </div>
        </button>

        <!-- Statistik -->
        <a href="{{ route('statistic') }}"
            class="flex flex-col items-center justify-center gap-1 px-3 py-1 transition-colors {{ request()->routeIs('statistic*') ? 'text-primary' : 'text-base-content/50 hover:text-primary' }}">
            <span class="material-symbols-outlined text-[26px]"
                style="{{ request()->routeIs('statistik') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">donut_large</span>
            <span
                class="text-xs {{ request()->routeIs('statistik') ? 'font-semibold' : 'font-medium' }}">Statistik</span>
        </a>

        <!-- Profil -->
        <a href="{{route('profile.index')}}"
            class="flex flex-col items-center justify-center gap-1 px-3 py-1 transition-colors {{ request()->routeIs('profile*') || request()->routeIs('password*') ? 'text-primary' : 'text-base-content/50 hover:text-primary' }}">
            <span class="material-symbols-outlined text-[26px]"
                style="{{ request()->routeIs('profil') ? 'font-variation-settings: \'FILL\' 1;' : '' }}">account_circle</span>
            <span class="text-xs {{ request()->routeIs('profil') ? 'font-semibold' : 'font-medium' }}">Profil</span>
        </a>

    </div>
</nav>

{{-- modal untuk memilih --}}
<dialog id="my_modal_3" class="modal">
    <div class="modal-box">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>
        <h3 class="text-xl font-bold text-gray-800 mb-2">Tambah Transaksi Baru</h3>
        <p class="text-sm text-gray-500 mb-6">Pilih jenis transaksi yang ingin Anda tambahkan</p>

        <div class="space-y-3">
            <!-- Pemasukan -->
            <a href="{{ route('incomes.create') }}"
                class="block w-full p-4 bg-linear-to-r from-green-50 to-emerald-50 hover:from-green-100 hover:to-emerald-100 rounded-xl transition-all duration-200 group border border-green-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center shadow-md group-hover:scale-105 transition-transform">
                            <span class="material-symbols-outlined text-[24px] text-green-500">arrow_downward</span>
                        </div>
                        <div class="text-left">
                            <p class="font-bold text-gray-800 text-lg">Pemasukan</p>
                            <p class="text-sm text-gray-500">Tambah uang masuk</p>
                        </div>
                    </div>
                    <span
                        class="material-symbols-outlined text-[20px] text-gray-400 group-hover:text-green-600 group-hover:translate-x-1 transition-all">chevron_right</span>
                </div>
            </a>

            <!-- Pengeluaran -->
            <a href="{{ route('expenses.create') }}"
                class="block w-full p-4 bg-linear-to-r from-red-50 to-rose-50 hover:from-red-100 hover:to-rose-100 rounded-xl transition-all duration-200 group border border-red-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center shadow-md group-hover:scale-105 transition-transform">
                            <span class="material-symbols-outlined text-[24px] text-rose-500">arrow_upward</span>
                        </div>
                        <div class="text-left">
                            <p class="font-bold text-gray-800 text-lg">Pengeluaran</p>
                            <p class="text-sm text-gray-500">Catat uang keluar</p>
                        </div>
                    </div>
                    <span
                        class="material-symbols-outlined text-[20px] text-gray-400 group-hover:text-rose-600 group-hover:translate-x-1 transition-all">chevron_right</span>
                </div>
            </a>
        </div>
    </div>
</dialog>
