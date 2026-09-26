# Adara Resto - Product Management System

Aplikasi pengelolaan menu makanan dan minuman restoran berbasis web sederhana yang dibuat menggunakan **PHP Native** dan **MySQL (PDO)**. Aplikasi ini dirancang untuk memudahkan manajemen produk restoran dengan antarmuka yang bersih, modern, dan responsif.

---

## 🚀 Fitur Utama

- **Dashboard Statistik**: Menampilkan total menu, total stok porsi, dan indikator status stok secara otomatis.
- **Manajemen Menu (CRUD)**:
  - **Create**: Menambah menu makanan atau minuman baru beserta harga dan stok porsi.
  - **Read**: Menampilkan daftar menu dalam bentuk kartu/tabel dengan format mata uang Rupiah.
  - **Update**: Mengubah data menu, kategori, harga, maupun stok.
  - **Delete**: Menghapus menu dari daftar database.
- **Pencarian Dinamis**: Mencari menu secara instan berdasarkan nama atau filter kategori (Makanan / Minuman).
- **Validasi Keamanan**:
  - Menggunakan **PDO Prepared Statements** untuk mencegah serangan SQL Injection.
  - Validasi input harga dan stok untuk memastikan data konsisten.

---

## 🛠️ Teknologi yang Digunakan

- **Backend**: PHP (Native PDO)
- **Database**: MySQL / MariaDB
- **Frontend**: HTML5, CSS3, FontAwesome (Ikon)
- **Server**: Apache (via XAMPP / Laragon)

---

## 📁 Struktur Folder Proyek

```text
product-manager/
├── config/
│   └── db.php           # Koneksi database menggunakan PDO
├── database/
│   └── store_db.sql     # Schema database dan data awal
├── public/
│   ├── assets/
│   │   └── style.css    # Styling antarmuka web
│   ├── create.php       # Form tambah menu
│   ├── delete.php       # Proses hapus menu
│   ├── edit.php         # Form & proses edit menu
│   └── index.php        # Halaman utama & daftar menu
└── README.md            # Dokumentasi proyek