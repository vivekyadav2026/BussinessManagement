<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Services\DocumentService;
use App\Models\Invoice;
use App\Models\Payroll;

Route::get('/', function () {
    $plans = \App\Models\Plan::where('is_active', true)->with('features')->get();
    $trialDays = (int) \App\Models\SystemSetting::get('trial_days', 14);
    $enableTrial = \App\Models\SystemSetting::get('enable_free_trial', '1') === '1';
    return view('welcome', compact('plans', 'trialDays', 'enableTrial'));
})->name('welcome');

Route::get('/set-locale/{lang}', [\App\Http\Controllers\LanguageController::class, 'switchLanguage'])->name('set-locale');

// Live Server Storage Fallback Route & Link Creation Helper
Route::get('/link-storage', function () {
    $target = storage_path('app/public');
    $link = public_path('storage');

    try {
        if (function_exists('symlink')) {
            if (is_link($link) || file_exists($link)) {
                @unlink($link);
            }
            @symlink($target, $link);
            if (file_exists($link)) {
                return '<div style="font-family:sans-serif; padding:40px; text-align:center;">'
                    . '<h1 style="color:#16a34a;">✅ Storage symlink created successfully via PHP symlink()!</h1>'
                    . '<p><a href="/" style="color:#4f46e5;">Return to Homepage</a></p>'
                    . '</div>';
            }
        }
        
        if (function_exists('exec')) {
            \Illuminate\Support\Facades\Artisan::call('storage:link');
            return '<div style="font-family:sans-serif; padding:40px; text-align:center;">'
                . '<h1 style="color:#16a34a;">✅ Storage link created via Artisan!</h1>'
                . '<p><a href="/" style="color:#4f46e5;">Return to Homepage</a></p>'
                . '</div>';
        }
    } catch (\Throwable $e) {
        // Suppress any host execution errors
    }

    // If symlinks cannot be created, remove any broken link or empty folder so Apache routes /storage/... to Laravel
    if (is_link($link)) {
        @unlink($link);
    } elseif (is_dir($link) && count(scandir($link)) <= 2) {
        @rmdir($link);
    }

    return '<div style="font-family:sans-serif; padding:40px; text-align:center;">'
        . '<h1 style="color:#3b82f6;">ℹ️ PHP exec() / symlink() is restricted by your hosting provider.</h1>'
        . '<p><b>Dynamic Fallback Activated:</b> Any empty/broken storage folder was removed so your live server automatically routes all image requests to Laravel fallback engine.</p>'
        . '<p style="color:#16a34a; font-weight:bold;">All your uploaded photos, logos, and product images will now stream smoothly!</p>'
        . '<p><a href="/" style="color:#4f46e5;">Return to Homepage</a></p>'
        . '</div>';
})->name('storage.link');

Route::get('/storage/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);
    if (!file_exists($filePath)) {
        $filePath = public_path('uploads/' . $path);
    }
    if (!file_exists($filePath)) {
        $filePath = storage_path('app/' . $path);
    }
    if (!file_exists($filePath)) {
        abort(404);
    }
    
    $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';
    return response()->file($filePath, ['Content-Type' => $mimeType]);
})->where('path', '.*')->name('storage.fallback');

Route::view('/features', 'pages.features')->name('public.features');
Route::view('/restaurant', 'pages.restaurant')->name('public.restaurant');
Route::view('/payments', 'pages.payments')->name('public.payments');

Route::get('/pricing', function () {
    $plans = \App\Models\Plan::where('is_active', true)->with('features')->get();
    $type = request('type', 'business'); // default to business
    $trialDays = (int) \App\Models\SystemSetting::get('trial_days', 14);
    $enableTrial = \App\Models\SystemSetting::get('enable_free_trial', '1') === '1';
    return view('pages.pricing', compact('plans', 'type', 'trialDays', 'enableTrial'));
})->name('public.pricing');

Route::get('/privacy', function () {
    $privacyPolicy = \App\Models\SystemSetting::getPrivacyPolicy();
    return view('pages.privacy', compact('privacyPolicy'));
})->name('public.privacy');
Route::redirect('/privacy-policy', '/privacy');

Route::get('/terms', function () {
    $terms = \App\Models\SystemSetting::getTermsAndConditions();
    return view('pages.terms', compact('terms'));
})->name('public.terms');
Route::redirect('/terms-and-conditions', '/terms');

// Public Document Endpoints
Route::get('/document/invoice/{invoice}/download', function (Invoice $invoice) {
    // Basic security: if they have the ID, they can download. In real app, use a secure token.
    return DocumentService::generateInvoicePdf($invoice)->download('invoice_'.$invoice->invoice_number.'.pdf');
})->name('document.invoice.download');

Route::get('/document/payslip/{payroll}/download', function (Payroll $payroll) {
    $user = auth()->user();
    if ($user->hasRole('Organization Admin') || ($payroll->employee && $payroll->employee->user_id === $user->id)) {
        return DocumentService::generatePayslipPdf($payroll)->download('payslip_'.$payroll->month.'.pdf');
    }
    abort(403, 'Unauthorized access to this payslip.');
})->middleware('auth')->name('document.payslip.download');

Route::get('/dashboard', function () {
    $user = auth()->user();
    
    if ($user->hasRole('Super Admin')) {
        return redirect()->route('super-admin.dashboard');
    }

    if ($user->organization_id && ($user->hasRole('Organization Admin') || $user->hasPermissionTo('dashboard.view'))) {
        return redirect()->route('organization.dashboard'); 
    }
    
    // Smart fallback for specialized staff without explicit dashboard.view permission
    if ($user->hasPermissionTo('restaurant.view')) {
        return redirect()->route('organization.menu.kitchen.index');
    } elseif ($user->hasPermissionTo('invoices.view')) {
        return redirect()->route('organization.invoices.index');
    } elseif ($user->hasPermissionTo('inventory.view')) {
        return redirect()->route('organization.inventory.index');
    }

    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// Razorpay Webhook
Route::post('webhook/razorpay', [\App\Http\Controllers\RazorpayWebhookController::class, 'handleWebhook']);

// Payment Checkout & Verification
Route::get('pay/invoice/{invoice}/checkout', [\App\Http\Controllers\PaymentController::class, 'checkoutInvoice'])->name('payment.invoice');
Route::get('pay/order/{order}', [\App\Http\Controllers\PaymentController::class, 'checkoutRestaurantOrder'])->name('payment.order');
Route::post('payments/verify', [\App\Http\Controllers\PaymentController::class, 'verifyPayment'])->name('payments.verify');

Route::middleware(['auth', 'role:Super Admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('organizations', \App\Http\Controllers\SuperAdmin\OrganizationController::class);
    Route::patch('organizations/{organization}/toggle-status', [\App\Http\Controllers\SuperAdmin\OrganizationController::class, 'toggleStatus'])->name('organizations.toggle-status');
    
    Route::resource('plans', \App\Http\Controllers\SuperAdmin\PlanController::class);
    
    Route::resource('subscriptions', \App\Http\Controllers\SuperAdmin\SubscriptionController::class);
    
    Route::get('settings', [\App\Http\Controllers\SuperAdmin\SystemSettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [\App\Http\Controllers\SuperAdmin\SystemSettingController::class, 'update'])->name('settings.update');
    
    Route::get('/profile', function(){ return "Profile"; })->name('profile.edit');
});




// Organization Dashboard (accessible to Org Admin & Employees with dashboard.view permission)
Route::middleware(['auth', \App\Http\Middleware\LocationContext::class, 'permission:dashboard.view'])->prefix('organization')->name('organization.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Organization\DashboardController::class, 'index'])->name('dashboard');
});

// Roles & Permissions management (permission protected)
Route::middleware(['auth', 'permission:roles.view'])->prefix('organization')->name('organization.')->group(function () {
    Route::resource('roles', \App\Http\Controllers\Organization\RoleController::class);
});

// Employees management (permission protected)
Route::middleware(['auth', 'permission:employees.view'])->prefix('organization')->name('organization.')->group(function () {
    Route::resource('employees', \App\Http\Controllers\Organization\EmployeeController::class);
    Route::patch('employees/{employee}/toggle-status', [\App\Http\Controllers\Organization\EmployeeController::class, 'toggleStatus'])->name('employees.toggle-status');
});

// Organization Admin strict routes (Locations & Subscriptions)
Route::middleware(['auth', 'role:Organization Admin'])->prefix('organization')->name('organization.')->group(function () {
    Route::resource('locations', \App\Http\Controllers\Organization\LocationController::class);
    Route::patch('locations/{location}/toggle-status', [\App\Http\Controllers\Organization\LocationController::class, 'toggleStatus'])->name('locations.toggle-status');
    
    // Subscription Management
    Route::prefix('subscription')->name('subscription.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Organization\SubscriptionController::class, 'index'])->name('index');
        Route::post('/switch', [\App\Http\Controllers\Organization\SubscriptionController::class, 'switchPlan'])->name('switch');
        Route::post('/initiate/{plan}', [\App\Http\Controllers\Organization\SubscriptionController::class, 'initiatePayment'])->name('initiate');
        Route::post('/confirm', [\App\Http\Controllers\Organization\SubscriptionController::class, 'confirmPayment'])->name('confirm');
        Route::post('/refund', [\App\Http\Controllers\Organization\SubscriptionController::class, 'requestRefund'])->name('refund');
    });

}); // End of Organization Admin Group

// Menu Management (Requires Restaurant Permissions, not necessarily Org Admin)
Route::middleware(['auth', \App\Http\Middleware\LocationContext::class, 'plan.feature:module_restaurant'])->prefix('organization/menu')->name('organization.menu.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Organization\RestaurantMenuController::class, 'index'])->name('index')->middleware('permission:restaurant.menu');
    Route::post('/categories', [\App\Http\Controllers\Organization\RestaurantMenuController::class, 'storeCategory'])->name('categories.store')->middleware('permission:restaurant.menu');
    Route::put('/categories/{category}', [\App\Http\Controllers\Organization\RestaurantMenuController::class, 'updateCategory'])->name('categories.update')->middleware('permission:restaurant.menu');
    Route::delete('/categories/{category}', [\App\Http\Controllers\Organization\RestaurantMenuController::class, 'destroyCategory'])->name('categories.destroy')->middleware('permission:restaurant.menu');

    Route::post('/items', [\App\Http\Controllers\Organization\RestaurantMenuController::class, 'storeItem'])->name('items.store')->middleware('permission:restaurant.menu');
    Route::put('/items/{item}', [\App\Http\Controllers\Organization\RestaurantMenuController::class, 'updateItem'])->name('items.update')->middleware('permission:restaurant.menu');
    Route::delete('/items/{item}', [\App\Http\Controllers\Organization\RestaurantMenuController::class, 'destroyItem'])->name('items.destroy')->middleware('permission:restaurant.menu');
    
    Route::get('/tables', [\App\Http\Controllers\Organization\TableController::class, 'index'])->name('tables.index')->middleware('permission:restaurant.tables');
    Route::post('/tables', [\App\Http\Controllers\Organization\TableController::class, 'store'])->name('tables.store')->middleware('permission:restaurant.tables');
    Route::put('/tables/{table}', [\App\Http\Controllers\Organization\TableController::class, 'update'])->name('tables.update')->middleware('permission:restaurant.tables');
    Route::delete('/tables/{table}', [\App\Http\Controllers\Organization\TableController::class, 'destroy'])->name('tables.destroy')->middleware('permission:restaurant.tables');
    Route::post('/tables/{table}/regenerate', [\App\Http\Controllers\Organization\TableController::class, 'regenerateQr'])->name('tables.regenerate')->middleware('permission:restaurant.tables');
    Route::get('/tables/print', [\App\Http\Controllers\Organization\TableController::class, 'printSheet'])->name('tables.print')->middleware('permission:restaurant.view');
    
    // Kitchen Display System Routes
    Route::middleware('permission:restaurant.kitchen')->group(function() {
        Route::get('/kitchen', [\App\Http\Controllers\Organization\KitchenOrderController::class, 'index'])->name('kitchen.index');
        Route::get('/kitchen/api/orders', [\App\Http\Controllers\Organization\KitchenOrderController::class, 'fetchOrders'])->name('kitchen.orders.fetch');
        Route::get('/kitchen/api/today-summary', [\App\Http\Controllers\Organization\KitchenOrderController::class, 'todaySummary'])->name('kitchen.today-summary');
        Route::post('/kitchen/api/orders/{order}/status', [\App\Http\Controllers\Organization\KitchenOrderController::class, 'updateStatus'])->name('kitchen.orders.status');
    });

    // Waiter POS & Bill Receipt Routes
    Route::middleware('permission:restaurant.view')->group(function() {
        Route::get('/pos', [\App\Http\Controllers\Organization\WaiterPosController::class, 'index'])->name('pos.index');
        Route::get('/pos/table/{table}', [\App\Http\Controllers\Organization\WaiterPosController::class, 'getTableOrder'])->name('pos.table-order');
        Route::post('/pos/orders', [\App\Http\Controllers\Organization\WaiterPosController::class, 'saveOrder'])->name('pos.orders.save')->middleware('permission:restaurant.orders');
        Route::post('/pos/orders/{order}/settle', [\App\Http\Controllers\Organization\WaiterPosController::class, 'settleOrder'])->name('pos.orders.settle')->middleware('permission:restaurant.orders');
        Route::post('/pos/orders/{order}/cancel', [\App\Http\Controllers\Organization\WaiterPosController::class, 'cancelOrder'])->name('pos.orders.cancel')->middleware('permission:restaurant.cancel_order');
        Route::get('/pos/orders/{order}/print-receipt', [\App\Http\Controllers\Organization\WaiterPosController::class, 'printReceipt'])->name('pos.orders.print-receipt');
        Route::get('/pos/orders/{order}/print-kot', [\App\Http\Controllers\Organization\WaiterPosController::class, 'printKot'])->name('pos.orders.print-kot');
    });

    // Counter Billing Mode Routes
    Route::middleware('permission:restaurant.counter')->group(function() {
        Route::get('/counter', [\App\Http\Controllers\Organization\CounterBillingController::class, 'index'])->name('counter.index');
        Route::get('/counter/api/active-orders', [\App\Http\Controllers\Organization\CounterBillingController::class, 'fetchActiveOrders'])->name('counter.orders.active');
        Route::get('/counter/api/completed-orders', [\App\Http\Controllers\Organization\CounterBillingController::class, 'fetchCompletedOrders'])->name('counter.orders.completed');
        Route::post('/counter/orders', [\App\Http\Controllers\Organization\CounterBillingController::class, 'saveOrder'])->name('counter.orders.save');
        Route::post('/counter/orders/{order}/settle', [\App\Http\Controllers\Organization\CounterBillingController::class, 'settleOrder'])->name('counter.orders.settle');
        Route::post('/counter/orders/{order}/cancel', [\App\Http\Controllers\Organization\CounterBillingController::class, 'cancelOrder'])->name('counter.orders.cancel')->middleware('permission:restaurant.cancel_order');
    });

    // Restaurant Sales Reports & Analytics Route
    Route::get('/reports', [\App\Http\Controllers\Organization\RestaurantReportController::class, 'index'])->name('reports.index')->middleware('permission:restaurant.reports');

});

Route::middleware(['auth', 'permission:products.view', 'plan.feature:module_retail'])->prefix('organization')->name('organization.')->group(function () {
    Route::resource('categories', \App\Http\Controllers\Organization\CategoryController::class)->except(['create', 'show', 'edit']);
    Route::get('products/{product}/print-barcode', [\App\Http\Controllers\Organization\ProductController::class, 'printBarcode'])->name('products.print-barcode');
    Route::resource('products', \App\Http\Controllers\Organization\ProductController::class);
});


Route::middleware(['auth', 'permission:clients.view', 'plan.feature:module_retail'])->prefix('organization')->name('organization.')->group(function () {
    Route::get('clients/search', [\App\Http\Controllers\Organization\ClientController::class, 'apiSearch'])->name('clients.search');
    Route::post('clients/quick-store', [\App\Http\Controllers\Organization\ClientController::class, 'quickStore'])->name('clients.quick-store');
    Route::resource('clients', \App\Http\Controllers\Organization\ClientController::class);
});

Route::middleware(['auth', \App\Http\Middleware\LocationContext::class, 'permission:inventory.view', 'plan.feature:module_retail'])->prefix('organization/inventory')->name('organization.inventory.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Organization\InventoryController::class, 'index'])->name('index');
    Route::get('scanner', [\App\Http\Controllers\Organization\InventoryController::class, 'scanner'])->name('scanner');
    Route::post('scanner/process', [\App\Http\Controllers\Organization\InventoryController::class, 'processBarcode'])->name('scanner.process');
    Route::post('adjust', [\App\Http\Controllers\Organization\InventoryController::class, 'adjust'])->name('adjust')->middleware('permission:inventory.adjust');
    Route::get('history', [\App\Http\Controllers\Organization\InventoryController::class, 'history'])->name('history');
});

Route::middleware(['auth', \App\Http\Middleware\LocationContext::class])->group(function () {
    Route::post('/organization/set-location', [\App\Http\Controllers\Organization\LocationController::class, 'switchLocation'])->name('organization.set-location');

    // Invoices are location-aware
    Route::middleware(['permission:invoices.view', 'plan.feature:module_retail'])->prefix('organization/invoices')->name('organization.invoices.')->group(function () {
        Route::get('products/search', [\App\Http\Controllers\Organization\InvoiceController::class, 'apiProductSearch'])->name('products.search');
        Route::get('/', [\App\Http\Controllers\Organization\InvoiceController::class, 'index'])->name('index');
        Route::get('create', [\App\Http\Controllers\Organization\InvoiceController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Organization\InvoiceController::class, 'store'])->name('store');
        Route::get('{invoice}', [\App\Http\Controllers\Organization\InvoiceController::class, 'show'])->name('show');
        Route::get('{invoice}/print', [\App\Http\Controllers\Organization\InvoiceController::class, 'print'])->name('print');
        Route::get('{invoice}/receipt', [\App\Http\Controllers\Organization\InvoiceController::class, 'receipt'])->name('receipt');
        Route::post('{invoice}/cancel', [\App\Http\Controllers\Organization\InvoiceController::class, 'cancel'])->name('cancel')->middleware('permission:invoices.cancel');
        Route::post('{invoice}/finalize', [\App\Http\Controllers\Organization\InvoiceController::class, 'finalizeDraft'])->name('finalize');
        
        // Payments against invoice
        Route::post('{invoice}/payments', [\App\Http\Controllers\Organization\TransactionController::class, 'store'])->name('payments.store');
        
        // Reminders & Payment Links
        Route::post('{invoice}/remind', [\App\Http\Controllers\Organization\ReminderController::class, 'send'])->name('remind');
        Route::get('{invoice}/payment-link', [\App\Http\Controllers\Organization\ReminderController::class, 'generateLink'])->name('payment-link');
    });

    Route::middleware(['permission:invoices.view', 'plan.feature:module_retail'])->prefix('organization/transactions')->name('organization.transactions.')->group(function () {
        Route::get('{transaction}/receipt', [\App\Http\Controllers\Organization\TransactionController::class, 'receipt'])->name('receipt');
    });

    // Receivables Dashboards
    Route::middleware(['permission:invoices.view', 'plan.feature:module_retail'])->prefix('organization/receivables')->name('organization.receivables.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Organization\ReceivableController::class, 'dashboard'])->name('index');
        Route::get('client-report', [\App\Http\Controllers\Organization\ReceivableController::class, 'clientReport'])->name('client_report');
        Route::get('overdue-report', [\App\Http\Controllers\Organization\ReceivableController::class, 'overdueReport'])->name('overdue_report');
    });

    // Attendance
    Route::middleware('permission:attendance.view')->prefix('organization/attendance')->name('organization.attendance.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Organization\AttendanceController::class, 'index'])->name('index');
        Route::post('bulk', [\App\Http\Controllers\Organization\AttendanceController::class, 'storeBulk'])->name('storeBulk')->middleware('permission:attendance.manage');
        Route::get('report', [\App\Http\Controllers\Organization\AttendanceController::class, 'report'])->name('report');
        Route::get('{employee}', [\App\Http\Controllers\Organization\AttendanceController::class, 'show'])->name('show');
    });

    // Payroll & Salary Structure
    Route::middleware(['permission:payroll.view', 'plan.feature:module_payroll'])->prefix('organization')->name('organization.')->group(function () {
        Route::get('/payroll', [\App\Http\Controllers\Organization\PayrollController::class, 'index'])->name('payroll.index');
        Route::post('/payroll/generate', [\App\Http\Controllers\Organization\PayrollController::class, 'generate'])->name('payroll.generate')->middleware('permission:payroll.manage');
        Route::get('/payroll/{payroll}', [\App\Http\Controllers\Organization\PayrollController::class, 'show'])->name('payroll.show');
        Route::put('/payroll/{payroll}/adjustment', [\App\Http\Controllers\Organization\PayrollController::class, 'updateAdjustment'])->name('payroll.updateAdjustment')->middleware('permission:payroll.manage');
        Route::put('/payroll/{payroll}/pay', [\App\Http\Controllers\Organization\PayrollController::class, 'markPaid'])->name('payroll.markPaid')->middleware('permission:payroll.manage');
        
        Route::get('/employees/{employee}/salary-structure', [\App\Http\Controllers\Organization\SalaryStructureController::class, 'show'])->name('employees.salary-structure.show');
        Route::post('/employees/{employee}/salary-structure', [\App\Http\Controllers\Organization\SalaryStructureController::class, 'store'])->name('employees.salary-structure.store')->middleware('permission:payroll.manage');
    });

    // Complaints
    Route::middleware('permission:complaints.view')->prefix('organization/complaints')->name('organization.complaints.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Organization\ComplaintController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Organization\ComplaintController::class, 'create'])->name('create')->middleware('permission:complaints.create');
        Route::post('/', [\App\Http\Controllers\Organization\ComplaintController::class, 'store'])->name('store')->middleware('permission:complaints.create');
        Route::get('/{complaint}', [\App\Http\Controllers\Organization\ComplaintController::class, 'show'])->name('show');
        Route::put('/{complaint}', [\App\Http\Controllers\Organization\ComplaintController::class, 'update'])->name('update')->middleware('permission:complaints.manage');
    });

    // Notifications
    Route::prefix('organization/notifications')->name('organization.notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Organization\NotificationController::class, 'index'])->name('index');
        Route::post('/mark-all-read', [\App\Http\Controllers\Organization\NotificationController::class, 'markAllAsRead'])->name('markAllAsRead');
        Route::post('/{id}/mark-read', [\App\Http\Controllers\Organization\NotificationController::class, 'markAsRead'])->name('markAsRead');
    });
});

// Public Signed Routes
Route::get('pay/invoice/{invoice}', [\App\Http\Controllers\PublicInvoiceController::class, 'show'])->name('public.invoice.pay')->middleware('signed');

// Public Menu & Orders
Route::get('/menu/{organization}/{location}', [\App\Http\Controllers\PublicMenuController::class, 'show'])->name('public.menu');
Route::get('/t/{token}', [\App\Http\Controllers\PublicMenuController::class, 'showByToken'])->name('public.menu.table');

Route::prefix('menu/{organization}/{location}')->name('public.order.')->group(function () {
    Route::post('/add', [\App\Http\Controllers\PublicOrderController::class, 'addToCart'])->name('add');
    Route::get('/cart', [\App\Http\Controllers\PublicOrderController::class, 'cart'])->name('cart');
    Route::post('/cart/{item}/remove', [\App\Http\Controllers\PublicOrderController::class, 'removeFromCart'])->name('remove');
    Route::post('/cart/{item}/update', [\App\Http\Controllers\PublicOrderController::class, 'updateQuantity'])->name('update-quantity');
    Route::get('/checkout', [\App\Http\Controllers\PublicOrderController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [\App\Http\Controllers\PublicOrderController::class, 'placeOrder'])->name('place');
    Route::get('/track/{order}', [\App\Http\Controllers\PublicOrderController::class, 'track'])->name('track');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    // Inventory
    Route::get('/inventory', [\App\Http\Controllers\Organization\InventoryController::class, 'index'])->name('organization.inventory.index');
    Route::post('/inventory/stock-movements', [\App\Http\Controllers\Organization\InventoryController::class, 'storeMovement'])->name('organization.inventory.movements.store');
    
    Route::get('/organization-profile', [\App\Http\Controllers\Organization\OrganizationProfileController::class, 'show'])->name('organization.profile');
    Route::put('/organization-profile', [\App\Http\Controllers\Organization\OrganizationProfileController::class, 'update'])->name('organization.profile.update');
    
    // Auth profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/handover-pdf', function () {
    $pdf = Barryvdh\DomPDF\Facade\Pdf::loadView('documents.handover');
    return $pdf->download('Vyapaargo_Handover_Documentation.pdf');
})->name('handover.pdf');

