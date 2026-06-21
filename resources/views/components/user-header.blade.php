 @php
     $authUser = Auth::user();
     $avatarUrl = $authUser->avatar ? Storage::url($authUser->avatar) : null;
     // Ambil inisial: maks 2 huruf dari kata pertama & kedua nama
     $nameParts = explode(' ', trim($authUser->name));
     $initials = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));
 @endphp
 <header
     class="w-full sticky top-0 z-50 bg-[#f9f9ff] border-b border-[#e1e2e9] flex justify-between items-center px-4 py-3 shadow-sm">
     <div class="flex items-center gap-3">
         @if ($avatarUrl)
             <div class="avatar">
                 <div class="w-10 h-10 rounded-full ring ring-primary ring-offset-1 overflow-hidden">
                     <img alt="Foto Profil" src="{{ $avatarUrl }}"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                     {{-- fallback inisial jika URL gagal dimuat --}}
                     <div class="w-10 h-10 rounded-full ring ring-primary ring-offset-1 bg-primary text-white text-sm font-bold items-center justify-center"
                         style="display:none">
                         {{ $initials }}
                     </div>
                 </div>
             </div>
         @else
             {{-- Tidak ada avatar: tampilkan inisial --}}
             <div
                 class="w-10 h-10 rounded-full ring ring-primary ring-offset-1 bg-primary text-white text-sm font-bold flex items-center justify-center shrink-0 select-none">
                 {{ $initials }}
             </div>
         @endif
         <div>
             <p class="text-xs font-semibold text-primary tracking-wider uppercase">Halo,</p>
             <h1 class="md:text-lg md:font-bold sm:text-md sm:font-semibold text-base-content leading-tight">{{ $authUser->name }}!</h1>
         </div>
     </div>
 </header>
