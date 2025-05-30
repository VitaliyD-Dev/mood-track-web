<x-app-layout>
    <x-slot name="header">
            <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight ">
            {{ __('Історія аналізів') }}
        </h2>
        <a href="{{ route('emotion.analyzer') }}" class="text-sm text-gray-600 hover:text-gray-900">
                {{ __('Новий аналіз <-') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="w-full" style="table-layout: fixed;">
                        <colgroup>
                            <col style="width: 15%">
                            <col style="width: 25%">
                            <col style="width: 40%">
                            <col style="width: 10%">
                            <col style="width: 10%">
                        </colgroup>
                        <thead>
                            <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                <th class="py-3 px-6 text-center">Користувач</th>
                                <th class="py-3 px-6 text-center">Текст</th>
                                <th class="py-3 px-6 text-center">Результат</th>
                                <th class="py-3 px-6 text-center">Дата</th>
                                <th class="py-3 px-6 text-center"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($analyses as $analysis)
                                <tr class="border-b border-gray-200">
                                    <td class="py-3 px-6 truncate text-center">{{ $analysis->user->name }}</td>
                                    <td class="py-3 px-6 truncate text-center">{{ Str::limit($analysis->input_text, 50) }}</td>
                                    <td class="py-3 px-6 whitespace-pre-wrap overflow-hidden">
                                        <div class="max-h-40 overflow-y-auto">
                                            @php
                                                // Виводимо основну емоцію
                                                echo "<p><strong>Primary Emotion:</strong> " . strtoupper($analysis->dominant_emotion) . " (" . round($analysis->confidence * 100, 1) . "%)</p>";
                                                
                                                // Виводимо заголовок для емоційного аналізу
                                                echo "<h4 class='font-bold mt-2'>Overall Emotional Breakdown:</h4>";
                                                
                                                // Отримуємо загальний розподіл емоцій
                                                $overallEmotions = $analysis->overall_emotions;
                                                
                                                // Визначаємо кольори для емоцій
                                                $emotionColors = [
                                                    'neutral' => '#9ca3af',
                                                    'joy' => '#f59e0b',
                                                    'sadness' => '#3b82f6',
                                                    'anger' => '#ef4444',
                                                    'fear' => '#8b5cf6',
                                                    'disgust' => '#10b981',
                                                    'surprise' => '#ec4899'
                                                ];
                                                
                                                // Виводимо полоски для кожної емоції
                                                foreach ($overallEmotions as $emotion => $percentage) {
                                                    $color = $emotionColors[strtolower($emotion)] ?? '#9ca3af';
                                                    $percentageDisplay = round($percentage * 100, 1);
                                                    
                                                    echo '<div style="position: relative; margin-bottom: 8px; background: #eee; height: 20px; border-radius: 4px; overflow: hidden;">';
                                                    echo '<span style="position: absolute; left: 10px; top: 2px; font-size: 14px; font-weight: bold; z-index: 10;">' . ucfirst($emotion) . ' (' . $percentageDisplay . '%)</span>';
                                                    echo '<div style="height: 100%; width: ' . $percentageDisplay . '%; background: ' . $color . ';"></div>';
                                                    echo '</div>';
                                                }
                                            @endphp
                                        </div>
                                    </td>
                                    <td class="py-3 px-6 truncate text-center">
                                        <div class="font-medium">{{ $analysis->created_at->format('Y-m-d') }}</div>
                                        <div class="text-sm text-gray-500 mt-1">{{ $analysis->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="py-3 px-6 text-center">
                                        <a href="{{ route('emotion.show', $analysis->id) }}" class="bg-blue-500 hover:bg-blue-700 text-black font-bold py-2 px-4 rounded">
                                            Деталі
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        
                    </table>
                </div>

                <div class="mt-4">
                    {{ $analyses->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
