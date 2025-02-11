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
    """Analyze the emotional content of text by sentences"""
    try:
        classifier = get_emotion_analyzer()
        sentences = split_into_sentences(text)
        
        # Analyze each sentence
        analyses = []
        for sentence in sentences:
            if len(sentence) > 3:  # Skip very short sentences
                analysis = analyze_sentence(sentence, classifier)
                if analysis:
                    analyses.append(analysis)
        
        # Format the output
        output = []
        output.append("=" * 80)
        output.append("EMOTIONAL ANALYSIS BY SENTENCE")
        output.append("=" * 80)
        
        for i, analysis in enumerate(analyses, 1):
            output.append(f"\nSENTENCE {i}:")
            output.append(f"   {analysis['text']}")
            output.append(f"\n   Primary Emotion: {analysis['dominant_emotion'].upper()} ({analysis['confidence']:.1%})")
            
            # Add emotion breakdown
            output.append("\n   Emotional Breakdown:")
            sorted_emotions = sorted(analysis['emotions'].items(), key=lambda x: x[1], reverse=True)
            for emotion, score in sorted_emotions:
                bar = "#" * int(score * 40)
                output.append(f"   {emotion.ljust(10)} {bar} {score:.1%}")
            
            output.append("-" * 80)
        
        # Add overall analysis
        if analyses:
            output.append("\nOVERALL SENTIMENT ANALYSIS:")
            # Calculate average emotions across all sentences
            all_emotions = {}
            for analysis in analyses:
                for emotion, score in analysis['emotions'].items():
                    all_emotions[emotion] = all_emotions.get(emotion, 0) + score
            
            # Get averages
            for emotion in all_emotions:
                all_emotions[emotion] /= len(analyses)
            
            # Get dominant overall emotion
            dominant_overall = max(all_emotions.items(), key=lambda x: x[1])
            output.append(f"\nDominant Emotion: {dominant_overall[0].upper()} ({dominant_overall[1]:.1%})")
            
            output.append("\nOverall Emotional Breakdown:")
            sorted_overall = sorted(all_emotions.items(), key=lambda x: x[1], reverse=True)
            for emotion, score in sorted_overall:
                bar = "#" * int(score * 40)
                output.append(f"{emotion.ljust(10)} {bar} {score:.1%}")
        
        output.append("=" * 80)
        
        # Encode the output as UTF-8
        result = "\n".join(output)
        return result.encode('utf-8').decode('utf-8')
        
    except Exception as e:
        logger.error(f"Analysis failed: {str(e)}")
        return f"Error analyzing text: {str(e)}"

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
