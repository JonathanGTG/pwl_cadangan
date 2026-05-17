<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Manager\DashboardController as ManagerDashboard;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Kasir\DashboardController as KasirDashboard;
use App\Http\Controllers\Manager\BranchController;
use App\Http\Controllers\Manager\MenuController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\StockRequestController as AdminStockRequest;
use App\Http\Controllers\Manager\StockRequestController as ManagerStockRequest;
use App\Http\Controllers\Admin\ExpenseController as AdminExpense;
use App\Http\Controllers\Manager\ExpenseController as ManagerExpense;
use App\Http\Controllers\Manager\PromotionController as ManagerPromo;
use App\Http\Controllers\Admin\PromotionController as AdminPromo;
use App\Http\Controllers\Kasir\TransactionController as KasirTransaction;
use App\Http\Controllers\Admin\TransactionController as AdminTransaction;
use App\Http\Controllers\Manager\ReportController as ManagerReport;
use App\Http\Controllers\Admin\ReportController as AdminReport;
use App\Http\Controllers\Manager\TransactionController as ManagerTransaction;
use App\Http\Controllers\Kasir\ShiftController;
use App\Http\Controllers\Admin\ShiftScheduleController;

Route::get('/', function () {
    if (!auth()->check()) return redirect('/login');

    return match(auth()->user()->role) {
        'manager' => redirect('/manager/dashboard'),
        'admin'   => redirect('/admin/dashboard'),
        'kasir'   => redirect('/kasir/transactions'),
        default   => redirect('/login'),
    };
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ── MANAGER ──────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:manager'])
    ->prefix('manager')
    ->name('manager.')
    ->group(function () {
        Route::get('/dashboard', [ManagerDashboard::class, 'index'])->name('dashboard');

        // Cabang
        Route::resource('branches', BranchController::class);
        Route::post('branches/{id}/restore', [BranchController::class, 'restore'])
             ->name('branches.restore');
        Route::post('branches/{branch}/add-kasir', [BranchController::class, 'addKasir'])
            ->name('branches.add-kasir');

        // Menu
        Route::resource('menus', MenuController::class)->except(['show']);
        Route::post('menus/{id}/restore', [MenuController::class, 'restore'])
             ->name('menus.restore');
        Route::get('menus/{menu}/recipe', [MenuController::class, 'recipe'])
            ->name('menus.recipe');

        // ── BARU: Master Bahan Baku ──────────────────────────────────────────
        Route::get('menus/ingredients', [MenuController::class, 'ingredients'])
             ->name('menus.ingredients');
        Route::post('menus/ingredients', [MenuController::class, 'storeIngredient'])
             ->name('menus.ingredients.store');
        Route::put('menus/ingredients/{ingredient}', [MenuController::class, 'updateIngredient'])
             ->name('menus.ingredients.update');
        // ────────────────────────────────────────────────────────────────────

        // Stock Requests
        Route::get('stock-requests', [ManagerStockRequest::class, 'index'])->name('stock-requests.index');
        Route::get('stock-requests/{stockRequest}', [ManagerStockRequest::class, 'show'])->name('stock-requests.show');
        Route::post('stock-requests/{stockRequest}/approve', [ManagerStockRequest::class, 'approve'])->name('stock-requests.approve');
        Route::post('stock-requests/{stockRequest}/reject', [ManagerStockRequest::class, 'reject'])->name('stock-requests.reject');
        Route::post('stock-requests/{stockRequest}/confirm-delivery', [ManagerStockRequest::class, 'confirmDelivery'])
            ->name('stock-requests.confirm-delivery');

        // Expenses
        Route::get('expenses', [ManagerExpense::class, 'index'])->name('expenses.index');
        Route::post('expenses/{expense}/verify', [ManagerExpense::class, 'verify'])->name('expenses.verify');
        Route::post('expenses/{expense}/reject', [ManagerExpense::class, 'reject'])->name('expenses.reject');

        // Promotions
        Route::resource('promotions', ManagerPromo::class)->except(['show']);
        Route::post('promotions/{promotion}/approve', [ManagerPromo::class, 'approvePromo'])->name('promotions.approve');
        Route::post('promotions/{promotion}/reject', [ManagerPromo::class, 'rejectPromo'])->name('promotions.reject');

        // Reports & Transactions
        Route::get('reports', [ManagerReport::class, 'index'])->name('reports.index');
        Route::get('transactions', [ManagerTransaction::class, 'index'])->name('transactions.index');
    });

// ── ADMIN (Admin Cabang) ──────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

        // Stok
        Route::get('stocks', [StockController::class, 'index'])->name('stocks.index');

        // Stock Requests
        Route::resource('stock-requests', AdminStockRequest::class)
             ->only(['index', 'create', 'store']);
        Route::post('stock-requests/{stockRequest}/confirm-delivery', [AdminStockRequest::class, 'confirmDelivery'])
            ->name('stock-requests.confirm-delivery');

        // Expenses
        Route::resource('expenses', AdminExpense::class)
            ->only(['index', 'create', 'store', 'destroy']);

        // Promotions
        Route::resource('promotions', AdminPromo::class)
             ->only(['index', 'create', 'store', 'destroy']);

        // Transactions
        Route::get('transactions', [AdminTransaction::class, 'index'])->name('transactions.index');
        Route::post('transactions/{transaction}/cancel', [AdminTransaction::class, 'cancel'])
             ->name('transactions.cancel');
        Route::post('transactions/{transaction}/reject-cancel', [AdminTransaction::class, 'rejectCancel'])
             ->name('transactions.reject-cancel');

        // Reports
        Route::get('reports', [AdminReport::class, 'index'])->name('reports.index');
        Route::get('reports/export', [AdminReport::class, 'export'])->name('reports.export');

        // Shifts
        Route::get('shifts', [ShiftScheduleController::class, 'index'])->name('shifts.index');
        Route::post('shifts', [ShiftScheduleController::class, 'store'])->name('shifts.store');
        Route::delete('shifts/{shiftSchedule}', [ShiftScheduleController::class, 'destroy'])->name('shifts.destroy');
    });

// ── KASIR ─────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:kasir'])
    ->prefix('kasir')
    ->name('kasir.')
    ->group(function () {
        Route::get('/dashboard', [KasirDashboard::class, 'index'])->name('dashboard');

        // Transactions
        Route::get('transactions', [KasirTransaction::class, 'index'])->name('transactions.index');
        Route::post('transactions', [KasirTransaction::class, 'store'])->name('transactions.store');
        Route::get('transactions/{transaction}', [KasirTransaction::class, 'show'])->name('transactions.show');
        Route::post('transactions/{transaction}/request-cancel', [KasirTransaction::class, 'requestCancel'])
             ->name('transactions.request-cancel');

        // Shifts
        Route::get('shifts', [ShiftController::class, 'index'])->name('shifts.index');
        Route::post('shifts/clock-in', [ShiftController::class, 'clockIn'])->name('shifts.clock-in');
        Route::post('shifts/{shift}/clock-out', [ShiftController::class, 'clockOut'])->name('shifts.clock-out');
    });

require __DIR__ . '/auth.php';
