##  Pengujian Sistem DASAR (Testing) 

Berikut adalah hasil pengujian fungsional sistem:

| No | Fitur yang Diuji | Skenario Pengujian | Input / Langkah | Hasil Diharapkan | Status |
|----|------------------|-------------------|-----------------|------------------|--------|
| 1 | Register Penyewa / Pemilik | Form kosong | Klik "Register" tanpa mengisi form | Muncul alert wajib isi data | PASS |
| 2 | Register Pemilik (valid) | Isi data + klik submit | Semua field valid + email valid | Akun terdaftar, email verifikasi dikirim | PASS |
| 3 | Verifikasi Akun via Email | Klik link verifikasi di email | Buka email → klik link verifikasi | Status akun berubah menjadi aktif | PASS |
| 4 | Login Penyewa (aktif) | Masukkan email, username, password | Akun sudah diverifikasi | Redirect ke dashboard penyewa | PASS |
| 5 | Login akun belum aktif | Login pakai akun tanpa verifikasi | Email & password valid, akun belum aktif | Tampil pesan: "Akun belum diverifikasi" | PASS |
| 6 | Akses admin_dashboard tanpa login | Ketik admin_dashboard.php langsung di URL | Belum login | Redirect ke login.php | PASS |
| 7 | Soft delete akun user | Klik "Hapus" di dashboard admin | Klik tombol hapus | User jadi nonaktif | PASS |
| 8 | Aktifkan kembali user | Klik "Aktifkan kembali" di user nonaktif | Klik tombol restore | Status user kembali aktif | PASS |
| 9 | Backup database | Klik tombol backup di dashboard admin | Halaman backup_data.php dijalankan | File .sql berhasil dihasilkan | PASS |
| 10 | Kirim pesan ke admin | Isi form hubungi_admin.php | Nama, email, subjek, isi pesan | Pesan tersimpan di DB & email masuk | PASS |
| 11 | Tampilkan peta kosan | Buka halaman detail kos | Marker lokasi ditampilkan pakai Leaflet.js | Peta muncul dan sesuai | PASS |
| 12 | Tambah kamar (pemilik) | Login sebagai pemilik dan tambahkan kamar | Input nama kamar, harga, fasilitas | Kamar tampil di daftar & bisa dipesan | PASS |
| 13 | Pemesanan kamar oleh penyewa | Login → pilih kamar → klik pesan | Pilih kamar → isi form pemesanan | Pesanan tersimpan di database | PASS |
| 14 | Upload bukti pembayaran | Penyewa upload bukti transaksi | Pilih file dan submit | File tersimpan dan dikonfirmasi | PASS |
| 15 | Grafik statistik admin | Admin buka dashboard | Admin login → lihat pie/line chart | Grafik muncul sesuai jumlah data | PASS |

---
##  Validasi Input Sistem

Berikut adalah hasil pengujian validasi input pada berbagai form dalam sistem:

| Form | Field | Validasi | Metode | Status |
|------|-------|----------|--------|--------|
| Register | Email | Harus format email | Backend + Frontend | PASS |
| Register | Password | Harus cocok dengan konfirmasi | Backend | PASS |
| Register | Role | Harus dipilih (penyewa/pemilik) | Backend | PASS |
| Register Pemilik | No GoPay | Hanya angka, panjang 10–15 digit | Frontend | PASS |
| Tambah Kamar | Harga | Harus angka ≥ 0 | Backend | PASS |
| Pesan Kamar | Field kosong | Tidak boleh kosong | Frontend | PASS |
| Hubungi Admin | Email, pesan | Tidak boleh kosong + format email valid | Backend | PASS |

---
  QA Testing & Evaluation SECARA MENYELURUH

---
 Hasil Pengujian

| Kategori | Status | Keterangan |
|----------|--------|-----------|
| Fitur Utama | PASS | Sistem berjalan sesuai fungsi dasar |
| Validasi & Struktur | PERLU PERBAIKAN | Masih ada kekurangan pada validasi dan struktur |
| Keamanan & Stabilitas | RISIKO | Terdapat potensi bug dan celah keamanan |

---

##  Fitur yang Berhasil (PASS)

| No | Fitur | Keterangan |
|----|------|----------|
| 1 | Login | Proses login berjalan dengan normal |
| 2 | Register | Registrasi user berhasil |
| 3 | Logout | Session berhasil dihapus |
| 4 | Koneksi Database | Sistem terhubung dengan database |
| 5 | List Kosan | Data kos dapat ditampilkan |
| 6 | Detail Kos | Informasi detail tampil dengan benar |
| 7 | Pemesanan Kamar | Data pemesanan tersimpan |
| 8 | Pembayaran | Modul pembayaran tersedia |
| 9 | Dashboard Penyewa | Data user tampil sesuai |
| 10 | Pengelolaan Kamar | Pemilik dapat mengelola kamar |

---

##  Fitur yang Perlu Perbaikan

| No | Fitur | Permasalahan | Rekomendasi |
|----|------|-------------|------------|
| 1 | Validasi Form | Validasi belum konsisten | Tambahkan validasi backend |
| 2 | Struktur Project | File belum terorganisir | Pisahkan folder (auth, admin, user) |
| 3 | Error Handling | Tidak terpusat | Tambahkan sistem error handling |
| 4 | UI Konsistensi | Tampilan belum seragam | Gunakan template/layout |
| 5 | Penamaan File | Tidak konsisten | Gunakan standar penamaan |
| 6 | Reusability Code | Potensi duplikasi | Gunakan function/helper |
| 7 | Notifikasi | Tidak real-time | Upgrade ke sistem notifikasi |

---

##  Potensi Bug & Risiko (FAIL / RISK)

| No | Area | Risiko |
|----|------|-------|
| 1 | Keamanan Login | Password belum di-hash |
| 2 | Database | Potensi SQL Injection |
| 3 | Session | Bisa bypass login |
| 4 | Upload File | Tidak ada validasi file |
| 5 | Akses URL | Halaman bisa diakses tanpa login |
| 6 | Input User | Rentan XSS |
| 7 | Booking | Potensi double booking |
| 8 | Pembayaran | Upload file tidak aman |
| 9 | Error Database | Tidak ditangani |
| 10 | Role Access | Tidak ada pembatasan role |

---

##  Checklist QA

### 🔹 Functional Testing
- [x] Login berjalan
- [x] Register berjalan
- [x] CRUD data berjalan
- [x] Pemesanan kamar berjalan

### 🔹 Validation Testing
- [ ] Validasi backend lengkap
- [ ] Validasi input aman

### 🔹 Security Testing
- [ ] Password hashing
- [ ] Prepared statement
- [ ] Proteksi session
- [ ] Validasi upload file

###  UI/UX Testing
- [x] Navigasi berjalan
- [ ] Konsistensi tampilan

### 🔹 Performance Testing
- [x] Aplikasi dapat berjalan
- [ ] Belum diuji performa secara mendalam

---

##  Analisis QA

Sistem yang dikembangkan telah memenuhi fungsi dasar aplikasi, namun masih memiliki beberapa kekurangan terutama pada aspek:

- Keamanan sistem  
- Validasi input  
- Struktur kode  
- Manajemen session  

Secara keseluruhan, sistem berada pada level:

> **Intermediate (Layak digunakan, namun belum siap production)**

---

##  Rekomendasi Perbaikan

###  Prioritas Tinggi
- Implementasi password hashing (`password_hash`)
- Gunakan prepared statement (PDO / MySQLi)
- Validasi input di backend
- Proteksi session dan autentikasi

###  Prioritas Menengah
- Perbaikan struktur folder
- Penambahan error handling
- Konsistensi UI

###  Prioritas Tambahan
- Logging system
- Notifikasi real-time
- Audit aktivitas user

---



