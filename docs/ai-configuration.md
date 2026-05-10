# Konfigurasi AI Assistant

Panduan setup dan konfigurasi AI Assistant yang mendukung multi-provider (OpenAI, Anthropic, Google Gemini).

## Provider yang Didukung

### OpenAI (GPT)
- **GPT-4o** — Model terbaru, cepat dan capable
- **GPT-4o Mini** — Hemat biaya, cepat
- **GPT-4 Turbo** — Capable dan cepat
- **GPT-4** — Paling capable, lebih lambat
- **GPT-3.5 Turbo** — Paling hemat, cepat

### Anthropic (Claude)
- **Claude Sonnet 4** — Terbaru, balanced
- **Claude 3.5 Sonnet** — Cepat dan pintar
- **Claude 3.5 Haiku** — Tercepat, paling hemat
- **Claude 3 Opus** — Paling capable

### Google Gemini
- **Gemini 2.0 Flash** — Terbaru, cepat
- **Gemini 1.5 Pro** — Capable, context panjang
- **Gemini 1.5 Flash** — Cepat, hemat

## Cara Setup

### 1. Dapatkan API Key

| Provider | URL | Format Key |
|----------|-----|-----------|
| OpenAI | https://platform.openai.com/api-keys | `sk-proj-...` |
| Anthropic | https://console.anthropic.com/settings/keys | `sk-ant-...` |
| Google Gemini | https://aistudio.google.com/apikey | `AIza...` |

### 2. Konfigurasi di Admin Panel

1. Buka **Platform > Pengaturan AI**
2. Pilih **AI Provider** yang diinginkan
3. Masukkan **API Key** sesuai provider
4. Pilih **Model** yang ingin digunakan
5. Klik **Test Connection** untuk verifikasi
6. Centang **Enable AI Assistant**
7. Simpan pengaturan

### 3. Pengaturan Lanjutan

| Setting | Default | Keterangan |
|---------|---------|------------|
| Max Tokens | 1000 | Panjang maksimal respons AI |
| Temperature | 0.7 | Kreativitas (0=fokus, 2=kreatif) |
| Rate Limit | 20/jam | Batas pesan per siswa per jam |
| Show Widget | Ya | Tampilkan chat widget floating |

## System Prompt

System prompt menentukan perilaku AI. Default prompt sudah dioptimasi untuk konteks pendidikan:
- Menjawab dalam bahasa yang sama dengan siswa
- Tidak memberikan jawaban langsung untuk tugas/ujian
- Mendorong berpikir kritis
- Memberikan contoh saat menjelaskan konsep

Kamu bisa kustomisasi system prompt di field **Custom System Prompt**.

## Cara Kerja

1. Siswa mengirim pertanyaan via chat widget atau halaman AI
2. Sistem menambahkan konteks kursus (jika ada) ke prompt
3. Request dikirim ke provider AI yang aktif
4. Respons disimpan dan ditampilkan ke siswa
5. Token usage di-track untuk monitoring

## Biaya Estimasi

| Provider | Model | Harga (per 1M token) |
|----------|-------|---------------------|
| OpenAI | GPT-4o Mini | ~$0.15 input / $0.60 output |
| Anthropic | Claude 3.5 Haiku | ~$0.25 input / $1.25 output |
| Google | Gemini 1.5 Flash | Gratis (limit) / $0.075 |

> **Tips:** Untuk penggunaan edukasi dengan budget terbatas, gunakan **Gemini 1.5 Flash** (gratis dengan limit) atau **GPT-4o Mini** (sangat murah).

## Monitoring

Statistik penggunaan AI bisa dilihat di halaman Pengaturan AI:
- Total Conversations
- Total Messages
- Tokens Used
- Active Chats
