# Checklist Deploy Trafie-library

## 1. Sebelum upload
- [ ] Ganti password admin lewat menu Akun Saya (jangan pakai admin12345 di server).
- [ ] Pastikan folder `_backup`, `node_modules`, dan file `.env` lokal TIDAK ikut diupload.
- [ ] Jalankan `npm run build` lalu pastikan folder `public/build` ada.

## 2. Pengaturan .env di server
```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://alamat-domain-kamu
DB_CONNECTION=mysql
DB_HOST=...
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...
SESSION_SECURE_COOKIE=true
LIBRARY_FINE_PER_DAY=1000
```
APP_DEBUG wajib false supaya pesan error internal tidak tampil ke pengunjung.

## 3. Perintah di server
```
composer install --no-dev --optimize-autoloader
php artisan key:generate          # hanya sekali, kalau APP_KEY masih kosong
php artisan migrate --force
php artisan db:seed --force       # hanya sekali: membuat akun admin dan kategori awal
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
Catatan: `db:seed` membuat admin@trafie.com dengan password awal admin12345. Segera ganti.

## 4. Server web
- [ ] Document root diarahkan ke folder `public`, bukan folder proyek.
- [ ] Folder `storage` dan `bootstrap/cache` bisa ditulis oleh server web.
- [ ] HTTPS aktif.

## 5. Setelah online
- [ ] Login admin, ganti password, uji tambah buku dengan cover.
- [ ] Daftar sebagai siswa, ajukan pinjam, setujui dari admin, kembalikan.
- [ ] Buka alamat yang tidak ada untuk memastikan halaman 404 bertema tampil.
- [ ] Jadwalkan backup database rutin (mingguan minimal).

## 6. Update berikutnya
```
php artisan down
git pull   # atau upload file yang berubah
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan up
```