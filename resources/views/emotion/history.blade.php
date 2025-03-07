<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Історія аналізів') }}
        </h2>
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
                                                $result = $analysis->result;
                                                
                                                
                                                // Шукаємо Primary Emotion
                                                if (preg_match('/Primary Emotion: ([A-Z]+) \(([0-9.]+)%?\)/', $result, $matches)) {
                                                    echo "<p><strong>Primary Emotion:</strong> {$matches[1]} ({$matches[2]}%)</p>";
                                                }
                                                
                                                // Виводимо заголовок для емоційного аналізу
                                                echo "<h4 class='font-bold mt-2'>Overall Emotional Breakdown:</h4>";
                                                
                                                // Шукаємо всі емоції та відсотки
                                                preg_match_all('/([A-Za-z]+) \(([0-9.]+)%?\)/', $result, $matches, PREG_SET_ORDER);
                                                
                                                // Масив для зберігання унікальних емоцій
                                                $processedEmotions = [];
                                                
                                                // Обробляємо знайдені емоції
                                                foreach ($matches as $match) {
                                                    $emotion = $match[1];
                                                    // Пропускаємо емоції у верхньому регістрі (це зазвичай заголовки)
                                                    if ($emotion === strtoupper($emotion)) {
                                                        continue;
                                                    }
                                                    
                                                    $percentage = floatval($match[2]);
                                                    
                                                    // Пропускаємо дублікати (емоції можуть повторюватися в різних частинах аналізу)
                                                    if (in_array(strtolower($emotion), $processedEmotions)) {
                                                        continue;
                                                    }
                                                    
                                                    $processedEmotions[] = strtolower($emotion);
                                                    
                                                    // Визначаємо колір для емоції
                                                    $color = '';
                                                    switch(strtolower($emotion)) {
                                                        case 'neutral':
                                                            $color = '#9ca3af';
                                                            break;
                                                        case 'joy':
                                                            $color = '#f59e0b';
                                                            break;
                                                        case 'sadness':
                                                            $color = '#3b82f6';
                                                            break;
                                                        case 'anger':
                                                            $color = '#ef4444';
                                                            break;
                                                        case 'fear':
                                                            $color = '#8b5cf6';
                                                            break;
                                                        case 'disgust':
                                                            $color = '#10b981';
                                                            break;
                                                        case 'surprise':
                                                            $color = '#ec4899';
                                                            break;
                                                        default:
                                                            $color = '#9ca3af';
                                                    }
                                                    
                                                    // Створюємо HTML для полоски
                                                    echo '<div style="position: relative; margin-bottom: 8px; background: #eee; height: 20px; border-radius: 4px; overflow: hidden;">';
                                                    echo '<span style="position: absolute; left: 10px; top: 2px; font-size: 14px; font-weight: bold; z-index: 10;">' . $emotion . ' (' . $percentage . '%)</span>';
                                                    echo '<div style="height: 100%; width: ' . $percentage . '%; background: ' . $color . ';"></div>';
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
