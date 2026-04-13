## 🧪 Pengujian Sistem (Testing)

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

## 🧪 Validasi Input Sistem

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

