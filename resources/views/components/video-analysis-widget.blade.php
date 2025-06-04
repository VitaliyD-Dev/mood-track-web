@props(['analysis'])

<div class="bg-white rounded-lg shadow p-4">
    <h3 class="text-lg font-semibold mb-2">{{ $analysis->video_title }}</h3>
    
    <div class="text-sm text-gray-600 mb-4">
        {{ $analysis->created_at->format('d.m.Y H:i') }}
    </div>

    @if($analysis->report)
        <div class="mb-4">
            <h4 class="font-medium text-gray-700 mb-2">Короткий огляд:</h4>
            <p class="text-sm text-gray-600">{{ $analysis->report->audience_summary }}</p>
        </div>

        <div class="flex justify-between text-sm text-gray-500">
            <span>{{ $analysis->total_comments }} коментарів</span>
            <a href="{{ route('video-analysis.show', $analysis->id) }}" class="text-blue-600 hover:text-blue-800">
                Детальніше →
            </a>
        </div>
    @else
        <div class="text-sm text-gray-500">
            Аналіз в процесі...
        </div>
    @endif
</div> 