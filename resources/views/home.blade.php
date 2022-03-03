<x-layout>
    <div class="mx-auto mb-20">
        <div class="bg-gray-200">
            <div class="flex justify-center">
                <form class="p-4" method="post" action="{{ route('email.subscribe') }}">
                    @csrf
                    Get a
                    <select name="time" class="px-2 py-1 bg-white rounded-full text-bold">
                        <option value="daily">daily</option>
                        <option value="weekly">weekly</option>
                    </select>
                    email of all new Jobs
                    <input name="email" type="email" placeholder="Type your email..." class="w-full px-2 py-1 mt-2 bg-white rounded-full text-bold md:w-auto md:mt-0" required/>
                    <button type="submit" class="inline-flex w-full mt-2 border border-red-500 rounded-full group focus:outline-none sm:w-auto md:mt-0">
        <span class="inline-flex items-center self-stretch justify-center w-full px-4 py-2 text-sm font-bold text-center text-white uppercase bg-red-500 rounded-full ring-1 ring-red-500 ring-offset-1 ring-offset-red-500 transform transition-transform group-hover:-translate-y-1 group-hover:-translate-x-1 group-focus:-translate-y-1 group-focus:-translate-x-1">
            Subscribe
        </span>
                    </button>
                </form>
            </div>
            @error('email')
            <p class="fixed top-0 left-0 p-2 mt-2 ml-2 text-base text-center text-white bg-red-500 rounded"  x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">{{ $message }}</p>
            @enderror
            @error('time')
            <p class="fixed top-0 left-0 p-2 mt-2 ml-2 text-base text-center text-white bg-red-500 rounded"  x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">{{ $message }}</p>
            @enderror
        </div>

        <div class="w-full pt-10 mx-auto lg:w-8/12 md:w-11/12">
            <h2 class="text-2xl font-bold text-black md:text-4xl">Remote Jobs</h2>
            <livewire:job-post-list />
        </div>
    </div>
    <livewire:feedback-form />
</x-layout>
