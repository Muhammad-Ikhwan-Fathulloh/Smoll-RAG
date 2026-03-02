from fastapi import FastAPI, HTTPException, Query
from fastapi.middleware.cors import CORSMiddleware
from contextlib import asynccontextmanager
from typing import List, Optional, Dict, Any
import logging
import time
import sys

from config import settings
from database import db
from models import ChatRequest, ChatResponse, DocumentRequest, SearchResult, HealthResponse
from model_loader import model_loader
from sentence_transformers import SentenceTransformer

# Setup logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')
logger = logging.getLogger(__name__)

# Global state
embedder = None

@asynccontextmanager
async def lifespan(app: FastAPI):
    global embedder
    logger.info("Initializing Minimal RAG System...")
    
    # 1. Database
    db.init_db()
    
    # 2. Embedding Model
    try:
        logger.info(f"Loading embedding model: {settings.EMBEDDING_MODEL}")
        embedder = SentenceTransformer(settings.EMBEDDING_MODEL)
    except Exception as e:
        logger.error(f"Failed to load embedding model: {e}")
        sys.exit(1)
    
    # 3. LLM
    if not model_loader.load_model():
        logger.error("Failed to load LLM")
        sys.exit(1)
        
    logger.info("System Ready!")
    yield

app = FastAPI(title="Smoll-RAG Minimal", lifespan=lifespan)

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_methods=["*"],
    allow_headers=["*"],
)

# Core Logic
def get_embedding(text: str) -> list:
    return embedder.encode(text).tolist()

# Endpoints
@app.get("/health", response_model=HealthResponse)
async def health():
    return HealthResponse(
        status="healthy",
        models_loaded=model_loader.model is not None and embedder is not None,
        database="connected",
        total_documents=db.get_document_count(),
        version="1.1.0"
    )

@app.post("/documents")
async def add_document(request: DocumentRequest):
    try:
        embedding = get_embedding(request.content)
        doc_id = db.add_document(request.content, embedding, request.metadata)
        return {"id": doc_id, "status": "success"}
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

@app.post("/chat", response_model=ChatResponse)
async def chat(request: ChatRequest):
    start_time = time.time()
    context_docs = []
    
    # RAG: Retrieve
    if request.use_rag:
        query_emb = get_embedding(request.instruction + " " + request.input_data)
        top_k = request.top_k or settings.TOP_K
        context_docs = db.search_similar(query_emb, top_k)
        # Filter by threshold
        context_docs = [d for d in context_docs if d['similarity'] >= settings.SIMILARITY_THRESHOLD]

    # RAG: Augment
    context_text = "\n".join([f"- {d['content']}" for d in context_docs]) if context_docs else "Tidak ada konteks relevan."
    
    messages = [
        {"role": "system", "content": f"Anda adalah asisten AI yang membantu. Gunakan konteks berikut untuk menjawab pertanyaan jika relevan.\n\nKONTEKS:\n{context_text}"},
        {"role": "user", "content": f"{request.instruction}\n{request.input_data}".strip()}
    ]
    
    # RAG: Generate
    try:
        response = model_loader.generate_response(
            messages,
            max_new_tokens=request.max_tokens,
            temperature=request.temperature
        )
        
        return ChatResponse(
            response=response,
            context_used=context_docs,
            processing_time=time.time() - start_time
        )
    except Exception as e:
        logger.error(f"Generation error: {e}")
        raise HTTPException(status_code=500, detail=str(e))

@app.get("/documents")
async def list_documents(page: int = 1, per_page: int = 10):
    offset = (page - 1) * per_page
    docs = db.get_all_documents(limit=per_page, offset=offset)
    return {"documents": docs}

if __name__ == "__main__":
    import uvicorn
    uvicorn.run("main:app", host="0.0.0.0", port=8000, reload=True)