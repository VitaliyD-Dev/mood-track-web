<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Історія аналізів відео') }}
            </h2>
            <a href="{{ route('video-comments.form') }}" class="text-sm text-gray-600 hover:text-gray-900">
                {{ __('Новий аналіз →') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    @if($analyses->isEmpty())
                        <div class="text-center py-8">
                            <p class="text-gray-500">{{ __('У вас ще немає аналізів відео.') }}</p>
                            <a href="{{ route('video-comments.form') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                {{ __('Зробити перший аналіз') }}
                            </a>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($analyses as $analysis)
                                <div class="bg-gray-50 p-4 rounded-lg hover:bg-gray-100 transition-colors">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="text-lg font-medium text-gray-900">{{ $analysis->video_title }}</h3>
                                            <p class="text-sm text-gray-500 mt-1">
                                                {{ __('ID відео') }}: {{ $analysis->video_id }}
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                {{ __('Коментарів') }}: {{ number_format($analysis->total_comments) }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm text-gray-500">
                                                {{ __('Дата аналізу') }}: {{ $analysis->created_at->format('d.m.Y H:i') }}
                                            </p>
                                            <a href="{{ route('video-analysis.show', $analysis->id) }}" 
                                               class="mt-2 inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                {{ __('Переглянути') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $analyses->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 