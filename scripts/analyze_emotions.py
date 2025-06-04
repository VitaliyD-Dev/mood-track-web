import argparse
import json
import sys
import os
import numpy as np
import onnxruntime as ort
import re

def tokenize_text(text, max_length=512):
    """Токенізація тексту для моделі RoBERTa"""
    # Базові токени RoBERTa
    BOS_TOKEN = 0
    EOS_TOKEN = 2
    PAD_TOKEN = 1
    
    # Конвертуємо текст в нижній регістр
    text = text.lower()
    
    # Розбиваємо на слова
    words = text.split()
    
    # Обмежуємо довжину
    words = words[:max_length-2]  # -2 для BOS і EOS токенів
    
    # Конвертуємо слова в токени (спрощена версія)
    tokens = [BOS_TOKEN]  # Початок послідовності
    
    for word in words:
        # Конвертуємо кожен символ в токен
        for char in word:
            token = ord(char) % 50265  # Використовуємо розмір словника моделі
            tokens.append(token)
        tokens.append(32)  # Пробіл між словами
    
    tokens.append(EOS_TOKEN)  # Кінець послідовності
    
    # Додаємо паддінг
    if len(tokens) < max_length:
        tokens.extend([PAD_TOKEN] * (max_length - len(tokens)))
    else:
        tokens = tokens[:max_length]
    
    # Створюємо маску уваги
    attention_mask = [1] * len(tokens)
    if len(attention_mask) < max_length:
        attention_mask.extend([0] * (max_length - len(attention_mask)))
    
    return {
        'input_ids': np.array(tokens, dtype=np.int64).reshape(1, -1),
        'attention_mask': np.array(attention_mask, dtype=np.int64).reshape(1, -1)
    }

def load_model():
    """Завантажуємо модель"""
    try:
        model_path = os.path.join(os.path.dirname(__file__), "emotion_model.onnx")
        if not os.path.exists(model_path):
            print("Model not found. Please run convert_model.py first to download and convert the model.", file=sys.stderr)
            raise FileNotFoundError("Model file not found")
            
        session = ort.InferenceSession(model_path)
        return session
    except Exception as e:
        print(f"Error loading model: {str(e)}", file=sys.stderr)
        raise

def analyze_emotions(texts):
    """
    Аналізує емоції в текстах використовуючи ONNX модель
    """
    try:
        print("Loading emotion model...", file=sys.stderr)
        session = load_model()
        print("Model loaded successfully", file=sys.stderr)

        # Словник емоцій
        emotions = {
            0: 'sadness',
            1: 'joy',
            2: 'love',
            3: 'anger',
            4: 'fear',
            5: 'surprise',
            6: 'neutral'
        }

        results = []
        for text in texts:
            try:
                print(f"\nAnalyzing comment: {text[:100]}...", file=sys.stderr)
                
                # Токенізуємо текст
                inputs = tokenize_text(text)
                print(f"Tokenized input shape: {inputs['input_ids'].shape}", file=sys.stderr)
                
                # Отримуємо прогнози
                ort_inputs = {
                    'input_ids': inputs['input_ids'],
                    'attention_mask': inputs['attention_mask']
                }
                outputs = session.run(None, ort_inputs)
                scores = outputs[0]
                print(f"Model output shape: {scores.shape}", file=sys.stderr)
                
                # Конвертуємо в словник емоцій
                emotions_dict = {
                    emotions[i]: float(score)
                    for i, score in enumerate(scores[0])
                }
                print(f"Emotions: {emotions_dict}", file=sys.stderr)
                
                # Знаходимо домінуючу емоцію
                dominant_emotion = max(emotions_dict.items(), key=lambda x: x[1])
                print(f"Dominant emotion: {dominant_emotion}", file=sys.stderr)
                
                results.append({
                    'label': dominant_emotion[0],
                    'score': dominant_emotion[1],
                    'emotions': emotions_dict
                })
                
            except Exception as e:
                print(f"Error analyzing comment: {str(e)}", file=sys.stderr)
                results.append({
                    'label': 'neutral',
                    'score': 1.0,
                    'emotions': {emotion: 0.0 for emotion in emotions.values()}
                })
        
        return results

    except Exception as e:
        print(f"Error in analyze_emotions: {str(e)}", file=sys.stderr)
        return [{
            'label': 'neutral',
            'score': 1.0,
            'emotions': {emotion: 0.0 for emotion in emotions.values()}
        } for _ in texts]

def main():
    parser = argparse.ArgumentParser(description='Analyze emotions in text')
    parser.add_argument('--input', required=True, help='Input file with JSON array of texts')
    args = parser.parse_args()

    try:
        # Читаємо вхідний файл
        with open(args.input, 'r', encoding='utf-8') as f:
            texts = json.load(f)

        # Аналізуємо емоції
        results = analyze_emotions(texts)

        # Виводимо результати
        print(json.dumps(results, ensure_ascii=False))

    except Exception as e:
        print(f"Error: {str(e)}", file=sys.stderr)
        sys.exit(1)

if __name__ == '__main__':
    main() 