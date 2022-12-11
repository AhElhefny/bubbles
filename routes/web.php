<?php

use Illuminate\Support\Facades\Route;

     // Route::get('/', 'WebsiteSettingsController@index');
     Route::get('/pp', function(){
        $order=\App\Models\Order::with(['city','branch','payment','services','user'])->first();
        $data=['id'=>$order->id,'order_number'=>$order->order_number,'car_type'=>$order->car_type,'total'=>$order->total,'delivery_date'=>$order->delivery_date,'delivery_time'=>$order->delivery_time];
        $bookingFire = app('firebase.firestore')->database()->collection('orders')->newDocument();
            $bookingFire->set($data);
     });

Route::group(['prefix' => LaravelLocalization::setLocale()], function()
{

    Route::get('/', function(){

        return view('auth.login');

     });

    Route::group(['middleware'=> array('auth') ,'prefix' => 'admin',  'namespace' => 'Dashboard'], function (){

        Route::get('/', 'DashboardController@index')->name('admin-dashboard');
        Route::resource('customers', 'CustomerController')->except('show');
        Route::get('customers/export-to-excel', 'CustomerController@exportToExcel')->name('customers.exportToExcel');
        Route::post('customers/import-excel', 'CustomerController@importExcel')->name('customers.importExcel');
        Route::get('customers/export-pdf', 'CustomerController@exportTopdf')->name('customers.exportTopdf');
        Route::resource('modules','ModuleController');
        Route::get('customers/quality-report', 'CustomerController@qualityReport')->name('customers.qualityReport');
        Route::prefix('customers/{id}')->group(function () {
        Route::post('balance_action', 'CustomerController@balanceAction')->name('customers.balance_action');
        Route::get('overview', 'CustomerController@overview')->name('customers.show');
        Route::get('orders', 'CustomerController@orders')->name('customers.orders');
        Route::get('addresses', 'CustomerController@addresses')->name('customers.addresses');

        });

        Route::resource('products', 'ProductController')->except('show');
        Route::resource('slider', 'SliderController')->except('show');
        Route::resource('websiteservice', 'WebsiteServicesController')->except('show');
        Route::resource('sellers', 'SellerController');
        Route::prefix('products')->group(function () {
        Route::get('{id}/show', 'ProductController@show')->name('products.show');

        });

        Route::resource('orders', 'OrderController')->except('show');

        Route::prefix('orders')->group(function () {
        Route::get('tmp/{template}', 'OrderController@index')->name('orders.templates');
        Route::post('cart-to-order/{cart_id}', 'OrderController@cartToOrder')->name('orders.cartToOrder');
        Route::post('import-excel', 'OrderController@importExcel')->name('orders.importExcel');
        Route::get('export-to-excel-downloads-template', 'OrderController@exportToExcelDownloadsTemplate')->name('orders.exportToExcelDownloadsTemplate');
        Route::get('export-to-excel', 'OrderController@exportToExcel')->name('orders.exportToExcel');
        Route::get('order-delivery-date-time-list', 'OrderController@orderDeliveryDateTimeList')->name('orders.orderDeliveryDateTimeList');
        Route::get('{id}/statuses-list', 'OrderController@statusesList')->name('orders.statuses-list');
        Route::post('{id}/sendtoDriver', 'OrderController@sendtoDriver')->name('orders.sendToDriver');
        Route::get('{id}/products-list', 'OrderController@productsList')->name('orders.products_list');
        Route::get('{id}/show', 'OrderController@show')->name('orders.show');
        Route::get('get-available-shifts', 'OrderController@getAvailableShifts')->name('orders.available_shifts');
        Route::post('change-bulk-statuses', 'OrderController@changeBulkstatuses')->name('orders.change_bulk_statuses');
        Route::get('{order_number}/view-invoice/{template?}','OrderController@viewInvoice')->name('orders.view_invoice');

        });

        Route::group(['prefix' => 'messages', 'as' => 'message.'], function () {
        Route::post('{conversation}/close', 'ChatController@closeConversation')->name('close');
        Route::get('/', 'ChatController@index')->name('index');
        Route::get('{conversation}', 'ChatController@chatHistory')->name('read');
        Route::post('{conversation}/set-action', 'ChatController@setAction')->name('set_action');

       });

       Route::group(['prefix' => 'chatbot', 'as' => 'chatbot.'], function () {
       Route::resource('answers', 'ChatbotController');

    });

    Route::resource('banks', 'BankController')->except('show');
    Route::resource('promocodes', 'PromocodesController')->except('show');
    Route::resource('region', 'AreaController')->except('show');
    Route::resource('template', 'TemplatesController')->except('show');
    Route::resource('pages', 'PagesController')->except('show');
    Route::resource('cities','CityController')->except('show');
    Route::get('contacts', 'ContactusController@index');
    Route::get('contactus/show/{id}', 'ContactusController@show');
    Route::resource('roles', 'RoleController');
    Route::resource('users','UserController')->except('show');
   
    Route::resource('notification-messages', 'AppNotificationController')->except('show');
    Route::get('notifications/show/{id}', 'AdminNotificationsController@show');
    Route::get('notification-users', 'AppNotificationController@showusers');

    Route::resource('delegates','DelegateController')->except('show');
    Route::resource('deliverydays', 'DeliveryDayController')->except('show');
    Route::resource('financial','FinancialController')->except('show');
    Route::resource('category', 'CategoryController')->except('show');
    Route::get('invoice','FinancialController@orders')->name('invoices');
    Route::get('show_invoice','FinancialController@show_invoices')->name('show_invoice');
    Route::get('financial/show_orders','FinancialController@financial_orders')->name('show-orders');

    Route::get('getusers', 'AppNotificationController@getusers');
    Route::resource('emails','EmailSmsController')->except('show');
    Route::resource('compansations','CompansationController')->except('show');
    Route::post('getusers','UserController@getusers')->name('getusers');
    Route::post('getordercustomer','CompansationController@getcustomers');

    Route::resource('carts','CartsController')->only('show','index');
    Route::get('cartorders','CartsController@cartorder')->name('cartorders.index');
    Route::get('logActivity', 'UserController@logActivity');
    Route::get('add-tolog', 'UserController@myTestAddToLog');

    Route::post('/general-setting/update', 'BusinessController@update')->name('setting.update');
    Route::get('/settings/app', 'BusinessController@main_settings');
    Route::get('settings/worktimes', 'BusinessController@worktimes_settings');
    Route::get('/settings/orders', 'BusinessController@order_settings')->name('order.settings');
    Route::post('/home-setting/update', 'BusinessController@updatesetting')->name('settings.update');

    Route::get('settings/home', 'BusinessController@home_settings');
    Route::get('settings/home_page', 'BusinessController@homepage_settings');
    Route::get('homesettings/delete/{id}', 'BusinessController@deleteimages');

    });

    Route::group(['prefix' => 'ajax', 'as' => 'ajax::', 'namespace' => 'Dashboard'], function () {
    Route::post('message/send', 'ChatController@ajaxSendMessage')->name('new');
    Route::delete('message/delete/{id}', 'ChatController@ajaxDeleteMessage')->name('delete');

   });

    Route::group(['prefix' => 'vendor', 'namespace' => 'Dashboard'], function () {

      Route::get('/dashboard', 'DashboardController@sellerDashboard')->name('dashboard');
      Route::resource('branches', 'BranchController');
      Route::resource('worktimes','WorkTimeController')->except('show');
      Route::resource('services', 'ServiceController')->except('show');
      Route::resource('carsize', 'CarSizeController')->except('show');
      Route::get('/settings/branches_taxes', 'BusinessController@branchestax_settings');
      Route::post('branchtaxes/update/{id}', 'BusinessController@updateBranchtax')->name('branches_settings.update');
      
      Route::prefix('sellerorders')->group(function () {
       
          Route::get('/', 'OrderController@sellersIndex')->name('orders.sellers');
          Route::get('/{order}/edit', 'OrderController@edit')->name('sellerorders.edit');
          Route::get('tmp/{template}', 'OrderController@index')->name('orders.templates');
          Route::post('cart-to-order/{cart_id}', 'OrderController@cartToOrder')->name('orders.cartToOrder');
          Route::post('import-excel', 'OrderController@importExcel')->name('orders.importExcel');
          Route::get('export-to-excel-downloads-template', 'OrderController@exportToExcelDownloadsTemplate')->name('orders.exportToExcelDownloadsTemplate');
          Route::get('export-to-excel', 'OrderController@exportToExcel')->name('orders.exportToExcel');
          Route::get('order-delivery-date-time-list', 'OrderController@orderDeliveryDateTimeList')->name('orders.orderDeliveryDateTimeList');
          Route::get('{id}/statuses-list', 'OrderController@statusesList')->name('sellerorders.statuses-list');
          Route::post('{id}/sendtoDriver', 'OrderController@sendtoDriver')->name('sellerorders.sendToDriver');
          Route::get('{id}/products-list', 'OrderController@productsList')->name('sellerorders.products_list');
          Route::get('{id}/show', 'OrderController@show')->name('sellerorders.show');
          Route::get('get-available-shifts', 'OrderController@getAvailableShifts')->name('sellerorders.available_shifts');
          Route::post('change-bulk-statuses', 'OrderController@changeBulkstatuses')->name('sellerorders.change_bulk_statuses');
          Route::get('{order_number}/view-invoice/{template?}','OrderController@viewInvoice')->name('sellerorders.view_invoice');

        });
   });
   
    Route::get('editprofile/{id}', '\App\Http\Controllers\Dashboard\UserController@editProfile');
    Route::put('updateprofile/{id}', '\App\Http\Controllers\Dashboard\UserController@updateProfile')->name('userprofile.update');
    Auth::routes();
    Route::get('/logout', '\App\Http\Controllers\Auth\LoginController@logout');
    Route::get('/home', 'HomeController@index')->name('home');
});

