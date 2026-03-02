import numpy as np
import logging
import time
from typing import List, Dict, Any, Optional
from datetime import datetime
from config import settings

# Setup logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

class Database:
    def __init__(self):
        # In-memory storage for simplicity (minimal RAG)
        self.documents = []
        self._is_postgres = False
        
        # Try to connect to Postgres if configured
        try:
            import psycopg2
            from psycopg2.extras import Json
            self.psycopg2 = psycopg2
            self.Json = Json
            
            self.conn_params = {
                "host": settings.DB_HOST,
                "port": settings.DB_PORT,
                "dbname": settings.DB_NAME,
                "user": settings.DB_USER,
                "password": settings.DB_PASSWORD,
                "connect_timeout": 2
            }
            # Test connection
            conn = psycopg2.connect(**self.conn_params)
            conn.close()
            self._is_postgres = True
            logger.info("Using PostgreSQL with pgvector")
        except Exception as e:
            logger.info(f"Using In-Memory storage (Postgres connection failed or not available: {e})")

    def init_db(self):
        """Inisialisasi database"""
        if self._is_postgres:
            try:
                with self.get_cursor() as cur:
                    cur.execute("CREATE EXTENSION IF NOT EXISTS vector;")
                    cur.execute("""
                        CREATE TABLE IF NOT EXISTS documents (
                            id SERIAL PRIMARY KEY,
                            content TEXT NOT NULL,
                            embedding vector(384),
                            metadata JSONB,
                            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                        );
                    """)
                    cur.execute("""
                        CREATE INDEX IF NOT EXISTS documents_embedding_idx 
                        ON documents USING ivfflat (embedding vector_cosine_ops)
                        WITH (lists = 100);
                    """)
                logger.info("Postgres tables initialized")
            except Exception as e:
                logger.error(f"Failed to init Postgres, falling back to In-Memory: {e}")
                self._is_postgres = False

    @contextmanager
    def get_cursor(self):
        if not self._is_postgres:
            raise RuntimeError("Not using PostgreSQL")
        
        conn = None
        cur = None
        try:
            conn = self.psycopg2.connect(**self.conn_params)
            cur = conn.cursor()
            yield cur
            conn.commit()
        except Exception as e:
            if conn:
                conn.rollback()
            raise e
        finally:
            if cur:
                cur.close()
            if conn:
                conn.close()

    def add_document(self, content: str, embedding: list, metadata: dict = None):
        """Tambah dokumen baru"""
        if self._is_postgres:
            try:
                with self.get_cursor() as cur:
                    cur.execute("""
                        INSERT INTO documents (content, embedding, metadata)
                        VALUES (%s, %s::vector, %s)
                        RETURNING id;
                    """, (content, embedding, self.Json(metadata) if metadata else None))
                    return cur.fetchone()[0]
            except Exception as e:
                logger.error(f"Postgres add_document failed: {e}")
        
        # In-memory fallback
        doc_id = len(self.documents) + 1
        self.documents.append({
            "id": doc_id,
            "content": content,
            "embedding": np.array(embedding),
            "metadata": metadata,
            "created_at": datetime.now()
        })
        return doc_id

    def search_similar(self, query_embedding: list, top_k: int = 3):
        """Cari dokumen paling mirip"""
        if self._is_postgres:
            try:
                with self.get_cursor() as cur:
                    cur.execute("""
                        SELECT content, metadata, 
                               1 - (embedding <=> %s::vector) as similarity
                        FROM documents
                        WHERE embedding IS NOT NULL
                        ORDER BY embedding <=> %s::vector
                        LIMIT %s;
                    """, (query_embedding, query_embedding, top_k))
                    
                    return [{
                        "content": r[0],
                        "metadata": r[1],
                        "similarity": float(r[2])
                    } for r in cur.fetchall()]
            except Exception as e:
                logger.error(f"Postgres search failed: {e}")

        # In-memory search (Numpy based)
        if not self.documents:
            return []
            
        q_vec = np.array(query_embedding)
        results = []
        
        for doc in self.documents:
            # Cosine similarity: (A . B) / (||A|| * ||B||)
            sim = np.dot(doc['embedding'], q_vec) / (np.linalg.norm(doc['embedding']) * np.linalg.norm(q_vec))
            results.append({
                "content": doc['content'],
                "metadata": doc['metadata'],
                "similarity": float(sim)
            })
            
        # Sort by similarity
        results.sort(key=lambda x: x['similarity'], reverse=True)
        return results[:top_k]

    def get_document_count(self):
        if self._is_postgres:
            try:
                with self.get_cursor() as cur:
                    cur.execute("SELECT COUNT(*) FROM documents;")
                    return cur.fetchone()[0]
            except: pass
        return len(self.documents)

    def get_all_documents(self, limit: int = 100, offset: int = 0):
        if self._is_postgres:
            try:
                with self.get_cursor() as cur:
                    cur.execute("SELECT id, content, metadata, created_at FROM documents ORDER BY id DESC LIMIT %s OFFSET %s;", (limit, offset))
                    return cur.fetchall()
            except: pass
        
        # In-memory
        sorted_docs = sorted(self.documents, key=lambda x: x['id'], reverse=True)
        paginated = sorted_docs[offset:offset+limit]
        return [(d['id'], d['content'], d['metadata'], d['created_at']) for d in paginated]

# Singleton instance
db = Database()

# Singleton instance
db = Database()