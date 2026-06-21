<?php

use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public $photo;
    public $amount;
    public $type;
    public $date;
    public $note;

    public function mount()
    {
        $this->amount = old('amount', '');
        $this->type = old('type', '');
        $this->date = old('date', now()->translatedFormat('Y-m-d'));
        $this->note = old('note', '');
    }
};
?>

<div>

    {{-- App Bar --}}
    <header class="sticky top-0 z-50 bg-base-100 border-b border-base-300/40 backdrop-blur-md">
        <div class="max-w-xl mx-auto flex items-center justify-between px-4 py-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('beranda') }}" class="btn btn-ghost btn-sm btn-circle">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="size-6 text-default">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                </a>
                <h1 class="text-lg font-bold text-default">Tambah Pengeluaran</h1>
            </div>
        </div>
    </header>

    <main class="px-4 pt-6 max-w-xl mx-auto space-y-6">

        <div class="card bg-white border border-base-300/40 shadow-sm">
            <div class="card-body gap-5">

                <form id="expenseForm" method="post" action="{{ route('expenses.store') }}" class="flex flex-col gap-5"
                    enctype="multipart/form-data">
                    @csrf

                    {{-- Jumlah --}}
                    <div class="form-control gap-1" x-data="{
                        raw: '{{ old('amount', '') }}',
                    
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
                                class="absolute left-4 z-10 text-red-700 font-bold text-xl pointer-events-none select-none">Rp</span>

                            {{-- Input tampilan: dikelola Alpine, TIDAK punya name --}}
                            <input id="amount_display" type="text" inputmode="numeric" placeholder="0"
                                x-bind:value="formatted" x-on:input="onInput($event)" autocomplete="off"
                                class="input input-bordered focus:input-primary w-full h-16 pl-14 text-red-600 text-3xl font-extrabold placeholder:text-red-500/25 @error('amount') input-error @enderror" />

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
                        <div x-show="!previewUrl" class="relative">
                            <button type="button" @click="showMenu = !showMenu" @click.away="showMenu = false"
                                class="btn btn-outline border-base-300 border-dashed w-full h-24 flex flex-col items-center justify-center gap-1 hover:bg-base-200 hover:border-primary/50 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6 text-base-content/50">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Z" />
                                </svg>
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
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-5 text-primary">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Z" />
                                    </svg>
                                    Ambil dari Kamera
                                </button>
                                <button type="button" @click="$refs.galleryInput.click()"
                                    class="btn btn-ghost justify-start gap-3 w-full font-normal">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-5 text-primary">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                    </svg>
                                    Pilih dari Galeri
                                </button>
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
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                    Hapus
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
                            <span wire:loading.remove>Simpan Pengeluaran</span>
                            <span wire:loading>Menyimpan...</span>
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
            <span wire:loading.remove>Simpan</span>
            <span wire:loading>Menyimpan...</span>
        </button>
    </div>

</div>
