<?php

use App\Domains\Core\Http\Controllers\ContextController;
use App\Domains\Core\Http\Middleware\EnsureActiveContext;
use App\Domains\Core\Models\BusinessUnit;
use App\Domains\Core\Services\ContextManager;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Cleaning\CleaningCoordinatorController;
use App\Http\Controllers\Cleaning\CleaningDailyControlController;
use App\Http\Controllers\Cleaning\CleaningDashboardController;
use App\Http\Controllers\Cleaning\CleaningServiceTemplateController;
use App\Http\Controllers\Cleaning\CleaningStoreController;
use App\Http\Controllers\Cleaning\CleaningSupervisorAssignmentController;
use App\Http\Controllers\Cleaning\CleaningTimesheetController;
use App\Http\Controllers\Cleaning\CleaningWorkerController;
use App\Http\Controllers\Management\ApprovalController;
use App\Http\Controllers\Management\AuditController;
use App\Http\Controllers\Management\BranchController;
use App\Http\Controllers\Management\BusinessUnitController;
use App\Http\Controllers\Management\DashboardController;
use App\Http\Controllers\Management\DepartmentController;
use App\Http\Controllers\Management\DocumentController;
use App\Http\Controllers\Management\EventController;
use App\Http\Controllers\Management\FinanceController;
use App\Http\Controllers\Management\InventoryController;
use App\Http\Controllers\Management\LocationController;
use App\Http\Controllers\Management\OrganizationController;
use App\Http\Controllers\Management\ProcurementController;
use App\Http\Controllers\Management\ProductionController;
use App\Http\Controllers\Management\ProjectController;
use App\Http\Controllers\Management\ProjectTaskController;
use App\Http\Controllers\Management\ReportController;
use App\Http\Controllers\Management\RoleController;
use App\Http\Controllers\Management\SettingsController;
use App\Http\Controllers\Management\UserController;
use App\Http\Controllers\Restaurant\KitchenController;
use App\Http\Controllers\Restaurant\PosController;
use App\Http\Controllers\Restaurant\RestaurantCategoryController;
use App\Http\Controllers\Restaurant\RestaurantDashboardController;
use App\Http\Controllers\Restaurant\RestaurantExpenseController;
use App\Http\Controllers\Restaurant\RestaurantLocationController;
use App\Http\Controllers\Restaurant\RestaurantMenuController;
use App\Http\Controllers\Restaurant\RestaurantNavigationController;
use App\Http\Controllers\Restaurant\RestaurantPurchasingController;
use App\Http\Controllers\Restaurant\RestaurantShiftController;
use App\Http\Controllers\Restaurant\RestaurantStoreController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->prefix('management')->name('management.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/finance', [FinanceController::class, 'index'])->name('finance');

    Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals');
    Route::post('/approvals/po/{order}', [ApprovalController::class, 'approvePurchaseOrder'])->name('approvals.po');
    Route::post('/approvals/po/{order}/reject', [ApprovalController::class, 'rejectPurchaseOrder'])->name('approvals.po.reject');
    Route::post('/approvals/invoice/{invoice}', [ApprovalController::class, 'approveSupplierInvoice'])->name('approvals.invoice');

    Route::get('/procurement', [ProcurementController::class, 'index'])->name('procurement');
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
    Route::get('/production', [ProductionController::class, 'index'])->name('production');
    Route::get('/events', [EventController::class, 'index'])->name('events');
    Route::get('/organization', [OrganizationController::class, 'index'])->name('organization.index');
    Route::get('/organization/home', [OrganizationController::class, 'index'])->name('organization');
    Route::get('/organization/business-units', [OrganizationController::class, 'businessUnits'])->name('organization.business-units');
    Route::get('/organization/branches', [OrganizationController::class, 'branches'])->name('organization.branches');
    Route::get('/organization/structure', [OrganizationController::class, 'structure'])->name('organization.structure');

    Route::resource('departments', DepartmentController::class)->names([
        'index' => 'organization.departments',
    ]);
    Route::resource('locations', LocationController::class)->names([
        'index' => 'organization.locations',
    ]);

    Route::get('/business-units/create', [BusinessUnitController::class, 'create'])->name('business-units.create-direct');
    Route::post('/business-units', [BusinessUnitController::class, 'store'])->name('business-units.store-direct');
    Route::get('/organization/{organization}/business-units/create', [BusinessUnitController::class, 'create'])->name('business-units.create');
    Route::post('/organization/{organization}/business-units', [BusinessUnitController::class, 'store'])->name('business-units.store');
    Route::post('/business-units/{businessUnit}/toggle', [BusinessUnitController::class, 'toggleStatus'])->name('business-units.toggle');
    Route::resource('/business-units', BusinessUnitController::class)->only(['show', 'edit', 'update', 'destroy']);

    Route::get('/branches/create', [BranchController::class, 'create'])->name('branches.create-direct');
    Route::post('/branches', [BranchController::class, 'store'])->name('branches.store-direct');
    Route::get('/business-units/{businessUnit}/branches/create', [BranchController::class, 'create'])->name('branches.create');
    Route::post('/business-units/{businessUnit}/branches', [BranchController::class, 'store'])->name('branches.store');
    Route::post('/branches/{branch}/toggle', [BranchController::class, 'toggleStatus'])->name('branches.toggle');
    Route::resource('/branches', BranchController::class)->only(['show', 'edit', 'update', 'destroy']);

    Route::resource('/users', UserController::class)->except(['show']);

    Route::resource('/projects', ProjectController::class);
    Route::get('/projects/{project}/tasks/create', [ProjectTaskController::class, 'create'])->name('project-tasks.create');
    Route::post('/projects/{project}/tasks', [ProjectTaskController::class, 'store'])->name('project-tasks.store');
    Route::resource('/project-tasks', ProjectTaskController::class)->only(['edit', 'update', 'destroy']);

    Route::resource('/documents', DocumentController::class);
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
    Route::get('/reports/export/{type}', [ReportController::class, 'export'])->name('reports.export');
    Route::get('/audit', [AuditController::class, 'index'])->name('audit');
    Route::get('/audit/export', [AuditController::class, 'export'])->name('audit.export');
    Route::resource('/roles', RoleController::class)->except(['show']);

    Route::get('/settings/system', [SettingsController::class, 'system'])->name('settings.system');
    Route::post('/settings/system', [SettingsController::class, 'updateSystem'])->name('settings.system.update');
    Route::get('/settings/organization', [SettingsController::class, 'organization'])->name('settings.organization');
    Route::post('/settings/organization', [SettingsController::class, 'updateOrganization'])->name('settings.organization.update');
    Route::get('/settings/financial', [SettingsController::class, 'financial'])->name('settings.financial');
    Route::post('/settings/financial', [SettingsController::class, 'updateFinancial'])->name('settings.financial.update');
    Route::get('/settings/numbering', [SettingsController::class, 'numbering'])->name('settings.numbering');
    Route::post('/settings/numbering', [SettingsController::class, 'updateNumbering'])->name('settings.numbering.update');
    Route::get('/settings/tax', [SettingsController::class, 'tax'])->name('settings.tax');
    Route::post('/settings/tax', [SettingsController::class, 'updateTax'])->name('settings.tax.update');
    Route::get('/settings/notifications', [SettingsController::class, 'notifications'])->name('settings.notifications');
    Route::post('/settings/notifications', [SettingsController::class, 'updateNotifications'])->name('settings.notifications.update');
});

Route::middleware('auth')->prefix('restaurant')->name('restaurant.')->group(function () {

    // 1. Dashboard
    Route::get('/dashboard', [RestaurantDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/sales', [RestaurantNavigationController::class, 'placeholder'])->name('dashboard.sales');
    Route::get('/dashboard/orders', [RestaurantNavigationController::class, 'placeholder'])->name('dashboard.orders');
    Route::get('/dashboard/stock', [RestaurantNavigationController::class, 'placeholder'])->name('dashboard.stock');
    Route::get('/dashboard/cash', [RestaurantNavigationController::class, 'placeholder'])->name('dashboard.cash');

    // 2. Sales / POS
    Route::get('/pos', [PosController::class, 'index'])->name('pos'); // New Sale
    Route::post('/pos/order', [PosController::class, 'storeOrder'])->name('pos.order');
    Route::get('/orders', [PosController::class, 'orders'])->name('orders'); // Orders
    Route::get('/sales/history', [RestaurantNavigationController::class, 'placeholder'])->name('sales.history');
    Route::get('/sales/refunds', [RestaurantNavigationController::class, 'placeholder'])->name('sales.refunds');
    Route::get('/sales/discounts', [RestaurantNavigationController::class, 'placeholder'])->name('sales.discounts');
    Route::get('/sales/voids', [RestaurantNavigationController::class, 'placeholder'])->name('sales.voids');

    // Kitchen (To be removed from menu later, but kept for now)
    Route::get('/kitchen', [KitchenController::class, 'index'])->name('kitchen');
    Route::post('/kitchen/{order}/status', [KitchenController::class, 'updateStatus'])->name('kitchen.status');

    // 3. Products & Menu
    Route::get('/menu', [RestaurantMenuController::class, 'index'])->name('menu'); // Products
    Route::post('/menu', [RestaurantMenuController::class, 'store'])->name('menu.store');
    Route::put('/menu/{item}', [RestaurantMenuController::class, 'update'])->name('menu.update');
    Route::delete('/menu/{item}', [RestaurantMenuController::class, 'destroy'])->name('menu.destroy');
    Route::post('/menu/{item}/toggle', [RestaurantMenuController::class, 'toggleStatus'])->name('menu.toggle');
    Route::get('/menu/search', [RestaurantMenuController::class, 'search'])->name('menu.search');
    Route::post('/menu/{item}/restock', [RestaurantMenuController::class, 'restock'])->name('menu.restock');

    // Categories
    Route::get('/menu/categories', [RestaurantCategoryController::class, 'index'])->name('menu.categories');
    Route::post('/menu/categories', [RestaurantCategoryController::class, 'store'])->name('menu.categories.store');
    Route::put('/menu/categories/{category}', [RestaurantCategoryController::class, 'update'])->name('menu.categories.update');
    Route::delete('/menu/categories/{category}', [RestaurantCategoryController::class, 'destroy'])->name('menu.categories.destroy');

    Route::get('/menu/prices', [RestaurantNavigationController::class, 'placeholder'])->name('menu.prices');

    // 4. Inventory / Store
    Route::get('/store', [RestaurantStoreController::class, 'index'])->name('store'); // Stock Overview
    Route::post('/store/receive', [RestaurantStoreController::class, 'receive'])->name('store.receive');
    Route::post('/store/wastage', [RestaurantStoreController::class, 'wastage'])->name('store.wastage');
    Route::get('/store/issue', [RestaurantNavigationController::class, 'placeholder'])->name('store.issue');
    Route::get('/store/transfer', [RestaurantNavigationController::class, 'placeholder'])->name('store.transfer');
    Route::get('/store/adjustment', [RestaurantNavigationController::class, 'placeholder'])->name('store.adjustment');
    Route::get('/store/count', [RestaurantNavigationController::class, 'placeholder'])->name('store.count');
    Route::get('/store/movement', [RestaurantNavigationController::class, 'placeholder'])->name('store.movement');

    // 4.1 Store Locations
    Route::get('/locations', [RestaurantLocationController::class, 'index'])->name('locations.index');
    Route::post('/locations', [RestaurantLocationController::class, 'store'])->name('locations.store');
    Route::put('/locations/{location}', [RestaurantLocationController::class, 'update'])->name('locations.update');
    Route::post('/locations/{location}/default', [RestaurantLocationController::class, 'setDefaultSalesLocation'])->name('locations.default');

    // 5. Purchasing
    Route::get('/purchasing/suppliers', [RestaurantPurchasingController::class, 'suppliers'])->name('purchasing.suppliers');
    Route::post('/purchasing/suppliers', [RestaurantPurchasingController::class, 'storeSupplier'])->name('purchasing.suppliers.store');

    Route::get('/purchasing/requests', [RestaurantPurchasingController::class, 'index'])->name('purchasing.requests');
    Route::get('/purchasing/requests/create', [RestaurantPurchasingController::class, 'createRequest'])->name('purchasing.requests.create');
    Route::get('/purchasing/requests/{id}/edit', [RestaurantPurchasingController::class, 'editRequest'])->name('purchasing.requests.edit');
    Route::put('/purchasing/requests/{id}', [RestaurantPurchasingController::class, 'updateRequest'])->name('purchasing.requests.update');
    Route::post('/purchasing/requests', [RestaurantPurchasingController::class, 'storeRequest'])->name('purchasing.store-request');
    Route::post('/purchasing/submit-draft/{id}', [RestaurantPurchasingController::class, 'submitDraft'])->name('purchasing.submit-draft');
    Route::post('/purchasing/receive/{id}', [RestaurantPurchasingController::class, 'receive'])->name('purchasing.receive');

    Route::get('/purchasing/orders', [RestaurantPurchasingController::class, 'orders'])->name('purchasing.orders');
    Route::get('/purchasing/received', [RestaurantNavigationController::class, 'placeholder'])->name('purchasing.received');

    // 6. Cash & Shifts
    Route::get('/shifts', [RestaurantShiftController::class, 'index'])->name('shifts'); // Current Shift
    Route::post('/shifts/open', [RestaurantShiftController::class, 'open'])->name('shifts.open'); // Open Shift
    Route::post('/shifts/{shift}/close', [RestaurantShiftController::class, 'close'])->name('shifts.close'); // Close Shift
    Route::get('/shifts/{shift}/report', [RestaurantShiftController::class, 'report'])->name('shifts.report'); // Z-Report / X-Report
    Route::get('/cash/transactions', [RestaurantNavigationController::class, 'placeholder'])->name('cash.transactions');
    Route::get('/expenses', [RestaurantExpenseController::class, 'index'])->name('expenses'); // Expenses
    Route::post('/expenses', [RestaurantExpenseController::class, 'store'])->name('expenses.store');
    Route::delete('/expenses/{expense}', [RestaurantExpenseController::class, 'destroy'])->name('expenses.destroy');
    Route::get('/cash/reconciliation', [RestaurantNavigationController::class, 'placeholder'])->name('cash.reconciliation');

    // 7. Customers
    Route::get('/customers', [RestaurantNavigationController::class, 'placeholder'])->name('customers.index');
    Route::get('/customers/history', [RestaurantNavigationController::class, 'placeholder'])->name('customers.history');

    // 8. Reports
    Route::get('/reports/sales', [RestaurantNavigationController::class, 'placeholder'])->name('reports.sales');
    Route::get('/reports/products', [RestaurantNavigationController::class, 'placeholder'])->name('reports.products');
    Route::get('/reports/inventory', [RestaurantNavigationController::class, 'placeholder'])->name('reports.inventory');
    Route::get('/reports/wastage', [RestaurantNavigationController::class, 'placeholder'])->name('reports.wastage');
    Route::get('/reports/purchasing', [RestaurantNavigationController::class, 'placeholder'])->name('reports.purchasing');
    Route::get('/reports/cashier', [RestaurantNavigationController::class, 'placeholder'])->name('reports.cashier');
    Route::get('/reports/shifts', [RestaurantNavigationController::class, 'placeholder'])->name('reports.shifts');
    Route::get('/reports/profit-loss', [RestaurantNavigationController::class, 'placeholder'])->name('reports.profit_loss');

    // 9. Approvals
    Route::get('/approvals/discounts', [RestaurantNavigationController::class, 'placeholder'])->name('approvals.discounts');
    Route::get('/approvals/voids', [RestaurantNavigationController::class, 'placeholder'])->name('approvals.voids');
    Route::get('/approvals/refunds', [RestaurantNavigationController::class, 'placeholder'])->name('approvals.refunds');
    Route::get('/approvals/stock', [RestaurantNavigationController::class, 'placeholder'])->name('approvals.stock');
    Route::get('/approvals/expenses', [RestaurantNavigationController::class, 'placeholder'])->name('approvals.expenses');
    Route::get('/approvals/purchases', [RestaurantNavigationController::class, 'placeholder'])->name('approvals.purchases');

    // 10. Audit
    Route::get('/audit/activity', [RestaurantNavigationController::class, 'placeholder'])->name('audit.activity');
    Route::get('/audit/sales', [RestaurantNavigationController::class, 'placeholder'])->name('audit.sales');
    Route::get('/audit/cash', [RestaurantNavigationController::class, 'placeholder'])->name('audit.cash');
    Route::get('/audit/inventory', [RestaurantNavigationController::class, 'placeholder'])->name('audit.inventory');
    Route::get('/audit/approvals', [RestaurantNavigationController::class, 'placeholder'])->name('audit.approvals');

    // 11. Settings
    Route::get('/settings/restaurant', [RestaurantNavigationController::class, 'placeholder'])->name('settings.restaurant');
    Route::get('/settings/products', [RestaurantNavigationController::class, 'placeholder'])->name('settings.products');
    Route::get('/settings/categories', [RestaurantNavigationController::class, 'placeholder'])->name('settings.categories');
    Route::get('/settings/payment-methods', [RestaurantNavigationController::class, 'placeholder'])->name('settings.payment_methods');
    Route::get('/settings/tax', [RestaurantNavigationController::class, 'placeholder'])->name('settings.tax');
    Route::get('/settings/users', [RestaurantNavigationController::class, 'placeholder'])->name('settings.users');
    Route::get('/settings/system', [RestaurantNavigationController::class, 'placeholder'])->name('settings.system');
});

Route::middleware(['auth', EnsureActiveContext::class])->prefix('cleaning')->name('cleaning.')->group(function () {
    Route::get('/dashboard', [CleaningDashboardController::class, 'index'])->name('dashboard');
    Route::post('/workers/{worker}/assign', [CleaningWorkerController::class, 'assign'])->name('workers.assign');
    Route::resource('workers', CleaningWorkerController::class);

    Route::get('/timesheets', [CleaningTimesheetController::class, 'index'])->name('timesheets.index');

    Route::get('/daily-control', [CleaningDailyControlController::class, 'index'])->name('daily-control.index')->middleware('supervisor');
    Route::get('/daily-control/history', [CleaningDailyControlController::class, 'history'])->name('daily-control.history')->middleware('supervisor');
    Route::post('/daily-control', [CleaningDailyControlController::class, 'store'])->name('daily-control.store')->middleware('supervisor');
    Route::post('/daily-control/{dailyControl}/workforce', [CleaningDailyControlController::class, 'updateWorkforceCheck'])->name('daily-control.workforce')->middleware('supervisor');
    Route::post('/daily-control/{dailyControl}/workforce/{workerAttendance}/checkout', [CleaningDailyControlController::class, 'checkoutWorker'])->name('daily-control.workforce.checkout')->middleware('supervisor');
    Route::post('/daily-control/{dailyControl}/workforce/checkout-all', [CleaningDailyControlController::class, 'bulkCheckoutWorkers'])->name('daily-control.workforce.checkout-all')->middleware('supervisor');

    // Work Activities & Issues & Submission
    Route::post('/daily-control/{dailyControl}/activities', [CleaningDailyControlController::class, 'storeActivity'])->name('daily-control.activities.store')->middleware('supervisor');
    Route::patch('/daily-control/activities/{workActivity}/status', [CleaningDailyControlController::class, 'updateActivityStatus'])->name('daily-control.activities.status')->middleware('supervisor');
    Route::put('/daily-control/activities/{workActivity}/items', [CleaningDailyControlController::class, 'updateActivityItems'])->name('daily-control.activities.items.update')->middleware('supervisor');
    Route::patch('/daily-control/activities/{workActivity}/verification', [CleaningDailyControlController::class, 'updateActivityVerification'])->name('daily-control.activities.verification')->middleware('supervisor');
    Route::post('/daily-control/{dailyControl}/issues', [CleaningDailyControlController::class, 'storeIssue'])->name('daily-control.issues.store')->middleware('supervisor');
    Route::patch('/daily-control/issues/{operationalIssue}/resolve', [CleaningDailyControlController::class, 'resolveIssue'])->name('daily-control.issues.resolve')->middleware('supervisor');
    Route::post('/daily-control/{dailyControl}/submit', [CleaningDailyControlController::class, 'submit'])->name('daily-control.submit')->middleware('supervisor');

    // Operations & Supervisor Monitoring (Cleaning Manager)
    Route::middleware('coordinator')->group(function () {
        Route::get('/operations', [CleaningCoordinatorController::class, 'operations'])->name('coordinator.operations');
        Route::get('/supervisors', [CleaningCoordinatorController::class, 'supervisors'])->name('coordinator.supervisors');
        Route::get('/reports', [CleaningCoordinatorController::class, 'reports'])->name('coordinator.reports');

        Route::post('/assignments', [CleaningSupervisorAssignmentController::class, 'store'])->name('assignments.store');
        Route::patch('/assignments/{assignment}/end', [CleaningSupervisorAssignmentController::class, 'end'])->name('assignments.end');

        // Service Templates
        Route::post('/templates', [CleaningServiceTemplateController::class, 'store'])->name('templates.store');
        Route::put('/templates/{template}', [CleaningServiceTemplateController::class, 'update'])->name('templates.update');
    });

    // Central Cleaning Store (Store Keeper & Cleaning Manager)
    Route::prefix('store')->name('store.')->group(function () {
        Route::get('/', [CleaningStoreController::class, 'index'])->name('index');
        Route::get('/receive', [CleaningStoreController::class, 'receiveForm'])->name('receive');
        Route::post('/receive', [CleaningStoreController::class, 'receive'])->name('receive.post');
        Route::get('/issue', [CleaningStoreController::class, 'issueForm'])->name('issue');
        Route::post('/issue', [CleaningStoreController::class, 'issue'])->name('issue.post');
        Route::get('/movements', [CleaningStoreController::class, 'movements'])->name('movements');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function (ContextManager $contextManager) {
        $bu = BusinessUnit::find($contextManager->getActiveBusinessUnitId());
        if ($bu && in_array($bu->category, ['Cleaning Operations', 'Facilities Management'])) {
            return redirect()->route('cleaning.dashboard');
        }
        if ($bu && in_array($bu->category, ['Restaurant & Food Services', 'On-board Train Catering'])) {
            return redirect()->route('restaurant.dashboard');
        }

        return redirect()->route('management.dashboard');
    })->name('dashboard')->middleware(EnsureActiveContext::class);

    Route::get('/context/switch', [ContextController::class, 'showSwitcher'])->name('context.switcher');
    Route::post('/context/switch', [ContextController::class, 'switchContext'])->name('context.switch');
});
