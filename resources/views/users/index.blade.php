<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Список користувачів') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-4 border-b pb-2">Список користувачів</h3>
                <ul class="space-y-6">
                    @foreach ($users as $user)
                        <li class="flex items-center gap-6 border-b pb-4">
                            <img src="{{ $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : asset('default-avatar.jpg') }}"
                                class="rounded-full w-16 h-16 object-cover shadow-md">
                            <span class="font-medium text-gray-700 hover:text-blue-600 cursor-pointer text-lg">
                                {{ $user->name }}
                            </span>
                            @if ($user->isOnline())
                                <span class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <circle cx="10" cy="10" r="8"></circle>
                                    </svg>
                                    <span class="font-bold text-black">Онлайн</span>
                                </span>
                            @else
                                <span class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-red-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <circle cx="10" cy="10" r="8"></circle>
                                    </svg>
                                    <span class="font-bold text-black">Офлайн</span>
                                </span>
                            @endif


                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>