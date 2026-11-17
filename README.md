# HelpDesk – IT Support Ticketing System

Aplikasi web untuk melaporkan, mendistribusikan, dan menyelesaikan kendala teknis di lingkungan kampus melalui sistem tiket terpusat.
Dibangun dengan **Laravel 12 · MySQL · Blade · Tailwind CSS** (auth: Laravel Breeze).

> Status: **Tahap 1 (MVP)** selesai. Lihat [Roadmap](#roadmap) untuk tahap berikutnya.

## Fitur (Tahap 1 – MVP)

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

- PHP **8.2+** dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`
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

Seeder juga membuat 6 kategori awal (Hardware, Software, Network, Account & Access, Laboratory Equipment, Other) dan 12 tiket contoh dalam berbagai status, lengkap dengan komentar dan riwayat aktivitas.
Untuk mengembalikan data ke kondisi awal: `php artisan migrate:fresh --seed`.

## Menjalankan test

Test memakai database MySQL terpisah, **`helpdesk_test`** (sudah diatur di `phpunit.xml`):

```bash
mysql -u root -e "CREATE DATABASE helpdesk_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan test
```

Feature test mencakup: akses per role, scoping daftar tiket, 403 untuk tiket milik orang lain, alur status lengkap, larangan melompati alur, penugasan khusus admin, validasi, komentar, manajemen kategori/user, dan perlindungan role saat register/profil.

## Struktur proyek (bagian penting)

```
app/
  Enums/                Role, TicketStatus, TicketPriority (label + warna badge)
  Http/Controllers/     DashboardController, TicketController, TicketActionController,
                        Admin/CategoryController, Admin/UserController
  Http/Middleware/      EnsureUserHasRole (alias: role)
  Http/Requests/        Validasi form (tiket, status, assign, komentar, kategori, user)
  Models/               User, Category, Ticket, Comment, TicketActivity
  Policies/             TicketPolicy (view/update/assign/changeStatus/comment + aturan alur)
  Services/             TicketService (logika bisnis + DB transaction + log aktivitas)
database/
  migrations/           users(+role), categories, tickets, comments, ticket_activities
  seeders/              UserSeeder, CategorySeeder, TicketSeeder
  sql/helpdesk.sql      skrip SQL lengkap
resources/views/
  layouts/              app (sidebar), sidebar, guest
  components/           status-badge, priority-badge, stat-card, card, ticket-table, ...
  dashboard/            admin, technician, requester
  tickets/              index, create, edit, show + partials
  admin/                categories, users
tests/Feature/          TicketAccessTest, TicketWorkflowTest, CommentTest, AdminTest
```

## Skema database

| Tabel | Kolom utama |
|---|---|
| `users` | id, name, email (unique), password (hash), **role** enum(admin, technician, requester) |
| `categories` | id, name (unique), description, is_active |
| `tickets` | id, ticket_number (unique), user_id → users, technician_id → users (nullable), category_id → categories, subject, description, priority enum(low, medium, high, critical), status enum(open, in_progress, resolved, closed), attachment, resolved_at, closed_at |
| `comments` | id, ticket_id → tickets, user_id → users, message |
| `ticket_activities` | id, ticket_id → tickets, user_id → users (nullable), action, old_value, new_value, description, created_at |

## Roadmap

**Tahap 2 – Enhancement:** pencarian dan filter lanjutan (kategori/prioritas), upload lampiran (validasi MIME/ukuran, penyimpanan privat), halaman Reports dengan Chart.js (status/kategori/prioritas/periode/teknisi + filter tanggal), export CSV, halaman *Ticket History* teknisi.

**Tahap 3 – Advanced:** notifikasi email dan perubahan status, SLA management, rating kepuasan, knowledge base/FAQ, advanced reporting.
