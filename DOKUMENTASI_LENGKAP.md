# Dokumentasi Lengkap - QR Code Event Invitation System

## 📋 Daftar Isi

1. [Pengenalan](#pengenalan)
2. [Teknologi yang Digunakan](#teknologi-yang-digunakan)
3. [Arsitektur Sistem](#arsitektur-sistem)
4. [Fitur-Fitur yang Tersedia](#fitur-fitur-yang-tersedia)
5. [Instalasi dan Setup](#instalasi-dan-setup)
6. [Struktur Database](#struktur-database)
7. [API Documentation](#api-documentation)
8. [Frontend Components](#frontend-components)
9. [Alur Kerja Aplikasi](#alur-kerja-aplikasi)
10. [Development Guide](#development-guide)
11. [Status Pengembangan](#status-pengembangan)

---

## 🎯 Pengenalan

**QR Code Event Invitation System** adalah sistem manajemen event lengkap yang dibangun dengan Laravel 12 dan Vue 3. Aplikasi ini dirancang untuk membantu pengelola event dalam mengelola peserta, menghasilkan QR code untuk undangan, melakukan check-in/check-out, dan berbagai fitur manajemen event lainnya.

### Tujuan Aplikasi

- Memudahkan pengelolaan peserta event
- Mengotomatisasi proses check-in dengan QR Code
- Memberikan sertifikat otomatis untuk peserta
- Mengelola custom fields untuk kebutuhan data tambahan
- Menyediakan doorprize system yang interaktif
- Real-time notification untuk update check-in

---

## 🛠 Teknologi yang Digunakan

### Backend
- **Framework**: Laravel 12
- **PHP Version**: 8.2+
- **Database**: MySQL
- **Authentication**: Token-based (MD5 untuk legacy support)
- **QR Code Library**: SimpleSoftwareIO/simple-qrcode
- **Excel Processing**: PhpOffice/PhpSpreadsheet
- **PDF Generation**: setasign/fpdf, setasign/fpdi
- **Real-time**: Pusher PHP Server

### Frontend
- **Framework**: Vue 3 (Composition API)
- **Router**: Vue Router 4
- **HTTP Client**: Axios
- **UI Components**: Custom components dengan CSS
- **QR Code Scanner**: html5-qrcode
- **QR Code Generator**: qrcode.vue
- **Date Picker**: flatpickr
- **Select Dropdown**: Select2
- **Icons**: RemixIcon
- **Notifications**: SweetAlert2
- **Real-time**: Pusher JS
- **Build Tool**: Vite

### Development Tools
- **Local Server**: Laravel Herd (optional)
- **Package Manager**: Composer (PHP), NPM (Node.js)
- **Node Version**: 20+

---

## 🏗 Arsitektur Sistem

Aplikasi ini menggunakan **Clean Architecture** dengan prinsip-prinsip berikut:

### 1. Repository Pattern
Repository layer menangani semua operasi database:
- `UserRepository` - Manajemen user
- `EventRepository` - Manajemen event
- `GuestRepository` - Manajemen guest/participant
- `SessionRepository` - Manajemen session

### 2. Service Layer
Business logic berada di service layer:
- `AuthService` - Autentikasi dan otorisasi
- `GuestService` - Logika bisnis untuk guest
- `FourVisionMediaService` - Integrasi dengan API eksternal
- `PusherService` - Real-time notifications

### 3. Controller Layer
Controllers bersifat thin, hanya menangani HTTP request/response:
- Semua controllers berada di `app/Http/Controllers/Api/`
- Menggunakan dependency injection untuk repositories dan services
- Request validation menggunakan Form Request classes

### 4. Request Validation
Form Request classes untuk validasi:
- `CheckInRequest`
- `LoginRequest`
- `UpdateProfileRequest`

### Struktur Direktori

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/          # API Controllers (14 controllers)
│   ├── Requests/         # Form Request Validation
│   └── Resources/        # API Resources (jika ada)
├── Models/               # Eloquent Models (10 models)
├── Repositories/         # Data Access Layer (4 repositories)
├── Services/             # Business Logic Layer (4 services)
└── Providers/           # Service Providers

database/
├── migrations/           # Database Migrations (11 migrations)
├── seeders/             # Database Seeders (3 seeders)
└── factories/           # Model Factories

resources/
├── js/
│   ├── components/      # Vue Components (18 components)
│   ├── styles/          # CSS Files (18 CSS files)
│   ├── utils/           # Utility functions
│   ├── App.vue          # Root component
│   ├── app.js           # Main JS entry
│   └── router.js        # Vue Router configuration
├── css/                 # Global CSS
└── views/               # Blade templates

routes/
├── api.php              # API Routes (50+ endpoints)
├── web.php              # Web Routes
└── console.php          # Console Routes
```

---

## ✨ Fitur-Fitur yang Tersedia

### 1. Authentication & User Management
- ✅ **Registrasi User** - Membuat akun baru dengan email dan password
- ✅ **Login** - Authentication dengan token-based system
- ✅ **Profile Management** - Update nama dan password
- ✅ **Token Authentication** - Setiap request menggunakan token di query parameter

### 2. Event Management
- ✅ **Create Event** - Membuat event baru dengan detail lengkap
- ✅ **Read Event** - Melihat daftar event dan detail event
- ✅ **Update Event** - Mengedit informasi event
- ✅ **Delete Event** - Menghapus event
- ✅ **Event Logo** - Upload dan update logo event
- ✅ **Field Orders** - Mengatur urutan field untuk display

### 3. Guest/Participant Management
- ✅ **Create Guest** - Menambah guest baru secara manual
- ✅ **Read Guest** - Melihat daftar guest dengan filter
- ✅ **Update Guest** - Mengedit informasi guest
- ✅ **Delete Guest** - Menghapus guest
- ✅ **Guest Detail** - Melihat detail lengkap guest
- ✅ **Check-in** - Proses check-in dengan update waktu kedatangan
- ✅ **Check-out** - Proses check-out dengan update waktu keluar
- ✅ **Profile Update & Check-in** - Update profile sekaligus check-in
- ✅ **Guest Filter** - Filter berdasarkan status attend (hadir/belum hadir)

### 4. QR Code System
- ✅ **QR Code Generation** - Generate QR code untuk setiap guest
- ✅ **QR Code Download** - Download QR code individual (PNG)
- ✅ **Bulk QR Download** - Download semua QR code dalam format ZIP
- ✅ **Selected QR Download** - Download QR code terpilih dalam format ZIP
- ✅ **QR Code Scanner** - Scan QR code untuk check-in (menggunakan kamera)

### 5. Import & Export
- ✅ **Excel Import** - Import guest dari file Excel (.xlsx, .xls, .csv)
- ✅ **Template Download** - Download template Excel untuk import
- ✅ **Dynamic Template** - Template Excel menyesuaikan custom fields
- ✅ **Import Koordinator** - Import koordinator dari kolom G
- ✅ **Import Peserta** - Import peserta dari kolom L (multiple names)
- ✅ **Duplicate Detection** - Deteksi dan skip duplicate entries

### 6. Custom Fields
- ✅ **Create Custom Field** - Membuat field tambahan untuk event
- ✅ **Field Types** - Support: input, textarea, file, select, radio, checkbox
- ✅ **Field Options** - Konfigurasi options untuk select/radio/checkbox
- ✅ **Required Fields** - Mark field sebagai required
- ✅ **Field Ordering** - Drag & drop untuk mengatur urutan field
- ✅ **Field Validation** - JSON validation rules
- ✅ **Field Placeholder** - Custom placeholder text

### 7. Certificate System
- ✅ **Certificate Templates** - Template sertifikat yang bisa dipilih
- ✅ **Certificate Configuration** - Setup sertifikat untuk event
- ✅ **Signature Upload** - Upload gambar tanda tangan
- ✅ **Custom Fields in Certificate** - Field tambahan untuk sertifikat
- ✅ **Certificate ID** - Auto-generate atau manual ID sertifikat
- ✅ **Introductory & Completion Phrase** - Teks kustom untuk sertifikat
- ✅ **Organization Info** - Nama organisasi, signatory, dll
- ✅ **Certificate Preview** - Preview sertifikat sebelum save
- ✅ **Certificate Download** - Download sertifikat individual

### 8. Session Management
- ✅ **Create Session** - Membuat session untuk event
- ✅ **Read Session** - Melihat daftar session
- ✅ **Update Session** - Mengedit session
- ✅ **Delete Session** - Menghapus session
- ✅ **Session Assignment** - Assign guest ke session tertentu

### 9. Print System
- ✅ **Print Invitation** - Cetak undangan dalam format PDF

### 10. Preview System
- ✅ **Event Preview** - Preview event dengan data lengkap

### 11. Doorprize System
- ✅ **Event Selection** - Pilih event yang berlangsung hari ini
- ✅ **Spinning Wheel** - Wheel of fortune untuk doorprize
- ✅ **Participant Display** - Tampilkan semua peserta di wheel
- ✅ **Winner Selection** - Acak dan pilih pemenang

### 12. Real-time Features
- ✅ **Pusher Integration** - Real-time notifications
- ✅ **Check-in Notifications** - Notifikasi real-time saat guest check-in
- ✅ **Notification Channel** - Channel berdasarkan user token
- ✅ **Event-based Notifications** - Notifikasi berdasarkan event ID

### 13. Theme & UI
- ✅ **Theme Settings** - Dark/Light mode toggle
- ✅ **Responsive Design** - Mobile-friendly interface
- ✅ **Custom CSS** - Styling yang konsisten
- ✅ **Icon System** - RemixIcon untuk icons
- ✅ **Loading States** - Skeleton loaders
- ✅ **Scroll to Top** - Button untuk scroll ke atas

### 14. External Integration
- ✅ **4Vision Media API** - Submit guest data saat first-time check-in
- ✅ **Error Handling** - Proper error handling dengan logging

### 15. Guide & Help
- ✅ **User Guide** - Panduan penggunaan lengkap dalam aplikasi
- ✅ **Table of Contents** - Navigasi cepat ke bagian guide
- ✅ **Step-by-step Instructions** - Instruksi detail untuk setiap fitur

---

## 📦 Instalasi dan Setup

### Prerequisites

- PHP 8.2 atau lebih tinggi
- Composer
- Node.js 20 atau lebih tinggi
- MySQL
- Laravel Herd (optional, untuk local development)

### Langkah Instalasi

#### 1. Clone atau Download Project

```bash
# Jika dari repository
git clone <repository-url>
cd qr-generate
```

#### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

#### 3. Setup Environment

```bash
# Copy file .env.example ke .env
cp .env.example .env

# Generate application key
php artisan key:generate
```

#### 4. Konfigurasi Database

Edit file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=qrinvite_database
DB_USERNAME=root
DB_PASSWORD=admin123
```

#### 5. Konfigurasi Pusher (Optional)

Tambahkan ke `.env` untuk real-time notifications:

```env
PUSHER_APP_KEY=your_pusher_key
PUSHER_APP_SECRET=your_pusher_secret
PUSHER_APP_ID=your_pusher_id
PUSHER_APP_CLUSTER=mt1
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http
```

#### 6. Jalankan Migration

```bash
php artisan migrate

# (Optional) Run seeders untuk data awal
php artisan db:seed
```

#### 7. Setup Storage Link

```bash
php artisan storage:link
```

#### 8. Setup Herd (jika menggunakan Herd)

File `.herd.yaml` sudah dikonfigurasi:
- PHP: 8.2
- Node: 20
- Webroot: public

Jalankan:
```bash
herd link qr-generate
```

#### 9. Jalankan Development Server

```bash
# Terminal 1: Laravel server (jika tidak menggunakan Herd)
php artisan serve

# Terminal 2: Vite dev server
npm run dev
```

#### 10. Akses Aplikasi

- **Dengan Herd**: `http://qr-generate.test` (atau sesuai konfigurasi Herd)
- **Tanpa Herd**: `http://localhost:8000`

#### 11. Build untuk Production

```bash
# Build frontend assets
npm run build
```

---

## 🗄 Struktur Database

### Tabel: users
Menyimpan data pengguna/admin.

| Column | Type | Description |
|--------|------|-------------|
| id_user | bigint | Primary key, auto increment |
| email | string | Email (unique) |
| password | string | Password (MD5 hash) |
| firstname | string | Nama depan |
| lastname | string | Nama belakang |
| token | string | Authentication token |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diupdate |

### Tabel: events
Menyimpan data event.

| Column | Type | Description |
|--------|------|-------------|
| id_event | bigint | Primary key, auto increment |
| id_user | bigint | Foreign key ke users |
| name_event | string | Nama event |
| date_event | date | Tanggal event |
| location_event | string | Lokasi event |
| guest_total | integer | Total guest |
| event_default_guest_pic | string | Path default guest picture |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diupdate |

### Tabel: sessions
Menyimpan data session untuk event.

| Column | Type | Description |
|--------|------|-------------|
| id_session | bigint | Primary key, auto increment |
| id_event | bigint | Foreign key ke events |
| name_session | string | Nama session |
| time_started_session | time | Waktu mulai |
| time_ended_session | time | Waktu selesai |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diupdate |

### Tabel: guests
Menyimpan data guest/participant.

| Column | Type | Description |
|--------|------|-------------|
| id_guest | bigint | Primary key, auto increment |
| id_event | bigint | Foreign key ke events |
| id_session | bigint | Foreign key ke sessions (nullable) |
| guest_title | string | Jabatan/gelar |
| guest_name | string | Nama guest |
| guest_address | text | Alamat |
| guest_province | string | Provinsi |
| guest_city | string | Kota |
| guest_institution_name | string | Nama institusi |
| guest_email | string | Email |
| guest_phone | string | Telepon |
| guest_label | integer | Label (0=normal, 1=VIP) |
| guest_status | boolean | Status check-in |
| guest_time_arrival | datetime | Waktu kedatangan |
| guest_time_leave | datetime | Waktu keluar |
| guest_pic | string | Path gambar guest |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diupdate |

### Tabel: event_custom_fields
Menyimpan custom fields untuk event.

| Column | Type | Description |
|--------|------|-------------|
| id_field | bigint | Primary key, auto increment |
| id_event | bigint | Foreign key ke events |
| field_name | string | Nama field (untuk referensi) |
| field_label | string | Label untuk display |
| field_type | enum | Type: input, textarea, file, select, radio, checkbox |
| field_options | text | JSON untuk options (select/radio/checkbox) |
| is_required | boolean | Apakah field wajib diisi |
| field_order | integer | Urutan field |
| field_placeholder | string | Placeholder text |
| field_validation | text | JSON validation rules |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diupdate |

### Tabel: guest_custom_field_values
Menyimpan nilai custom fields untuk setiap guest.

| Column | Type | Description |
|--------|------|-------------|
| id_value | bigint | Primary key, auto increment |
| id_guest | bigint | Foreign key ke guests |
| id_field | bigint | Foreign key ke event_custom_fields |
| field_value | text | Nilai field |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diupdate |

### Tabel: event_field_orders
Menyimpan urutan field untuk display.

| Column | Type | Description |
|--------|------|-------------|
| id_order | bigint | Primary key, auto increment |
| id_event | bigint | Foreign key ke events |
| field_name | string | Nama field |
| field_order | integer | Urutan field |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diupdate |

### Tabel: certificate_templates
Menyimpan template sertifikat.

| Column | Type | Description |
|--------|------|-------------|
| id_template | bigint | Primary key, auto increment |
| template_name | string | Nama template |
| template_description | text | Deskripsi template |
| template_html | text | HTML template |
| template_css | text | CSS untuk template |
| is_active | boolean | Status aktif |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diupdate |

### Tabel: event_certificates
Menyimpan konfigurasi sertifikat untuk event.

| Column | Type | Description |
|--------|------|-------------|
| id_certificate | bigint | Primary key, auto increment |
| id_event | bigint | Foreign key ke events |
| id_template | bigint | Foreign key ke certificate_templates |
| introductory_phrase | string | Frasa pembuka |
| completion_phrase | string | Frasa penutup |
| organization_name | string | Nama organisasi |
| signatory_name | string | Nama penandatangan |
| signatory_title | string | Jabatan penandatangan |
| signature_image | string | Path gambar tanda tangan |
| verification_url_base | string | Base URL untuk verifikasi |
| certificate_id_prefix | string | Prefix untuk ID sertifikat |
| auto_generate_id | boolean | Auto generate ID |
| custom_fields | json | JSON untuk field tambahan |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diupdate |

### Tabel: attendees
Tabel untuk legacy support (optional).

---

## 📡 API Documentation

### Base URL
```
/api
```

### Authentication
Semua endpoint (kecuali login dan register) memerlukan token yang dikirim melalui query parameter:
```
?token=your_token_here
```

### Endpoints

#### Authentication

##### POST /api/login
Login user dan mendapatkan token.

**Request:**
```json
{
  "email": "user@example.com",
  "password": "password"
}
```

**Response:**
```json
{
  "email": "user@example.com",
  "token": "abc123..."
}
```

##### POST /api/register
Mendaftarkan user baru.

**Request:**
```json
{
  "firstname": "John",
  "lastname": "Doe",
  "email": "user@example.com",
  "password": "password",
  "password_confirmation": "password"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Registration successful",
  "user": {...}
}
```

#### User Management

##### GET /api/user/profile?token={token}
Mendapatkan profil user.

##### PUT /api/user/update-name?token={token}
Update nama user.

**Request:**
```json
{
  "firstname": "John",
  "lastname": "Doe"
}
```

##### PUT /api/user/update-password?token={token}
Update password user.

**Request:**
```json
{
  "current_password": "old_password",
  "password": "new_password",
  "password_confirmation": "new_password"
}
```

#### Event Management

##### GET /api/event?token={token}
Mendapatkan daftar semua event user.

**Response:**
```json
[
  {
    "id_event": 1,
    "name_event": "Event Name",
    "date_event": "2024-01-01",
    "guest_total": 100,
    ...
  }
]
```

##### POST /api/event?token={token}
Membuat event baru.

**Request:**
```json
{
  "name_event": "Event Name",
  "date_event": "2024-01-01",
  "location_event": "Location",
  "guest_total": 100
}
```

##### GET /api/event/{id}?token={token}
Mendapatkan detail event.

##### PUT /api/event/{id}?token={token}
Update event.

##### DELETE /api/event/{id}?token={token}
Menghapus event.

##### POST /api/event/{id}/logo?token={token}
Upload logo event.

**Request:** multipart/form-data dengan file `logo`

##### GET /api/event/{id}/field-orders?token={token}
Mendapatkan urutan field event.

##### PUT /api/event/{id}/field-orders?token={token}
Update urutan field event.

#### Guest Management

##### GET /api/guest?token={token}&eventId={id}&attend={true|false}
Mendapatkan daftar guest event.

**Query Parameters:**
- `eventId`: ID event (required)
- `attend`: Filter berdasarkan status hadir (optional)

**Response:**
```json
{
  "event": {...},
  "guests": [...]
}
```

##### GET /api/detail-guest?token={token}&guestId={id}
Mendapatkan detail guest.

##### POST /api/guest?token={token}
Membuat guest baru.

**Request:**
```json
{
  "id_event": 1,
  "guest_name": "Guest Name",
  "guest_email": "guest@example.com",
  ...
}
```

##### PUT /api/guest/{id}?token={token}
Update guest.

##### DELETE /api/guest/{id}?token={token}
Menghapus guest.

##### POST /api/update-guest?token={token}&eventId={id}&guestId={id}
Check-in guest.

**Response:**
```json
{
  "success": true,
  "message": "Guest checked in successfully",
  "notification": {...}
}
```

##### POST /api/update-guest-leave?token={token}&eventId={id}&guestId={id}
Check-out guest.

##### POST /api/update-profile-and-checkin?token={token}&eventId={id}&guestId={id}
Update profile dan check-in sekaligus.

**Request:**
```json
{
  "guestName": "Updated Name",
  "guestPhone": "081234567890",
  "guestEmail": "newemail@example.com"
}
```

#### QR Code

##### GET /api/qr-code?token={token}&eventId={id}&guestId={id}
Generate QR code (return base64).

**Response:**
```json
{
  "success": true,
  "qr_url": "https://...",
  "qr_code": "data:image/png;base64,..."
}
```

##### GET /api/download-qr?token={token}&eventId={id}&guestId={id}
Download QR code sebagai file PNG.

#### Session Management

##### GET /api/session?token={token}&eventId={id}
Mendapatkan daftar session.

##### POST /api/session?token={token}
Membuat session baru.

##### GET /api/session/{id}?token={token}
Mendapatkan detail session.

##### PUT /api/session/{id}?token={token}
Update session.

##### DELETE /api/session/{id}?token={token}
Menghapus session.

#### Import & Export

##### GET /api/download-template?token={token}&eventId={id}
Download template Excel untuk import.

##### POST /api/import-excel?token={token}&eventId={id}
Import guest dari Excel.

**Request:** multipart/form-data dengan file `excel_file`

**Optional Parameters:**
- `default_label`: Default label (0/1)
- `default_session`: Default session ID

**Response:**
```json
{
  "success": true,
  "message": "Import completed!",
  "data": {
    "koordinator_imported": 10,
    "peserta_imported": 50,
    "total_imported": 60,
    "skipped": 5,
    "errors": 0
  }
}
```

##### GET /api/download-all-qr?token={token}&eventId={id}
Download semua QR code sebagai ZIP.

##### POST /api/download-selected-qr?token={token}&eventId={id}
Download QR code terpilih sebagai ZIP.

**Request:**
```json
{
  "guestIds": [1, 2, 3, ...]
}
```

#### Custom Fields

##### GET /api/event/{eventId}/custom-fields?token={token}
Mendapatkan daftar custom fields event.

##### POST /api/event/{eventId}/custom-fields?token={token}
Membuat custom field baru.

**Request:**
```json
{
  "field_name": "custom_field_1",
  "field_label": "Custom Field Label",
  "field_type": "input",
  "is_required": false,
  "field_order": 1,
  "field_placeholder": "Enter value",
  "field_options": {...},  // untuk select/radio/checkbox
  "field_validation": {...}
}
```

##### PUT /api/event/{eventId}/custom-fields/{fieldId}?token={token}
Update custom field.

##### DELETE /api/event/{eventId}/custom-fields/{fieldId}?token={token}
Menghapus custom field.

##### PUT /api/event/{eventId}/custom-fields/reorder?token={token}
Mengubah urutan custom fields.

**Request:**
```json
{
  "fieldIds": [3, 1, 2]  // urutan baru
}
```

#### Certificate

##### GET /api/certificate/templates?token={token}
Mendapatkan daftar template sertifikat.

##### GET /api/event/{eventId}/certificate?token={token}
Mendapatkan konfigurasi sertifikat event.

##### POST /api/event/{eventId}/certificate?token={token}
Simpan konfigurasi sertifikat.

**Request:**
```json
{
  "id_template": 1,
  "introductory_phrase": "Sertifikat dengan bangga diberikan kepada",
  "completion_phrase": "telah menyelesaikan...",
  "organization_name": "Organization Name",
  "signatory_name": "Name",
  "signatory_title": "Title",
  "certificate_id_prefix": "SK-",
  "auto_generate_id": true,
  "custom_fields": {...}
}
```

##### POST /api/event/{eventId}/certificate/signature?token={token}
Upload gambar tanda tangan.

**Request:** multipart/form-data dengan file `signature`

##### DELETE /api/event/{eventId}/certificate?token={token}
Menghapus konfigurasi sertifikat.

#### Print & Preview

##### GET /api/print-invitation?token={token}&eventId={id}&guestId={id}
Cetak undangan (PDF).

##### GET /api/preview-event?token={token}&eventId={id}
Preview event data.

---

## 🎨 Frontend Components

### Main Components

1. **App.vue** - Root component dengan routing dan layout
2. **Navbar.vue** - Navigation bar dengan user menu
3. **Dashboard.vue** - Dashboard utama dengan daftar event
4. **Login.vue** - Halaman login
5. **Register.vue** - Halaman registrasi

### Event Components

6. **EventDetail.vue** - Detail event dengan semua fitur manajemen
   - Guest list dengan filter
   - QR code generation
   - Import/Export
   - Custom fields management
   - Certificate settings

### Guest Components

7. **GuestCheckIn.vue** - Halaman check-in untuk guest
8. **ScanQR.vue** - Scanner QR code untuk check-in

### Feature Components

9. **CustomFieldManager.vue** - Manager untuk custom fields
10. **CertificateSettings.vue** - Settings untuk sertifikat
11. **CertificatePreview.vue** - Preview sertifikat
12. **Doorprize.vue** - Doorprize dengan spinning wheel
13. **Preview.vue** - Preview event

### UI Components

14. **Modal.vue** - Reusable modal component
15. **Select2.vue** - Select2 wrapper component
16. **DatePicker.vue** - Date picker component
17. **SkeletonLoader.vue** - Loading skeleton
18. **ThemeSettings.vue** - Theme toggle (dark/light)
19. **ScrollToTop.vue** - Scroll to top button
20. **Guide.vue** - User guide/panduan
21. **SettingsTab.vue** - Settings tabs
22. **AdminPanel.vue** - Admin panel component
23. **DynamicFormField.vue** - Dynamic form field untuk custom fields
24. **SpinningWheel.vue** - Spinning wheel untuk doorprize

### Routing

Semua routes didefinisikan di `resources/js/router.js`:

- `/` - Redirect ke dashboard
- `/login` - Login page
- `/register` - Register page
- `/dashboard` - Dashboard (requires auth)
- `/event/:id` - Event detail (requires auth)
- `/scan-qr` - QR scanner
- `/guest-checkin` - Guest check-in page
- `/preview/:id` - Preview event
- `/settings` - Settings page (requires auth)
- `/guide` - User guide (requires auth)
- `/doorprize` - Doorprize page (requires auth)

---

## 🔄 Alur Kerja Aplikasi

### 1. Alur Registrasi dan Login

```
1. User membuka halaman Register
2. Mengisi form (firstname, lastname, email, password)
3. Sistem membuat akun dan generate token
4. User diarahkan ke Login
5. User login dengan email dan password
6. Sistem return token
7. Token disimpan di localStorage
8. User diarahkan ke Dashboard
```

### 2. Alur Membuat Event

```
1. User masuk ke Dashboard
2. Klik "Tambah Event" atau "Create Event"
3. Isi form event (nama, tanggal, lokasi, total guest)
4. Sistem membuat event baru
5. Event muncul di daftar Dashboard
6. User bisa klik event untuk masuk ke Event Detail
```

### 3. Alur Menambah Guest

**Manual:**
```
1. Di Event Detail, klik "Tambah Guest"
2. Isi form guest (nama, email, telepon, dll)
3. Sistem membuat guest baru
4. Guest muncul di daftar
5. QR code bisa langsung di-generate
```

**Import Excel:**
```
1. Di Event Detail, klik "Import Excel"
2. Download template terlebih dahulu (optional)
3. Isi template dengan data guest
4. Upload file Excel
5. Sistem import data:
   - Import koordinator dari kolom G
   - Import peserta dari kolom L (bisa multiple names)
6. Sistem skip duplicate entries
7. Return summary import
```

### 4. Alur Check-in

**Via QR Scanner (Admin):**
```
1. Admin membuka halaman Scan QR
2. Scan QR code guest
3. Sistem redirect ke Guest Check-in page
4. Tampilkan data guest
5. Admin klik "Check-in"
6. Sistem update status dan waktu kedatangan
7. Real-time notification via Pusher
8. Submit data ke 4Vision Media API (first-time check-in)
```

**Via Guest Check-in Page:**
```
1. Guest membuka link dari QR code
2. Sistem validasi token dan guest ID
3. Tampilkan form check-in
4. Guest bisa update profile (optional)
5. Guest klik "Check-in"
6. Sistem update status dan waktu kedatangan
7. Real-time notification via Pusher
8. Submit data ke 4Vision Media API (first-time check-in)
```

### 5. Alur Custom Fields

```
1. Di Event Detail, masuk ke tab "Custom Fields"
2. Klik "Tambah Field"
3. Pilih type field (input, select, radio, checkbox, dll)
4. Isi konfigurasi field:
   - Label, name, placeholder
   - Options (jika select/radio/checkbox)
   - Required atau tidak
   - Validation rules
5. Drag & drop untuk mengatur urutan
6. Field muncul di form guest
7. Data tersimpan di guest_custom_field_values
```

### 6. Alur Certificate

```
1. Di Event Detail, masuk ke tab "Certificate"
2. Pilih template sertifikat
3. Konfigurasi sertifikat:
   - Introductory phrase
   - Completion phrase
   - Organization info
   - Signatory info
   - Upload signature image
4. Preview sertifikat
5. Save konfigurasi
6. Sertifikat bisa di-generate untuk guest yang sudah check-in
```

### 7. Alur Doorprize

```
1. Buka halaman Doorprize
2. Pilih event yang berlangsung hari ini
3. Sistem load semua peserta event
4. Peserta ditampilkan di spinning wheel
5. Klik "Putar" untuk mengacak
6. Sistem memilih pemenang secara acak
7. Tampilkan pemenang
8. Bisa putar ulang untuk pemenang berikutnya
```

---

## 💻 Development Guide

### Code Standards

1. **PHP Coding Standards**
   - Mengikuti PSR-12
   - Type hints untuk semua parameters dan return types
   - Docblocks untuk semua public methods
   - Single Responsibility Principle

2. **JavaScript/Vue Standards**
   - Vue 3 Composition API
   - ES6+ syntax
   - Consistent naming: camelCase untuk variables/functions
   - Component-based architecture

3. **Database Standards**
   - Snake_case untuk column names
   - Prefix `id_` untuk primary keys
   - Foreign keys mengikuti pattern `id_table`
   - Timestamps untuk created_at dan updated_at

### Best Practices

1. **Repository Pattern**
   - Semua database queries di repository
   - Repository di-inject ke controllers dan services
   - Repository methods harus reusable

2. **Service Layer**
   - Business logic di service
   - Service bisa menggunakan multiple repositories
   - Service di-inject ke controllers

3. **Error Handling**
   - Try-catch untuk semua operations yang bisa fail
   - Log errors dengan proper context
   - Return meaningful error messages
   - HTTP status codes yang sesuai

4. **Security**
   - Token validation untuk semua protected endpoints
   - Input validation menggunakan Form Requests
   - SQL injection prevention (gunakan Eloquent/Query Builder)
   - XSS prevention (Vue auto-escape)
   - File upload validation

### Testing

```bash
# Run PHPUnit tests
php artisan test

# Run specific test
php artisan test --filter TestClassName
```

### Debugging

1. **Laravel Logs**
   - Logs tersimpan di `storage/logs/laravel.log`
   - Gunakan `Log::info()`, `Log::error()`, dll

2. **Browser DevTools**
   - Console untuk JavaScript errors
   - Network tab untuk API requests
   - Vue DevTools extension untuk debugging Vue

### Common Commands

```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Create migration
php artisan make:migration create_table_name

# Create model
php artisan make:model ModelName

# Create controller
php artisan make:controller Api/ControllerName

# Create service
# (Manual - tidak ada artisan command)

# Create repository
# (Manual - tidak ada artisan command)
```

---

## 📊 Status Pengembangan

### ✅ Fitur yang Sudah Selesai

1. ✅ Authentication & Authorization
2. ✅ User Management
3. ✅ Event Management (CRUD)
4. ✅ Guest Management (CRUD)
5. ✅ QR Code Generation & Download
6. ✅ QR Code Scanner
7. ✅ Check-in & Check-out
8. ✅ Profile Update & Check-in
9. ✅ Session Management
10. ✅ Excel Import & Export
11. ✅ Custom Fields (Full CRUD)
12. ✅ Certificate System (Templates, Configuration, Signature)
13. ✅ Print Invitation
14. ✅ Preview System
15. ✅ Doorprize System
16. ✅ Real-time Notifications (Pusher)
17. ✅ 4Vision Media API Integration
18. ✅ Theme Settings (Dark/Light Mode)
19. ✅ User Guide
20. ✅ Responsive Design

### 🔄 Fitur yang Mungkin Perlu Dikembangkan Lebih Lanjut

1. ⏳ Certificate Generation untuk Guest (PDF)
2. ⏳ Batch Certificate Download
3. ⏳ Advanced Analytics & Reports
4. ⏳ Email Notifications
5. ⏳ SMS Notifications
6. ⏳ Multi-language Support
7. ⏳ Role-based Access Control (Admin, Staff, dll)
8. ⏳ Event Templates
9. ⏳ Guest Photo Upload
10. ⏳ Attendance Reports (Export PDF/Excel)

### 📝 Catatan Penting

1. **Password Authentication**: Menggunakan MD5 untuk legacy support. Disarankan migrasi ke bcrypt di production.

2. **Token Security**: Token saat ini disimpan di query parameter. Untuk production, pertimbangkan menggunakan:
   - Bearer token di Authorization header
   - Refresh token mechanism
   - Token expiration

3. **File Storage**: 
   - Guest images: `/storage/app/public/img/guest/`
   - Event logos: `/storage/app/public/events/{eventId}/logo/`
   - Signatures: `/storage/app/public/events/{eventId}/signatures/`
   - Pastikan `storage:link` sudah dijalankan

4. **HTTPS**: Untuk akses kamera (QR scanner) di production, aplikasi harus dijalankan melalui HTTPS.

5. **Environment Variables**: Pastikan semua konfigurasi penting ada di `.env`:
   - Database credentials
   - Pusher credentials
   - 4Vision Media API credentials (jika ada)
   - APP_URL

6. **Performance**: 
   - Untuk event dengan banyak guest (>1000), pertimbangkan pagination
   - QR code generation dalam bulk mungkin memakan waktu

---

## 📚 Referensi

- [Laravel Documentation](https://laravel.com/docs)
- [Vue 3 Documentation](https://vuejs.org/)
- [Vue Router Documentation](https://router.vuejs.org/)
- [SimpleSoftwareIO QR Code](https://www.simplesoftware.io/#/docs/simple-qrcode)
- [Pusher Documentation](https://pusher.com/docs)

---

## 📄 Lisensi

MIT License

---

## 👥 Kontributor

Dokumentasi ini dibuat untuk membantu pengembangan dan penggunaan aplikasi QR Code Event Invitation System.

**Versi Dokumentasi**: 1.0  
**Terakhir Diupdate**: Desember 2024

---

## 🆘 Troubleshooting

### Problem: Migration Error

**Solution:**
```bash
php artisan migrate:fresh
# atau
php artisan migrate:rollback
php artisan migrate
```

### Problem: Vite Build Error

**Solution:**
```bash
rm -rf node_modules package-lock.json
npm install
npm run dev
```

### Problem: Storage Link Error

**Solution:**
```bash
php artisan storage:link
# Pastikan symlink dibuat: public/storage -> storage/app/public
```

### Problem: QR Code Tidak Muncul

**Solution:**
- Pastikan token valid
- Check eventId dan guestId
- Check browser console untuk errors
- Pastikan API endpoint accessible

### Problem: Pusher Tidak Berfungsi

**Solution:**
- Check Pusher credentials di `.env`
- Pastikan Pusher service berjalan
- Check browser console untuk errors
- Pastikan channel dan event names benar

---

**Selamat Menggunakan Aplikasi! 🎉**
