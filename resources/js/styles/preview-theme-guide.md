# Preview Theme Customization Guide

## Cara Mengubah Theme Preview

Untuk mengubah warna dan tema pada halaman Preview, edit CSS variables di file `preview-new.css` pada bagian `:root`.

## CSS Variables yang Tersedia

### Warna Utama
```css
--preview-primary-color: #FF6900;        /* Warna utama (orange) */
--preview-primary-hover: #E55A00;       /* Warna hover */
--preview-text-primary: #002a36;        /* Warna teks utama (dark blue) */
--preview-text-secondary: #4b5563;      /* Warna teks sekunder (gray) */
```

### Box Count (Angka Tamu Undangan, Hadir, dll)
```css
--box-count-number-color: #FF6900;      /* Warna angka di box statistics */
--box-count-bg-color: #ffffff;          /* Background box statistics */
```

### Box Important (Tamu Ditempat)
```css
--box-important-bg-color: #FF6900;      /* Background box "Tamu Ditempat" */
--box-important-text-color: #ffffff;    /* Warna teks di box "Tamu Ditempat" */
--box-important-pulse-color: rgba(255, 105, 0, 0.7);  /* Warna animasi pulse */
```

### Avatar/Image Filter (Warna Gambar Peserta)
```css
--avatar-filter-brightness: 0.95;      /* Kecerahan (0-2, default: 0.95) */
--avatar-filter-contrast: 1.1;        /* Kontras (0-2, default: 1.1) */
--avatar-filter-saturate: 1.8;        /* Saturasi warna (0-2, default: 1.8) */
--avatar-filter-hue-rotate: 150deg;   /* Rotasi hue/warna (0-360deg, default: 150deg) */
--avatar-default-color: #3b82f6;       /* Warna default jika tidak ada gambar (blue) */
```

## Contoh Custom Theme

### Theme Biru
```css
:root {
    --preview-primary-color: #3b82f6;
    --preview-primary-hover: #2563eb;
    --box-count-number-color: #3b82f6;
    --box-important-bg-color: #3b82f6;
    --box-important-pulse-color: rgba(59, 130, 246, 0.7);
    --avatar-filter-hue-rotate: 200deg;
    --avatar-default-color: #3b82f6;
}
```

### Theme Hijau
```css
:root {
    --preview-primary-color: #10b981;
    --preview-primary-hover: #059669;
    --box-count-number-color: #10b981;
    --box-important-bg-color: #10b981;
    --box-important-pulse-color: rgba(16, 185, 129, 0.7);
    --avatar-filter-hue-rotate: 120deg;
    --avatar-default-color: #10b981;
}
```

### Theme Ungu
```css
:root {
    --preview-primary-color: #8b5cf6;
    --preview-primary-hover: #7c3aed;
    --box-count-number-color: #8b5cf6;
    --box-important-bg-color: #8b5cf6;
    --box-important-pulse-color: rgba(139, 92, 246, 0.7);
    --avatar-filter-hue-rotate: 270deg;
    --avatar-default-color: #8b5cf6;
}
```

## Catatan

- Semua perubahan warna akan langsung terlihat setelah CSS di-refresh
- Animasi pulse pada box "Tamu Ditempat" akan mengikuti warna `--box-important-pulse-color`
- Filter avatar hanya berlaku untuk gambar dengan nama file yang mengandung "avatar.png" atau "avatar.jpg"
- Untuk mengubah warna avatar lebih dramatis, ubah nilai `--avatar-filter-hue-rotate` (0-360 derajat)
