<x-app>
    <header class="p-2 bg-white">
        <div class="flex">
            <div class="flex-1 text-center">
                <h1 class="max-w-3xl mx-auto text-5xl font-bold text-center md:text-6xl lg:text-7xl text-red-500 font-sans">Laravel <span class="text-gray-900 font-title lowercase">Remote</span></h1>
            </div>
            <div class="flex-none flex">
                <div class="m-auto">
                    <a class="group relative inline-flex border border-red-500 focus:outline-none w-full sm:w-auto" href="">
    <span class="w-full inline-flex items-center justify-center self-stretch px-4 py-2 text-sm text-white text-center font-bold uppercase bg-red-500 ring-1 ring-red-500 ring-offset-1 ring-offset-red-500 transform transition-transform group-hover:-translate-y-1 group-hover:-translate-x-1 group-focus:-translate-y-1 group-focus:-translate-x-1">
        Post a job
    </span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="mx-auto">
        <div class="bg-gray-200">
            <div class="flex justify-center">
                <form class="p-4" method="post" action="{{ route('email.subscribe') }}">
                    @csrf
                    Get a
                    <select name="time" class="rounded-full text-bold bg-white px-2 py-1">
                        <option value="daily">daily</option>
                        <option value="daily">weekly</option>
                    </select>
                    email of all new Jobs
                    <input name="email" type="email" placeholder="Type your email..." class="rounded-full text-bold bg-white px-2 py-1 md:w-auto md:mt-0 mt-2 w-full" required/>
                    <button type="submit" class="inline-flex border border-red-500 rounded-full focus:outline-none w-full sm:w-auto md:mt-0 mt-2">
    <span class="w-full inline-flex items-center justify-center rounded-full self-stretch px-4 py-2 text-sm text-white text-center font-bold uppercase bg-red-500 ring-1 ring-red-500 ring-offset-1 ring-offset-red-500 transform transition-transform group-hover:-translate-y-1 group-hover:-translate-x-1 group-focus:-translate-y-1 group-focus:-translate-x-1">
        Subscribe
    </span>
                    </button>
                </form>
            </div>
            @error('email')
            <p class="text-white mt-2 ml-2 text-center text-base fixed top-0 left-0 bg-red-500 rounded p-2"  x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">{{ $message }}</p>
            @enderror
            @error('time')
            <p class="text-white mt-2 ml-2 text-center text-base fixed top-0 left-0 bg-red-500 rounded p-2"  x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">{{ $message }}</p>
            @enderror

            @if(session()->has('success'))
                <p class="text-white mt-2 ml-2 text-center text-base fixed top-0 left-0 bg-green-500 rounded p-2"  x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">{{ session('success') }}</p>
            @endif
        </div>

        <div class="md:w-8/12 w-full mx-auto pt-10">
            <h2 class="md:text-4xl text-2xl font-bold text-black">Remote Jobs</h2>
            <div class="flex flex-col space-y-4 mt-10">
                @foreach($jobPosts as $job)
                    <div x-data="{ open: false }">
                        <div class="flex shadow-md md:px-5 px-2 py-3 items-center bg-white rounded-md cursor-pointer" @click="open = !open">
                            <div class="flex space-x-6 items-center flex-1">
                                <div class="rounded-xs flex-shrink-0 overflow-hidden flex items-center justify-center" >
                        <span class="bg-white font-500 flex items-center justify-center w-full h-full text-2xl uppercase">
                            <div class="rounded-lg w-14 h-14 flex items-center justify-center border border-gray-300 text-gray-400 font-bold font-2xl">{{\Str::initials($job->company)}}</div>
                        </span>
                                </div>
                                <div class="md:flex-shrink-0">
                                    <span class="font-500 md:text-xl text-base text-black whitespace-pre-wrap font-extrabold">{{ $job->position }}</span>
                                    <p class="font-400 mb-1 text-gray-900" data-text="true">{{ $job->company }}</p>
                                    <div class="flex md:flex-nowrap flex-wrap md:items-center md:space-x-4 md:space-y-0 space-y-2">
                                        @if($job->location)
                                            <span class="px-2 py-1 text-xs uppercase md:rounded-sm rounded-xs bg-opacity-10 bg-black text-gray-900">
                                                🌏 {{ $job->location }}
                                        </span>
                                        @endif

                                        @if ($job->salary_max)
                                            <span class="px-2 py-1 text-xs uppercase md:rounded-sm rounded-xs bg-opacity-10 bg-black text-gray-900">
                                                🤑 $@money($job->salary_min) - $@money($job->salary_max)
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="md:flex-shrink-0 space-y-2 md:space-x-4">
                                <span class="text-sm text-gray-900" data-text="true">{{ $job->source_created_at->diffForHumans(null, \Carbon\CarbonInterface::DIFF_ABSOLUTE, true) }}</span>
                                <a class="group relative inline-flex border border-red-500 focus:outline-none w-full sm:w-auto" target="_blank" href="{{ $job->source_url }}">
    <span class="w-full inline-flex items-center justify-center self-stretch px-4 py-2 text-sm text-white text-center font-bold uppercase bg-red-500 ring-1 ring-red-500 ring-offset-1 ring-offset-red-500 transform transition-transform group-hover:-translate-y-1 group-hover:-translate-x-1 group-focus:-translate-y-1 group-focus:-translate-x-1">
        Apply
    </span>
                                </a>
                            </div>
                        </div>
                        <div x-show="open" class="bg-white px-5 py-2 -mt-1 shadow-md body">
                            {!! utf8_decode($job->body) !!}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app>
