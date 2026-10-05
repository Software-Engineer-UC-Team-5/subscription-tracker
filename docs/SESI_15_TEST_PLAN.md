# LAPORAN RANCANGAN PENGUJIAN PERANGKAT LUNAK (SOFTWARE EVALUATION & TEST CASES)
**Mata Kuliah:** Software Engineering  
**Institusi:** Universitas Ciputra Surabaya - Informatika (2026)  
**Dosen Pengampu:** Theresia Ratih Dewi Saputri, Ph.D.  
**Aplikasi:** Subscription Tracker  

---

### 1. Metodologi Pengujian (V-Model Integration)
Berdasarkan pendekatan **V-Model**, pengujian sistem *Subscription Tracker* dirancang berlapis dari tingkat terkecil hingga penerimaan pengguna akhir:
1. **Unit Testing (Module Level):** Menguji metode individual pada *Service Layer* dan *Domain Model* secara terisolasi (misal: validasi logika `isDue()` dan kalkulasi biaya tahunan).
2. **Component & System Testing (Integration Level):** Memverifikasi interaksi antar lapisan (Controller, Service, Repository, Database, dan Scheduler) dalam memproses transaksi data.
3. **Acceptance Testing / UAT (Requirements Level):** Memastikan sistem memenuhi kebutuhan fungsional (FR-001 s.d. FR-006) dan use case (UC01 s.d. UC12) dari perspektif pengguna akhir (*Black Box Testing*).

---

### 2. Tabel Kasus Uji (Test Cases Specification)

| ID Uji | Use Case / Kebutuhan | Skenario Pengujian | Data Uji / Langkah | Hasil yang Diharapkan | Kategori Pengujian | Status |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **TC-01** | UC01 / FR-001 | Pendaftaran akun baru (*Register*) | Input: Nama, Email valid, Password $\ge$ 8 karakter | Akun berhasil dibuat, password di-hash bcrypt, redirect ke dashboard | System / UAT | Pending |
| **TC-02** | UC02 / FR-001 | Autentikasi pengguna (*Login*) | Input email & password yang cocok | Sesi login aktif, token terbentuk, diarahkan ke dashboard | System / UAT | Pending |
| **TC-03** | UC05 / FR-002 | Tambah subscription reguler | Input: Spotify, Rp 54.990, Monthly, Kategori Hiburan | Data tersimpan di tabel `subscriptions`, muncul di daftar aktif | System / UAT | Pending |
| **TC-04** | UC10 / FR-004 | Tambah subscription dengan *Free Trial* | Centang *is_free_trial*, start_date: hari ini, end_date: +14 hari | Record tersimpan di `subscriptions` dan relasi 1-to-1 di `free_trials` | Component / System | Pending |
| **TC-05** | UC07 / FR-002 | Edit data langganan (Update) | Ubah harga atau tanggal pembayaran berikutnya | Data di database terbarui, log aktivitas mencatat aksi `UPDATE` | System / UAT | Pending |
| **TC-06** | UC08 / FR-002 | Pembatalan langganan (Cancel/Delete) | Ubah status menjadi `CANCELLED` atau klik Hapus | Status berubah menjadi non-aktif / data terhapus, log `DELETE` | System / UAT | Pending |
| **TC-07** | UC09 / FR-003 | Pencarian & Filter langganan | Filter berdasarkan Kategori & Status `ACTIVE` | Menampilkan hanya langganan yang cocok dengan kriteria filter | UAT | Pending |
| **TC-08** | UC04 / FR-005 | Kalkulasi pengeluaran bulanan | Langganan 1: Rp 100.000 (Monthly), Langganan 2: Rp 1.200.000 (Yearly) | Total bulanan terhitung tepat Rp 200.000 (`calculateMonthlyExpense`) | Unit Testing | Pending |
| **TC-09** | UC04 / FR-005 | Deteksi langganan mendekati jatuh tempo | Tanggal tagihan H+3 dari hari ini | Terdeteksi di list *upcoming payments* dashboard (`getUpcomingPayments`) | Unit Testing | Pending |
| **TC-10** | UC11 / FR-006 | Konfigurasi pengingat (*Reminder*) | Pilih subscription, tipe `PAYMENT_DUE`, notify: 3 hari sebelum | Konfigurasi tersimpan di tabel `reminders` dengan status aktif | System / UAT | Pending |
| **TC-11** | UC12 / FR-006 | Eksekusi Scheduler Pengingat Otomatis | Jalankan artisan command `app:check-reminders` saat ada tagihan H-3 | Notifikasi berstatus `PENDING` dibuat, email terkirim, status berubah `SENT` | Component / System | Pending |
| **TC-12** | NFR-004 | Keamanan Metode Pembayaran | Input metode pembayaran: "BCA Debit Utama" | Sistem hanya menyimpan nama alias, tidak meminta/menyimpan nomor kartu | System / Security | Pending |

---

### 3. Petunjuk Pengisian Hasil Pengujian (Post-Implementation)
Setelah boilerplate dan antarmuka aplikasi siap diuji:
1. Jalankan pengujian otomatis via PHPUnit: `php artisan test`.
2. Lakukan simulasi skenario manual pada browser untuk TC-01 s.d. TC-12.
3. Ubah kolom **Status** dari `Pending` menjadi `PASS` / `FAIL`, dan sertakan lampiran tangkapan layar (*screenshot*) bukti uji pada bab lampiran laporan akhir.
