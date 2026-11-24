# HelpDesk – IT Support Ticketing System

Aplikasi web untuk melaporkan, mendistribusikan, dan menyelesaikan kendala teknis di lingkungan kampus melalui sistem tiket terpusat.
Dibangun dengan **Laravel 12 · MySQL · Blade · Tailwind CSS** (auth: Laravel Breeze).

> Status: **Tahap 1 (MVP)** dan **Tahap 2 (Enhancement)** selesai, **Tahap 3 (Advanced)** sedang berjalan (notifikasi email sudah selesai). Lihat [Roadmap](#roadmap).

## Fitur Tahap 3 – Advanced (berjalan)

| Area | Isi |
|---|---|
| Notifikasi email | Email otomatis saat **status tiket berubah** (ke pelapor), saat **tiket ditugaskan** (ke teknisi), dan saat ada **komentar baru** (ke pelapor/teknisi lain di tiket). Orang yang melakukan aksi tidak menerima email atas aksinya sendiri. Email dikirim setelah transaksi database berhasil; jika pengiriman gagal, error dicatat ke log tanpa membatalkan aksi tiket. |

### Konfigurasi email

Secara default `MAIL_MAILER=log`, jadi email **tidak benar-benar dikirim**, melainkan ditulis ke `storage/logs/laravel.log`. Ini cocok untuk pengembangan.

Untuk melihat email dalam bentuk tampilan asli, gunakan SMTP testing seperti [Mailpit](https://mailpit.axllent.org/) atau Mailtrap:

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="helpdesk@kampus.local"
MAIL_FROM_NAME="${APP_NAME}"
```

Lalu buka inbox Mailpit di http://127.0.0.1:8025.

## Fitur Tahap 2 – Enhancement

| Area | Isi |
|---|---|
| Search & filter | Pencarian kata kunci (subject, nomor tiket, deskripsi) serta filter status, kategori, prioritas, rentang tanggal dibuat, dan teknisi (khusus admin, termasuk "Belum ditugaskan"). Filter tersimpan di URL, jadi bisa dibagikan atau di-bookmark. Hasil pencarian tetap dibatasi sesuai hak akses masing-masing role. |
| Lampiran | Satu lampiran per tiket (gambar atau dokumen: JPG, PNG, GIF, WEBP, PDF, DOC/DOCX, XLS/XLSX, TXT; maks. 5 MB). Validasi mencakup **ekstensi + MIME dari isi file + ukuran**, sehingga file `.php` yang diganti namanya menjadi `.jpg` akan ditolak. File disimpan **privat** (`storage/app/private`) dengan nama acak dan hanya disajikan lewat route yang dicek `TicketPolicy`. Gambar tampil sebagai pratinjau; dokumen selalu diunduh. Requester bisa mengganti atau menghapus lampiran selama tiket masih Open. |
| Reports (admin) | Halaman **Reports** dengan filter tanggal dan preset (7/30/90 hari, tahun ini). Isinya: ringkasan (tiket masuk, tingkat penyelesaian, rata-rata waktu penyelesaian, belum ditugaskan), grafik per periode (harian, atau bulanan bila rentang > 62 hari), per status, per prioritas, per kategori, dan tabel kinerja teknisi. Grafik memakai **Chart.js**, yang hanya dimuat di halaman bergrafik. |
| Export CSV | Unduh semua tiket dalam rentang tanggal laporan (UTF-8 dengan BOM agar rapi di Excel). Data di-*stream* per 500 baris, dan sel yang diawali `= + - @` dinetralkan untuk mencegah *CSV/formula injection*. |
| Dashboard admin | Grafik donat status dan grafik batang kategori. |
| Ticket History | Menu khusus teknisi berisi tiket yang sudah Resolved/Closed. |
| Profil | Ubah nama/email/password dan hapus akun (dari Breeze). |

## Fitur Tahap 1 – MVP

| Area | Isi |
|---|---|
| Autentikasi | Register (otomatis sebagai Requester), login, logout, reset password, profil. Password di-hash (bcrypt), CSRF aktif. |
| Role & akses | 3 role: `admin`, `technician`, `requester`. Middleware `role:` untuk area admin, `TicketPolicy` untuk akses per tiket. Role tidak bisa diisi lewat form register/profil, dan admin tidak bisa mengubah role dirinya sendiri. |
| Dashboard | Berbeda per role: kartu statistik status, daftar tiket terbaru, jumlah teknisi, tiket belum ditugaskan, tiket per kategori. |
| Tiket | Buat (status otomatis **Open**, nomor unik `TCK-YYYYMMDD-00001`), daftar ter-scope per role + pagination + filter status (dan teknisi untuk admin), detail, edit sesuai kewenangan, hapus (admin). |
| Penugasan | Admin menugaskan atau mengganti teknisi (dengan info jumlah tiket aktif per teknisi). |
| Status & prioritas | Alur `Open → In Progress → Resolved → Closed` dengan aturan per role (lihat di bawah). Admin dan teknisi yang ditugaskan dapat mengubah prioritas. |
| Komentar | Percakapan antara requester, teknisi, dan admin. Hanya pihak yang berwenang yang bisa melihat atau menulis; tiket Closed tidak bisa dikomentari. |
| Kategori | CRUD oleh admin, aktif/nonaktif, jumlah tiket per kategori. Kategori yang masih dipakai tiket hanya dinonaktifkan, tidak dihapus. |
| User management | Admin melihat, menambah, dan mengedit akun (termasuk menjadikan user sebagai teknisi). |
| Riwayat aktivitas | Setiap pembuatan, penugasan, perubahan status/prioritas, dan komentar dicatat di `ticket_activities` dan ditampilkan sebagai timeline. |

### Aturan alur status

| Role | Transisi yang diizinkan |
|---|---|
| Technician (hanya tiket yang ditugaskan kepadanya) | Open → In Progress, In Progress → Resolved |
| Requester (hanya tiket miliknya) | Resolved → Closed (konfirmasi selesai) |
| Administrator | Ke status mana pun (termasuk membuka kembali tiket) |

Requester hanya bisa mengedit subject/deskripsi/kategori selama tiket masih **Open**.

## Prasyarat

- PHP **8.2+** dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `fileinfo` (wajib untuk deteksi MIME lampiran)
- `php.ini`: `upload_max_filesize` dan `post_max_size` minimal **6M** agar lampiran 5 MB bisa diunggah (default XAMPP hanya 2M; Laragon sudah cukup)
- Composer 2
- Node.js 18+ dan npm
- MySQL 8 (atau MariaDB 10.6+)
- Disarankan: **Laragon**, XAMPP, atau Laravel Herd

## Instalasi

```bash
# 1. Ambil source code
git clone <url-repo> help-desk
cd help-desk

# 2. Dependensi
composer install
npm install

# 3. Konfigurasi environment
cp .env.example .env          # Windows CMD: copy .env.example .env
php artisan key:generate
```

Sesuaikan koneksi database di `.env` (default sudah cocok untuk Laragon/XAMPP):

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=helpdesk
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Siapkan database — pilih salah satu

**Opsi A – Migration + Seeder (disarankan)**

```bash
# buat database kosong terlebih dahulu, misal lewat HeidiSQL/phpMyAdmin atau:
mysql -u root -e "CREATE DATABASE helpdesk CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

php artisan migrate --seed
```

**Opsi B – Impor skrip SQL**

```bash
mysql -u root < database/sql/helpdesk.sql
```

`database/sql/helpdesk.sql` membuat database `helpdesk` beserta seluruh tabel dan data awal yang sama dengan hasil seeder.

### 5. Build aset dan jalankan

```bash
npm run build
php artisan serve
```

Buka **http://127.0.0.1:8000**.
Jika port 8000 sudah dipakai aplikasi lain, gunakan `php artisan serve --port=8010` lalu buka http://127.0.0.1:8010.

> Untuk pengembangan dengan hot-reload: jalankan `npm run dev` di terminal terpisah.

## Akun demo

Semua password: **`password`**

| Role | Email |
|---|---|
| Administrator | `admin@helpdesk.test` |
| Technician | `tech1@helpdesk.test` (Budi Teknisi) |
| Technician | `tech2@helpdesk.test` (Sari Teknisi) |
| Requester | `student1@helpdesk.test` (Andi Mahasiswa) |
| Requester | `student2@helpdesk.test` (Rina Mahasiswa) |

Seeder juga membuat 6 kategori awal (Hardware, Software, Network, Account & Access, Laboratory Equipment, Other) dan 20 tiket contoh dalam berbagai status. Tiket-tiket ini tersebar di 2 bulan terakhir (relatif terhadap tanggal saat seeder dijalankan) dan lengkap dengan komentar serta riwayat aktivitas, sehingga grafik Reports langsung terisi.
Untuk mengembalikan data ke kondisi awal: `php artisan migrate:fresh --seed`.

> Catatan: `database/sql/helpdesk.sql` berisi tanggal tetap (September–November 2026). Bila diimpor jauh setelahnya, pilih rentang tanggal tersebut di halaman Reports, atau jalankan `php artisan migrate:fresh --seed` untuk data dengan tanggal terbaru. Data demo tidak menyertakan file lampiran.

## Menjalankan test

Test memakai database MySQL terpisah, **`helpdesk_test`** (sudah diatur di `phpunit.xml`):

```bash
mysql -u root -e "CREATE DATABASE helpdesk_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan test
```

Feature test (92 test) mencakup:
- **Tahap 1:** akses per role, scoping daftar tiket, 403 untuk tiket milik orang lain, alur status lengkap, larangan melompati alur, penugasan khusus admin, validasi, komentar, manajemen kategori/user, dan perlindungan role saat register/profil.
- **Tahap 2:** pencarian dan filter (termasuk tidak bocor ke tiket orang lain), Ticket History, upload/penolakan lampiran (ekstensi, MIME palsu, ukuran), hak akses unduh lampiran, file tidak bisa diakses publik, ganti/hapus lampiran, agregasi Reports, rentang tanggal, isi export CSV dan pencegahan formula injection.
- **Tahap 3:** penerima notifikasi email per aksi (status, penugasan, komentar), aktor tidak menerima email atas aksinya sendiri, isi email, dan email gagal tidak menggagalkan aksi tiket.
- **Smoke test:** setiap halaman untuk tiap role dirender tanpa error.

## Struktur proyek (bagian penting)

```
app/
  Enums/                Role, TicketStatus, TicketPriority (label + warna badge)
  Http/Controllers/     DashboardController, TicketController (+history, attachment), TicketActionController,
                        Admin/CategoryController, Admin/UserController, Admin/ReportController (+export CSV)
  Http/Middleware/      EnsureUserHasRole (alias: role)
  Http/Requests/        Validasi form (tiket, lampiran, status, assign, komentar, kategori, user)
  Models/               User, Category, Ticket (scope visibleTo + filter), Comment, TicketActivity
  Notifications/        TicketStatusChanged, TicketAssigned, TicketCommented (email)
  Policies/             TicketPolicy (view/update/assign/changeStatus/comment + aturan alur)
  Services/             TicketService (logika bisnis, lampiran, DB transaction, log aktivitas, notifikasi)
                        ReportService (agregasi statistik dengan Query Builder)
  Support/              AttachmentPolicy (format & ukuran lampiran yang diizinkan)
database/
  migrations/           users(+role), categories, tickets, comments, ticket_activities
  seeders/              UserSeeder, CategorySeeder, TicketSeeder
  sql/helpdesk.sql      skrip SQL lengkap
resources/views/
  layouts/              app (sidebar), sidebar, guest
  components/           status-badge, priority-badge, stat-card, card, ticket-table, chart, ...
  dashboard/            admin, technician, requester
  tickets/              index (+history), create, edit, show + partials
  admin/                categories, users, reports
resources/js/charts.js  inisialisasi Chart.js (dimuat dinamis)
tests/Feature/          TicketAccessTest, TicketWorkflowTest, CommentTest, AdminTest, NotificationTest, ...
```

## Skema database

| Tabel | Kolom utama |
|---|---|
| `users` | id, name, email (unique), password (hash), **role** enum(admin, technician, requester) |
| `categories` | id, name (unique), description, is_active |
| `tickets` | id, ticket_number (unique), user_id → users, technician_id → users (nullable), category_id → categories, subject, description, priority enum(low, medium, high, critical), status enum(open, in_progress, resolved, closed), attachment (path privat), attachment_name (nama asli), resolved_at, closed_at |
| `comments` | id, ticket_id → tickets, user_id → users, message |
| `ticket_activities` | id, ticket_id → tickets, user_id → users (nullable), action, old_value, new_value, description, created_at |

## Roadmap

**Tahap 1 – MVP:** ✅ selesai. **Tahap 2 – Enhancement:** ✅ selesai.

**Tahap 3 – Advanced:**

- [x] Notifikasi email untuk perubahan status, penugasan, dan komentar
- [ ] SLA management (target waktu respons/penyelesaian per prioritas)
- [ ] Rating kepuasan setelah tiket ditutup
- [ ] Knowledge base / FAQ
- [ ] Advanced reporting
