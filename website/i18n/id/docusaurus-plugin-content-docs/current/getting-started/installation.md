---
sidebar_position: 1
---

# Installation

1. Instal [skijasi](https://skijasi-docs.uatech.co.id/getting-started/installation) dulu. Setelah itu, Anda dapat menginstal modul dengan perintah berikut.

```bash
composer require skijasi/commerce-module
```

2. Jalankan perintah berikut untuk mengatur modul dengan mudah.

```bash
php artisan migrate
php artisan skijasi-commerce:setup
composer dump-autoload
php artisan db:seed --class=SkijasiCommerceModuleSeeder
```

3. Tambahkan plugin ke `MIX_SKIJASI_PLUGINS` Anda ke `.env`. Jika Anda memiliki plugin lain yang diinstal, sertakan mereka menggunakan koma pembatas (,).

```
MIX_SKIJASI_PLUGINS=commerce-module
```

4. Tambahkan menu plugin ke `MIX_SKIJASI_MENU` Anda ke `.env`. Jika Anda memiliki menu lain, sertakan menu tersebut menggunakan koma pembatas (,).

```
MIX_SKIJASI_MENU=admin,commerce-module
```

5. Isi variabel lain dalam file .env.
    - `COMMERCE_PRODUCTS_LIMIT_QUERY=10` Limit query browse on product, default is 10.
    - `MIX_PAYMENT_MODULE=commerce-module` Register the payment module.

6. Isi konfigurasi pembayaran di `skijasi-commerce.php`. Sebagai contoh:
    - `'payments' => ['NadzorServera\Skijasi\Module\Commerce\SkijasiCommerceModule']`

7. Instal [commerce theme](https://github.com/nadzorservera-croatia/skijasi-commerce-theme)