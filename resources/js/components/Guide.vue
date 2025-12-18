<template>
    <div class="guide-page">
        <div class="guide-header">
            <h1>
                <i class="ri-book-open-line"></i>
                Panduan Penggunaan MeetPassQR
            </h1>
            <p class="guide-subtitle">Pelajari cara menggunakan semua fitur aplikasi dengan mudah dan cepat</p>
        </div>

        <!-- Table of Contents -->
        <div class="table-of-contents">
            <h2><i class="ri-list-check"></i> Daftar Isi</h2>
            <div class="toc-grid">
                <button 
                    v-for="section in sections" 
                    :key="section.id"
                    @click="scrollToSection(section.id)"
                    class="toc-item"
                    :class="{ active: activeSection === section.id }"
                >
                    <i :class="section.icon"></i>
                    <span>{{ section.title }}</span>
                </button>
            </div>
        </div>

        <!-- Guide Sections -->
        <div class="guide-content">
            <!-- Section 1: Login & Register -->
            <section :id="sections[0].id" class="guide-section">
                <div class="section-header">
                    <div class="section-number">01</div>
                    <div class="section-title-content">
                        <h2><i :class="sections[0].icon"></i> {{ sections[0].title }}</h2>
                        <p>Langkah-langkah untuk membuat akun baru dan masuk ke aplikasi</p>
                    </div>
                </div>

                <div class="guide-steps">
                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">1</span>
                            <h3>Membuat Akun Baru (Register)</h3>
                        </div>
                        <div class="step-content">
                            <ol class="step-list">
                                <li>Buka halaman Register pada aplikasi</li>
                                <li>Isi formulir dengan data berikut:
                                    <ul>
                                        <li><strong>Nama:</strong> Masukkan nama lengkap Anda</li>
                                        <li><strong>Email:</strong> Masukkan alamat email yang valid</li>
                                        <li><strong>Password:</strong> Buat password yang kuat (minimal 8 karakter)</li>
                                        <li><strong>Konfirmasi Password:</strong> Ketik ulang password yang sama</li>
                                    </ul>
                                </li>
                                <li>Klik tombol <strong>"Daftar"</strong> untuk membuat akun</li>
                                <li>Setelah berhasil, Anda akan diarahkan ke halaman login</li>
                            </ol>
                            <div class="step-image-placeholder">
                                <i class="ri-image-line"></i>
                                <p>Gambar: Halaman Register</p>
                                <small>Tambahkan screenshot halaman register di sini</small>
                            </div>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">2</span>
                            <h3>Login ke Aplikasi</h3>
                        </div>
                        <div class="step-content">
                            <ol class="step-list">
                                <li>Buka halaman Login</li>
                                <li>Masukkan email dan password yang sudah didaftarkan</li>
                                <li>Klik tombol <strong>"Masuk"</strong></li>
                                <li>Setelah login berhasil, Anda akan diarahkan ke Dashboard</li>
                            </ol>
                            <div class="step-image-placeholder">
                                <i class="ri-image-line"></i>
                                <p>Gambar: Halaman Login</p>
                                <small>Tambahkan screenshot halaman login di sini</small>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section 2: Membuat Event -->
            <section :id="sections[1].id" class="guide-section">
                <div class="section-header">
                    <div class="section-number">02</div>
                    <div class="section-title-content">
                        <h2><i :class="sections[1].icon"></i> {{ sections[1].title }}</h2>
                        <p>Panduan lengkap membuat dan mengelola event dengan QR Code</p>
                    </div>
                </div>

                <div class="guide-steps">
                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">1</span>
                            <h3>Membuat Event Baru</h3>
                        </div>
                        <div class="step-content">
                            <ol class="step-list">
                                <li>Di halaman Dashboard, klik tombol <strong>"+ Buat Event Baru"</strong></li>
                                <li>Modal form akan muncul, isi informasi event:
                                    <ul>
                                        <li><strong>Nama Event:</strong> Nama acara yang akan dibuat</li>
                                        <li><strong>Tanggal Event:</strong> Pilih tanggal menggunakan date picker</li>
                                        <li><strong>Lokasi:</strong> Masukkan lokasi acara</li>
                                        <li><strong>Total Tamu:</strong> Jumlah tamu yang diundang (minimal 10)</li>
                                    </ul>
                                </li>
                                <li>Klik tombol <strong>"Buat Event"</strong> untuk menyimpan</li>
                                <li>Event baru akan muncul di daftar event di Dashboard</li>
                            </ol>
                            <div class="step-image-placeholder">
                                <i class="ri-image-line"></i>
                                <p>Gambar: Modal Buat Event</p>
                                <small>Tambahkan screenshot modal create event di sini</small>
                            </div>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">2</span>
                            <h3>Mengelola Event</h3>
                        </div>
                        <div class="step-content">
                            <ol class="step-list">
                                <li>Klik pada card event di Dashboard untuk membuka detail event</li>
                                <li>Di halaman detail event, Anda dapat:
                                    <ul>
                                        <li><strong>Melihat informasi event</strong> (nama, tanggal, lokasi, jumlah tamu)</li>
                                        <li><strong>Menambahkan tamu</strong> ke dalam event</li>
                                        <li><strong>Mengelola sesi</strong> event (jika event memiliki beberapa sesi)</li>
                                        <li><strong>Mengelola custom field</strong> untuk data tamu tambahan</li>
                                        <li><strong>Mencetak QR Code</strong> untuk undangan</li>
                                        <li><strong>Export data</strong> tamu dalam format Excel/CSV</li>
                                    </ul>
                                </li>
                            </ol>
                            <div class="step-image-placeholder">
                                <i class="ri-image-line"></i>
                                <p>Gambar: Halaman Detail Event</p>
                                <small>Tambahkan screenshot halaman detail event di sini</small>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section 3: Menambahkan Tamu -->
            <section :id="sections[2].id" class="guide-section">
                <div class="section-header">
                    <div class="section-number">03</div>
                    <div class="section-title-content">
                        <h2><i :class="sections[2].icon"></i> {{ sections[2].title }}</h2>
                        <p>Cara menambahkan dan mengelola daftar tamu dalam event</p>
                    </div>
                </div>

                <div class="guide-steps">
                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">1</span>
                            <h3>Menambahkan Tamu Manual</h3>
                        </div>
                        <div class="step-content">
                            <ol class="step-list">
                                <li>Buka detail event yang ingin ditambahkan tamunya</li>
                                <li>Klik tab <strong>"Daftar Tamu"</strong></li>
                                <li>Klik tombol <strong>"+ Tambah Tamu"</strong></li>
                                <li>Isi formulir data tamu:
                                    <ul>
                                        <li><strong>Telepon:</strong> Nomor telepon tamu (wajib, unik)</li>
                                        <li><strong>Sesi:</strong> Pilih sesi event (jika ada)</li>
                                        <li><strong>Label VIP:</strong> Pilih 1 untuk VIP, 0 untuk tamu biasa</li>
                                        <li><strong>Custom Fields:</strong> Isi field tambahan yang sudah dibuat (jika ada)</li>
                                    </ul>
                                </li>
                                <li>Klik tombol <strong>"Tambah"</strong> untuk menyimpan</li>
                            </ol>
                            <div class="step-image-placeholder">
                                <i class="ri-image-line"></i>
                                <p>Gambar: Modal Tambah Tamu</p>
                                <small>Tambahkan screenshot modal tambah tamu di sini</small>
                            </div>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">2</span>
                            <h3>Import Tamu dari Excel/CSV</h3>
                        </div>
                        <div class="step-content">
                            <ol class="step-list">
                                <li>Di halaman detail event, klik tombol <strong>"Import Tamu"</strong></li>
                                <li>Siapkan file Excel (.xlsx) atau CSV dengan format kolom:
                                    <ul>
                                        <li><strong>guest_phone</strong> (wajib)</li>
                                        <li><strong>guest_name</strong> (opsional)</li>
                                        <li><strong>guest_email</strong> (opsional)</li>
                                        <li>Kolom lainnya sesuai kebutuhan</li>
                                    </ul>
                                </li>
                                <li>Pilih file yang akan diimport</li>
                                <li>Klik <strong>"Import"</strong> untuk memproses data</li>
                                <li>Sistem akan memvalidasi dan menambahkan tamu yang valid</li>
                            </ol>
                            <div class="step-image-placeholder">
                                <i class="ri-image-line"></i>
                                <p>Gambar: Proses Import Tamu</p>
                                <small>Tambahkan screenshot proses import di sini</small>
                            </div>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">3</span>
                            <h3>Mengelola Tamu</h3>
                        </div>
                        <div class="step-content">
                            <ol class="step-list">
                                <li>Untuk <strong>mengedit</strong> data tamu, klik ikon edit pada baris tamu</li>
                                <li>Untuk <strong>menghapus</strong> tamu, klik ikon delete dan konfirmasi</li>
                                <li>Gunakan <strong>search/filter</strong> untuk mencari tamu tertentu</li>
                                <li>Gunakan <strong>pagination</strong> untuk navigasi jika tamu banyak</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section 4: Manajemen Sesi -->
            <section :id="sections[3].id" class="guide-section">
                <div class="section-header">
                    <div class="section-number">04</div>
                    <div class="section-title-content">
                        <h2><i :class="sections[3].icon"></i> {{ sections[3].title }}</h2>
                        <p>Membuat dan mengelola sesi event untuk membagi peserta ke dalam kelompok</p>
                    </div>
                </div>

                <div class="guide-steps">
                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">1</span>
                            <h3>Membuat Sesi Event</h3>
                        </div>
                        <div class="step-content">
                            <ol class="step-list">
                                <li>Di halaman detail event, klik tab <strong>"Sesi"</strong></li>
                                <li>Klik tombol <strong>"+ Tambah Sesi"</strong></li>
                                <li>Isi informasi sesi:
                                    <ul>
                                        <li><strong>Nama Sesi:</strong> Nama sesi (contoh: "Sesi 1 - Pagi", "Workshop A")</li>
                                        <li><strong>Waktu Mulai:</strong> Jam mulai sesi</li>
                                        <li><strong>Waktu Selesai:</strong> Jam selesai sesi</li>
                                        <li><strong>Deskripsi:</strong> Keterangan tambahan (opsional)</li>
                                    </ul>
                                </li>
                                <li>Klik <strong>"Simpan"</strong> untuk membuat sesi</li>
                                <li>Sesi akan muncul di daftar dan dapat dipilih saat menambah tamu</li>
                            </ol>
                            <div class="step-image-placeholder">
                                <i class="ri-image-line"></i>
                                <p>Gambar: Form Tambah Sesi</p>
                                <small>Tambahkan screenshot form tambah sesi di sini</small>
                            </div>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">2</span>
                            <h3>Mengelola Sesi</h3>
                        </div>
                        <div class="step-content">
                            <ol class="step-list">
                                <li>Untuk <strong>mengedit</strong> sesi, klik ikon edit pada baris sesi</li>
                                <li>Untuk <strong>menghapus</strong> sesi, klik ikon delete dan konfirmasi</li>
                                <li><strong>Catatan:</strong> Sesi yang sudah memiliki tamu tidak dapat dihapus</li>
                                <li>Anda dapat melihat jumlah tamu per sesi di daftar</li>
                            </ol>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">3</span>
                            <h3>Menetapkan Tamu ke Sesi</h3>
                        </div>
                        <div class="step-content">
                            <ol class="step-list">
                                <li>Saat menambah tamu baru, pilih <strong>"Sesi"</strong> dari dropdown</li>
                                <li>Atau saat mengedit tamu, ubah sesi yang ditetapkan</li>
                                <li>Tamu dapat dipindahkan antar sesi kapan saja</li>
                                <li>Filter tamu berdasarkan sesi untuk melihat daftar per sesi</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section 5: Custom Fields -->
            <section :id="sections[4].id" class="guide-section">
                <div class="section-header">
                    <div class="section-number">05</div>
                    <div class="section-title-content">
                        <h2><i :class="sections[4].icon"></i> {{ sections[4].title }}</h2>
                        <p>Menambahkan field tambahan untuk mengumpulkan data tamu lebih lengkap</p>
                    </div>
                </div>

                <div class="guide-steps">
                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">1</span>
                            <h3>Membuat Custom Field</h3>
                        </div>
                        <div class="step-content">
                            <ol class="step-list">
                                <li>Di halaman detail event, klik tab <strong>"Custom Fields"</strong></li>
                                <li>Klik tombol <strong>"+ Tambah Field"</strong></li>
                                <li>Isi informasi field:
                                    <ul>
                                        <li><strong>Label Field:</strong> Nama field yang akan ditampilkan</li>
                                        <li><strong>Tipe Field:</strong> Pilih tipe (Input, Textarea, Select, Radio, Checkbox, File)</li>
                                        <li><strong>Field Wajib:</strong> Centang jika field harus diisi</li>
                                        <li><strong>Placeholder:</strong> Teks petunjuk (opsional)</li>
                                        <li><strong>Options:</strong> Untuk Select/Radio/Checkbox, tambahkan pilihan</li>
                                    </ul>
                                </li>
                                <li>Klik <strong>"Simpan"</strong> untuk membuat field</li>
                            </ol>
                            <div class="step-image-placeholder">
                                <i class="ri-image-line"></i>
                                <p>Gambar: Custom Field Manager</p>
                                <small>Tambahkan screenshot custom field manager di sini</small>
                            </div>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">2</span>
                            <h3>Menggunakan Custom Field</h3>
                        </div>
                        <div class="step-content">
                            <ol class="step-list">
                                <li>Custom field akan otomatis muncul saat menambah/edit tamu</li>
                                <li>Field wajib akan ditandai dengan asterisk (*)</li>
                                <li>Data custom field akan tersimpan dan dapat dilihat di daftar tamu</li>
                                <li>Custom field juga dapat di-export bersama data tamu lainnya</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section 6: QR Code & Preview -->
            <section :id="sections[5].id" class="guide-section">
                <div class="section-header">
                    <div class="section-number">06</div>
                    <div class="section-title-content">
                        <h2><i :class="sections[5].icon"></i> {{ sections[5].title }}</h2>
                        <p>Mencetak QR Code dan memahami bagaimana sistem preview bekerja</p>
                    </div>
                </div>

                <div class="guide-steps">
                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">1</span>
                            <h3>Mencetak QR Code</h3>
                        </div>
                        <div class="step-content">
                            <ol class="step-list">
                                <li>Di halaman detail event, klik tombol <strong>"Cetak QR"</strong></li>
                                <li>Pilih opsi cetak:
                                    <ul>
                                        <li><strong>Cetak Semua QR:</strong> Mencetak QR Code untuk semua tamu</li>
                                        <li><strong>Cetak Per Sesi:</strong> Mencetak QR Code berdasarkan sesi</li>
                                        <li><strong>Cetak Per Tamu:</strong> Mencetak QR Code individual</li>
                                    </ul>
                                </li>
                                <li>Klik <strong>"Cetak"</strong> untuk membuka preview cetakan</li>
                                <li>Di halaman preview, gunakan tombol print browser untuk mencetak</li>
                            </ol>
                            <div class="step-image-placeholder">
                                <i class="ri-image-line"></i>
                                <p>Gambar: Halaman Preview QR Code</p>
                                <small>Tambahkan screenshot halaman preview di sini</small>
                            </div>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">2</span>
                            <h3>Memahami QR Code</h3>
                        </div>
                        <div class="step-content">
                            <ul class="step-list">
                                <li>Setiap tamu memiliki <strong>QR Code unik</strong> yang berisi link undangan</li>
                                <li>QR Code berisi informasi:
                                    <ul>
                                        <li>Link unik untuk check-in tamu</li>
                                        <li>Data event dan informasi tamu</li>
                                    </ul>
                                </li>
                                <li>Tamu dapat <strong>scan QR Code</strong> untuk membuka halaman check-in</li>
                                <li>QR Code dapat dicetak dan dibagikan kepada tamu sebelum event</li>
                            </ul>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">3</span>
                            <h3>Halaman Preview Print</h3>
                        </div>
                        <div class="step-content">
                            <ol class="step-list">
                                <li>Halaman preview menampilkan QR Code dalam format siap cetak</li>
                                <li>Setiap halaman berisi QR Code dengan informasi tamu</li>
                                <li>Layout dioptimalkan untuk pencetakan A4</li>
                                <li>Gunakan <strong>Print Preview</strong> browser sebelum mencetak</li>
                            </ol>
                            <div class="step-image-placeholder">
                                <i class="ri-image-line"></i>
                                <p>Gambar: Preview Print QR Code</p>
                                <small>Tambahkan screenshot preview print di sini</small>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section 7: Halaman Preview Monitoring -->
            <section :id="sections[6].id" class="guide-section">
                <div class="section-header">
                    <div class="section-number">07</div>
                    <div class="section-title-content">
                        <h2><i :class="sections[6].icon"></i> {{ sections[6].title }}</h2>
                        <p>Monitor kehadiran tamu secara real-time dengan statistik lengkap</p>
                    </div>
                </div>

                <div class="guide-steps">
                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">1</span>
                            <h3>Akses Halaman Preview</h3>
                        </div>
                        <div class="step-content">
                            <ol class="step-list">
                                <li>Di halaman detail event, klik tombol <strong>"Preview"</strong> atau buka link <strong>/preview/{id_event}</strong></li>
                                <li>Halaman preview akan menampilkan monitoring real-time event</li>
                                <li>Halaman ini dapat dibuka di perangkat terpisah (laptop, tablet, TV) untuk dipajang selama event</li>
                                <li>Tidak memerlukan login untuk melihat halaman preview</li>
                            </ol>
                            <div class="step-image-placeholder">
                                <i class="ri-image-line"></i>
                                <p>Gambar: Halaman Preview Monitoring</p>
                                <small>Tambahkan screenshot halaman preview monitoring di sini</small>
                            </div>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">2</span>
                            <h3>Statistik Kehadiran</h3>
                        </div>
                        <div class="step-content">
                            <p>Halaman preview menampilkan 5 kartu statistik real-time:</p>
                            <ul class="step-list">
                                <li><strong>Tamu Undangan:</strong> Total jumlah tamu yang diundang dalam event</li>
                                <li><strong>Tamu Hadir:</strong> Jumlah tamu yang sudah melakukan check-in</li>
                                <li><strong>Tamu Pulang:</strong> Jumlah tamu yang sudah melakukan check-out</li>
                                <li><strong>Tamu Ditempat:</strong> Jumlah tamu yang sedang berada di lokasi event (sudah check-in tapi belum check-out)</li>
                                <li><strong>Tamu Belum Hadir:</strong> Jumlah tamu yang belum melakukan check-in</li>
                            </ul>
                            <p><strong>Catatan:</strong> Statistik akan terupdate otomatis secara real-time menggunakan teknologi Pusher ketika ada tamu yang check-in atau check-out.</p>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">3</span>
                            <h3>Daftar Peserta Terbaru</h3>
                        </div>
                        <div class="step-content">
                            <ol class="step-list">
                                <li>Di bawah statistik, terdapat section <strong>"Peserta Terbaru"</strong></li>
                                <li>Menampilkan daftar tamu yang baru saja melakukan check-in</li>
                                <li>Setiap tamu ditampilkan dengan:
                                    <ul>
                                        <li>Foto profil atau avatar default</li>
                                        <li>Nama tamu</li>
                                        <li>Gelar/jabatan (jika ada)</li>
                                    </ul>
                                </li>
                                <li>Daftar akan terupdate otomatis ketika ada tamu baru yang check-in</li>
                            </ol>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">4</span>
                            <h3>Notifikasi Check-in Real-time</h3>
                        </div>
                        <div class="step-content">
                            <ol class="step-list">
                                <li>Ketika tamu melakukan check-in melalui scan QR, akan muncul <strong>overlay welcome</strong></li>
                                <li>Overlay menampilkan:
                                    <ul>
                                        <li>Pesan <strong>"Selamat Datang"</strong> atau <strong>"Terima kasih atas kehadirannya"</strong> (untuk check-out)</li>
                                        <li>Nama tamu yang baru check-in</li>
                                        <li>Status check-in berhasil dengan icon checkmark</li>
                                        <li>Label "Peserta Terbaru"</li>
                                    </ul>
                                </li>
                                <li>Overlay akan hilang otomatis setelah 10 detik</li>
                                <li>Data statistik dan daftar peserta akan langsung terupdate</li>
                            </ol>
                            <div class="step-image-placeholder">
                                <i class="ri-image-line"></i>
                                <p>Gambar: Welcome Overlay saat Check-in</p>
                                <small>Tambahkan screenshot welcome overlay di sini</small>
                            </div>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">5</span>
                            <h3>Cara Menggunakan Preview untuk Monitoring</h3>
                        </div>
                        <div class="step-content">
                            <ol class="step-list">
                                <li><strong>Persiapan Event:</strong>
                                    <ul>
                                        <li>Siapkan perangkat display (laptop, tablet, atau TV yang terhubung ke komputer)</li>
                                        <li>Buka halaman preview di perangkat tersebut</li>
                                        <li>Letakkan perangkat di lokasi yang mudah terlihat oleh peserta event</li>
                                    </ul>
                                </li>
                                <li><strong>Selama Event:</strong>
                                    <ul>
                                        <li>Halaman akan otomatis menampilkan update real-time</li>
                                        <li>Peserta dapat melihat siapa saja yang sudah hadir</li>
                                        <li>Anda dapat memantau statistik kehadiran dari mana saja</li>
                                    </ul>
                                </li>
                                <li><strong>Keuntungan:</strong>
                                    <ul>
                                        <li>Monitor kehadiran secara real-time tanpa perlu refresh halaman</li>
                                        <li>Memberikan transparansi kepada peserta tentang siapa saja yang hadir</li>
                                        <li>Membantu mengidentifikasi peserta yang belum hadir</li>
                                        <li>Fitur welcome overlay menambahkan pengalaman yang menarik</li>
                                    </ul>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section 8: Scan QR & Check-in -->
            <section :id="sections[7].id" class="guide-section">
                <div class="section-header">
                    <div class="section-number">08</div>
                    <div class="section-title-content">
                        <h2><i :class="sections[7].icon"></i> {{ sections[7].title }}</h2>
                        <p>Bagaimana sistem scan QR dan proses check-in tamu bekerja</p>
                    </div>
                </div>

                <div class="guide-steps">
                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">1</span>
                            <h3>Halaman Scan QR</h3>
                        </div>
                        <div class="step-content">
                            <ol class="step-list">
                                <li>Akses menu <strong>"Scan QR"</strong> di navbar</li>
                                <li>Halaman akan meminta izin untuk mengakses kamera</li>
                                <li>Izinkan akses kamera untuk memulai scanning</li>
                                <li>Arahkan kamera ke QR Code tamu</li>
                                <li>Sistem akan otomatis membaca QR Code yang terdeteksi</li>
                            </ol>
                            <div class="step-image-placeholder">
                                <i class="ri-image-line"></i>
                                <p>Gambar: Halaman Scan QR</p>
                                <small>Tambahkan screenshot halaman scan QR di sini</small>
                            </div>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">2</span>
                            <h3>Proses Check-in Tamu</h3>
                        </div>
                        <div class="step-content">
                            <ol class="step-list">
                                <li>Setelah QR Code ter-scan, sistem akan memvalidasi:
                                    <ul>
                                        <li>Apakah QR Code valid dan terdaftar</li>
                                        <li>Status tamu (sudah check-in atau belum)</li>
                                        <li>Informasi event dan sesi</li>
                                    </ul>
                                </li>
                                <li>Jika valid dan belum check-in:
                                    <ul>
                                        <li>Tampilkan informasi tamu</li>
                                        <li>Konfirmasi check-in</li>
                                        <li>Update status tamu menjadi "Hadir"</li>
                                        <li>Tampilkan pesan sukses</li>
                                    </ul>
                                </li>
                                <li>Jika sudah check-in, akan muncul notifikasi</li>
                            </ol>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">3</span>
                            <h3>Check-in Mandiri oleh Tamu</h3>
                        </div>
                        <div class="step-content">
                            <ol class="step-list">
                                <li>Tamu dapat check-in sendiri melalui link di QR Code</li>
                                <li>Setelah scan QR Code, tamu akan dibawa ke halaman check-in</li>
                                <li>Halaman menampilkan informasi event</li>
                                <li>Tamu mengisi data tambahan (jika ada custom field)</li>
                                <li>Tamu klik tombol <strong>"Check-in"</strong></li>
                                <li>Sistem akan memvalidasi dan menyimpan data check-in</li>
                                <li>Tampilkan konfirmasi sukses dengan detail event</li>
                            </ol>
                            <div class="step-image-placeholder">
                                <i class="ri-image-line"></i>
                                <p>Gambar: Halaman Guest Check-in</p>
                                <small>Tambahkan screenshot halaman check-in tamu di sini</small>
                            </div>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">4</span>
                            <h3>Melacak Status Check-in</h3>
                        </div>
                        <div class="step-content">
                            <ul class="step-list">
                                <li>Di halaman detail event, Anda dapat melihat:
                                    <ul>
                                        <li><strong>Status tamu:</strong> Hadir/Belum hadir</li>
                                        <li><strong>Waktu check-in:</strong> Kapan tamu melakukan check-in</li>
                                        <li><strong>Filter:</strong> Filter tamu berdasarkan status</li>
                                    </ul>
                                </li>
                                <li>Data check-in real-time akan terupdate otomatis</li>
                                <li>Anda dapat export data check-in untuk laporan</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section 9: Check-out Tamu -->
            <section :id="sections[8].id" class="guide-section">
                <div class="section-header">
                    <div class="section-number">09</div>
                    <div class="section-title-content">
                        <h2><i :class="sections[8].icon"></i> {{ sections[8].title }}</h2>
                        <p>Proses check-out tamu yang sudah selesai mengikuti event</p>
                    </div>
                </div>

                <div class="guide-steps">
                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">1</span>
                            <h3>Check-out melalui Scan QR</h3>
                        </div>
                        <div class="step-content">
                            <ol class="step-list">
                                <li>Buka halaman <strong>"Scan QR"</strong> di navbar</li>
                                <li>Scan QR Code tamu yang ingin di-check-out</li>
                                <li>Sistem akan memvalidasi:
                                    <ul>
                                        <li>Apakah tamu sudah check-in</li>
                                        <li>Status tamu saat ini</li>
                                    </ul>
                                </li>
                                <li>Jika tamu sudah check-in, sistem akan menampilkan opsi check-out</li>
                                <li>Klik <strong>"Check-out"</strong> untuk menyelesaikan proses</li>
                                <li>Waktu check-out akan tercatat otomatis</li>
                            </ol>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">2</span>
                            <h3>Check-out Manual</h3>
                        </div>
                        <div class="step-content">
                            <ol class="step-list">
                                <li>Di halaman detail event, buka tab <strong>"Daftar Tamu"</strong></li>
                                <li>Cari tamu yang ingin di-check-out</li>
                                <li>Klik ikon check-out pada baris tamu</li>
                                <li>Konfirmasi check-out</li>
                                <li>Status tamu akan berubah menjadi "Pulang"</li>
                            </ol>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">3</span>
                            <h3>Notifikasi Check-out</h3>
                        </div>
                        <div class="step-content">
                            <ul class="step-list">
                                <li>Setelah check-out, akan muncul notifikasi real-time di halaman Preview</li>
                                <li>Pesan <strong>"Terima kasih atas kehadirannya"</strong> akan ditampilkan</li>
                                <li>Statistik di Preview akan terupdate:
                                    <ul>
                                        <li><strong>Tamu Pulang</strong> akan bertambah</li>
                                        <li><strong>Tamu Ditempat</strong> akan berkurang</li>
                                    </ul>
                                </li>
                                <li>Overlay akan hilang otomatis setelah 5 detik</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section 10: Sertifikat -->
            <section :id="sections[9].id" class="guide-section">
                <div class="section-header">
                    <div class="section-number">10</div>
                    <div class="section-title-content">
                        <h2><i :class="sections[9].icon"></i> {{ sections[9].title }}</h2>
                        <p>Mengatur dan membuat sertifikat untuk peserta event</p>
                    </div>
                </div>

                <div class="guide-steps">
                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">1</span>
                            <h3>Mengatur Sertifikat untuk Event</h3>
                        </div>
                        <div class="step-content">
                            <ol class="step-list">
                                <li>Di halaman detail event, klik tab <strong>"Sertifikat"</strong></li>
                                <li>Pilih <strong>Template Sertifikat</strong> dari dropdown</li>
                                <li>Isi konten sertifikat:
                                    <ul>
                                        <li><strong>Frasa Pembuka:</strong> Teks sebelum nama peserta (contoh: "Sertifikat dengan bangga diberikan kepada")</li>
                                        <li><strong>Frasa Penutup:</strong> Teks setelah nama peserta (contoh: "telah mengikuti event")</li>
                                        <li><strong>Nama Organisasi:</strong> Nama organisasi/penyelenggara</li>
                                        <li><strong>Nama Penandatangan:</strong> Nama yang akan muncul di sertifikat</li>
                                        <li><strong>Jabatan Penandatangan:</strong> Jabatan penandatangan</li>
                                        <li><strong>Upload Tanda Tangan:</strong> Upload gambar tanda tangan (opsional)</li>
                                        <li><strong>URL Verifikasi:</strong> Link untuk verifikasi sertifikat (opsional)</li>
                                    </ul>
                                </li>
                                <li>Klik <strong>"Simpan Pengaturan"</strong> untuk menyimpan</li>
                            </ol>
                            <div class="step-image-placeholder">
                                <i class="ri-image-line"></i>
                                <p>Gambar: Halaman Certificate Settings</p>
                                <small>Tambahkan screenshot certificate settings di sini</small>
                            </div>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">2</span>
                            <h3>Membuat Template Sertifikat Custom</h3>
                        </div>
                        <div class="step-content">
                            <ol class="step-list">
                                <li>Akses menu <strong>"Custom Template Editor"</strong> dari navbar atau route <code>/certificate/templates</code></li>
                                <li>Klik <strong>"-- Buat Template Baru --"</strong> atau pilih template yang sudah ada</li>
                                <li>Isi informasi template:
                                    <ul>
                                        <li><strong>Nama Template:</strong> Nama untuk template ini</li>
                                        <li><strong>Deskripsi:</strong> Keterangan template (opsional)</li>
                                    </ul>
                                </li>
                                <li>Di editor <strong>HTML Structure</strong>, buat struktur HTML sertifikat:
                                    <ul>
                                        <li>Gunakan placeholder seperti <code>{{recipient_name}}</code>, <code>{{course_title}}</code>, <code>{{certificate_id}}</code></li>
                                        <li>Placeholder lainnya: <code>{{introductory_phrase}}</code>, <code>{{completion_phrase}}</code>, <code>{{organization_name}}</code>, <code>{{signatory_name}}</code>, <code>{{signature_image}}</code></li>
                                    </ul>
                                </li>
                                <li>Di editor <strong>CSS Styles</strong>, tambahkan styling untuk template</li>
                                <li>Gunakan <strong>Preview</strong> untuk melihat hasil real-time</li>
                                <li>Klik <strong>"Simpan Template"</strong> untuk menyimpan</li>
                            </ol>
                            <div class="step-image-placeholder">
                                <i class="ri-image-line"></i>
                                <p>Gambar: Custom Template Editor</p>
                                <small>Tambahkan screenshot custom template editor di sini</small>
                            </div>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">3</span>
                            <h3>Mengunduh Sertifikat</h3>
                        </div>
                        <div class="step-content">
                            <ol class="step-list">
                                <li>Di halaman Certificate Settings, scroll ke bagian <strong>"Peserta yang Sudah Check-in"</strong></li>
                                <li>Pilih peserta yang akan dibuatkan sertifikat (centang checkbox)</li>
                                <li>Klik tombol <strong>"Download Sertifikat"</strong></li>
                                <li>Sistem akan membuat sertifikat untuk peserta terpilih</li>
                                <li>File PDF sertifikat akan terunduh otomatis</li>
                                <li>Setiap sertifikat memiliki ID unik untuk verifikasi</li>
                            </ol>
                            <div class="step-image-placeholder">
                                <i class="ri-image-line"></i>
                                <p>Gambar: Download Sertifikat</p>
                                <small>Tambahkan screenshot proses download sertifikat di sini</small>
                            </div>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">4</span>
                            <h3>Preview Sertifikat</h3>
                        </div>
                        <div class="step-content">
                            <ul class="step-list">
                                <li>Di Custom Template Editor, panel <strong>Preview</strong> menampilkan hasil real-time</li>
                                <li>Preview akan terupdate otomatis saat Anda mengubah HTML atau CSS</li>
                                <li>Di Certificate Settings, klik <strong>"Preview"</strong> untuk melihat contoh sertifikat</li>
                                <li>Preview menggunakan data sample untuk demonstrasi</li>
                                <li>Pastikan semua placeholder ter-render dengan benar sebelum menyimpan</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section 11: Doorprize -->
            <section :id="sections[10].id" class="guide-section">
                <div class="section-header">
                    <div class="section-number">11</div>
                    <div class="section-title-content">
                        <h2><i :class="sections[10].icon"></i> {{ sections[10].title }}</h2>
                        <p>Menggunakan spinning wheel untuk mengundi pemenang doorprize</p>
                    </div>
                </div>

                <div class="guide-steps">
                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">1</span>
                            <h3>Akses Halaman Doorprize</h3>
                        </div>
                        <div class="step-content">
                            <ol class="step-list">
                                <li>Klik menu <strong>"Doorprize"</strong> di navbar</li>
                                <li>Halaman doorprize akan menampilkan form pemilihan event</li>
                                <li>Pilih event yang berlangsung hari ini dari dropdown</li>
                                <li>Sistem akan otomatis memuat peserta dari event yang dipilih</li>
                            </ol>
                            <div class="step-image-placeholder">
                                <i class="ri-image-line"></i>
                                <p>Gambar: Halaman Doorprize</p>
                                <small>Tambahkan screenshot halaman doorprize di sini</small>
                            </div>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">2</span>
                            <h3>Memutar Spinning Wheel</h3>
                        </div>
                        <div class="step-content">
                            <ol class="step-list">
                                <li>Setelah event dipilih, spinning wheel akan menampilkan semua peserta</li>
                                <li>Setiap peserta akan muncul sebagai segment di wheel dengan warna berbeda</li>
                                <li>Nama peserta ditampilkan di outer edge wheel, mengikuti kurva segment</li>
                                <li>Untuk memutar wheel:
                                    <ul>
                                        <li>Klik pada wheel, atau</li>
                                        <li>Tekan <strong>Ctrl + Enter</strong> (Windows) atau <strong>Cmd + Enter</strong> (Mac)</li>
                                    </ul>
                                </li>
                                <li>Wheel akan berputar beberapa kali sebelum berhenti</li>
                                <li>Panah biru di kanan wheel menunjukkan pemenang</li>
                            </ol>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">3</span>
                            <h3>Mengelola Pemenang</h3>
                        </div>
                        <div class="step-content">
                            <ol class="step-list">
                                <li>Setelah wheel berhenti, pemenang akan ditampilkan di section <strong>"Hasil Undian"</strong></li>
                                <li>Pemenang otomatis dihapus dari wheel agar tidak terpilih lagi</li>
                                <li>Anda dapat memutar wheel lagi untuk memilih pemenang berikutnya</li>
                                <li>Semua pemenang akan tercatat dengan:
                                    <ul>
                                        <li>Urutan pemenang (1, 2, 3, dst)</li>
                                        <li>Nama pemenang</li>
                                        <li>Email dan nomor telepon (jika ada)</li>
                                        <li>Waktu undian</li>
                                    </ul>
                                </li>
                            </ol>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">4</span>
                            <h3>Download Hasil Undian</h3>
                        </div>
                        <div class="step-content">
                            <ol class="step-list">
                                <li>Setelah selesai mengundi, klik tombol <strong>"Download Hasil"</strong></li>
                                <li>File Excel akan terunduh dengan data:
                                    <ul>
                                        <li>Urutan pemenang</li>
                                        <li>Nama, email, telepon</li>
                                        <li>Waktu undian</li>
                                    </ul>
                                </li>
                                <li>File dapat digunakan untuk dokumentasi dan laporan</li>
                            </ol>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">5</span>
                            <h3>Tips Menggunakan Doorprize</h3>
                        </div>
                        <div class="step-content">
                            <ul class="step-list">
                                <li><strong>Jumlah Peserta:</strong>
                                    <ul>
                                        <li>Wheel dapat menampilkan hingga 50+ peserta</li>
                                        <li>Untuk peserta banyak (>50), nama mungkin tidak terlihat jelas</li>
                                        <li>Disarankan membagi peserta menjadi beberapa kelompok</li>
                                    </ul>
                                </li>
                                <li><strong>Ukuran Wheel:</strong>
                                    <ul>
                                        <li>Wheel otomatis menyesuaikan ukuran berdasarkan jumlah peserta</li>
                                        <li>Font size dan posisi label menyesuaikan untuk readability</li>
                                    </ul>
                                </li>
                                <li><strong>Real-time Update:</strong>
                                    <ul>
                                        <li>Peserta yang sudah terpilih otomatis dihapus dari wheel</li>
                                        <li>Wheel akan ter-update secara real-time</li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section 12: Export & Laporan -->
            <section :id="sections[11].id" class="guide-section">
                <div class="section-header">
                    <div class="section-number">12</div>
                    <div class="section-title-content">
                        <h2><i :class="sections[11].icon"></i> {{ sections[11].title }}</h2>
                        <p>Cara export data tamu dan membuat laporan event</p>
                    </div>
                </div>

                <div class="guide-steps">
                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">1</span>
                            <h3>Export Data Tamu</h3>
                        </div>
                        <div class="step-content">
                            <ol class="step-list">
                                <li>Di halaman detail event, klik tombol <strong>"Export"</strong></li>
                                <li>Pilih format export:
                                    <ul>
                                        <li><strong>Excel (.xlsx):</strong> Untuk Microsoft Excel</li>
                                        <li><strong>CSV:</strong> Untuk spreadsheet umum</li>
                                    </ul>
                                </li>
                                <li>Data yang diexport mencakup:
                                    <ul>
                                        <li>Data tamu (nama, email, telepon, dll)</li>
                                        <li>Custom field data</li>
                                        <li>Status check-in</li>
                                        <li>Waktu check-in</li>
                                    </ul>
                                </li>
                                <li>File akan otomatis terunduh</li>
                            </ol>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">2</span>
                            <h3>Statistik Event</h3>
                        </div>
                        <div class="step-content">
                            <ul class="step-list">
                                <li>Di dashboard dan detail event, Anda dapat melihat:
                                    <ul>
                                        <li><strong>Total tamu</strong> yang diundang</li>
                                        <li><strong>Jumlah tamu yang hadir</strong></li>
                                        <li><strong>Persentase kehadiran</strong></li>
                                        <li><strong>Breakdown per sesi</strong> (jika ada)</li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section 13: Pengaturan -->
            <section :id="sections[12].id" class="guide-section">
                <div class="section-header">
                    <div class="section-number">13</div>
                    <div class="section-title-content">
                        <h2><i :class="sections[12].icon"></i> {{ sections[12].title }}</h2>
                        <p>Mengatur profil akun dan kustomisasi tema aplikasi</p>
                    </div>
                </div>

                <div class="guide-steps">
                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">1</span>
                            <h3>Mengatur Profil</h3>
                        </div>
                        <div class="step-content">
                            <ol class="step-list">
                                <li>Klik menu <strong>"Pengaturan"</strong> di navbar</li>
                                <li>Di tab <strong>"Profil"</strong>, Anda dapat:
                                    <ul>
                                        <li>Mengubah nama pengguna</li>
                                        <li>Mengubah password</li>
                                    </ul>
                                </li>
                                <li>Isi form dan klik <strong>"Simpan"</strong></li>
                            </ol>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">2</span>
                            <h3>Kustomisasi Tema Aplikasi</h3>
                        </div>
                        <div class="step-content">
                            <ol class="step-list">
                                <li>Klik ikon <strong>palet warna</strong> di pojok kanan bawah</li>
                                <li>Panel theme settings akan terbuka</li>
                                <li>Anda dapat:
                                    <ul>
                                        <li>Memilih <strong>preset theme</strong> (Default, Dark, Ocean, Forest, Sunset, Purple)</li>
                                        <li><strong>Mengubah warna primary</strong> sesuai keinginan</li>
                                        <li><strong>Mengubah background</strong> dan warna text</li>
                                        <li><strong>Mengubah border color</strong></li>
                                    </ul>
                                </li>
                                <li>Klik <strong>"Save Theme"</strong> untuk menyimpan</li>
                                <li>Klik <strong>"Reset to Default"</strong> untuk kembali ke tema default</li>
                                <li>Tema akan tersimpan dan diterapkan di semua halaman aplikasi</li>
                            </ol>
                            <div class="step-image-placeholder">
                                <i class="ri-image-line"></i>
                                <p>Gambar: Theme Settings Panel</p>
                                <small>Tambahkan screenshot theme settings di sini</small>
                            </div>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-header">
                            <span class="step-number">3</span>
                            <h3>Kustomisasi Tema Preview</h3>
                        </div>
                        <div class="step-content">
                            <ol class="step-list">
                                <li>Tema Preview dapat dikustomisasi melalui Theme Settings</li>
                                <li>Warna yang dapat diubah:
                                    <ul>
                                        <li><strong>Primary Color:</strong> Mempengaruhi warna angka statistik, box "Tamu Ditempat", dan nama peserta</li>
                                        <li><strong>Avatar Color:</strong> Warna avatar/gambar peserta akan mengikuti primary color</li>
                                        <li><strong>Text Colors:</strong> Warna teks untuk header dan label</li>
                                    </ul>
                                </li>
                                <li>Semua perubahan warna akan langsung terlihat di halaman Preview</li>
                                <li>Animasi dan efek visual akan menyesuaikan dengan primary color</li>
                            </ol>
                            <div class="step-image-placeholder">
                                <i class="ri-image-line"></i>
                                <p>Gambar: Preview dengan Custom Theme</p>
                                <small>Tambahkan screenshot preview dengan custom theme di sini</small>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Guide',
    data() {
        return {
            activeSection: null,
            sections: [
                { id: 'login-register', title: 'Login & Register', icon: 'ri-login-box-line' },
                { id: 'membuat-event', title: 'Membuat Event', icon: 'ri-calendar-event-line' },
                { id: 'menambahkan-tamu', title: 'Menambahkan Tamu', icon: 'ri-user-add-line' },
                { id: 'sesi-management', title: 'Manajemen Sesi', icon: 'ri-time-line' },
                { id: 'custom-fields', title: 'Custom Fields', icon: 'ri-file-list-3-line' },
                { id: 'qr-preview', title: 'QR Code & Preview', icon: 'ri-qr-code-line' },
                { id: 'preview-monitoring', title: 'Preview Monitoring', icon: 'ri-dashboard-line' },
                { id: 'scan-checkin', title: 'Scan QR & Check-in', icon: 'ri-scan-line' },
                { id: 'checkout', title: 'Check-out Tamu', icon: 'ri-logout-box-line' },
                { id: 'sertifikat', title: 'Sertifikat', icon: 'ri-award-line' },
                { id: 'doorprize', title: 'Doorprize', icon: 'ri-gift-line' },
                { id: 'export-laporan', title: 'Export & Laporan', icon: 'ri-file-download-line' },
                { id: 'pengaturan', title: 'Pengaturan', icon: 'ri-settings-3-line' }
            ]
        };
    },
    mounted() {
        // Intersection Observer for active section highlighting
        this.setupIntersectionObserver();
    },
    methods: {
        scrollToSection(sectionId) {
            const element = document.getElementById(sectionId);
            if (element) {
                const offset = 100; // Offset untuk navbar
                const elementPosition = element.getBoundingClientRect().top + window.pageYOffset;
                const offsetPosition = elementPosition - offset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        },
        setupIntersectionObserver() {
            const options = {
                root: null,
                rootMargin: '-100px 0px -50% 0px',
                threshold: 0
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        this.activeSection = entry.target.id;
                    }
                });
            }, options);

            // Observe all guide sections
            this.$nextTick(() => {
                this.sections.forEach(section => {
                    const element = document.getElementById(section.id);
                    if (element) {
                        observer.observe(element);
                    }
                });
            });
        }
    }
};
</script>

<style scoped>
@import '../styles/guide.css';
</style>
