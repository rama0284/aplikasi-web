# Pertemuan 03 — Sistem Peminjaman Ruang dan Peralatan Kampus (SIRPEK)

<p align="center">
  <img src="https://img.shields.io/badge/status-praktikum_03-blue" alt="Status">
  <img src="https://img.shields.io/badge/stack-blade%2Fhtml%2Fcss-%23F3F4F6" alt="Stack">
</p>

## Informasi Projek

| Item | Nilai |
|------|-------|
| Judul | SIRPEK — Sistem Peminjaman Ruang dan Peralatan Kampus |
| Mata Kuliah | Praktik Aplikasi Web (INF60295) |
| Pertemuan | 3 — Sitemap, User Flow, Wireframe Responsif, Prototype & Usability |
| Kelas | <!-- TODO: isi kelas (mis. TI-A) --> |
| Tanggal Revisi | 2026-09-19 |
| Penyusun | Rama Putra Mahendra (24051130042) — dikerjakan individu |

> [!CAUTION]
> Bagian yang bertanda `TODO:` harus disesuaikan dengan data asli kelompok (persona, user story, anggota) sebelum diunggah.

---

## Ringkasan Studi Kasus

Sistem **peminjaman ruang (+ informasi peralatan) kampus** dengan dua peran:

1. **Mahasiswa (peminjam)** — masuk → mencari ruang → memilih hasil → mengisi pengajuan → memantau status.
2. **Petugas/admin** — meninjau pengajuan dan mengubah status (Disetujui / Ditolak + alasan).

**Ruang lingkup MVP:** satu alur tujuan utama — *ketua panitia memastikan tersedianya ruang untuk acara 50 peserta, mengajukan peminjaman, dan mengetahui keputusan* — didukung 9 user story (US-P-01…P-06, US-O-01…O-03). Manajemen master ruang/peralatan (US-O-04) berada di luar lingkup MVP.

---

## Daftar File

| No | File | Isi |
|----|------|-----|
| 1 | [01-scope-canvas.md](01-scope-canvas.md) | Persona, backlog & user story, Scope Canvas (tujuan, halaman, di luar scope, asumsi) |
| 2 | [02-sitemap.md](02-sitemap.md) | Arsitektur informasi 3 level, diagram Mermaid, daftar halaman, tabel pemeriksaan keterhubungan |
| 3 | [03-user-flow-pengguna.md](03-user-flow-pengguna.md) | User flow mahasiswa (Mermaid) + tabel skenario |
| 4 | [04-user-flow-admin.md](04-user-flow-admin.md) | User flow petugas (Mermaid) + tabel skenario |
| 5 | [05-wireframe-desktop.html](05-wireframe-desktop.html) | Wireframe low-fi desktop (~1440px), 8 layar + state, buka langsung di browser |
| 6 | [06-wireframe-mobile.html](06-wireframe-mobile.html) | Wireframe low-fi mobile (~375px), pola hamburger + tab bar, 6 layar |
| 7 | [prototype.html](prototype.html) | Prototype klik: happy path + jalur gagal + alur admin (semua tombol kembali berfungsi) |
| 8 | [07-usability-walkthrough.md](07-usability-walkthrough.md) | Template + hasil uji usability (9 temuan, 7 direvisi) |
| 9 | [08-keputusan-desain.md](08-keputusan-desain.md) | 6 keputusan desain penting dengan pertimbangan persona/user story |

Cara pakai: buka **`prototype.html`** dan ikuti urutan → setiap langkah mengarah ke layar di `05-wireframe-desktop.html`.

---

## Checklist Pemenuhan Rubrik

| Kriteria | Bobot | Status |
|----------|-------|--------|
| Wireframe responsif (desktop + mobile recruit-mobile-pattern) | 25% | ✅ Terpenuhi |
| User flow (pengguna + admin, ada decision point & jalur gagal) | 20% | ✅ Terpenuhi |
| Keterhubungan dengan backlog (user story ↔ AC ↔ layar/state) | 15% | ✅ Terpenuhi (tabel di 02-sitemap.md) |
| Arsitektur informasi & sitemap (3 level, Mermaid, daftar halaman) | 15% | ✅ Terpenuhi |
| Prototype & usability (happy path + gagal, lembar kerja uji) | 15% | ✅ Terpenuhi |
| Dokumentasi & kolaborasi (README berisi kontribusi) | 10% | ✅ Terpenuhi |

## Kontribusi

Dikerjakan secara **individu**.

| No | Nama | NIM | Kontribusi |
|----|------|-----|------------|
| 1 | Rama Putra Mahendra | 24051130042 | Seluruh artefak: scope canvas, sitemap, 2 user flow, wireframe desktop & mobile, prototype, usability walkthrough, keputusan desain, dokumentasi |