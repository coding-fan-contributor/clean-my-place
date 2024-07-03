<?php

use Illuminate\Http\Request;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\PushController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\CronjobController;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// ============== Ajax =================
//admin

Route::get('/test_mail', [ApiController::class,'test_mail']);
Route::post('/changeStatus', [AjaxController::class,'changeStatus']);
Route::post('/updatePriority', [AjaxController::class,'updatePriority']);
Route::post('/delete', [AjaxController::class,'delete']);
Route::post('/changeOrderHistoryStatus', [AjaxController::class,'changeCleansStatus']);
Route::post('/changeAvailability', [AjaxController::class,'changeAvailability']);
Route::post('/deletecleanerschedule', [AjaxController::class,'deletecleanerschedule']);

Route::post('/changePayoutStatus', [AjaxController::class,'changePayoutStatus']);
Route::post('/pay_payout', [AjaxController::class,'pay_payout']);

//chat notify
Route::post('/chatnotify', [PushController::class,'chatNotify']);
//get & delete chats
Route::post('/getchatlist', [ApiController::class,'getChatList']);
Route::post('/deletechat', [ApiController::class,'deleteChat']);

// change order history date
Route::post('/changehistorydate', [AjaxController::class,'changehistorydate']);

// Resend order notifications
Route::post('/resend_notifications', [AjaxController::class,'resend_notifications']);



// ============= CRON ORDERS ===================

// send notification by reminder
Route::get('/cron/reminder', [CronjobController::class,'reminder']);

// send notification for complete order reminder after 1 hour of order complete
Route::get('/cron/complete_reminder', [CronjobController::class,'complete_reminder']);
Route::get('/cron/cron_test', [CronjobController::class,'cron_test']);


Route::get('/test', [ApiController::class,'test']);



// api
Route::group(['middleware' => ['HeaderAuth']], function () {
    //user
    Route::post('/user/register',[ApiController::class,'user_register']);
    Route::post('/user/login',[ApiController::class,'user_login']);
    Route::post('/user/forgot',[ApiController::class,'user_forgot']);
    Route::post('/user/verify',[ApiController::class,'user_verify']);
    Route::post('/user/reset',[ApiController::class,'user_reset']);
    Route::get('/user/get/{id}',[ApiController::class,'user_get']);
    Route::post('/user/update_profile',[ApiController::class,'user_update_profile']);
    Route::get('/user/{id}/bookings',[ApiController::class,'user_bookings']);
    Route::get('/user/{id}/pendingbookings',[ApiController::class,'user_pending_bookings']);
    Route::get('/user/{id}/subscriptions',[ApiController::class,'user_subscriptions']);
    Route::get('/user/{id}/transactions',[ApiController::class,'user_transactions']);

    Route::post('/user/social_login',[ApiController::class,'socialLogin']);
    Route::post('/user/apple_login',[ApiController::class,'appleLogin']);

    Route::post('/user/resumehold',[ApiController::class,'resumehold']);

    Route::get('/user/banners', [ApiController::class,'getUserBanners']);

    // alternate datetime availability accept/reject || for user
    Route::post('/user/accept_alternative',[ApiController::class,'accept_alternative']);
    Route::post('/user/reject_alternative',[ApiController::class,'reject_alternative']);


    //cleaner
    Route::post('/cleaner/register',[ApiController::class,'cleaner_register']);
    Route::post('/cleaner/login',[ApiController::class,'cleaner_login']);
    Route::post('/cleaner/forgot',[ApiController::class,'cleaner_forgot']);
    Route::post('/cleaner/verify',[ApiController::class,'cleaner_verify']);
    Route::post('/cleaner/reset',[ApiController::class,'cleaner_reset']);
    Route::get('/cleaner/get/{id}',[ApiController::class,'cleaner_get']);
    Route::post('/cleaner/update_profile',[ApiController::class,'cleaner_update_profile']);

    Route::get('/cleaner/{id}/bookings',[ApiController::class,'cleaner_bookings']);
    Route::get('/cleaner/{id}/pendingbookings',[ApiController::class,'cleaner_pending_bookings']);
    Route::get('/cleaner/{cleaner_id}/schedule',[ApiController::class,'cleaner_schedule']);
    Route::post('/cleaner/{id}/transactions',[ApiController::class,'cleaner_transactions']);

    Route::get('/cleaner/{id}/checkverification',[ApiController::class,'cleaner_check_verification']);
    Route::post('/cleaner/{id}/updatedocument',[ApiController::class,'cleaner_updatedocument']);

    Route::get('/cleaner/{id}/notifications',[ApiController::class,'get_notifications']);

    Route::get('/cleaner/banners', [ApiController::class,'getCleanerBanners']);

    //favourites
    Route::post('/favourite/create',[ApiController::class,'favourite_create']);
    Route::post('/favourite/delete',[ApiController::class,'favourite_delete']);
    Route::get('/favourite/get/{user_id}',[ApiController::class,'favourite_get']);


    // global
    Route::get('/orderdetails/{id}',[ApiController::class,'order_details']);
    Route::get('/orderhistory/{id}',[ApiController::class,'cleans_details']);

    Route::post('/order_create',[ApiController::class,'order_create']);
    Route::get('/test_latlongdis/{order_id}',[ApiController::class,'get_nearest_cleaners']);
    Route::get('/order_status_update/{order_id}/{status}',[ApiController::class,'order_status_update']);
    Route::post('/orderstatuschange',[ApiController::class,'order_status_change']);
    Route::post('/historystatuschange',[ApiController::class,'history_status_change']);
    Route::post('/acceptorder',[ApiController::class,'accept_order']);
    Route::post('/acceptrequest', [ApiController::class,'accept_request']);
    Route::post('/rejectorder',[ApiController::class,'reject_order']);
    Route::post('/cancelorder',[ApiController::class,'cancel_order']);
    Route::post('/cleaners/delete',[ApiController::class,'delete_cleaner']);

    Route::post('/addrating',[ApiController::class,'addrating']);
    Route::get('/getratings/{cleaner_id}',[ApiController::class,'get_ratings']);

    Route::post('/schedule/create',[ApiController::class,'schedule_create']);
    Route::post('/schedule/delete',[ApiController::class,'schedule_delete']);


    Route::get('/getprice/{hour}',[ApiController::class,'get_price']);

    Route::get('/getsettings',[ApiController::class,'get_settings']);
    Route::get('/getextraservices',[ApiController::class,'get_extra_services']);
    Route::get('/gettaxsettings',[ApiController::class,'get_tax_settings']);

    // update image
    Route::any('/user/update_user_image/{user_id}',[ApiController::class,'update_user_image']);
    Route::any('/cleaner/update_cleaner_image/{cleaner_id}',[ApiController::class,'update_cleaner_image']);

    Route::any('/cleaner/upload_cleaner_id/{cleaner_id}',[ApiController::class,'upload_cleaner_id']);
    Route::any('/cleaner/upload_cleaner_address/{cleaner_id}',[ApiController::class,'upload_cleaner_address']);


    Route::post('/contact',[ApiController::class,'contact']);


    // stripe

    Route::post('/chargenewcustomer/{user_id}/{order_id}/{amount}/{token}', [StripeController::class,'charge_new_customer']);
    Route::post('/chargeexistingcustomer/{user_id}/{order_id}/{amount}', [StripeController::class,'charge_existing_customer']);

    // skip next clean
    Route::post('/user/{id}/skip_next_clean',[ApiController::class,'skip_next_clean']);

    // alternate datetime availability || for cleaner
    Route::post('/cleaner/available_alternate',[ApiController::class,'available_alternate']);


    // test & debug
    Route::get('/test/debug', [ApiController::class,'testDebug']);


    // stripe connect gets
    Route::get('/balance/{cleaner_id}',[ApiController::class,'get_balance']);
    Route::get('/payouts/{cleaner_id}',[ApiController::class,'get_payouts']);
    Route::get('/balance_payouts/{cleaner_id}',[ApiController::class,'get_balance_payouts']);

});
