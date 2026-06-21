<x-layout>
    <div class="bg-base-100 text-base-content min-h-screen flex items-center justify-center p-4">

        <!-- Decorative blobs -->
        <div class="fixed inset-0 -z-10 overflow-hidden opacity-50 pointer-events-none">
            <div
                class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-primary/5 rounded-full blur-[100px] animate-pulse">
            </div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] bg-secondary/10 rounded-full blur-[120px] animate-pulse"
                style="animation-delay:2s"></div>
        </div>

        <div class="w-full max-w-[440px]">
            <div class="card bg-white shadow-xl border border-base-300/30">
                <div class="card-body gap-5">

                    <!-- Header -->
                    <div class="flex flex-col items-center text-center gap-3">
                        <div class="avatar placeholder">
                            <div class="w-16 rounded-xl shadow-lg flex items-center justify-center">
                                <img src="{{ asset('images/logo.png') }}" alt="Logo">
                            </div>
                        </div>
                        <div>
                            <h1 class="text-2xl font-extrabold text-base-content">Nendi Detku</h1>
                            <p class="text-sm text-base-content/60 mt-0.5">Mulai perjalanan finansial Anda hari ini</p>
                        </div>
                    </div>

                    <!-- Form Register - Siap untuk Laravel -->
                    <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-4">
                        @csrf

                        <!-- Nama Lengkap -->
                        <div class="form-control gap-1">
                            <label class="label py-0" for="name">
                                <span
                                    class="label-text text-xs font-semibold tracking-wide text-base-content/60 uppercase">Nama
                                    Lengkap</span>
                            </label>
                            <input
                                class="input input-bordered w-full focus:input-primary @error('name') input-error @enderror"
                                id="name" name="name" type="text" placeholder="John Doe"
                                value="{{ old('name') }}" autofocus />
                            @error('name')
                                <p class="text-xs text-error mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="form-control gap-1">
                            <label class="label py-0" for="email">
                                <span
                                    class="label-text text-xs font-semibold tracking-wide text-base-content/60 uppercase">Email</span>
                            </label>
                            <input
                                class="input input-bordered w-full focus:input-primary @error('email') input-error @enderror"
                                id="email" name="email" type="email" placeholder="nama@email.com"
                                value="{{ old('email') }}" />
                            @error('email')
                                <p class="text-xs text-error mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password dengan icon mata -->
                        <div class="form-control gap-1">
                            <label class="label py-0" for="password">
                                <span
                                    class="label-text text-xs font-semibold tracking-wide text-base-content/60 uppercase">Password</span>
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

                        <!-- Konfirmasi Password dengan icon mata -->
                        <div class="form-control gap-1">
                            <label class="label py-0" for="password_confirmation">
                                <span
                                    class="label-text text-xs font-semibold tracking-wide text-base-content/60 uppercase">Konfirmasi
                                    Password</span>
                            </label>
                            <div class="relative">
                                <input class="input input-bordered w-full focus:input-primary pr-10 @error('password_confirmation') input-error @enderror"
                                    id="password_confirmation" name="password_confirmation" type="password"
                                    placeholder="••••••••" />
                                <button type="button"
                                    class="toggle-password absolute inset-y-0 right-0 flex items-center pr-3 text-base-content/60 hover:text-primary transition-colors"
                                    data-target="password_confirmation">
                                    <span class="eye-icon material-symbols-outlined text-[20px]">visibility</span>
                                    <span class="eye-slash-icon material-symbols-outlined text-[20px] hidden">visibility_off</span>
                                </button>
                            </div>
                            @error('password_confirmation')
                                <p class="text-xs text-error mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Terms -->
                        <p class="text-xs text-center text-base-content/60 px-2">
                            Dengan mendaftar, Anda menyetujui
                            <a class="link link-primary font-semibold" href="#">Ketentuan Layanan</a> dan
                            <a class="link link-primary font-semibold" href="#">Kebijakan Privasi</a> kami.
                        </p>

                        <button class="btn btn-primary w-full text-base font-semibold" type="submit">
                            Daftar
                        </button>
                    </form>

                    <!-- Login link -->
                    <div class="divider my-0"></div>
                    <p class="text-center text-sm text-base-content/60 mt-[-10px]">
                        Sudah punya akun?
                        <a class="link link-primary font-bold ml-1" href="{{ route('login') }}">Masuk</a>
                    </p>

                </div>
            </div>
        </div>

        <script>
            // Toggle password visibility untuk semua field password
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
