<x-app-layout>
    <div class="max-w-5xl mx-auto p-8">
        <div class="bg-white shadow-xl rounded-xl overflow-hidden">
            <!-- Заголовок -->
            <div class="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 p-8">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-white/10 rounded-lg backdrop-blur-sm">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold text-black mb-2">Детальний емоційний аналіз</h2>
                        <p class="text-blue-100 text-lg">Повний розбір емоційного стану тексту</p>
                    </div>
                </div>
            </div>

            @if (!empty($analysis))
                <div class="p-6 space-y-8">
                    <!-- Вхідний текст -->
                    <div class="bg-gray-50 rounded-xl p-6 border border-gray-100 shadow-sm">
                        <h3 class="text-xl font-semibold mb-3 text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Проаналізований текст
                        </h3>
                        <div class="bg-white p-4 rounded-lg border border-gray-200">
                            <p class="text-gray-700 leading-relaxed">{{ $analysis->input_text }}</p>
                        </div>
                    </div>

                    <!-- Основна емоція -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100 shadow-sm">
                        <h3 class="text-xl font-semibold mb-3 text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Основна емоція
                        </h3>
                        <div class="flex items-center justify-between bg-white p-4 rounded-lg border border-blue-200">
                            <div class="flex items-center">
                                <div class="w-16 h-16 rounded-full flex items-center justify-center text-white font-bold text-xl"
                                     style="background-color: {{ $emotionColors[strtolower($analysis->dominant_emotion)] ?? '#9ca3af' }}">
                                    {{ substr(strtoupper($analysis->dominant_emotion), 0, 1) }}
                                </div>
                                <div class="ml-4">
                                    <p class="text-2xl font-bold text-gray-800">{{ strtoupper($analysis->dominant_emotion) }}</p>
                                    <p class="text-sm text-gray-500">Основна емоція тексту</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-3xl font-bold text-blue-600">{{ round($analysis->confidence * 100, 1) }}%</div>
                                <p class="text-sm text-gray-500">Впевненість моделі</p>
                            </div>
                        </div>
                    </div>

                    <!-- Загальний розподіл емоцій -->
                    <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                        <h3 class="text-xl font-semibold mb-4 text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Загальний розподіл емоцій
                        </h3>
                        <div class="space-y-4">
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
                                    $percentageDisplay = number_format($percentage * 100, 1);
                                    
                                    echo '<div class="mb-4">';
                                    echo '<div class="flex justify-between mb-1">';
                                    echo '<span class="text-base font-medium text-gray-700">' . ucfirst($emotion) . '</span>';
                                    echo '<span class="text-base font-medium text-gray-600">' . $percentageDisplay . '%</span>';
                                    echo '</div>';
                                    echo '<div style="background: #f3f4f6; border-radius: 4px; height: 32px; position: relative;">';
                                    echo '<div style="position: absolute; top: 0; left: 0; height: 100%; border-radius: 4px; width: ' . $percentage * 100 . '%; background-color: ' . $color . ';">';
                                    echo '</div>';
                                    echo '</div>';
                                    echo '</div>';
                                }
                            @endphp
                        </div>
                    </div>

                    <!-- Аналіз по реченнях -->
                    <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                        <h3 class="text-xl font-semibold mb-4 text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Аналіз по реченнях
                        </h3>
                        <div class="space-y-4">
                            @foreach ($analysis->sentence_analysis as $index => $sentence)
                                <div class="bg-gray-50 rounded-lg p-4 mb-4" x-data="{ expanded: false }">
                                    <div class="cursor-pointer" @click="expanded = !expanded">
                                        <div class="flex items-center justify-between mb-2">
                                            <p class="text-gray-700 flex-1">{{ $sentence['text'] }}</p>
                                            <button class="text-gray-400 hover:text-gray-600 transition-colors duration-200 ml-4">
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
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <span class="px-3 py-1 text-sm font-medium rounded-full text-white"
                                                      style="background-color: {{ $emotionColors[strtolower($sentence['dominant_emotion'])] ?? '#9ca3af' }}">
                                                    {{ $emotions[strtolower($sentence['dominant_emotion'])] ?? $sentence['dominant_emotion'] }}
                                                </span>
                                                <span class="text-sm text-gray-600">{{ number_format($sentence['confidence'] * 100, 1) }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Розгорнутий контент -->
                                    <div x-show="expanded" 
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                                         x-transition:enter-end="opacity-100 transform translate-y-0"
                                         x-transition:leave="transition ease-in duration-150"
                                         x-transition:leave-start="opacity-100 transform translate-y-0"
                                         x-transition:leave-end="opacity-0 transform -translate-y-2"
                                         class="mt-4 space-y-3">
                                        @foreach ($sentence['emotions'] as $emotion => $percentage)
                                            <div>
                                                <div class="flex justify-between mb-1">
                                                    <span class="text-sm font-medium text-gray-700">{{ ucfirst($emotion) }}</span>
                                                    <span class="text-sm font-medium text-gray-600">{{ number_format($percentage * 100, 1) }}%</span>
                                                </div>
                                                <div style="background: #f3f4f6; border-radius: 4px; height: 24px; position: relative;">
                                                    <div style="position: absolute; top: 0; left: 0; height: 100%; border-radius: 4px; width: {{ $percentage * 100 }}%; background-color: {{ $emotionColors[strtolower($emotion)] ?? '#9ca3af' }};">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <div class="p-6 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="mt-3 text-gray-500 text-lg">Немає аналізу для відображення.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
