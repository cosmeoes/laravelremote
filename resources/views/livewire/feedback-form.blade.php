<div class="fixed px-2 py-2 bg-white border border-gray-200" :class="open ? 'md:bottom-0 md:right-0 md:top-auto top-0 md:w-auto md:h-auto w-screen h-full' : 'bottom-0 right-0'" x-data="{ open: false }">
    <div class="flex flex-wrap flex-none mb-2 cursor-pointer" @click="open = !open">
        <div class="flex w-full px-3 md:mb-0 space-x-2">
            <p class="font-bold">Have feedback? Need help?</p>
            <svg x-show="!open" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path></svg>
            <svg x-show="open" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
        </div>
    </div>
    <form x-show="open" wire:submit.prevent="submit" class="flex flex-col flex-1 space-y-2">
        @if(session()->has('message')) 
            <p class="px-2 text-base text-center text-white bg-green-500 rounded" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                {{session('message')}}
            </p>
        @endif
            @csrf
            <div class="mb-2">
                <div class="w-full px-3 md:mb-0">
                    <label class="block mb-2 text-xs font-bold tracking-wide text-gray-700 uppercase" for="grid-first-name">
                        Name
                    </label>
                    <input name="name" wire:model="name" required class="block w-full py-3 mb-3 leading-tight text-gray-700 bg-gray-200 border border-gray-200 rounded appearance-none md:px-4 focus:outline-none focus:bg-white" id="name" type="text">
                    @error('name') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mb-2">
                <div class="w-full px-3">
                    <label class="block mb-2 text-xs font-bold tracking-wide text-gray-700 uppercase" for="email">
                        E-mail
                    </label>
                    <input name="email" wire:model="email"required class="block w-full py-3 mb-3 leading-tight text-gray-700 bg-gray-200 border border-gray-200 rounded appearance-none md:px-4 focus:outline-none focus:bg-white focus:border-gray-500" id="email" type="email">
                    @error('email') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="mb-2">
                <div class="w-full px-3">
                    <label class="block mb-2 text-xs font-bold tracking-wide text-gray-700 uppercase" for="message">
                        Message
                    </label>
                    <textarea form="contact" required name="message" wire:model="message" class="block w-full h-48 py-3 mb-3 leading-tight text-gray-700 bg-gray-200 border border-gray-200 rounded appearance-none resize-none md:px-4 no-resize focus:outline-none focus:bg-white focus:border-gray-500" id="message"></textarea>
                    @error('message') <span class="text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="items-center md:flex">
                <button type="submit" class="relative inline-flex w-full mx-auto border border-red-500 group focus:outline-none sm:w-auto">
            <span class="inline-flex items-center self-stretch justify-center w-full px-4 py-2 text-sm font-bold text-center text-white uppercase bg-red-500 ring-1 ring-red-500 ring-offset-1 ring-offset-red-500 transform transition-transform group-hover:-translate-y-1 group-hover:-translate-x-1 group-focus:-translate-y-1 group-focus:-translate-x-1">
                Send
            </span>
                </button>
            </div>
    </form>
</div>
