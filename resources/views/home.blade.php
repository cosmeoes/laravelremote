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

        <div class="w-full pt-10 mx-auto md:w-8/12">
            <h2 class="text-2xl font-bold text-black md:text-4xl">Remote Jobs</h2>
            <div class="flex flex-col mt-10 space-y-4">
                @foreach($jobPosts as $job)
                    <div x-data="{ open: false }">
                        <div class="flex items-center px-2 py-3 bg-white shadow-md cursor-pointer md:px-5 rounded-md" @click="open = !open">
                            <div class="flex items-center flex-1 space-x-6">
                                <div class="flex items-center justify-center flex-shrink-0 overflow-hidden rounded-xs" >
                            <span class="flex items-center justify-center w-full h-full text-2xl uppercase bg-white font-500">
                                <div class="flex items-center justify-center font-bold text-gray-400 border border-gray-300 rounded-lg w-14 h-14 font-2xl">{{\Str::initials($job->company)}}</div>
                            </span>
                                </div>
                                <div class="md:flex-shrink-0">
                                    <span x-data="{ tooltip: false }" @mouseover="tooltip = true" @mouseleave="tooltip = false" class="text-base font-extrabold text-black font-500 md:text-xl">
                                        {{ \Str::limit($job->position, 50) }}
                                        @if(strlen($job->position) > 50)
                                            <div x-show="tooltip" class="absolute p-2 text-xs text-white bg-gray-400 rounded-lg transform">{{ $job->position }}</div>
                                        @endif
                                    </span>
                                    <p class="mb-1 text-gray-900 font-400" data-text="true">{{ $job->company }}</p>
                                    <div class="flex flex-wrap font-bold md:flex-nowrap md:items-center md:space-x-4 md:space-y-0">
                                        @if($job->location)
                                            <span class="px-2 py-1 mb-2 text-xs text-gray-900 uppercase bg-black md:rounded-sm rounded-xs bg-opacity-10 md:mb-0">
                                                    🌏 {{ $job->location }}
                                            </span>
                                        @endif

                                        @if ($job->salary_range->isNotEmpty())
                                            <span class="px-2 py-1 mb-2 text-xs text-gray-900 uppercase bg-black md:rounded-sm rounded-xs bg-opacity-10 md:mb-0">
                                                    🤑 {{$job->salary_range}}
                                            </span>
                                        @endif

                                        @if($job->job_type)
                                            @foreach(explode(',', $job->job_type) as $jobType)
                                                <span class="px-2 py-1 mb-2 text-xs text-gray-900 uppercase bg-black md:rounded-sm rounded-xs bg-opacity-10 md:mb-0">
                                                    {{str_replace(['-', '_'], ' ', trim($jobType))}}
                                            </span>
                                                @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="md:flex-shrink-0 space-y-2 md:space-x-4">
                                <span class="text-sm text-gray-900" data-text="true">{{ $job->source_created_at->diffForHumans(null, \Carbon\CarbonInterface::DIFF_ABSOLUTE, true) }}</span>
                                <a class="relative inline-flex w-full border border-red-500 group focus:outline-none sm:w-auto" target="_blank" href="{{ $job->source_url }}">
        <span class="inline-flex items-center self-stretch justify-center w-full px-4 py-2 text-sm font-bold text-center text-white uppercase bg-red-500 ring-1 ring-red-500 ring-offset-1 ring-offset-red-500 transform transition-transform group-hover:-translate-y-1 group-hover:-translate-x-1 group-focus:-translate-y-1 group-focus:-translate-x-1">

            Apply
        </span>
                                </a>
                            </div>
                        </div>
                        <div x-show="open" class="px-5 py-2 -mt-1 bg-white shadow-md body">
                            {!! $job->body !!}
                        </div>
                    </div>
                @endforeach
                {{ $jobPosts->links() }}
            </div>
        </div>
    </div>
    <livewire:feedback-form />
</x-layout>
