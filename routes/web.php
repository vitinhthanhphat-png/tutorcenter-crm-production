<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CashbookController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClassesController;
use App\Http\Controllers\DispatchRequestController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\LeadsController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\StudentsController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\StudentPortalController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\PdfExportController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\InstallController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Installer Routes (no auth required)
|--------------------------------------------------------------------------
*/
Route::prefix('install')->name('install.')->middleware('web')->group(function () {
    Route::get('/',         [InstallController::class, 'index'])->name('index');
    Route::get('/database', [InstallController::class, 'database'])->name('database');
    Route::post('/database',[InstallController::class, 'saveDatabase'])->name('save-database');
    Route::get('/admin',    [InstallController::class, 'admin'])->name('admin');
    Route::post('/admin',   [InstallController::class, 'saveAdmin'])->name('save-admin');
    Route::get('/settings', [InstallController::class, 'settings'])->name('settings');
    Route::post('/settings',[InstallController::class, 'saveSettings'])->name('save-settings');
    Route::get('/finalize', [InstallController::class, 'finalize'])->name('finalize');
    Route::post('/execute', [InstallController::class, 'execute'])->name('execute');
    Route::get('/complete', [InstallController::class, 'complete'])->name('complete');
});

Route::view('/', 'welcome');

/*
|--------------------------------------------------------------------------
| Authenticated + Tenant-scoped Routes
|--------------------------------------------------------------------------
| All routes below require login AND a valid tenant context.
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'tenant'])->group(function () {

    // Dashboard — all authenticated roles
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Classes — view: all | create/edit/delete: manager only
    Route::get('classes', [ClassesController::class, 'index'])->name('classes.index');

    Route::middleware('role:center_manager,super_admin')->group(function () {
        Route::get('classes/create', [ClassesController::class, 'create'])->name('classes.create');
        Route::post('classes', [ClassesController::class, 'store'])->name('classes.store');
        Route::get('classes/{class}/edit', [ClassesController::class, 'edit'])->name('classes.edit');
        Route::put('classes/{class}', [ClassesController::class, 'update'])->name('classes.update');
        Route::patch('classes/{class}', [ClassesController::class, 'update']);
        Route::delete('classes/{class}', [ClassesController::class, 'destroy'])->name('classes.destroy');
    });

    Route::get('classes/{class}', [ClassesController::class, 'show'])->name('classes.show');

    // Attendance — teacher + manager
    Route::middleware('role:teacher,center_manager,super_admin')->group(function () {
        Route::get('attendance/{session}', [AttendanceController::class, 'show'])->name('attendance.show');
        Route::post('attendance/{session}', [AttendanceController::class, 'store'])->name('attendance.store');
    });

    // Students & CRM — manager + accountant (not teacher)
    Route::middleware('role:center_manager,accountant,super_admin')->group(function () {
        Route::resource('students', StudentsController::class)->names('students');
    });

    // Finance — accountant + manager only
    Route::middleware('role:accountant,center_manager,super_admin')->group(function () {
        Route::get('finance', [FinanceController::class, 'index'])->name('finance.index');
        Route::get('finance/invoices', [FinanceController::class, 'invoices'])->name('finance.invoices');
        Route::post('finance/invoices', [FinanceController::class, 'storeInvoice'])->name('finance.invoices.store');
        Route::get('finance/report', [FinanceController::class, 'report'])->name('finance.report');
    });

    // Enrollments — manager + accountant
    Route::middleware('role:center_manager,accountant,super_admin')->group(function () {
        Route::post('enrollments', [EnrollmentController::class, 'store'])->name('enrollments.store');
        // Transfer lớp
        Route::get('enrollments/{enrollment}/transfer',  [TransferController::class, 'create'])->name('enrollments.transfer.create');
        Route::post('enrollments/{enrollment}/transfer', [TransferController::class, 'store'])->name('enrollments.transfer.store');
    });

    // Calendar (TKB) — all roles
    Route::get('calendar',        [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('calendar/events', [CalendarController::class, 'events'])->name('calendar.events');

    // Dispatch Requests — manager can create/cancel
    Route::middleware('role:center_manager,super_admin')->group(function () {
        Route::get('dispatch-requests', [DispatchRequestController::class, 'index'])->name('dispatch-requests.index');
        Route::get('dispatch-requests/create', [DispatchRequestController::class, 'create'])->name('dispatch-requests.create');
        Route::post('dispatch-requests', [DispatchRequestController::class, 'store'])->name('dispatch-requests.store');
        Route::patch('dispatch-requests/{dispatchRequest}/cancel', [DispatchRequestController::class, 'cancel'])->name('dispatch-requests.cancel');
    });

    // CRM Leads — manager + operations
    Route::middleware('role:center_manager,operations,accountant,super_admin')->group(function () {
        Route::get('leads', [LeadsController::class, 'index'])->name('leads.index');
        Route::get('leads/create', [LeadsController::class, 'create'])->name('leads.create');
        Route::post('leads', [LeadsController::class, 'store'])->name('leads.store');
        Route::get('leads/{lead}', [LeadsController::class, 'show'])->name('leads.show');
        Route::get('leads/{lead}/edit', [LeadsController::class, 'edit'])->name('leads.edit');
        Route::put('leads/{lead}', [LeadsController::class, 'update'])->name('leads.update');
        Route::delete('leads/{lead}', [LeadsController::class, 'destroy'])->name('leads.destroy');
        Route::post('leads/{lead}/convert', [LeadsController::class, 'convert'])->name('leads.convert');
    });

    // Cashbook — accountant + manager
    Route::middleware('role:accountant,center_manager,super_admin')->group(function () {
        Route::get('cashbook', [CashbookController::class, 'index'])->name('cashbook.index');
        Route::post('cashbook', [CashbookController::class, 'store'])->name('cashbook.store');
        Route::delete('cashbook/{cashbook}', [CashbookController::class, 'destroy'])->name('cashbook.destroy');
    });

    // Payroll — manager + accountant
    Route::middleware('role:center_manager,accountant,super_admin')->group(function () {
        Route::get('payroll', [PayrollController::class, 'index'])->name('payroll.index');
        Route::post('payroll/generate', [PayrollController::class, 'generate'])->name('payroll.generate');
        Route::patch('payroll/{payroll}/confirm', [PayrollController::class, 'confirm'])->name('payroll.confirm');
        Route::patch('payroll/{payroll}/mark-paid', [PayrollController::class, 'markPaid'])->name('payroll.markPaid');
        Route::delete('payroll/{payroll}', [PayrollController::class, 'destroy'])->name('payroll.destroy');
    });

    // Export CSV — manager + accountant
    Route::middleware('role:center_manager,accountant,super_admin')->group(function () {
        Route::get('export',                 [ExportController::class, 'index']     )->name('export.index');
        Route::get('export/students',        [ExportController::class, 'students']  )->name('export.students');
        Route::get('export/cashbook',        [ExportController::class, 'cashbook']  )->name('export.cashbook');
        Route::get('export/payroll',         [ExportController::class, 'payroll']   )->name('export.payroll');
        Route::get('export/attendance',      [ExportController::class, 'attendance'])->name('export.attendance');
    });

    // PDF Export — manager + accountant
    Route::middleware('role:center_manager,accountant,super_admin')->group(function () {
        Route::get('pdf/students/{student}', [PdfExportController::class, 'studentPdf'] )->name('pdf.student');
        Route::get('pdf/invoices/{invoice}', [PdfExportController::class, 'invoicePdf'] )->name('pdf.invoice');
        Route::get('pdf/payroll',            [PdfExportController::class, 'payrollPdf'] )->name('pdf.payroll');
        Route::get('pdf/classes/{classroom}',[PdfExportController::class, 'attendancePdf'])->name('pdf.attendance');
    });

    // Grade Report — teacher + manager
    Route::middleware('role:teacher,center_manager,super_admin')->group(function () {
        Route::get('grades', [GradeController::class, 'list'])->name('grades.list');
        Route::get('classes/{classroom}/grades', [GradeController::class, 'index'])->name('grades.class');
        Route::get('students/{student}/grades',  [GradeController::class, 'show'] )->name('grades.student');
    });

    // Reservation — manager + accountant
    Route::middleware('role:center_manager,accountant,super_admin')->group(function () {
        Route::get('enrollments/{enrollment}/reserve',  [\App\Http\Controllers\ReservationController::class, 'create'])->name('reservations.create');
        Route::post('enrollments/{enrollment}/reserve', [\App\Http\Controllers\ReservationController::class, 'store'] )->name('reservations.store');
        Route::patch('enrollments/{enrollment}/reactivate', [\App\Http\Controllers\ReservationController::class, 'reactivate'])->name('reservations.reactivate');
    });
});

Route::middleware(['auth', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/', [AdminController::class, 'index'])->name('index');

    // Tenant management
    Route::get('tenants', [AdminController::class, 'tenants'])->name('tenants');
    Route::get('tenants/create', [AdminController::class, 'tenantCreate'])->name('tenants.create');
    Route::post('tenants', [AdminController::class, 'tenantStore'])->name('tenants.store');
    Route::get('tenants/{tenant}', [AdminController::class, 'tenantShow'])->name('tenants.show');
    Route::get('tenants/{tenant}/edit', [AdminController::class, 'tenantEdit'])->name('tenants.edit');
    Route::put('tenants/{tenant}', [AdminController::class, 'tenantUpdate'])->name('tenants.update');
    Route::patch('tenants/{tenant}/toggle', [AdminController::class, 'tenantToggle'])->name('tenants.toggle');
    Route::delete('tenants/{tenant}', [AdminController::class, 'tenantDestroy'])->name('tenants.destroy');

    // Branch management (cross-tenant)
    Route::get('branches', [AdminController::class, 'branches'])->name('branches');
    Route::get('branches/create', [AdminController::class, 'branchCreate'])->name('branches.create');
    Route::post('branches', [AdminController::class, 'branchStore'])->name('branches.store');
    Route::get('branches/{branch}/edit', [AdminController::class, 'branchEdit'])->name('branches.edit');
    Route::put('branches/{branch}', [AdminController::class, 'branchUpdate'])->name('branches.update');
    Route::delete('branches/{branch}', [AdminController::class, 'branchDestroy'])->name('branches.destroy');

    // User management (cross-tenant)
    Route::get('users', [AdminController::class, 'users'])->name('users');
    Route::get('users/create', [AdminController::class, 'userCreate'])->name('users.create');
    Route::post('users', [AdminController::class, 'userStore'])->name('users.store');
    Route::get('users/{user}/edit', [AdminController::class, 'userEdit'])->name('users.edit');
    Route::put('users/{user}', [AdminController::class, 'userUpdate'])->name('users.update');
    Route::patch('users/{user}/toggle', [AdminController::class, 'userToggle'])->name('users.toggle');
    Route::post('users/{user}/reset-password', [AdminController::class, 'userResetPassword'])->name('users.reset-password');
    Route::delete('users/{user}', [AdminController::class, 'userDestroy'])->name('users.destroy');
    Route::post('users/{user}/assignments', [AdminController::class, 'userAssignmentAdd'])->name('users.assignments.add');
    Route::delete('users/{user}/assignments/{assignment}', [AdminController::class, 'userAssignmentRemove'])->name('users.assignments.remove');

    // System settings
    Route::get('settings', [AdminController::class, 'settings'])->name('settings');

    // Dispatch Requests (cross-tenant approvals)
    Route::get('dispatch-requests', [AdminController::class, 'dispatchRequests'])->name('dispatch-requests');
    Route::patch('dispatch-requests/{dispatchRequest}/approve', [AdminController::class, 'approveRequest'])->name('dispatch-requests.approve');
    Route::patch('dispatch-requests/{dispatchRequest}/reject', [AdminController::class, 'rejectRequest'])->name('dispatch-requests.reject');

    // Staff Assignments (cross-tenant)
    Route::get('assignments',  [AdminController::class, 'assignments']  )->name('assignments');
    Route::delete('assignments/{assignment}', [AdminController::class, 'revokeAssignment'])->name('assignments.revoke');

    // Audit Log (super_admin only)
    Route::get('audit', [AuditLogController::class, 'index'])->name('audit');
});

// Profile (Breeze)
Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// ── Student / Parent Self-Service Portal ──────────────────────────────────
Route::middleware(['auth', 'verified'])
    ->prefix('portal')
    ->name('portal.')
    ->group(function () {
        Route::get('/', [StudentPortalController::class, 'index'])->name('index');
        Route::get('/attendance', [StudentPortalController::class, 'attendance'])->name('attendance');
        Route::get('/invoices', [StudentPortalController::class, 'invoices'])->name('invoices');
    });

require __DIR__ . '/auth.php';
