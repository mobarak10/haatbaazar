<?php

use App\Http\Controllers\AdvancedSalaryController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CashController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerDueManageController;
use App\Http\Controllers\DamageController;
use App\Http\Controllers\DamageReturnController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IncomeRecordController;
use App\Http\Controllers\IncomeSectorController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\MokamController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductTransferController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\Reports\CashBookController;
use App\Http\Controllers\Reports\ExpenseReportController;
use App\Http\Controllers\Reports\IncomeReportController;
use App\Http\Controllers\Reports\LedgerController;
use App\Http\Controllers\Reports\OverallReportController;
use App\Http\Controllers\Reports\ProductionReportController;
use App\Http\Controllers\Reports\ProductReportController;
use App\Http\Controllers\Reports\ProfitLossReportController;
use App\Http\Controllers\Reports\PurchaseReportController;
use App\Http\Controllers\Reports\SaleReportController;
use App\Http\Controllers\Reports\StockReportController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleReturnController;
use App\Http\Controllers\ShowroomController;
use App\Http\Controllers\SMSController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierDueManageController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CostCategoryController;
use App\Http\Controllers\CostSubcategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\LoanAccountController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\LoanInstallmentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified', 'daily_balance'])->group(function () {
    Route::get('/local', [LocaleController::class, 'switchLanguage'])->name('switchLanguage');


    Route::get('/user_profile', [AdminController::class, 'user_profile'])->name('user_profile');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*======Permission Start=======*/

    Route::prefix('permission')->name('permission.')->group(function () {
        // generate all permissions
        Route::get('generate-all-permissions', [PermissionController::class, 'generateAllPermissions'])->name('generate.all');
    });
    /*======Permission End=======*/

    /**==== start product resources route ====**/
    Route::resource('user', UserController::class);
    /**==== end product resources route ====**/

    // role resource routes
    Route::resource('role', RoleController::class);
    /*======Role End=======*/

    /**==== start product resources route ====**/
    Route::resource('product', ProductController::class);
    /**==== end product resources route ====**/

    /**==== start supplier resources route ====**/
    Route::resource('supplier', SupplierController::class);
    /**==== end supplier resources route ====**/

    /**==== start supplier route ====**/
    Route::prefix('supplier')->name('supplier.')->group(function () {
        Route::post('get-all-active-suppliers', [SupplierController::class, 'allActiveSuppliers']);
        Route::post('get-details-from-party', [SupplierController::class, 'supplierDetails']);
    });
    /**==== end supplier route ====**/

    /**==== start customer resources route ====**/
    Route::resource('customer', CustomerController::class);
    /**==== end customer resources route ====**/

    Route::prefix('customer')->name('customer.')->group(function () {
        Route::post('get-all-active-customers', [CustomerController::class, 'allActiveCustomers']);
        Route::post('create-new-customer', [CustomerController::class, 'newCustomer']);
    });

    /**==== start mokam resources route ====**/
    Route::resource('mokam', MokamController::class);
    /**==== end mokam resources route ====**/

    Route::prefix('mokam')->name('mokam.')->group(function () {
        Route::post('get-all-mokams', [MokamController::class, 'allMokams']);
    });

    /**==== start unit resources route ====**/
    Route::resource('unit', UnitController::class);
    /**==== end unit resources route ====**/

    /**==== start category resources route ====**/
    Route::resource('category', CategoryController::class);
    /**==== end category resources route ====**/

    /**==== start brand resources route ====**/
    Route::resource('brand', BrandController::class);
    /**==== end brand resources route ====**/

    /**==== start showroom resources route ====**/
    Route::resource('showroom', ShowroomController::class);
    /**==== end showroom resources route ====**/

    Route::prefix('showroom')->name('showroom.')->group(function () {
        Route::post('get-all-showroom', [ShowroomController::class, 'allShowroom']);
        Route::get('get-product-for-showroom/{id}', [ShowroomController::class, 'getProductForShowroom']);
    });

    /**==== start cash resources route ====**/
    Route::resource('cash', CashController::class);
    /**==== end cash resources route ====**/

    /**==== start cash route ====**/
    Route::prefix('cash')->name('cash.')->group(function () {
        Route::post('get-all-cashes', [CashController::class, 'allCashes']);
        Route::post('get-details-from-cash', [CashController::class, 'cashDetails']);
    });
    /**==== end cash route ====**/

    /**==== start barcode route ====**/
    Route::prefix('barcode')->name('barcode.')->group(function () {
        Route::get('batch', [BarcodeController::class, 'batchBarcode'])->name('batch');
        Route::get('single', [BarcodeController::class, 'singleBarcode'])->name('single');
    });
    /**==== end barcode route ====**/


    /**==== Cost category Start ====**/
    Route::resource('cost-category', CostCategoryController::class);
    /**==== Cost category End ====**/

    /**==== Cost subcategory Start ====**/
    Route::resource('cost-subcategory', CostSubcategoryController::class);
    /**==== Cost subcategory End ====**/

    /**==== Expense Start ====**/
    Route::resource('expense', ExpenseController::class);
    /**==== Expense End====**/

    /**==== Loan Account Start ====**/
    Route::resource('loan-account', LoanAccountController::class);
    /**==== Loan Account End====**/

    /**==== Loan Start ====**/
    Route::resource('loan', LoanController::class);
    /**==== Loan End====**/

    /**==== Loan Installment Start ====**/
    Route::resource('loan-installment', LoanInstallmentController::class);
    /**==== Loan Installment End====**/

    /**==== start bank resources route ====**/
    Route::resource('bank', BankController::class);
    /**==== end bank resources route ====**/

    /**==== start bank-account resources route ====**/
    Route::resource('bank-account', BankAccountController::class);
    /**==== end bank-account resources route ====**/

    /**==== start cash route ====**/
    Route::prefix('bank-account')->name('bank-account.')->group(function () {
        Route::post('get-all-bank-account', [BankAccountController::class, 'allBankAccount']);
    });
    /**==== end cash route ====**/

    /**==== start production resources route ====**/
    Route::resource('production', ProductionController::class);
    /**==== end production resources route ====**/

    /**==== start production route ====*/
    Route::prefix('product-transfer')->name('product-transfer.')->group(function () {
        Route::put('get-all-products', [ProductTransferController::class, 'getAllProduct']);
    });
    /**==== end production route ====*/

    /**==== start product transfer resources route ====**/
    Route::resource('product-transfer', ProductTransferController::class);
    /**==== end product transfer resources route ====**/

    /**==== start damage resources route ====**/
    Route::resource('damage-stock', DamageController::class);
    /**==== end damage resources route ====**/

    /**==== start damage return resources route ====**/
    Route::resource('damage-return', DamageReturnController::class);
    /**==== end damage return resources route ====**/

    /**==== start purchase resources route ====**/
    Route::resource('purchase', PurchaseController::class);
    /**==== end purchase resources route ====**/

    /**==== start sale return resources route ====**/
    Route::resource('purchase-return', PurchaseReturnController::class);
    /**==== end sale return resources route ====**/

    Route::prefix('raw-material-stock')->name('raw-material-stock.')->group(function () {
        //
    });

    /**==== start sale resources route ====**/
    Route::resource('sale', SaleController::class);
    /**==== end sale resources route ====**/

    /**==== start sale return resources route ====**/
    Route::resource('sale-return', SaleReturnController::class);
    /**==== end sale return resources route ====**/

    /**==== Income sector recourse route ====**/
    Route::resource('income-sector', IncomeSectorController::class);
    /**==== Income sector recourse route ====**/

    /**==== Income record recourse route ====**/
    Route::resource('income-record', IncomeRecordController::class);
    /**==== Income record recourse route ====**/

    /**==== start stock resources route ====**/
    Route::resource('stock', StockController::class);

    /**==== end stock resources route ====**/
    Route::prefix('stock')->name('stock.')->group(function () {

    });

    /**==== customer due-manage resources route ====**/
    Route::resource('customer-due-manage', CustomerDueManageController::class);
    /**==== customer due-manage resources route ====**/

    /**==== supplier due-manage resources route ====**/
    Route::resource('supplier-due-manage', SupplierDueManageController::class);
    /**==== supplier due-manage resources route ====**/

    /** ==== advanced salary resource route ==== **/
    Route::resource('advanced-salary', AdvancedSalaryController::class);
    /** ==== advanced salary resource route ==== **/

    /** ==== salary resource route ==== **/
    Route::resource('salary', SalaryController::class);
    /** ==== salary resource route ==== **/

    /** ==== transaction resource route ==== **/
    Route::resource('transaction', TransactionController::class);
    /** ==== transaction resource route ==== **/

    /**==== end stock resources route ====**/
    Route::prefix('sms')->name('sms.')->group(function () {
        Route::get('group-sms', [SMSController::class, 'groupSms'])->name('groupSms');
        Route::post('group-sms', [SMSController::class, 'groupSmsSend']);

        Route::get('custom-sms', [SMSController::class, 'customSms'])->name('customSms');
        Route::post('custom-sms', [SMSController::class, 'customSmsSend']);

        Route::get('sms-report', [SMSController::class, 'index'])->name('index');
    });

    Route::prefix('report')->name('report.')->group(function () {
        Route::get('cash-book', [CashBookController::class, 'index'])->name('cash-book');
        Route::post('cash-book', [CashBookController::class, 'store'])->name('cash-store');
        Route::get('customer-ledger', [LedgerController::class, 'customerLedger'])->name('customer-ledger');
        Route::get('supplier-ledger', [LedgerController::class, 'supplierLedger'])->name('supplier-ledger');
        Route::get('purchase-report', [PurchaseReportController::class, 'index'])->name('purchase-report');
        Route::get('production-report', [ProductionReportController::class, 'index'])->name('production-report');
        Route::get('product-report', [ProductReportController::class, 'index'])->name('product-report');
        Route::get('sale-report', [SaleReportController::class, 'index'])->name('sale-report');
        Route::get('overall-report', [OverallReportController::class, 'index'])->name('overall-report');
        Route::get('profit-loss', [ProfitLossReportController::class, 'index'])->name('profit-loss');
        Route::get('stock-report', [StockReportController::class, 'index'])->name('stock-report');
        Route::get('income-report', [IncomeReportController::class, 'index'])->name('income-report');
        Route::get('expense-report', [ExpenseReportController::class, 'index'])->name('expense-report');
    });
});

require __DIR__ . '/auth.php';
