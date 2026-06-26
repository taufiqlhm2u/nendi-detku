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

    <style>
        /* Backdrop fade-in */
        #alert_modal::backdrop {
            animation: backdropFadeIn 0.25s ease forwards;
        }

        @keyframes backdropFadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        /* Mobile: slide up dari bawah */
        @media (max-width: 639px) {
            #alert_modal .modal-box {
                animation: slideUpMobile 0.35s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
            }

            @keyframes slideUpMobile {
                from {
                    opacity: 0;
                    transform: translateY(100%);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        }

        /* Desktop: scale + fade dari tengah */
        @media (min-width: 640px) {
            #alert_modal .modal-box {
                animation: popInDesktop 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
            }

            @keyframes popInDesktop {
                from {
                    opacity: 0;
                    transform: scale(0.85) translateY(-12px);
                }
                to {
                    opacity: 1;
                    transform: scale(1) translateY(0);
                }
            }
        }

        /* Animasi ikon muncul dengan bounce kecil */
        #alert_modal .alert-icon {
            animation: iconBounce 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) 0.15s both;
        }

        @keyframes iconBounce {
            from {
                opacity: 0;
                transform: scale(0.4);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Teks muncul sedikit terlambat */
        #alert_modal .alert-content {
            animation: contentFadeUp 0.3s ease 0.2s both;
        }

        @keyframes contentFadeUp {
            from {
                opacity: 0;
                transform: translateY(6px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <dialog id="alert_modal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box rounded-2xl px-6 py-8 max-w-sm mx-auto text-center">
            <span class="alert-icon material-symbols-outlined text-[52px] {{ $c['icon_class'] }}"
                style="font-variation-settings: 'FILL' 1; display: inline-block;">{{ $c['icon'] }}</span>
            <div class="alert-content">
                <h3 class="font-bold text-lg mt-3 text-base-content">{{ $c['title'] }}</h3>
                <p class="text-sm text-base-content/60 mt-2">{{ $alertMessage }}</p>
                <div class="mt-6">
                    <button onclick="alert_modal.close()"
                        class="btn-alert outline-0 border-0 w-full py-2.5 rounded-xl text-sm font-semibold transition-colors {{ $c['btn_class'] }}">
                        OK
                    </button>
                </div>
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