# IT Inventory Management

Web application internal untuk pengelolaan aset dan operasional inventory Divisi IT. Sistem ini dirancang untuk memusatkan data aset IT, melacak lokasi dan penanggung jawab perangkat, mencatat riwayat mutasi/perpindahan, mengelola peminjaman dan pengembalian, serta mendokumentasikan pemeliharaan (maintenance) secara terstruktur dan auditable.

---

## 🛠 Tech Stack

- **Backend:** Laravel 13.x (PHP 8.3+)
- **Database:** MySQL 8.0+
- **Frontend:** Blade Templates & Reusable Components
- **Styling:** Tailwind CSS
- **Authentication & Authorization:** Laravel Auth, Gates/Policies, Middleware

---

## 📋 Fitur Utama (MVP Scope)

1. **Authentication & Role-Based Access Control (RBAC):**
   - Role Administrator & IT Staff (dan opsional Viewer).
   - Route & action protection via Middleware and Gates/Policies.
   - Manajemen user dan status akun aktif/nonaktif.

2. **Master Data Management:**
   - Kategori Aset, Lokasi/Ruangan (hierarkis via `parent_id`), dan Departemen.
   - Status operasional standar (`available`, `assigned`, `borrowed`, `maintenance`, `retired`).
   - Kondisi fisik standar (`good`, `minor_damage`, `major_damage`).

3. **Asset Management:**
   - CRUD Aset dengan `asset_tag` unik (auto-generate / manual validation).
   - Detail aset komprehensif beserta riwayat lengkap.
   - Pencarian, filter kategori/lokasi/status, sorting, dan pagination.
   - Soft delete / retire untuk integritas data.

4. **Mutasi & Perubahan Penanggung Jawab:**
   - Pencatatan perpindahan lokasi dan assignment penanggung jawab.
   - Histori mutasi otomatis tercatat secara append-only.

5. **Peminjaman & Pengembalian (Loans):**
   - Peminjaman aset berstatus *available*.
   - Validasi aset ganda / aset dalam perbaikan.
   - Pencatatan tanggal kembali, due date, dan kondisi fisik saat kembali.

6. **Pemeliharaan (Maintenance Records):**
   - Pencatatan isu kerusakan, teknisi, tindakan perbaikan, biaya, dan hasil akhir.
   - Penguncian status aset saat maintenance aktif.

7. **Dashboard & Laporan:**
   - KPI ringkasan total aset, status, sebaran kategori & lokasi.
   - Pantauan aset dipinjam dan dalam perbaikan secara real-time.
   - Laporan terfilter berdasarkan periode, kategori, lokasi, dan status.

8. **Audit Trail & Data Integrity:**
   - Pencatatan aktor dan waktu di setiap transaksi krusial.
   - Transaksi database atomik (ACID) untuk mencegah inkonsistensi status.

---

## 🚀 Panduan Setup & Instalasi Lokal

### Prasyarat
- PHP >= 8.3 (ekstensi: pdo_mysql, mbstring, openssl, tokenizer, xml, ctype, json, bcmath)
- Composer
- Node.js (v18+) & NPM
- MySQL 8.0+ (misal via [Laragon](https://laragon.org/) — **pastikan service MySQL sudah berjalan**)

> **⚠️ Catatan Laragon:** Sebelum menjalankan migrasi, buka Laragon dan klik **Start All** untuk memastikan MySQL berjalan di port 3306.

### Langkah Instalasi
```bash
# 1. Clone repository
git clone https://github.com/naufalFaqih/it-inventory-management.git
cd it-inventory-management

# 2. Instalasi dependensi backend
composer install

# 3. Konfigurasi Environment
cp .env.example .env
php artisan key:generate
```

Edit file `.env` dan sesuaikan konfigurasi database (sudah tersedia di `.env.example`):
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=it_inventory
DB_USERNAME=root
DB_PASSWORD=        # kosongkan untuk Laragon default
```

Buat database `it_inventory` terlebih dahulu di MySQL/phpMyAdmin, kemudian:
```bash
# 4. Jalankan Migrasi & Seeder
php artisan migrate --seed

# 5. Instalasi dependensi frontend & build asset
npm install
npm run build

# 6. Jalankan Server Pengembangan
php artisan serve
```

Buka browser: **http://localhost:8000/login**

### Akun Bawaan (Seeder)

| Email | Password | Role |
|-------|----------|------|
| admin@inventory.local | password | Administrator |
| staff@inventory.local | password | IT Staff |
| viewer@inventory.local | password | Viewer (read-only) |

> **Catatan:** Akun `inactive@inventory.local` sengaja dinonaktifkan — tidak dapat login.

---

## 📄 Lisensi
Internal Proprietary - Divisi IT.
