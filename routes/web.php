<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DeliveryNoteController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\SystemLogController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\SuperAdmin\SuperAdminController;
use App\Http\Controllers\SuperAdmin\ShopController;
use App\Http\Controllers\SuperAdmin\UserController as SuperAdminUserController;
use App\Http\Controllers\SuperAdmin\SubscriptionController as SuperAdminSubscriptionController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\ServiceController;

Route::get('/', [WebsiteController::class, 'index'])->name('home');
Route::get('/services', [WebsiteController::class, 'services'])->name('website.services');
Route::get('/service/{slug}', [WebsiteController::class, 'serviceDetail'])->name('website.service_detail');
Route::get('/services/{slug}', [WebsiteController::class, 'serviceDetail']);
Route::get('/products', [WebsiteController::class, 'products'])->name('website.products');
Route::get('/products-catalog', [WebsiteController::class, 'products']);
Route::get('/page/{slug}', [WebsiteController::class, 'showPage'])->name('website.page');
Route::get('/about-us', fn() => redirect()->route('website.page', 'about-us'));
Route::get('/privacy-policy', fn() => redirect()->route('website.page', 'privacy-policy'));
Route::get('/terms-and-conditions', fn() => redirect()->route('website.page', 'terms-conditions'));
Route::get('/terms-conditions', fn() => redirect()->route('website.page', 'terms-conditions'));
Route::get('/refund-policy', fn() => redirect()->route('website.page', 'refund-policy'));
Route::get('/contact-us', fn() => redirect()->route('website.page', 'contact-us'));
Route::post('/contact-us', [WebsiteController::class, 'contactSubmit'])->name('contact.submit');
Route::post('/submit-proposal', [\App\Http\Controllers\ProposalSubmissionController::class, 'submit'])->name('proposal.submit');

// Language Switcher (Publicly accessible)
Route::get('language/{locale}', [\App\Http\Controllers\LanguageController::class, 'switch'])->name('language.switch');

// Quick Cache Clear Route for Live Server Deployment
Route::get('/clear-cache', function () {
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');

    // Also manually wipe storage/framework/views compiled files
    $files = glob(storage_path('framework/views/*.php'));
    foreach ($files as $file) {
        if (is_file($file)) {
            @unlink($file);
        }
    }

    return '<div style="font-family: Arial, sans-serif; text-align: center; padding: 50px;">
        <h2 style="color: #16A579;">✔ All Laravel Caches Cleared Successfully!</h2>
        <p>Compiled views, route cache, config cache, and application cache have been cleared.</p>
        <p>Now press <b>Ctrl + F5</b> on your browser to see the new UI.</p>
        <a href="' . url('/dashboard') . '" style="display: inline-block; margin-top: 15px; padding: 10px 20px; background: #4361EE; color: #fff; text-decoration: none; border-radius: 6px;">Go to Dashboard</a>
    </div>';
});

// Seamless asset delivery for uploads (handles live cPanel/shared-hosting root mismatches)
Route::get('/uploads/{path}', function ($path) {
    // 1. Check in public/uploads/
    $publicPath = public_path('uploads/' . $path);
    if (file_exists($publicPath) && !is_dir($publicPath)) {
        return response()->file($publicPath, [
            'Cache-Control' => 'public, max-age=2592000',
        ]);
    }

    // 2. Fallback check in root uploads/
    $rootPath = base_path('uploads/' . $path);
    if (file_exists($rootPath) && !is_dir($rootPath)) {
        return response()->file($rootPath, [
            'Cache-Control' => 'public, max-age=2592000',
        ]);
    }

    abort(404);
})->where('path', '.*');

Route::get('/public/uploads/{path}', function ($path) {
    $publicPath = public_path('uploads/' . $path);
    if (file_exists($publicPath) && !is_dir($publicPath)) {
        return response()->file($publicPath, [
            'Cache-Control' => 'public, max-age=2592000',
        ]);
    }
    abort(404);
})->where('path', '.*');

Auth::routes();

Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('employees', EmployeeController::class)->middleware('admin');
    
    Route::resource('customers', CustomerController::class);
    Route::post('customers/ajax-store', [CustomerController::class, 'ajaxStore'])->name('customers.ajax_store');
    Route::get('customers/{customer}/statement', [CustomerController::class, 'statement'])->name('customers.statement');
    Route::get('customers/{customer}/statement/pdf', [CustomerController::class, 'statementPdf'])->name('customers.statement_pdf');
    Route::get('admin/products/generate-sku', [ProductController::class, 'generateSku'])->name('products.generate_sku');
    Route::get('products/generate-sku', [ProductController::class, 'generateSku']);
    Route::resource('admin/products', ProductController::class, ['names' => 'products']);
    // Fallback legacy admin routes
    Route::get('products/{product}/edit', [ProductController::class, 'edit']);
    Route::put('products/{product}', [ProductController::class, 'update']);
    Route::delete('products/{product}', [ProductController::class, 'destroy']);
    Route::post('products', [ProductController::class, 'store']);

    // Dedicated Services Management Module
    Route::resource('admin/services', ServiceController::class)->names('services');
    Route::resource('categories', CategoryController::class);
    Route::resource('units', UnitController::class);
    Route::resource('quotes', QuoteController::class);
    Route::get('quotes/{quote}/convert', [QuoteController::class, 'convertToInvoice'])->name('quotes.convert');
    Route::get('quotes/{quote}/pdf', [QuoteController::class, 'downloadPdf'])->name('quotes.pdf');
    Route::post('quotes/{quote}/assign', [QuoteController::class, 'assignEmployee'])->name('quotes.assign_employee');
    
    Route::resource('delivery_notes', DeliveryNoteController::class)->only(['index', 'show', 'store', 'create', 'edit', 'update']);
    Route::get('invoices/{invoice}/delivery-note', [DeliveryNoteController::class, 'createFromInvoice'])->name('delivery_notes.create_from_invoice');
    Route::post('invoices/{invoice}/convert-to-delivery-note', [DeliveryNoteController::class, 'convertFromInvoice'])->name('invoices.convert_to_delivery_note');
    Route::get('delivery_notes/{deliveryNote}/pdf', [DeliveryNoteController::class, 'generatePDF'])->name('delivery_notes.pdf');
    Route::post('delivery_notes/{deliveryNote}/status', [DeliveryNoteController::class, 'updateStatus'])->name('delivery_notes.update_status');
    Route::post('delivery_notes/{deliveryNote}/assign', [DeliveryNoteController::class, 'assignEmployee'])->name('delivery_notes.assign_employee');
    
    Route::resource('invoices', InvoiceController::class);
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
    Route::post('invoices/{invoice}/assign', [InvoiceController::class, 'assignEmployee'])->name('invoices.assign_employee');
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

    Route::post('job-orders/{id}/assign', [App\Http\Controllers\JobOrderController::class, 'assignEmployee'])
        ->name('job-orders.assign_employee');

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

        // Platform Branding Settings
        Route::get('platform-settings', [SuperAdminController::class, 'platformSettings'])->name('platform_settings');
        Route::put('platform-settings', [SuperAdminController::class, 'updatePlatformSettings'])->name('platform_settings.update');

        // Test Email Dispatch
        Route::post('test-email', [SuperAdminController::class, 'sendTestEmail'])->name('test_email');

        // Subscription & Package Management
        Route::get('subscriptions', [SuperAdminSubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::post('subscriptions/{shop}/assign', [SuperAdminSubscriptionController::class, 'assignPackage'])->name('subscriptions.assign');
        Route::post('subscriptions/shops/{shop}/status', [SuperAdminSubscriptionController::class, 'toggleShopStatus'])->name('subscriptions.shop_status');
        Route::post('subscriptions/{subscription}/approve', [SuperAdminSubscriptionController::class, 'approveRequest'])->name('subscriptions.approve');
        Route::get('packages', [SuperAdminSubscriptionController::class, 'packages'])->name('packages.index');
        Route::put('packages/{package}', [SuperAdminSubscriptionController::class, 'updatePackage'])->name('packages.update');

        // Website CMS Management
        Route::prefix('cms')->as('cms.')->group(function () {
            Route::get('/', [\App\Http\Controllers\SuperAdmin\CmsController::class, 'index'])->name('index');
            Route::get('settings', [\App\Http\Controllers\SuperAdmin\CmsController::class, 'settings'])->name('settings');
            Route::post('settings', [\App\Http\Controllers\SuperAdmin\CmsController::class, 'updateSettings'])->name('settings.update');
            Route::get('sections', [\App\Http\Controllers\SuperAdmin\CmsController::class, 'sections'])->name('sections');
            Route::post('sections', [\App\Http\Controllers\SuperAdmin\CmsController::class, 'updateSections'])->name('sections.update');

            // CMS Resources
            Route::resource('modules', \App\Http\Controllers\SuperAdmin\CmsAppModuleController::class)->except(['show']);
            Route::resource('features', \App\Http\Controllers\SuperAdmin\CmsFeatureController::class)->except(['show']);
            Route::resource('showcase', \App\Http\Controllers\SuperAdmin\CmsShowcaseController::class)->only(['index', 'edit', 'update']);
            Route::resource('solutions', \App\Http\Controllers\SuperAdmin\CmsSolutionController::class)->except(['show']);
            Route::resource('testimonials', \App\Http\Controllers\SuperAdmin\CmsTestimonialController::class)->except(['show']);
            Route::post('testimonials/{testimonial}/toggle-approval', [\App\Http\Controllers\SuperAdmin\CmsTestimonialController::class, 'toggleApproval'])->name('testimonials.toggle_approval');
            Route::resource('faqs', \App\Http\Controllers\SuperAdmin\CmsFaqController::class)->except(['show']);
            Route::resource('nav', \App\Http\Controllers\SuperAdmin\CmsNavController::class)->only(['index', 'store', 'update', 'destroy']);
            
            // Dynamic CMS Pages Manager
            Route::post('pages/{page}/toggle-active', [\App\Http\Controllers\SuperAdmin\CmsPageController::class, 'toggleActive'])->name('pages.toggle_active');
            Route::resource('pages', \App\Http\Controllers\SuperAdmin\CmsPageController::class)->except(['show']);

            // Homepage Hero Slider Manager
            Route::post('slides/update-order', [\App\Http\Controllers\SuperAdmin\HomepageSlideController::class, 'updateOrder'])->name('slides.update_order');
            Route::post('slides/reorder', [\App\Http\Controllers\SuperAdmin\HomepageSlideController::class, 'updateOrder'])->name('slides.reorder');
            Route::post('slides/{slide}/toggle-active', [\App\Http\Controllers\SuperAdmin\HomepageSlideController::class, 'toggleActive'])->name('slides.toggle_active');
            Route::resource('slides', \App\Http\Controllers\SuperAdmin\HomepageSlideController::class)->except(['show']);

            // Proposal Form Settings & Dropdown Options
            Route::get('proposals/settings', [\App\Http\Controllers\SuperAdmin\ProposalSettingController::class, 'index'])->name('proposals.settings');
            Route::post('proposals/settings', [\App\Http\Controllers\SuperAdmin\ProposalSettingController::class, 'updateSettings'])->name('proposals.settings.update');
            Route::post('proposals/options', [\App\Http\Controllers\SuperAdmin\ProposalSettingController::class, 'storeOption'])->name('proposals.options.store');
            Route::put('proposals/options/{option}', [\App\Http\Controllers\SuperAdmin\ProposalSettingController::class, 'updateOption'])->name('proposals.options.update');
            Route::delete('proposals/options/{option}', [\App\Http\Controllers\SuperAdmin\ProposalSettingController::class, 'destroyOption'])->name('proposals.options.destroy');
            Route::post('proposals/options/{option}/toggle-active', [\App\Http\Controllers\SuperAdmin\ProposalSettingController::class, 'toggleOptionActive'])->name('proposals.options.toggle_active');

            Route::get('messages', [\App\Http\Controllers\SuperAdmin\ContactMessageController::class, 'index'])->name('messages.index');
            Route::post('messages/{message}/read', [\App\Http\Controllers\SuperAdmin\ContactMessageController::class, 'markRead'])->name('messages.read');
            Route::delete('messages/{message}', [\App\Http\Controllers\SuperAdmin\ContactMessageController::class, 'destroy'])->name('messages.destroy');
        });

        // Marketing: Proposal Requests Management
        Route::get('proposals/export-csv', [\App\Http\Controllers\SuperAdmin\ProposalRequestController::class, 'exportCsv'])->name('proposals.export_csv');
        Route::resource('proposals', \App\Http\Controllers\SuperAdmin\ProposalRequestController::class)->only(['index', 'show', 'destroy']);
        Route::put('proposals/{proposal}/status', [\App\Http\Controllers\SuperAdmin\ProposalRequestController::class, 'updateStatus'])->name('proposals.status.update');
    });

    // Shop Admin Subscriptions & Multi-Shop Branches
    Route::get('subscription/expired', [SubscriptionController::class, 'expired'])->name('subscription.expired');
    Route::post('subscription/request-renewal', [SubscriptionController::class, 'requestRenewal'])->name('subscription.request_renewal');
    Route::get('subscription', [SubscriptionController::class, 'mySubscription'])->name('subscription.index')->middleware('admin');

    // Business Multi-Shop Branches
    Route::resource('branches', BranchController::class)->only(['index', 'create', 'store'])->middleware('admin');
    Route::post('branches/switch/{id}', [BranchController::class, 'switchBranch'])->name('branches.switch')->middleware('admin');

});



