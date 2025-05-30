<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <a href="{{ route('video-comments.form') }}" class="block p-6 bg-white border rounded-lg shadow hover:bg-gray-50">
                            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Аналіз відео</h5>
                            <p class="font-normal text-gray-700">Проаналізуйте коментарі та емоції під відео на YouTube</p>
                        </a>

                        <a href="{{ route('video-analysis.history') }}" class="block p-6 bg-white border rounded-lg shadow hover:bg-gray-50">
                            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Історія аналізів</h5>
                            <p class="font-normal text-gray-700">Перегляньте історію ваших аналізів відео</p>
                        </a>

                        <a href="{{ route('emotion.analyzer') }}" class="block p-6 bg-white border rounded-lg shadow hover:bg-gray-50">
                            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Аналіз тексту</h5>
                            <p class="font-normal text-gray-700">Проаналізуйте ваш текст на емоції</p>
                        </a>

                        <a href="{{ route('emotion.history') }}" class="block p-6 bg-white border rounded-lg shadow hover:bg-gray-50">
                            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Історія аналізів тексту</h5>
                            <p class="font-normal text-gray-700">Перегляньте історію ваших аналізів тексту</p>
                        </a>


                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>