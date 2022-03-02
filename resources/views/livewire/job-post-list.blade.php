<div class="flex flex-col mt-2 space-y-4">
    <div class="flex space-x-2">
        @foreach($tags as $id => $tag)
            <span @click="$wire.removeTag({{$id}})" class="px-2 py-1 mb-2 text-base font-semibold border-2 border-gray-900 cursor-pointer rounded-xl text-gray-black md:mb-0 hover:text-white hover:bg-red-500">
                {{ $tag['name'] }} x</span>
        @endforeach
    </div>
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
                <div class="flex-wrap justify-center flex-1 hidden space-x-3 md:flex">
                    @foreach($job->tags->filter(fn($tag) => !isset($tags[$tag->id]) )->splice(0, 5) as $tag)
                        <span x-data="{ tooltip: false }" class="">
                            <span @mouseover="tooltip = true" @mouseleave="tooltip = false" @click.stop="$wire.addTag(@js($tag->only('id', 'name')))" class="px-2 py-1 mb-2 text-base font-semibold border-2 border-gray-900 rounded-xl text-gray-black md:mb-0 hover:text-white hover:bg-gray-900">{{ $tag->name }}</span>
                            <div x-show="tooltip" class="absolute items-center p-2 text-xs leading-tight text-white bg-red-500 rounded-lg transform translate-y-1/4">
                                <svg class="absolute z-10 w-6 h-6 text-red-500 fill-current stroke-current transform -translate-y-1/2" width="8" height="8">
                                    <rect x="12" y="-10" width="8" height="8" transform="rotate(45)" />
                                </svg>
                                Add to filters 
                            </div>
                        </span>
                    @endforeach
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
    {{ $jobPosts->links('vendor.pagination.simple-tailwind') }}
</div>
