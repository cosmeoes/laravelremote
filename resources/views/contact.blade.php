<x-layout>
    <form action="{{ route('contact.store') }}" method="POST" id="contact" class="w-full px-2 mb-20 lg:w-1/2 lg:mx-auto lg:text-lx">
        <h2 class="my-10 text-2xl text-center">This section is under construction 🚧. If you want to post a job, you can contact me by filling out the form below, and I can add your post manually and send you an invoice.</h2>
        @csrf
        <div class="flex flex-wrap mb-2">
            <div class="w-full px-3 mb-6 md:mb-0">
                <label class="block mb-2 text-xs font-bold tracking-wide text-gray-700 uppercase" for="grid-first-name">
                    Name / Company
                </label>
                <input name="name" required class="block w-full px-4 py-3 mb-3 leading-tight text-gray-700 bg-gray-200 border border-gray-200 rounded appearance-none focus:outline-none focus:bg-white" id="grid-first-name" type="text">
            </div>
        </div>
        <div class="flex flex-wrap invisible h-0 mb-2">
            <div class="w-full px-3 mb-6 md:mb-0">
                <label class="block mb-2 text-xs font-bold tracking-wide text-gray-700 uppercase" for="grid-website">
                    Website
                </label>
                <input name="website" class="block w-full px-4 py-3 mb-3 leading-tight text-gray-700 bg-gray-200 border border-gray-200 rounded appearance-none focus:outline-none focus:bg-white" id="grid-website" type="text">
            </div>
        </div>
        <div class="flex flex-wrap mb-6">
            <div class="w-full px-3">
                <label class="block mb-2 text-xs font-bold tracking-wide text-gray-700 uppercase" for="email">
                    E-mail
                </label>
                <input name="email" required class="block w-full px-4 py-3 mb-3 leading-tight text-gray-700 bg-gray-200 border border-gray-200 rounded appearance-none focus:outline-none focus:bg-white focus:border-gray-500" id="email" type="email">
                <p class="text-xs italic text-gray-600">This is the email I'll use to contact you once I receive your message</p>
            </div>
        </div>
        <div class="flex flex-wrap mb-6">
            <div class="w-full px-3">
                <label class="block mb-2 text-xs font-bold tracking-wide text-gray-700 uppercase" for="message">
                    Message
                </label>
                <textarea form="contact" required name="message" class="block w-full h-48 px-4 py-3 mb-3 leading-tight text-gray-700 bg-gray-200 border border-gray-200 rounded appearance-none resize-none no-resize focus:outline-none focus:bg-white focus:border-gray-500" id="message"></textarea>
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
</x-layout>
