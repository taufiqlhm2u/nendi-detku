<?php

use Livewire\Component;

new class extends Component {
    public $old_password;
    public $new_password;
    public $new_password_confirmation;
    public $showOldPassword = false;
    public $showNewPassword = false;
    public $showConfirmPassword = false;

    public function mount()
    {
        $this->old_password = old('old_password', '');
        $this->new_password = old('new_password', '');
        $this->new_password_confirmation = old('new_password_confirmation', '');
    }

    public function toggleOldPassword()
    {
        $this->showOldPassword = !$this->showOldPassword;
    }

    public function toggleNewPassword()
    {
        $this->showNewPassword = !$this->showNewPassword;
    }

    public function toggleConfirmPassword()
    {
        $this->showConfirmPassword = !$this->showConfirmPassword;
    }
};
?>

<div>
    <main class="px-4 pt-6 pb-32 max-w-md mx-auto space-y-8">
        <section>
            <form action="{{ route('password.update') }}" method="POST" class="flex flex-col gap-4">
                @csrf
                @method('PUT')

                {{-- Password Lama --}}
                <div class="form-control gap-1">
                    <label class="label py-0" for="old_password">
                        <span
                            class="label-text text-xs font-semibold tracking-wide text-base-content/60 uppercase">Password
                            Lama</span>
                    </label>
                    <div class="relative flex items-center">
                        <label
                            class="input input-bordered w-full flex items-center gap-2 pr-10 focus-within:input-primary @error('old_password') input-error @enderror">
                            <span class="material-symbols-outlined text-[18px] opacity-40">lock</span>
                            <input wire:model="old_password" class="w-full bg-transparent outline-none"
                                id="old_password" type="{{ $showOldPassword ? 'text' : 'password' }}"
                                name="old_password" placeholder="Masukkan password lama" />
                        </label>
                        <button type="button" wire:click="toggleOldPassword"
                            class="absolute right-3 flex items-center text-base-content/60 hover:text-primary transition-colors">
                            <span
                                class="material-symbols-outlined text-[20px]">{{ $showOldPassword ? 'visibility_off' : 'visibility' }}</span>
                        </button>
                    </div>
                    @error('old_password')
                        <p class="text-xs text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password Baru --}}
                <div class="form-control gap-1">
                    <label class="label py-0" for="new_password">
                        <span
                            class="label-text text-xs font-semibold tracking-wide text-base-content/60 uppercase">Password
                            Baru</span>
                    </label>
                    <div class="relative flex items-center">
                        <label
                            class="input input-bordered w-full flex items-center gap-2 pr-10 focus-within:input-primary @error('new_password') input-error @enderror">
                            <span class="material-symbols-outlined text-[18px] opacity-40">key</span>
                            <input wire:model="new_password" class="w-full bg-transparent outline-none"
                                id="new_password" type="{{ $showNewPassword ? 'text' : 'password' }}"
                                name="new_password" placeholder="Masukkan password baru" />
                        </label>
                        <button type="button" wire:click="toggleNewPassword"
                            class="absolute right-3 flex items-center text-base-content/60 hover:text-primary transition-colors">
                            <span
                                class="material-symbols-outlined text-[20px]">{{ $showNewPassword ? 'visibility_off' : 'visibility' }}</span>
                        </button>
                    </div>
                    @error('new_password')
                        <p class="text-xs text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Konfirmasi Password --}}
                <div class="form-control gap-1">
                    <label class="label py-0" for="new_password_confirmation">
                        <span
                            class="label-text text-xs font-semibold tracking-wide text-base-content/60 uppercase">Konfirmasi
                            Password Baru</span>
                    </label>
                    <div class="relative flex items-center">
                        <label
                            class="input input-bordered w-full flex items-center gap-2 pr-10 focus-within:input-primary @error('new_password_confirmation') input-error @enderror">
                            <span class="material-symbols-outlined text-[18px] opacity-40">key</span>
                            <input wire:model="new_password_confirmation" class="w-full bg-transparent outline-none"
                                id="new_password_confirmation" type="{{ $showConfirmPassword ? 'text' : 'password' }}"
                                name="new_password_confirmation" placeholder="Konfirmasi password baru" />
                        </label>
                        <button type="button" wire:click="toggleConfirmPassword"
                            class="absolute right-3 flex items-center text-base-content/60 hover:text-primary transition-colors">
                            <span
                                class="material-symbols-outlined text-[20px]">{{ $showConfirmPassword ? 'visibility_off' : 'visibility' }}</span>
                        </button>
                    </div>
                    @error('new_password_confirmation')
                        <p class="text-xs text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="fixed bottom-[80px] left-0 right-0 px-4 max-w-md mx-auto z-10">
                    <button type="submit"
                        class="btn btn-primary w-full h-12 text-sm font-semibold rounded-xl shadow-[0_0_15px_rgba(var(--p),0.3)] active:scale-95 transition-transform">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </section>
    </main>
</div>
