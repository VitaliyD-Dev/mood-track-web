<x-app-layout>
    <div class="bg-gray-50">
        <div class="max-w-3xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-6">
                        Отримайте емоційний аналіз коментарів з відео
                    </h2>

                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <ul class="text-sm text-red-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('video-comments.fetch') }}" class="space-y-6">
                        @csrf
                        <div>
                            <label for="video_url" class="block text-base text-gray-700 mb-2">
                                Введіть посилання на відео YouTube
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/>
                                    </svg>
                                </div>
                                <input type="url" 
                                       name="video_url" 
                                       id="video_url" 
                                       required
                                       class="block w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-red-500 focus:border-transparent transition duration-150 ease-in-out text-base"
                                       placeholder="https://www.youtube.com/watch?v=...">
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                Вставте повне посилання на відео з YouTube
                            </p>
                        </div>

                        <div class="flex justify-between items-center">
                            <a href="{{ route('video-analysis.history') }}" class="text-blue-600 hover:text-blue-800">
                                {{ __('Історія аналізів') }}
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center px-6 py-3 bg-red-600 text-white text-base font-medium rounded-xl hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-150 ease-in-out shadow-sm hover:shadow">
                                Отримати коментарі
                                <svg class="ml-2 -mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>