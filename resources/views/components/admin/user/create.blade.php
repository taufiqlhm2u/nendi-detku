<?php

use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public $nama;
    public $email;
    public $password;
    public $password_confirmation;
    public $role;
    public $showPassword;
    public $showConfirmPassword;

    public function mount()
    {
        $this->nama = old('nama', '');
        $this->email = old('email', '');
        $this->password = old('password', '');
        $this->password_confirmation = old('password_confirmation', '');
        $this->role = old('role', '');
    }

    public function togglePassword()
    {
        $this->showPassword = !$this->showPassword;
    }

    public function toggleConfirmPassword()
    {
        $this->showConfirmPassword = !$this->showConfirmPassword;
    }
};
?>

<div>

    {{-- App Bar --}}
    <header class="sticky top-0 z-50 bg-base-100 border-b border-base-300/40 backdrop-blur-md">
        <div class="max-w-xl mx-auto flex items-center justify-between px-4 py-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.user.index') }}" class="btn btn-ghost btn-sm btn-circle">
                    <span class="material-symbols-outlined text-[24px] text-default">arrow_back_ios_new</span>
                </a>
                <h1 class="text-lg font-bold text-default">Tambah User</h1>
            </div>
        </div>
    </header>

    <main class="px-4 pt-6 max-w-xl mx-auto space-y-6">

        <div class="card bg-white border border-base-300/40 shadow-sm">
            <div class="card-body gap-5">

                <form method="post" action="{{ route('admin.user.store') }}" class="flex flex-col gap-5"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="form-control gap-1">
                        <label class="label py-0" for="nama">
                            <span class="label-text text-xs font-bold tracking-widest  text-base-content/60">Nama
                                Lengkap</span>
                        </label>
                        <input wire:model="nama" name="nama" id="nama" type="text"
                            class="input input-bordered focus:input-primary w-full @error('nama') input-error @enderror"
                            placeholder="masukan nama lengkap" />
                        @error('nama')
                            <p class="text-xs text-error mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-control gap-1">
                        <label class="label py-0" for="email">
                            <span
                                class="label-text text-xs font-bold tracking-widest  text-base-content/60">Email</span>
                        </label>
                        <input wire:model="email" name="email" id="email" type="text"
                            class="input input-bordered focus:input-primary w-full @error('email') input-error @enderror"
                            placeholder="your@email.com" />
                        @error('email')
                            <p class="text-xs text-error mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-control gap-1">
                        <label class="label py-0" for="password">
                            <span class="label-text text-xs font-semibold tracking-wide text-base-content/60">Buat
                                Password</span>
                        </label>
                        <div class="relative flex items-center">
                            <label
                                class="input input-bordered w-full flex items-center gap-2 pr-10 focus-within:input-primary @error('password') input-error @enderror">
                                <span class="material-symbols-outlined text-[18px] opacity-40">key</span>
                                <input wire:model="new_password" class="w-full bg-transparent outline-none"
                                    id="new_password" type="{{ $showPassword ? 'text' : 'password' }}" name="password"
                                    placeholder="Masukkan password " />
                            </label>
                            <button type="button" wire:click="togglePassword"
                                class="absolute right-3 flex items-center text-base-content/60 hover:text-primary transition-colors">
                                <span
                                    class="material-symbols-outlined text-[20px]">{{ $showPassword ? 'visibility_off' : 'visibility' }}</span>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-xs text-error mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div class="form-control gap-1">
                        <label class="label py-0" for="password_confirmation">
                            <span class="label-text text-xs font-semibold tracking-wide text-base-content/60">Konfirmasi
                                Password</span>
                        </label>
                        <div class="relative flex items-center">
                            <label
                                class="input input-bordered w-full flex items-center gap-2 pr-10 focus-within:input-primary @error('password_confirmation') input-error @enderror">
                                <span class="material-symbols-outlined text-[18px] opacity-40">key</span>
                                <input wire:model="password_confirmation" class="w-full bg-transparent outline-none"
                                    id="password_confirmation" type="{{ $showConfirmPassword ? 'text' : 'password' }}"
                                    name="password_confirmation" placeholder="Konfirmasi password " />
                            </label>
                            <button type="button" wire:click="toggleConfirmPassword"
                                class="absolute right-3 flex items-center text-base-content/60 hover:text-primary transition-colors">
                                <span
                                    class="material-symbols-outlined text-[20px]">{{ $showConfirmPassword ? 'visibility_off' : 'visibility' }}</span>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <p class="text-xs text-error mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">
                                <label class="label py-0" for="role">
                                    <span
                                        class="label-text text-xs font-semibold tracking-wide text-base-content/60">Role</span>
                                </label>
                            </legend>
                            <select
                                class="select w-full flex items-center gap-2 pr-10 focus-within:input-primary @error('role') input-error @enderror"
                                id="role" name="role" wire:model="role">
                                <option disabled selected>Pilih role</option>
                                <option value="admin">Admin</option>
                                <option value="user">User</option>
                            </select>
                            @error('role')
                                <p class="text-xs text-error mt-1">{{ $message }}</p>
                            @enderror
                        </fieldset>
                    </div>

                    {{-- Submit button (desktop only) --}}
                    <div class="hidden md:block pt-1">
                        <button class="btn btn-primary w-full text-base font-bold gap-2" type="submit"
                            wire:loading.attr="disabled">
                            <span wire:loading class="loading loading-spinner loading-sm"></span>
                            <span wire:loading.remove>Tambah</span>
                            <span wire:loading>Menambah...</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </main>

    {{-- Bottom Action (mobile only) --}}
    <div
        class="fixed bottom-0 left-0 w-full px-4 py-4 bg-base-100/80 backdrop-blur-md border-t border-base-300/30 z-50 md:hidden">
        <button
            class="btn btn-primary w-full h-12 text-md font-bold gap-2 rounded-2xl shadow-lg active:scale-95 transition-transform"
            form="incomeForm" type="submit" wire:loading.attr="disabled">
            <span wire:loading class="loading loading-spinner loading-sm"></span>
            <span wire:loading.remove>Tambah</span>
            <span wire:loading>menambah...</span>
        </button>
    </div>

</div>
