# admin : adminSdit@payment.com/admin@admin.com admin1234
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).


## deployment 
1. sediakan hosting
2. sediakan domain
3. set git pada hosting. set up ssh pada hosting
4. pull repo ke direktori sejajar dengan public_html
5. set up .env , kosong kan key: .. 
6. set up database pada hosting
7. masukan atribut DB pada env
8. composer update, key generate, migrate, db:seed
9. pindahkan semua yang ada dalam public ke public_html
10. karna direktori sejajar dengan html mala modifikasi index.php dengan /sditPayment/ sebelum nama folder dalam direktori.
11. coba refresh link pada browser.

## membuat akun filament
1. php artisan make:filament-user
2. php artisan make:filament-user --email=admin@bungacempaka.sch.id --password=admin1234

## QRcide: composer require simplesoftwareio/simple-qrcode 
## 16062026
- generate tagihan :
-- jenis spp. pilihan jenis sekolah. pilih kelas.bulan.tahun.nominal.kasih tanda jika bulan ini sudah generate tagihan spp. simpan log pembuatan tagihan. jangan sampai ada tagihan double.
-- jenis daftar ulang. muncul 4 jenis sekolah dengan form nominal . pilih tahun ajaran. simpan log pembuatan tagihan. validasi jika akan dibuat lagi dalam 12 bulan terakhir.
-- hilangkan pilihan daftar masuk.
- pembayaran siswa buat breadcumb.
- potongan dan nominal bayar menggunakan event yang bikin berat karena saling membaca setiap perubahan angka. jangan pakai event itu.
- pembayaran den struk trf berhasil , hanya saja bukti bayar nya diklik 403.
- buat kompresi gambar dengan library bawaan laravel.
- 