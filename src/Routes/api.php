<?php

use Illuminate\Support\Facades\Route;
use NadzorServera\Skijasi\Middleware\ApiRequest;
use NadzorServera\Skijasi\Middleware\SkijasiAuthenticate;
use NadzorServera\Skijasi\Middleware\SkijasiCheckPermissions;
use NadzorServera\Skijasi\Module\Commerce\Helper\Route as HelperRoute;

$api_route_prefix = \config('skijasi.api_route_prefix');

Route::group(['prefix' => $api_route_prefix, 'as' => 'skijasi.', 'middleware' => [ApiRequest::class]], function () {
    Route::group(['prefix' => 'module/commerce/v1'], function () {
        Route::group(['prefix' => 'product'], function () {
            Route::get('/', HelperRoute::getController('ProductController@browse'))->middleware(SkijasiCheckPermissions::class.':browse_products');
            Route::get('/bin', HelperRoute::getController('ProductController@browseBin'))->middleware(SkijasiCheckPermissions::class.':browse_products_bin');
            Route::get('/read', HelperRoute::getController('ProductController@read'))->middleware(SkijasiCheckPermissions::class.':read_products');
            Route::get('/read-slug', HelperRoute::getController('ProductController@readBySlug'))->middleware(SkijasiCheckPermissions::class.':read_products');
            Route::post('/restore', HelperRoute::getController('ProductController@restore'))->middleware(SkijasiCheckPermissions::class.':restore_products');
            Route::post('/restore-multiple', HelperRoute::getController('ProductController@restoreMultiple'))->middleware(SkijasiCheckPermissions::class.':restore_products');
            Route::post('/add', HelperRoute::getController('ProductController@add'))->middleware(SkijasiCheckPermissions::class.':add_products');
            Route::put('/edit', HelperRoute::getController('ProductController@edit'))->middleware(SkijasiCheckPermissions::class.':edit_products');
            Route::delete('/delete', HelperRoute::getController('ProductController@delete'))->middleware(SkijasiCheckPermissions::class.':delete_products');
            Route::delete('/delete-multiple', HelperRoute::getController('ProductController@deleteMultiple'))->middleware(SkijasiCheckPermissions::class.':delete_products');
            Route::delete('/force-delete', HelperRoute::getController('ProductController@forceDelete'))->middleware(SkijasiCheckPermissions::class.':delete_permanent_products');
            Route::delete('/force-delete-multiple', HelperRoute::getController('ProductController@forceDeleteMultiple'))->middleware(SkijasiCheckPermissions::class.':delete_permanent_products');
        });

        Route::group(['prefix' => 'product-detail'], function () {
            Route::post('/add', HelperRoute::getController('ProductDetailController@add'))->middleware(SkijasiCheckPermissions::class.':add_product_details');
            Route::put('/edit', HelperRoute::getController('ProductDetailController@edit'))->middleware(SkijasiCheckPermissions::class.':edit_product_details');
            Route::delete('/delete', HelperRoute::getController('ProductDetailController@delete'))->middleware(SkijasiCheckPermissions::class.':delete_product_details');

            Route::get('/browse', HelperRoute::getController('ProductDetailController@browse'))->middleware(SkijasiCheckPermissions::class.':browse_product_details');
        });

        Route::group(['prefix' => 'product-category'], function () {
            Route::get('/', HelperRoute::getController('ProductCategoryController@browse'))->middleware(SkijasiCheckPermissions::class.':browse_product_categories');
            Route::get('/bin', HelperRoute::getController('ProductCategoryController@browseBin'))->middleware(SkijasiCheckPermissions::class.':browse_product_categories_bin');
            Route::get('/read', HelperRoute::getController('ProductCategoryController@read'))->middleware(SkijasiCheckPermissions::class.':read_product_categories');
            Route::get('/read-slug', HelperRoute::getController('ProductCategoryController@readBySlug'))->middleware(SkijasiCheckPermissions::class.':read_product_categories');
            Route::post('/restore', HelperRoute::getController('ProductCategoryController@restore'))->middleware(SkijasiCheckPermissions::class.':restore_product_categories');
            Route::post('/restore-multiple', HelperRoute::getController('ProductCategoryController@restoreMultiple'))->middleware(SkijasiCheckPermissions::class.':restore_product_categories');
            Route::post('/add', HelperRoute::getController('ProductCategoryController@add'))->middleware(SkijasiCheckPermissions::class.':add_product_categories');
            Route::put('/edit', HelperRoute::getController('ProductCategoryController@edit'))->middleware(SkijasiCheckPermissions::class.':edit_product_categories');
            Route::delete('/delete', HelperRoute::getController('ProductCategoryController@delete'))->middleware(SkijasiCheckPermissions::class.':delete_product_categories');
            Route::delete('/delete-multiple', HelperRoute::getController('ProductCategoryController@deleteMultiple'))->middleware(SkijasiCheckPermissions::class.':delete_product_categories');
            Route::delete('/force-delete', HelperRoute::getController('ProductCategoryController@forceDelete'))->middleware(SkijasiCheckPermissions::class.':delete_permanent_product_categories');
            Route::delete('/force-delete-multiple', HelperRoute::getController('ProductCategoryController@forceDeleteMultiple'))->middleware(SkijasiCheckPermissions::class.':delete_permanent_product_categories');
        });

        Route::group(['prefix' => 'discount'], function () {
            Route::get('/', HelperRoute::getController('DiscountController@browse'))->middleware(SkijasiCheckPermissions::class.':browse_discounts');
            Route::get('/bin', HelperRoute::getController('DiscountController@browseBin'))->middleware(SkijasiCheckPermissions::class.':browse_discounts_bin');
            Route::get('/read', HelperRoute::getController('DiscountController@read'))->middleware(SkijasiCheckPermissions::class.':read_discounts');
            Route::post('/restore', HelperRoute::getController('DiscountController@restore'))->middleware(SkijasiCheckPermissions::class.':restore_discounts');
            Route::post('/restore-multiple', HelperRoute::getController('DiscountController@restoreMultiple'))->middleware(SkijasiCheckPermissions::class.':restore_discounts');
            Route::post('/add', HelperRoute::getController('DiscountController@add'))->middleware(SkijasiCheckPermissions::class.':add_discounts');
            Route::put('/edit', HelperRoute::getController('DiscountController@edit'))->middleware(SkijasiCheckPermissions::class.':edit_discounts');
            Route::delete('/delete', HelperRoute::getController('DiscountController@delete'))->middleware(SkijasiCheckPermissions::class.':delete_discounts');
            Route::delete('/delete-multiple', HelperRoute::getController('DiscountController@deleteMultiple'))->middleware(SkijasiCheckPermissions::class.':delete_discounts');
            Route::delete('/force-delete', HelperRoute::getController('DiscountController@forceDelete'))->middleware(SkijasiCheckPermissions::class.':delete_permanent_discounts');
            Route::delete('/force-delete-multiple', HelperRoute::getController('DiscountController@forceDeleteMultiple'))->middleware(SkijasiCheckPermissions::class.':delete_permanent_discounts');
        });

        Route::group(['prefix' => 'payment'], function () {
            Route::get('/', HelperRoute::getController('PaymentController@browse'))->middleware(SkijasiCheckPermissions::class.':browse_payments');
            Route::get('/read', HelperRoute::getController('PaymentController@read'))->middleware(SkijasiCheckPermissions::class.':read_payments');
            Route::post('/add', HelperRoute::getController('PaymentController@add'))->middleware(SkijasiCheckPermissions::class.':add_payments');
            Route::put('/edit', HelperRoute::getController('PaymentController@edit'))->middleware(SkijasiCheckPermissions::class.':edit_payments');
            Route::delete('/delete', HelperRoute::getController('PaymentController@delete'))->middleware(SkijasiCheckPermissions::class.':delete_payments');
            Route::get('/option', HelperRoute::getController('PaymentController@browseOption'))->middleware(SkijasiCheckPermissions::class.':browse_payment_options');
            Route::post('/option/add', HelperRoute::getController('PaymentController@addOption'))->middleware(SkijasiCheckPermissions::class.':add_payment_options');
            Route::put('/option/edit', HelperRoute::getController('PaymentController@editOption'))->middleware(SkijasiCheckPermissions::class.':edit_payment_options');
            Route::delete('/option/delete', HelperRoute::getController('PaymentController@deleteOption'))->middleware(SkijasiCheckPermissions::class.':delete_payment_options');
            Route::put('/option/arrange', HelperRoute::getController('PaymentController@arrangeOption'))->middleware(SkijasiCheckPermissions::class.':edit_payment_options');

            Route::group(['prefix' => 'public'], function () {
                Route::get('/', HelperRoute::getController('PublicController\PaymentController@browse'));
                Route::get('/read', HelperRoute::getController('PublicController\PaymentController@read'));
                    
            });
           
        });

        Route::group(['prefix' => 'order', 'middleware' => [SkijasiAuthenticate::class]], function () {
            Route::get('/', HelperRoute::getController('OrderController@browse'))->middleware(SkijasiCheckPermissions::class.':browse_orders');
            Route::get('/read', HelperRoute::getController('OrderController@read'))->middleware(SkijasiCheckPermissions::class.':read_orders');
            Route::post('/confirm', HelperRoute::getController('OrderController@confirm'))->middleware(SkijasiCheckPermissions::class.':confirm_orders');
            Route::post('/reject', HelperRoute::getController('OrderController@reject'))->middleware(SkijasiCheckPermissions::class.':confirm_orders');
            Route::post('/ship', HelperRoute::getController('OrderController@ship'))->middleware(SkijasiCheckPermissions::class.':confirm_orders');
            Route::post('/done', HelperRoute::getController('OrderController@done'))->middleware(SkijasiCheckPermissions::class.':confirm_orders');

            Route::get('/orderpermonth', HelperRoute::getController('OrderController@getOrdersPerMonth'))->middleware(SkijasiCheckPermissions::class.':read_orders');
        });

        Route::group(['prefix' => 'cart'], function () {
            Route::get('/', HelperRoute::getController('CartController@browse'))->middleware(SkijasiCheckPermissions::class.':browse_carts');
            Route::get('/read', HelperRoute::getController('CartController@read'))->middleware(SkijasiCheckPermissions::class.':read_carts');
            Route::delete('/delete', HelperRoute::getController('CartController@delete'));

            Route::put('/edit', HelperRoute::getController('CartController@edit'))->middleware(SkijasiCheckPermissions::class.':edit_carts');
        });

        Route::group(['prefix' => 'user-address'], function () {
            Route::get('/', HelperRoute::getController('UserAddressController@browse'))->middleware(SkijasiCheckPermissions::class.':browse_user_addresses');
            Route::get('/read', HelperRoute::getController('UserAddressController@read'))->middleware(SkijasiCheckPermissions::class.':read_user_addresses');
        });

        Route::group(['prefix' => 'review', 'middleware' => [SkijasiAuthenticate::class]], function () {
            Route::get('/', HelperRoute::getController('ReviewController@browse'))->middleware(SkijasiCheckPermissions::class.':browse_product_reviews');
            Route::get('/read', HelperRoute::getController('ReviewController@read'))->middleware(SkijasiCheckPermissions::class.':read_product_reviews');
            Route::delete('/delete', HelperRoute::getController('ReviewController@delete'))->middleware(SkijasiCheckPermissions::class.':delete_product_reviews');
        });

        Route::group(['prefix' => 'product/public'], function () {
            Route::get('/', HelperRoute::getController('PublicController\ProductController@browse'));
            Route::get('/browse-category-slug', HelperRoute::getController('PublicController\ProductController@browseByCategorySlug'));
            Route::get('/browse-similar', HelperRoute::getController('PublicController\ProductController@browseSimilar'));
            Route::get('/read', HelperRoute::getController('PublicController\ProductController@read'));
            Route::get('/read-by-cart', HelperRoute::getController('PublicController\ProductController@readSimple'));
            Route::get('/search', HelperRoute::getController('PublicController\ProductController@search'));
            Route::get('/best-selling', HelperRoute::getController('PublicController\ProductController@browseBestSellingProduct'));
        });

        Route::group(['prefix' => 'product-category/public'], function () {
            Route::get('/', HelperRoute::getController('PublicController\ProductCategoryController@browse'));
            Route::get('/read', HelperRoute::getController('PublicController\ProductCategoryController@read'));
        });

        Route::group(['prefix' => 'cart/public', 'middleware' => [SkijasiAuthenticate::class]], function () {
            Route::get('/', HelperRoute::getController('PublicController\CartController@browse'));
            Route::post('/add', HelperRoute::getController('PublicController\CartController@add'));
            Route::post('/addplacanja', HelperRoute::getController('PublicController\CartController@addplacanja'));
            
            Route::post('/addplacanjazahtjevi', HelperRoute::getController('PublicController\CartController@addplacanjazahtjevi'));

            Route::put('/edit', HelperRoute::getController('PublicController\CartController@edit'));
            Route::put('/edit-cart', HelperRoute::getController('PublicController\CartController@editCart'));
            Route::delete('/delete', HelperRoute::getController('PublicController\CartController@delete'));
            Route::post('/validate', HelperRoute::getController('PublicController\CartController@validate'));

            Route::get('/totalitems', HelperRoute::getController('PublicController\CartController@getTotalCartItems'));
        });

        Route::group(['prefix' => 'order/public', 'middleware' => [SkijasiAuthenticate::class]], function () {
            Route::get('/', HelperRoute::getController('PublicController\OrderController@browse'));
            Route::get('/read', HelperRoute::getController('PublicController\OrderController@read'));
            Route::post('/pay', HelperRoute::getController('PublicController\OrderController@pay'));
            Route::post('/finish', HelperRoute::getController('PublicController\OrderController@finish'));

            Route::post('/orderstatus', HelperRoute::getController('PublicController\OrderController@getOrderStatus'));
                //uplatnice
                Route::post('/stvoriuplatnicu', HelperRoute::getController('PublicController\OrderController@stvoriuplatnicu'));


        });

        Route::group(['prefix' => 'review/public'], function () {
            Route::get('/', HelperRoute::getController('PublicController\ReviewController@browse'));
            Route::post('/submit', HelperRoute::getController('PublicController\ReviewController@submit'));
            Route::get('/read', HelperRoute::getController('PublicController\ReviewController@read'));
        });

        Route::group(['prefix' => 'user-address/public', 'middleware' => [SkijasiAuthenticate::class]], function () {
            Route::get('/', HelperRoute::getController('PublicController\UserAddressController@browse'));
            Route::get('/read', HelperRoute::getController('PublicController\UserAddressController@read'));
            Route::post('/add', HelperRoute::getController('PublicController\UserAddressController@add'));
            Route::put('/edit', HelperRoute::getController('PublicController\UserAddressController@edit'));
            Route::delete('/delete', HelperRoute::getController('PublicController\UserAddressController@delete'));
            Route::post('/main', HelperRoute::getController('PublicController\UserAddressController@setMain'));
        });


        
        Route::get('/user/public/count', HelperRoute::getController('PublicController\UserController@countUsers'));
        Route::get('/user/public/browsezborovi', HelperRoute::getController('PublicController\UserController@fetchZborovi'));


        Route::group(['prefix' => 'user/public', 'middleware' => [SkijasiAuthenticate::class]], function () {
            Route::put('/edit', HelperRoute::getController('PublicController\UserController@edit'));
            Route::post('/change', HelperRoute::getController('PublicController\UserController@changePassword'));
            Route::put('/prijavnicaedit', HelperRoute::getController('PublicController\UserController@prijavnicaedit'));

           
        });


        Route::group(['prefix' => 'notification/public', 'middleware' => [SkijasiAuthenticate::class]], function () {
            Route::get('/', HelperRoute::getController('PublicController\NotificationController@browse'));
            Route::post('/read', HelperRoute::getController('PublicController\NotificationController@read'));
            Route::post('/read-all', HelperRoute::getController('PublicController\NotificationController@readAll'));
        });

        Route::group(['prefix' => 'configurations', 'middleware' => [SkijasiAuthenticate::class]], function () {
            Route::put('/edit', HelperRoute::getController('ConfigurationController@edit'))->middleware(SkijasiCheckPermissions::class.':edit_configurations');
            Route::put('/edit-multiple', HelperRoute::getController('ConfigurationController@editMultiple'))->middleware(SkijasiCheckPermissions::class.':edit_configurations');
            Route::post('/add', HelperRoute::getController('ConfigurationController@add'))->middleware(SkijasiCheckPermissions::class.':add_configurations');
            Route::delete('/delete', HelperRoute::getController('ConfigurationController@delete'))->middleware(SkijasiCheckPermissions::class.':delete_configurations');
        });



        Route::group(['prefix' => 'configurations'], function () {
            Route::get('/', HelperRoute::getController('ConfigurationController@browse'));
            Route::get('/read', HelperRoute::getController('ConfigurationController@read'));
        });
    });
});
