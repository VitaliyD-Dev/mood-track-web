import os
import sys
import torch
from transformers import AutoModelForSequenceClassification, AutoTokenizer
import onnx
import onnxruntime as ort

def download_and_convert_model():
    """
    Завантажує модель з HuggingFace та конвертує її в ONNX формат
    """
    try:
        print("Starting model download and conversion...", file=sys.stderr)
        
        # Шлях для збереження моделі
        model_path = os.path.join(os.path.dirname(__file__), "emotion_model.onnx")
        
        # Завантажуємо модель та токенізатор
        print("Downloading model from HuggingFace...", file=sys.stderr)
        model = AutoModelForSequenceClassification.from_pretrained("j-hartmann/emotion-english-distilroberta-base")
        tokenizer = AutoTokenizer.from_pretrained("j-hartmann/emotion-english-distilroberta-base")
        
        # Підготовка вхідних даних для конвертації
        print("Preparing input data for conversion...", file=sys.stderr)
        dummy_input = tokenizer("This is a test", return_tensors="pt")
        
        # Конвертуємо модель в ONNX
        print("Converting model to ONNX format...", file=sys.stderr)
        torch.onnx.export(
            model,
            (dummy_input["input_ids"], dummy_input["attention_mask"]),
            model_path,
            input_names=["input_ids", "attention_mask"],
            output_names=["logits"],
            dynamic_axes={
                "input_ids": {0: "batch_size", 1: "sequence"},
                "attention_mask": {0: "batch_size", 1: "sequence"},
                "logits": {0: "batch_size"}
            },
            opset_version=12
        )
        
        # Перевіряємо конвертовану модель
        print("Validating converted model...", file=sys.stderr)
        onnx_model = onnx.load(model_path)
        onnx.checker.check_model(onnx_model)
        
        # Тестуємо конвертовану модель
        print("Testing converted model...", file=sys.stderr)
        session = ort.InferenceSession(model_path)
        ort_inputs = {
            "input_ids": dummy_input["input_ids"].numpy(),
            "attention_mask": dummy_input["attention_mask"].numpy()
        }
        outputs = session.run(None, ort_inputs)
        
        print("Model successfully converted and validated!", file=sys.stderr)
        return True
        
    except Exception as e:
        print(f"Error in model conversion: {str(e)}", file=sys.stderr)
        return False

if __name__ == "__main__":
    success = download_and_convert_model()
    sys.exit(0 if success else 1) 