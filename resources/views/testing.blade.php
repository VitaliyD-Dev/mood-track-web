<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Результати тестування</h1>

        @if(isset($metrics['error']))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ $metrics['error'] }}</span>
            </div>
        @else
            <!-- Загальні метрики -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Загальні метрики</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <p class="text-sm text-blue-600">Загальна кількість</p>
                        <p class="text-2xl font-bold text-blue-700">{{ $metrics['total'] }}</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <p class="text-sm text-green-600">Правильних прогнозів</p>
                        <p class="text-2xl font-bold text-green-700">{{ $metrics['correct'] }}</p>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <p class="text-sm text-purple-600">Точність (Accuracy)</p>
                        <p class="text-2xl font-bold text-purple-700">{{ number_format($metrics['accuracy'] * 100, 2) }}%</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <p class="text-sm text-yellow-600">Macro F1-score</p>
                        <p class="text-2xl font-bold text-yellow-700">{{ number_format($metrics['macro_f1'] * 100, 2) }}%</p>
                        <p class="text-xs text-yellow-500 mt-1">Середнє значення F1 для всіх класів</p>
                    </div>
                    <div class="bg-indigo-50 p-4 rounded-lg">
                        <p class="text-sm text-indigo-600">Weighted F1-score</p>
                        <p class="text-2xl font-bold text-indigo-700">{{ number_format($metrics['weighted_f1'] * 100, 2) }}%</p>
                        <p class="text-xs text-indigo-500 mt-1">Зважене середнє F1 з урахуванням розміру класів</p>
                    </div>
                </div>
            </div>

            <!-- Графік метрик по емоціях -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Метрики по емоціях</h2>
                <div class="h-96">
                    <canvas id="metricsChart"></canvas>
                </div>
            </div>

            <!-- Матриця помилок -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Матриця помилок</h2>
                <div class="h-96">
                    <canvas id="confusionMatrixChart"></canvas>
                </div>
            </div>

            <!-- Аналіз помилок -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Аналіз помилок та рекомендації</h2>
                @foreach(['joy', 'sadness', 'anger', 'disgust', 'surprise', 'neutral', 'fear'] as $emotion)
                    @if(isset($metrics['error_analysis'][$emotion]['examples']) && count($metrics['error_analysis'][$emotion]['examples']) > 0)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ ucfirst($emotion) }}</h3>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-medium text-gray-700 mb-2">Часті помилки:</h4>
                                <ul class="list-disc list-inside mb-4">
                                    @foreach($metrics['error_analysis'][$emotion]['misclassifications'] as $wrongEmotion => $count)
                                        @if($count > 0)
                                            <li class="text-gray-600">
                                                Помилково класифіковано як {{ $wrongEmotion }}: {{ $count }} разів
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                                <h4 class="font-medium text-gray-700 mb-2">Приклади помилок:</h4>
                                <div class="space-y-4">
                                    @foreach($metrics['error_analysis'][$emotion]['examples'] as $example)
                                        <div class="border-l-4 border-red-500 pl-4">
                                            <p class="text-sm text-gray-600 mb-1">
                                                <span class="font-medium">Коментар:</span> {{ Str::limit($example['comment'], 200) }}
                                            </p>
                                            <p class="text-sm text-gray-600 mb-1">
                                                <span class="font-medium">Система:</span> {{ $example['system'] }} | 
                                                <span class="font-medium">Експерт:</span> {{ $example['expert'] }}
                                            </p>
                                            @if($example['notes'])
                                                <p class="text-sm text-gray-600">
                                                    <span class="font-medium">Примітки:</span> {{ $example['notes'] }}
                                                </p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach

                <div class="mt-6 bg-blue-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-blue-800 mb-2">Рекомендації для покращення:</h3>
                    <ul class="list-disc list-inside space-y-2 text-blue-700">
                        <li>Додати обробку змішаних емоцій (наприклад, "радість + втома")</li>
                        <li>Покращити розпізнавання слабко виражених емоцій</li>
                        <li>Додати контекстний аналіз для кращого розуміння сарказму та іронії</li>
                        <li>Враховувати примітки експертів при класифікації</li>
                        <li>Додати вагові коефіцієнти для різних типів емоційних маркерів</li>
                    </ul>
                </div>
            </div>

            <!-- Додаємо Chart.js -->
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                // Дані для графіків
                const emotions = ['joy', 'sadness', 'anger', 'disgust', 'surprise', 'neutral', 'fear'];
                const metrics = @json($metrics['metrics_by_emotion']);
                const confusionMatrix = @json($metrics['confusion_matrix']);

                // Графік метрик
                new Chart(document.getElementById('metricsChart'), {
                    type: 'bar',
                    data: {
                        labels: emotions.map(e => e.charAt(0).toUpperCase() + e.slice(1)),
                        datasets: [
                            {
                                label: 'Precision',
                                data: emotions.map(e => metrics[e].precision * 100),
                                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Recall',
                                data: emotions.map(e => metrics[e].recall * 100),
                                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'F1-score',
                                data: emotions.map(e => metrics[e].f1_score * 100),
                                backgroundColor: 'rgba(75, 192, 192, 0.5)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                title: {
                                    display: true,
                                    text: 'Відсоток'
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const emotion = emotions[context.dataIndex];
                                        const support = metrics[emotion].support;
                                        return `${context.dataset.label}: ${context.raw.toFixed(2)}% (support: ${support})`;
                                    }
                                }
                            }
                        }
                    }
                });

                // Матриця помилок як теплова карта
                const ctx = document.getElementById('confusionMatrixChart').getContext('2d');
                const data = [];
                const maxValue = Math.max(...emotions.flatMap(a => 
                    emotions.map(p => confusionMatrix[a][p])
                ));

                emotions.forEach((actual, i) => {
                    emotions.forEach((predicted, j) => {
                        data.push({
                            x: j,
                            y: i,
                            v: confusionMatrix[actual][predicted]
                        });
                    });
                });

                new Chart(ctx, {
                    type: 'bubble',
                    data: {
                        datasets: [{
                            data: data,
                            backgroundColor(context) {
                                const value = context.dataset.data[context.dataIndex].v;
                                const alpha = value / maxValue;
                                return `rgba(54, 162, 235, ${alpha})`;
                            },
                            borderColor: 'white',
                            borderWidth: 1,
                            radius: 30
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const v = context.dataset.data[context.dataIndex];
                                        const actual = emotions[v.y];
                                        const predicted = emotions[v.x];
                                        const count = v.v;
                                        const total = Object.values(confusionMatrix).reduce((sum, row) => sum + (row[predicted] || 0), 0); // Correctly calculate column sum
                                        const percentage = total > 0 ? ((count / total) * 100).toFixed(1) : 0;
                                        return `Факт: ${actual}\nПрогноз: ${predicted}\nКількість: ${count} (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                type: 'category',
                                labels: emotions.map(e => e.charAt(0).toUpperCase() + e.slice(1)),
                                offset: true,
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                type: 'category',
                                labels: emotions.map(e => e.charAt(0).toUpperCase() + e.slice(1)),
                                offset: true,
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            </script>
        @endif
    </div>
</x-app-layout> 