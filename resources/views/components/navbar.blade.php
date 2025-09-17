<x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" icon="home">
    Dashboard
</x-nav-link>

<x-nav-link href="{{ route('barang.index') }}" :active="request()->routeIs('barang.*')" icon="box">
    Barang
</x-nav-link>

<x-nav-link href="{{ route('laporan') }}" :active="request()->routeIs('laporan.*')" icon="report">
    Laporan
</x-nav-link>

<x-nav-link href="{{ route('mutasi.index') }}" :active="request()->routeIs('mutasi.*')" icon="mutasi">
    History
</x-nav-link>
