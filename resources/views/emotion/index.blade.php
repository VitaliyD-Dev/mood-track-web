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
                    <div>
                        <x-label for="text" value="Введіть текст для аналізу" />
                        <textarea id="text" name="text" class="w-full p-2 border rounded-md" required></textarea>



                    </div>

                    <div class="flex justify-between mt-6">
                        <x-button type="submit">
                            Аналізувати
                        </x-button>
                        <a href="{{ route('emotion.history') }}" class="px-4 py-2 bg-blue-500 text-black rounded-md">
                            Переглянути історію
                        </a>
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

    <script>
        document.querySelector('form').addEventListener('submit', function () {
            document.querySelector('button[type="submit"]').disabled = true;
        });
    </script>

</x-app-layout>