<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DeliveryNoteController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\SystemLogController;
use App\Http\Controllers\SuperAdmin\SuperAdminController;
use App\Http\Controllers\SuperAdmin\ShopController;
use App\Http\Controllers\SuperAdmin\UserController as SuperAdminUserController;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('employees', EmployeeController::class)->middleware('admin');
    
    Route::resource('customers', CustomerController::class);
    Route::post('customers/ajax-store', [CustomerController::class, 'ajaxStore'])->name('customers.ajax_store');
    Route::get('customers/{customer}/statement', [CustomerController::class, 'statement'])->name('customers.statement');
    Route::get('customers/{customer}/statement/pdf', [CustomerController::class, 'statementPdf'])->name('customers.statement_pdf');
    Route::resource('products', ProductController::class);
    Route::resource('quotes', QuoteController::class);
    Route::get('quotes/{quote}/convert', [QuoteController::class, 'convertToInvoice'])->name('quotes.convert');
    Route::get('quotes/{quote}/pdf', [QuoteController::class, 'downloadPdf'])->name('quotes.pdf');
    
    Route::resource('delivery_notes', DeliveryNoteController::class)->only(['index', 'show', 'store', 'create', 'edit', 'update']);
    Route::get('invoices/{invoice}/delivery-note', [DeliveryNoteController::class, 'createFromInvoice'])->name('delivery_notes.create_from_invoice');
    Route::post('invoices/{invoice}/convert-to-delivery-note', [DeliveryNoteController::class, 'convertFromInvoice'])->name('invoices.convert_to_delivery_note');
    Route::get('delivery_notes/{deliveryNote}/pdf', [DeliveryNoteController::class, 'generatePDF'])->name('delivery_notes.pdf');
    Route::post('delivery_notes/{deliveryNote}/status', [DeliveryNoteController::class, 'updateStatus'])->name('delivery_notes.update_status');
    
    Route::resource('invoices', InvoiceController::class);
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
    Route::post('invoices/{invoice}/payments', [PaymentController::class, 'store'])->name('payments.store');
    
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index')->middleware('admin');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update')->middleware('admin');
    Route::resource('departments', DepartmentController::class);
    
    Route::get('system-logs', [SystemLogController::class, 'index'])->name('system_logs.index');
    Route::get('recycle-bin', [App\Http\Controllers\RecycleBinController::class, 'index'])->name('recycle_bin.index');
    Route::post('recycle-bin/restore', [App\Http\Controllers\RecycleBinController::class, 'restore'])->name('recycle_bin.restore');

    // Job Order routes
    Route::get('job-orders-statement-pdf', [App\Http\Controllers\JobOrderController::class, 'downloadStatementPdf'])
        ->name('job-orders.statement-pdf');

    Route::get('job-orders', [App\Http\Controllers\JobOrderController::class, 'index'])
        ->name('job-orders.index');

    Route::get('job-orders/create', [App\Http\Controllers\JobOrderController::class, 'create'])
        ->name('job-orders.create');

    Route::post('job-orders', [App\Http\Controllers\JobOrderController::class, 'store'])
        ->name('job-orders.store');

    Route::post('job-orders/from-quote/{quoteId}', [App\Http\Controllers\JobOrderController::class, 'storeFromQuote'])
        ->name('job-orders.from-quote');

    Route::get('job-orders/{id}', [App\Http\Controllers\JobOrderController::class, 'show'])
        ->name('job-orders.show');

    Route::get('job-orders/{id}/edit', [App\Http\Controllers\JobOrderController::class, 'edit'])
        ->name('job-orders.edit');

    Route::put('job-orders/{id}', [App\Http\Controllers\JobOrderController::class, 'update'])
        ->name('job-orders.update');

    Route::post('job-orders/{id}/payments', [App\Http\Controllers\JobOrderController::class, 'addPayment'])
        ->name('job-orders.payments');
    Route::put('job-order-payments/{id}', [App\Http\Controllers\JobOrderController::class, 'updatePayment'])->name('job-order-payments.update');
    Route::delete('job-order-payments/{id}', [App\Http\Controllers\JobOrderController::class, 'deletePayment'])->name('job-order-payments.destroy');

    Route::post('job-orders/{id}/assignments', [App\Http\Controllers\JobOrderController::class, 'addAssignment'])
        ->name('job-orders.assignments.store');

    Route::patch('job-assignments/{assignment}/status', [App\Http\Controllers\JobOrderController::class, 'updateAssignmentStatus'])
        ->name('job-assignments.update-status');

    Route::delete('job-assignments/{assignment}', [App\Http\Controllers\JobOrderController::class, 'deleteAssignment'])
        ->name('job-assignments.destroy');

    Route::get('job-orders/{id}/pdf', [App\Http\Controllers\JobOrderController::class, 'downloadPdf'])
        ->name('job-orders.pdf');

    Route::get('job-orders/{id}/invoice', [App\Http\Controllers\JobOrderController::class, 'generateInvoice'])
        ->name('job-orders.invoice');

    Route::post('job-orders/{id}/cancel', [App\Http\Controllers\JobOrderController::class, 'cancel'])
        ->name('job-orders.cancel');

    Route::delete('job-orders/{id}', [App\Http\Controllers\JobOrderController::class, 'destroy'])
        ->name('job-orders.destroy');

    // Super Admin Switch Shop Routes
    Route::post('super-admin/switch-shop/{id}', [ShopController::class, 'switchShop'])->name('super_admin.switch_shop');
    Route::post('super-admin/switch-shop-all', [ShopController::class, 'switchShopAll'])->name('super_admin.switch_shop_all');

    // Super Admin Management Panel
    Route::group(['prefix' => 'super-admin', 'as' => 'super_admin.', 'middleware' => 'super_admin'], function () {
        Route::get('/', [SuperAdminController::class, 'index'])->name('dashboard');
        
        // Shop Management
        Route::resource('shops', ShopController::class);
        Route::post('shops/{shop}/toggle-status', [ShopController::class, 'toggleStatus'])->name('shops.toggle_status');
        Route::post('shops/{shop}/update-status', [ShopController::class, 'updateStatus'])->name('shops.update_status');
        
        // Platform Users Management
        Route::resource('users', SuperAdminUserController::class);

        // Test Email Dispatch
        Route::post('test-email', [SuperAdminController::class, 'sendTestEmail'])->name('test_email');
    });

});



