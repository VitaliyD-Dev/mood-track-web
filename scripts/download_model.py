from transformers import AutoTokenizer, AutoModelForSequenceClassification
import os

def download_model():
    print("Downloading emotion analyzer model...")
    
    # Створюємо директорію для моделі, якщо її немає
    model_dir = os.path.join(os.path.dirname(__file__), 'models', 'emotion-analyzer')
    os.makedirs(model_dir, exist_ok=True)
    
    try:
        # Завантажуємо модель та токенізатор
        model_name = "j-hartmann/emotion-english-distilroberta-base"  # Англійська модель емоцій
        tokenizer = AutoTokenizer.from_pretrained(model_name)
        model = AutoModelForSequenceClassification.from_pretrained(model_name)
        
        # Зберігаємо модель локально
        model.save_pretrained(model_dir)
        tokenizer.save_pretrained(model_dir)
        
        print(f"Model saved successfully to {model_dir}")
    except Exception as e:
        print(f"Error downloading model: {str(e)}")
        raise

if __name__ == "__main__":
    download_model() 