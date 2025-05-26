import sys
import json
import traceback
from transformers import pipeline

def analyze_emotions(comments, title, description):
    try:
        # Завантажуємо модель для аналізу емоцій
        print("Loading emotion analyzer model...", file=sys.stderr)
        emotion_analyzer = pipeline("text-classification", model="j-hartmann/emotion-english-distilroberta-base")
        print("Model loaded successfully", file=sys.stderr)

        results = []

        # Аналізуємо кожен коментар
        for comment in comments:
            try:
                # Отримуємо емоцію для кожного коментаря
                emotion_result = emotion_analyzer(comment)
                print(f"Analyzed comment: {comment[:50]}...", file=sys.stderr)

                # Створюємо результат для кожного коментаря
                results.append({
                    'comment': comment,
                    'label': emotion_result[0]['label'],
                    'score': emotion_result[0]['score']
                })
            except Exception as e:
                print(f"Error analyzing comment: {str(e)}", file=sys.stderr)
                results.append({
                    'comment': comment,
                    'label': 'unknown',
                    'score': 0.0
                })

        return results
    except Exception as e:
        print(f"Error in analyze_emotions: {str(e)}", file=sys.stderr)
        print(traceback.format_exc(), file=sys.stderr)
        return []

def main():
    try:
        payload_path = sys.argv[1]
        with open(payload_path, 'r', encoding='utf-8') as f:
            payload = json.load(f)
        print("Received payload", file=sys.stderr)
        analyzed_comments = analyze_emotions(payload['comments'], payload['title'], payload['description'])
        sys.stdout.write(json.dumps(analyzed_comments))
    except Exception as e:
        print(f"Error in main: {str(e)}", file=sys.stderr)
        print(traceback.format_exc(), file=sys.stderr)
        sys.stdout.write(json.dumps([]))

if __name__ == "__main__":
    main()