<x-layout>
    <div class="bg-[#f5f5f7] text-[#1d1d1f] min-h-screen pb-28">

        <main class="max-w-md mx-auto px-4 pt-8 flex flex-col items-center">

            <!-- Profile Header -->
            <div class="flex flex-col items-center mb-8 page-fade" style="--delay:0s">
                <div class="avatar">
                    <div class="w-24 rounded-full ring-2 ring-white shadow-md">
                        <img alt="User Avatar"
                            src="{{ $user->photo_profile ? asset('storage/' . $user->photo_profile) : asset('images/user-default.jpeg') }}"
                            onerror="this.src='{{ asset('images/user-default.jpeg') }}'" />
                    </div>
                </div>
                <h2 class="mt-4 text-lg font-semibold tracking-tight">{{ $user->name }}</h2>
                <p class="text-sm text-[#1d1d1f]/40 mt-0.5">{{ $user->email }}</p>
            </div>

            <!-- Settings List -->
            <div class="w-full flex flex-col gap-5 page-fade" style="--delay:0.1s">

                <!-- Akun Section -->
                <div>
                    <p class="text-xs font-medium text-[#1d1d1f]/40 uppercase tracking-wider px-1 mb-2">Akun</p>
                    <div class="rounded-2xl bg-white overflow-hidden shadow-sm">
                        <a href="{{ route('profile.edit', $user->id) }}"
                            class="flex items-center justify-between px-5 py-4 active:bg-[#f5f5f7] transition-colors">
                            <div class="flex items-center gap-3.5">
                                <div class="w-9 h-9 rounded-full bg-[#f0f0f5] flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[18px] text-[#1d1d1f]/60">person</span>
                                </div>
                                <span class="text-[15px] font-medium">Edit Profil</span>
                            </div>
                            <span class="material-symbols-outlined text-[18px] text-[#1d1d1f]/25">chevron_right</span>
                        </a>
                    </div>
                </div>

                <!-- Keamanan Section -->
                <div>
                    <p class="text-xs font-medium text-[#1d1d1f]/40 uppercase tracking-wider px-1 mb-2">Keamanan</p>
                    <div class="rounded-2xl bg-white overflow-hidden shadow-sm">
                        <a href="{{ route('password') }}"
                            class="flex items-center justify-between px-5 py-4 active:bg-[#f5f5f7] transition-colors">
                            <div class="flex items-center gap-3.5">
                                <div class="w-9 h-9 rounded-full bg-[#f0f0f5] flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[18px] text-[#1d1d1f]/60">lock</span>
                                </div>
                                <span class="text-[15px] font-medium">Ubah Kata Sandi</span>
                            </div>
                            <span class="material-symbols-outlined text-[18px] text-[#1d1d1f]/25">chevron_right</span>
                        </a>
                    </div>
                </div>

                <!-- Lainnya Section -->
                <div>
                    <p class="text-xs font-medium text-[#1d1d1f]/40 uppercase tracking-wider px-1 mb-2">Lainnya</p>
                    <div class="rounded-2xl bg-white overflow-hidden shadow-sm">
                        <button onclick="logout_modal.showModal()"
                            class="flex items-center justify-between px-5 py-4 w-full active:bg-red-50 transition-colors">
                            <div class="flex items-center gap-3.5">
                                <div class="w-9 h-9 rounded-full bg-red-50 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[18px] text-red-400">logout</span>
                                </div>
                                <span class="text-[15px] font-medium text-red-500">Keluar</span>
                            </div>
                            <span class="material-symbols-outlined text-[18px] text-red-300">chevron_right</span>
                        </button>
                    </div>
                </div>

                <!-- Version -->
                <p class="text-center text-[11px] tracking-widest uppercase text-[#1d1d1f]/25 font-medium pt-2 pb-4">
                    Versi {{ config('app.version') }}
                </p>

            </div>
        </main>

        <x-user-dock />
    </div>

    {{-- Modal Konfirmasi Logout --}}
    <dialog id="logout_modal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box rounded-2xl px-6 py-8 max-w-sm mx-auto text-center">

            <div class="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-[32px] text-amber-500"
                    style="font-variation-settings: 'FILL' 1;">logout</span>
            </div>

            <h3 class="font-bold text-lg text-base-content">Keluar dari Akun?</h3>
            <p class="text-sm text-base-content/50 mt-2 leading-relaxed">
                Anda akan keluar dari sesi ini. Anda perlu login kembali untuk mengakses akun Anda.
            </p>

            <form action="{{ route('logout') }}" method="POST" class="mt-6 flex flex-col gap-2">
                @csrf
                <button type="submit"
                    class="btn bg-amber-500 hover:bg-amber-600 text-white w-full h-11 text-sm font-semibold rounded-xl active:scale-95 transition-transform border-none">
                    Ya, Keluar
                </button>
                <button type="button" onclick="logout_modal.close()"
                    class="btn btn-ghost w-full h-11 text-sm rounded-xl text-base-content/60">
                    Batal
                </button>
            </form>
        </div>

        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

</x-layout>