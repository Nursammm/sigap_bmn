<div class="relative z-40 md:hidden" role="dialog" aria-modal="true" x-show="sidebarOpen" x-cloak>
    <div class="fixed inset-0 bg-gray-900/50" @click="sidebarOpen=false" x-transition.opacity></div>
    <div class="fixed inset-y-0 left-0 w-64 text-white shadow-xl" 
     style="background-color: #08376B;" 
     x-transition>

      <div class="flex h-16 items-center px-4">

        <div class="flex items-center justify-center h-30">
    <img class="w-20 h-20 object-contain" src="{{ asset('storage/gap2.png') }}" alt="Logo">
    </div>

        <button class="ml-auto rounded-md p-2 hover:bg-white/10" @click="sidebarOpen=false" aria-label="Tutup menu">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="size-6"><path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
      </div>

      <x-navbar></x-navbar>

      <!-- Profil (mobile) -->
      <div class="mt-auto border-t border-white/10 px-4 py-4">
        <div class="flex items-center">
          <img class="size-10 rounded-full outline -outline-offset-1 outline-white/10" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=facearea&facepad=2&w=128&h=128&q=80" alt="Avatar">
          <div class="ml-3">
            <p class="text-sm font-medium">Nursam</p>
            <p class="text-xs text-gray-300">nursam@gmail.com</p>
          </div>
        </div>
        <div class="mt-3 space-y-1">
          <a href="#" class="block rounded-md px-3 py-2 text-sm text-gray-200 hover:bg-white/10">Your profile</a>
          <a href="#" class="block rounded-md px-3 py-2 text-sm text-gray-200 hover:bg-white/10">Settings</a>
          <a href="#" class="block rounded-md px-3 py-2 text-sm text-gray-200 hover:bg-white/10">Sign out</a>
        </div>
      </div>
    </div>
  </div>
