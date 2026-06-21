<x-layout>
    <div class="bg-[#f9f9ff] text-[#191c21] min-h-screen pb-20">

        {{-- ===== Top App Bar ===== --}}
        <x-user-header />

        <livewire:user.beranda />

        <!-- ===== Floating Action Button ===== -->
        <button onclick="my_modal_3.showModal()"
            class="btn fixed bottom-20 right-5 w-14 h-14 rounded-2xl shadow-xl bg-accent hover:bg-accent-light border-2 border-white/60 active:scale-90 transition-transform duration-200 z-60 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-base-content" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
        </button>

        {{-- modal untuk memilih --}}
        <dialog id="my_modal_3" class="modal">
            <div class="modal-box">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Tambah Transaksi Baru</h3>
                <p class="text-sm text-gray-500 mb-6">Pilih jenis transaksi yang ingin Anda tambahkan</p>

                <!-- Selection Options -->
                <div class="space-y-3">
                    <!-- Pemasukan Option -->
                    <a href="{{ route('incomes.create') }}"
                        class="block w-full p-4 bg-linear-to-r from-green-50 to-emerald-50 hover:from-green-100 hover:to-emerald-100 rounded-xl transition-all duration-200 group border border-green-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center shadow-md group-hover:scale-105 transition-transform">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                    </svg>
                                </div>
                                <div class="text-left">
                                    <p class="font-bold text-gray-800 text-lg">Pemasukan</p>
                                    <p class="text-sm text-gray-500">Tambah uang masuk</p>
                                </div>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-gray-400 group-hover:text-green-600 group-hover:translate-x-1 transition-all"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </a>

                    <!-- Pengeluaran Option -->
                    <a href="{{ route('expenses.create') }}"
                        class="block w-full p-4 bg-linear-to-r from-red-50 to-rose-50 hover:from-red-100 hover:to-rose-100 rounded-xl transition-all duration-200 group border border-red-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center shadow-md group-hover:scale-105 transition-transform">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
                                    </svg>
                                </div>
                                <div class="text-left">
                                    <p class="font-bold text-gray-800 text-lg">Pengeluaran</p>
                                    <p class="text-sm text-gray-500">Catat uang keluar</p>
                                </div>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-gray-400 group-hover:text-red-600 group-hover:translate-x-1 transition-all"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </a>
                </div>
            </div>
        </dialog>

        <!-- ===== Bottom Navigation Bar ===== -->
        <x-user-dock></x-user-dock>
    </div>
</x-layout>
