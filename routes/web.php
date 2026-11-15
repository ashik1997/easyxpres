<?php

use App\Http\Controllers\Admin\AdminForgotPasswordController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AppSetupController;
use App\Http\Controllers\Backend\BankNameController;
use App\Http\Controllers\Backend\PackageController;
use App\Http\Controllers\Backend\SubPackageController;
use App\Http\Controllers\BalanceController;
use App\Http\Controllers\EpsPaymentController;
use App\Http\Controllers\Frontend\IbankingOrderController;
use App\Http\Controllers\MaleRechargeController;
use App\Http\Controllers\MobileBankingController;
use App\Http\Controllers\MobileBankingOrderController;
use App\Http\Controllers\CallingCardController;
use App\Http\Controllers\MaliOperatorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VpnController;
use App\Http\Controllers\VpnNameController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RechargeController;
use App\Http\Controllers\ShopAutoLoginController;



Route::get('/', function () {
    return view('auth.login');
});

// Route::get('/storage-link', function () {
//     $targetFolder = storage_path('app/public');
//     $linkFolder = $_SERVER['DOCUMENT_ROOT'] . '/storage';
//     symlink($targetFolder, $linkFolder);
// });

Route::get('/storage-link', function () {
    Artisan::call('storage:link');
    return 'Done';
})->middleware('auth');


Route::get('clear-cache', function () {
	Artisan::call('optimize:clear');
    return redirect('/');
});
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::post('/change-password', [ProfileController::class, 'changePassword'])
    ->name('password.change')
    ->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

Route::get('/pending-approval', function () {
    return view('auth.pending');
})->name('pending.approval');

Route::middleware(['auth', 'approved'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/shop/auto-login', ShopAutoLoginController::class)
        ->middleware('throttle:10,1')
        ->name('shop.auto-login');



    // Balance Add Routes
    Route::get('/balance/add', [App\Http\Controllers\BalanceController::class, 'showAddForm'])->name('balance.add.form');
    Route::post('/balance/add', [App\Http\Controllers\BalanceController::class, 'addBalance'])->name('balance.add');
    Route::post('/app/balance/add', [App\Http\Controllers\BalanceController::class, 'appAddBalance'])->name('app.balance.add');

    // iBanking Order Routes
    Route::get('/app/ibanking', [IbankingOrderController::class, 'showForm'])->name('app.ibanking.form');
    Route::post('/ibanking/add', [IbankingOrderController::class, 'addiBanking'])->name('ibanking.add');
    Route::post('/app/ibanking/add', [IbankingOrderController::class, 'appAddiBanking'])->name('app.ibanking.add');
    Route::get('/ibanking/history', [IbankingOrderController::class, 'iBankingHistory'])->name('ibanking.history');
    Route::get('/app/ibanking/history', [IbankingOrderController::class, 'appiBankingHistory'])->name('app.ibanking.history');
    Route::get(
        '/app/ibanking/history/{id}/attachment/{type}',
        [IbankingOrderController::class, 'viewUserAttachment']
    )->where('type', 'document|slip')->name('app.ibanking.attachment');
    Route::get(
        '/app/ibanking/history/{id}/attachment/{type}/download',
        [IbankingOrderController::class, 'downloadUserAttachment']
    )->where('type', 'document|slip')->name('app.ibanking.attachment.download');

    // package show sub-packages route
    Route::get('/packages/{package}/sub-packages', [SubPackageController::class, 'showSubPackages'])->name('package.show');
    Route::get('/app/packages/{package}/sub-packages', [SubPackageController::class, 'appShowSubPackages'])->name('app.package.show');

    Route::get('/app/package/history', [SubPackageController::class, 'appPackageHistory'])->name('app.package.history');

    // payment store route
    Route::post('/payment/store', [SubPackageController::class, 'payStore'])->name('payment.store');

    // Recharge Submit Route
    Route::get('/app/recharge', [RechargeController::class, 'showRechargeForm'])->name('app.recharge.form');
    Route::post('/recharge/submit', [RechargeController::class, 'submitRecharge'])->name('recharge.submit');
    Route::post('/app/recharge/submit', [RechargeController::class, 'appSubmitRecharge'])->name('app.recharge.submit');
    Route::get('/recharge/history', [RechargeController::class, 'rechargeHistory'])->name('recharge.history');
    Route::get('/app/recharge/history', [RechargeController::class, 'appRechargeHistory'])->name('app.recharge.history');

    // MaleRecharge Submit Route
    Route::get('/app/male/recharge', [MaleRechargeController::class, 'showRechargeForm'])->name('app.male.recharge.form');
    Route::post('/male/recharge/submit', [MaleRechargeController::class, 'submitRecharge'])->name('male.recharge.submit');
    Route::post('/app/male/recharge/submit', [MaleRechargeController::class, 'appSubmitRecharge'])->name('app.male.recharge.submit');
    Route::get('/male/recharge/history', [MaleRechargeController::class, 'rechargeHistory'])->name('male.recharge.history');
    Route::get('/app/male/recharge/history', [MaleRechargeController::class, 'appRechargeHistory'])->name('app.male.recharge.history');

    // packag history 
    Route::get('/package/history', [RechargeController::class, 'packageHistory'])->name('packages.history');
    Route::get('/app/recharge/package/history', [RechargeController::class, 'appRechargePackageHistory'])->name('app.recharge.packages.history');

    // Caling Card
    Route::get('/app/calling/card/list', [CallingCardController::class, 'appCallingCard'])->name('app.calling.card');
    Route::get('/web/calling/card/list', [CallingCardController::class, 'webCallingCard'])->name('web.calling.card');
    Route::get('/app/calling/card/my', [CallingCardController::class, 'myCallingCard'])->name('my.calling.card');
    Route::post('/calling-card/buy', [CallingCardController::class, 'buy'])->name('calling.card.buy');

    // VPN
    Route::get('/app/vpn/list', [VpnController::class, 'catalog'])->name('app.vpn');
    Route::get('/app/vpn/my', [VpnController::class, 'history'])->name('my.vpn');
    Route::post('/vpn/buy', [VpnController::class, 'buy'])->name('vpn.buy');

    // Mobile Banking Order history 
    Route::get('/mobile/banking/history', [MobileBankingController::class, 'mobileBankingHistory'])->name('mobile.banking.history');
    Route::get('/app/mobile/banking/view', [MobileBankingController::class, 'appMobileBankingView'])->name('app.mobile.banking.view');
    Route::get('/app/mobile/banking/history', [MobileBankingController::class, 'appMobileBankingHistory'])->name('app.mobile.banking.history');
    Route::post('/mobile/banking/store', [MobileBankingController::class, 'payStore'])->name('mobile.banking.store');

    // balance history 
    Route::get('/balance/history', [BalanceController::class, 'balanceHistory'])->name('balance.history');
    Route::get('/app/balance/history', [BalanceController::class, 'appBalanceHistory'])->name('app.balance.history');
    Route::get('/balance/current', [BalanceController::class, 'currentBalance'])->name('balance.current');

    // EPS automated add balance (the existing manual add balance flow remains unchanged)
    Route::get('/balance/eps', [EpsPaymentController::class, 'index'])->name('balance.payment.index');
    Route::post('/balance/eps/payments', [EpsPaymentController::class, 'create'])
        ->middleware('throttle:5,1')
        ->name('balance.payment.create');
    Route::post('/balance/eps/payments/{payment}/status', [EpsPaymentController::class, 'status'])
        ->middleware('throttle:10,1')
        ->name('balance.payment.status');
});

// EPS redirects are public because the customer returns from the hosted gateway.
// Every callback is correlated by a one-time secret and verified against EPS server-to-server.
Route::middleware('throttle:20,1')->group(function () {
    Route::get('/balance/eps/callback/{merchantReference}/{callbackToken}/success', [EpsPaymentController::class, 'success'])
        ->name('balance.payment.success');
    Route::get('/balance/eps/callback/{merchantReference}/{callbackToken}/fail', [EpsPaymentController::class, 'fail'])
        ->name('balance.payment.fail');
    Route::get('/balance/eps/callback/{merchantReference}/{callbackToken}/cancel', [EpsPaymentController::class, 'cancel'])
        ->name('balance.payment.cancel');
    Route::post('/balance/eps/ipn', [EpsPaymentController::class, 'ipn'])
        ->name('balance.payment.ipn');
});

Route::prefix('api/shop/wallet')->middleware('throttle:60,1')->group(function () {
    Route::post('/balance', [BalanceController::class, 'shopWalletBalance']);
    Route::post('/debit', [BalanceController::class, 'shopWalletDebit']);
    Route::post('/reverse', [BalanceController::class, 'shopWalletReverse']);
});

// Admin Auth Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Forget Password Routes
    Route::get('admin-forgot-password', [AdminForgotPasswordController::class, 'showForm'])->name('forgot');
    Route::post('admin-send-otp', [AdminForgotPasswordController::class, 'sendOtp'])->name('send.otp');
    Route::get('admin-verify-otp', [AdminForgotPasswordController::class, 'verifyForm'])->name('verify.form');
    Route::post('admin-verify-otp', [AdminForgotPasswordController::class, 'verifyOtp'])->name('verify.otp');
    Route::get('admin-reset-password', [AdminForgotPasswordController::class, 'resetForm'])->name('reset.form');
    Route::post('admin-reset-password', [AdminForgotPasswordController::class, 'resetPassword'])->name('reset.password');






    Route::middleware('admin.auth')->group(function () {
        Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');

        // admin.users.list route is already defined above
        Route::get('/users', [AdminUserController::class, 'userList'])->name('users.list');
        Route::post('/users/{id}/approve', [AdminUserController::class, 'approve'])->name('users.approve');
        Route::post('/users/{id}/reject', [AdminUserController::class, 'reject'])->name('users.reject');

        Route::post('/admin/users/balance/update', [AdminUserController::class, 'updateBalance'])
            ->name('users.balance.update');

        // Admin User Pasword Change Route
        Route::post('/admin/users/password/update/{id}', [AdminUserController::class, 'updatePassword'])
            ->name('users.password.update');

        // Pending Balance Route
        Route::get('/balance/pending', [App\Http\Controllers\BalanceController::class, 'pendingBalance'])->name('balance.pending');
        Route::get('/balance/history', [App\Http\Controllers\BalanceController::class, 'adminBalanceHistory'])->name('balance.history');
        Route::get('/balance/eps', [EpsPaymentController::class, 'adminIndex'])->name('balance.eps');
        // Approved Balance Route
        Route::get('/balance/approved/{id}', [App\Http\Controllers\BalanceController::class, 'approvedBalance'])->name('balance.approved');
        // Rejected Balance Route
        Route::get('/balance/rejected/{id}', [App\Http\Controllers\BalanceController::class, 'rejectedBalance'])->name('balance.rejected');



        Route::get('/download/{transaction}', [App\Http\Controllers\BalanceController::class, 'downloadFile'])->name('transaction.download');
        Route::post('/balance/status/{transaction}', [App\Http\Controllers\BalanceController::class, 'changeStatus'])->name('balance.changeStatus');

        // Mobile Banking Routes
        Route::get('/mobile/banking', [MobileBankingController::class, 'index'])->name('mobile.banking.list');
        Route::get('/mobile/banking/create', [MobileBankingController::class, 'create'])->name('mobile.banking.create');
        Route::post('/mobile/banking/packages', [MobileBankingController::class, 'store'])->name('mobile.banking.store');
        Route::get('/mobile/banking/{package}/edit', [MobileBankingController::class, 'edit'])->name('mobile.banking.edit');
        Route::put('/mobile/banking/{package}', [MobileBankingController::class, 'update'])->name('mobile.banking.update');
        Route::delete('/mobile/banking/{package}', [MobileBankingController::class, 'destroy'])->name('mobile.banking.destroy');
        Route::get(
            'admin/mobile/banking/status/{id}',
            [MobileBankingController::class, 'changeStatus']
        )->name('mobile.banking.changeStatus');

        // Bank Name Routes
        Route::get('/bank/name', [BankNameController::class, 'index'])->name('ibanking.list');
        Route::get('/bank/name/create', [BankNameController::class, 'create'])->name('ibanking.create');
        Route::post('/bank/name/store', [BankNameController::class, 'store'])->name('ibanking.store');
        Route::delete('/bank/name/{package}', [BankNameController::class, 'destroy'])->name('iBanking.destroy');
        Route::get('/bank/name/{package}/edit', [BankNameController::class, 'edit'])->name('ibanking.edit');
        Route::put('/bank/name/{package}', [BankNameController::class, 'update'])->name('ibanking.update');
        Route::get(
            'admin/bank/name/status/{id}',
            [BankNameController::class, 'changeStatus']
        )->name('iBanking.changeStatus');

        // iBanking Rate Routes
        Route::get('/ibanking/rate', [BankNameController::class, 'rate'])
            ->name('ibanking.rate');
        Route::post('/ibanking/rate/store', [BankNameController::class, 'updateRate'])
            ->name('ibanking.update');

        // iBanking Order Routes
        Route::get('/ibanking/orders/list', [IbankingOrderController::class, 'iBankingOrder'])->name('ibanking.orders.list');
        Route::get('/ibanking-orders/approve/{id}', [IbankingOrderController::class, 'approveOrder'])->name('ibanking_orders.approve');
        Route::get('/ibanking-orders/reject/{id}', [IbankingOrderController::class, 'rejectOrder'])->name('ibanking_orders.reject');
        Route::put(
            '/ibanking-orders/{id}/upload-slip',
            [IbankingOrderController::class, 'uploadSlip']
        )->name('ibanking_orders.upload_slip');
        Route::get(
            '/ibanking-orders/{id}/attachment/{type}',
            [IbankingOrderController::class, 'viewAttachment']
        )->where('type', 'document|slip')->name('ibanking_orders.attachment');
        Route::get(
            '/ibanking-orders/{id}/attachment/{type}/download',
            [IbankingOrderController::class, 'downloadAttachment']
        )->where('type', 'document|slip')->name('ibanking_orders.attachment.download');

        // Mobile Banking order Routes
        Route::get('/mobile/banking/orders/list', [MobileBankingOrderController::class, 'index'])->name('mobile.banking.orders.list');
        Route::get('/mobile/banking-orders/approve/{id}', [MobileBankingOrderController::class, 'approveOrder'])->name('mobile_banking_orders.approve');
        Route::get('/mobile/banking-orders/reject/{id}', [MobileBankingOrderController::class, 'rejectOrder'])->name('mobile_banking_orders.reject');
        Route::put(
            '/mobile-banking-orders/{id}/update-note',
            [MobileBankingOrderController::class, 'updateNote']
        )
            ->name('mobile_banking_orders.update_note');



        // Package Routes
        Route::get('/packages', [PackageController::class, 'index'])->name('packages.list');
        Route::get('/packages/create', [PackageController::class, 'create'])->name('packages.create');
        Route::post('/packages', [PackageController::class, 'store'])->name('packages.store');
        Route::get('/packages/{package}/edit', [PackageController::class, 'edit'])->name('packages.edit');
        Route::put('/packages/{package}', [PackageController::class, 'update'])->name('packages.update');
        Route::delete('/packages/{package}', [PackageController::class, 'destroy'])->name('packages.destroy');
        // status change route
        Route::get(
            'admin/packages/status/{id}',
            [PackageController::class, 'changeStatus']
        )->name('packages.changeStatus');

        // Calling Card Routes
        Route::get('/calling/cards/list', [CallingCardController::class, 'indexCallingCards'])->name('calling_card.index');
        Route::get('/calling/cards/create', [CallingCardController::class, 'createCallingCards'])->name('calling_card.create');
        Route::post('/calling/cards', [CallingCardController::class, 'storeCallingCards'])->name('calling_card.store');
        // delete and edit route will be added later after creating the listing page for calling card
        Route::delete('/calling/cards/{id}', [CallingCardController::class, 'destroyCalling'])->name('calling_card.destroy');
        Route::get('/calling/cards/{id}/edit', [CallingCardController::class, 'editCallingCard'])->name('calling_card.edit');
        Route::put('/calling/cards/{id}', [CallingCardController::class, 'updateCallingCard'])->name('calling_card.update');

        // VPN Name Routes
        Route::get('/vpn/names', [VpnNameController::class, 'index'])->name('vpn_names.index');
        Route::get('/vpn/names/create', [VpnNameController::class, 'create'])->name('vpn_names.create');
        Route::post('/vpn/names', [VpnNameController::class, 'store'])->name('vpn_names.store');
        Route::get('/vpn/names/{vpnName}/edit', [VpnNameController::class, 'edit'])->name('vpn_names.edit');
        Route::put('/vpn/names/{vpnName}', [VpnNameController::class, 'update'])->name('vpn_names.update');
        Route::delete('/vpn/names/{vpnName}', [VpnNameController::class, 'destroy'])->name('vpn_names.destroy');

        // VPN Account Routes
        Route::get('/vpns', [VpnController::class, 'index'])->name('vpns.index');
        Route::get('/vpns/create', [VpnController::class, 'create'])->name('vpns.create');
        Route::post('/vpns', [VpnController::class, 'store'])->name('vpns.store');
        Route::get('/vpns/{vpn}/edit', [VpnController::class, 'edit'])->name('vpns.edit');
        Route::put('/vpns/{vpn}', [VpnController::class, 'update'])->name('vpns.update');
        Route::delete('/vpns/{vpn}', [VpnController::class, 'destroy'])->name('vpns.destroy');

        // Sub List Package Routes
        Route::get('/sub-packages', [SubPackageController::class, 'index'])->name('subpackages.list');
        Route::get('/sub-packages/create', [SubPackageController::class, 'create'])->name('subpackages.create');
        Route::post('/sub-packages', [SubPackageController::class, 'store'])->name('subpackages.store');
        Route::get('/sub-packages/{subPackage}/edit', [SubPackageController::class, 'edit'])->name('subpackages.edit');
        Route::put('/sub-packages/{subPackage}', [SubPackageController::class, 'update'])->name('subpackages.update');
        Route::delete('/sub-packages/{subPackage}', [SubPackageController::class, 'destroy'])->name('subpackages.destroy');

        // Package Order List Route
        Route::get('/package-orders', [SubPackageController::class, 'packageOrders'])->name('package.orders');
        // status pending approved rejected route
        Route::get('/package-orders/approve/{id}', [SubPackageController::class, 'approveOrder'])->name('package_orders.approve');
        Route::get('/package-orders/reject/{id}', [SubPackageController::class, 'rejectOrder'])->name('package_orders.reject');

        Route::get('/package/orders/{id}', [SubPackageController::class, 'orders'])
            ->name('admin.package.orders');


        // Recharge Management Routes
        Route::get('/recharges', [RechargeController::class, 'index'])->name('recharges.pending');
        Route::get('/recharges/approve/{id}', [RechargeController::class, 'approveRecharge'])->name('recharges.approved');
        Route::get('/recharges/reject/{id}', [RechargeController::class, 'rejectRecharge'])->name('recharges.rejected');

        // Male Recharge Management Routes
        Route::get('/male/recharges', [MaleRechargeController::class, 'index'])->name('male.recharges.pending');
        Route::get('/male/recharges/approve/{id}', [MaleRechargeController::class, 'approveRecharge'])->name('male.recharges.approved');
        Route::get('/male/recharges/reject/{id}', [MaleRechargeController::class, 'rejectRecharge'])->name('male.recharges.rejected');

        // Mali Operator Routes
        Route::get('mali/operators', [MaliOperatorController::class, 'index'])->name('mali.operators.index');
        Route::get('mali/operators/edit/{id}', [MaliOperatorController::class, 'edit'])->name('mali.operators.edit');
        Route::post('mali/operators/update/{id}', [MaliOperatorController::class, 'update'])->name('mali.operators.update');
        Route::post('mali/operators/status/{id}', [MaliOperatorController::class, 'updateStatus'])->name('mali.operators.status');

        // Admin Setup Content
        Route::get('/app-setup/content', [AppSetupController::class, 'content'])
            ->name('setup.content');
        Route::post('/app-setup/content', [AppSetupController::class, 'update'])
            ->name('setup.content.update');
        Route::post('/app-setup/license', [AppSetupController::class, 'updateLicense'])
            ->name('setup.license.update');
        Route::get('/app-setup/notification', [AppSetupController::class, 'notification'])
            ->name('notification.message');
        Route::post('/app-setup/notification/update', [AppSetupController::class, 'updateNotification'])
            ->name('setup.notification.update');

        // Admin Setup Play Game
        Route::get('/app-setup/play_game', [AppSetupController::class, 'playGame'])
            ->name('setup.play_game');
        Route::post('/app-setup/play_game', [AppSetupController::class, 'updatePlayGame'])
            ->name('setup.play_game.update');

        // Admin Setup Link & Icon
        Route::get('/app-setup/link_icon', [AppSetupController::class, 'linkIcon'])
            ->name('setup.link_icon');
        Route::post('/app-setup/link_icon', [AppSetupController::class, 'updateLinkIcon'])
            ->name('setup.link_icon.update');

        // Admin Register Balance Content
        Route::get('/app-setup/content/register', [AppSetupController::class, 'contentRegister'])
            ->name('setup.content.register');
        Route::post('/app-setup/content/register', [AppSetupController::class, 'updateRegister'])
            ->name('setup.content.register.update');

        // change password
        Route::get('/app-setup/change/password', [AppSetupController::class, 'changePassword'])
            ->name('change.password');
        Route::post('/app-setup/change/password/update', [AppSetupController::class, 'updatePassword'])
            ->name('setup.password.update');

        // Admin Setup Social
        Route::get('/app-setup/social', [AppSetupController::class, 'social'])
            ->name('setup.social');
        Route::post('/app-setup/social', [AppSetupController::class, 'updateSocial'])
            ->name('setup.social.update');

        // status change route
        Route::get(
            'admin/sub-packages/status/{id}',
            [SubPackageController::class, 'changeStatusSub']
        )->name('subpackages.changeStatus');
    });
});
