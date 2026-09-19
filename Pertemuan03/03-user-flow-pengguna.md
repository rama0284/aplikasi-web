# 03 — User Flow: Mahasiswa (Peminjam)

**Tujuan alur:** Mahasiswa (ketua panitia) menemukan ruang yang cocok → mengajukan peminjaman → memantau status sampai ada keputusan. Tanpa jalan buntu: setiap keputusan punya jalur lanjut atau kembali.

---

## 1. Diagram User Flow (Mermaid flowchart)

```mermaid
flowchart TD
    M(["MULAI
    Buka Beranda SIRPEK"]) --> A[A: Masuk / Login]

    A --> A1{Akun valid?}
    A1 -- "Tidak" --> A2[Layar Login:
    pesan kredensial salah] --> A
    A1 -- "Ya" --> B[D: Dasbor Mahasiswa]
    B --> C[C: Pilih menu Cari Ruang]

    C --> C1[Isi kriteria:
    kapasitas, tanggal, fasilitas]
    C1 --> C2[Klik tombol Cari Ruang]

    C2 --> C3{Input lengkap dan valid?}
    C3 -- "Tidak" --> C4[Form: pesan validasi
    per field wajib] --> C1
    C3 -- "Ya" --> D1[Sistem menampilkan
    State Loading … ]

    D1 --> D2{Sistem menemukan hasil?}
    D2 -- "Tidak, kosong" --> D3[Hasil Pencarian:
    State Kosong + saran ubah filter] --> C
    D2 -- "Ya" --> E[Hasil Pencarian:
    daftar kartu ruang]

    E --> F[Mahasiswa memilih kartu
    Ruang R.12 → Detail Ruang]
    F --> F1{Sistem cek slot
    tanggal terpilih}
    F1 -- "Tidak tersedia" --> F2[Detail Ruang:
    banner Tidak Tersedia +
    rekomendasi ruang lain] --> E
    F1 -- "Tersedia" --> G[Detail Ruang:
    kapasitas, fasilitas, slot]
    G --> H[Mahasiswa klik Ajukan Peminjaman]

    H --> H1[Form Pengajuan:
    isi kegiatan, tanggal, waktu, jumlah, keperluan]
    H1 --> H2[Sistem cek bentrok jadwal
    saat menekan Kirim]
    H2 --> H3{Bentrok atau data
    belum valid?}
    H3 -- "Ya" --> H4[Form: banner bentrok &
    pesan validasi; tombol ke Cari Lagi] --> C
    H3 -- "Tidak" --> I[Layar Sukses:
    nomor pengajuan PNJ-2026-…,
    status Diproses]

    I --> J[Status & Riwayat Pengajuan]
    J --> J1{Keputusan petugas?}
    J1 -- "Diproses" --> J2[Badge Diproses;
    Bisa klik Batalkan] --> K[Konfirmasi
    batalkan?] -- "Ya" --> J3[Status Dibatalkan]
    J1 -- "Disetujui" --> J4[Badge Disetujui +
    ringkasan slot]
    J1 -- "Ditolak" --> J5[Badge Ditolak +
    alasan petugas]
    J3 --> Z([SELESAI: ruang dilepas,
    slot untuk panitia lain])
    J4 --> Z2([SELESAI: acara dapat
    dilaksanakan sesuai jadwalnya])
    J5 --> Z3([SELESAI: mahasiswa tahu
    penolakan + alasan])
    K -- "Tidak" --> J
```

### Happy path ringkas
**MULAI → Login → Dasbor → Cari Ruang → Hasil (ada) → Detail (tersedia) → Form → Valid → **Sukses PNJ-2026-00123 → Status Riwayat → Disetujui → SELESAI.**

### Jalur gagal / alternatif (tidak ada jalan buntu)
1. **Login gagal** → pesan error → kembali mengisi login.
2. **Input tidak lengkap** → validasi per field → perbaiki form.
3. **Tidak ada hasil** → State Kosong + saran ubah filter → kembali ke Cari Ruang.
4. **Ruang tidak tersedia** → banner + rekomendasi ruang lain → kembali ke Hasil.
5. **Jadwal bentrok saat submit** → pesan bentrok → tombol "Cari Lagi" → kembali ke Cari Ruang.
6. **Status Ditolak** → alasan tampil → mahasiswa dapat mengajukan ulang (kembali ke Cari Ruang).

---

## 2. Skenario Flow (Format Tabel)

**Skenario uji:** *"Anda ketua panitia yang membutuhkan ruang untuk 50 peserta pada 15 Oktober 2026. Temukan, periksa fasilitas, ajukan, lalu pastikan status dapat ditemukan kembali."*

| Langkah | Aktor | Aksi | Respons Sistem | Keputusan/Kondisi | Layar |
|---------|-------|------|----------------|-------------------|-------|
| 1 | Mahasiswa | Membuka aplikasi & klik **Masuk**, isi NIM & sandi | Validasi kredensial | Jika salah → pesan error (ulang Langkah 1) | H-PUB-02 Masuk |
| 2 | Sistem | Arahkan ke dasbor pengguna | Tampil ringkasan pengajuan & menu utama | Login berhasil | H-USER-01 Dasbor |
| 3 | Mahasiswa | Klik menu **Cari Ruang** | Buka form pencarian | — | H-USER-02 |
| 4 | Mahasiswa | Isi kapasitas ≥ 50, tanggal 15/10/2026, centang fasilitas (AC, Proyektor) | Validasi input | Tidak lengkap → pesan validasi (ulang Langkah 4) | H-USER-02 |
| 5 | Sistem | Jalankan pencarian → **State Loading** | Spinner/skeleton | — | H-USER-02 (varian loading) |
| 6 | Sistem | Tampilkan hasil sesuai filter | Jika kosong → saran ubah filter (kembali Langkah 4) | Ada hasil / kosong | H-USER-03 |
| 7 | Mahasiswa | Memilih kartu **R.12 Auditorium** | Buka detail ruang | — | H-USER-04 |
| 8 | Sistem | Cek ketersediaan slot 15/10 | Jika tidak tersedia → banner + rekomendasi (kembali Langkah 7) | Tersedia / tidak | H-USER-04 (state) |
| 9 | Mahasiswa | Klik **Ajukan Peminjaman** | Buka form pengajuan | — | H-USER-05 |
| 10 | Mahasiswa | Isi nama kegiatan, BP, tanggal, waktu 08.00–12.00, jumlah ≥ 50, keperluan | Validasi & cek bentrok saat **Kirim** | Bentrok/error → banner + tombol "Cari Lagi" | H-USER-05 (state) |
| 11 | Sistem | Simpan pengajuan → tampil **nomor PNJ-2026-00123**, status Diproses | Konfirmasi sukses | — | H-USER-05 (state sukses) / H-USER-06 |
| 12 | Mahasiswa | Buka **Status & Riwayat**, lihat badge **Diproses** | Status tersedia & dapat dibatalkan | Tetap diproses / berubah | H-USER-06 |
| 13 | Sistem | Petugas meninjau & mengubah status (lihat file 04) | Badge berubah menjadi **Disetujui** / **Ditolak** | Keputusan petugas | H-USER-06 (sinkron) |
| 14 | Mahasiswa | Membaca hasil keputusan (disetujui + jadwal, atau ditolak + alasan) | Informasi jelas, bisa mengajukan ulang | — | H-USER-06 → SELESAI |