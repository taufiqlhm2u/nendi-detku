<x-layout.app>
    <div class="bg-base-100 text-base-content min-h-screen flex flex-col items-center justify-center p-4">
        <!-- Top glow -->
        <div
            class="fixed top-0 left-1/2 -translate-x-1/2 w-full max-w-lg h-64 bg-primary/5 blur-[120px] pointer-events-none">
        </div>

        <main class="w-full max-w-md z-10 flex flex-col gap-5">

            <!-- Brand -->
            <div class="flex flex-col items-center text-center gap-3">
                <div class="avatar placeholder">
                    <div class="w-16 rounded-xl shadow-lg flex items-center justify-center">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo">
                    </div>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-primary tracking-tight">Nendi Detku</h1>
                    <p class="text-sm text-base-content/60 mt-0.5">Kelola keuangan Anda dengan bijak</p>
                </div>
            </div>

            <!-- Card -->
            <div class="card bg-white shadow-xl border border-base-300/30">
                <div class="card-body gap-5">
                    <h2 class="card-title text-xl font-bold">Masuk ke Akun</h2>

                    <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-4">
                        @csrf

                        <!-- Email -->
                        <div class="form-control gap-1">
                            <label class="label py-0" for="email">
                                <span
                                    class="label-text text-xs font-semibold tracking-wide text-base-content/60 uppercase">Email</span>
                            </label>
                            <div>
                                <input
                                    class="input input-bordered w-full focus:input-primary @error('email') input-error @enderror"
                                    id="email" name="email" type="email" placeholder="nama@email.com"
                                    value="{{ old('email') }}" required autofocus />
                                @error('email')
                                    <p class="text-xs text-error mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Password dengan toggle mata di dalam input -->
                        <div class="form-control gap-1">
                            <label class="label py-0" for="password">
                                <span
                                    class="label-text text-xs font-semibold tracking-wide text-base-content/60 uppercase">Kata
                                    Sandi</span>
                            </label>
                            <div class="relative">
                                <input
                                    class="input input-bordered w-full focus:input-primary pr-10 @error('password') input-error @enderror"
                                    id="password" name="password" type="password" placeholder="••••••••" required />
                                <button type="button" id="toggle-pass"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-base-content/60 hover:text-primary transition-colors">
                                    <svg id="eye-icon" class="w-5 h-5" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    <svg id="eye-slash-icon" class="w-5 h-5 hidden" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-xs text-error mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Remember Me (opsional) -->
                        <div class="flex items-center justify-between">
                            <label class="label cursor-pointer gap-2">
                                <input type="checkbox" name="remember" class="checkbox checkbox-sm checkbox-primary"
                                    {{ old('remember') ? 'checked' : '' }}>
                                <span class="label-text text-sm">Ingat saya</span>
                            </label>
                        </div>

                        <button class="btn btn-primary w-full text-base font-semibold" type="submit">
                            Masuk
                        </button>
                    </form>

                    <!-- Register link -->
                    <p class="text-center text-sm text-base-content/60">
                        Belum punya akun?
                        <a href="{{ route('register') }}" class="link link-primary font-bold ml-1">Daftar sekarang</a>
                    </p>
                </div>
            </div>
        </main>

        <div class="fixed bottom-0 right-0 w-64 h-64 bg-secondary/10 blur-[100px] pointer-events-none"></div>

        <script>
            document.getElementById('toggle-pass').addEventListener('click', function() {
                const input = document.getElementById('password');
                const eyeIcon = document.getElementById('eye-icon');
                const eyeSlashIcon = document.getElementById('eye-slash-icon');

                if (input.type === 'password') {
                    input.type = 'text';
                    eyeIcon.classList.add('hidden');
                    eyeSlashIcon.classList.remove('hidden');
                } else {
                    input.type = 'password';
                    eyeIcon.classList.remove('hidden');
                    eyeSlashIcon.classList.add('hidden');
                }
            });
        </script>
    </div>
</x-layout.app>
