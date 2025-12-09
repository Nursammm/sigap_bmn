<aside 
    class="hidden md:fixed md:inset-y-0 md:flex md:flex-col transition-all duration-300 shadow-xl"
    style="background-color:#08376B;"
    :class="sidebarOpen ? 'md:w-64' : 'md:w-0 overflow-hidden'">

    <div class="flex h-full flex-col text-white">

        <div>
            <div class="flex items-center justify-center h-28 py-6">
                <img class="w-40 h-40 object-contain" src="{{ asset('storage/gap2.png') }}" alt="Logo">
            </div>

            <div class="px-4 mt-2 pb-6 space-y-1 overflow-y-auto">
                <x-navbar />
            </div>
        </div>

        <div class="mt-auto"></div>

        <div class="px-4 mb-2">
            <x-notifications.bell-deletion />
        </div>

        <div class="border-t border-white/10">
            <div class="px-4 py-3">
                <div class="relative" x-data="{ open:false }" @keydown.escape.stop="open=false">
                    <button 
                        @click="open=!open" 
                        :aria-expanded="open" 
                        class="flex items-center gap-3 w-full rounded-md px-3 py-2 hover:bg-white/10 transition focus:outline-none">
                        <img class="w-10 h-10 rounded-full outline -outline-offset-1 outline-white/10" 
                             src="{{ Auth::user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=08376B&color=fff' }}" 
                             alt="Avatar">
                        <div class="text-left">
                            <div class="text-sm font-medium leading-tight">{{ Auth::user()->name }}</div>
                            <div class="text-xs text-gray-300">{{ Auth::user()->email }}</div>
                        </div>
                        <svg class="ml-auto w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M6 9l6 6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>

                    <div 
                        x-cloak 
                        x-show="open" 
                        x-transition.opacity.duration.100ms 
                        @click.outside="open=false"
                        class="absolute bottom-14 left-2 w-52 rounded-md bg-white py-1 text-gray-800 shadow-lg outline-1 outline-black/5">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Your profile</a>
                        @admin
                        <a href="{{ route('users.index') }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Users</a>
                        @endadmin
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left block px-4 py-2 text-sm hover:bg-gray-100">
                                Sign out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</aside>
