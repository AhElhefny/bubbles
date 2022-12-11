<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\V3\OrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

    Route::group([

       'prefix' => 'v3',
       'namespace' => 'Api\V3',
    
    ], function () {

        Route::post('contact-us/send', 'ContactUsController@send');
        Route::prefix('carts')->group(function () {

            Route::get('{cart_id}/items', 'CartController@cartItems');
            Route::get('{user_id}/user-cart', 'CartController@userCart');
            Route::get('service-worktimes/{cart_id}', 'CartController@serviceWorkTimes');
            Route::post('add', 'CartController@addToCart');
            Route::post('add/service', 'CartController@addserviceToCart');
            Route::post('{cart_id}/services/{product_id}/remove', 'CartController@removeFromCart');
            Route::post('{cart_id}/empty', 'CartController@emptyCart');
            // Route::post('{cart_id}/add-to-user', 'CartController@updateCartUser')->middleware('auth:api');add_cart_to_user
            Route::post('{cart_id}/add-to-user', 'CartController@add_cart_to_user')->middleware('auth:api');

            
        });

        Route::get('branches','BranchController@index');
        Route::get('branches/{id}','BranchController@show');
        Route::post('branches/open','BranchController@openBranch');
        Route::get('services','ServiceController@index');
        Route::get('services/{id}','ServiceController@show');
        Route::get('search/services','ServiceController@search');

        Route::prefix('services')->group(function () {
            Route::get('/{service}/show', 'ProductController@servicedetail');
        });

        Route::prefix('categories')->group(function () {
            Route::get('/', 'ProductController@categories');
            Route::get('{id}/show', 'ProductController@category');
        });

        Route::prefix('notifications')->group(function () {
            Route::get('/', 'NotificationController@index');
            Route::post('/subscribe', 'NotificationController@subscribe');
        });

        Route::prefix('settings')->group(function () {
            
            Route::get('general', 'SettingController@general');
            Route::get('cities', 'SettingController@cities');
            Route::get('payment-methods', 'SettingController@paymentMethods');
            Route::get('banks-list', 'SettingController@banks');
            Route::get('socials', 'SettingController@socialsAccounts');
            Route::get('slider', 'SettingController@slider');
            Route::get('orders/min-amount', 'SettingController@orderMinAmountAndPrice');
            Route::post('/notifications', 'SettingController@notificationSettings')->middleware('auth:api');
            Route::get('worktimes', 'SettingController@worktimeSettings');

        });

        Route::prefix('auth')->group(function () {

            Route::post('otp-login', 'AuthController@otp_login');
            Route::post('vendor_login', 'AuthController@vendor_login');
            Route::post('otp-verify', 'AuthController@otp_verify');
            Route::middleware('auth:api')->group(function () {
            Route::post('otp-register', 'AuthController@otp_register');
            Route::post('logout', 'AuthController@logout');

            });
        });
         
       Route::middleware('auth:api')->group(function () {
        Route::prefix('orders')->group(function () {

            Route::post('receipt-details', 'OrderController@getReceipt')->name('orders.getReceipt');
            Route::get('/', 'OrderController@index');
            Route::post('{order}/update-payment-method', 'OrderController@updatePaymentMethod');
            Route::get('{order}/show', 'OrderController@show');
            Route::post('{order}/cancel', 'OrderController@cancel');
            Route::post('create', 'OrderController@create');
            Route::post('reorder', 'OrderController@reorder');
            Route::post('{order}/rate', 'OrderController@rate');
            Route::post('use-wallet', 'OrderController@useWallet');
            Route::post('apply-coupon', 'OrderController@applyCoupon');
            Route::post('get-available-shifts', 'OrderController@getAvailableShifts');
            Route::get('has-new-updates', 'OrderController@hasNewUpdates');
            Route::get('branch-orders', 'OrderController@washerOrders');
            Route::post('update-status', 'OrderController@changeOrderStatus');
            Route::post('activate-orders', 'OrderController@orderActivate');

        }); 

        Route::prefix('user')->group(function () {

            Route::get('profile', 'UserController@profile');
            Route::post('profile/update', 'UserController@updateProfile');
            Route::post('photo/update', 'UserController@updatePhoto');
            Route::post('update-device-token', 'UserController@updateDeviceToken');
            Route::get('wallet', 'UserController@Wallet');
        });

        Route::prefix('addresses')->group(function () {
            Route::get('/get-default-address', 'AddressController@getDefaultAddress');
            Route::post('/set-default-address', 'AddressController@setDefaultAddress');
            Route::get('', 'AddressController@index');
            Route::post('store', 'AddressController@store');
            Route::post('{id}/update', 'AddressController@update');
            Route::post('{id}/delete', 'AddressController@delete');

        });
      });

        Route::prefix('pages')->group(function () {
       
            Route::get('/', 'PageController@index');
            Route::get('{slug}', 'PageController@page');
           
        });

        Route::prefix('payments')->group(function () {
            Route::get('payfort/return', 'OrderController@payfortReturn');
            Route::get('status/{status}', 'OrderController@paymentStatus')->name('payments.status');
            Route::get('/callback',[OrderController::class,'callback']);

        });

        Route::get('get-city-by-coordinates', 'AddressController@getCityByCoordinates');
        Route::prefix('external-services/mirnah')->group(function () {
            Route::get('auto-send-new-order-to-mirnah', 'MirnahController@autoSendOrderToMirnah')->name('mirnah.autoSendOrderToMirnah');
            Route::post('get-new-orders', 'MirnahController@getNewOrders')->name('mirnah.getNewOrders');
            Route::post('finish-order', 'MirnahController@finishOrder')->name('mirnah.finishOrder');
            Route::post('send-driver-location', 'MirnahController@currentDriverLocation')->name('mirnah.sendDriverLocation');

        });
});


