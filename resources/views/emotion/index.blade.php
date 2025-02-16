<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Emotion Analyzer') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form method="POST" action="{{ route('analyze') }}">
                    @csrf
                    <div class="mb-4">
                        <textarea name="text" rows="5" class="w-full border-gray-300 rounded-md shadow-sm"
                            placeholder="Введіть текст для аналізу...">{{ old('text') }}</textarea>
                    </div>
                    <div class="flex justify-center">
                        <x-button type="submit" class="ml-4">
                            Аналізувати
                        </x-button>
                    </div>

                </form>

                @if(isset($result))
                    <div class="mt-6 p-4 bg-gray-50 rounded-md">
                        <pre class="whitespace-pre-wrap">{{ $result }}</pre>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>