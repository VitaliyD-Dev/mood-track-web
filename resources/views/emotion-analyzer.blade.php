<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <title>Text Emotion Analyzer</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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
</head>

<body>
    <div class="container">
        <h1 class="text-center">Text Emotion Analyzer</h1>

        <form method="POST" action="{{ url('/emotion-analyzer') }}">
            @csrf
            <div class="mb-3">
                <textarea name="text" class="form-control" rows="5"
                    placeholder="Введіть текст...">{{ old('text') }}</textarea>
            </div>
            <button type="submit" class="btn btn-success">Аналізувати</button>
        </form>

        @if (isset($result))
            <div id="result" class="mt-3">{{ $result }}</div>
        @endif
    </div>
</body>

</html>