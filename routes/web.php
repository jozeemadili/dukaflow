<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Livewire\Admin;
use App\Livewire\Auth\CompleteRegistration;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Portal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect(auth()->user()->isInternal() ? route('admin.dashboard') : route('portal.dashboard'));
    }

    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
    Route::get('/register/complete', CompleteRegistration::class)->name('register.complete');
    Route::get('/forgot-password', ForgotPassword::class)->name('password.request');
    Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset');

    Route::get('/auth/google/redirect', [GoogleController::class, 'redirect'])->name('auth.google.redirect');
    Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');
});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('login');
})->middleware('auth')->name('logout');

Route::middleware(['auth', 'internal'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', Admin\Dashboard::class)->name('dashboard');

    Route::get('/merchants', Admin\Merchants\Index::class)->name('merchants.index');
    Route::get('/merchants/create', Admin\Merchants\Create::class)->name('merchants.create');
    Route::get('/merchants/{merchant}', Admin\Merchants\Show::class)->name('merchants.show');

    Route::get('/kyc', Admin\Kyc\Index::class)->name('kyc.index');
    Route::get('/payments', Admin\Payments\Index::class)->name('payments.index');
    Route::get('/leads', Admin\Leads\Index::class)->name('leads.index');
    Route::get('/users', Admin\Users\Index::class)->name('users.index');
    Route::get('/audit-log', Admin\AuditLog\Index::class)->name('audit-log.index');
    Route::get('/credit', Admin\Credit\Index::class)->name('credit.index');
});

Route::middleware(['auth', 'merchant'])->prefix('portal')->name('portal.')->group(function () {
    Route::get('/', Portal\Dashboard::class)->name('dashboard');

    Route::get('/pos', Portal\Pos\Index::class)->name('pos.index');
    Route::get('/sales', Portal\Sales\Index::class)->name('sales.index');
    Route::get('/expenses', Portal\Expenses\Index::class)->name('expenses.index');
    Route::get('/inventory', Portal\Inventory\Index::class)->name('inventory.index');
    Route::get('/inventory/{item}', Portal\Inventory\Show::class)->name('inventory.show');
    Route::get('/stock-receipts', Portal\StockReceipts\Index::class)->name('stock-receipts.index');
    Route::get('/stock-receipts/{receipt}', Portal\StockReceipts\Show::class)->name('stock-receipts.show');
    Route::get('/suppliers', Portal\Suppliers\Index::class)->name('suppliers.index');
    Route::get('/stores', Portal\Stores\Index::class)->name('stores.index');
    Route::get('/stores/{store}', Portal\Stores\Show::class)->name('stores.show');
    Route::get('/customers', Portal\Customers\Index::class)->name('customers.index');
    Route::get('/customers/{customer}', Portal\Customers\Show::class)->name('customers.show');
    Route::get('/payments', Portal\Payments\Index::class)->name('payments.index');
    Route::get('/payment-methods', Portal\PaymentMethods\Index::class)->name('payment-methods.index');
    Route::get('/invoices', Portal\Invoices\Index::class)->name('invoices.index');
    Route::get('/invoices/{invoice}', Portal\Invoices\Show::class)->name('invoices.show');
    Route::get('/kyc', Portal\Kyc\Index::class)->name('kyc.index');
    Route::get('/staff', Portal\Staff\Index::class)->name('staff.index');
    Route::get('/discount-limits', Portal\DiscountLimits\Index::class)->name('discount-limits.index');
    Route::get('/branding', Portal\Branding\Index::class)->name('branding.index');
    Route::get('/credit', Portal\Credit\Index::class)->name('credit.index');
});
