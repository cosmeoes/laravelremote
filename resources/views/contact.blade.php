<x-layout>
    <form action="{{ route('contact.store') }}" method="POST" id="contact" class="w-full px-2 lg:w-1/2 lg:mx-auto lg:text-lx mb-20">
        <h2 class="text-center my-10 text-2xl">This section is under construction 🚧. If you want to post a job, you can contact me by filling out the form below, and I can add your post manually and send you an invoice.</h2>
        @csrf
        <div class="flex flex-wrap mb-2">
            <div class="w-full px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-first-name">
                    Name / Company
                </label>
                <input name="name" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="grid-first-name" type="text">
            </div>
        </div>
        <div class="flex flex-wrap mb-2 invisible h-0">
            <div class="w-full px-3 mb-6 md:mb-0">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-website">
                    Website
                </label>
                <input name="website" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white" id="grid-website" type="text">
            </div>
        </div>
        <div class="flex flex-wrap mb-6">
            <div class="w-full px-3">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="email">
                    E-mail
                </label>
                <input name="email" required class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" id="email" type="email">
                <p class="text-gray-600 text-xs italic">This is the email I'll use to contact you once I receive your message</p>
            </div>
        </div>
        <div class="flex flex-wrap mb-6">
            <div class="w-full px-3">
                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="message">
                    Message
                </label>
                <textarea form="contact" required name="message" class="no-resize appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white focus:border-gray-500 h-48 resize-none" id="message"></textarea>
            </div>
        </div>
        <div class="md:flex items-center">
            <button type="submit" class="group relative inline-flex border border-red-500 focus:outline-none w-full sm:w-auto mx-auto">
        <span class="w-full inline-flex items-center justify-center self-stretch px-4 py-2 text-sm text-white text-center font-bold uppercase bg-red-500 ring-1 ring-red-500 ring-offset-1 ring-offset-red-500 transform transition-transform group-hover:-translate-y-1 group-hover:-translate-x-1 group-focus:-translate-y-1 group-focus:-translate-x-1">
            Send
        </span>
            </button>
        </div>
    </form>
</x-layout>
