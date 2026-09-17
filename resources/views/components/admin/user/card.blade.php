<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $role = '';

    #[Computed]
    public function users()
    {
        return User::when(filled($this->search), function($q){
                    $q->where('email', 'LIKE', '%' . $this->search . '%')
                    ->orWhere('name', 'LIKE', '%' . $this->search . '%');
                })
                ->when(filled($this->role) && $this->role !== '', function($q2){
                    $q2->where('role', $this->role);
                })
                ->whereNot('id', Auth::id())
                ->paginate(10);
    }

    public function resetFilter()
    {
        return $this->reset(['search', 'role']);
    }
};
?>

<div>
    {{-- Page Header: Riwayat --}}
    <main class="px-4 pt-4 space-y-5 max-w-xl mx-auto relative">
        {{-- Global Loading Overlay --}}
        <div wire:loading wire:target="setFilter, filterMonth, filterYear"
            class="absolute inset-0 z-50 bg-base-100/50 backdrop-blur-sm rounded-3xl h-screen">
            <div class="flex justify-center w-full h-screen items-center">
                <span class="loading loading-spinner loading-lg text-primary"></span>
            </div>
        </div>

        <div class="flex justify-between">
            <h1 class="text-xl font-bold tracking-tight">Data user</h1>
            <a href="{{ route('admin.user.create') }}">
                <button class="btn bg-blue-500 btn-sm text-white">Tambah</button>
            </a>
        </div>


        {{-- Search --}}
        <div class="flex justify-between w-full! flex-gap gap-2 space-y-3 page-fade" style="--delay: 0s">
            <label class="input">
                <svg class="h-[1em] opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <g stroke-linejoin="round" stroke-linecap="round" stroke-width="2.5" fill="none"
                        stroke="currentColor">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.3-4.3"></path>
                    </g>
                </svg>
                <input type="search" placeholder="Caru user..." wire:model.live.debounce.300ms="search" />
            </label>
            <div>
                <select wire:model.live="role"
                    class="select select-bordered select-sm w-30 bg-white focus:outline-none focus:border-primary font-semibold text-[#4b5f80]">
                    <option value="">Semua role</option>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
            </div>
        </div>

        {{-- Transaction List --}}
        <div class="space-y-6 pb-12 page-fade" style="--delay: 0.1s">

            @forelse ($this->users as $user)
                <div
                    class="group flex items-center gap-3 p-3.5 bg-white rounded-2xl border border-transparent hover:border-primary/30 shadow-sm transition-all">

                    {{-- Icon --}}
                    <div
                        class="w-12 h-12 shrink-0 flex items-center justify-center rounded-xl bg-primary/10 text-primary">
                        <span class="material-symbols-outlined">person</span>
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm truncate">{{ $user->name }}</p>
                        <p class="text-xs text-[#191c21]/40 truncate">
                            {{ $user->email }}
                        </p>
                    </div>

                    {{-- Role --}}
                    @if ($user->role === 'admin')
                        <div class="badge badge-outline badge-success">{{ ucfirst($user->role) }}</div>
                    @else
                        <div class="badge badge-outline badge-warning">{{ ucfirst($user->role) }}</div>
                    @endif

                    {{-- Actions --}}
                    <div class="text-right flex items-center gap-1">
                        <a href="{{ route('admin.user.edit', $user->id) }}">
                            <button class="p-2 text-primary hover:bg-primary/10 rounded-xl transition-colors">
                            <span class="material-symbols-outlined text-[20px]">edit</span>
                        </button>
                        </a>
                        <button class="p-2 text-rose-600 hover:bg-rose-50 rounded-xl transition-colors"
                            onclick="delete_user_modal_{{ $user->id }}.showModal()">
                            <span class="material-symbols-outlined text-[20px]">delete</span>
                        </button>
                    </div>
                </div>

                <dialog id="delete_user_modal_{{ $user->id }}" class="modal modal-bottom sm:modal-middle">
                    <div class="modal-box rounded-2xl px-6 py-8 max-w-sm mx-auto text-center">

                        {{-- Icon --}}
                        <div class="w-14 h-14 rounded-2xl bg-red-50 flex items-center justify-center mx-auto mb-4">
                            <span class="material-symbols-outlined text-[32px] text-red-500"
                                style="font-variation-settings: 'FILL' 1;">delete_forever</span>
                        </div>

                        <h3 class="font-bold text-lg text-base-content">Hapus Akun User?</h3>
                        <p class="text-sm text-base-content/50 mt-2 leading-relaxed">
                            Tindakan ini <span class="font-semibold text-red-500">tidak dapat dibatalkan</span>.
                            Seluruh data akun dan transaksi User akan dihapus secara permanen.
                        </p>

                        {{-- Form DELETE --}}
                        <form action="{{ route('admin.user.destroy', $user->id) }}" method="POST"
                            class="mt-6 flex flex-col gap-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="btn bg-red-500 hover:bg-red-600 text-white w-full h-11 text-sm font-semibold rounded-xl active:scale-95 transition-transform border-none">
                                Ya, Hapus
                            </button>
                            <button type="button" onclick="delete_user_modal_{{ $user->id }}.close()"
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
            @empty
                {{-- Empty State --}}
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-base-200 flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-3xl text-[#191c21]/30">group</span>
                    </div>
                    <p class="font-semibold text-sm text-[#191c21]/60">Tidak ada user</p>
                    <p class="text-xs text-[#191c21]/40 mt-1">
                        Belum ada user dalam sistem ini
                    </p>
                </div>
            @endforelse

            <div class="pt-4">
                {{ $this->users->links() }}
            </div>

        </div>
    </main>
</div>
