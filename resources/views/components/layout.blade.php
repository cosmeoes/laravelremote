<x-app>
    <x-header></x-header>

    @if(session()->has('success'))
        <p class="text-white mt-2 ml-2 text-center text-base fixed top-0 left-0 bg-green-500 rounded p-2"  x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">{{ session('success') }}</p>
    @endif
    {{ $slot }}
</x-app>
