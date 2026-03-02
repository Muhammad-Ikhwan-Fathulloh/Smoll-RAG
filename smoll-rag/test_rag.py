import requests
import time

BASE_URL = "http://localhost:8000"

def test_rag():
    print("--- Testing Minimal RAG System ---")
    
    # 1. Health Check
    print("\n1. Checking Health...")
    try:
        response = requests.get(f"{BASE_URL}/health")
        print(f"Status: {response.json()}")
    except Exception as e:
        print(f"Health check failed: {e}. Make sure the server is running (python main.py)")
        return

    # 2. Add Document
    print("\n2. Adding Document...")
    doc = {
        "content": "Ibukota Indonesia adalah Nusantara.",
        "metadata": {"source": "test"}
    }
    response = requests.post(f"{BASE_URL}/documents", json=doc)
    print(f"Result: {response.json()}")

    # 3. Chat with RAG
    print("\n3. Testing Chat with RAG...")
    chat_req = {
        "instruction": "Di mana ibukota Indonesia?",
        "input_data": "",
        "use_rag": True
    }
    start = time.time()
    response = requests.post(f"{BASE_URL}/chat", json=chat_req)
    end = time.time()
    
    res_data = response.json()
    print(f"Response: {res_data['response']}")
    print(f"Context used: {len(res_data['context_used'])} documents")
    print(f"Time: {end - start:.2f}s")
    
    if "Nusantara" in res_data['response']:
        print("\n✅ SUCCESS: Model answered correctly using the context!")
    else:
        print("\n❌ FAILURE: Model did not mention the context correctly.")

if __name__ == "__main__":
    test_rag()
