<x-layout>
    <div class="bg-[#f9f9ff] text-[#191c21] min-h-screen pb-24">

        <x-user-header />

        <main class="max-w-md mx-auto px-4 pt-6 flex flex-col items-center">

            <!-- Profile Header -->
            <div class="flex flex-col items-center mb-8">
                <div class="relative">
                    <div class="avatar">
                        <div class="w-32 rounded-full ring ring-white ring-offset-0 shadow-lg">
                            <img alt="User Avatar"
                                src="{{ $user->photo_profile ? asset('storage/' . $user->photo_profile) : asset('images/user-default.jpeg') }}"
                                onerror="this.src='{{ asset('images/user-default.jpeg') }}'" />
                        </div>
                    </div>
                </div>
                <div class="mt-4 text-center">
                    <h2 class="text-xl font-bold">{{ $user->name }}</h2>
                    <p class="text-sm text-[#191c21]/50">{{ $user->email }}</p>
                </div>
            </div>

            <!-- Settings List -->
            <div class="w-full flex flex-col gap-4">

                <!-- Account Section -->
                <div class="card bg-white border border-base-300/40 shadow-sm overflow-hidden">
                    <a class="flex items-center justify-between px-5 py-4 hover:bg-[#f9f9ff] transition-colors group cursor-pointer"
                        href="{{ route('profile.edit', $user->id) }}">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined">person</span>
                            </div>
                            <span class="font-medium text-base">Edit Profil</span>
                        </div>
                        <span
                            class="material-symbols-outlined text-[#191c21]/30 group-hover:translate-x-1 transition-transform">chevron_right</span>
                    </a>
                    <div class="divider my-0 mx-5 h-px"></div>
                    <a class="flex items-center justify-between px-5 py-4 hover:bg-[#f9f9ff] transition-colors group cursor-pointer"
                        href="chnage_pw.html">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined">lock</span>
                            </div>
                            <span class="font-medium text-base">Ubah Kata Sandi</span>
                        </div>
                        <span
                            class="material-symbols-outlined text-[#191c21]/30 group-hover:translate-x-1 transition-transform">chevron_right</span>
                    </a>
                </div>

                <!-- Security Card -->
                <div class="card bg-primary text-primary-content shadow-lg overflow-hidden relative">
                    <div class="card-body gap-1 relative z-10">
                        <h3 class="font-bold text-lg">Keamanan Akun</h3>
                        <p class="text-sm opacity-90">Status akun Anda terlindungi dengan otentikasi dua faktor.</p>
                    </div>
                    <div class="absolute -right-4 -bottom-4 opacity-20 pointer-events-none">
                        <span class="material-symbols-outlined text-[100px]"
                            style="font-variation-settings:'FILL' 1;">verified_user</span>
                    </div>
                </div>

                <!-- Danger Zone -->
                <div class="card bg-white border border-base-300/40 shadow-sm overflow-hidden">
                    <button onclick="logout_modal.showModal()"
                        class="flex items-center justify-between px-5 py-4 hover:bg-red-50 transition-colors group w-full">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-10 h-10 rounded-full bg-error/10 flex items-center justify-center text-red-500">
                                <span class="material-symbols-outlined">logout</span>
                            </div>
                            <span class="font-semibold text-base text-red-500">Logout</span>
                        </div>
                        <span
                            class="material-symbols-outlined text-red-500/50 group-hover:translate-x-1 transition-transform">chevron_right</span>
                    </button>
                </div>

                <!-- Version -->
                <div class="text-center py-6">
                    <p class="text-xs font-semibold tracking-widest text-[#191c21]/30 uppercase">Versi Aplikasi 1.0.0
                        (Stable)</p>
                </div>

            </div>
        </main>

        <!-- ===== Bottom Navigation Bar ===== -->
        <x-user-dock />

    </div>

    {{-- ===== Modal Konfirmasi Logout ===== --}}
    <dialog id="logout_modal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box rounded-2xl px-6 py-8 max-w-sm mx-auto text-center">

            {{-- Icon --}}
            <div class="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-[32px] text-amber-500"
                    style="font-variation-settings: 'FILL' 1;">logout</span>
            </div>

            <h3 class="font-bold text-lg text-base-content">Keluar dari Akun?</h3>
            <p class="text-sm text-base-content/50 mt-2 leading-relaxed">
                Anda akan keluar dari sesi ini. Anda perlu login kembali untuk mengakses akun Anda.
            </p>

            {{-- Form Logout --}}
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

        {{-- Klik backdrop = tutup --}}
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

</x-layout>
