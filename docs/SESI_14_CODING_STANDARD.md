# LAPORAN STANDAR PEMROGRAMAN (CODING STANDARD)
**Mata Kuliah:** Software Engineering  
**Institusi:** Universitas Ciputra Surabaya - Informatika (2026)  
**Dosen Pengampu:** Theresia Ratih Dewi Saputri, Ph.D.  
**Aplikasi:** Subscription Tracker  

---

### 1. Pendahuluan dan Tujuan
Standar pemrograman (*coding standard*) dan gaya pemrograman (*programming style*) diterapkan pada proyek pengembangan *Subscription Tracker* untuk menjamin konsistensi, keterbacaan kode (*readability*), kemudahan pemeliharaan (*maintainability*), serta efisiensi kolaborasi tim. Dengan pedoman yang seragam, seluruh pengembang dapat memahami, memodifikasi, dan meninjau kode (*code review*) secara efektif serta meminimalisir potensi cacat logika maupun celah keamanan.

### 2. Aturan Penamaan (*Naming Agreement*)
Seluruh penamaan identitas kode wajib menggunakan bahasa Inggris deskriptif yang mencerminkan fungsi dan tujuannya secara spesifik:
* **Classes, Enums, Interfaces:** Menggunakan format **PascalCase** (Contoh: `SubscriptionController`, `BillingPeriod`, `NotificationService`).
* **Methods & Functions:** Menggunakan format **camelCase** dengan kata kerja awal (Contoh: `getActiveSubscriptions()`, `isDue()`, `markAsConverted()`).
* **Variables & Properties:** Menggunakan format **camelCase** yang jelas dan ringkas (Contoh: `nextPaymentDate`, `monthlyExpense`; *hindari singkatan ambigu atau bahasa campur*).
* **Database Tables & Columns:** Menggunakan format **snake_case** jamak untuk tabel dan tunggal untuk kolom (Contoh: tabel `subscriptions`, kolom `user_id`, `cancel_before_days`).
* **Constants & Enum Cases:** Menggunakan format **UPPER_SNAKE_CASE** (Contoh: `PAYMENT_DUE`, `FREE_TRIAL_END`, `ACTIVE`).

### 3. Pedoman Struktur dan Gaya Kode (*Programming Style*)
* **Indentation & Spacing:** Mengikuti standar **PSR-12** (PHP Standard Recommendation) menggunakan 4 spasi (bukan tab) dan tidak menyisakan *trailing whitespace* di akhir baris.
* **Architecture Layering Boundary:**
  1. *Controller Layer:* Bertanggung jawab murni menerima request, memanggil validasi `FormRequest`, mendelegasikan ke Service, dan mengembalikan HTTP response. Dilarang menulis raw query SQL atau manipulasi database langsung di Controller.
  2. *Service Layer:* Menampung seluruh aturan bisnis (*business logic*), seperti kalkulasi estimasi biaya dan validasi status masa *free trial*.
  3. *Repository Layer:* Satu-satunya lapisan data access untuk isolasi query database dengan *parameterized queries* guna mencegah SQL Injection.
* **Type Safety & Declaration:** Wajib mencantumkan deklarasi tipe data eksplisit (*type hinting*) pada argumen fungsi dan nilai kembalian (*return type*), serta memanfaatkan PHP 8.2+ Backed Enums untuk nilai diskrit.

### 4. Dokumentasi dan Manajemen Versi (*Documentation & Version Management*)
* **Source Code Documentation:** Menggunakan format PHPDoc pada fungsi atau logika kalkulasi kompleks untuk memberikan konteks parameter dan pengecualian (*exceptions*), tanpa mengulang hal yang sudah jelas terbaca dari nama fungsi.
* **Version Management:** Menggunakan Git dengan pesan commit terstruktur berbahasa Inggris (format: `feat:`, `fix:`, `refactor:`, `docs:`) untuk menjaga riwayat perubahan yang jelas dan memfasilitasi integrasi antar anggota tim pengembang.
