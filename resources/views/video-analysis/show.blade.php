<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Аналіз відео') }}
            </h2>
            <a href="{{ route('video-analysis.history') }}" class="text-sm text-gray-600 hover:text-gray-900">
                {{ __('← Назад до історії') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $analysis->video_title }}</h3>
                        <p class="text-sm text-gray-500">ID: {{ $analysis->video_id }}</p>
                        <p class="text-sm text-gray-500">{{ __('Дата аналізу') }}: {{ $analysis->created_at->format('d.m.Y H:i') }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="bg-white p-4 rounded-lg shadow">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">{{ __('Кількість коментарів') }}</h4>
                            <div id="commentsChart" style="height: 300px;"></div>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">{{ __('Емоційний аналіз') }}</h4>
                            <div id="emotionsChart" style="height: 300px;"></div>
                        </div>
                    </div>

                    <div class="mt-8">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">{{ __('Коментарі') }}</h4>
                        <div class="space-y-4">
                            @foreach($comments as $comment)
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="text-sm font-medium text-gray-900">{{ $comment->author_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $comment->published_at->format('d.m.Y H:i') }}</div>
                                    </div>
                                    <p class="text-gray-700">{{ $comment->text }}</p>
                                    @if($comment->emotion)
                                        <div class="mt-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $comment->emotion }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            {{ $comments->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Графік кількості коментарів
            var commentsOptions = {
                series: [{
                    name: '{{ __("Кількість коментарів") }}',
                    data: @json($commentsData)
                }],
                chart: {
                    type: 'line',
                    height: 300,
                    toolbar: {
                        show: false
                    }
                },
                xaxis: {
                    type: 'datetime'
                },
                yaxis: {
                    title: {
                        text: '{{ __("Кількість") }}'
                    }
                }
            };

            var commentsChart = new ApexCharts(document.querySelector("#commentsChart"), commentsOptions);
            commentsChart.render();

            // Графік емоцій
            var emotionsOptions = {
                series: @json($emotionsData->map(function($item) {
                    return [
                        'name' => $item['date'],
                        'data' => $item['emotions']->map(function($emotion) {
                            return $emotion['value'];
                        })->toArray()
                    ];
                })),
                chart: {
                    type: 'bar',
                    height: 300,
                    stacked: true,
                    toolbar: {
                        show: false
                    }
                },
                xaxis: {
                    categories: @json($emotionsLabels)
                },
                yaxis: {
                    title: {
                        text: '{{ __("Кількість") }}'
                    }
                },
                colors: ['#f59e0b', '#3b82f6', '#ef4444', '#8b5cf6', '#10b981', '#ec4899', '#9ca3af']
            };

            var emotionsChart = new ApexCharts(document.querySelector("#emotionsChart"), emotionsOptions);
            emotionsChart.render();
        });
    </script>
    @endpush
</x-app-layout> 