<aside 
    class="hidden md:fixed md:inset-y-0 md:flex md:flex-col transition-all duration-300"
    style="background-color: #08376B;"
    :class="sidebarOpen ? 'md:w-64' : 'md:w-0 overflow-hidden'">
    <div class="flex grow flex-col">
        
        <!-- Logo -->
 
    <div class="flex items-center justify-center h-25">
        <img class="w-30 h-30 object-contain" src="{{ asset('storage/gap2.png') }}" alt="Logo">
    </div>

        <!-- Navigasi Utama -->
        <x-navbar></x-navbar>

        <!-- Profil (desktop) -->
        <div class="border-t border-white/10 p-4 text-white">
            <div class="relative" x-data="{ open:false }" @keydown.escape.stop="open=false">
                <button 
                    @click="open=!open" 
                    :aria-expanded="open" 
                    class="flex w-full items-center gap-3 rounded-md p-2 hover:bg-white/10 focus:outline-2 focus:outline-offset-2 focus:outline-indigo-500">
                    <img class="size-10 rounded-full outline -outline-offset-1 outline-white/10" 
                         src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=facearea&facepad=2&w=128&h=128&q=80" 
                         alt="Avatar">
                    <div class="text-left">
                        <div class="text-sm font-medium">Nursam</div>
                        <div class="text-xs text-gray-300">nursam@gmail.com</div>
                    </div>
                    <svg class="ml-auto size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M6 9l6 6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>

                <!-- Dropdown Profile -->
                <div 
                    x-cloak 
                    x-show="open" 
                    x-transition.opacity.duration.100ms 
                    @click.outside="open=false"
                    class="absolute bottom-14 left-2 w-48 rounded-md bg-white py-1 text-gray-800 shadow-lg outline-1 outline-black/5">
                    <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-100">Your profile</a>
                    <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-100">Settings</a>
                    <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-100">Sign out</a>
                </div>
            </div>
        </div>
    </div>
</aside>
