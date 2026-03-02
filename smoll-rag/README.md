# Smoll-RAG: Minimalist & Efficient Local RAG

Sistem RAG (Retrieval-Augmented Generation) yang dirancang untuk pembelajaran dan penggunaan lokasi yang sangat efisien menggunakan **SmolLM2-135M** dan **PostgreSQL/pgvector**.

## ✨ Fitur Utama
- **Model Ringan**: Menggunakan `SmolLM2-135M-Instruct` (hanya ~270MB), sangat cepat di CPU maupun GPU.
- **Hybrid Storage**: Mendukung PostgreSQL dengan `pgvector` atau fallback In-Memory (jika tanpa DB).
- **Efisiensi Tinggi**: Tokenizer dan template dioptimalkan untuk performa maksimal.
- **Docker-Ready**: Setup instan dengan Docker Compose.

---

## 🚀 Cara Menjalankan (Docker-Compose)

Cara tercepat untuk menjalankan sistem lengkap dengan database vektor:

1.  **Pastikan Docker & Docker Compose sudah terinstal.**
2.  **Jalankan container**:
    ```bash
    docker-compose up -d
    ```
    *Ini akan menjalankan Database (pgvector), UI DB (pgweb), dan API.*
3.  **Cek Log**:
    ```bash
    docker-compose logs -f rag-api
    ```

API akan tersedia di `http://localhost:8000`. Dokumentasi Swagger ada di `http://localhost:8000/docs`.

---

## 🛠️ Cara Menjalankan (Lokal tanpa Docker)

Jika Anda ingin menjalankan tanpa database PostgreSQL (menggunakan In-Memory fallback):

1.  **Instal dependensi**:
    ```bash
    pip install -r requirements.txt
    ```
2.  **Jalankan aplikasi**:
    ```bash
    python main.py
    ```

---

## 🧪 Pengujian (RAG Flow)

Setelah aplikasi berjalan, gunakan script pengujian untuk mengetes alur penambahan dokumen dan tanya-jawab:

```bash
python test_rag.py
```

### Alur Kerja:
1.  **Input**: Anda memasukkan dokumen/teks.
2.  **Retrieve**: Sistem mencari potongan teks yang relevan di database vektor.
3.  **Augment**: Konteks yang ditemukan digabung ke dalam prompt.
4.  **Generate**: SmolLM memberikan jawaban berdasarkan konteks tersebut.

---

## 📝 Konfigurasi (`config.py`)
Anda dapat mengubah model atau parameter RAG di file `.env` atau langsung di `config.py`:
- `MODEL_NAME`: Default `HuggingFaceTB/SmolLM2-135M-Instruct`.
- `USE_GPU`: Set ke `true` jika memiliki GPU NVIDIA.
- `SIMILARITY_THRESHOLD`: Batas minimal kecocokan pencarian (default `0.3`).

---

## 📦 Lisensi
MIT