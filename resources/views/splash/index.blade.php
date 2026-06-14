<x-layout.app>
    <div class="bg-base-100 text-base-content overflow-hidden min-h-screen">
        <main class="flex flex-col items-center justify-center min-h-screen w-full relative">

            <!-- Background blobs -->
            <div class="absolute inset-0 pointer-events-none overflow-hidden">
                <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-primary/5 rounded-full blur-[100px] animate-pulse"></div>
                <div class="absolute -bottom-[10%] -right-[10%] w-[50%] h-[50%] bg-secondary/10 rounded-full blur-[120px] animate-pulse"
                    style="animation-delay: 2s"></div>
            </div>

            <!-- Logo + Brand -->
            <div class="flex flex-col items-center justify-center gap-4 animate-reveal z-10">
                <div class="relative group">
                    <div class="absolute inset-0 bg-primary/20 rounded-full blur-xl scale-110 group-hover:scale-125 transition-transform duration-700 animate-pulse-soft"></div>
                    <div class="avatar placeholder relative z-10">
                        <div class="w-24 sm:w-32 rounded-full bg-primary text-primary-content ring ring-white/10 ring-offset-0 flex items-center justify-center overflow-hidden">
                            <img src="{{ asset('images/logo.png') }}" alt="Nendi Detku Logo" class="w-full h-full object-cover">
                        </div>
                    </div>
                </div>

                <div class="text-center space-y-2 pt-2">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-primary relative inline-block">
                        Nendi Detku
                        <span class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-3/4 h-1 bg-accent rounded-full"></span>
                    </h1>
                    <p class="text-xs tracking-[0.2em] text-base-content/60 font-semibold uppercase pt-4">
                        Catat. Pantau. Kendali.
                    </p>
                </div>
            </div>

            <!-- Loading progress -->
            <div class="absolute bottom-16 w-32">
                <progress class="progress progress-primary w-full h-1" id="loading-bar" value="0" max="100"></progress>
            </div>

            <!-- Footer -->
            <footer class="absolute bottom-8 opacity-40">
                <p class="text-xs font-semibold tracking-widest">v1.0.4 Premium Experience</p>
            </footer>
        </main>
    </div>

    <style>
        @keyframes pulse-soft {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.9; }
        }
        @keyframes slide-up {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        @keyframes pulse {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 0.6; }
        }
        .animate-pulse-soft {
            animation: pulse-soft 3s ease-in-out infinite;
        }
        .animate-reveal {
            animation: slide-up 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }
        .animate-pulse {
            animation: pulse 4s ease-in-out infinite;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const bar = document.getElementById('loading-bar');
            let progress = 0;
            const interval = setInterval(() => {
                progress += Math.random() * 15;
                if (progress >= 100) {
                    progress = 100;
                    clearInterval(interval);
                    setTimeout(() => { 
                        bar.parentElement.style.opacity = '0'; 
                        bar.parentElement.style.transition = 'opacity 0.5s';
                        // Redirect ke halaman login setelah loading selesai
                        setTimeout(() => {
                            window.location.href = "{{ route('login') }}";
                        }, 500);
                    }, 500);
                }
                bar.value = progress;
            }, 150);
        });

        // Efek parallax ringan saat mouse bergerak
        document.addEventListener('mousemove', (e) => {
            const moveX = (e.clientX - window.innerWidth / 2) * 0.01;
            const moveY = (e.clientY - window.innerHeight / 2) * 0.01;
            const logo = document.querySelector('.animate-reveal');
            if (logo) logo.style.transform = `translate(${moveX}px, ${moveY}px)`;
        });
    </script>
</x-layout.app>