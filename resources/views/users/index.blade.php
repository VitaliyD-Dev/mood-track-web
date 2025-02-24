<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Список користувачів') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <h3 class="text-lg font-medium text-gray-900">Список користувачів</h3>
                <ul class="mt-4 space-y-4">
                    @foreach ($users as $user)
                        <li class="flex justify-between items-center border-b pb-2">
                            <span class="font-medium text-gray-700">{{ $user->name }}</span>
                            @if ($user->isOnline())
                                <span class="text-green-500">● Онлайн</span>
                            @else
                                <span class="text-red-500">● Офлайн</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
