<?php

use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public $expense;
    public $photo;
    public $amount;
    public $type;
    public $date;
    public $note;

    public function mount()
    {
        $e = $this->expense;
        $this->amount = old('amount', $e->amount);
        $this->type = old('type', $e->type);
        $this->date = old('date', $e->date->translatedFormat('Y-m-d'));
        $this->note = old('note', $e->note);
    }
};
?>

<div>

    {{-- App Bar --}}
    <header class="sticky top-0 z-50 bg-base-100 border-b border-base-300/40 backdrop-blur-md">
        <div class="max-w-xl mx-auto flex items-center justify-between px-4 py-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('expenses.show', $this->expense->id) }}" class="btn btn-ghost btn-sm btn-circle">
                    <span class="material-symbols-outlined text-[24px] text-default">arrow_back_ios_new</span>
                </a>
                <h1 class="text-lg font-bold text-default">Edit Pengeluaran</h1>
            </div>
        </div>
    </header>

    <main class="px-4 pt-6 max-w-xl mx-auto space-y-6">

        <div class="card bg-white border border-base-300/40 shadow-sm">
            <div class="card-body gap-5">

                <form id="expenseForm" method="post" action="{{ route('expenses.update', $this->expense->id) }}"
                    class="flex flex-col gap-5" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Jumlah --}}
                    <div class="form-control gap-1" x-data="{
                        raw: '{{ old('amount', $this->expense->amount) }}',
                    
                        get formatted() {
                            const num = parseInt(this.raw, 10);
                            if (!this.raw || isNaN(num)) return '';
                            return new Intl.NumberFormat('id-ID').format(num);
                        },
                    
                        onInput(e) {
                            const digits = e.target.value.replace(/\D/g, '');
                            this.raw = digits;
                            this.$nextTick(() => {
                                e.target.value = this.formatted;
                                const len = e.target.value.length;
                                e.target.setSelectionRange(len, len);
                            });
                        }
                    }">
                        <label class="label py-0" for="amount_display">
                            <span
                                class="label-text text-xs font-bold tracking-widest uppercase text-base-content/60">Jumlah</span>
                        </label>

                        <div class="relative flex items-center">
                            <span
                                class="absolute left-4 z-10 text-rose-600 font-bold text-xl pointer-events-none select-none">Rp</span>

                            {{-- Input tampilan: dikelola Alpine, TIDAK punya name --}}
                            <input id="amount_display" type="text" inputmode="numeric" placeholder="0"
                                x-bind:value="formatted" x-on:input="onInput($event)" autocomplete="off"
                                class="input input-bordered focus:input-primary w-full h-16 pl-14 text-rose-600 text-3xl font-extrabold placeholder:text-rose-500/25 @error('amount') input-error @enderror" />

                            {{-- Hidden input: nilai bersih dikirim ke server --}}
                            <input type="hidden" name="amount" x-bind:value="raw" />
                        </div>

                        @error('amount')
                            <p class="text-xs text-error mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tipe + Tanggal --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        {{-- Tipe --}}
                        <div class="form-control gap-1">
                            <label class="label py-0" for="tipe">
                                <span
                                    class="label-text text-xs font-bold tracking-widest uppercase text-base-content/60">Tipe</span>
                            </label>
                            <select wire:model="type"
                                class="select select-bordered focus:select-primary w-full @error('type') select-error @enderror"
                                id="tipe" name="type">
                                <option value="">Pilih kategori...</option>
                                <option value="shopping">Belanja</option>
                                <option value="snacks">Jajan</option>
                                <option value="personal needs">Kebutuhan Pribadi</option>
                                <option value="transportation">Transportasi</option>
                                <option value="savings">Tabungan</option>
                                <option value="bills">Tagihan</option>
                                <option value="other">Lainnya</option>
                            </select>
                            @error('type')
                                <p class="text-xs text-error mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tanggal --}}
                        <div class="form-control gap-1">
                            <label class="label py-0" for="date">
                                <span
                                    class="label-text text-xs font-bold tracking-widest uppercase text-base-content/60">Tanggal
                                    Transaksi</span>
                            </label>
                            <input wire:model="date" name="date" id="date" type="date"
                                class="input input-bordered focus:input-primary w-full @error('date') input-error @enderror" />
                            @error('date')
                                <p class="text-xs text-error mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Keterangan --}}
                    <div class="form-control gap-1">
                        <label class="label py-0" for="note">
                            <span
                                class="label-text text-xs font-bold tracking-widest uppercase text-base-content/60">Keterangan</span>
                        </label>
                        <textarea wire:model="note" name="note" id="note" rows="3" placeholder="Tambahkan catatan (opsional)..."
                            class="textarea textarea-bordered focus:textarea-primary w-full resize-none @error('note') textarea-error @enderror"></textarea>
                        @error('note')
                            <p class="text-xs text-error mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Foto --}}
                    <div class="form-control gap-1" x-data="{
                        previewUrl: null,
                        fileName: '',
                        fileSize: '',
                        errorMsg: '',
                        showMenu: false,
                        existingPhotoUrl: '{{ $this->expense->image ? asset('storage/' . $this->expense->image) : '' }}',
                    
                        async handleFile(event) {
                            this.errorMsg = '';
                            let file = event.target.files[0];
                            if (!file) return;
                    
                            if (!file.type.startsWith('image/')) {
                                this.errorMsg = 'File harus berupa gambar (JPG, PNG, dll).';
                                this.clear();
                                return;
                            }
                    
                            if (file.size > 2 * 1024 * 1024) {
                                file = await this.compressImage(file);
                            }
                    
                            if (file.size > 5 * 1024 * 1024) {
                                this.errorMsg = 'Ukuran maksimal foto adalah 5MB.';
                                this.clear();
                                return;
                            }
                    
                            this.fileName = file.name;
                            this.fileSize = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
                    
                            const reader = new FileReader();
                            reader.onload = (e) => this.previewUrl = e.target.result;
                            reader.readAsDataURL(file);
                    
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(file);
                            this.$refs.mainInput.files = dataTransfer.files;
                    
                            this.showMenu = false;
                        },
                    
                        compressImage(file) {
                            return new Promise((resolve) => {
                                const reader = new FileReader();
                                reader.readAsDataURL(file);
                                reader.onload = (event) => {
                                    const img = new Image();
                                    img.src = event.target.result;
                                    img.onload = () => {
                                        const MAX = 1600;
                                        let w = img.width,
                                            h = img.height;
                                        if (w > h) {
                                            if (w > MAX) {
                                                h = Math.round(h * MAX / w);
                                                w = MAX;
                                            }
                                        } else {
                                            if (h > MAX) {
                                                w = Math.round(w * MAX / h);
                                                h = MAX;
                                            }
                                        }
                                        const canvas = document.createElement('canvas');
                                        canvas.width = w;
                                        canvas.height = h;
                                        canvas.getContext('2d').drawImage(img, 0, 0, w, h);
                                        canvas.toBlob((blob) => {
                                            if (!blob) { resolve(file); return; }
                                            resolve(new File([blob], file.name, { type: 'image/jpeg', lastModified: Date.now() }));
                                        }, 'image/jpeg', 0.8);
                                    };
                                    img.onerror = () => resolve(file);
                                };
                                reader.onerror = () => resolve(file);
                            });
                        },
                    
                        clear() {
                            this.previewUrl = null;
                            this.fileName = '';
                            this.fileSize = '';
                            this.errorMsg = '';
                            ['cameraInput', 'galleryInput', 'mainInput'].forEach(r => {
                                if (this.$refs[r]) this.$refs[r].value = '';
                            });
                        }
                    }">

                        <label class="label py-0">
                            <span
                                class="label-text text-xs font-bold tracking-widest uppercase text-base-content/60">Bukti
                                Pengeluaran (Opsional)</span>
                        </label>

                        {{-- Hidden Inputs --}}
                        <input type="file" accept="image/*" x-ref="mainInput" name="photo" class="hidden" />
                        <input type="file" accept="image/*" capture="environment" x-ref="cameraInput" class="hidden"
                            @change="handleFile($event)" />
                        <input type="file" accept="image/*" x-ref="galleryInput" class="hidden"
                            @change="handleFile($event)" />

                        {{-- Upload Button --}}
                        <div x-show="!previewUrl && !existingPhotoUrl" class="relative">
                            <button type="button" @click="showMenu = !showMenu" @click.away="showMenu = false"
                                class="btn btn-outline border-base-300 border-dashed w-full h-24 flex flex-col items-center justify-center gap-1 hover:bg-base-200 hover:border-primary/50 transition-colors">
                                <span
                                    class="material-symbols-outlined text-[24px] text-base-content/50">add_a_photo</span>
                                <span class="text-sm font-medium text-base-content/70">Tambahkan Foto</span>
                            </button>

                            {{-- Dropdown Menu --}}
                            <div x-show="showMenu" style="display: none;"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute z-50 bottom-full left-0 right-0 mb-2 bg-base-100 rounded-xl shadow-lg border border-base-200 p-2 flex flex-col gap-1">
                                <button type="button" @click="$refs.cameraInput.click()"
                                    class="btn btn-ghost justify-start gap-3 w-full font-normal">
                                    <span
                                        class="material-symbols-outlined text-[20px] text-primary">photo_camera</span>
                                    Ambil dari Kamera
                                </button>
                                <button type="button" @click="$refs.galleryInput.click()"
                                    class="btn btn-ghost justify-start gap-3 w-full font-normal">
                                    <span
                                        class="material-symbols-outlined text-[20px] text-primary">photo_library</span>
                                    Pilih dari Galeri
                                </button>
                            </div>
                        </div>

                        {{-- Existing Photo Preview --}}
                        <div x-show="!previewUrl && existingPhotoUrl" style="display: none;"
                            class="flex flex-col gap-3 p-3 bg-white border border-base-200 rounded-xl shadow-sm relative">
                            <div class="w-full h-48 bg-base-200 rounded-lg overflow-hidden group">
                                <img :src="existingPhotoUrl" class="w-full h-full object-cover" alt="Foto saat ini" />
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex-1 min-w-0 pr-2">
                                    <p class="text-sm font-bold truncate text-base-content">Foto Saat Ini</p>
                                    <p class="text-xs text-base-content/60">Tersimpan di database</p>
                                </div>
                                <div class="relative">
                                    <button type="button" @click="showMenu = !showMenu"
                                        @click.away="showMenu = false"
                                        class="btn btn-primary btn-sm gap-2 btn-outline">
                                        <span class="material-symbols-outlined text-[16px]">edit</span>
                                        Ubah
                                    </button>

                                    {{-- Dropdown Menu for Existing Photo --}}
                                    <div x-show="showMenu" style="display: none;"
                                        x-transition:enter="transition ease-out duration-150"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-100"
                                        x-transition:leave-start="opacity-100 scale-100"
                                        x-transition:leave-end="opacity-0 scale-95"
                                        class="absolute z-50 bottom-full right-0 mb-2 w-52 bg-base-100 rounded-xl shadow-lg border border-base-200 p-2 flex flex-col gap-1">
                                        <button type="button" @click="$refs.cameraInput.click()"
                                            class="btn btn-ghost justify-start gap-3 w-full font-normal text-sm">
                                            <span
                                                class="material-symbols-outlined text-[18px] text-primary">photo_camera</span>
                                            Ambil dari Kamera
                                        </button>
                                        <button type="button" @click="$refs.galleryInput.click()"
                                            class="btn btn-ghost justify-start gap-3 w-full font-normal text-sm">
                                            <span
                                                class="material-symbols-outlined text-[18px] text-primary">photo_library</span>
                                            Pilih dari Galeri
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Error Alpine --}}
                        <template x-if="errorMsg">
                            <p x-text="errorMsg" class="text-xs text-error mt-1"></p>
                        </template>

                        {{-- Preview --}}
                        <div x-show="previewUrl" style="display: none;"
                            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            class="flex flex-col gap-3 p-3 bg-white border border-base-200 rounded-xl shadow-sm">
                            <div class="w-full h-48 bg-base-200 rounded-lg overflow-hidden">
                                <img :src="previewUrl" class="w-full h-full object-cover" alt="Preview foto" />
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex-1 min-w-0 pr-2">
                                    <p class="text-sm font-bold truncate text-base-content" x-text="fileName"></p>
                                    <p class="text-xs text-base-content/60" x-text="fileSize"></p>
                                </div>
                                <button type="button" @click="clear()"
                                    class="btn btn-error btn-sm gap-2 btn-outline">
                                    <span class="material-symbols-outlined text-[16px]">delete</span>
                                    Batal
                                </button>
                            </div>
                        </div>

                        @error('photo')
                            <p class="text-xs text-error mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Submit button (desktop only) --}}
                    <div class="hidden md:block pt-1">
                        <button class="btn btn-primary w-full text-base font-bold gap-2" type="submit"
                            wire:loading.attr="disabled">
                            <span wire:loading class="loading loading-spinner loading-sm"></span>
                            <span wire:loading.remove>Simpan Perubahan</span>
                            <span wire:loading>Mengubah...</span>
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
            form="expenseForm" type="submit" wire:loading.attr="disabled">
            <span wire:loading class="loading loading-spinner loading-sm"></span>
            <span wire:loading.remove>Simpan Perubahan</span>
            <span wire:loading>Mengubah...</span>
        </button>
    </div>

</div>