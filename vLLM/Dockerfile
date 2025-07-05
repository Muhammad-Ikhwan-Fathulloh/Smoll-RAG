FROM nvcr.io/nvidia/pytorch:24.03-py3

RUN apt-get update && apt-get install -y git curl
RUN pip install vllm

EXPOSE 8000

CMD ["python", "-m", "vllm.entrypoints.openai.api_server", \
     "--model", "TheBloke/Mistral-7B-Instruct-v0.2-GGUF", \
     "--gpu-memory-utilization", "0.9", \
     "--max-num-batched-tokens", "8192", \
     "--trust-remote-code", \
     "--port", "8000"]