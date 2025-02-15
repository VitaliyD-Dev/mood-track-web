<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Text Emotion Analyzer') }}
        </h2>
    </x-slot>

    <style>
        body {
            background-color: #f5f5f5;
        }

        .container {
            max-width: 800px;
            margin-top: 50px;
        }

        textarea {
            resize: vertical;
        }

        #result {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
            white-space: pre-wrap;
            font-family: monospace;
        }
    </style>

<div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 container">
                    <form method="POST" action="{{ url('/emotion-analyzer') }}">
                        @csrf
                        <div class="mb-3">
                            <textarea name="text" class="form-control" rows="5" placeholder="Введіть текст...">{{ old('text') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Аналізувати</button>
                    </form>

                    @if (isset($result))
                        <div id="result" class="mt-3">{{ $result }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>