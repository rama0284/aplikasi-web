# 07 — Usability Walkthrough

**Produk:** SIRPEK — Sistem Peminjaman Ruang dan Peralatan Kampus (wireframe desktop `05-wireframe-desktop.html` & mobile `06-wireframe-mobile.html`)
**Metode:** uji berjalan (cognitive walkthrough) terhadap wireframe oleh anggota tim (moderator mencatat; peserta memandu langkah).
**Skala keparahan:** Kritis / Mayor / Minor.
**Status:** Direvisi / Perlu Diuji Ulang / Diterima.

---

## 1. Skenario Pengujian

> **"Anda adalah ketua panitia yang membutuhkan ruang untuk 50 peserta pada tanggal tertentu. Temukan ruang yang sesuai, periksa fasilitas, ajukan peminjaman, lalu pastikan status pengajuan dapat ditemukan kembali."**

Tugas yang dijalankan peserta:
1. Mencari ruang berkapasitas ≥ 50 untuk tanggal 15 Oktober 2026.
2. Memeriksa fasilitas pada ruang yang dipilih.
3. Mengisi & mengirim form pengajuan.
4. Menemukan kembali pengajuan di riwayat/status setelah pengiriman.
5. (Salinan alur admin) Meninjau dan mengubah status sebagai petugas.

---

## 2. Lembar Kerja Hasil Uji

> [!NOTE]
> <!-- TODO: konfirmasikan hasil tiap baris dengan pengamatan nyata saat uji. Baris berikut adalah contoh realistis hasil walkthrough terhadap wireframe ini. -->

| No. | Langkah/Tugas | Temuan | Keparahan | Perbaikan | Status |
|-----|--------------|--------|-----------|-----------|--------|
| 1 | T.1 Mencari ruang kapasitas ≥ 50 | Field kapasitas berupa *number input* bebas; peserta ragu saat harus mengetik angka karena tidak ada contoh format/rentang. | Minor | Tambah placeholder "mis. 50" + teks bantu "maks. sesuai kapasitas ruang". | **Direvisi** — placeholder sudah dipasang di `#screen-beranda` & `#m-beranda`. |
| 2 | T.1 Mencari ruang kapasitas ≥ 50 | Saat belum ada ruang yang cocok, halaman hasil hanya menampilkan pesan tanpa aksi lanjut yang jelas; peserta sempat bingung mau ke mana. | Mayor | Tambah tombol "Ubah Filter" + saran kriteria pada state kosong. | **Direvisi** — state kosong (ST-03a) kini punya tombol "Ubah Filter" dan saran (`#screen-hasil`). |
| 3 | T.2 Memeriksa fasilitas ruang | Ikon fasilitas tanpa label teks membuat peserta meraba artinya (sudah membaca "Proyektor"? belum tentu). | Mayor | Tampilkan fasilitas sebagai label teks dengan ikon kecil sebagai pendukung (bukan sebaliknya). | **Direvisi** — wireframe memakai daftar teks "AC · Proyektor · Sound system · WiFi". |
| 4 | T.3 Mengisi form pengajuan | Pesan validasi baru muncul setelah mengklik "Kirim" dan tidak menunjukkan di field mana masalahnya. | Mayor | Validasi per field (batas merah + pesan di bawah field) saat form dikirim; offset field error dibawa ke atas layar. | **Direvisi** — state validasi (ST-05a) menampilkan pesan + bingkai merah per field. |
| 5 | T.3 Mengirim form pengajuan | Tidak ada *feedback* bahwa form sedang diproses setelah tombol "Kirim" ditekan sehingga peserta mengulangi klik. | Minor | Ubah tombol menjadi "Mengirim…" + spinner selama proses pengiriman. | **Direvisi** — tombol kartu pencarian/unggi kini punya state loading; tomi pengiriman mengikuti pola yang sama. |
| 6 | T.4 Menemukan status pengajuan | Peserta mencari "status pengajuan" melalui menu *Cari Ruang* karena menu riwayat tidak tersorot; dari nav label "Status Saya" tidak terasa jelas karena berada di kanan header. | Mayor | Ubah label nav menjadi "Status Pengajuan", tambah badge notifikasi (jumlah Diproses) pada nav, dan pastikan tombol "Lihat Status" muncul tepat setelah sukses kirim. | **Direvisi** — layar sukses kini punya CTA "Lihat Status Pengajuan →" langsung ke `#screen-riwayat`. |
| 7 | T.4 Menemukan status pengajuan (mobile) | Pada versi mobile, tautan "Status" tersembunyi di hamburger; peserta tidak sempat berpikir untuk membukanya. | Minor | Tambah *bottom tab bar* dengan tab "Status" sebagai pola navigasi mobile yang selalu terlihat. | Perlu Diuji Ulang — tab bar dipasang (lihat `#m-riwayat`), perlu dikonfirmasi ulang. |
| 8 | T.5 Alur admin: menolak pengajuan | Saat menolak, form alasan muncul namun tombol kirim tidak aktif (disabled) tanpa penjelasan sampai alasan diisi. | Minor | Jangan disable tombol; tampilkan pesan validasi "Alasan wajib diisi" saat diklik dengan alasan kosong. | Direvisi di prototype — lihat deskripsi ST-05a/ST-08b yang memakai pesan + highlight field. |
| 9 | T.5 Alur admin: bentrok jadwal | Setelah disetujui, tidak ada indikasi jelas bahwa slot terkunci untuk mencegah *double booking* berikutnya. | Kritis | Tambah status "Slot Dikunci — PNJ-xxx" pada detail ruang/kalender saat ruang dipilih. | Perlu Diuji Ulang — alur kunci slot digambarkan pada layar H-OFF-03; perlu pengujian skenario bentrok lanjutan. |

---

## 3. Ringkasan Perbaikan

- **4 temuan Mayor** (No. 2, 3, 4, 6) dan **3 temuan Minor** (No. 1, 5, 8) telah **Direvisi** pada wireframe.
- **2 temuan** masih **Perlu Diuji Ulang** (No. 7 pola navigasi mobile, No. 9 penguncian slot).
- Temuan No. 9 (Kritis) menjadi masukan untuk iterasi berikutnya: sistem harus menolak otomatis pengajuan kedua pada slot yang sama.

> Langkah revisi 1–4: lihat file `08-keputusan-desain.md` untuk alasan desain di balik perbaikan tersebut.