# LAPORAN STANDAR PEMROGRAMAN (CODING STANDARD)
**Mata Kuliah:** Software Engineering | **Dosen Pengampu:** Christian, S.Kom., M.MT.  
**Program Studi:** Informatika, Universitas Ciputra Surabaya (2026)  
**Aplikasi:** Subscription Tracker (Team 5)  
**Anggota Kelompok:** Arya Pramudika (1076012614909), Dessica Suhaedi Hartono (0706012324017), Diardo Marendi Krista (070612424024), Gita Iriana (070612424021), Muhammad Hafiz Adra (070612414007)

---

### 1. Pendahuluan dan Tujuan
Penerapan standar pemrograman (*coding standard*) dan gaya penulisan kode (*programming style*) pada proyek *Subscription Tracker* bertujuan untuk menjaga konsistensi sintaks, meningkatkan keterbacaan (*readability*), mempermudah pemeliharaan jangka panjang (*maintainability*), serta meminimalkan potensi cacat logika (*defects*) dan celah keamanan selama kolaborasi tim lintas pengembang.

### 2. Aturan Penamaan (*Naming Agreements*)
Seluruh penamaan elemen kode wajib menggunakan bahasa Inggris deskriptif yang mencerminkan fungsi spesifiknya:
- **Classes, Interfaces, Enums:** Menggunakan **PascalCase** (contoh: `SubscriptionController`, `DashboardService`, `BillingPeriod`).
- **Methods & Functions:** Menggunakan **camelCase** berawalan kata kerja (contoh: `getActiveSubscriptions()`, `isDue()`, `markAsConverted()`).
- **Variables & Properties:** Menggunakan **camelCase** yang ringkas dan intuitif (contoh: `nextPaymentDate`, `monthlyExpense`; *tanpa singkatan ambigu*).
- **Database Tables & Columns:** Menggunakan **snake_case** jamak untuk tabel (`subscriptions`, `free_trials`) dan tunggal untuk kolom (`user_id`, `notify_before`).
- **Constants & Enum Cases:** Menggunakan **UPPER_SNAKE_CASE** (contoh: `PAYMENT_DUE`, `FREE_TRIAL_END`, `ACTIVE`, `CANCELLED`).

### 3. Pedoman Gaya dan Struktur Kode (*Programming Style & Architecture*)
- **Standar Format (PSR-12):** Mengikuti rekomendasi PSR-12 dengan indentasi 4 spasi (bukan tab), penempatan kurawal pembuka konsisten, batas panjang baris ideal 80–120 karakter, dan tanpa *trailing whitespace*.
- **Pemisahan Lapisan Arsitektur (*Layered Architecture Boundary*):**
  1. *Controller Layer:* Menangani routing, validasi request melalui `FormRequest`, mendelegasikan alur ke Service, dan mengembalikan response JSON/view. Dilarang memuat *raw query* atau logika bisnis di Controller.
  2. *Service Layer:* Memusatkan seluruh logika bisnis (*business rules*), seperti kalkulasi pengeluaran bulanan/tahunan (`DashboardService`) dan evaluasi jatuh tempo pengingat (`NotificationService`).
  3. *Repository / Model Layer:* Mengisolasi interaksi data ke basis data menggunakan query terparametrisasi / Eloquent ORM.
- **Type Safety & Declaration:** Wajib mencantumkan deklarasi tipe data eksplisit (*strict type hinting*) pada setiap parameter dan *return type* fungsi, serta memanfaatkan PHP 8.2+ Backed Enums untuk representasi status diskrit.

### 4. Keamanan dan Praktik Rekayasa (*Security & Best Practices*)
- **Proteksi Data (NFR-004):** Kata sandi wajib di-hash menggunakan `bcrypt` sebelum persistensi. Metode pembayaran hanya mencatat nama alias/label (dilarang meminta atau menyimpan nomor kartu kredit/CVV/PIN).
- **Pencegahan Celah Injeksi & XSS:** Query wajib terhindar dari *string concatenation* langsung guna menangkal SQL Injection. Pada layer presentasi Blade, output wajib memanfaatkan *auto-escaping* `{{ $var }}`.

### 5. Dokumentasi dan Manajemen Versi (*Version Control*)
- **Dokumentasi Kode (PHPDoc):** Ditulis pada metode dengan logika kalkulasi kompleks untuk menerangkan konteks parameter, return value, dan kemungkinan *exception*.
- **Konvensi Commit Git:** Pesan commit wajib berbahasa Inggris dengan format terstruktur Conventional Commits (misal: `feat:`, `fix:`, `refactor:`, `docs:`) guna mempermudah peninjauan riwayat perubahan (*audit trail*).
