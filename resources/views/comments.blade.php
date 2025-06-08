<x-app-layout>
    <div class="w-[1200px] h-[600px] mx-auto flex justify-center items-center mt-10">
        <iframe src="https://www.youtube.com/embed/{{ $videoId }}?rel=0&showinfo=0" frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen style="width: 900px; height: 550px;">
        </iframe>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Blocks arranged in a 2x2 grid on medium screens and above -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white rounded-xl shadow-sm p-6 overflow-hidden">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Аналіз коментарів</h2>
                <div class="overflow-y-auto space-y-6"
                    style="max-height: 400px; scrollbar-width: thin;">
                    <div class="flex items-start space-x-3">
                        <span class="text-2xl">🧠</span>
                        <div class="flex-1">
                            @if(isset($analysisReport))
                                @php
                                    // Simple Markdown parsing function
                                    // Handles bold (**text**), inline italics (*text* or -text-), and lists (* item or - item)
                                    $parseMarkdown = function($text) {
                                        // Convert bold markdown: **text** -> <strong>text</strong>
                                        $text = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $text);

                                        // Process lines to identify lists and paragraphs
                                        $lines = explode("\n", $text);
                                        $formattedLines = [];
                                        $inList = false;

                                        foreach ($lines as $line) {
                                            $trimmedLine = trim($line);

                                            // Check for list item start: * text or - text
                                            if (preg_match('/^[-*] (.*?)$/', $trimmedLine, $matches)) {
                                                if (!$inList) {
                                                    $formattedLines[] = '<ul>';
                                                    $inList = true;
                                                }
                                                // Text after the list marker
                                                $content = $matches[1];

                                                // Apply inline italics within the list item: *text* -> <em>text</em>
                                                $content = preg_replace('/\*(.*?)\*/s', '<em>$1</em>', $content);
                                                 // Apply inline italics within the list item: -text- -> <em>text</em>
                                                $content = preg_replace('/-{1}(.*?)-{1}/s', '<em>$1</em>', $content);

                                                $formattedLines[] = '<li>' . $content . '</li>';
                                            } else {
                                                // Not a list item
                                                if ($inList) {
                                                    $formattedLines[] = '</ul>';
                                                    $inList = false;
                                                }
                                                // Add as a paragraph if the line is not empty
                                                 if ($trimmedLine !== '') {
                                                    // Apply inline italics within the paragraph: *text* -> <em>text</em>
                                                    $content = preg_replace('/\*(.*?)\*/s', '<em>$1</em>', $trimmedLine);
                                                     // Apply inline italics within the paragraph: -text- -> <em>text</em>
                                                    $content = preg_replace('/-{1}(.*?)-{1}/s', '<em>$1</em>', $content);

                                                    $formattedLines[] = '<p>' . $content . '</p>';
                                                 } else {
                                                    // Preserve empty lines for spacing between paragraphs
                                                    $formattedLines[] = '';
                                                 }
                                            }
                                        }
                                        // Close the list if still inside one at the end
                                        if ($inList) {
                                            $formattedLines[] = '</ul>';
                                        }
                                        return implode("\n", $formattedLines);
                                    };
                                @endphp
                                <div class="space-y-4">
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <h3 class="font-semibold text-gray-800 mb-2">Емоційний настрій:</h3>
                                        <div class="text-gray-700 leading-relaxed">{!! $parseMarkdown($analysisReport->emotional_overview) !!}</div>
                                    </div>
                                    
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <h3 class="font-semibold text-gray-800 mb-2">Тематичний аналіз:</h3>
                                        <div class="text-gray-700 leading-relaxed">{!! $parseMarkdown($analysisReport->topical_analysis) !!}</div>
                                    </div>
                                    
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <h3 class="font-semibold text-gray-800 mb-2">Суперечливі теми:</h3>
                                         <div class="text-gray-700 leading-relaxed">{!! $parseMarkdown($analysisReport->controversial_topics) !!}</div>
                                    </div>
                                    
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <h3 class="font-semibold text-gray-800 mb-2">Аналіз контенту:</h3>
                                         <div class="text-gray-700 leading-relaxed">{!! $parseMarkdown($analysisReport->content_inspection) !!}</div>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center justify-center h-32">
                                    <p class="text-gray-500">Аналіз завантажується...</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 overflow-hidden">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Контентний аналіз</h2>
                <div class="overflow-y-auto space-y-6"
                    style="max-height: 400px; scrollbar-width: thin;">
                    <div class="flex items-start space-x-3">
                        <span class="text-2xl">⚠️</span>
                        <div class="flex-1">
                            @if(isset($analysisReport))
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="text-gray-700 leading-relaxed">{!! $parseMarkdown($analysisReport->content_inspection ?? 'Аналіз контенту...') !!}</div>
                                </div>
                            @else
                                <div class="flex items-center justify-center h-32">
                                    <p class="text-gray-500">Аналіз контенту...</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Charts Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <!-- Comments Over Time Chart -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Кількість коментарів за датами</h2>
                <canvas id="commentsChart"></canvas>
            </div>

            <!-- Emotions Over Time Chart -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Емоції у часі</h2>
                <canvas id="emotionsChart"></canvas>
            </div>
        </div>

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

    <!-- Chart.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Charts Initialization -->
    <script>
        // Prepare data for charts
        const commentsData = @json($commentsData ?? []);
        const emotionsData = @json($emotionsData ?? []);

        // Comments Over Time Chart
        const commentsCtx = document.getElementById('commentsChart').getContext('2d');
        new Chart(commentsCtx, {
            type: 'line',
            data: {
                labels: commentsData.map(item => item.date),
                datasets: [{
                    label: 'Кількість коментарів',
                    data: commentsData.map(item => item.count),
                    borderColor: 'rgb(59, 130, 246)',
                    tension: 0.1,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Динаміка коментарів'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Emotions Over Time Chart
        const emotionsCtx = document.getElementById('emotionsChart').getContext('2d');
        const emotionColors = {
            'joy': 'rgb(245, 158, 11)',
            'sadness': 'rgb(59, 130, 246)',
            'anger': 'rgb(239, 68, 68)',
            'fear': 'rgb(139, 92, 246)',
            'disgust': 'rgb(16, 185, 129)',
            'surprise': 'rgb(236, 72, 153)',
            'neutral': 'rgb(156, 163, 175)'
        };

        const datasets = Object.keys(emotionColors).map(emotion => ({
            label: emotion.charAt(0).toUpperCase() + emotion.slice(1),
            data: emotionsData.map(item => item[emotion] || 0),
            borderColor: emotionColors[emotion],
            backgroundColor: emotionColors[emotion] + '40',
            fill: false
        }));

        new Chart(emotionsCtx, {
            type: 'line',
            data: {
                labels: emotionsData.map(item => item.date),
                datasets: datasets
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Динаміка емоцій'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
</x-app-layout>