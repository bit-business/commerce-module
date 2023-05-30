---
sidebar_position: 1
---

# Installation

1. Install [skijasi](https://skijasi-docs.nadzorserveraweb.hr/getting-started/installation) first. After that, you can install the module with the following command.

    ```bash
    composer require skijasi/commerce-module
    ```

1. Run the following command to easily setup the module.

    ```bash
    php artisan skijasi-commerce:setup
    php artisan migrate
    composer dump-autoload
    php artisan db:seed --class="Database\Seeders\Skijasi\Commerce\SkijasiCommerceModuleSeeder"
    ```

1. Add the plugins to your `MIX_SKIJASI_PLUGINS` to `.env`. If you have another plugins installed, include them using delimiter comma (,).

    ```
    MIX_SKIJASI_PLUGINS=commerce-module
    ```

1. Add the plugins menu to your `MIX_SKIJASI_MENU` to `.env`. If you have another menu, include them using delimiter comma (,).

    ```
    MIX_SKIJASI_MENU=${MIX_DEFAULT_MENU},commerce-module
    ```

1. Fill the other variables in .env file.
    - `COMMERCE_PRODUCTS_LIMIT_QUERY=10` Limit query browse on product, default is 10.
    - `MIX_PAYMENT_MODULE=commerce-module` Register the payment module.

1. Fill the payment config in `skijasi-commerce.php`. For example:
    - `'payments' => ['NadzorServera\Skijasi\Module\Commerce\SkijasiCommerceModule']`

1. Your commerce module already installed and you can see the menu at the dashboard.

1. Next you can install the theme for the frontpage, you can see the available theme [here](https://github.com/nadzorservera-croatia/skijasi-awesome#themes)
