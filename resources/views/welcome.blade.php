<x-layout>
    <div class="min-h-screen bg-base-100 flex items-center justify-center">
        <div class="relative w-full max-w-sm mx-auto px-4 py-8">
            
            <!-- Subtle gradient background -->
            <div class="absolute inset-0 bg-gradient-to-br from-primary/5 via-transparent to-secondary/5 rounded-3xl -z-10"></div>
            
            <!-- Main content -->
            <div class="flex flex-col items-center gap-8">
                
                <!-- Logo -->
                <div class="avatar">
                    <div class="w-20 h-20 rounded-full ring ring-base-300 ring-offset-2">
                        <img src="{{ asset('images/logo.png') }}" alt="Nendi Detku Logo" class="object-cover">
                    </div>
                </div>
                
                <!-- Brand -->
                <div class="text-center space-y-2">
                    <h1 class="text-3xl font-bold tracking-tight">
                        Nendi Detku
                    </h1>
                    <p class="text-sm text-base-content/60 tracking-wide">
                        Catat. Pantau. Kendali.
                    </p>
                </div>
                
                <!-- Loading -->
                <div class="w-full max-w-xs pt-4">
                    <progress class="progress progress-primary w-full h-1.5" id="loading-bar" value="0" max="100"></progress>
                </div>
                
                <!-- Footer -->
                <p class="text-xs text-base-content/30 tracking-widest pt-8">
                    v{{config('app.version')}}
                </p>
                
            </div>
            
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const bar = document.getElementById('loading-bar');
            let progress = 0;
            
            const interval = setInterval(() => {
                progress += Math.random() * 12 + 3;
                if (progress >= 100) {
                    progress = 100;
                    clearInterval(interval);
                    setTimeout(() => {
                        window.location.href = "{{ route('beranda') }}";
                    }, 400);
                }
                bar.value = progress;
            }, 120);
        });
    </script>
</x-layout>