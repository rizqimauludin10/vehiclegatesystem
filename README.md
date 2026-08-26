# 🛡️ Vehicle Gate Monitoring System

<div align="center">

![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4.x-EF4223?style=for-the-badge&logo=codeigniter&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.x-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.x-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)
![PWA](https://img.shields.io/badge/PWA-Ready-5A0FC8?style=for-the-badge&logo=pwa&logoColor=white)

**Sistem Monitoring Kendaraan Keluar Berbasis QR Code untuk Lingkungan Militer**

*Dibangun khusus untuk jaringan intranet — data personel terlindungi, akses terkontrol*

</div>

---

## 📋 Tentang Proyek

**Vehicle Gate Monitoring System (VGS)** adalah aplikasi web berbasis intranet yang dirancang untuk memantau dan mencatat kendaraan yang keluar dari lingkungan tertentu. Sistem ini menggunakan QR Code sebagai media identifikasi kendaraan, memungkinkan petugas pos jaga melakukan pengecekan secara cepat, akurat, dan terdokumentasi.

Aplikasi ini dibangun dengan pendekatan **keamanan data militer** sebagai prioritas utama — tidak ada koneksi ke internet, semua data tersimpan dan diakses hanya melalui jaringan intranet lokal.

---

## 🎯 Tujuan

- Meminimalisir potensi pencurian kendaraan di lingkungan kerja
- Memberikan validasi identitas kendaraan secara real-time saat melintas gate
- Mencatat log seluruh aktivitas kendaraan keluar secara otomatis
- Menyediakan laporan dan rekap data yang mudah diakses oleh admin

---

## 👥 Role Pengguna

| Role | Akses |
|------|-------|
| **Admin** | Kelola seluruh data master, generate QR, lihat laporan lengkap |
| **Petugas** | Scan QR kendaraan di pos jaga, lihat log scan pribadi |

---

## ✨ Fitur Utama

### 🔐 Autentikasi & Keamanan
- Login berbasis session dengan role-based access control
- Filter middleware untuk Admin dan Petugas
- Auto-redirect berdasarkan role dan jenis perangkat (mobile/desktop)
- Session timeout otomatis
- Proteksi CSRF pada setiap form dan request AJAX

### 👤 Management Personel
- CRUD data personel lengkap (NRP, nama, pangkat, jabatan, satuan kerja)
- Upload foto personel
- Dropdown pangkat terkelompok (Perwira, Bintara, Tamtama, PNS)
- Relasi ke tabel `pangkat` dan `satuan_kerja` (FK normalized)
- Input nomor HP/WhatsApp untuk kontak langsung
- Status aktif / nonaktif personel
- Import data dari Excel (dengan validasi per baris)

### 🚗 Management Kendaraan
- CRUD data kendaraan per personel (1 personel bisa punya banyak kendaraan)
- Kode kendaraan auto-generate (format: `KND-0001`)
- Multi foto galeri kendaraan (maks. 6 foto)
- Field data STNK lengkap: Nomor Rangka, Nomor Mesin, Nomor STNK, Nomor BPKB
- Upload foto STNK
- Status aktif / dibekukan
- Proteksi hapus: kendaraan tidak bisa dihapus jika QR masih aktif

### 📊 Management Data Master
- **Pangkat**: CRUD data pangkat militer & PNS dengan kelompok (Perwira, Bintara, Tamtama, PNS), kategori, dan golongan
- **Satuan Kerja**: CRUD satker dengan kode unik, nama singkat, dan nama panjang

### 🔲 QR Code Management
- Generate QR Code per kendaraan (1 kendaraan = 1 QR aktif)
- Regenerate QR otomatis menonaktifkan QR lama
- Download QR Code sebagai file PNG
- Revoke / nonaktifkan QR Code
- Filter status QR (aktif / nonaktif)

### 📷 Scan QR Code
Tersedia dalam 3 mode tampilan:

#### Mode Desktop (Hardware Scanner)
- Input autofokus untuk scanner USB/Wireless
- Hasil scan tampil langsung: foto personel + detail kendaraan
- Log scan tersimpan otomatis
- Live clock dan counter scan harian

#### Mode Monitor (Fullscreen)
- Tampilan fullscreen untuk layar TV/monitor di pos jaga
- Layout 2 panel: identitas personel (kiri) + detail kendaraan (kanan)
- Galeri foto kendaraan dengan lightbox
- Audio beep feedback (sukses/gagal)
- TV scanline effect
- Ticker informasi berjalan
- Responsif hingga resolusi 4K
- Data hasil scan tidak hilang hingga scan berikutnya

#### Mode Mobile (Kamera HP/Tablet)
- Buka kamera langsung dari browser (html5-qrcode)
- Deteksi kamera belakang otomatis
- Toggle flash/torch
- QR frame dengan animasi scan line
- Vibrate feedback saat QR terbaca
- Layout landscape: 2 kolom (personel kiri, kendaraan kanan)
- Galeri foto kendaraan grid 3 kolom (portrait) / 4 kolom (landscape)
- Log scan mini dalam aplikasi
- Tombol "Scan Lagi" dengan resume kamera tanpa restart

### 📋 Hasil Scan Menampilkan
- Foto personel pemilik kendaraan
- Identitas: nama, NRP, pangkat, jabatan, satuan kerja
- Tombol **direct WhatsApp** ke nomor HP personel dengan pesan otomatis
- Detail kendaraan spesifik yang di-scan: plat nomor, merk, model, warna, tahun
- Nomor STNK & tahun kendaraan
- Galeri foto kendaraan (tampak depan, samping, belakang, dll)
- Foto STNK (dengan border gold)
- Status kendaraan (aktif / dibekukan)

### 📈 Dashboard Admin
- Statistik: total personel, kendaraan, petugas, keluar hari ini
- **Bar chart**: aktivitas kendaraan keluar 7 hari terakhir
- **Doughnut chart**: distribusi shift (pagi, siang, malam) bulan ini
- **Horizontal bar chart**: top 5 satker paling aktif
- **Rekap per Satuan Kerja**: tabel + grouped bar chart jumlah personel, kendaraan, QR aktif, dan coverage QR per satker

### 📊 Laporan & Rekap
- Filter multi-kriteria: tanggal, petugas, shift, pangkat, kelompok pangkat, satuan kerja, jenis kendaraan
- DataTables dengan pagination, sorting, dan search
- Export CSV dengan BOM UTF-8 (kompatibel Excel)
- Tampilan laporan berbeda: Admin (semua data) vs Petugas (log pribadi)

### 📱 Progressive Web App (PWA)
- Install ke homescreen HP/tablet tanpa App Store
- Offline fallback page
- Service worker dengan cache strategy
- Shortcut langsung ke Mobile Scanner
- Update banner otomatis saat ada versi baru
- Tombol install di sidebar

---

## 🗄️ Struktur Database

```
users               → akun admin & petugas
personel            → data personel militer
pangkat             → master data pangkat (FK dari personel)
satuan_kerja        → master data satker (FK dari personel)
kendaraan           → data kendaraan milik personel
kendaraan_foto      → galeri foto kendaraan (multi foto)
qrcode              → QR Code per kendaraan
log_keluar          → log setiap scan QR
ci_sessions         → session database
```

---

## 🛠️ Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Backend Framework | CodeIgniter 4 |
| Database | MySQL 8.x |
| Frontend CSS | Bootstrap 5 |
| Font Icons | Bootstrap Icons |
| Chart | Chart.js 4 |
| Tabel Data | DataTables 1.10 |
| QR Generator | chillerlan/php-qrcode v5.x |
| QR Scanner | html5-qrcode 2.3.x |
| UUID | ramsey/uuid |
| PWA | Service Worker + Web App Manifest |
| Font | Inter, JetBrains Mono, Rajdhani |

---

## 📁 Struktur Direktori

```
vehicle-gate-system/
├── app/
│   ├── Controllers/
│   │   ├── Auth.php
│   │   ├── Dashboard.php
│   │   ├── Personel.php
│   │   ├── Kendaraan.php
│   │   ├── Pangkat.php
│   │   ├── Satker.php
│   │   ├── QRCode.php
│   │   ├── Scan.php
│   │   ├── Laporan.php
│   │   └── Pwa.php
│   ├── Models/
│   │   ├── UserModel.php
│   │   ├── PersonelModel.php
│   │   ├── KendaraanModel.php
│   │   ├── KendaraanFotoModel.php
│   │   ├── PangkatModel.php
│   │   ├── SatkerModel.php
│   │   ├── QRCodeModel.php
│   │   └── LogKeluarModel.php
│   ├── Views/
│   │   ├── layouts/
│   │   │   ├── main.php
│   │   │   └── auth.php
│   │   ├── auth/
│   │   ├── dashboard/
│   │   ├── personel/
│   │   ├── kendaraan/
│   │   ├── pangkat/
│   │   ├── satker/
│   │   ├── qrcode/
│   │   ├── scan/
│   │   │   ├── index.php       ← Hardware scanner
│   │   │   ├── monitor.php     ← Fullscreen TV mode
│   │   │   └── mobile.php      ← Mobile camera mode
│   │   ├── laporan/
│   │   └── pwa/
│   ├── Filters/
│   │   ├── AuthFilter.php
│   │   └── RoleFilter.php
│   └── Database/
│       ├── Migrations/
│       └── Seeds/
├── public/
│   ├── uploads/
│   │   ├── personel/
│   │   └── kendaraan/
│   ├── assets/
│   │   └── icons/              ← PWA icons
│   ├── manifest.json           ← PWA manifest
│   └── sw.js                   ← Service Worker
└── composer.json
```

---

## 🚀 Instalasi

### Prasyarat
- PHP >= 8.0
- MySQL >= 8.0
- Composer
- Ekstensi PHP: `gd`, `intl`, `mbstring`, `mysqli`
- Web server: Apache / Nginx

### Langkah Instalasi

**1. Clone repository**
```bash
git clone https://github.com/username/vehicle-gate-system.git
cd vehicle-gate-system
```

**2. Install dependencies**
```bash
composer install
```

**3. Konfigurasi environment**
```bash
cp env .env
```

Edit file `.env`:
```env
CI_ENVIRONMENT = development

database.default.hostname = localhost
database.default.database = vehicle_gate_db
database.default.username = root
database.default.password = your_password
database.default.DBDriver = MySQLi

app.baseURL = 'http://your-intranet-ip/vehicle-gate-system/public/'
```

**4. Buat database**
```sql
CREATE DATABASE vehicle_gate_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
```

**5. Jalankan migration & seeder**
```bash
php spark migrate
php spark db:seed AdminSeeder
```

**6. Buat folder upload**
```bash
mkdir -p public/uploads/personel
mkdir -p public/uploads/kendaraan
mkdir -p public/assets/icons
chmod 755 public/uploads/personel
chmod 755 public/uploads/kendaraan
```

**7. Akses aplikasi**
```
http://your-intranet-ip/vehicle-gate-system/public/
```

### Akun Default
```
Username : admin
Password : admin123
```
> ⚠️ **Segera ganti password** setelah login pertama!

---

## 📖 Panduan Penggunaan

### Alur Admin
```
Login → Dashboard
  ├── Input Data Pangkat & Satker
  ├── Input Data Personel (NRP, foto, no HP)
  ├── Input Data Kendaraan (foto galeri, foto STNK, field STNK)
  ├── Generate QR Code per Kendaraan → Download/Cetak Kartu
  ├── Kelola User Petugas
  └── Monitor Laporan & Rekap
```

### Alur Petugas
```
Login → (Auto-redirect berdasarkan perangkat)
  ├── Desktop → Halaman Scan (hardware scanner)
  ├── Monitor → Fullscreen mode (/petugas/scan/monitor)
  └── HP/Tablet → Mobile scanner (/petugas/scan/mobile)
              ↓
         Scan QR Kartu Kendaraan
              ↓
         Tampil: Foto Personel + Detail Kendaraan + Galeri Foto
              ↓
         Log Tersimpan Otomatis
```

### Alur Scan QR
```
Petugas scan QR kendaraan
      ↓
Sistem cari kode QR → dapat kendaraan_id
      ↓
Validasi: QR aktif? Kendaraan tidak dibekukan? Personel aktif?
      ↓
Tampilkan hasil: identitas personel + detail kendaraan
      ↓
Log otomatis tersimpan (kendaraan_id, petugas_id, timestamp, shift)
```

---

## 🔒 Keamanan

- Aplikasi **hanya dapat diakses melalui jaringan intranet**
- Semua endpoint dilindungi dengan autentikasi session
- Role-based access: Admin dan Petugas memiliki akses berbeda
- CSRF token pada setiap form dan AJAX request
- UUID digunakan sebagai identifier di URL (bukan ID integer)
- Data personel militer tidak pernah dikirim ke luar jaringan
- QR Code dapat di-revoke kapan saja oleh admin
- Kendaraan dibekukan = QR ditolak saat scan

---

## 📱 PWA — Install ke Homescreen

### Android (Chrome)
1. Buka URL aplikasi di Chrome
2. Tunggu banner "Tambahkan ke layar utama" muncul
3. Atau: Menu Chrome (⋮) → "Tambahkan ke layar utama"
4. Icon VGS akan muncul di homescreen

### iOS (Safari)
1. Buka URL aplikasi di Safari
2. Tap tombol Share (□↑)
3. Pilih "Tambahkan ke Layar Utama"
4. Konfirmasi → Done

---

## 🗺️ Roadmap

- [ ] Brute force protection & password strength
- [ ] QR Code HMAC signature untuk keamanan lebih tinggi
- [ ] Database backup & restore
- [ ] Notifikasi real-time (kendaraan dibekukan mencoba scan)
- [ ] Export laporan PDF berkop satuan
- [ ] Audit trail perubahan data
- [ ] Mode offline dengan background sync
- [ ] Manajemen shift manual oleh admin

---

## 🤝 Kontribusi

Proyek ini dikembangkan untuk keperluan internal. Untuk pertanyaan atau kontribusi, silakan hubungi pengembang.

---

## 📄 Lisensi

Proyek ini bersifat **proprietary** dan dikembangkan untuk penggunaan internal lingkungan militer. Dilarang mendistribusikan atau memodifikasi tanpa izin.

---

<div align="center">

**Vehicle Gate Monitoring System**

*Dibangun dengan ❤️ untuk keamanan dan ketertiban lingkungan kerja*

`CodeIgniter 4` · `MySQL` · `Bootstrap 5` · `PWA`

</div>
