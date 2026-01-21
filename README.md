# 📢 Sistem Pengaduan Kampus

Sistem manajemen pengaduan kampus berbasis web yang memungkinkan mahasiswa untuk menyampaikan keluhan, dosen untuk memberikan tanggapan, dan admin untuk mengelola seluruh proses pengaduan.
<img width="1920" height="1080" alt="image" src="https://github.com/user-attachments/assets/18c20582-dce9-4893-a193-2723bf6e540e" />


## 🌟 Fitur Utama

### 👨‍🎓 Mahasiswa
- Registrasi dan login akun
- Submit pengaduan baru
- Melihat status pengaduan yang telah diajukan
- Dashboard untuk monitoring pengaduan

### 👨‍🏫 Dosen
- Login ke sistem
- Melihat daftar pengaduan
- Memberikan tanggapan terhadap pengaduan
- Dashboard pengelolaan pengaduan

### 👨‍💼 Admin
- Dashboard monitoring semua pengaduan
- Update status pengaduan
- Export data pengaduan ke Excel
- Manajemen sistem secara keseluruhan

## 🛠️ Teknologi yang Digunakan

- **Backend:** PHP
- **Database:** MySQL
- **Frontend:** HTML, CSS
- **Containerization:** Docker & Docker Compose
- **Export:** Library Excel (PHPSpreadsheet/PHPExcel)

## 📁 Struktur Proyek

```
pengaduan_kampus/
├── admin/                  # Modul admin
│   ├── admin_dashboard.php
│   ├── export_excel.php
│   └── update_status.php
├── assets/                 # File CSS
│   ├── dosen.css
│   ├── mahasiswa.css
│   └── styles.css
├── dosen/                  # Modul dosen
│   ├── beri_tanggapan.php
│   ├── dashboard.php
│   └── lihat_pengaduan.php
├── mahasiswa/              # Modul mahasiswa
│   ├── dashboard.php
│   ├── pengaduan_saya.php
│   └── submit.php
├── db.php                  # Konfigurasi database
├── docker-compose.yml      # Docker compose config
├── Dockerfile             # Docker image config
├── index.php              # Landing page
├── login.php              # Halaman login
├── logout.php             # Proses logout
├── register.php           # Halaman registrasi
├── reset_password.php     # Reset password
└── navbar.php             # Komponen navbar
```

## 🚀 Instalasi

### Prasyarat
- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Docker dan Docker Compose (opsional)

### Instalasi Manual

1. Clone repository ini
```bash
git clone https://github.com/username/pengaduan_kampus.git
cd pengaduan_kampus
```

2. Import database
```bash
mysql -u root -p < sql_dump.sql.txt
```

3. Konfigurasi database di `db.php`
```php
$host = "localhost";
$user = "root";
$pass = "";
$db = "pengaduan_kampus";
```

4. Jalankan server PHP
```bash
php -S localhost:8000
```

5. Akses aplikasi di browser
```
http://localhost:8000
```

### Instalasi dengan Docker

1. Clone repository
```bash
git clone https://github.com/username/pengaduan_kampus.git
cd pengaduan_kampus
```

2. Jalankan Docker Compose
```bash
docker-compose up -d
```

3. Import database (jika diperlukan)
```bash
docker exec -i mysql_container mysql -uroot -ppassword pengaduan_kampus < sql_dump.sql.txt
```

4. Akses aplikasi
```
http://localhost:8080
```

## 📝 Penggunaan

### Registrasi Mahasiswa
1. Buka halaman registrasi
2. Isi form dengan data yang valid
3. Login dengan kredensial yang telah dibuat

### Submit Pengaduan
1. Login sebagai mahasiswa
2. Pilih menu "Submit Pengaduan"
3. Isi detail pengaduan
4. Submit dan tunggu tanggapan

### Memberikan Tanggapan (Dosen)
1. Login sebagai dosen
2. Lihat daftar pengaduan
3. Pilih pengaduan yang ingin ditanggapi
4. Berikan tanggapan dan update status

### Manajemen Pengaduan (Admin)
1. Login sebagai admin
2. Akses dashboard admin
3. Monitor semua pengaduan
4. Export data jika diperlukan

## 🗄️ Database

Detail struktur database dapat dilihat di:
- `langkah_database.txt` - Langkah-langkah setup database
- `sql_dump.sql.txt` - SQL dump untuk import database

## 🤝 Kontribusi

Kontribusi sangat diterima! Silakan:
1. Fork repository ini
2. Buat branch fitur baru (`git checkout -b fitur-baru`)
3. Commit perubahan (`git commit -m 'Menambahkan fitur baru'`)
4. Push ke branch (`git push origin fitur-baru`)
5. Buat Pull Request

## 📄 Lisensi

Project ini menggunakan lisensi MIT. Lihat file `LICENSE` untuk detail lebih lanjut.

## 👨‍💻 Pengembang

Dibuat dengan ❤️ oleh Iqbal

## 📞 Kontak

Jika ada pertanyaan atau saran, silakan hubungi:
- Email: iqbalguntur313@gmail.com
- GitHub: https://github.com/MfBally354

---

⭐ Jangan lupa beri bintang jika project ini bermanfaat!
