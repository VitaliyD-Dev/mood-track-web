import logging
import sys
import json
import re
from transformers import pipeline

# Налаштування логування
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s',
    filename='emotion_analyzer.log',
    encoding='utf-8'
)
logger = logging.getLogger(__name__)


def split_into_sentences(text):
    """Розбиває текст на речення"""
    sentences = re.split(r'(?<=[.!?])\s+', text)
    return [s.strip() for s in sentences if s.strip()]


def get_emotion_analyzer():
    """Ініціалізуємо аналізатор емоцій"""
    try:
        classifier = pipeline(
            task="text-classification",
            model="j-hartmann/emotion-english-distilroberta-base",
            return_all_scores=True
        )
        return classifier
    except Exception as e:
        logger.error(f"Не вдалося ініціалізувати аналізатор: {str(e)}")
        raise


def analyze_sentence(sentence, classifier):
    """Аналізує окреме речення"""
    try:
        results = classifier(sentence)[0]
        emotions_dict = {item['label']: item['score'] for item in results}
        dominant_emotion = max(emotions_dict.items(), key=lambda x: x[1])
        
        return {
            'text': sentence,
            'dominant_emotion': dominant_emotion[0],
            'confidence': round(dominant_emotion[1], 4),
            'emotions': emotions_dict
        }
    except Exception as e:
        logger.error(f"Помилка аналізу речення: {str(e)}")
        return None


def analyze_text(text):
    try:
        classifier = get_emotion_analyzer()
        sentences = split_into_sentences(text)
        
        sentence_analysis = []
        for sentence in sentences:
            if len(sentence) > 3:
                analysis = analyze_sentence(sentence, classifier)
                if analysis:
                    sentence_analysis.append(analysis)
        
        overall_emotions = {}
        for analysis in sentence_analysis:
            for emotion, score in analysis['emotions'].items():
                overall_emotions[emotion] = overall_emotions.get(emotion, 0) + score
        
        if sentence_analysis:
            for emotion in overall_emotions:
                overall_emotions[emotion] /= len(sentence_analysis)

            dominant_overall = max(overall_emotions.items(), key=lambda x: x[1])
            
            result = {
                'dominant_emotion': dominant_overall[0] if dominant_overall else 'neutral',  # за замовчуванням 'neutral'
                'confidence': round(dominant_overall[1], 4) if dominant_overall else 0,
                'sentence_analysis': sentence_analysis,
                'overall_emotions': overall_emotions
            }
        else:
            result = {
                'dominant_emotion': 'neutral',  # за замовчуванням 'neutral'
                'confidence': 0,
                'sentence_analysis': [],
                'overall_emotions': {}
            }
        
        return json.dumps(result, ensure_ascii=False, indent=4)
    
    except Exception as e:
        logger.error(f"Помилка аналізу тексту: {str(e)}")
        return json.dumps({'error': str(e)})


if __name__ == "__main__":
    try:
        if len(sys.argv) > 1:
            text = sys.argv[1]
            result = analyze_text(text)
            print(result)
        else:
            print(json.dumps({"error": "No text provided"}))
    except Exception as e:
        print(json.dumps({"error": str(e)}))
