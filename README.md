# Subscription Tracker

Sistem manajemen dan pelacak biaya langganan berbasis web (Laravel 12 / PHP 8.2+) untuk memantau siklus tagihan, masa _free trial_, pengingat jatuh tempo, dan riwayat notifikasi.

---

## 01. PRASYARAT SISTEM

Sebelum memulai, pastikan perangkat Anda telah terpasang:

-   **PHP**: Versi 8.2 atau lebih baru (disarankan 8.3 / 8.4)
-   **Composer**: Versi 2.x
-   **Database**: MySQL atau MariaDB (port default 3306)
-   **Web Server / Lingkungan Lokal**:
    -   **Laravel Herd** (Sangat disarankan), ATAU
    -   **PHP CLI (`php artisan serve`)** / **XAMPP**

---

## 02. PANDUAN INSTALASI & SETUP (LANGKAH DEMI LANGKAH)

> [!IMPORTANT]
> Jalankan perintah berikut secara berurutan di dalam terminal untuk menyiapkan proyek dari awal:

### Langkah 1: Kloning Repository

Buka terminal dan unduh repositori ini ke komputer lokal Anda:

```bash
git clone https://github.com/Software-Engineer-UC-Team-5/subscription-tracker.git
cd subscription-tracker
```

### Langkah 2: Instal Dependensi PHP

Jalankan instalasi dependensi vendor via Composer:

```bash
composer install
```

### Langkah 3: Konfigurasi Berkas Environment (.env)

Salin template konfigurasi `.env.example` menjadi `.env`:

-   **Windows (PowerShell / CMD):**
    ```powershell
    copy .env.example .env
    ```
-   **macOS / Linux:**
    ```bash
    cp .env.example .env
    ```

Buka berkas `.env` dan sesuaikan kredensial koneksi basis data MySQL lokal Anda:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=subscription_tracker
DB_USERNAME=root
DB_PASSWORD=root
```

> [!NOTE]
> Jika menggunakan XAMPP standar tanpa kata sandi, biarkan `DB_PASSWORD=` kosong. Jika menggunakan Laravel Herd, sesuaikan dengan kata sandi MySQL lokal Anda.

### Langkah 4: Generate Application Encryption Key

Buat kunci enkripsi aplikasi:

```bash
php artisan key:generate
```

### Langkah 5: Eksekusi Migrasi Basis Data & Seeder

Pastikan service database MySQL aktif, kemudian jalankan seluruh migrasi dan seed data awal:

```bash
php artisan migrate:fresh --seed
```

Perintah ini akan membuat 10 tabel domain dan mengisi akun demo serta 5 kategori default (Streaming, Gaming, Productivity, Cloud, Music).

---

## 03. CARA MENJALANKAN APLIKASI DI BROWSER

Pilih salah satu metode berikut sesuai lingkungan yang Anda gunakan:

### METODE A: Menggunakan Laravel Herd (Rekomendasi)

Jika menggunakan **Laravel Herd**:

1. Pastikan direktori proyek berada di dalam folder yang dipantau oleh Herd (_parked_ atau _linked_).
2. Buka browser dan akses alamat lokal:
    ```text
    http://subscription-tracker.test
    ```
    _(Tidak perlu menjalankan perintah server tambahan di terminal)._

> [!TIP]
> Laravel Herd secara otomatis mengelola service PHP, Nginx, dan DNS lokal `.test`.

### METODE B: Menggunakan Server Lokal Bawaan (Artisan Serve)

Jika menggunakan XAMPP atau PHP CLI standar:

1. Jalankan server lokal melalui terminal proyek:
    ```bash
    php artisan serve
    ```
2. Buka browser dan akses alamat default:
    ```text
    http://127.0.0.1:8000
    ```

---

## 04. KREDENSIAL AKUN DEMO

Setelah proses seed berhasil, Anda dapat langsung menguji login dengan akun bawaan berikut:

| Parameter      | Kredensial Default |
| :------------- | :----------------- |
| **Email**      | `user@example.com` |
| **Kata Sandi** | `password`         |

_(Fitur registrasi akun baru juga dapat dicoba mandiri melalui halaman `/register`)._

---

## 05. AUTOMATED TESTING

Proyek ini telah dilengkapi automated feature tests untuk menguji seluruh alur autentikasi, proteksi sesi, dan ketersediaan 37 rute aplikasi:

```bash
php artisan test
```

---

## 06. STRUKTUR MODUL & 37 RUTE AKTIF

Seluruh rute aplikasi telah terdaftar aktif tanpa status 404:

-   **Autentikasi**: Masuk akun (`/login`), pendaftaran (`/register`), dan keluar sesi (`/logout`).
-   **Dashboard & Analisis**: Ringkasan biaya langganan dan jadwal tagihan mendatang (`/dashboard`).
-   **Subscriptions**: Manajemen langganan aktif, arsip, dan riwayat pembayaran (`/subscriptions`).
-   **Kategori**: Pengelompokan jenis layanan langganan (`/categories`).
-   **Metode Pembayaran**: Pengelolaan instrumen pembayaran seperti kartu, e-wallet, dan transfer bank (`/payment-methods`).
-   **Pengingat (Reminders)**: Konfigurasi pengingat jatuh tempo dan akhir masa _free trial_ (`/reminders`).
-   **Notifikasi**: Riwayat notifikasi sistem/email dan penandaan baca (`/notifications`).
-   **Log Aktivitas**: Audit trail riwayat tindakan pengguna pada sistem (`/activity-logs`).

---

## 07. PANDUAN FRONTEND

-   **Framework CSS**: Menggunakan **Tailwind CSS via CDN** yang diatur terpusat di [resources/views/layouts/app.blade.php](resources/views/layouts/app.blade.php).
-   **Kanvas Bersih**: Seluruh view telah terintegrasi dengan layout modern full-width, bilah samping (sidebar) responsif, dan kanvas fleksibel untuk monitor standar maupun ultrawide.

---

## 08. PANDUAN PENGINGAT OTOMATIS (SCHEDULER, QUEUE & NOTIFIKASI)

Sistem Subscription Tracker dilengkapi fungsionalitas otomatis untuk memeriksa jatuh tempo autodebit dan batas akhir masa _free trial_ (FR-006 / UC11 / UC12):

### 1. Menjalankan Task Scheduler (Pengecekan Pengingat)

Sistem memiliki command scheduler bawaan yang dikonfigurasi untuk berjalan setiap 15 menit (`routes/console.php`):

-   **Di Lingkungan Pengembangan Lokal (Local Development)**:
    Jalankan worker scheduler melalui terminal terpisah:
    ```bash
    php artisan schedule:work
    ```
-   **Pengecekan Pengingat Manual (On-Demand)**:
    Anda dapat memicu pemeriksaan jatuh tempo secara instan kapan saja tanpa menunggu scheduler:
    ```bash
    php artisan app:check-reminders
    ```

### 2. Menjalankan Queue Worker (Pemrosesan Latar Belakang)

Jika ingin memproses antrean email atau job di latar belakang:

1. Pastikan koneksi antrean di berkas `.env` menggunakan database:
    ```env
    QUEUE_CONNECTION=database
    ```
2. Jalankan antrean worker di terminal:
    ```bash
    php artisan queue:work
    ```

### 3. Pengujian Notifikasi & Email Lokal

Untuk menguji pengiriman notifikasi pengingat secara lokal:

-   Set driver email pada berkas `.env` ke `log` agar isi email tercatat rapi di log aplikasi tanpa membutuhkan server SMTP sungguhan:
    ```env
    MAIL_MAILER=log
    ```
-   Saat scheduler atau command `php artisan app:check-reminders` mendeteksi langganan yang memasuki jadwal pengingat (misal: H-3 atau H-1):
    1. Record notifikasi baru otomatis masuk ke tabel `notifications` dan memicu **bubble badge merah** di navbar/sidebar.
    2. Mailable `SubscriptionReminderMail` akan otomatis dikirim dan isi pesannya tercatat di berkas `storage/logs/laravel.log`.

---
