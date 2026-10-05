# 🥗 NutriScan AI — Smart Nutrition & Food Vision Platform

**NutriScan AI** adalah aplikasi web analisis makanan berbasis kecerdasan buatan (*Artificial Intelligence*) yang dirancang untuk membantu mahasiswa dan masyarakat memantau asupan gizi harian secara cerdas, cepat, dan presisi. 

Pengguna cukup mengunggah atau memotret hidangan makanan mereka. AI Vision mengenali hidangan, menaksir berat porsi visual, mencocokkannya dengan basis data nutrisi resmi (*Tabel Komposisi Pangan Indonesia / TKPI* Kemenkes RI), dan menyimpannya secara rapi ke dalam buku harian konsumsi harian (*Food Diary*).

---

## 🌟 Fitur Utama

1. **AI Food Scanner (Vision API 9Router)**
   - Unggah foto makanan (JPG, PNG, WEBP).
   - Pengenalan jenis makanan otomatis dengan AI Vision model (seperti `gpt-4o-mini`).
   - Estimasi porsi visual dalam gram beserta tingkat keyakinan (*confidence score*).
   - Dukungan *Mode Demo / Fallback Cerdas* yang transparan jika API Key belum dipasang.

2. **Kalkulator Makronutrien & Koreksi Porsi Real-time**
   - Slider porsi dinamis (10g – 2000g) yang secara otomatis memperbarui angka Kalori (kkal), Protein (g), Karbohidrat (g), dan Lemak (g).
   - Diagram Donat (*Chart.js*) untuk visualisasi rasio makronutrien hidangan.
   - Pilihan pencocokan ulang (*food correction*) jika AI mendeteksi variasi yang berbeda.

3. **Food Diary Harian Terpadu**
   - Pengelompokan makanan berdasarkan waktu konsumsi: **Sarapan, Makan Siang, Makan Malam, dan Camilan**.
   - Navigasi tanggal kalender untuk meninjau pola makan hari-hari sebelumnya.
   - Fitur tambah makanan manual (memilih dari database TKPI atau input kustom).

4. **Dashboard Interaktif & Ringkasan Gizi**
   - Indikator target harian (*progress bar*) untuk Kalori, Protein, Karbohidrat, dan Lemak.
   - Grafik batang tren kalori 7 hari terakhir.
   - Daftar hidangan yang baru dikonsumsi hari ini.

5. **Riwayat & Analisis Tren Kesehatan**
   - Filter tren konsumsi 7 hari dan 30 hari terakhir.
   - Statistik rata-rata kalori dan protein harian.
   - Galeri foto riwayat seluruh pemindaian makanan.

6. **Chatbot Asisten Gizi AI (NutriScan Assistant)**
   - Konsultasi interaktif seputar gizi seimbang, pola makan sehat, dan ide hidangan lokal.
   - Kontekstual sesuai sasaran gizi pengguna.
   - Dilengkapi *disclaimer medis edukatif* (bukan pengganti diagnosis klinis).

7. **Profil & Sasaran Nutrisi Personal**
   - Kustomisasi target energi (kkal) dan batas makronutrien (protein, karbohidrat, lemak).
   - Catatan preferensi diet atau pantangan makanan.

---

## 🛠️ Arsitektur & Teknologi

- **Backend Framework**: Laravel 13 (PHP 8.2+)
- **Database**: MySQL (didukung MariaDB & SQLite)
- **Frontend / UI**:
  - Blade Templating Engine
  - Modern Responsive Styling (Tailwind CSS via CDN, palet warna bertema *Emerald Green* `#166534` & *Warm Cream* `#F8F8F2`)
  - Interaktivitas Dinamis: Vanilla JavaScript & Chart.js
  - Ikonografi: Lucide Icons
- **AI Integration**: 9Router OpenAI-compatible endpoint (`/v1/chat/completions`) dengan dukungan Vision & JSON Mode.

---

## 📋 Prasyarat Sistem

- PHP >= 8.2 dengan ekstensi `pdo`, `mbstring`, `fileinfo`, `curl`
- Composer (Package Manager PHP)
- MySQL / MariaDB (misalnya via XAMPP / Laragon) atau SQLite

---

## 🚀 Panduan Menjalankan Secara Lokal

### 1. Salin Konfigurasi Lingkungan
Pastikan file `.env` sudah dibuat dari `.env.example`:
```bash
cp .env.example .env
```

### 2. Generate Application Key (Jika Diperlukan)
```bash
php artisan key:generate
```

### 3. Konfigurasi Database
Buka file `.env` dan sesuaikan koneksi database Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nutriscan_ai
DB_USERNAME=root
DB_PASSWORD=
```
*(Pastikan Anda telah membuat database bernama `nutriscan_ai` di phpMyAdmin / MySQL CLI)*.

### 4. Jalankan Migrasi & Seeder Data Makanan
Jalankan perintah berikut untuk membuat struktur tabel dan mengisi ratusan data bahan makanan Indonesia (TKPI) beserta akun demo:
```bash
php artisan migrate --seed
```

### 5. Buat Symlink Storage Publik
Agar foto makanan yang diunggah dapat ditampilkan di browser:
```bash
php artisan storage:link
```

### 6. Menjalankan Server Lokal
Jalankan server pengembangan Laravel:
```bash
php artisan serve
```
Aplikasi kini dapat diakses melalui browser di: **http://127.0.0.1:8000**

---

## 🔑 Konfigurasi AI Vision (9Router / GripHub)

Aplikasi memakai **9Router OpenAI-compatible API** sebagai provider AI default. Contoh
setup yang sudah terbukti berjalan memakai **GripHub Router**.

Buka file `.env` dan atur parameter berikut:

```env
# Provider AI: "9router" (OpenAI-compatible) atau "gemini"
NUTRISCAN_AI_PROVIDER=9router

# Base URL endpoint API
NUTRISCAN_AI_BASE_URL=https://griphubrouter.web.id/v1

# API Key dari dashboard provider
NUTRISCAN_AI_API_KEY=sk-xxxxxx...

# Model AI Vision (harus mendukung kemampuan vision/gambar)
NUTRISCAN_AI_9ROUTER_MODEL=gemini-3.8-flash

# Mode demo/mock jika API Key belum diisi (true / false)
NUTRISCAN_DEMO_MODE=false
```

Setelah mengubah `.env`, jalankan:

```bash
php artisan config:clear
```

> **Catatan Uji Coba / Praktikum:**  
> Jika `NUTRISCAN_AI_API_KEY` belum diisi atau `NUTRISCAN_DEMO_MODE=true`, sistem otomatis
> beralih ke **Mode Demo** dengan label yang jelas. Anda tetap dapat mengunggah foto makanan,
> melihat simulasi deteksi porsi visual, menyesuaikan slider gram, dan menyimpannya ke Food
> Diary tanpa kendala.
>
> **Model vision yang tersedia di GripHub** (contoh): `gemini-3.8-flash`, `gemini-3.1-pro`,
> `gpt-5.6-luna`, `gpt-5.6-sol`, `claude-sonnet-5`, `claude-opus-5`, `qwen-3.8-max`, `grok-4.7`, dll.
> Lihat daftar lengkap via `GET {BASE_URL}/models` dengan header `Authorization: Bearer <API_KEY>`.

---

## 👤 Akun Pengujian Default (Seeder)

Setelah menjalankan `php artisan db:seed`, Anda dapat langsung masuk menggunakan akun default berikut:
- **Email**: `demo@nutriscan.ai`
- **Password**: `password123`

Atau Anda dapat mendaftarkan akun baru melalui halaman registrasi **Daftar Gratis**.

---

## 🧪 Menjalankan Pengujian Otomatis (Feature Tests)

Proyek ini telah dilengkapi dengan pengujian otomatis untuk alur autentikasi dan alur pemindaian makanan:
```bash
php artisan test
```

---

## 📄 Lisensi & Hak Cipta
Dikembangkan untuk keperluan praktikum pengembangan aplikasi web berbasis kecerdasan buatan.  
Basis data nutrisi mengacu pada **Tabel Komposisi Pangan Indonesia (TKPI)** Kementerian Kesehatan Republik Indonesia.
