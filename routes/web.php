<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\AdmissionController;
use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\InstitutionController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\IncomeExpenseReportController;
use App\Http\Controllers\Admin\LedgerController;
use App\Http\Controllers\Admin\SubLedgerController;
use App\Http\Controllers\Admin\FundController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\CashierController;
use App\Http\Controllers\Admin\MonthSettingController;
use App\Http\Controllers\Admin\FeeSettingController;
use App\Http\Controllers\Admin\FeeGroupController;
use App\Http\Controllers\Admin\FeeCollectionController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\FeeTypeController;
use App\Http\Controllers\Admin\StudentFeeCollectionController;
use App\Http\Controllers\Admin\FeeReportController;

Route::get('/', function () {
    return view('welcome');
});

// লগইন করার পর ড্যাশবোর্ড রিডাইরেক্ট
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        if ($user->is_super_admin) {
            return redirect()->route('super-admin.dashboard');
        } elseif ($user->is_soft_admin) {
            return redirect()->route('soft-admin.dashboard');
        } elseif ($user->is_madrasa_admin) {
            return redirect()->route('madrasa-admin.dashboard');
        } elseif ($user->is_teacher) {
            return redirect()->route('teacher.dashboard');
        } elseif ($user->is_student) {
            return redirect()->route('student.dashboard');
        } elseif ($user->is_guardian) {
            return redirect()->route('guardian.dashboard');
        }
        
        return redirect('/');
    })->name('dashboard');
});

// ============ Location AJAX Routes (Auth ছাড়া) ============
Route::get('get-districts/{divisionId}', [UserController::class, 'getDistricts'])->name('get.districts');
Route::get('get-upazilas/{districtId}', [UserController::class, 'getUpazilas'])->name('get.upazilas');

// ============ সুপার অ্যাডমিন এবং অ্যাডমিন এর জন্য ড্যাশবোর্ড রুটস ============
Route::middleware(['auth'])->group(function () {
    
    // ✅ সুপার অ্যাডমিন ড্যাশবোর্ড
    Route::middleware(['role:super_admin'])->group(function () {
        Route::get('/super-admin/dashboard', [AdminDashboardController::class, 'index'])->name('super-admin.dashboard');
    });
    
    // ✅ সফট অ্যাডমিন ড্যাশবোর্ড
    Route::middleware(['role:soft_admin'])->group(function () {
        Route::get('/soft-admin/dashboard', [AdminDashboardController::class, 'index'])->name('soft-admin.dashboard');
    });
    
    // ✅ মাদ্রাসা অ্যাডমিন ড্যাশবোর্ড
    Route::middleware(['role:madrasa_admin'])->group(function () {
        Route::get('/madrasa-admin/dashboard', [AdminDashboardController::class, 'index'])->name('madrasa-admin.dashboard');
    });
    
    // ✅ শিক্ষক ড্যাশবোর্ড
    Route::middleware(['role:teacher'])->group(function () {
        Route::get('/teacher/dashboard', function () {
            return view('teacher.dashboard');
        })->name('teacher.dashboard');
    });
    
    // ✅ শিক্ষার্থী ড্যাশবোর্ড
    Route::middleware(['role:student'])->group(function () {
        Route::get('/student/dashboard', function () {
            return view('student.dashboard');
        })->name('student.dashboard');
    });
    
    // ✅ অভিভাবক ড্যাশবোর্ড
    Route::middleware(['role:guardian'])->group(function () {
        Route::get('/guardian/dashboard', function () {
            return view('guardian.dashboard');
        })->name('guardian.dashboard');
    });

    // ✅ Common (All Auth Users)
    Route::middleware(['role:admin,super_admin,soft_admin,madrasa_admin,teacher,student,guardian'])->group(function () {
        Route::get('dashboard/profile', [ProfileController::class, 'adminProfile'])->name('dashboard.profile');
        Route::post('dashboard/profile', [ProfileController::class, 'adminProfileUpdate'])->name('dashboard.profile.update');
        Route::get('dashboard/manage-account', [ProfileController::class, 'adminAccount'])->name('dashboard.account');
        Route::post('dashboard/manage-account/password', [ProfileController::class, 'adminPasswordUpdate'])->name('dashboard.account.password');
    });
    
    // ✅ Manager+ (admin/super_admin/soft_admin)
    Route::middleware(['role:super_admin,soft_admin,madrasa_admin'])->group(function () {
        Route::get('dashboard/notifications/poll', [NotificationController::class, 'poll'])->name('dashboard.notifications.poll');
        Route::post('dashboard/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('dashboard.notifications.markAllRead');
        Route::post('dashboard/notifications/clear-all', [NotificationController::class, 'clearAll'])->name('dashboard.notifications.clearAll');
    });
    
    // ✅ Admin+ (super_admin/soft_admin)
    Route::middleware(['role:super_admin,soft_admin'])->group(function () {
        Route::get('dashboard/settings', [SettingController::class, 'index'])->name('dashboard.settings.index');
    });
    
    // ==================== User Management ====================
    Route::middleware(['role:super-admin,soft-admin,madrasa-admin'])->prefix('dashboard')->group(function(){
        Route::get('users', [UserController::class, 'index'])->name('dashboard.user.index');
        Route::get('users/create', [UserController::class, 'create'])->name('dashboard.user.create');
        Route::post('users/store', [UserController::class, 'store'])->name('dashboard.user.store');
        Route::get('users/{id}', [UserController::class, 'show'])->name('dashboard.user.show');
        Route::get('users/{id}/edit', [UserController::class, 'edit'])->name('dashboard.user.edit');
        Route::put('users/{id}', [UserController::class, 'update'])->name('dashboard.user.update');
        Route::delete('users/{id}', [UserController::class, 'destroy'])->name('dashboard.user.destroy');
        Route::patch('users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('dashboard.user.toggle-status');
        Route::get('users/advanced-search', [UserController::class, 'advancedSearch'])->name('dashboard.user.advanced-search');
        Route::get('users/search', [UserController::class, 'search'])->name('dashboard.user.search');
   Route::post('user/preview-id', [UserController::class, 'previewId'])->name('dashboard.user.preview-id');
        });
    
    // ==================== Student Management ====================
    Route::middleware(['role:super_admin,soft_admin,madrasa_admin'])->prefix('dashboard')->group(function(){
        Route::get('students', [StudentController::class, 'index'])->name('dashboard.students.index');
        Route::get('students/create', [StudentController::class, 'create'])->name('dashboard.students.create');
        Route::post('students/store', [StudentController::class, 'store'])->name('dashboard.students.store');
        Route::get('students/{id}', [StudentController::class, 'show'])->name('dashboard.students.show');
        Route::get('students/{id}/edit', [StudentController::class, 'edit'])->name('dashboard.students.edit');
        Route::put('students/{id}', [StudentController::class, 'update'])->name('dashboard.students.update');
        Route::delete('students/{id}', [StudentController::class, 'destroy'])->name('dashboard.students.destroy');
        Route::get('students/search', [StudentController::class, 'search'])->name('dashboard.students.search');
    });
    
    // ==================== Admission Management ====================
// ==================== Admission Management ====================
Route::middleware(['role:super_admin,soft_admin,madrasa_admin'])
    ->prefix('dashboard')
    ->group(function(){

    // SEARCH ROUTE FIRST
    Route::get('admissions/search', [AdmissionController::class, 'search'])
        ->name('dashboard.admissions.search');

    Route::get('admissions/fee-structure/{classId}', [AdmissionController::class, 'getFeeStructure'])
        ->name('dashboard.admissions.fee-structure');

    Route::get('admissions', [AdmissionController::class, 'index'])
        ->name('dashboard.admissions.index');

    Route::get('admissions/create', [AdmissionController::class, 'create'])
        ->name('dashboard.admissions.create');

    Route::post('admissions/store', [AdmissionController::class, 'store'])
        ->name('dashboard.admissions.store');

    // {id} ROUTES BELOW
    Route::get('admissions/{id}', [AdmissionController::class, 'show'])
        ->name('dashboard.admissions.show');

    Route::get('admissions/{id}/edit', [AdmissionController::class, 'edit'])
        ->name('dashboard.admissions.edit');

    Route::put('admissions/{id}', [AdmissionController::class, 'update'])
        ->name('dashboard.admissions.update');

    Route::delete('admissions/{id}', [AdmissionController::class, 'destroy'])
        ->name('dashboard.admissions.destroy');

    Route::patch('admissions/{id}/toggle-status', [AdmissionController::class, 'toggleStatus'])
        ->name('dashboard.admissions.toggle-status');
    
});
    Route::middleware(['role:super_admin,soft_admin,madrasa_admin'])->prefix('dashboard')->group(function(){
    // 🏫 Institution Routes
    Route::get('institutions', [InstitutionController::class, 'index'])->name('dashboard.institutions.index');
    Route::post('institutions', [InstitutionController::class, 'store'])->name('dashboard.institutions.store');

    // AJAX Edit Modal
    Route::get('institutions/{id}/edit', [InstitutionController::class, 'edit'])->name('institutions.edit');
    Route::put('institutions/{id}', [InstitutionController::class, 'update'])->name('institutions.update');

    // Delete
    Route::delete('institutions/{id}', [InstitutionController::class, 'destroy'])->name('dashboard.institutions.destroy');

});
    // ==================== Academic Year Management ====================
    Route::middleware(['role:super_admin,soft_admin,madrasa_admin'])->prefix('dashboard')->group(function(){
        Route::get('academic-years', [AcademicYearController::class, 'index'])->name('dashboard.academic-years.index');
        Route::get('academic-years/create', [AcademicYearController::class, 'create'])->name('dashboard.academic-years.create');
        Route::post('academic-years/store', [AcademicYearController::class, 'store'])->name('dashboard.academic-years.store');
        Route::get('academic-years/{id}', [AcademicYearController::class, 'show'])->name('dashboard.academic-years.show');
        Route::get('academic-years/{id}/edit', [AcademicYearController::class, 'edit'])->name('dashboard.academic-years.edit');
        Route::put('academic-years/{id}', [AcademicYearController::class, 'update'])->name('dashboard.academic-years.update');
        Route::delete('academic-years/{id}', [AcademicYearController::class, 'destroy'])->name('dashboard.academic-years.destroy');
        Route::post('academic-years/{id}/toggle-status', [AcademicYearController::class, 'toggleStatus'])->name('dashboard.academic-years.toggle-status');
        Route::post('academic-years/{id}/set-current', [AcademicYearController::class, 'setCurrent'])->name('dashboard.academic-years.set-current');
        Route::post('academic-year/store-ajax', [AcademicYearController::class, 'storeAjax'])
        ->name('dashboard.academic-year.store-ajax');
        });
    
    // ==================== Class Management ====================
    Route::middleware(['role:super_admin,soft_admin,madrasa_admin'])->prefix('dashboard')->group(function(){
        Route::get('classes', [ClassController::class, 'index'])->name('dashboard.classes.index');
        Route::get('classes/create', [ClassController::class, 'create'])->name('dashboard.classes.create');
        Route::post('classes/store', [ClassController::class, 'store'])->name('dashboard.classes.store');
        Route::get('classes/{id}', [ClassController::class, 'show'])->name('dashboard.classes.show');
        Route::get('classes/{id}/edit', [ClassController::class, 'edit'])->name('dashboard.classes.edit');
        Route::put('classes/{id}', [ClassController::class, 'update'])->name('dashboard.classes.update');
        Route::delete('classes/{id}', [ClassController::class, 'destroy'])->name('dashboard.classes.destroy');
        Route::post('classes/{id}/toggle-status', [ClassController::class, 'toggleStatus'])->name('dashboard.classes.toggle-status');
        
        // AJAX Routes for Class
        Route::get('classes/{classId}/sections', [ClassController::class, 'getSections'])->name('dashboard.classes.sections');
        Route::get('get-all-classes', [ClassController::class, 'getAllClasses'])->name('dashboard.classes.all');
        Route::post('class/store-ajax', [ClassController::class, 'storeAjax'])->name('dashboard.class.store-ajax');
        });
    
    // ==================== Section Management ====================
    Route::middleware(['role:super_admin,soft_admin,madrasa_admin'])->prefix('dashboard')->group(function(){
        Route::get('sections', [SectionController::class, 'index'])->name('dashboard.sections.index');
        Route::get('sections/create', [SectionController::class, 'create'])->name('dashboard.sections.create');
        Route::post('sections/store', [SectionController::class, 'store'])->name('dashboard.sections.store');
        Route::get('sections/{id}', [SectionController::class, 'show'])->name('dashboard.sections.show');
        Route::get('sections/{id}/edit', [SectionController::class, 'edit'])->name('dashboard.sections.edit');
        Route::put('sections/{id}', [SectionController::class, 'update'])->name('dashboard.sections.update');
        Route::delete('sections/{id}', [SectionController::class, 'destroy'])->name('dashboard.sections.destroy');
        Route::post('sections/{id}/toggle-status', [SectionController::class, 'toggleStatus'])->name('dashboard.sections.toggle-status');
        
        // AJAX Routes for Section
        Route::get('get-sections/{classId}', [SectionController::class, 'getSectionsByClass'])->name('dashboard.sections.by-class');
        Route::post('sections/bulk-create', [SectionController::class, 'bulkCreate'])->name('dashboard.sections.bulk-create');
        Route::get('section-stats', [SectionController::class, 'getSectionStats'])->name('dashboard.sections.stats');
    });
    
    // ==================== Teacher View Routes ====================
    Route::middleware(['role:teacher'])->prefix('dashboard')->group(function(){
        Route::get('students', [StudentController::class, 'index'])->name('dashboard.students.index');
        Route::get('students/{id}', [StudentController::class, 'show'])->name('dashboard.students.show');
        Route::get('admissions', [AdmissionController::class, 'index'])->name('dashboard.admissions.index');
        Route::get('admissions/{id}', [AdmissionController::class, 'show'])->name('dashboard.admissions.show');
    });
    
    Route::middleware(['role:super_admin,soft_admin,madrasa_admin'])->prefix('dashboard')->group(function(){
    Route::get('/transactions', [TransactionController::class, 'index'])->name('dashboard.transactions.index');
    Route::post('/transactions/store', [TransactionController::class, 'store'])->name('dashboard.transactions.store');
    Route::delete('/transactions/{id}', [TransactionController::class, 'destroy'])->name('dashboard.transactions.destroy');
    Route::get('/reports/income-expense',[IncomeExpenseReportController::class, 'incomeExpense'])->name('dashboard.reports.income-expense');
Route::get('/fee-report', [FeeReportController::class, 'index'])
    ->name('dashboard.fee-report.index');
     Route::get('fee-report', [FeeReportController::class, 'index'])->name('dashboard.fee-report.index');
    Route::get('fee-report/print', [FeeReportController::class, 'print'])->name('dashboard.fee-report.print');
    Route::get('fee-report/pdf', [FeeReportController::class, 'pdf'])->name('dashboard.fee-report.pdf');
    Route::get('fee-report/excel', [FeeReportController::class, 'excel'])->name('dashboard.fee-report.excel');
    Route::post('/ledger/store', [LedgerController::class, 'store'])->name('dashboard.ledger.store');
    Route::post('/sub-ledger/store', [SubLedgerController::class, 'store'])->name('dashboard.sub-ledger.store');
    Route::get('/sub-ledger/{id}', [SubLedgerController::class, 'getByLedger'])->name('dashboard.ledger.index');
    Route::get('/fund-ledgers/{id}', [LedgerController::class, 'fundLedgers'])->name('dashboard.ledger.fundLedgers');

    Route::post('/fund/store', [FundController::class, 'store'])->name('dashboard.fund.store');
    Route::post('/payment-method/store', [PaymentMethodController::class, 'store'])->name('dashboard.payment-method.store');
    Route::get('/payment-method/list', [PaymentMethodController::class, 'index'])->name('dashboard.payment-method.index');
    Route::delete('/payment-method/{id}', [PaymentMethodController::class, 'destroy']);
    Route::post('/cashier/store', [CashierController::class, 'store'])->name('dashboard.cashier.store');
    Route::get('/cashier/list', [CashierController::class, 'index']);
    Route::delete('/cashier/{id}', [CashierController::class, 'destroy']);
    });

    
    // ==================== Student Profile Routes ====================
    Route::middleware(['role:student'])->prefix('dashboard')->group(function(){
        Route::get('my-profile', [StudentController::class, 'myProfile'])->name('dashboard.student.profile');
        Route::get('my-admission', [StudentController::class, 'myAdmission'])->name('dashboard.student.admission');
    });
    
    // ==================== Guardian Routes ====================
    Route::middleware(['role:guardian'])->prefix('dashboard')->group(function(){
        Route::get('my-children', [StudentController::class, 'myChildren'])->name('dashboard.guardian.children');
        Route::get('my-children/{id}', [StudentController::class, 'show'])->name('dashboard.guardian.child.show');
    });

    // ── Month Settings Routes ──────────────────────────────────────────────────
Route::middleware(['role:super_admin,soft_admin,madrasa_admin'])->prefix('dashboard/month')->group(function(){
    Route::get('/', [MonthSettingController::class, 'index'])->name('dashboard.month.index');
    Route::post('/', [MonthSettingController::class, 'store'])->name('dashboard.month.store');
    Route::get('/suggest', [MonthSettingController::class, 'suggest'])->name('dashboard.month.suggest');
    
    Route::get('/{monthSetting}/edit', [MonthSettingController::class, 'edit'])->name('dashboard.month.edit');
    Route::put('/{monthSetting}', [MonthSettingController::class, 'update'])->name('dashboard.month.update');
    Route::delete('/{monthSetting}', [MonthSettingController::class, 'destroy'])->name('dashboard.month.destroy');
});

// ── Fee Settings Routes ──────────────────────────────────────────────────
Route::prefix('dashboard/fee-settings')
    ->name('dashboard.fee-settings.')
    ->middleware(['role:super_admin,soft_admin,madrasa_admin'])
    ->group(function () {

        Route::get('/', [FeeSettingController::class, 'index'])
            ->name('index');

        Route::get('/get', [FeeSettingController::class, 'get'])
            ->name('get');

        Route::post('/save', [FeeSettingController::class, 'save'])
            ->name('save');

        Route::post('/reset', [FeeSettingController::class, 'reset'])
            ->name('reset');
    });

Route::middleware(['role:super_admin,soft_admin,madrasa_admin'])->prefix('dashboard')->group(function(){
    Route::get('/fees', [FeeGroupController::class, 'index'])->name('dashboard.fees.index');
    Route::get('/fees/create', [FeeGroupController::class, 'create'])->name('dashboard.fees.create');
    Route::post('/fees/store', [FeeGroupController::class, 'store'])->name('dashboard.fees.store');
    Route::get('/fees/{fee}/edit', [FeeGroupController::class, 'edit'])->name('dashboard.fees.edit');
    Route::put('/fees/{fee}/update', [FeeGroupController::class, 'update'])->name('dashboard.fees.update');
    Route::delete('/fees/{fee}/delete', [FeeGroupController::class, 'destroy'])->name('dashboard.fees.destroy');

    // AJAX ROUTES
    Route::get('/fees/get-ledgers/{fund_id}', [FeeGroupController::class, 'getLedgers'])->name('dashboard.fees.getLedgers');
    Route::get('/fees/get-sub-ledgers/{ledger_id}', [FeeGroupController::class, 'getSubLedgers'])->name('dashboard.fees.getSubLedgers');
    Route::get('/fees/get-student-fees', [FeeGroupController::class, 'getStudentFees'])->name('dashboard.fees.getStudentFees');
    Route::post('/fees/update-status', [FeeGroupController::class, 'updateStatus'])->name('dashboard.fees.updateStatus');

});

Route::prefix('dashboard/fee-type')
    ->name('dashboard.fee-type.')
    ->middleware(['role:super_admin,soft_admin,madrasa_admin'])
    ->group(function () {
        Route::get('/', [FeeTypeController::class, 'index'])->name('index');
        Route::post('/store', [FeeTypeController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [FeeTypeController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [FeeTypeController::class, 'update'])->name('update');
        Route::delete('/destroy/{id}', [FeeTypeController::class, 'destroy'])->name('destroy');
        Route::post('/toggle-status/{id}', [FeeTypeController::class, 'toggleStatus'])->name('toggle-status');

        });

Route::middleware(['role:super_admin,soft_admin,madrasa_admin'])->prefix('dashboard')->group(function(){
    
    // Fee Collection Routes
    Route::get('/fee-collections', [FeeCollectionController::class, 'index'])->name('dashboard.feeCollections.index');
    Route::get('/fee-collections/create', [FeeCollectionController::class, 'create'])->name('dashboard.feeCollections.create');
    Route::post('/fee-collections', [FeeCollectionController::class, 'store'])->name('dashboard.feeCollections.store');
    Route::get('/fee-collections/{id}', [FeeCollectionController::class, 'show'])->name('dashboard.feeCollections.show');
    Route::delete('/fee-collections/{id}', [FeeCollectionController::class, 'destroy'])->name('dashboard.feeCollections.destroy');
    
});

Route::middleware(['role:super_admin,soft_admin,madrasa_admin'])
    ->prefix('dashboard')
    ->name('dashboard.fee-collection.')
    ->group(function () {
        Route::get('/fee-collection',[StudentFeeCollectionController::class, 'index'])->name('index');
        Route::get('/fee-collection/student-info',[StudentFeeCollectionController::class, 'studentInfo'])->name('studentInfo');
        Route::get('/fee-collection/payment-methods',[StudentFeeCollectionController::class, 'paymentMethods'])->name('paymentMethods');
        Route::post('/fee-collection/save-payment',[StudentFeeCollectionController::class, 'savePayment'])->name('savePayment');
        Route::get('/fee-collection/today-payments',[StudentFeeCollectionController::class, 'todayPayments'])->name('todayPayments');
        Route::get('/fee-collection/statement',[StudentFeeCollectionController::class, 'statement'])->name('statement');
        Route::post('/fee-collection/add-cashier',[StudentFeeCollectionController::class, 'addCashier'])->name('addCashier');
        Route::get('/fee-collection/print-receipt/{id}',[StudentFeeCollectionController::class, 'printReceipt'])->name('printReceipt');
    });

Route::middleware(['role:super_admin,soft_admin,madrasa_admin'])
    ->prefix('dashboard')
    ->name('dashboard.fee-collection.')
    ->group(function () {

        Route::get('/fee-collection', [StudentFeeCollectionController::class, 'index'])->name('index');
        Route::get('/fee-collection/student-info', [StudentFeeCollectionController::class, 'studentInfo'])->name('studentInfo');
        Route::post('/fee-collection/save-payment', [StudentFeeCollectionController::class, 'savePayment'])->name('savePayment');
        Route::get('/fee-collection/today-payments', [StudentFeeCollectionController::class, 'todayPayments'])->name('todayPayments');
        Route::get('/fee-collection/statement', [StudentFeeCollectionController::class, 'statement'])->name('statement');
        Route::post('/fee-collection/add-cashier', [StudentFeeCollectionController::class, 'addCashier'])->name('addCashier');
        Route::get('/fee-collection/payment-methods', [StudentFeeCollectionController::class, 'paymentMethods'])->name('paymentMethods');
        Route::get('/fee-collection/print-receipt/{id}', [StudentFeeCollectionController::class, 'printReceipt'])->name('printReceipt');
    });

});

require __DIR__.'/auth.php';