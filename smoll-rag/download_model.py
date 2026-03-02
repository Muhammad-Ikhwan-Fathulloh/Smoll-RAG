from transformers import AutoModelForCausalLM, AutoTokenizer
import os
import logging

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

MODEL_NAME = "HuggingFaceTB/SmolLM2-135M-Instruct"
CACHE_DIR = "./models"

def download_model():
    """Download model dan tokenizer"""
    logger.info(f"Downloading model: {MODEL_NAME}")
    
    # Create cache directory
    os.makedirs(CACHE_DIR, exist_ok=True)
    
    # Download tokenizer
    logger.info("Downloading tokenizer...")
    tokenizer = AutoTokenizer.from_pretrained(
        MODEL_NAME,
        cache_dir=CACHE_DIR,
        trust_remote_code=True
    )
    logger.info("Tokenizer downloaded")
    
    # Download model
    logger.info("Downloading model (this may take a while)...")
    model = AutoModelForCausalLM.from_pretrained(
        MODEL_NAME,
        cache_dir=CACHE_DIR,
        trust_remote_code=True,
        torch_dtype="auto"
    )
    logger.info("Model downloaded successfully!")
    
    # Save locally
    logger.info("Saving model locally...")
    model.save_pretrained("./models/smollm2-135m")
    tokenizer.save_pretrained("./models/smollm2-135m")
    logger.info("Model saved to ./models/smollm2-135m")

if __name__ == "__main__":
    download_model()