import http from 'k6/http';
import { sleep, check } from 'k6';

export let options = {
    stages: [
        { duration: '30s', target: 100 },
        { duration: '1m', target: 500 },
        { duration: '30s', target: 0 },
    ],
    thresholds: {
        http_req_duration: ['p(95)<700'],
    },
};

export default function () {
    let url = 'http://localhost:8000/v1/chat/completions';
    let payload = JSON.stringify({
        model: "TheBloke/Mistral-7B-Instruct-v0.2-GGUF",
        messages: [{ role: "user", content: "Apa itu machine learning?" }],
        temperature: 0.7,
        max_tokens: 512
    });

    let params = { headers: { 'Content-Type': 'application/json' } };

    let res = http.post(url, payload, params);
    check(res, { 'status was 200': (r) => r.status === 200 });
    sleep(1);
}