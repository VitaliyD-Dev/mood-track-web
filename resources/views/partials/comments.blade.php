{{-- Tailwind safelist for emotion colors --}}
<span class="hidden
    bg-green-100 text-green-800
    bg-blue-100 text-blue-800
    bg-red-100 text-red-800
    bg-purple-100 text-purple-800
    bg-yellow-100 text-yellow-800
    bg-orange-100 text-orange-800
    bg-gray-100 text-gray-800
"></span>

@foreach ($comments as $comment)
    <div class="bg-white rounded-xl p-4">
        <div class="flex items-start space-x-3">
            <img 
                src="{{ $comment['snippet']['topLevelComment']['snippet']['authorProfileImageUrl'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($comment['snippet']['topLevelComment']['snippet']['authorDisplayName']) }}" 
                alt="Avatar" 
                class="w-10 h-10 rounded-full"
            >
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <h3 class="text-sm font-medium text-gray-900">
                        {{ $comment['snippet']['topLevelComment']['snippet']['authorDisplayName'] }}
                    </h3>
                    <span class="text-xs text-gray-500">
                        {{ \Carbon\Carbon::parse($comment['snippet']['topLevelComment']['snippet']['publishedAt'])->diffForHumans() }}
                    </span>
                    @if(isset($comment['emotion']))
                        @php
                            $emotionColors = [
                                'joy' => ['bg-green-100', 'text-green-800'],
                                'sadness' => ['bg-blue-100', 'text-blue-800'], 
                                'anger' => ['bg-red-100', 'text-red-800'],
                                'fear' => ['bg-purple-100', 'text-purple-800'],
                                'surprise' => ['bg-yellow-100', 'text-yellow-800'],
                                'disgust' => ['bg-orange-100', 'text-orange-800'],
                                'neutral' => ['bg-gray-100', 'text-gray-800']
                            ];
                            $emotions = [
                                'joy' => 'Радість',
                                'sadness' => 'Смуток',
                                'anger' => 'Гнів', 
                                'fear' => 'Страх',
                                'surprise' => 'Здивування',
                                'disgust' => 'Відраза',
                                'neutral' => 'Нейтрально'
                            ];
                            $bgColor = $emotionColors[$comment['emotion']][0] ?? 'bg-gray-100';
                            $textColor = $emotionColors[$comment['emotion']][1] ?? 'text-gray-800';
                            $emotionText = $emotions[$comment['emotion']] ?? $comment['emotion'];
                        @endphp
                        <span class="px-2 py-1 text-xs rounded-full {{ $bgColor }} {{ $textColor }}">
                            {{ $emotionText }}
                        </span>
                    @endif
                </div>
                <p class="mt-1 text-sm text-gray-700 whitespace-pre-line">
                    {!! $comment['snippet']['topLevelComment']['snippet']['textDisplay'] !!}
                </p>
                <div class="mt-2 flex items-center gap-4">
                    <div class="flex items-center text-sm text-gray-500">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                        </svg>
                        {{ number_format($comment['snippet']['topLevelComment']['snippet']['likeCount']) }} лайків
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach 