# PRODUCT REQUIREMENTS DOCUMENT (PRD)
# HelpDesk – IT Support Ticketing System

**Versi:** 1.0  
**Status:** Draft  
**Platform:** Web Application  
**Backend Framework:** Laravel 12  
**Database:** MySQL  
**Frontend:** Blade + Tailwind CSS  
**Target Pengguna:** Mahasiswa, Staf IT, dan Administrator

---

## 1. Project Overview

### 1.1 Latar Belakang

Dalam lingkungan kampus, berbagai permasalahan teknis dapat terjadi, seperti gangguan koneksi internet, masalah perangkat komputer, kerusakan perangkat laboratorium, hingga kendala akses sistem informasi.

Proses pelaporan masalah yang masih dilakukan secara informal melalui pesan pribadi atau komunikasi langsung sering kali menyebabkan laporan tidak terdokumentasi dengan baik, sulit dipantau, dan tidak memiliki riwayat penyelesaian yang terstruktur.

HelpDesk merupakan aplikasi berbasis web yang dirancang untuk memfasilitasi proses pelaporan, pengelolaan, dan penyelesaian permasalahan teknis melalui sistem ticketing terpusat.

Setiap laporan akan direpresentasikan sebagai sebuah tiket yang memiliki informasi pelapor, kategori masalah, tingkat prioritas, teknisi yang bertanggung jawab, status pengerjaan, serta riwayat komunikasi.

### 1.2 Product Vision

Membangun platform IT Support Ticketing System yang mempermudah pengguna dalam melaporkan kendala teknis sekaligus membantu tim IT mengelola, memantau, dan menyelesaikan laporan secara terstruktur dan transparan.

### 1.3 Objectives

1. Menyediakan sistem pelaporan masalah teknis secara terpusat.
2. Mempermudah administrator dalam mendistribusikan tiket kepada teknisi.
3. Memungkinkan pengguna memantau perkembangan laporan secara real-time melalui pembaruan status.
4. Menyediakan dokumentasi dan riwayat penyelesaian setiap masalah.
5. Menampilkan statistik performa penanganan laporan melalui dashboard.

---

## 2. Target Users

HelpDesk memiliki tiga jenis pengguna dengan hak akses berbeda.

### 2.1 Student / Requester

Pengguna yang mengalami kendala teknis dan membutuhkan bantuan dari tim IT.

Hak akses:
- Membuat tiket laporan baru.
- Melihat daftar tiket miliknya.
- Melihat detail dan status tiket.
- Menambahkan komentar pada tiket.
- Melihat riwayat penanganan tiket.
- Menutup tiket yang sudah selesai jika diperlukan.

### 2.2 IT Support / Technician

Petugas yang bertanggung jawab menangani laporan teknis.

Hak akses:
- Melihat tiket yang ditugaskan kepadanya.
- Melihat detail permasalahan.
- Memperbarui status tiket.
- Menambahkan komentar atau catatan penanganan.
- Mengubah prioritas tiket sesuai kewenangan.
- Menandai tiket sebagai selesai.

### 2.3 Administrator

Pengguna dengan hak akses tertinggi yang bertanggung jawab mengelola sistem.

Hak akses:
- Mengelola seluruh tiket.
- Melihat seluruh laporan yang masuk.
- Menugaskan tiket kepada teknisi.
- Mengelola akun pengguna.
- Mengelola kategori permasalahan.
- Mengelola prioritas tiket.
- Melihat statistik dan laporan.
- Memantau aktivitas penanganan tiket.

---

## 3. Functional Requirements

### FR-01: Authentication & Authorization

Sistem harus menyediakan mekanisme autentikasi dan pembatasan akses berdasarkan role pengguna.

Fitur:
- Login menggunakan email dan password.
- Register akun untuk pengguna.
- Logout.
- Validasi kredensial.
- Password disimpan menggunakan hashing.
- Role-based access control.
- Pembatasan halaman berdasarkan role.

Acceptance Criteria:
- Pengguna yang belum login tidak dapat mengakses halaman dashboard.
- Pengguna hanya dapat mengakses fitur sesuai role.
- Pengguna tidak dapat mengubah role miliknya sendiri.
- Password tidak disimpan dalam bentuk plaintext.

### FR-02: Dashboard

Sistem menyediakan dashboard yang menampilkan informasi ringkas berdasarkan role pengguna.

Dashboard Administrator:
- Total tiket.
- Tiket Open.
- Tiket In Progress.
- Tiket Resolved.
- Tiket Closed.
- Jumlah teknisi.
- Grafik laporan berdasarkan kategori dan status.
- Daftar tiket terbaru.

Dashboard Technician:
- Total tiket yang ditugaskan.
- Tiket yang belum ditangani.
- Tiket In Progress.
- Tiket yang sudah diselesaikan.
- Daftar tugas terbaru.

Dashboard Requester:
- Total tiket milik pengguna.
- Tiket Open.
- Tiket In Progress.
- Tiket Resolved.
- Riwayat laporan terbaru.

### FR-03: Ticket Management

Sistem harus menyediakan fitur pengelolaan tiket untuk melaporkan masalah teknis.

Informasi tiket:
- Ticket ID.
- Subject.
- Description.
- Category.
- Priority.
- Status.
- Attachment.
- Reporter.
- Assigned Technician.
- Created At.
- Updated At.

Fitur:
- Membuat tiket baru.
- Melihat daftar tiket.
- Melihat detail tiket.
- Mengedit informasi tiket sesuai kewenangan.
- Melakukan pencarian tiket.
- Filter berdasarkan status, kategori, dan prioritas.
- Pagination.
- Upload lampiran berupa gambar atau dokumen yang diizinkan.

Aturan:
- Setiap tiket memiliki ID unik.
- Setiap tiket harus memiliki subject, description, category, dan priority.
- Tiket baru secara otomatis memiliki status Open.
- Requester hanya dapat melihat tiket miliknya sendiri.
- Administrator dapat melihat seluruh tiket.
- Technician hanya dapat melihat tiket yang ditugaskan kepadanya.

### FR-04: Ticket Assignment

Sistem memungkinkan administrator menugaskan tiket kepada teknisi.

Fitur:
- Memilih teknisi yang tersedia.
- Menentukan teknisi penanggung jawab.
- Mengubah penugasan tiket.
- Melihat daftar tiket berdasarkan teknisi.

Aturan:
- Hanya administrator yang dapat melakukan assignment.
- Tiket yang telah ditugaskan akan terlihat pada dashboard teknisi terkait.
- Perubahan assignment harus tercatat dalam riwayat aktivitas.

### FR-05: Ticket Status Management

Sistem menyediakan siklus status untuk memantau proses penyelesaian masalah.

Status tiket:
- **Open:** Laporan baru diterima dan belum ditangani.
- **In Progress:** Tiket sedang dikerjakan teknisi.
- **Resolved:** Masalah telah ditangani oleh teknisi.
- **Closed:** Tiket telah dikonfirmasi selesai dan ditutup.

Alur utama:

Open → In Progress → Resolved → Closed

Aturan:
- Requester tidak dapat mengubah tiket menjadi In Progress.
- Technician dapat mengubah status tiket yang ditugaskan kepadanya.
- Technician dapat menandai tiket sebagai Resolved.
- Requester atau administrator dapat melakukan penutupan sesuai kebijakan sistem.
- Setiap perubahan status harus tercatat dalam riwayat aktivitas.

### FR-06: Priority Management

Sistem memiliki empat tingkat prioritas.

| Priority | Deskripsi |
|---|---|
| Low | Masalah ringan yang tidak mengganggu aktivitas utama. |
| Medium | Masalah yang mengganggu sebagian aktivitas. |
| High | Masalah yang menghambat aktivitas penting. |
| Critical | Masalah serius yang membutuhkan penanganan segera. |

Prioritas digunakan untuk membantu tim IT menentukan urutan penanganan masalah.

### FR-07: Ticket Comments

Sistem menyediakan fitur komunikasi antara requester dan technician melalui komentar pada tiket.

Fitur:
- Menambahkan komentar.
- Melihat seluruh komentar pada tiket.
- Menampilkan nama pengirim dan waktu komentar.
- Menyimpan riwayat percakapan.

Aturan:
- Komentar harus memiliki isi.
- Komentar terhubung dengan tiket tertentu.
- Hanya pengguna yang memiliki akses terhadap tiket yang dapat melihat dan memberikan komentar.
- Komentar tidak dapat diakses oleh pengguna yang tidak berwenang.

### FR-08: Category Management

Administrator dapat mengelola kategori permasalahan.

Kategori awal:
- Hardware.
- Software.
- Network.
- Account & Access.
- Laboratory Equipment.
- Other.

Fitur:
- Membuat kategori.
- Mengedit kategori.
- Menghapus atau menonaktifkan kategori.
- Melihat jumlah tiket berdasarkan kategori.

### FR-09: Activity History

Sistem harus menyimpan riwayat aktivitas tiket.

Informasi yang dicatat:
- Aktivitas yang dilakukan.
- Pengguna yang melakukan aktivitas.
- Status sebelumnya.
- Status terbaru.
- Waktu aktivitas.
- Keterangan tambahan.

Contoh aktivitas:
- Ticket created.
- Ticket assigned to technician.
- Priority changed.
- Status changed to In Progress.
- Comment added.
- Ticket resolved.
- Ticket closed.

### FR-10: Reporting & Analytics

Administrator dapat melihat laporan pengelolaan tiket.

Fitur:
- Statistik tiket berdasarkan status.
- Statistik tiket berdasarkan kategori.
- Statistik tiket berdasarkan prioritas.
- Jumlah tiket berdasarkan periode.
- Jumlah tiket yang ditangani setiap teknisi.
- Filter berdasarkan tanggal.

Fitur tambahan:
- Export laporan ke CSV.
- Visualisasi menggunakan Chart.js.

---

## 4. Non-Functional Requirements

### NFR-01: Security
- Password wajib menggunakan hashing.
- Semua input pengguna harus divalidasi.
- Menerapkan CSRF protection.
- Mencegah akses data tanpa otorisasi.
- Menggunakan middleware untuk membatasi akses berdasarkan role.
- Validasi MIME type, ukuran, dan ekstensi file upload.
- File lampiran harus disimpan secara aman.

### NFR-02: Performance
- Halaman dashboard harus menampilkan data secara efisien.
- Menggunakan pagination untuk daftar tiket.
- Menghindari query database berulang yang tidak diperlukan.
- Menggunakan eager loading pada relasi Eloquent yang relevan.

### NFR-03: Usability
- Antarmuka responsif untuk desktop dan mobile.
- Navigasi mudah dipahami.
- Status tiket ditampilkan dengan indikator visual.
- Form memiliki validasi dan pesan kesalahan yang jelas.

### NFR-04: Maintainability
- Menggunakan pola arsitektur MVC Laravel.
- Memisahkan logika bisnis dari tampilan.
- Menggunakan migration dan seeder untuk pengelolaan database.
- Menggunakan struktur kode yang konsisten.

---

## 5. Database Design

Berikut rancangan tabel utama yang digunakan.

### 5.1 users

| Field | Type | Description |
|---|---|---|
| id | BIGINT | Primary Key |
| name | VARCHAR | Nama pengguna |
| email | VARCHAR | Email pengguna, unique |
| password | VARCHAR | Hashed password |
| role | ENUM | admin, technician, requester |
| created_at | TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | Waktu diperbarui |

### 5.2 categories

| Field | Type | Description |
|---|---|---|
| id | BIGINT | Primary Key |
| name | VARCHAR | Nama kategori |
| description | TEXT | Deskripsi kategori |
| is_active | BOOLEAN | Status kategori |
| created_at | TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | Waktu diperbarui |

### 5.3 tickets

| Field | Type | Description |
|---|---|---|
| id | BIGINT | Primary Key |
| ticket_number | VARCHAR | Nomor tiket unik |
| user_id | BIGINT | FK ke users sebagai requester |
| technician_id | BIGINT, nullable | FK ke users sebagai technician |
| category_id | BIGINT | FK ke categories |
| subject | VARCHAR | Judul laporan |
| description | TEXT | Detail permasalahan |
| priority | ENUM | low, medium, high, critical |
| status | ENUM | open, in_progress, resolved, closed |
| attachment | VARCHAR, nullable | Lokasi file lampiran |
| resolved_at | TIMESTAMP, nullable | Waktu penyelesaian |
| created_at | TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | Waktu diperbarui |

### 5.4 comments

| Field | Type | Description |
|---|---|---|
| id | BIGINT | Primary Key |
| ticket_id | BIGINT | FK ke tickets |
| user_id | BIGINT | FK ke users |
| message | TEXT | Isi komentar |
| created_at | TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | Waktu diperbarui |

### 5.5 ticket_activities

| Field | Type | Description |
|---|---|---|
| id | BIGINT | Primary Key |
| ticket_id | BIGINT | FK ke tickets |
| user_id | BIGINT, nullable | FK ke users |
| action | VARCHAR | Jenis aktivitas |
| old_value | TEXT, nullable | Nilai sebelumnya |
| new_value | TEXT, nullable | Nilai terbaru |
| description | TEXT, nullable | Keterangan aktivitas |
| created_at | TIMESTAMP | Waktu aktivitas |

### 5.6 Database Relationships

- User memiliki banyak tiket sebagai requester.
- User dapat menangani banyak tiket sebagai technician.
- Category memiliki banyak tiket.
- Ticket memiliki banyak comments.
- User memiliki banyak comments.
- Ticket memiliki banyak ticket activities.
- User dapat membuat banyak ticket activities.

---

## 6. Technology Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 12 |
| Programming Language | PHP |
| Database | MySQL |
| Frontend | Blade Templates |
| Styling | Tailwind CSS |
| JavaScript | JavaScript |
| Charts | Chart.js |
| Authentication | Laravel Authentication Starter Kit |
| ORM | Eloquent ORM |
| Version Control | Git & GitHub |
| Development Environment | Laragon / XAMPP / Laravel Herd |

---

## 7. Laravel Implementation Requirements

Untuk menunjukkan penerapan materi Pemrograman Framework, project harus memanfaatkan fitur inti Laravel berikut.

| Laravel Concept | Implementation |
|---|---|
| Routing | Web routes untuk seluruh halaman |
| MVC | Pemisahan Model, View, dan Controller |
| Migration | Pembuatan struktur database |
| Seeder & Factory | Data awal dan dummy data |
| Eloquent ORM | Relasi antar tabel |
| Blade Template | Layout dan komponen UI |
| Authentication | Login dan logout |
| Middleware | Pembatasan akses berdasarkan role |
| Form Validation | Validasi laporan dan komentar |
| File Storage | Upload lampiran |
| Request & Response | Penanganan HTTP request |
| Query Builder | Query pencarian dan filter |
| Authorization / Policy | Pembatasan akses terhadap tiket |
| Pagination | Daftar tiket |
| Database Transaction | Menjaga konsistensi operasi terkait |
| Testing | Feature test untuk alur utama |

---

## 8. UI/UX Requirements

### 8.1 Design Direction

HelpDesk menggunakan desain dashboard modern dengan pendekatan minimalis dan profesional.

Karakteristik desain:
- Clean dashboard layout.
- Sidebar navigation.
- Card-based statistics.
- Data table yang mudah dibaca.
- Status badges.
- Responsive layout.
- Konsistensi warna dan tipografi.

### 8.2 Main Pages

| Page | Description |
|---|---|
| Login | Autentikasi pengguna |
| Register | Pendaftaran requester |
| Dashboard | Ringkasan informasi |
| Ticket List | Daftar tiket |
| Create Ticket | Form pembuatan tiket |
| Ticket Detail | Detail tiket dan komentar |
| Technician Tasks | Daftar tugas teknisi |
| User Management | Pengelolaan akun |
| Category Management | Pengelolaan kategori |
| Reports | Statistik dan laporan |
| Profile | Pengaturan profil |

### 8.3 Navigation Structure

**Requester**
- Dashboard
- My Tickets
- Create Ticket
- Profile

**Technician**
- Dashboard
- Assigned Tickets
- Ticket History
- Profile

**Administrator**
- Dashboard
- All Tickets
- User Management
- Categories
- Reports
- Profile

---

## 9. Scope & Prioritization

Pengembangan dilakukan secara bertahap agar fitur utama dapat diselesaikan terlebih dahulu.

### Phase 1 – MVP (Must Have)

- Authentication dan role management.
- Dashboard berdasarkan role.
- CRUD ticket.
- Category management.
- Ticket assignment.
- Status management.
- Ticket comments.
- Authorization berdasarkan kepemilikan dan penugasan tiket.
- Database migration dan seeder.

### Phase 2 – Enhancement (Should Have)

- Ticket activity history.
- Search dan filter.
- Upload attachment.
- Dashboard analytics.
- Chart.js visualization.
- Profile management.
- Export laporan CSV.

### Phase 3 – Advanced Features (Could Have)

- Email notification.
- Notifikasi perubahan status.
- SLA management.
- Rating kepuasan pengguna.
- Knowledge base atau FAQ.
- Advanced reporting.

---

## 10. User Flow

### 10.1 Requester Flow

1. Pengguna melakukan login.
2. Pengguna masuk ke dashboard.
3. Pengguna membuka halaman Create Ticket.
4. Pengguna mengisi subject, description, category, dan priority.
5. Pengguna mengirim laporan.
6. Sistem menyimpan tiket dengan status Open.
7. Pengguna dapat melihat perkembangan tiket.
8. Pengguna berkomunikasi dengan teknisi melalui komentar.
9. Pengguna melihat hasil penyelesaian tiket.

### 10.2 Technician Flow

1. Teknisi melakukan login.
2. Sistem menampilkan tiket yang ditugaskan kepadanya.
3. Teknisi membuka detail tiket.
4. Teknisi memulai penanganan dengan mengubah status menjadi In Progress.
5. Teknisi memberikan pembaruan melalui komentar.
6. Teknisi menyelesaikan permasalahan.
7. Teknisi mengubah status menjadi Resolved.

### 10.3 Administrator Flow

1. Administrator melakukan login.
2. Administrator melihat seluruh laporan pada dashboard.
3. Administrator membuka tiket baru.
4. Administrator memeriksa kategori dan prioritas.
5. Administrator memilih teknisi yang bertanggung jawab.
6. Administrator memantau perkembangan tiket.
7. Administrator melihat statistik dan laporan.

---

## 11. Acceptance Criteria

Project HelpDesk dinyatakan memenuhi kebutuhan MVP apabila:

1. Pengguna dapat melakukan registrasi dan login.
2. Sistem dapat membedakan akses Admin, Technician, dan Requester.
3. Requester dapat membuat dan melihat tiket miliknya.
4. Administrator dapat melihat seluruh tiket.
5. Administrator dapat menugaskan tiket kepada teknisi.
6. Technician hanya dapat mengelola tiket yang ditugaskan kepadanya.
7. Status tiket dapat diperbarui sesuai aturan workflow.
8. Pengguna yang berwenang dapat menambahkan komentar.
9. Sistem menyimpan relasi data dengan benar.
10. Dashboard menampilkan informasi yang sesuai dengan role.
11. Sistem menolak akses terhadap tiket yang tidak memiliki izin.
12. Fitur utama dapat dijalankan tanpa error.

---

## 12. Demo Scenario

Untuk demonstrasi di kelas, gunakan skenario berikut:

**Studi Kasus: Gangguan Koneksi Internet Laboratorium**

1. Login sebagai Requester.
2. Buat tiket dengan subject "Internet Connection Unavailable".
3. Pilih kategori Network.
4. Atur priority menjadi High.
5. Kirim laporan dan tunjukkan tiket pada halaman My Tickets.
6. Logout, kemudian login sebagai Admin.
7. Buka tiket yang baru dibuat.
8. Assign tiket kepada salah satu Technician.
9. Logout, kemudian login sebagai Technician.
10. Buka Assigned Tickets dan ubah status menjadi In Progress.
11. Tambahkan komentar mengenai proses pemeriksaan jaringan.
12. Ubah status menjadi Resolved.
13. Login kembali sebagai Requester dan periksa hasil penanganan.
14. Login sebagai Admin dan tunjukkan perubahan statistik dashboard serta riwayat aktivitas.

Skenario ini memperlihatkan proses bisnis secara menyeluruh sekaligus mendemonstrasikan authentication, authorization, CRUD, Eloquent relationship, validation, dan workflow.

---

## 13. Project Success Metrics

Keberhasilan implementasi diukur melalui indikator berikut:

- Seluruh fitur MVP berfungsi sesuai acceptance criteria.
- Tiga role pengguna memiliki pembatasan akses yang benar.
- Seluruh proses ticketing dapat diselesaikan melalui aplikasi.
- Data tiket, komentar, dan aktivitas tersimpan dengan relasi yang konsisten.
- Tidak terdapat akses tidak sah terhadap tiket pengguna lain.
- Dashboard menampilkan statistik sesuai data yang tersimpan.

---

## 14. Future Development

Pengembangan selanjutnya dapat diarahkan menjadi sistem IT Service Management yang lebih komprehensif dengan menambahkan SLA tracking, automated ticket assignment, email notifications, knowledge base, dan integrasi API.

Dengan pengembangan tersebut, HelpDesk tidak hanya berfungsi sebagai aplikasi demonstrasi Laravel, tetapi juga dapat dikembangkan menjadi produk portfolio yang lebih matang.

---

**End of Document**