# LAPORAN STANDAR PEMROGRAMAN (CODING STANDARD)

**Mata Kuliah:** Software Engineering  
**Institusi:** Universitas Ciputra Surabaya - Informatika (2026)  
**Dosen Pengampu:** Christian, S.Kom., M.MT.  
**Aplikasi:** Subscription Tracker

---

### 1. Pendahuluan dan Tujuan

Standar pemrograman (_coding standard_) dan gaya pemrograman (_programming style_) diterapkan pada proyek pengembangan _Subscription Tracker_ untuk menjamin konsistensi, keterbacaan kode (_readability_), kemudahan pemeliharaan (_maintainability_), serta efisiensi kolaborasi tim. Dengan pedoman yang seragam, seluruh pengembang dapat memahami, memodifikasi, dan meninjau kode (_code review_) secara efektif serta meminimalisir potensi cacat logika maupun celah keamanan.

### 2. Aturan Penamaan (_Naming Agreement_)

Seluruh penamaan identitas kode wajib menggunakan bahasa Inggris deskriptif yang mencerminkan fungsi dan tujuannya secara spesifik:

-   **Classes, Enums, Interfaces:** Menggunakan format **PascalCase** (Contoh: `SubscriptionController`, `BillingPeriod`, `NotificationService`).
-   **Methods & Functions:** Menggunakan format **camelCase** dengan kata kerja awal (Contoh: `getActiveSubscriptions()`, `isDue()`, `markAsConverted()`).
-   **Variables & Properties:** Menggunakan format **camelCase** yang jelas dan ringkas (Contoh: `nextPaymentDate`, `monthlyExpense`; _hindari singkatan ambigu atau bahasa campur_).
-   **Database Tables & Columns:** Menggunakan format **snake_case** jamak untuk tabel dan tunggal untuk kolom (Contoh: tabel `subscriptions`, kolom `user_id`, `cancel_before_days`).
-   **Constants & Enum Cases:** Menggunakan format **UPPER_SNAKE_CASE** (Contoh: `PAYMENT_DUE`, `FREE_TRIAL_END`, `ACTIVE`).

### 3. Pedoman Struktur dan Gaya Kode (_Programming Style_)

-   **Indentation & Spacing:** Mengikuti standar **PSR-12** (PHP Standard Recommendation) menggunakan 4 spasi (bukan tab) dan tidak menyisakan _trailing whitespace_ di akhir baris.
-   **Architecture Layering Boundary:**
    1. _Controller Layer:_ Bertanggung jawab murni menerima request, memanggil validasi `FormRequest`, mendelegasikan ke Service, dan mengembalikan HTTP response. Dilarang menulis raw query SQL atau manipulasi database langsung di Controller.
    2. _Service Layer:_ Menampung seluruh aturan bisnis (_business logic_), seperti kalkulasi estimasi biaya dan validasi status masa _free trial_.
    3. _Repository Layer:_ Satu-satunya lapisan data access untuk isolasi query database dengan _parameterized queries_ guna mencegah SQL Injection.
-   **Type Safety & Declaration:** Wajib mencantumkan deklarasi tipe data eksplisit (_type hinting_) pada argumen fungsi dan nilai kembalian (_return type_), serta memanfaatkan PHP 8.2+ Backed Enums untuk nilai diskrit.

### 4. Dokumentasi dan Manajemen Versi (_Documentation & Version Management_)

-   **Source Code Documentation:** Menggunakan format PHPDoc pada fungsi atau logika kalkulasi kompleks untuk memberikan konteks parameter dan pengecualian (_exceptions_), tanpa mengulang hal yang sudah jelas terbaca dari nama fungsi.
-   **Version Management:** Menggunakan Git dengan pesan commit terstruktur berbahasa Inggris (format: `feat:`, `fix:`, `refactor:`, `docs:`) untuk menjaga riwayat perubahan yang jelas dan memfasilitasi integrasi antar anggota tim pengembang.
