<x-app-layout>
    <h3>Коментарі до відео:</h3>
    @foreach ($comments as $comment)
        <div>
            <strong>{{ $comment['snippet']['topLevelComment']['snippet']['authorDisplayName'] }}</strong>
            <p>{{ $comment['snippet']['topLevelComment']['snippet']['textDisplay'] }}</p>
        </div>
    @endforeach

</x-app-layout>