# Hafizuna: Sistem Informasi Manajemen Hafalan Al-Qur'an
### SD Islam Al Azhar 27 Cibinong
[**Link Website / Demo**](https://hafizuna.web.id)

---

## 📜 Deskripsi Proyek
**Hafizuna** adalah aplikasi web modern berbasis **Laravel** dan **Livewire** yang dirancang untuk mendigitalkan proses manajemen hafalan Al-Qur'an (Tahfidz). Aplikasi ini menggantikan metode pencatatan manual (buku penghubung) menjadi sistem terintegrasi yang transparan, efisien, dan *real-time*.

Sistem ini menghubungkan **Guru**, **Orang Tua**, dan **Admin Sekolah** dalam satu platform untuk memantau perkembangan hafalan siswa, mencatat setoran harian, serta menghasilkan laporan penilaian secara otomatis. Berdasarkan pengujian *usability* (USE Questionnaire), sistem ini mendapatkan skor **97.93%** dengan predikat **"Sangat Layak"**.

Proyek ini merupakan hasil Tugas Akhir Mata Kuliah **Rekayasa Perangkat Lunak** dari Politeknik Statistika STIS.

## ✨ Fitur Utama
Aplikasi ini memiliki tiga aktor utama dengan hak akses yang disesuaikan:

#### 👤 Admin
* **Manajemen Data Master:** Mengelola data Siswa, Guru, Orang Tua, Kelas, dan Periode Akademik
* **Manajemen Kelompok:** Mengatur pembagian kelompok halaqah siswa dengan guru pembimbingnya
* **Monitoring & Log:** Memantau seluruh aktivitas sistem melalui Log Aktivitas dan melihat rekapitulasi global
* **Pengaturan Kurikulum:** Menentukan target hafalan per kelompok dan bobot penilaian sistem (Tajwid, Makhroj, Kelancaran)
* **Laporan Menyeluruh:** Mengunduh laporan hafalan tingkat kelompok dan keseluruhan dalam format PDF/Excel

#### 👨‍🏫 Guru
* **Input Setoran Hafalan:** Mencatat hafalan siswa (Surah, Ayat Awal-Akhir) dengan penilaian Tajwid, Makhroj, dan Kelancaran secara *real-time*
* **Koreksi Interaktif:** Memberikan koreksi pada kata/ayat yang salah dengan menandai langsung di teks Al-Qur'an
* **Notifikasi Otomatis:** Mengirim notifikasi hasil setoran ke email orang tua
* **Laporan Kelompok:** Mengunduh rekap perkembangan siswa dalam kelompok bimbingan
* **Manajemen Kelompok:** Mengelola data siswa dalam kelompok yang dibimbing

#### 👨‍👩‍👧 Orang Tua
* **Dashboard Monitoring:** Melihat ringkasan statistik perkembangan hafalan anak secara visual
* **Riwayat Setoran:** Melihat detail setoran harian (tanggal, surah, ayat, nilai per aspek, dan catatan koreksi guru)
* **Notifikasi:** Menerima pemberitahuan via email setiap ada setoran hafalan baru
* **Laporan Detail:** Mengunduh laporan perkembangan hafalan anak dalam format PDF/Excel

## 🛠 Teknologi yang Digunakan
* **Backend Framework:** Laravel 11 (PHP 8.2+)
* **Frontend Framework:** Livewire 3 (Fullstack Reactivity)
* **Styling:** Tailwind CSS + Alpine.js
* **Database:** MySQL
* **Fitur Tambahan:**
    * `DomPDF` (Cetak Laporan PDF)
    * `Maatwebsite Excel` (Export Data Excel)
    * Email Notification System (Laravel Mail)
    * Log Aktivitas Otomatis

## 🚀 Cara Menjalankan Proyek

**1. Prasyarat:**
* PHP >= 8.2
* Composer
* Node.js & NPM
* MySQL Server
* XAMPP/WAMP/LAMP (opsional)

**2. Instalasi:**
```bash
# Clone repository
git clone https://github.com/muhammadludviarg/hafizuna-aplikasi-web.git

# Masuk ke direktori
cd hafizuna-app

# Install dependencies PHP
composer install

# Install dependencies JavaScript
npm install

# Setup Environment
cp .env.example .env
php artisan key:generate
```

**3. Konfigurasi Database:**
* Buka phpMyAdmin atau MySQL client
* Buat database baru (misal: `hafizuna_db`)
* Import file SQL yang tersedia di folder `database/` (jika ada)
* Sesuaikan kredensial di file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hafizuna_db
DB_USERNAME=root
DB_PASSWORD=
```

* Jalankan migrasi dan seeder:
```bash
php artisan migrate --seed
```

**4. Konfigurasi Email (Opsional):**
Untuk fitur notifikasi email, sesuaikan konfigurasi di `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

**5. Menjalankan Aplikasi:**
```bash
# Terminal 1 - Server PHP
php artisan serve

# Terminal 2 - Compile Assets (di terminal terpisah)
npm run dev
```

Akses aplikasi di browser: `http://localhost:8000`

## 🔐 Akun Testing
Untuk mencoba sistem, gunakan akun berikut:

**Admin:**
* Email: `admin1@hafizuna.com`
* Password: `Password123!`

**Guru:**
* Email: `suharyono@guru.dummy.com`
* Password: `password123`

**Orang Tua:**
* Email: `indra.lesmana@dummy.com`
* Password: `password123`

## 🌳 Struktur Proyek

Proyek ini menggunakan arsitektur MVC yang dimodifikasi dengan pola **Livewire Component**:

```
hafizuna-app/
├── app/
│   ├── Helpers/              # Fungsi bantuan global
│   ├── Http/
│   │   ├── Controllers/      # Controller untuk Auth, Export, dll
│   │   └── Middleware/       # Middleware autentikasi & otorisasi
│   ├── Livewire/             # LOGIKA UTAMA (Komponen Livewire)
│   │   ├── Admin/            # Komponen untuk Admin
│   │   ├── Guru/             # Komponen untuk Guru
│   │   ├── OrangTua/         # Komponen untuk Orang Tua
│   │   └── Auth/             # Komponen autentikasi
│   ├── Models/               # Eloquent Models (representasi tabel)
│   └── Exports/              # Logika export Excel
├── database/
│   ├── migrations/           # Definisi skema database
│   └── seeders/              # Data dummy & master data
├── resources/
│   ├── views/
│   │   ├── layouts/          # Template layout utama
│   │   ├── livewire/         # View Livewire components
│   │   └── exports/          # Template PDF
│   ├── css/                  # File CSS (Tailwind)
│   └── js/                   # File JavaScript
├── routes/
│   ├── web.php               # Route web utama
│   └── auth.php              # Route autentikasi
├── public/                   # Aset publik (gambar, CSS/JS compiled)
├── storage/                  # File storage & logs
└── vendor/                   # Dependencies Composer
```

### Penjelasan Komponen Penting

1. **`app/Livewire/Guru/InputNilai.php`**: Komponen inti untuk input setoran hafalan dengan fitur koreksi interaktif
2. **`app/Livewire/Admin/LogAktivitas.php`**: Menampilkan audit trail aktivitas pengguna
3. **`app/Models/SesiHafalan.php`**: Model utama untuk transaksi setoran hafalan
4. **`database/seeders/QuranSeeder.php`**: Seeder data 114 surah Al-Qur'an dan ayat-ayatnya

## 🗃️ Skema Database

Database dirancang ternormalisasi dengan 17 tabel utama:

### 1. Manajemen Pengguna & Autentikasi

| Tabel | Deskripsi | Relasi Penting |
|-------|-----------|----------------|
| `akun` | Data akun login (email, password_hash, status) | Parent untuk semua user |
| `admin` | Profil administrator | `id_akun` → `akun` |
| `guru` | Profil guru | `id_akun` → `akun` |
| `orang_tua` | Profil orang tua siswa | `id_akun` → `akun` |
| `siswa` | Profil siswa | `id_kelas`, `id_ortu` |

### 2. Organisasi Akademik

| Tabel | Deskripsi |
|-------|-----------|
| `kelas` | Data kelas (nama, tahun ajaran) |
| `kelompok` | Kelompok hafalan/halaqah |
| `siswa_kelompok` | Pivot: alokasi siswa ke kelompok |
| `target_hafalan_kelompok` | Target hafalan per kelompok dan periode |

### 3. Data Al-Qur'an

| Tabel | Deskripsi |
|-------|-----------|
| `surah` | Master 114 surah (nomor, nama, jumlah ayat) |
| `ayat` | Master data ayat per surah (teks Arab, terjemahan) |

### 4. Transaksi Hafalan (Core System)

| Tabel | Deskripsi | Kolom Penting |
|-------|-----------|---------------|
| **`sesi_hafalan`** | Record setoran hafalan siswa | `id_siswa`, `id_guru`, `ayat_mulai`, `ayat_selesai`, `skor_tajwid`, `skor_makhroj`, `skor_kelancaran`, `nilai_rata` |
| **`koreksi`** | Detail koreksi kesalahan bacaan | `id_sesi`, `id_ayat`, `kata_ke`, `kategori_kesalahan`, `catatan` |
| **`sistem_penilaian`** | Parameter penilaian & grading | `aspek`, `grade`, `proporsi_minimal`, `proporsi_maksimal` |

### 5. Sistem Pendukung

| Tabel | Deskripsi |
|-------|-----------|
| `notifikasi` | Log notifikasi ke orang tua |
| `log_aktivitas` | Audit trail sistem |
| `password_resets` | Token reset password |

**Diagram ERD lengkap** tersedia di laporan milestone (halaman 50-62).

## 📊 Hasil Evaluasi Sistem

Berdasarkan pengujian dengan **7 responden** (2 admin, 4 guru, 1 orang tua):

### Black Box Testing
✅ **100% Success Rate** - Semua fitur berfungsi sesuai spesifikasi

### USE Questionnaire (Usability Testing)

| Aspek | Skor | Kategori |
|-------|------|----------|
| **Usefulness** | 98.57% | Sangat Layak |
| **Ease of Use** | 96.57% | Sangat Layak |
| **Ease of Learning** | 96.42% | Sangat Layak |
| **Satisfaction** | 100% | Sangat Layak |
| **TOTAL** | **97.93%** | **Sangat Layak** |

## 📝 Dokumentasi Lengkap

Dokumentasi teknis lengkap meliputi:
- Use Case Diagram & Description
- Activity Diagram
- Sequence Diagram
- Class Diagram
- Rancangan Antarmuka (Figma)
- Analisis Risiko & Mitigasi

Tersedia di: **[Laporan Milestone 4](./K6_3SD1_RPL_2025_Laporan%20Milestone%204.pdf)**

## 👥 Tim Penyusun

**Kelompok 6 - 2KS3 (Politeknik Statistika STIS)**

| Nama | NIM | Role |
|------|-----|------|
| **Abdul Hanif Al Fatah** | 222312938 | Developer |
| **Aurelia Ayala Naura** | 222313006 | Developer |
| **Fadzilla Kusuma Ningrum** | 222313071 | Developer |
| **Immanita Denawinta Ginting** | 222313138 | Developer |
| **Mario Mikail H. Simanjuntak** | 222313196 | Developer |
| **Moses Noel Estomihi Simanullang** | 222313218 | Developer |
| **Muhammad Ludvi Argorahayu** | 222313248 | Project Lead & Developer |

---

## 📞 Kontak & Dukungan

**Klien:** SD Islam Al Azhar 27 Cibinong  
**Institusi:** Politeknik Statistika STIS  
**Mata Kuliah:** Rekayasa Perangkat Lunak  
**Tahun:** 2025  

---

## 📄 Lisensi

Proyek ini dikembangkan untuk keperluan akademik dan internal SD Islam Al Azhar 27 Cibinong.  
Hak cipta dilindungi sesuai dengan ketentuan yang disepakati antara tim pengembang dan klien.

---