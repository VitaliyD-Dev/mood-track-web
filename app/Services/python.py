import logging
import sys
import os
from transformers import pipeline
import torch
import re

# Set UTF-8 encoding for output
import locale
import codecs
sys.stdout = codecs.getwriter('utf-8')(sys.stdout.buffer)
locale.setlocale(locale.LC_ALL, 'en_US.UTF-8')

# Configure logging
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s',
    filename='emotion_analyzer.log',
    encoding='utf-8'
)
logger = logging.getLogger(__name__)

# Suppress unnecessary warnings
import warnings
warnings.filterwarnings('ignore')

def split_into_sentences(text):
    """Split text into sentences"""
    # Split by common sentence endings (., !, ?)
    # but keep the delimiter with the sentence
    sentences = re.split('(?<=[.!?])\s+', text)
    # Filter out empty sentences and strip whitespace
    return [s.strip() for s in sentences if s.strip()]

def get_emotion_analyzer():
    """Initialize the emotion analyzer with minimal dependencies"""
    try:
        classifier = pipeline(
            task="text-classification",
            model="j-hartmann/emotion-english-distilroberta-base",
            return_all_scores=True
        )
        return classifier
    except Exception as e:
        logger.error(f"Failed to initialize classifier: {str(e)}")
        raise

def analyze_sentence(sentence, classifier):
    """Analyze a single sentence"""
    try:
        results = classifier(sentence)[0]
        emotions_dict = {item['label']: item['score'] for item in results}
        dominant_emotion = max(emotions_dict.items(), key=lambda x: x[1])
        
        return {
            'text': sentence,
            'dominant_emotion': dominant_emotion[0],
            'confidence': dominant_emotion[1],
            'emotions': emotions_dict
        }
    except Exception as e:
        logger.error(f"Failed to analyze sentence: {str(e)}")
        return None

def analyze_text(text):
    try:
        classifier = get_emotion_analyzer()
        sentences = split_into_sentences(text)

        analyses = []
        for sentence in sentences:
            if len(sentence) > 3:
                analysis = analyze_sentence(sentence, classifier)
                if analysis:
                    analyses.append(analysis)

        output = []
        output.append("<div class='emotion-analysis'>")
        output.append("<h2>Emotional Analysis by Sentence</h2>")

        for i, analysis in enumerate(analyses, 1):
            output.append(f"<div class='sentence-block'>")
            output.append(f"<h3>Sentence {i}:</h3>")
            output.append(f"<p>{analysis['text']}</p>")
            output.append(f"<p><strong>Primary Emotion:</strong> {analysis['dominant_emotion'].upper()} ({analysis['confidence']:.1%})</p>")
            
            # Емоційний розподіл
            output.append("<div class='emotion-breakdown'>")
            output.append("<h4>Emotional Breakdown:</h4>")
            for emotion, score in sorted(analysis['emotions'].items(), key=lambda x: x[1], reverse=True):
                output.append(f"""
                    <div class='emotion-bar'>
                        <span>{emotion.capitalize()} ({score:.1%})</span>
                        <div class='bar' style='width: {score * 100}%;'></div>
                    </div>
                """)
            output.append("</div>")  # Закриваємо emotional-breakdown
            output.append("</div>")  # Закриваємо sentence-block

        # Підсумковий аналіз
        if analyses:
            output.append("<div class='overall-analysis'>")
            output.append("<h3>Overall Sentiment Analysis</h3>")
            all_emotions = {}
            for analysis in analyses:
                for emotion, score in analysis['emotions'].items():
                    all_emotions[emotion] = all_emotions.get(emotion, 0) + score
            for emotion in all_emotions:
                all_emotions[emotion] /= len(analyses)

            dominant_overall = max(all_emotions.items(), key=lambda x: x[1])
            output.append(f"<p><strong>Dominant Emotion:</strong> {dominant_overall[0].upper()} ({dominant_overall[1]:.1%})</p>")
            output.append("<div class='emotion-breakdown'>")
            output.append("<h4>Overall Emotional Breakdown:</h4>")
            for emotion, score in sorted(all_emotions.items(), key=lambda x: x[1], reverse=True):
                output.append(f"""
                    <div class='emotion-bar'>
                        <span>{emotion.capitalize()} ({score:.1%})</span>
                        <div class='bar' style='width: {score * 100}%;'></div>
                    </div>
                """)
            output.append("</div>")  # Закриваємо emotional-breakdown
            output.append("</div>")  # Закриваємо overall-analysis

        output.append("</div>")  # Закриваємо emotion-analysis

        return "\n".join(output)
    
    except Exception as e:
        logger.error(f"Analysis failed: {str(e)}")
        return f"<p>Error analyzing text: {str(e)}</p>"


if __name__ == "__main__":
    try:
        if len(sys.argv) > 1:
            text = sys.argv[1].encode('utf-8').decode('utf-8')
            result = analyze_text(text)
            print(result)
        else:
            print("No text provided")
    except Exception as e:
        print(f"Error: {str(e)}")
