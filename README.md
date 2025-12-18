# QR Code Event Invitation System

Sistem manajemen event dengan QR Code invitation yang dibangun dengan Laravel 12 dan Vue 3.

## Fitur

- ✅ Authentication dengan token-based system
- ✅ Manajemen Event (Create, Read, Update, Delete)
- ✅ Manajemen Guest/Participant
- ✅ QR Code generation untuk invitation
- ✅ QR Code scanning untuk check-in
- ✅ Real-time notifications via Pusher
- ✅ Integrasi dengan 4Vision Media API
- ✅ Profile update dan check-in sekaligus
- ✅ Admin panel untuk kelola event dan guest
- ✅ Responsive design dengan Vue 3

## Teknologi

- **Backend**: Laravel 12
- **Frontend**: Vue 3
- **Database**: MySQL
- **QR Code**: SimpleSoftwareIO/simple-qrcode & html5-qrcode
- **Real-time**: Pusher
- **Build Tool**: Vite
- **PHP Version**: 8.2+
- **Node Version**: 20+

## Architecture

Project ini menggunakan **Clean Architecture** dengan pattern:

- **Repository Pattern**: Data access layer
- **Service Layer**: Business logic layer
- **Controller**: Thin controllers, hanya handle HTTP requests
- **Request Validation**: Form Request classes
- **Dependency Injection**: Untuk loose coupling

## Instalasi

### 1. Prerequisites

- PHP 8.2+
- Composer
- Node.js 20+
- MySQL
- Herd (untuk local development)

### 2. Clone atau download project

### 3. Install dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 4. Setup environment

```bash
# Copy file .env.example ke .env (jika ada)
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Konfigurasi database

Edit file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=qrinvite_database
DB_USERNAME=root
DB_PASSWORD=admin123
```

### 6. Konfigurasi Pusher (optional)

Tambahkan ke `.env`:

```env
PUSHER_APP_KEY=443fb7eb8273230a07de
PUSHER_APP_SECRET=85b45764ff857c901bf5
PUSHER_APP_ID=1101241
PUSHER_APP_CLUSTER=mt1
```

### 7. Jalankan migration

```bash
php artisan migrate
```

### 8. Setup Herd (jika menggunakan Herd)

File `.herd.yaml` sudah dikonfigurasi:
- PHP: 8.2
- Node: 20
- Webroot: public

Jalankan:
```bash
herd link qr-generate
```

### 9. Jalankan development server

```bash
# Terminal 1: Laravel server (jika tidak menggunakan Herd)
php artisan serve

# Terminal 2: Vite dev server
npm run dev
```

### 10. Akses aplikasi

- Dengan Herd: `http://qr-generate.test` (atau sesuai konfigurasi Herd)
- Tanpa Herd: `http://localhost:8000`

## API Endpoints

### Authentication
- `POST /api/login` - Login user
  - Body: `{ "email": "user@example.com", "password": "password" }`
  - Response: `{ "email": "...", "token": "..." }`

### Events
- `GET /api/event?token={token}` - Get list of events for user
  - Response: Array of events with id, name, date, total

### Guests
- `GET /api/guest?token={token}&eventId={id}&attend={true|false}` - Get list of guests
  - Response: Event info with guests array
- `GET /api/detail-guest?token={token}&guestId={id}` - Get guest detail
  - Response: Guest detail with image, address, attend status, arrival time
- `POST /api/update-guest?token={token}&eventId={id}&guestId={id}` - Check-in guest
  - Body: `{ "token": "...", "eventId": 1 }`
  - Response: `{ "success": true, "message": "...", "notification": {...} }`
- `POST /api/update-profile-and-checkin?token={token}&eventId={id}&guestId={id}` - Update profile and check-in
  - Body: `{ "guestName": "...", "guestPhone": "...", "guestEmail": "..." }`
  - Response: `{ "success": true, "message": "...", "notification": {...} }`

## Struktur Database

### Tabel: users
- `id_user` - Primary key
- `email` - Email (unique)
- `password` - Password (MD5 hash)
- `firstname` - First name
- `lastname` - Last name
- `token` - Authentication token

### Tabel: events
- `id_event` - Primary key
- `id_user` - Foreign key to users
- `name_event` - Event name
- `date_event` - Event date
- `location_event` - Event location
- `guest_total` - Total guests
- `event_default_guest_pic` - Default guest picture path

### Tabel: sessions
- `id_session` - Primary key
- `id_event` - Foreign key to events
- `name_session` - Session name
- `time_started_session` - Start time
- `time_ended_session` - End time

### Tabel: guests
- `id_guest` - Primary key
- `id_event` - Foreign key to events
- `id_session` - Foreign key to sessions (nullable)
- `guest_title` - Guest title
- `guest_name` - Guest name
- `guest_address` - Address
- `guest_province` - Province
- `guest_city` - City
- `guest_institution_name` - Institution name
- `guest_email` - Email
- `guest_phone` - Phone
- `guest_label` - Label
- `guest_status` - Check-in status (boolean)
- `guest_time_arrival` - Arrival time
- `guest_time_leave` - Leave time
- `guest_pic` - Picture filename

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/          # API Controllers
│   ├── Requests/         # Form Request Validation
│   └── Resources/        # API Resources
├── Models/               # Eloquent Models
├── Repositories/         # Data Access Layer
├── Services/             # Business Logic Layer
└── Providers/           # Service Providers

database/
├── migrations/           # Database Migrations
├── seeders/             # Database Seeders
└── factories/           # Model Factories

routes/
├── api.php              # API Routes
├── web.php              # Web Routes
└── console.php          # Console Routes
```

## Clean Code Principles

1. **Separation of Concerns**: Repository untuk data access, Service untuk business logic
2. **Single Responsibility**: Setiap class memiliki satu tanggung jawab
3. **Dependency Injection**: Services dan Repositories di-inject ke Controllers
4. **Request Validation**: Form Request classes untuk validation
5. **Type Hints**: Menggunakan type hints untuk better IDE support
6. **Error Handling**: Proper error handling dengan try-catch dan logging

## Features Detail

### QR Code Check-in
- Guest dapat check-in dengan scan QR code
- First-time check-in akan update `guest_time_arrival`
- Real-time notification via Pusher ke admin

### Profile Update & Check-in
- Guest dapat update profile (name, phone, email) dan check-in sekaligus
- Validasi nama tidak boleh kosong
- Real-time notification via Pusher

### 4Vision Media API Integration
- Otomatis submit guest data ke 4Vision Media API saat first-time check-in
- Error handling dengan logging

### Pusher Real-time Notifications
- Real-time notifications untuk admin saat guest check-in
- Channel: user token, Event: event ID

## Migration dari Project Lama

Lihat file `MIGRATION_GUIDE.md` untuk detail migrasi dari project lama.

## Dokumentasi Lengkap

Untuk dokumentasi lengkap dan detail, silakan lihat file **[DOKUMENTASI_LENGKAP.md](./DOKUMENTASI_LENGKAP.md)** yang mencakup:

- Pengenalan dan overview lengkap
- Arsitektur sistem secara detail
- Semua fitur yang tersedia
- Panduan instalasi lengkap
- Struktur database lengkap
- API documentation lengkap
- Frontend components documentation
- Alur kerja aplikasi
- Development guide
- Status pengembangan
- Troubleshooting

## Catatan

- Password authentication menggunakan MD5 (legacy support)
- Guest images di-upload ke `/storage/app/public/img/guest/`
- Event default guest picture: `/event/avatar.jpg`
- Event logos: `/storage/app/public/events/{eventId}/logo/`
- Signatures: `/storage/app/public/events/{eventId}/signatures/`
- Pastikan `php artisan storage:link` sudah dijalankan
- Pastikan aplikasi dijalankan melalui HTTPS untuk akses kamera di production
- QR Code berisi URL untuk check-in dengan parameter guestId, eventId, dan token

## Lisensi

MIT License
