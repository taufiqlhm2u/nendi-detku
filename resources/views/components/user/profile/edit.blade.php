<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;

    public $photo = null;
    public bool $removePhoto = false;

    public function removeStoredPhoto(): void
    {
        $this->removePhoto = true;
        $this->photo = null;
    }

    public function cancelPhotoPreview(): void
    {
        $this->photo = null;
    }

    public function saveAvatar(): void
    {
        $this->validate(
            [
                'photo' => ['nullable', 'image', 'max:2048'],
            ],
            [
                'photo.image' => 'File harus berupa gambar.',
                'photo.max' => 'Ukuran gambar maksimal 2 MB.',
            ],
        );

        $user = Auth::user();

        if ($this->removePhoto) {
            if ($user->photo_profile) {
                Storage::disk('public')->delete($user->photo_profile);
            }
            $user->photo_profile = null;
            $user->save();
            $this->removePhoto = false;
            session()->flash('success', 'Foto profil berhasil dihapus.');
            $this->redirect(request()->header('Referer'), navigate: true); // ← tambah ini
            return;
        }

        if ($this->photo) {
            if ($user->photo_profile) {
                Storage::disk('public')->delete($user->photo_profile);
            }
            $user->photo_profile = $this->photo->store('avatars', 'public');
            $user->save();
            $this->photo = null;
            session()->flash('success', 'Foto profil berhasil diperbarui.');
            $this->redirect(request()->header('Referer'), navigate: true); // ← tambah ini
        }
    }
};
?>

<div>
    <main class="px-4 pt-6 pb-32 max-w-md mx-auto space-y-8">

        {{-- ===== Avatar Section ===== --}}
        <section class="flex flex-col items-center pt-2">
            <div class="relative w-28 h-28">

                @if ($photo)
                    <div class="w-28 h-28 rounded-full ring-2 ring-primary ring-offset-2 overflow-hidden">
                        <img alt="Preview Foto" src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover" />
                    </div>
                @elseif (auth()->user()->photo_profile && !$removePhoto)
                    <div class="w-28 h-28 rounded-full ring-2 ring-primary ring-offset-2 overflow-hidden">
                        <img alt="Foto Profil" src="{{ Storage::url(auth()->user()->photo_profile) }}"
                            onerror="this.src='{{ asset('images/user-default.jpeg') }}'"
                            class="w-full h-full object-cover" />
                    </div>
                @else
                    <div class="w-28 h-28 rounded-full ring-2 ring-base-300 ring-offset-2 overflow-hidden">
                        <img alt="Foto Default" src="{{ asset('images/user-default.jpeg') }}"
                            class="w-full h-full object-cover" />
                    </div>
                @endif

                <button type="button" onclick="document.getElementById('photo-input').click()"
                    class="w-8 h-8 rounded-full bg-primary flex items-center justify-center absolute bottom-0 right-0 border-2 border-white shadow active:scale-90 transition-transform">
                    <span class="material-symbols-outlined text-[16px] text-white">photo_camera</span>
                </button>

                <input id="photo-input" type="file" accept="image/*" wire:model="photo" class="hidden" />
            </div>

            {{-- Nama & email --}}
            <p class="mt-3 font-semibold text-base text-base-content">{{ auth()->user()->name }}</p>
            <p class="text-sm text-base-content/50">{{ auth()->user()->email }}</p>

            {{-- Aksi foto --}}
            @if ($photo)
                <div class="flex gap-2 mt-4">
                    <button type="button" wire:click="saveAvatar"
                        class="btn btn-primary btn-sm rounded-full gap-1 active:scale-95 transition-transform">
                        <span class="material-symbols-outlined text-[14px]">check</span>
                        Simpan Foto
                    </button>
                    <button type="button" wire:click="cancelPhotoPreview"
                        class="btn btn-ghost btn-sm rounded-full text-red-500 hover:bg-error/10 gap-1">
                        <span class="material-symbols-outlined text-[14px]">close</span>
                        Batal
                    </button>
                </div>
            @elseif (auth()->user()->photo_profile && !$removePhoto)
                <button type="button" wire:click="removeStoredPhoto"
                    class="btn btn-ghost btn-sm text-red-500 mt-3 rounded-full hover:bg-error/10 gap-1">
                    <span class="material-symbols-outlined text-[15px]">delete</span>
                    Hapus Foto
                </button>
            @else
                <label for="photo-input"
                    class="btn btn-ghost btn-sm text-primary mt-3 rounded-full cursor-pointer hover:bg-primary/10 gap-1">
                    <span class="material-symbols-outlined text-[15px]">upload</span>
                    Unggah Foto
                </label>
            @endif

            @if ($removePhoto)
                <div class="flex gap-2 mt-4">
                    <button type="button" wire:click="saveAvatar"
                        class="btn bg-red-500 text-white btn-sm rounded-full gap-1 active:scale-95">
                        <span class="material-symbols-outlined text-[14px]">delete</span>
                        Ya, Hapus
                    </button>
                    <button type="button" wire:click="$set('removePhoto', false)"
                        class="btn btn-ghost btn-sm rounded-full">
                        Batal
                    </button>
                </div>
            @endif

            @error('photo')
                <p class="text-error text-xs mt-2">{{ $message }}</p>
            @enderror
        </section>

        {{-- ===== Form Profil ===== --}}
        <section>
            <p class="text-xs font-bold tracking-widest uppercase text-base-content/40 mb-4">Informasi Akun</p>

            <form action="{{ route('profile.update', auth()->id()) }}" method="POST" class="flex flex-col gap-4"
                novalidate>
                @csrf
                @method('PUT')

                {{-- Nama Lengkap --}}
                <div class="form-control gap-1">
                    <label class="label py-0" for="name">
                        <span class="label-text text-xs font-semibold tracking-wide text-base-content/60 uppercase">Nama Lengkap</span>
                    </label>
                    <label class="input input-bordered w-full flex items-center gap-2 focus-within:input-primary @error('name') input-error @enderror">
                        <span class="material-symbols-outlined text-[18px] opacity-40">person</span>
                        <input class="w-full bg-transparent outline-none" id="name" type="text" name="name"
                            value="{{ old('name', auth()->user()->name) }}" placeholder="Nama lengkap" required
                            minlength="2" maxlength="255" />
                    </label>
                    @error('name')
                        <p class="text-xs text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="form-control gap-1">
                    <label class="label py-0" for="email">
                        <span class="label-text text-xs font-semibold tracking-wide text-base-content/60 uppercase">Email</span>
                    </label>
                    <label class="input input-bordered w-full flex items-center gap-2 focus-within:input-primary @error('email') input-error @enderror">
                        <span class="material-symbols-outlined text-[18px] opacity-40">mail</span>
                        <input class="w-full bg-transparent outline-none" id="email" type="email" name="email"
                            value="{{ old('email', auth()->user()->email) }}" placeholder="mail@site.com" required />
                    </label>
                    @error('email')
                        <p class="text-xs text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="btn btn-primary w-full h-12 text-sm font-semibold rounded-xl mt-1 active:scale-95 transition-transform">
                    Simpan Perubahan
                </button>
            </form>
        </section>

        {{-- ===== Danger Zone ===== --}}
        <section class="border border-red-600/20 rounded-2xl p-5 bg-red-600/5">
            <p class="text-xs font-bold tracking-widest uppercase text-red-600/60 mb-1">Danger Zone</p>
            <p class="text-sm text-base-content/50 mb-4">
                Menghapus akun bersifat permanen dan tidak dapat dipulihkan. Seluruh data transaksi Anda akan ikut
                terhapus.
            </p>
            <button type="button" onclick="delete_account_modal.showModal()"
                class="btn bg-white text-red-500 border-red-500 w-full h-11 text-sm font-semibold hover:bg-red-500 hover:text-white transition-all ease-in duration-300 rounded-xl active:scale-95">
                Hapus Akun Saya
            </button>
        </section>

    </main>

    {{-- ===== Modal Konfirmasi Hapus Akun ===== --}}
    <dialog id="delete_account_modal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box rounded-2xl px-6 py-8 max-w-sm mx-auto text-center">

            {{-- Icon --}}
            <div class="w-14 h-14 rounded-2xl bg-red-50 flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-[32px] text-red-500"
                    style="font-variation-settings: 'FILL' 1;">delete_forever</span>
            </div>

            <h3 class="font-bold text-lg text-base-content">Hapus Akun?</h3>
            <p class="text-sm text-base-content/50 mt-2 leading-relaxed">
                Tindakan ini <span class="font-semibold text-red-500">tidak dapat dibatalkan</span>.
                Seluruh data akun dan transaksi Anda akan dihapus secara permanen.
            </p>

            {{-- Form DELETE --}}
            <form action="{{ route('profile.destroy', auth()->id()) }}" method="POST" class="mt-6 flex flex-col gap-2">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="btn bg-red-500 hover:bg-red-600 text-white w-full h-11 text-sm font-semibold rounded-xl active:scale-95 transition-transform border-none">
                    Ya, Hapus Akun Saya
                </button>
                <button type="button" onclick="delete_account_modal.close()"
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

</div>
