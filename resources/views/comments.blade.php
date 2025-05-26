<x-app-layout>
    <div class="w-[1200px] h-[600px] mx-auto flex justify-center items-center mt-10">
        <iframe src="https://www.youtube.com/embed/{{ $videoId }}?rel=0&showinfo=0" frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen style="width: 900px; height: 550px;">
        </iframe>
    </div>


    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-8">
            <div class="p-4">
                <h1 class="text-xl font-bold text-gray-900 mb-2">{{ $videoInfo['snippet']['title'] }}</h1>
                <p class="text-gray-700 whitespace-pre-line">
                    {{ $videoInfo['snippet']['description'] }}
                </p>
                <div class="mt-4 flex items-center text-sm text-gray-500">
                    <span class="mr-4">
                        {{ number_format($videoInfo['statistics']['viewCount']) }} переглядів
                    </span>
                    <span class="mr-4">
                        {{ number_format($videoInfo['statistics']['likeCount']) }} вподобань
                    </span>
                    <span>
                        {{ \Carbon\Carbon::parse($videoInfo['snippet']['publishedAt'])->format('d.m.Y') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <h2 class="text-xl font-bold text-gray-900">
                Коментарі ({{ number_format($videoInfo['statistics']['commentCount']) }})
            </h2>
            <div id="comments-container">
                @include('partials.comments', ['comments' => $comments])
            </div>
        </div>
    </div>
</x-app-layout>