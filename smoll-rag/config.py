from pydantic_settings import BaseSettings
import os

class Settings(BaseSettings):
    # Database
    DB_HOST: str = os.getenv("DB_HOST", "localhost")
    DB_PORT: int = int(os.getenv("DB_PORT", "5432"))
    DB_NAME: str = os.getenv("DB_NAME", "mydatabase")
    DB_USER: str = os.getenv("DB_USER", "user")
    DB_PASSWORD: str = os.getenv("DB_PASSWORD", "password")
    
    # HuggingFace model
    MODEL_NAME: str = os.getenv("MODEL_NAME", "HuggingFaceTB/SmolLM2-135M-Instruct")
    MODEL_CACHE_DIR: str = os.getenv("MODEL_CACHE_DIR", "./models")
    
    # Embedding model
    EMBEDDING_MODEL: str = os.getenv("EMBEDDING_MODEL", "sentence-transformers/all-MiniLM-L6-v2")
    
    # RAG settings
    TOP_K: int = int(os.getenv("TOP_K", "3"))
    SIMILARITY_THRESHOLD: float = float(os.getenv("SIMILARITY_THRESHOLD", "0.3"))
    
    # Generation settings
    MAX_NEW_TOKENS: int = int(os.getenv("MAX_NEW_TOKENS", "256"))
    TEMPERATURE: float = float(os.getenv("TEMPERATURE", "0.3"))
    USE_GPU: bool = os.getenv("USE_GPU", "false").lower() == "true"

settings = Settings()