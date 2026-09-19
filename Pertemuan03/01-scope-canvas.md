# 01 — Scope Canvas

**Mata Kuliah:** Praktik Aplikasi Web (INF60295) — Pertemuan 3
**Studi kasus:** Sistem Peminjaman Ruang dan Peralatan Kampus (SIRPEK)
**Peran utama:** Mahasiswa (peminjam) dan Petugas (admin)
**Tanggal revisi:** 2026-09-19

---

## 1. Persona (Input Pertemuan 2)

> [!NOTE]
> <!-- TODO: sesuaikan dengan persona asli kelompok (Pertemuan 2). -->
> Berikut contoh realistis yang dipakai sebagai dasar desain. Ganti dengan data kelompok bila sudah ada.

### Persona Primer — Mahasiswa peminjam

- **Nama:** Raka Ardiansyah
- **Umur / Status:** 20 tahun, Mahasiswa Semester 4, Ketua Panitia Acara Himpunan
- **Kebutuhan:** mencari ruang yang menampung **≥ 50 peserta** untuk tanggal acara, memeriksa fasilitas (AC, proyektor, sound system), mengajukan peminjaman, lalu memantau status sampai ada keputusan.
- **Pain point:** harus datang ke kantor petugas atau tidak tahu perkembangan pengajuan; pernah bentrok jadwal antar-panitia karena proses manual.
- **Konteks perangkat:** lebih sering menggunakan HP, kadang laptop.

### Persona Sekunder — Petugas admin

- **Nama:** Ibu Sri Lestari
- **Umur / Status:** 45 tahun, Petugas Administrasi Fakultas
- **Kebutuhan:** meninjau pengajuan masuk, memeriksa ketersediaan/bentrok ruang, lalu mengubah status (disetujui/ditolak dengan alasan).
- **Pain point:** surat pengajuan kertas menumpuk; sulit mengecek bentrok jadwal lintas pengaju.
- **Konteks perangkat:** dominan menggunakan desktop.

---

## 2. Backlog & User Story (input Pertemuan 2)

> [!NOTE]
> <!-- TODO: sesuaikan dengan backlog & user story asli kelompok. -->
> Dibuat sebagai contoh realistis sesuai alur utama: **masuk → mencari ruang → memilih hasil → mengisi pengajuan → memantau status**; petugas **meninjau & mengubah status**.

| ID | Prioritas | User Story | Acceptance Criteria (ringkas) |
|----|-----------|------------|-------------------------------|
| US-P-01 | Must | Sebagai **mahasiswa**, saya dapat **masuk** ke sistem dengan akun kampus sehingga saya dapat mengakses fitur peminjaman. | Login valid → masuk ke Dasbor; kredensial salah → pesan error + field ditandai; belum punya akun → tautan registrasi. |
| US-P-02 | Must | Sebagai **mahasiswa**, saya dapat **mencari ruang** berdasarkan kapasitas, tanggal, dan fasilitas sehingga saya dapat menemukan ruang yang sesuai. | Pencarian punya kriteria: kapasitas, tanggal, fasilitas; hasil sesuai filter; tidak ada hasil → state kosong + saran filter; proses mencari → state loading. |
| US-P-03 | Must | Sebagai **mahasiswa**, saya dapat **melihat detail ruang** (kapasitas, fasilitas, lokasi) sehingga saya dapat memastikan ruang sesuai kebutuhan. | Detail menampilkan kapasitas, fasilitas, lokasi, slot waktu; ruang tidak tersedia pada tanggal terpilih → ditandai jelas. |
| US-P-04 | Must | Sebagai **mahasiswa**, saya dapat **mengajukan peminjaman** dengan mengisi form (nama kegiatan, tanggal, waktu, jumlah peserta, keperluan) sehingga pengajuan saya tercatat. | Semua field wajib divalidasi (error per field); dicek bentrok jadwal; berhasil → muncul nomor pengajuan; bentrok → pesan + saran ruang lain. |
| US-P-05 | Must | Sebagai **mahasiswa**, saya dapat **memantau status pengajuan** sehingga saya mengetahui keputusan petugas. | Status tampil: Diproses / Disetujui / Ditolak / Dibatalkan; perubahan status tersimpan di riwayat. |
| US-P-06 | Must | Sebagai **mahasiswa**, saya dapat **membatalkan pengajuan** yang masih berstatus Diproses sehingga slot ruang tersedia untuk panitia lain. | Tombol "Batalkan" hanya muncul untuk status Diproses; ada konfirmasi; status berubah menjadi Dibatalkan. |
| US-O-01 | Must | Sebagai **petugas**, saya dapat **melihat daftar pengajuan** sehingga saya dapat meninjau antrian peminjaman. | Daftar semua pengajuan; filter per status; informasi pemohon tampil ringkas. |
| US-O-02 | Must | Sebagai **petugas**, saya dapat **melihat detail pengajuan** sehingga saya dapat memverifikasi kelengkapan data pemohon. | Detail lengkap: pemohon, kegiatan, tanggal, waktu, jumlah peserta, keperluan. |
| US-O-03 | Must | Sebagai **petugas**, saya dapat **menyetujui atau menolak** pengajuan (dengan alasan saat menolak) sehingga mahasiswa mendapat kepastian. | Status berubah; alasan (jika ditolak) tersimpan; mahasiswa melihat perubahan status di riwayat. |
| US-O-04 | Should | Sebagai **petugas**, saya dapat **mengelola master ruang & peralatan** sehingga data ruang selalu mutakhir. | CRUD ruang; CRUD peralatan; relasi ruang-peralatan. *(di luar scope MVP, lihat bagian 4)* |

---

## 3. Scope Canvas

**Tujuan utama yang dipilih (satu alur selesai awal–akhir):**

> **Ketua panitia (mahasiswa) memastikan tersedianya ruang untuk acara 50 peserta pada tanggal tertentu, mengajukan peminjaman, dan mengetahui keputusan tanpa harus datang ke kantor petugas.**

| Komponen | Isi |
|----------|-----|
| **Tujuan Pengguna** | Mahasiswa mencari ruang yang muat ≥ 50 peserta untuk tanggal acara, memeriksa fasilitas, mengajukan peminjaman, lalu memantau status sampai disetujui/ditolak. |
| **User Story Terpilih** | US-P-02 (cari ruang), US-P-03 (detail ruang), US-P-04 (ajukan peminjaman), US-P-05 (pantau status). |
| **Halaman yang Dibutuhkan** | Cari Ruang → Hasil Pencarian → Detail Ruang → Form Pengajuan → Status & Riwayat (+ Dasbor Mahasiswa sebagai titik masuk). |
| **Di Luar Ruang Lingkup** | • Peminjaman peralatan sebagai pesanan terpisah (peralatan hanya jadi info fasilitas di ruang)<br>• Notifikasi email/SMS/WhatsApp real-time<br>• Manajemen master ruang & peralatan oleh petugas (US-O-04)<br>• Pembayaran/denda<br>• Kalender publik terbuka |
| **Asumsi yang Diuji** | • Mahasiswa sudah punya akun kampus (login via SSO kampus)<br>• Kebutuhan peserta cukup diwakili satu angka kapasitas<br>• Bentrok jadwal dapat dicek otomatis saat submit pengajuan<br>• Slot peminjaman = jadwal bebas pada tanggal & jam tertentu (bukan jadwal kuliah otomatis) |

---

## 4. Justifikasi Pilihan Scope

- 4 user story Must (US-P-02, US-P-03, US-P-04, US-P-05) membentuk **satu alur utuh** dari mencari hingga memantau status — dapat diselesaikan awal–akhir dalam satu prototipe.
- Alur petugas (US-O-01, US-O-02, US-O-03) diletakkan sebagai **halaman pendukung** agar uji coba end-to-end (pengajuan → keputusan petugas → status tercermin di riwayat mahasiswa) dapat dijalankan.
- US-O-04 (master data) sengaja dikeluarkan dari MVP: tidak termasuk alur utama mahasiswa dan menambah cakupan tanpa menambah skor keterhubungan yang signifikan.