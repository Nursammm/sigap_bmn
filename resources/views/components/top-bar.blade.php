  <div class="sticky top-0 z-30 flex h-16 items-center gap-2 bg-gray-800 px-4 text-white md:hidden">
    <button @click="sidebarOpen = !sidebarOpen" class="rounded-md p-2 text-gray-300 hover:bg-white/10 hover:text-white focus:outline-2 focus:outline-offset-2 focus:outline-indigo-500" aria-label="Buka menu">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="size-6"><path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </button>
    <div class="font-semibold">{{ $slot }}</div>
    <div class="ml-auto">
    </div>
  </div>
