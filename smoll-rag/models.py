from pydantic import BaseModel
from typing import Optional, List, Dict, Any
from datetime import datetime

class ChatRequest(BaseModel):
    instruction: str
    input_data: str = ""
    use_rag: bool = True
    temperature: Optional[float] = 0.7
    max_tokens: Optional[int] = 256
    top_k: Optional[int] = None

class ChatResponse(BaseModel):
    response: str
    context_used: Optional[List[Dict[str, Any]]] = None
    tokens_used: Optional[int] = None
    processing_time: Optional[float] = None

class DocumentRequest(BaseModel):
    content: str
    metadata: Optional[Dict[str, Any]] = None

class DocumentResponse(BaseModel):
    id: int
    content_preview: str
    metadata: Optional[Dict[str, Any]]
    created_at: datetime
    content_length: int

class SearchRequest(BaseModel):
    query: str
    top_k: int = 3
    min_similarity: Optional[float] = None

class SearchResult(BaseModel):
    content: str
    metadata: Optional[Dict[str, Any]]
    similarity: float
    document_id: Optional[int] = None

class HealthResponse(BaseModel):
    status: str
    models_loaded: bool
    database: str
    total_documents: int
    version: str