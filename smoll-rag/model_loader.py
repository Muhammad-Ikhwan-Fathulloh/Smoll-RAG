import torch
from transformers import AutoModelForCausalLM, AutoTokenizer
import logging
import os
from config import settings

logger = logging.getLogger(__name__)

class HFModelLoader:
    def __init__(self):
        self.model = None
        self.tokenizer = None
        self.device = None
        
    def load_model(self):
        """Load model dari HuggingFace"""
        try:
            logger.info(f"Loading model: {settings.MODEL_NAME}")
            
            # Setup device
            if settings.USE_GPU and torch.cuda.is_available():
                self.device = torch.device("cuda")
                logger.info(f"Using GPU: {torch.cuda.get_device_name(0)}")
            else:
                self.device = torch.device("cpu")
                logger.info("Using CPU")
            
            # Load tokenizer
            logger.info("Loading tokenizer...")
            self.tokenizer = AutoTokenizer.from_pretrained(
                settings.MODEL_NAME,
                cache_dir=settings.MODEL_CACHE_DIR,
                trust_remote_code=True
            )
            
            # Set padding token
            if self.tokenizer.pad_token is None:
                self.tokenizer.pad_token = self.tokenizer.eos_token
            
            # Load model
            logger.info(f"Loading model into {self.device}...")
            
            # Load with float16 if GPU, float32 if CPU
            dtype = torch.float16 if self.device.type == "cuda" else torch.float32
            
            self.model = AutoModelForCausalLM.from_pretrained(
                settings.MODEL_NAME,
                cache_dir=settings.MODEL_CACHE_DIR,
                trust_remote_code=True,
                torch_dtype=dtype,
                low_cpu_mem_usage=True
            ).to(self.device)
            
            logger.info("Model loaded successfully!")
            return True
            
        except Exception as e:
            logger.error(f"Failed to load model: {e}")
            return False
    
    def generate_response(self, messages, max_new_tokens=None, temperature=None):
        """Generate response menggunakan chat template (aliran yang benar untuk SmolLM)"""
        if self.model is None or self.tokenizer is None:
            raise Exception("Model not loaded")
        
        try:
            # Apply chat template
            prompt = self.tokenizer.apply_chat_template(
                messages, 
                tokenize=False, 
                add_generation_prompt=True
            )
            
            inputs = self.tokenizer(prompt, return_tensors="pt").to(self.device)
            
            # Generate
            with torch.no_grad():
                outputs = self.model.generate(
                    **inputs,
                    max_new_tokens=max_new_tokens or settings.MAX_NEW_TOKENS,
                    temperature=temperature or settings.TEMPERATURE,
                    do_sample=(temperature or settings.TEMPERATURE) > 0,
                    pad_token_id=self.tokenizer.pad_token_id,
                    eos_token_id=self.tokenizer.eos_token_id,
                )
            
            # Decode only the new tokens
            new_tokens = outputs[0][inputs.input_ids.shape[-1]:]
            response = self.tokenizer.decode(new_tokens, skip_special_tokens=True)
            
            return response.strip()
            
        except Exception as e:
            logger.error(f"Error generating response: {e}")
            raise

# Singleton instance
model_loader = HFModelLoader()