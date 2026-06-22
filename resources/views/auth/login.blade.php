<x-layout>
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

                    <form method="POST" action="{{ route('login.verif') }}" class="flex flex-col gap-4">
                        @csrf

                        <!-- Email -->
                        <div class="form-control gap-1">
                            <label class="label py-0" for="email">
                                <span
                                    class="label-text text-xs font-semibold tracking-wide text-base-content/60 uppercase">Email</span>
                            </label>
                                <input
                                    class="input input-bordered w-full focus:input-primary @error('email') input-error @enderror"
                                    id="email" name="email" type="email" placeholder="nama@email.com"
                                    value="{{ old('email') }}" autofocus />
                                @error('email')
                                    <p class="text-xs text-error mt-1">{{ $message }}</p>
                                @enderror
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
                                    id="password" name="password" type="password" placeholder="••••••••" />
                                <button type="button"
                                    class="toggle-password absolute inset-y-0 right-0 flex items-center pr-3 text-base-content/60 hover:text-primary transition-colors"
                                    data-target="password">
                                    <span class="eye-icon material-symbols-outlined text-[20px]">visibility</span>
                                    <span class="eye-slash-icon material-symbols-outlined text-[20px] hidden">visibility_off</span>
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
            // Toggle password visibility
            document.querySelectorAll('.toggle-password').forEach(button => {
                button.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const input = document.getElementById(targetId);
                    const eyeIcon = this.querySelector('.eye-icon');
                    const eyeSlashIcon = this.querySelector('.eye-slash-icon');

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
            });
        </script>
    </div>
</x-layout>
