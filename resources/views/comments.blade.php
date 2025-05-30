<x-app-layout>
    <div class="w-[1200px] h-[600px] mx-auto flex justify-center items-center mt-10">
        <iframe src="https://www.youtube.com/embed/{{ $videoId }}?rel=0&showinfo=0" frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen style="width: 900px; height: 550px;">
        </iframe>
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