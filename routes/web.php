<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Services\DocumentService;
use App\Models\Invoice;
use App\Models\Payroll;

Route::view('/', 'welcome')->name('welcome');
Route::view('/features', 'pages.features')->name('public.features');
Route::view('/restaurant', 'pages.restaurant')->name('public.restaurant');
Route::view('/payments', 'pages.payments')->name('public.payments');
Route::view('/pricing', 'pages.pricing')->name('public.pricing');

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
    } elseif ($user->hasRole('Organization Admin')) {
        return redirect()->route('organization.dashboard'); 
    } elseif ($user->hasRole('Employee')) {
        return redirect()->route('organization.dashboard');
    }

    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Razorpay Webhook
Route::post('webhook/razorpay', [\App\Http\Controllers\RazorpayWebhookController::class, 'handleWebhook']);

// Payment Checkout
Route::get('pay/invoice/{invoice}', [\App\Http\Controllers\PaymentController::class, 'checkoutInvoice'])->name('payment.invoice');
Route::get('pay/order/{order}', [\App\Http\Controllers\PaymentController::class, 'checkoutRestaurantOrder'])->name('payment.order');

Route::middleware(['auth', 'role:Super Admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('organizations', \App\Http\Controllers\SuperAdmin\OrganizationController::class);
    Route::patch('organizations/{organization}/toggle-status', [\App\Http\Controllers\SuperAdmin\OrganizationController::class, 'toggleStatus'])->name('organizations.toggle-status');
    
    Route::resource('plans', \App\Http\Controllers\SuperAdmin\PlanController::class)->except(['show', 'destroy']);
    
    Route::resource('subscriptions', \App\Http\Controllers\SuperAdmin\SubscriptionController::class)->except(['create', 'store', 'show', 'destroy']);
    Route::get('/profile', function(){ return "Profile"; })->name('profile.edit');
});

Route::middleware(['auth'])->prefix('organization')->name('organization.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Organization\DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth', 'role:Organization Admin'])->prefix('organization')->name('organization.')->group(function () {
    Route::resource('roles', \App\Http\Controllers\Organization\RoleController::class)->except(['show', 'destroy']);
    
    Route::resource('employees', \App\Http\Controllers\Organization\EmployeeController::class);
    Route::patch('employees/{employee}/toggle-status', [\App\Http\Controllers\Organization\EmployeeController::class, 'toggleStatus'])->name('employees.toggle-status');
    
    Route::resource('locations', \App\Http\Controllers\Organization\LocationController::class)->except(['show', 'destroy']);
    Route::patch('locations/{location}/toggle-status', [\App\Http\Controllers\Organization\LocationController::class, 'toggleStatus'])->name('locations.toggle-status');
    
    // Subscription Management
    Route::prefix('subscription')->name('subscription.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Organization\SubscriptionController::class, 'index'])->name('index');
        Route::post('/switch', [\App\Http\Controllers\Organization\SubscriptionController::class, 'switchPlan'])->name('switch');
    });

    // Menu Management
    Route::middleware(['permission:restaurant.view', 'plan.feature:module_restaurant'])->prefix('menu')->name('menu.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Organization\RestaurantMenuController::class, 'index'])->name('index');
        Route::post('/categories', [\App\Http\Controllers\Organization\RestaurantMenuController::class, 'storeCategory'])->name('categories.store')->middleware('permission:restaurant.manage');
        Route::put('/categories/{category}', [\App\Http\Controllers\Organization\RestaurantMenuController::class, 'updateCategory'])->name('categories.update')->middleware('permission:restaurant.manage');
        Route::delete('/categories/{category}', [\App\Http\Controllers\Organization\RestaurantMenuController::class, 'destroyCategory'])->name('categories.destroy')->middleware('permission:restaurant.manage');

        Route::post('/items', [\App\Http\Controllers\Organization\RestaurantMenuController::class, 'storeItem'])->name('items.store')->middleware('permission:restaurant.manage');
        Route::put('/items/{item}', [\App\Http\Controllers\Organization\RestaurantMenuController::class, 'updateItem'])->name('items.update')->middleware('permission:restaurant.manage');
        Route::delete('/items/{item}', [\App\Http\Controllers\Organization\RestaurantMenuController::class, 'destroyItem'])->name('items.destroy')->middleware('permission:restaurant.manage');
        
        Route::get('/tables', [\App\Http\Controllers\Organization\TableController::class, 'index'])->name('tables.index');
        Route::post('/tables', [\App\Http\Controllers\Organization\TableController::class, 'store'])->name('tables.store')->middleware('permission:restaurant.manage');
        Route::put('/tables/{table}', [\App\Http\Controllers\Organization\TableController::class, 'update'])->name('tables.update')->middleware('permission:restaurant.manage');
        Route::delete('/tables/{table}', [\App\Http\Controllers\Organization\TableController::class, 'destroy'])->name('tables.destroy')->middleware('permission:restaurant.manage');
        Route::post('/tables/{table}/regenerate', [\App\Http\Controllers\Organization\TableController::class, 'regenerateQr'])->name('tables.regenerate')->middleware('permission:restaurant.manage');
        Route::get('/tables/print', [\App\Http\Controllers\Organization\TableController::class, 'printSheet'])->name('tables.print');
        
        Route::get('/kitchen', [\App\Http\Controllers\Organization\KitchenOrderController::class, 'index'])->name('kitchen.index')->middleware('permission:restaurant.view');
        Route::get('/kitchen/api/orders', [\App\Http\Controllers\Organization\KitchenOrderController::class, 'fetchOrders'])->name('kitchen.orders.fetch')->middleware('permission:restaurant.view');
        Route::post('/kitchen/api/orders/{order}/status', [\App\Http\Controllers\Organization\KitchenOrderController::class, 'updateStatus'])->name('kitchen.orders.status')->middleware('permission:restaurant.manage');
    });
});

Route::middleware(['auth', 'permission:products.view'])->prefix('organization')->name('organization.')->group(function () {
    Route::resource('categories', \App\Http\Controllers\Organization\CategoryController::class)->except(['create', 'show', 'edit', 'destroy']);
    Route::resource('products', \App\Http\Controllers\Organization\ProductController::class)->except(['show', 'destroy']);
});

Route::middleware(['auth', 'permission:clients.view'])->prefix('organization')->name('organization.')->group(function () {
    Route::get('clients/search', [\App\Http\Controllers\Organization\ClientController::class, 'apiSearch'])->name('clients.search');
    Route::resource('clients', \App\Http\Controllers\Organization\ClientController::class)->except(['destroy']);
});

Route::middleware(['auth', \App\Http\Middleware\LocationContext::class, 'permission:inventory.view'])->prefix('organization/inventory')->name('organization.inventory.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Organization\InventoryController::class, 'index'])->name('index');
    Route::get('scanner', [\App\Http\Controllers\Organization\InventoryController::class, 'scanner'])->name('scanner');
    Route::post('scanner/process', [\App\Http\Controllers\Organization\InventoryController::class, 'processBarcode'])->name('scanner.process');
    Route::post('adjust', [\App\Http\Controllers\Organization\InventoryController::class, 'adjust'])->name('adjust')->middleware('permission:inventory.adjust');
    Route::get('history', [\App\Http\Controllers\Organization\InventoryController::class, 'history'])->name('history');
});

Route::middleware(['auth', \App\Http\Middleware\LocationContext::class])->group(function () {
    Route::post('/organization/set-location', [\App\Http\Controllers\Organization\LocationController::class, 'switchLocation'])->name('organization.set-location');

    // Invoices are location-aware
    Route::middleware('permission:invoices.view')->prefix('organization/invoices')->name('organization.invoices.')->group(function () {
        Route::get('products/search', [\App\Http\Controllers\Organization\InvoiceController::class, 'apiProductSearch'])->name('products.search');
        Route::get('/', [\App\Http\Controllers\Organization\InvoiceController::class, 'index'])->name('index');
        Route::get('create', [\App\Http\Controllers\Organization\InvoiceController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Organization\InvoiceController::class, 'store'])->name('store');
        Route::get('{invoice}', [\App\Http\Controllers\Organization\InvoiceController::class, 'show'])->name('show');
        Route::get('{invoice}/print', [\App\Http\Controllers\Organization\InvoiceController::class, 'print'])->name('print');
        Route::post('{invoice}/cancel', [\App\Http\Controllers\Organization\InvoiceController::class, 'cancel'])->name('cancel')->middleware('permission:invoices.cancel');
        
        // Payments against invoice
        Route::post('{invoice}/payments', [\App\Http\Controllers\Organization\TransactionController::class, 'store'])->name('payments.store');
        
        // Reminders & Payment Links
        Route::post('{invoice}/remind', [\App\Http\Controllers\Organization\ReminderController::class, 'send'])->name('remind');
        Route::get('{invoice}/payment-link', [\App\Http\Controllers\Organization\ReminderController::class, 'generateLink'])->name('payment-link');
    });

    Route::middleware('permission:invoices.view')->prefix('organization/transactions')->name('organization.transactions.')->group(function () {
        Route::get('{transaction}/receipt', [\App\Http\Controllers\Organization\TransactionController::class, 'receipt'])->name('receipt');
    });

    // Receivables Dashboards
    Route::middleware('permission:invoices.view')->prefix('organization/receivables')->name('organization.receivables.')->group(function () {
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

