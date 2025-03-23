<x-app-layout>
    <div class="max-w-4xl mx-auto p-6 bg-white shadow-lg rounded-lg">
        <h2 class="text-2xl font-bold mb-4">Детальний емоційний аналіз</h2>

        @if (!empty($analysis))
            <!-- Вхідний текст -->
            <div class="mb-6 p-4 border rounded-lg bg-gray-50">
                <h3 class="text-lg font-semibold mb-2">Проаналізований текст:</h3>
                <p class="text-gray-700">{{ $analysis->input_text }}</p>
            </div>

            <!-- Основна емоція -->
            <div class="mb-6 p-4 border rounded-lg bg-blue-50">
                <h3 class="text-lg font-semibold mb-2">Основна емоція:</h3>
                <p class="text-xl font-bold text-blue-700">
                    {{ strtoupper($analysis->dominant_emotion) }} 
                    <span class="text-sm font-normal text-gray-600">
                        (Впевненість: {{ round($analysis->confidence * 100, 1) }}%)
                    </span>
                </p>
            </div>

            <!-- Загальний розподіл емоцій -->
            <div class="mb-6 p-4 border rounded-lg">
                <h3 class="text-lg font-semibold mb-4">Загальний розподіл емоцій:</h3>
                @php
                    $emotionColors = [
                        'neutral' => '#9ca3af',
                        'joy' => '#f59e0b',
                        'sadness' => '#3b82f6',
                        'anger' => '#ef4444',
                        'fear' => '#8b5cf6',
                        'disgust' => '#10b981',
                        'surprise' => '#ec4899'
                    ];
                    
                    foreach ($analysis->overall_emotions as $emotion => $percentage) {
                        $color = $emotionColors[strtolower($emotion)] ?? '#9ca3af';
                        $percentageDisplay = round($percentage * 100, 1);
                        
                        echo '<div style="position: relative; margin-bottom: 8px; background: #eee; height: 20px; border-radius: 4px; overflow: hidden;">';
                        echo '<span style="position: absolute; left: 10px; top: 2px; font-size: 14px; font-weight: bold; z-index: 10;">' . ucfirst($emotion) . ' (' . $percentageDisplay . '%)</span>';
                        echo '<div style="height: 100%; width: ' . $percentageDisplay . '%; background: ' . $color . ';"></div>';
                        echo '</div>';
                    }
                @endphp
            </div>

            <!-- Аналіз по реченнях -->
            <div class="mb-6 p-4 border rounded-lg">
                <h3 class="text-lg font-semibold mb-4">Аналіз по реченнях:</h3>
                @foreach ($analysis->sentence_analysis as $index => $sentence)
                    <div class="mb-4 p-3 bg-gray-50 rounded-lg" x-data="{ expanded: false }">
                        <div class="flex items-center justify-between cursor-pointer" @click="expanded = !expanded">
                            <div class="flex items-center gap-2">
                                <p class="text-gray-700">{{ $sentence['text'] }}</p>
                                <span class="px-2 py-1 rounded-full text-sm" 
                                      style="background-color: {{ $emotionColors[strtolower($sentence['dominant_emotion'])] ?? '#9ca3af' }}; color: white;">
                                    {{ strtoupper($sentence['dominant_emotion']) }}
                                    ({{ round($sentence['confidence'] * 100, 1) }}%)
                                </span>
                            </div>
                            <button class="text-gray-500 hover:text-gray-700">
                                <svg class="w-5 h-5 transform transition-transform duration-200" 
                                     :class="{ 'rotate-180': expanded }"
                                     fill="none" 
                                     stroke="currentColor" 
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" 
                                          stroke-linejoin="round" 
                                          stroke-width="2" 
                                          d="M19 9l-7 7-7-7">
                                    </path>
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Розгорнутий контент -->
                        <div x-show="expanded" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform -translate-y-2"
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 transform translate-y-0"
                             x-transition:leave-end="opacity-0 transform -translate-y-2"
                             class="mt-3 pt-3 border-t border-gray-200">
                            <h4 class="text-sm font-semibold mb-2">Розподіл емоцій для цього речення:</h4>
                            @foreach ($sentence['emotions'] as $emotion => $percentage)
                                <div style="position: relative; margin-bottom: 6px; background: #eee; height: 16px; border-radius: 4px; overflow: hidden;">
                                    <span style="position: absolute; left: 8px; top: 1px; font-size: 12px; font-weight: bold; z-index: 10;">
                                        {{ ucfirst($emotion) }} ({{ round($percentage * 100, 1) }}%)
                                    </span>
                                    <div style="height: 100%; width: {{ round($percentage * 100, 1) }}%; background: {{ $emotionColors[strtolower($emotion)] ?? '#9ca3af' }};"></div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500">Немає аналізу для відображення.</p>
        @endif
    </div>
</x-app-layout>
