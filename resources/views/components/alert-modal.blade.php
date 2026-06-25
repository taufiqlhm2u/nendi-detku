{{-- Alert Modal --}}
@php
    $alertType = null;
    $alertMessage = null;

    if (session('success')) {
        $alertType = 'success';
        $alertMessage = session('success');
    } elseif (session('error')) {
        $alertType = 'error';
        $alertMessage = session('error');
    } elseif (session('warning')) {
        $alertType = 'warning';
        $alertMessage = session('warning');
    } elseif (session('info')) {
        $alertType = 'info';
        $alertMessage = session('info');
    }
@endphp

@if ($alertType)
    @php
        $config = [
            'success' => [
                'icon' => 'check_circle',
                'title' => 'Berhasil',
                'icon_class' => 'text-emerald-500',
                'btn_class' => 'bg-emerald-500 hover:bg-emerald-600 text-white border border-emerald-500 hover:border-emerald-600',
            ],
            'error' => [
                'icon' => 'cancel',
                'title' => 'Gagal',
                'icon_class' => 'text-red-500',
                'btn_class' => 'bg-red-500 hover:bg-red-600 text-white border border-red-500 hover:border-red-600',
            ],
            'warning' => [
                'icon' => 'warning',
                'title' => 'Perhatian',
                'icon_class' => 'text-amber-500',
                'btn_class' => 'bg-amber-500 hover:bg-amber-600 text-white border border-amber-500 hover:border-amber-600',
            ],
            'info' => [
                'icon' => 'info',
                'title' => 'Informasi',
                'icon_class' => 'text-blue-500',
                'btn_class' => 'bg-blue-500 hover:bg-blue-600 text-white border border-blue-500 hover:border-blue-600',
            ],
        ];
        $c = $config[$alertType];
    @endphp

    <dialog id="alert_modal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box rounded-2xl px-6 py-8 max-w-sm mx-auto text-center">
            <span class="material-symbols-outlined text-[52px] {{ $c['icon_class'] }}"
                style="font-variation-settings: 'FILL' 1;">{{ $c['icon'] }}</span>
            <h3 class="font-bold text-lg mt-3 text-base-content">{{ $c['title'] }}</h3>
            <p class="text-sm text-base-content/60 mt-2">{{ $alertMessage }}</p>
            <div class="mt-6">
                <button onclick="alert_modal.close()"
                    class="btn-alert outline-0 border-0 w-full py-2.5 rounded-xl text-sm font-semibold transition-colors {{ $c['btn_class'] }}">
                    OK
                </button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            alert_modal.showModal();
        });
    </script>
@endif