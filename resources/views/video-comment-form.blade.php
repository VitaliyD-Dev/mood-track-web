<x-app-layout>

    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        <form method="POST" action="{{ route('video-comments.fetch') }}">
            @csrf
            <label for="video_url">Введіть посилання на відео YouTube:</label>
            <input type="url" name="video_url" id="video_url" required>
            <button type="submit">Отримати коментарі</button>
        </form>
    </div>
</x-app-layout>