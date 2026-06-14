<x-layout.app>
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
                    <form method="POST" action="{{ route('register') }}" class="flex flex-col gap-4">
                        @csrf

                        <!-- Nama Lengkap -->
                        <div class="form-control gap-1">
                            <label class="label py-0" for="name">
                                <span class="label-text text-xs font-semibold tracking-wide text-base-content/60 uppercase">Nama Lengkap</span>
                            </label>
                            <input 
                                class="input input-bordered w-full focus:input-primary @error('name') input-error @enderror" 
                                id="name" 
                                name="name"
                                type="text" 
                                placeholder="John Doe" 
                                value="{{ old('name') }}"
                                required 
                                autofocus
                            />
                            @error('name')
                                <p class="text-xs text-error mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="form-control gap-1">
                            <label class="label py-0" for="email">
                                <span class="label-text text-xs font-semibold tracking-wide text-base-content/60 uppercase">Email</span>
                            </label>
                            <input 
                                class="input input-bordered w-full focus:input-primary @error('email') input-error @enderror" 
                                id="email" 
                                name="email"
                                type="email" 
                                placeholder="nama@email.com" 
                                value="{{ old('email') }}"
                                required 
                            />
                            @error('email')
                                <p class="text-xs text-error mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password dengan icon mata -->
                        <div class="form-control gap-1">
                            <label class="label py-0" for="password">
                                <span class="label-text text-xs font-semibold tracking-wide text-base-content/60 uppercase">Password</span>
                            </label>
                            <div class="relative">
                                <input 
                                    class="input input-bordered w-full focus:input-primary pr-10 @error('password') input-error @enderror" 
                                    id="password" 
                                    name="password"
                                    type="password" 
                                    placeholder="••••••••" 
                                    required 
                                />
                                <button 
                                    type="button" 
                                    class="toggle-password absolute inset-y-0 right-0 flex items-center pr-3 text-base-content/60 hover:text-primary transition-colors"
                                    data-target="password"
                                >
                                    <svg class="eye-icon w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    <svg class="eye-slash-icon w-5 h-5 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-xs text-error mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Konfirmasi Password dengan icon mata -->
                        <div class="form-control gap-1">
                            <label class="label py-0" for="password_confirmation">
                                <span class="label-text text-xs font-semibold tracking-wide text-base-content/60 uppercase">Konfirmasi Password</span>
                            </label>
                            <div class="relative">
                                <input 
                                    class="input input-bordered w-full focus:input-primary pr-10" 
                                    id="password_confirmation" 
                                    name="password_confirmation"
                                    type="password" 
                                    placeholder="••••••••" 
                                    required 
                                />
                                <button 
                                    type="button" 
                                    class="toggle-password absolute inset-y-0 right-0 flex items-center pr-3 text-base-content/60 hover:text-primary transition-colors"
                                    data-target="password_confirmation"
                                >
                                    <svg class="eye-icon w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    <svg class="eye-slash-icon w-5 h-5 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                    </svg>
                                </button>
                            </div>
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
                    <p class="text-center text-sm text-base-content/60">
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
</x-layout.app>