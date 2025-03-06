<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Історія аналізів') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
                        {{ session('success') }}
                    </div>
                @endif

                <table class="min-w-full bg-white">
                    <thead>
                        <tr class="w-full bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">ID</th>
                            <th class="py-3 px-6 text-left">Користувач</th>
                            <th class="py-3 px-6 text-left">Текст</th>
                            <th class="py-3 px-6 text-left">Результат</th>
                            <th class="py-3 px-6 text-left">Дата</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($analyses as $analysis)
                            <tr class="border-b border-gray-200">
                                <td class="py-3 px-6">{{ $analysis->id }}</td>
                                <td class="py-3 px-6">{{ $analysis->user->name }}</td>
                                <td class="py-3 px-6">{{ Str::limit($analysis->input_text, 50) }}</td>
                                <td class="py-3 px-6 whitespace-pre-wrap">{{ Str::limit($analysis->result, 100) }}</td>
                                <td class="py-3 px-6">{{ $analysis->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $analyses->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
