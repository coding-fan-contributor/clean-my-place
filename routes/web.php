<?php
use App\Http\Controllers\WebController;
use App\Http\Controllers\PublicAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AppPaymentController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/admin/login');
});

Route::get('/admin', function () {
    return redirect('/admin/login');
});

// ================ web
Route::get('/tnc', [WebController::class,'tnc']);
Route::get('/cancellation-policy', [WebController::class,'cancellation']);

// ====== Public Auth | does not require csrf
Route::group(['middleware' => ['web']], function () {
    Route::get('/admin/login', function(){
        return view('admin.login');
    });
    Route::post('login_admin', [PublicAuthController::class,'login_admin']);
    Route::post('login_user', [PublicAuthController::class,'login_user']);

});

// ====== Admin
Route::group(['middleware' => ['web']], function () {
    Route::get('/admin/dashboard', [AdminController::class,'index']);
    Route::get('/admin/logout', [AdminController::class,'logout_admin']);
    Route::get('/admin/profile', [AdminController::class,'profile']);
    Route::post('/admin/updateprofile', [AdminController::class,'updateprofile']);
    Route::post('/admin/updatepassword', [AdminController::class,'updatepassword']);
    //users
    Route::get('/admin/customers', [AdminController::class,'users']);
    Route::get('/admin/customer/{id}', [AdminController::class,'updateuser_view']);
    Route::post('/admin/updatecustomer/{id}', [AdminController::class,'updateuser']);
    Route::get('/CreateCustomer', [AdminController::class,'createuser_view']);
    Route::post('/CreateCustomer', [AdminController::class,'createuser']);
    //cleaners
    Route::get('/admin/cleaners', [AdminController::class,'cleaners']);
    Route::get('/admin/create/cleaner', [AdminController::class,'createcleaner_view']);
    Route::post('/admin/createcleaner', [AdminController::class,'createcleaner']);
    Route::get('/admin/cleaner/{id}', [AdminController::class,'updatecleaner_view']);
    Route::post('/admin/updatecleaner/{id}', [AdminController::class,'updatecleaner']);
    Route::post('/admin/updatecleanerchedule/{id}', [AdminController::class,'updatecleanerchedule']);
    //mails content
    Route::get('/MailContent', [AdminController::class,'mailcontent']);
    Route::get('/CreateMailContent', [AdminController::class,'createmailcontent_view']);
    Route::post('/AddMailContent', [AdminController::class,'add_mail_content']);
    Route::get('/MailContentEdit/{id}', [AdminController::class,'update_mail_content_view']);
    Route::post('/UpdateMailContent/{id}', [AdminController::class,'update_mail_content']);
    //transactions
    Route::any('/admin/transactions', [AdminController::class,'transactions']);
    //reviews
    Route::get('/admin/reviews', [AdminController::class,'reviews']);
    Route::get('/admin/create/review', [AdminController::class,'createreview_view']);
    Route::post('/admin/createreview', [AdminController::class,'createreview']);
    //orders
    Route::any('/admin/orders', [AdminController::class,'orders']);
    Route::get('/admin/order/{id}', [AdminController::class,'order']);
    Route::post('/admin/updateorder/{id}', [AdminController::class,'updateorder']);
    Route::post('/admin/accept_order/{order_id}/{cleaner_id}',[AdminController::class,'accept_order']);
    //galleries
    Route::get('/admin/galleries', [AdminController::class,'galleries']);
    //settings
    Route::get('/admin/settings', [AdminController::class,'settings']);
    Route::post('/admin/createorupdatesetting', [AdminController::class,'createorupdatesetting']);


    //banners
    Route::get('/admin/banners', [AdminController::class,'banners']);
    Route::get('/admin/create/banner', [AdminController::class,'createbanner_view']);
    Route::post('/admin/createbanner', [AdminController::class,'createbanner']);

    //ExtraServices
    Route::get('/admin/extraservices', [AdminController::class,'extraservices']);
    Route::get('/admin/create/extraservice', [AdminController::class,'createextraservice_view']);
    Route::post('/admin/createextraservice', [AdminController::class,'createextraservice']);

    //TaxSettings
    Route::get('/admin/taxsettings', [AdminController::class,'taxsettings']);
    Route::get('/admin/create/taxsetting', [AdminController::class,'createtaxsetting_view']);
    Route::post('/admin/createtaxsetting', [AdminController::class,'createtaxsetting']);

    // export
    Route::get('/admin/export_users', [AdminController::class,'export_users']);
    Route::get('/admin/export_cleaners', [AdminController::class,'export_cleaners']);
    Route::get('/admin/export_orders/{start?}/{end?}', [AdminController::class,'export_orders']);
    Route::get('/admin/export_transactions/{start?}/{end?}', [AdminController::class,'export_transactions']);

    // payouts
    Route::any('/admin/payouts', [AdminController::class,'payouts']);

    // documents
    Route::get('/admin/documents', [AdminController::class,'documents']);
    Route::get('/admin/document/{id}', [AdminController::class,'document']);
    Route::post('/admin/updatedocument/{id}', [AdminController::class,'updatedocument']);


    // app payment of stripe
    Route::get('/processpayment/{order_id}', [AppPaymentController::class,'processpayment']);
    Route::post('/charge', [AppPaymentController::class,'charge']);
    Route::post('/chargeconfirmed', [AppPaymentController::class,'chargeconfirmed']);

    // card update
    Route::get('/cardupdate/{user_id}', [AppPaymentController::class,'cardupdate']);
    Route::post('/updatecard', [AppPaymentController::class,'updatecard']);

    // push notifications
    Route::get('/admin/push', [AdminController::class,'push_view']);
    Route::post('/admin/sendpush', [AdminController::class,'sendpush']);
});
