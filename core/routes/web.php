<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BonusRewardController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ArchivementController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CronController;
use App\Http\Controllers\Gateway\Espay\PaymentController;
use App\Http\Controllers\Gateway\Paylabs\PaylabsPaymentController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\NewCronController;
use App\Http\Controllers\NewSponsorRegitserController;
use App\Http\Controllers\SponsorRegisterController;
use App\Http\Controllers\UserController;
use App\Models\rekening;
use App\Models\test;
use App\Models\ureward;
use App\Models\User;
use App\Models\UserExtra;
use App\Models\UserGold;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

Route::get('cyc',function(){
    updateCycleNasional(1);
});


Route::get('reset-point-startDay',[CronController::class,'resetCountingLF']);

// Route::get('create-acc/{jml}/{username}',[LandingController::class,'createAcc']);
// Route::get('/update-counting-user/{username}/{pos}/{count}',function($username,$pos,$count){
//     return UpdateCountingUser($username,$pos,$count);
// });
// Route::get('dashboard-admin',[AdminController::class,'viewOnly']);
// Route::get('/',[LandingController::class,'index'])->name('home');
Route::get('/',function(){
    return redirect('/login');
})->name('home');

Route::get('/build',function(){
    return view('v3.build');
});

Route::get('/cek_bit', function(){
    dd(PHP_INT_MAX);
});
Route::get('/clear', function(){
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
});
Route::get('/key', function(){
    \Illuminate\Support\Facades\Artisan::call('key:generate');
});
Route::get('/voldemort/up', function(){
    \Illuminate\Support\Facades\Artisan::call('up');
    echo "Website on Live Production";
});
Route::get('/voldemort/down', function(){
    \Illuminate\Support\Facades\Artisan::call('down --secret="harrypotter"');
    echo "Website on Maintenance";
});

Route::get('/generateUniqueCode/{no_bro}', 'Auth\RegisterController@generateUniqueCode');
Route::get('ref-user',function(){
    return User::userTree(Auth::user()->id);
});
Route::get('/clauseter-maps',[AdminController::class,'maps']);
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
Route::get('/cron', [NewCronController::class,'bonusPasangan'])->name('bv.matching.cron');
// Route::get('/pool', [NewCronController::class,'poolSharing'])->name('bv.matching.pool');
Route::get('/monoleg', 'CronController@monoleg')->name('bv.matching.monoleg');
Route::get('/monoleg_saving', 'CronController@monolegSaving')->name('bv.matching.monoleg_saving');
Route::get('/is-gold-cron', 'CronController@isGold');
Route::get('/is-gold-back', 'CronController@isGoldBack');
Route::get('/is-silver-check', 'CronController@isSilverCheck');
Route::get('/is-silver-cron','CronController@isSilverCron');
Route::get('gold-today', 'UserController@goldRates');
Route::get('cron-company-checkin', 'UserController@cronDailyCheckIn');
Route::get('cron-address-lang', 'CronController@userAddressLang');

// Route::get('/landing',function(){
//     return view('home.index');
// });
// Route::get('/',function(){
//     return redirect(url('login'));
// })->name('home');

// Route::get('/cron', 'CronController@cron')->name('bv.matching.cron');
// Route::get('/cron30bro', 'CronController@cron30bro')->name('bv.matching.cron30bro');

Route::namespace('Gateway')->prefix('ipn')->name('ipn.')->group(function () {
    Route::post('paypal', 'paypal\ProcessController@ipn')->name('paypal');
    Route::get('paypal_sdk', 'paypal_sdk\ProcessController@ipn')->name('paypal_sdk');
    Route::post('perfect_money', 'perfect_money\ProcessController@ipn')->name('perfect_money');
    Route::post('stripe', 'stripe\ProcessController@ipn')->name('stripe');
    Route::post('stripe_js', 'stripe_js\ProcessController@ipn')->name('stripe_js');
    Route::post('stripe_v3', 'stripe_v3\ProcessController@ipn')->name('stripe_v3');
    Route::post('skrill', 'skrill\ProcessController@ipn')->name('skrill');
    Route::post('paytm', 'paytm\ProcessController@ipn')->name('paytm');
    Route::post('payeer', 'payeer\ProcessController@ipn')->name('payeer');
    Route::post('paystack', 'paystack\ProcessController@ipn')->name('paystack');
    Route::post('voguepay', 'voguepay\ProcessController@ipn')->name('voguepay');
    Route::get('flutterwave/{trx}/{type}', 'flutterwave\ProcessController@ipn')->name('flutterwave');
    Route::post('razorpay', 'razorpay\ProcessController@ipn')->name('razorpay');
    Route::post('instamojo', 'instamojo\ProcessController@ipn')->name('instamojo');
    Route::get('blockchain', 'blockchain\ProcessController@ipn')->name('blockchain');
    Route::get('blockio', 'blockio\ProcessController@ipn')->name('blockio');
    Route::post('coinpayments', 'coinpayments\ProcessController@ipn')->name('coinpayments');
    Route::post('coinpayments_fiat', 'coinpayments_fiat\ProcessController@ipn')->name('coinpayments_fiat');
    Route::post('coingate', 'coingate\ProcessController@ipn')->name('coingate');
    Route::post('coinbase_commerce', 'coinbase_commerce\ProcessController@ipn')->name('coinbase_commerce');
    Route::get('mollie', 'mollie\ProcessController@ipn')->name('mollie');
    Route::post('cashmaal', 'cashmaal\ProcessController@ipn')->name('cashmaal');
});

// User Support Ticket
Route::prefix('ticket')->group(function () {
    Route::get('/', 'TicketController@supportTicket')->name('ticket');
    Route::get('/new', 'TicketController@openSupportTicket')->name('ticket.open');
    Route::post('/create', 'TicketController@storeSupportTicket')->name('ticket.store');
    Route::get('/view/{ticket}', 'TicketController@viewTicket')->name('ticket.view');
    Route::post('/reply/{ticket}', 'TicketController@replyTicket')->name('ticket.reply');
    Route::get('/download/{ticket}', 'TicketController@ticketDownload')->name('ticket.download');
});


/*
|--------------------------------------------------------------------------
| Start Admin Area
|--------------------------------------------------------------------------
*/

Route::namespace('Admin')->prefix('admin')->name('admin.')->group(function () {
    Route::namespace('Auth')->group(function () {
        Route::get('/', 'LoginController@showLoginForm')->name('login');
        Route::post('/', 'LoginController@login')->name('login');
        Route::get('logout', 'LoginController@logout')->name('logout');
        // Admin Password Reset
        Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.reset');
        Route::post('password/reset', 'ForgotPasswordController@sendResetLinkEmail');
        Route::post('password/verify-code', 'ForgotPasswordController@verifyCode')->name('password.verify-code');
        Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.change-link');
        Route::post('password/reset/change', 'ResetPasswordController@reset')->name('password.change');
    });

    Route::middleware('admin')->group(function () {

        Route::get('new-dashboard', [AdminDashboardController::class,'index'])->name('dashboard.new');
        Route::get('dashboard', 'AdminController@dashboard')->name('dashboard');
        Route::get('profile', 'AdminController@profile')->name('profile');
        Route::post('profile', 'AdminController@profileUpdate')->name('profile.update');
        Route::get('password', 'AdminController@password')->name('password');
        Route::post('password', 'AdminController@passwordUpdate')->name('password.update');


        //Manage Survey
        Route::get('survey/category/all', 'SurveyController@allCategory')->name('survey.category.all');
        Route::post('survey/category/add', 'SurveyController@addCategory')->name('survey.category.add');
        Route::post('survey/category/update', 'SurveyController@updateCategory')->name('survey.category.update');

        Route::get('survey/report', 'SurveyController@report')->name('survey.report');
        Route::get('survey/report/{id}', 'SurveyController@reportQuestion')->name('survey.report.question');
        Route::get('survey/report/download/{id}', 'SurveyController@reportDownload')->name('survey.report.download');

        Route::get('survey/all', 'SurveyController@allSurvey')->name('survey.all');
        Route::get('survey/new', 'SurveyController@newSurvey')->name('survey.new');
        Route::post('survey/add', 'SurveyController@addSurvey')->name('survey.add');
        Route::get('survey/edit/{id}', 'SurveyController@editSurvey')->name('survey.edit');
        Route::post('survey/update', 'SurveyController@updateSurvey')->name('survey.update');

        Route::get('survey/all/question/{id}', 'SurveyController@allQuestion')->name('survey.all.question');
        Route::get('survey/new/question/{id}', 'SurveyController@newQuestion')->name('survey.new.question');
        Route::post('survey/question/add', 'SurveyController@addQuestion')->name('survey.add.question');
        Route::get('survey/question/edit/{question_id}/{survey_id}', 'SurveyController@editQuestion')->name('survey.question.edit');
        Route::post('survey/question/update', 'SurveyController@updateQuestion')->name('survey.question.update');

        Route::get('notification/read/{id}','AdminController@notificationRead')->name('notification.read');
        Route::get('notifications','AdminController@notifications')->name('notifications');

        // Users Manager
        Route::get('users', 'ManageUsersController@allUsers')->name('users.all');
        Route::get('users_export', 'ManageUsersController@exportAllUsers')->name('users.export.all');
        Route::get('users/active', 'ManageUsersController@activeUsers')->name('users.active');
        Route::get('users/banned', 'ManageUsersController@bannedUsers')->name('users.banned');
        Route::get('users/data/reject', 'ManageUsersController@rejectDataUsers')->name('users.datareject');
        Route::get('users/data/verified', 'ManageUsersController@verifiedDataUsers')->name('users.dataverified');
        Route::get('users/data/waiting-for-verification', 'ManageUsersController@verificationDataUsers')->name('users.dataverification');
        Route::get('users/email-verified', 'ManageUsersController@emailVerifiedUsers')->name('users.emailVerified');
        Route::get('users/email-unverified', 'ManageUsersController@emailUnverifiedUsers')->name('users.emailUnverified');
        Route::get('users/sms-unverified', 'ManageUsersController@smsUnverifiedUsers')->name('users.smsUnverified');
        Route::get('users/sms-verified', 'ManageUsersController@smsVerifiedUsers')->name('users.smsVerified');
        Route::get('user/login/{id}', 'ManageUsersController@login')->name('users.login');
        Route::post('user/verify/{id}', 'ManageUsersController@verify')->name('users.verify');
        Route::post('user/reject/{id}', 'ManageUsersController@reject')->name('users.reject');

        Route::get('users/balance','ManageUsersController@balance')->name('users.balance');

        Route::get('users/{scope}/search', 'ManageUsersController@search')->name('users.search');
        Route::get('users/{scope}/dateSearch', 'ManageUsersController@dateSearch')->name('users.dateSearch');

        Route::get('user/detail/{id}', 'ManageUsersController@detail')->name('users.detail');
        Route::get('user/detail-rank/{id}', 'ManageUsersController@updateRank')->name('users.detail.rank');
        Route::get('user/detail-rank-founder/{id}', 'ManageUsersController@updateRankFounder')->name('users.detail.rank.founder');
        Route::get('user/detailbalance/{id}', 'ManageUsersController@BalanceLog')->name('users.detailbalance');
        Route::post('user/detail/find', 'ManageUsersController@detailFind')->name('users.detail.find');
        Route::get('user/gold_invest', 'ManageUsersController@userGold')->name('invest.gdetail');
        Route::get('user/gold_invest_export', 'ManageUsersController@exportUserGold')->name('invest.gdetail.export');
        Route::get('user/gold_invest_detail/{id}', 'ManageUsersController@goldDetail')->name('users.invest.detail');
        Route::get('user/gold-reward', 'ManageUsersController@userGoldReward')->name('users.reward.gold');
        Route::get('user/{user}/add-gold-reward', 'ManageUsersController@addUserGoldReward')->name('users.reward.add-gold');
        Route::post('user/{user}/add-gold-reward', 'ManageUsersController@storeUserGoldReward')->name('users.reward.store-weekly-gold');
        Route::get('user/referral/{id}', 'ManageUsersController@userRef')->name('users.ref');
        Route::post('user/update/{id}', 'ManageUsersController@update')->name('users.update');
        Route::post('user/rek/{id}', 'ManageUsersController@rek')->name('users.rek');
        Route::post('user/add-sub-pin/{id}', 'ManageUsersController@addSubPin')->name('users.addSubPin');
        Route::post('user/add-sub-point/{id}', 'ManageUsersController@addSubPoint')->name('users.addSubPoint');
        Route::post('user/add-sub-balance/{id}', 'ManageUsersController@addSubBalance')->name('users.addSubBalance');
        Route::post('user/add-sub-b-balance/{id}', 'ManageUsersController@addSubBBalance')->name('users.addSubBBalance');
        Route::post('user/set-user-placement/{id}', 'ManageUsersController@setUserPlacement')->name('users.setUserPlacement');
        Route::post('user/update_counting/{id}', 'ManageUsersController@updateCounting')->name('users.updateCounting');
        Route::get('user/send-email/{id}', 'ManageUsersController@showEmailSingleForm')->name('users.email.single');
        Route::post('user/send-email/{id}', 'ManageUsersController@sendEmailSingle')->name('users.email.single');
        Route::get('user/transactions/{id}', 'ManageUsersController@transactions')->name('users.transactions');
        Route::get('user/deposits/{id}', 'ManageUsersController@deposits')->name('users.deposits');
        Route::get('user/deposits/via/{method}/{type?}/{userId}', 'ManageUsersController@depViaMethod')->name('users.deposits.method');
        Route::get('user/withdrawals/{id}', 'ManageUsersController@withdrawals')->name('users.withdrawals');
        Route::get('user/withdrawals/via/{method}/{type?}/{userId}', 'ManageUsersController@withdrawalsViaMethod')->name('users.withdrawals.method');
        // Login History
        Route::get('users/login/history/{id}', 'ManageUsersController@userLoginHistory')->name('users.login.history.single');

        Route::get('users/send-email', 'ManageUsersController@showEmailAllForm')->name('users.email.all');
        Route::post('users/send-email', 'ManageUsersController@sendEmailAll')->name('users.email.send');

        Route::get('user/completed/survey/{id}', 'ManageUsersController@survey')->name('users.survey');


        Route::get('user/custom_order', 'CorderController@adminIndex')->name('custom.order');
        Route::get('user/custom_order/details/{id}', 'CorderController@detail')->name('corder.details');
        Route::post('user/custom_`order/approve', 'CorderController@app')->name('corder.approve');
        Route::post('user/custom_order/reject', 'CorderController@rej')->name('corder.reject');
        Route::post('user/custom_order/update', 'CorderController@upd')->name('corder.update');

        // mlm plan
        Route::get('plans', 'MlmController@plan')->name('plan');
        Route::post('plan/store', 'MlmController@planStore')->name('plan.store');

        Route::post('plan/update', 'MlmController@planUpdate')->name('plan.update');

        // mlm plan
        Route::get('product', 'ProductController@products')->name('product.index');
        Route::post('products/store', 'ProductController@productsStore')->name('products.store');
        Route::post('products/update', 'ProductController@productsUpdate')->name('products.update');
        Route::get('product-order', 'ProductController@order')->name('product.order');
        Route::post('product-order', 'ProductController@orderUp')->name('product.order.up');

        // admin product
        Route::get('admin-reward', 'AdminController@AdminReward')->name('adminReward');
        Route::post('admin-reward', 'AdminController@AdminRewardStore')->name('adminReward.store');


        // matching bonus
        Route::post('matching-bonus/update', 'MlmController@matchingUpdate')->name('matching-bonus.update');

        // tree
        Route::get('/tree/{id}', 'ManageUsersController@tree')->name('users.single.tree');
        Route::get('/user/tree/{user}', 'ManageUsersController@otherTree')->name('users.other.tree');
        Route::get('/user/tree/search', 'ManageUsersController@otherTree')->name('users.other.tree.search');

        Route::get('notice', 'GeneralSettingController@noticeIndex')->name('setting.notice');
        Route::post('notice/update', 'GeneralSettingController@noticeUpdate')->name('setting.notice.update');


        // bonus reward
        // Route::resource('bonus-reward', 'BonusRewardController');
        Route::get('bonus-reward','BonusRewardController@index')->name('reward.allReward');

        Route::get('phone-reward','BonusRewardController@phone')->name('phoneReward');
        Route::get('thai-reward','BonusRewardController@thai')->name('thaiReward');
        Route::get('turkie-reward','BonusRewardController@turkie')->name('turkieReward');

        Route::get('bonus-reward-check','BonusRewardController@userReport')->name('reward.checkTree');
        Route::get('bonus-reward-export','BonusRewardController@goldUserExport')->name('reward.goldExport');
        Route::post('bonus-reward','BonusRewardController@store')->name('reward.Store');
        Route::post('bonus-monthly','BonusRewardController@monthly')->name('reward.monthly');
        Route::get('bonus-reward/{id}','BonusRewardController@store')->name('reward.getId');
        Route::post('bonus-reward/update', 'BonusRewardController@upreward')->name('reward.update');
        Route::get('user-reward','BonusRewardController@UserBonus')->name('reward.userBonus');
        Route::post('user-reward-update','BonusRewardController@UserUpdate')->name('reward.userUpdate');
        Route::get('sharing-provit','BonusRewardController@SharingProvit')->name('reward.provitSharing');
        Route::post('sharing-provit','BonusRewardController@SharingProvitPost')->name('reward.provitSharingPost');

        Route::get('all-member-grow','BonusRewardController@memberGrow')->name('reward.memberGrow');
    

        // Subscriber
        Route::get('subscriber', 'SubscriberController@index')->name('subscriber.index');
        Route::get('subscriber/send-email', 'SubscriberController@sendEmailForm')->name('subscriber.sendEmail');
        Route::post('subscriber/remove', 'SubscriberController@remove')->name('subscriber.remove');
        Route::post('subscriber/send-email', 'SubscriberController@sendEmail')->name('subscriber.sendEmail');


        // Deposit Gateway
        Route::name('gateway.')->prefix('gateway')->group(function(){
            // Automatic Gateway
            Route::get('automatic', 'GatewayController@index')->name('automatic.index');
            Route::get('automatic/edit/{alias}', 'GatewayController@edit')->name('automatic.edit');
            Route::post('automatic/update/{code}', 'GatewayController@update')->name('automatic.update');
            Route::post('automatic/remove/{code}', 'GatewayController@remove')->name('automatic.remove');
            Route::post('automatic/activate', 'GatewayController@activate')->name('automatic.activate');
            Route::post('automatic/deactivate', 'GatewayController@deactivate')->name('automatic.deactivate');

            // Manual Methods
            Route::get('manual', 'ManualGatewayController@index')->name('manual.index');
            Route::get('manual/new', 'ManualGatewayController@create')->name('manual.create');
            Route::post('manual/new', 'ManualGatewayController@store')->name('manual.store');
            Route::get('manual/edit/{alias}', 'ManualGatewayController@edit')->name('manual.edit');
            Route::post('manual/update/{id}', 'ManualGatewayController@update')->name('manual.update');
            Route::post('manual/activate', 'ManualGatewayController@activate')->name('manual.activate');
            Route::post('manual/deactivate', 'ManualGatewayController@deactivate')->name('manual.deactivate');
        });


        // DEPOSIT SYSTEM
        Route::name('deposit.')->prefix('deposit')->group(function(){
            Route::get('/', 'DepositController@deposit')->name('list');
            Route::get('pending', 'DepositController@pending')->name('pending');
            Route::get('rejected', 'DepositController@rejected')->name('rejected');
            Route::get('approved', 'DepositController@approved')->name('approved');
            Route::get('successful', 'DepositController@successful')->name('successful');
            Route::get('details/{id}', 'DepositController@details')->name('details');

            Route::post('reject', 'DepositController@reject')->name('reject');
            Route::post('approve', 'DepositController@approve')->name('approve');
            Route::get('via/{method}/{type?}', 'DepositController@depViaMethod')->name('method');
            Route::get('/{scope}/search', 'DepositController@search')->name('search');
            Route::get('date-search/{scope}', 'DepositController@dateSearch')->name('dateSearch');

            Route::get('export', 'DepositController@export')->name('export');

        });


        // WITHDRAW SYSTEM
        Route::name('withdraw.')->prefix('withdraw')->group(function(){
            Route::get('pending', 'WithdrawalController@pending')->name('pending');
            Route::get('approved', 'WithdrawalController@approved')->name('approved');
            Route::get('rejected', 'WithdrawalController@rejected')->name('rejected');
            Route::get('log', 'WithdrawalController@log')->name('log');
            Route::get('via/{method_id}/{type?}', 'WithdrawalController@logViaMethod')->name('method');
            Route::get('{scope}/search', 'WithdrawalController@search')->name('search');
            Route::get('date-search/{scope}', 'WithdrawalController@dateSearch')->name('dateSearch');
            Route::get('details/{id}', 'WithdrawalController@details')->name('details');
            Route::post('approve', 'WithdrawalController@approve')->name('approve');
            Route::post('reject', 'WithdrawalController@reject')->name('reject');
            
            Route::get('export','WithdrawalController@export')->name('export');

            // Withdraw Method
            Route::get('method/', 'WithdrawMethodController@methods')->name('method.index');
            Route::get('method/create', 'WithdrawMethodController@create')->name('method.create');
            Route::post('method/create', 'WithdrawMethodController@store')->name('method.store');
            Route::get('method/edit/{id}', 'WithdrawMethodController@edit')->name('method.edit');
            Route::post('method/edit/{id}', 'WithdrawMethodController@update')->name('method.update');
            Route::post('method/activate', 'WithdrawMethodController@activate')->name('method.activate');
            Route::post('method/deactivate', 'WithdrawMethodController@deactivate')->name('method.deactivate');
        });

        Route::name('pin.')->prefix('pin')->group(function(){
            Route::get('/pin-delivered','ReportController@adminPinDeliver')->name('delivered');
            Route::get('/pin-all','ReportController@pinAll')->name('all');
        });
        Route::name('point.')->prefix('point')->group(function(){
            Route::get('/point-delivered','ReportController@SystemPointDeliver')->name('delivered');
            Route::get('/point-all','ReportController@pointAll')->name('all');
        });

        Route::get('log/invest', 'ReportController@invest')->name('log.invest');
        Route::get('log/transaction', 'ReportController@transaction')->name('log.transaction');
        Route::get('log/transaction/search', 'ReportController@transactionSearch')->name('log.transaction.search');
        Route::get('log/login/history', 'ReportController@loginHistory')->name('log.login.history');
        Route::get('log/login/ipHistory/{ip}', 'ReportController@loginIpHistory')->name('log.login.ipHistory');

        // Report
        Route::get('report/referral-commission', 'ReportController@refCom')->name('report.refCom');
        Route::get('report/binary-commission', 'ReportController@binary')->name('report.binaryCom');
        Route::get('report/founder-commission', 'ReportController@founder')->name('report.founder');
        Route::get('report/leadership-commission', 'ReportController@leadership')->name('report.leadership');
        Route::get('report/all-payout','ReportController@allPayout')->name('report.allPayout');

        Route::get('report/bv-log', 'ReportController@bvLog')->name('report.bvLog');
        Route::get('report/bv-log/{id}', 'ReportController@singleBvLog')->name('report.single.bvLog');
        Route::get('report/transaction/search', 'ReportController@transactionSearch')->name('report.transaction.search');


       

        
        Route::get('report/export', 'ReportController@export')->name('report.export');

        Route::get('report/user','ReportController@exportUser')->name('report.user');


        // Admin Support
        Route::get('tickets', 'SupportTicketController@tickets')->name('ticket');
        Route::get('tickets/pending', 'SupportTicketController@pendingTicket')->name('ticket.pending');
        Route::get('tickets/closed', 'SupportTicketController@closedTicket')->name('ticket.closed');
        Route::get('tickets/answered', 'SupportTicketController@answeredTicket')->name('ticket.answered');
        Route::get('tickets/view/{id}', 'SupportTicketController@ticketReply')->name('ticket.view');
        Route::post('ticket/reply/{id}', 'SupportTicketController@ticketReplySend')->name('ticket.reply');
        Route::get('ticket/download/{ticket}', 'SupportTicketController@ticketDownload')->name('ticket.download');
        Route::post('ticket/delete', 'SupportTicketController@ticketDelete')->name('ticket.delete');


        // Language Manager
        Route::get('/language', 'LanguageController@langManage')->name('language.manage');
        Route::post('/language', 'LanguageController@langStore')->name('language.manage.store');
        Route::post('/language/delete/{id}', 'LanguageController@langDel')->name('language.manage.del');
        Route::post('/language/update/{id}', 'LanguageController@langUpdatepp')->name('language.manage.update');
        Route::get('/language/edit/{id}', 'LanguageController@langEdit')->name('language.key');
        Route::post('/language/import', 'LanguageController@langImport')->name('language.import_lang');



        Route::post('language/store/key/{id}', 'LanguageController@storeLanguageJson')->name('language.store.key');
        Route::post('language/delete/key/{id}', 'LanguageController@deleteLanguageJson')->name('language.delete.key');
        Route::post('language/update/key/{id}', 'LanguageController@updateLanguageJson')->name('language.update.key');



        // General Setting
        Route::get('general-setting', 'GeneralSettingController@index')->name('setting.index');
        Route::post('general-setting', 'GeneralSettingController@update')->name('setting.update');
       
        // Logo-Icon
        Route::get('setting/logo-icon', 'GeneralSettingController@logoIcon')->name('setting.logo_icon');
        Route::post('setting/logo-icon', 'GeneralSettingController@logoIconUpdate')->name('setting.logo_icon');

        // Plugin
        Route::get('extensions', 'ExtensionController@index')->name('extensions.index');
        Route::post('extensions/update/{id}', 'ExtensionController@update')->name('extensions.update');
        Route::post('extensions/activate', 'ExtensionController@activate')->name('extensions.activate');
        Route::post('extensions/deactivate', 'ExtensionController@deactivate')->name('extensions.deactivate');


        // Email Setting
        Route::get('email-template/global', 'EmailTemplateController@emailTemplate')->name('email.template.global');
        Route::post('email-template/global', 'EmailTemplateController@emailTemplateUpdate')->name('email.template.global');
        Route::get('email-template/setting', 'EmailTemplateController@emailSetting')->name('email.template.setting');
        Route::post('email-template/setting', 'EmailTemplateController@emailSettingUpdate')->name('email.template.setting');
        Route::get('email-template/index', 'EmailTemplateController@index')->name('email.template.index');
        Route::get('email-template/{id}/edit', 'EmailTemplateController@edit')->name('email.template.edit');
        Route::post('email-template/{id}/update', 'EmailTemplateController@update')->name('email.template.update');
        Route::post('email-template/send-test-mail', 'EmailTemplateController@sendTestMail')->name('email.template.sendTestMail');


        // SMS Setting
        Route::get('sms-template/global', 'SmsTemplateController@smsSetting')->name('sms.template.global');
        Route::post('sms-template/global', 'SmsTemplateController@smsSettingUpdate')->name('sms.template.global');
        Route::get('sms-template/index', 'SmsTemplateController@index')->name('sms.template.index');
        Route::get('sms-template/edit/{id}', 'SmsTemplateController@edit')->name('sms.template.edit');
        Route::post('sms-template/update/{id}', 'SmsTemplateController@update')->name('sms.template.update');
        Route::post('email-template/send-test-sms', 'SmsTemplateController@sendTestSMS')->name('sms.template.sendTestSMS');

        // Exchange
        Route::get('exchange', 'ExchangeController@index')->name('exchange');
        Route::get('gold-check', 'ExchangeController@goldQwa')->name('exchange');
        Route::post('exchange/reject/{id}', 'ExchangeController@reject')->name('exchange.reject');
        Route::post('exchange/accept/{id}', 'ExchangeController@verify')->name('exchange.accept');

        Route::get('delivery', 'DeliveryController@index')->name('delivery');
        Route::post('delivery/deliver', 'DeliveryController@delivery')->name('deliver.deliver');
        Route::get('BroDelivery', 'BrodevController@index')->name('BroDelivery');
        Route::post('BroDelivery/deliver', 'BrodevController@delivery')->name('BroDeliver.deliver');

        // SEO
        Route::get('seo', 'FrontendController@seoEdit')->name('seo');


        // Frontend
        Route::name('frontend.')->prefix('frontend')->group(function () {

            Route::get('templates', 'FrontendController@templates')->name('templates');
            Route::post('templates', 'FrontendController@templatesActive')->name('templates.active');

            Route::get('frontend-sections/{key}', 'FrontendController@frontendSections')->name('sections');
            Route::post('frontend-content/{key}', 'FrontendController@frontendContent')->name('sections.content');
            Route::get('frontend-element/{key}/{id?}', 'FrontendController@frontendElement')->name('sections.element');
            Route::post('remove', 'FrontendController@remove')->name('remove');

            // Page Builder
            Route::get('manage-pages', 'PageBuilderController@managePages')->name('manage.pages');
            Route::post('manage-pages', 'PageBuilderController@managePagesSave')->name('manage.pages.save');
            Route::post('manage-pages/update', 'PageBuilderController@managePagesUpdate')->name('manage.pages.update');
            Route::post('manage-pages/delete', 'PageBuilderController@managePagesDelete')->name('manage.pages.delete');
            Route::get('manage-section/{id}', 'PageBuilderController@manageSection')->name('manage.section');
            Route::post('manage-section/{id}', 'PageBuilderController@manageSectionUpdate')->name('manage.section.update');
        });
    });
});




/*
|--------------------------------------------------------------------------
| Start User Area
|--------------------------------------------------------------------------
*/
Route::get('provinces', [UserController::class,'provinces'])->name('provinces');
Route::get('cities', [UserController::class,'cities'])->name('cities');
Route::get('districts', [UserController::class,'districts'])->name('districts');
Route::get('villages', [UserController::class,'villages'])->name('villages');

Route::name('user.')->group(function () {
    Route::get('/login', [LoginController::class,'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class,'login']);
    Route::get('logout', [LoginController::class,'logout'])->name('logout');

    Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
    Route::post('register', [RegisterController::class,'register'])->middleware('regStatus');


    Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('password/code-verify', 'Auth\ForgotPasswordController@codeVerify')->name('password.code_verify');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');
    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('password/verify-code', 'Auth\ForgotPasswordController@verifyCode')->name('password.verify-code');

   
});


Route::name('user.')->prefix('user')->group(function () {
    Route::middleware('auth')->group(function () {
        
        Route::get('authorization', 'AuthorizationController@authorizeForm')->name('authorization');
        Route::get('resend-verify', 'AuthorizationController@sendVerifyCode')->name('send_verify_code');
        Route::post('verify-email', 'AuthorizationController@emailVerification')->name('verify_email');
        Route::post('verify-sms', 'AuthorizationController@smsVerification')->name('verify_sms');
        Route::post('verify-g2fa', 'AuthorizationController@g2faVerification')->name('go2fa.verify');

        Route::get('update-stockiest/{id}','UserController@updateStockiest')->name('user.update_stockiest');

        Route::middleware(['checkStatus'])->group(function () {
            Route::post('register-ayam','UserController@ayamkuRegister')->name('register.ayamku');
            Route::get('login-ayam-auth','UserController@ayamkuLoginAuth')->name('login.ayamku');

            Route::get('dashboard', 'UserController@home')->name('home');
            Route::get('referals', 'UserController@ref')->name('my.referral');

            Route::post('daily-checkin-new', 'UserController@newDailyCheckIn')->name('new-daily-checkin');
            Route::post('daily-checkin', 'UserController@dailyCheckIn')->name('daily-checkin');
            Route::post('weekly-checkin', 'UserController@weeklyCheckIn')->name('weekly-checkin');
            Route::post('clam-bonus-reward', 'UserController@claimBonusReward')->name('claim-reward');
            Route::post('serial-number', 'UserController@serialNum')->name('serialnum');

            Route::get('profile-setting', 'UserController@profile')->name('profile-setting');
            Route::post('profile-setting', 'UserController@submitProfile');
            Route::post('edit_rekening', 'UserController@edit_rekening')->name('edit_rekening');
            Route::post('add_rekening', 'UserController@add_rekening')->name('add_rekening');
            Route::get('change-password', 'UserController@changePassword')->name('change-password');
            Route::post('change-password', 'UserController@submitPassword');

            Route::post('add_address', 'AlamatController@add_address')->name('add_address');
            Route::post('edit_address', 'AlamatController@edit_address')->name('edit_address');
            

            Route::get('reward', 'UrewardController@userReward')->name('reward');
            Route::get('reward/print/{id}', 'UrewardController@printTicket')->name('ticket.print');
            //Claimreward
            Route::post('claim_reward/{id}', 'UrewardController@claimReward')->name('claim.reward');


            //Convert
            Route::post('convert-bwallet',[UserController::class,'convertBwallet'])->name('convert.bwallet');

            //2FA
            Route::get('twofactor', 'UserController@show2faForm')->name('twofactor');
            Route::post('twofactor/enable', 'UserController@create2fa')->name('twofactor.enable');
            Route::post('twofactor/disable', 'UserController@disable2fa')->name('twofactor.disable');
            Route::get('login/history', 'UserController@userLoginHistory')->name('login.history');

            //F
            Route::get('/plan', 'PlanController@planIndex')->name('plan.index');
            Route::get('/plan/ro', 'PlanController@repeatOrder')->name('plan.ro');
            Route::post('/plan', 'PlanController@planStore')->name('plan.purchase');
            Route::post('/plan/ro', 'PlanController@planRoStore')->name('plan.purchase.ro');
            Route::get('/archivement',[ArchivementController::class,'index'])->name('archivement.view');
            Route::get('/tarik-emas',[ArchivementController::class,'tarikEmas'])->name('archivement.terikEmas');
            Route::post('/tarik-emas',[ArchivementController::class,'tarikEmasPost'])->name('terikEmas.post');

            Route::get('/user-pin',[SponsorRegisterController::class,'userSendPin'])->name('pins.view');
            Route::get('/user-point',[SponsorRegisterController::class,'userPoint'])->name('point.view');
            Route::get('pin/log', 'UserReportController@PinDeliveriyLog')->name('pins.PinDeliveriyLog');
            Route::get('/user-order',[SponsorRegisterController::class,'userOrderPin'])->name('pins.order');
            Route::post('/user-order',[PaymentController::class,'payment'])->name('pins.order.post');
            Route::put('/user-order/{id}',[PaymentController::class,'userOrderUpdate'])->name('pins.order.update');

            Route::get('/Product', 'ProductController@productIndex')->name('product.index');
            Route::post('/Product-purchase', 'ProductController@productPurchase')->name('product.purchase')->middleware('addressCheck');
            Route::post('/Product-cart', 'ProductController@productCart')->name('product.cart');
            Route::post('/Product-cart/{id}', 'ProductController@productCartUpdate')->name('product.cart.update');
            Route::post('/Product-cart-del/{id}', 'ProductController@productCartDelete')->name('product.cart.delete');
            Route::get('/product-invoice', 'ProductController@productInvoice')->name('product.inv');
            Route::get('/invoice-details/{inv}', 'ProductController@inv')->name('product.inv.details');
            Route::get('/Product-tracking', 'ProductController@productTracking')->name('product.tracking');
            Route::put('/Product-tracking/{id}', 'ProductController@productTrackingUp')->name('product.tracking.up');
            Route::get('point/log', 'ProductController@PointDeliveriyLog')->name('product.point');
            
            Route::get('/referral-log', 'UserController@referralCom')->name('referral.log');

            // Route::get('/binary-log', 'PlanController@binaryCom')->name('binary.log');
            Route::get('/binary-summery', 'PlanController@binarySummery')->name('binary.summery');
            Route::get('/bv-log', 'PlanController@bvlog')->name('bv.log');

            Route::get('/referrals', 'PlanController@myRefLog')->name('my.ref');

            Route::get('/tree', 'PlanController@myTree')->name('my.tree');
            Route::get('/all-users', 'UserController@allInUsers')->name('all.users');
            Route::get('/tree/{user}', 'PlanController@otherTree')->name('other.tree');
            Route::get('/tree-other', 'PlanController@seeTree')->name('see.tree');
            Route::get('/tree/search', 'PlanController@otherTree')->name('other.tree.search');

            Route::get('/register-by-sponsor',[SponsorRegisterController::class,'index'])->name('sponsor.regist')->middleware('checkPlacement');
            Route::get('/register-by-sponsor-up',[SponsorRegisterController::class,'sponsorSet'])->name('sponsor.regist.up')->middleware('checkPlacement');

            Route::post('/register-by-sponsor-set',[SponsorRegisterController::class,'setSession'])->name('sponsor.set');
            Route::post('/register-by-sponsor-set-update',[SponsorRegisterController::class,'setSessionUpdate'])->name('sponsor.set.update');
            Route::post('/register-sponsor',[SponsorRegisterController::class,'registerUser'])->name('sponsorRegist.post');
            Route::post('/register-sponsor-update',[NewSponsorRegitserController::class,'register'])->name('sponsorRegist.post.up');
            
            //balance transfer
            Route::get('/transfer', 'UserController@indexTransfer')->name('balance.transfer');
            Route::post('/transfer', 'UserController@balanceTransfer')->name('balance.transfer.post');
            Route::post('/search-user', 'UserController@searchUser')->name('search.user');
            
            Route::post('add-sub-balance/{id}', 'UserController@addSubBalance')->name('addSubBalance');
            Route::post('send-pin/{id}', [SponsorRegisterController::class,'sendPin'])->name('addPinUser');
            


            //Report
            Route::get('report/deposit/log', 'UserReportController@depositHistory')->name('report.deposit');
            // Route::get('report/invest/log', 'UserReportController@investLog')->name('report.invest');
            // Route::get('report/transactions/log', 'UserReportController@transactions')->name('report.transactions');
            // Route::get('report/withdraw/log', 'UserReportController@withdrawLog')->name('report.withdraw');
            // Route::get('report/referral/commission', 'UserReportController@refCom')->name('report.refCom');
            // Route::get('report/binary/commission', 'UserReportController@binaryCom')->name('report.binaryCom');

            // Deposit
            Route::any('deposit', 'Gateway\PaymentController@deposit')->name('deposit');
            Route::post('deposit/insert', 'Gateway\PaymentController@depositInsert')->name('deposit.insert');
            Route::get('deposit/preview', 'Gateway\PaymentController@depositPreview')->name('deposit.preview');
            Route::get('deposit/confirm', 'Gateway\PaymentController@depositConfirm')->name('deposit.confirm');
            Route::get('deposit/manual', 'Gateway\PaymentController@manualDepositConfirm')->name('deposit.manual.confirm');
            Route::post('deposit/manual', 'Gateway\PaymentController@manualDepositUpdate')->name('deposit.manual.update');
            Route::get('deposit/history', 'UserController@depositHistory')->name('deposit.history');
            // Route::get('thank-you', 'Gateway\PaymentController@thankyou')->name('deposit.manual.thankyou');
            // Route::get('cancel-payment', 'Gateway\PaymentController@cancelpayment')->name('deposit.manual.cancel');
            // Route::get('callback-url', 'Gateway\PaymentController@callback')->name('deposit.manual.callback');

            // Withdraw
            // Route::get('withdraw', 'UserController@withdrawMoney')->name('withdraw');
            Route::post('withdraw', 'UserController@withdrawStore')->name('withdraw.money');
            Route::get('withdraw/preview', 'UserController@withdrawPreview')->name('withdraw.preview');
            Route::post('withdraw/preview', 'UserController@withdrawSubmit')->name('withdraw.submit');
            Route::get('withdraw/history', 'UserController@withdrawLog')->name('withdraw.history');

            //Survey
            Route::get('/survey', 'UserController@surveyAvailable')->name('survey');
            Route::get('/survey/{id}/questions', 'UserController@surveyQuestions')->name('survey.questions');
            Route::post('/survey/answers', 'UserController@surveyQuestionsAnswers')->name('survey.questions.answers');

            Route::get('gold_invest', 'UserController@goldInvest')->name('gold.invest');
            Route::post('/Product-custom', 'CorderController@productCustom')->name('product.custom');
            Route::post('/Product-rcustom', 'CorderController@rproductCustom')->name('product.rcustom');

           
            Route::any('deposit', 'Gateway\PaymentController@deposit')->name('deposit');
            Route::get('report/deposit/log', 'UserReportController@depositHistory')->name('report.deposit');
            Route::get('report/gold/log', 'UserReportController@dailyGoldLog')->name('report.gold');
            Route::get('report/invest/log', 'UserReportController@investLog')->name('report.invest');
            Route::get('report/transactions/log', 'UserReportController@transactions')->name('report.transactions');
            Route::get('report/withdraw/log', 'UserReportController@withdrawLog')->name('report.withdraw');
            Route::get('report/referral/commission', 'UserReportController@refCom')->name('report.refCom');
            Route::get('report/binary/commission', 'UserReportController@binaryCom')->name('report.binaryCom');
            Route::get('report/leadership/commission', 'UserReportController@leadersComm')->name('report.leadersComm');
            Route::get('report/other-bonus', 'UserReportController@otherBonus')->name('report.other');

            Route::get('report/exchange/log', 'UserReportController@exchangeLog')->name('report.exchangeLog');
            Route::get('report/delivery/log', 'UserReportController@deliveryLog')->name('report.deliveryLog');
            Route::get('report/Brodelivery/log', 'UserReportController@BroDeliveryLog')->name('report.BroDeliveryLog');

          
            Route::post('/buymp', 'PlanController@buyMpStore')->name('plan.mppurchase');
            Route::post('/check-default', 'PlanController@changeDafault')->name('plan.changedef');
            Route::get('/manage-user', 'UserController@user_boom')->name('user_boom');
            Route::get('/tree', 'PlanController@myTree')->name('my.tree');

            Route::middleware(['checkKyc'])->group(function () {
                Route::get('withdraw', 'UserController@withdrawMoney')->name('withdraw');
                Route::post('gold/withdraw', 'UserController@withdrawGold')->name('withdraw.gold');

            });

            Route::post('convert-deopsit',[SponsorRegisterController::class,'convertSaldo'])->name('convert.saldo');

            Route::post('gold_exchange', 'UserController@goldExchange')->name('gold.exchange');
            Route::post('gold_delivery', 'SendgoldController@goldDelivery')->name('gold.delivery');

            Route::get('/cek_pos/{id}', 'UserController@cek_pos')->name('cek_pos');
            Route::get('/cek_tree/{id}', 'UserController@cek_tree')->name('cek_tree');
            Route::post('/user', 'UserController@user')->name('user');

            Route::get('/verification','UserController@verification')->name('verification');
            Route::post('/submitVerification','UserController@submitVerification')->name('submitVerification');
        });
    });
});

Route::post('/check/referral', 'SiteController@CheckUsername')->name('check.referral');
Route::post('/check/referralbro', 'SiteController@CheckBro')->name('check.referralbro');
Route::post('/get/user/position', 'SiteController@userPosition')->name('get.user.position');

Route::get('thank-you', 'Gateway\PaymentController@thankyou')->name('deposit.manual.thankyou');
Route::get('cancel-payment', 'Gateway\PaymentController@cancelpayment')->name('deposit.manual.cancel');
Route::post('callback-url', 'Gateway\PaymentController@callback')->name('deposit.manual.callback');

Route::post('subscriber', 'SiteController@subscriberStore')->name('subscriber.store');
// Policy Details
Route::get('policy/{slug}/{id}', 'SiteController@policyDetails')->name('policy.details');

Route::get('/contact', 'SiteController@contact')->name('contact');
Route::post('/contact', 'SiteController@contactSubmit')->name('contact.send');
Route::get('/change/{lang?}', 'SiteController@changeLanguage')->name('lang');

Route::get('/blog', 'SiteController@blog')->name('blog');
Route::get('/blog/details/{slug}/{id}', 'SiteController@singleBlog')->name('singleBlog');

Route::get('/home2', 'SiteController@index2')->name('home2');
// Route::get('/', 'SiteController@index')->name('home'); // Disabled temporary

Route::get('/{slug}', 'SiteController@pages')->name('pages');

Route::get('placeholder-image/{size}', 'SiteController@placeholderImage')->name('placeholderImage');
Route::get('links/{slug}', 'SiteController@links')->name('links');
Route::get('find-uname/{uname}',[SponsorRegisterController::class,'findUname']);
