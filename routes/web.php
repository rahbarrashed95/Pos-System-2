<?php

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

include_once('install.php');

Route::middleware(['IsInstalled'])->group(function () {

    Route::get('/', function () {
        return view('welcome');
    });

    Auth::routes();

    Route::get('/business/register', 'BusinessController@getRegister')->name('business.getRegister');
    Route::post('/business/register', 'BusinessController@postRegister')->name('business.postRegister');
    Route::post('/business/register/check-username', 'BusinessController@postCheckUsername')->name('business.postCheckUsername');
});
Route::get('/show', function () {
    return view('profile_show');
});

Route::get('/print_header_footer', 'HomeController@printHeaderFooter')->name('print_header_footer');

Route::group(['middleware' => ['auth']], function() {
//Transaction
// Route::get('/banking/transaction', 'BankingController@transaction')->name('banking.transaction');
// Route::get('/banking/transaction/edit/{id}', 'BankingController@editTransaction')->name('banking.transaction.edit');
// Route::put('/banking/transaction/update/{id}', 'BankingController@updateTransaction')->name('banking.transaction.update');
Route::get('/banking_transaction/user/{id}', 'BankTransactionController@transactionByUser')->name('banking.transaction.user');
Route::get('/banking_transaction/type/{type}/user/{id}', 'BankTransactionController@transactionByTypeUser')->name('banking.transaction.type.user');
// Route::delete('/banking/transaction/delete/{id}', 'BankingController@destroyTransaction')->name('banking.destroyTransaction');
// Route::get('/banking/transaction/create', 'BankingController@createTransaction')->name('banking.createTransaction');
// Route::post('/banking/transaction/create/store', 'BankingController@storeTransaction')->name('banking.storeTransaction');

// /banking/categories

//Banking Category
// Route::get('/banking/categories', 'BankingCategoryController@index')->name('banking.cIndex');
// Route::get('/banking/categories/delete/{id}', 'BankingCategoryController@destroy')->name('banking.cDestroy');
// Route::get('/banking/categories/create', 'BankingCategoryController@create')->name('banking.cCreate');
// Route::post('/banking/categories/create/store', 'BankingCategoryController@store')->name('banking.cStore');
// Route::post('/banking/categories/edit/{id}', 'BankingCategoryController@edit')->name('banking.cStore');
Route::resource('/customer-warranty', 'CustomerWarrantyController');
Route::resource('/warranty-check', 'WarrantyCheckController');
Route::get('/edit_warranty/{id}', 'WarrantyCheckController@edit');
Route::post('/search', 'WarrantyCheckController@search');
Route::post('/update_warranty/{id}', 'WarrantyCheckController@update');
Route::get('/delete_warranty/{id}', 'WarrantyCheckController@delete');
Route::resource('/supplier-warranty', 'SupplierWarrantyController');
Route::resource('/banking-categories', 'BankingCategoryController');
Route::resource('/banking_users', 'BankingController');
Route::resource('/banking_transaction', 'BankTransactionController');
Route::resource('/transfer_balance_bank', 'TransferBalanceBankController');
Route::resource('/receive_balance_bank', 'ReceiveBalanceBankController');
Route::resource('/requisition', 'RequisitionController');

Route::resource('/transfer_balance_personal', 'TransferBalancePersonalController');
Route::resource('/receive_balance_personal', 'ReceiveBalancePersonalController');
Route::get('/transfer_balance_bank/transaction/bank/{id}/{id2}/{id3}', 'TransferBalanceBankController@transactionByBank')->name('transfer_balance_bank.transaction.bank');
Route::get('/transfer_balance_personal/transaction/personal/{id}', 'ReceiveBalancePersonalController@transactionByUser')->name('transfer_balance_personal.transaction.personal');
});

//Routes for authenticated users only
Route::middleware(['IsInstalled', 'auth', 'SetSessionData', 'language', 'timezone'])->group(function () {

    Route::get('/logout', 'Auth\LoginController@logout')->name('logout');

    Route::get('/home', 'HomeController@index')->name('home');
    Route::post('/home/get-purchase-details', 'HomeController@getPurchaseDetails');
    Route::post('/home/get-sell-details', 'HomeController@getSellDetails');
    Route::get('/home/product-stock-alert', 'HomeController@getProductStockAlert');
    Route::get('/home/purchase-payment-dues', 'HomeController@getPurchasePaymentDues');
    Route::get('/home/sales-payment-dues', 'HomeController@getSalesPaymentDues');
    
    Route::get('/business/settings', 'BusinessController@getBusinessSettings')->name('business.getBusinessSettings');
    Route::post('/business/update', 'BusinessController@postBusinessSettings')->name('business.postBusinessSettings');
    Route::get('/user/profile', 'UserController@getProfile')->name('user.getProfile');
    Route::post('/user/update', 'UserController@updateProfile')->name('user.updateProfile');
    Route::post('/user/update-password', 'UserController@updatePassword')->name('user.updatePassword');

    Route::resource('brands', 'BrandController');
    
    Route::resource('payment-account', 'PaymentAccountController');

    Route::resource('tax-rates', 'TaxRateController');

    Route::resource('units', 'UnitController');

    Route::get('/contacts/import', 'ContactController@getImportContacts')->name('contacts.import');
    Route::post('/contacts/import', 'ContactController@postImportContacts');
    Route::post('/contacts/check-contact-id', 'ContactController@checkContactId');
    Route::get('/contacts/customers', 'ContactController@getCustomers');
    Route::resource('contacts', 'ContactController');

    Route::resource('categories', 'CategoryController');
    Route::get('sub_categories/{id}', 'CategoryController@sub_categories');
    Route::resource('variation-templates', 'VariationTemplateController');

    Route::get('/products/view-product-group-price/{id}', 'ProductController@viewGroupPrice');
    Route::get('/products/add-selling-prices/{id}', 'ProductController@addSellingPrices');
    Route::post('/products/save-selling-prices', 'ProductController@saveSellingPrices');
    Route::post('/products/mass-delete', 'ProductController@massDestroy');
    Route::get('/products/view/{id}', 'ProductController@view');
    Route::get('/products/list', 'ProductController@getProducts');
    Route::get('/products/list-no-variation', 'ProductController@getProductsWithoutVariations');
    
    Route::post('/products/get_sub_categories', 'ProductController@getSubCategories');
    Route::post('/products/product_form_part', 'ProductController@getProductVariationFormPart');
    Route::post('/products/get_product_variation_row', 'ProductController@getProductVariationRow');
    Route::post('/products/get_variation_template', 'ProductController@getVariationTemplate');
    Route::get('/products/get_variation_value_row', 'ProductController@getVariationValueRow');
    Route::post('/products/check_product_sku', 'ProductController@checkProductSku');
    Route::get('/products/quick_add', 'ProductController@quickAdd');
    Route::post('/products/save_quick_product', 'ProductController@saveQuickProduct');
    
    Route::resource('products', 'ProductController');

    Route::get('/purchases/get_products', 'PurchaseController@getProducts');
    Route::get('/purchases/get_suppliers', 'PurchaseController@getSuppliers');
    Route::post('/purchases/get_purchase_entry_row', 'PurchaseController@getPurchaseEntryRow');
    Route::post('/purchases/check_ref_number', 'PurchaseController@checkRefNumber');
    Route::get('/purchases/print/{id}', 'PurchaseController@printInvoice');
    Route::resource('purchases', 'PurchaseController');

    Route::get('/sells/duplicate/{id}', 'SellController@duplicateSell');
    Route::get('/sells/discount-sell', 'SellController@getDiscountSell');
    Route::get('/sells/drafts', 'SellController@getDrafts');
    Route::get('/sells/quotations', 'SellController@getQuotations');
    Route::get('/sells/draft-dt', 'SellController@getDraftDatables');
    Route::resource('sells', 'SellController');

    Route::get('/sells/pos/get_product_row/{variation_id}/{location_id}', 'SellPosController@getProductRow');
    Route::post('/sells/pos/get_payment_row', 'SellPosController@getPaymentRow');
    Route::get('/sells/pos/get-recent-transactions', 'SellPosController@getRecentTransactions');
    Route::get('/sells/{transaction_id}/print', 'SellPosController@printInvoice')->name('sell.printInvoice');
    Route::get('/sells/pos/get-product-suggestion', 'SellPosController@getProductSuggestion');
    Route::resource('pos', 'SellPosController');
    Route::get('pos-search-product', 'SellPosController@search_product');

    Route::resource('roles', 'RoleController');

    Route::resource('users', 'ManageUserController');
    Route::resource('hrm_employee', 'HrmController');
    Route::resource('hrm_transactions', 'HrmTransactionController');
    Route::resource('hrm_attendence', 'HrmAttendenceController');

    Route::resource('group-taxes', 'GroupTaxController');

    Route::get('/barcodes/set_default/{id}', 'BarcodeController@setDefault');
    Route::resource('barcodes', 'BarcodeController');

    //Invoice schemes..
    Route::get('/invoice-schemes/set_default/{id}', 'InvoiceSchemeController@setDefault');
    Route::resource('invoice-schemes', 'InvoiceSchemeController');

    //Print Labels
    Route::get('/labels/show', 'LabelsController@show');
    Route::get('/labels/add-product-row', 'LabelsController@addProductRow');
    Route::post('/labels/preview', 'LabelsController@preview');

    //Reports...
    Route::get('/reports/service-staff-report', 'ReportController@getServiceStaffReport');
    Route::get('/reports/table-report', 'ReportController@getTableReport');
    Route::get('/reports/profit-loss', 'ReportController@getProfitLoss');
    Route::get('/reports/get-opening-stock', 'ReportController@getOpeningStock');
    Route::get('/reports/purchase-sell', 'ReportController@getPurchaseSell');
    
    Route::get('/reports/purchase-daily-sell', 'ReportController@getPurchaseSellOnly');
    Route::get('/reports/product-wise-sell', 'ReportController@productWiseSell');
    Route::get('/reports/customer-wise-due-sell', 'ReportController@customerWiseDueSell');
    Route::get('/reports/customer-wise-sell-report', 'ReportController@customerWiseSellReport');
    
    //moinul
    Route::get('/reports/sale-update-tracking', 'ReportController@sellUpdateTracking');
    Route::get('/reports/sale-delete-tracking', 'ReportController@sellDeleteTracking');
    Route::get('/reports/sale-update-tracking-products-details/{id}', 'ReportController@sellUpdateTrackingProduct')->name('sellUpdateTrackingProduct');

    Route::get('/reports/customer-supplier', 'ReportController@getCustomerSuppliers');
    Route::get('/reports/report-supplier', 'ReportController@getSuppliers');
    Route::get('/reports/stock-report', 'ReportController@getStockReport');
    //new
    Route::get('/reports/stock-track-report', 'ReportController@getStockTrackReport');
    //new
    //moinul
    Route::get('/reports/physical-stock-report', 'ReportController@getPhysicalStockReport');
    Route::get('/reports/physical-report-details/{id}', 'ReportController@StockReportdetails');
    
    Route::get('/reports/physical-stock-report-print/{from}/{to}/{name?}', 'ReportController@getPhysicalStockReportPrint');
    
    Route::get('/reports/physical-stock-report-ajax', 'ReportController@getPhysicalStockReportAajax');
    Route::get('/reports/stock-alert-report', 'ReportController@getStockAlertReport');
    Route::get('/reports/stock-details', 'ReportController@getStockDetails');
    Route::get('/reports/tax-report', 'ReportController@getTaxReport');
    Route::get('/reports/trending-products', 'ReportController@getTrendingProducts');
    Route::get('/reports/expense-report', 'ReportController@getExpenseReport');
    Route::get('/reports/stock-adjustment-report', 'ReportController@getStockAdjustmentReport');
    Route::get('/reports/register-report', 'ReportController@getRegisterReport');
    Route::get('/reports/sales-representative-report', 'ReportController@getSalesRepresentativeReport');
    Route::get('/reports/sales-representative-total-expense', 'ReportController@getSalesRepresentativeTotalExpense');
    Route::get('/reports/sales-representative-total-sell', 'ReportController@getSalesRepresentativeTotalSell');
    Route::get('/reports/sales-representative-total-commission', 'ReportController@getSalesRepresentativeTotalCommission');
    Route::get('/reports/stock-expiry', 'ReportController@getStockExpiryReport');
    Route::get('/reports/stock-expiry-edit-modal/{purchase_line_id}', 'ReportController@getStockExpiryReportEditModal');
    Route::post('/reports/stock-expiry-update', 'ReportController@updateStockExpiryReport')->name('updateStockExpiryReport');
    Route::get('/reports/customer-group', 'ReportController@getCustomerGroup');
    Route::get('/reports/product-purchase-report', 'ReportController@getproductPurchaseReport');
    Route::get('/reports/product-sell-report', 'ReportController@getproductSellReport');
    Route::get('/reports/due-sell-report', 'ReportController@getDueSellReport');
    
    Route::get('/reports/lot-report', 'ReportController@getLotReport');
    Route::get('/reports/purchase-payment-report', 'ReportController@purchasePaymentReport');
    Route::get('/reports/sell-payment-report', 'ReportController@sellPaymentReport');
    
    Route::get('/reports/product-download', 'ReportController@productDownload');
    Route::get('/reports/product-review', 'ReportController@productReview');
    
    //Business Location Settings...
    Route::prefix('business-location/{location_id}')->name('location.')->group(function () {
        Route::get('settings', 'LocationSettingsController@index')->name('settings');
        Route::post('settings', 'LocationSettingsController@updateSettings')->name('settings_update');
    });

    //Business Locations...
    Route::post('business-location/check-location-id', 'BusinessLocationController@checkLocationId');
    Route::resource('business-location', 'BusinessLocationController');

    //Invoice layouts..
    Route::resource('invoice-layouts', 'InvoiceLayoutController');

    //Expense Categories...
    Route::resource('expense-categories', 'ExpenseCategoryController');

    //Expenses...
    Route::resource('expenses', 'ExpenseController');

    //Transaction payments...
    Route::get('/payments/show-child-payments/{payment_id}', 'TransactionPaymentController@showChildPayments');
    Route::get('/payments/view-payment/{payment_id}', 'TransactionPaymentController@viewPayment');
    Route::get('/payments/add_payment/{transaction_id}', 'TransactionPaymentController@addPayment');
    Route::get('/payments/pay-contact-due/{contact_id}', 'TransactionPaymentController@getPayContactDue');
    Route::get('/contact/add_advance/{contact_id}', 'TransactionPaymentController@getAddAdvance');
    Route::match(['put', 'patch'],'/contact/add_advance/{id}', 'TransactionPaymentController@storeAdvance');
    Route::post('/payments/pay-contact-due', 'TransactionPaymentController@postPayContactDue');
    Route::resource('payments', 'TransactionPaymentController');

    //Printers...
    Route::resource('printers', 'PrinterController');

    Route::get('/stock-adjustments/remove-expired-stock/{purchase_line_id}', 'StockAdjustmentController@removeExpiredStock');
    Route::post('/stock-adjustments/get_product_row', 'StockAdjustmentController@getProductRow');
    Route::resource('stock-adjustments', 'StockAdjustmentController');
    
      //22.07.2020
    Route::post('physical-stock-update/{id}', 'StockAdjustmentController@PhysicalStockupdate');
    Route::get('physical-stock-edit/{id}', 'StockAdjustmentController@PhysicalStockEdit');
    Route::get('physical-stock-delete/{id}', 'StockAdjustmentController@PhysicalStockDelete');
    Route::get('physical-stock', 'StockAdjustmentController@PhysicalStock');
    Route::get('multiple-product-physical-stock', 'StockAdjustmentController@multiProductPhysicalStock');
    Route::post('multiple-product-physical-stock', 'StockAdjustmentController@multiProductPhysicalStockPost');

    Route::get('multiple-product-physical-stock-ajax-session-default', 'StockAdjustmentController@multiProductPhysicalStockAjaxSessionDefault');
    Route::get('multiple-product-physical-stock-ajax-session', 'StockAdjustmentController@multiProductPhysicalStockAjaxSession');
    Route::get('multiple-product-physical-stock-ajax-session-single', 'StockAdjustmentController@multiProductPhysicalStockAjaxSessionSingel');
    Route::get('multiple-product-physical-stock-ajax-session-single-remove', 'StockAdjustmentController@multiProductPhysicalStockAjaxSessionSingelRemove');
    

    
    Route::resource('stock-return', 'StockReturnController');
    Route::get('stock-return/print/{id}', 'StockReturnController@printInvoice');
    
    #---------------------------
    Route::get('stock-return/print/new/{id}', 'StockReturnController@printInvoiceNew');
    #---------------------------

    Route::get('/cash-register/register-details', 'CashRegisterController@getRegisterDetails');
    Route::get('/cash-register/close-register', 'CashRegisterController@getCloseRegister');
    Route::post('/cash-register/close-register', 'CashRegisterController@postCloseRegister');
    Route::resource('cash-register', 'CashRegisterController');

    //Import products
    Route::get('/import-products', 'ImportProductsController@index');
    Route::post('/import-products/store', 'ImportProductsController@store');

    //Sales Commission Agent
    Route::resource('sales-commission-agents', 'SalesCommissionAgentController');
    Route::get('sales-commission-agents/product/add/{id}', 'SalesCommissionAgentController@addProduct');
    Route::get('sales-commission-agents/product/view/{id}', 'SalesCommissionAgentController@showProduct');
    Route::post('/sales-commission-agents/product/store',[
        'uses' => 'SalesCommissionAgentController@storeProduct',
        'as' => 'storeProduct'
    ]);
    Route::delete('/sales-commission-agents/product/delete/{id}',[
        'uses' => 'SalesCommissionAgentController@destroyProduct',
        'as' => 'destroyProduct'
    ]);
    Route::get('sales-commission-agents/product/edit/{id}', 'SalesCommissionAgentController@editProduct');
    Route::put('/sales-commission-agents/product/store/{id}',[
        'uses' => 'SalesCommissionAgentController@updateProduct',
        'as' => 'updateProduct'
    ]);


    //Stock Transfer
    Route::get('stock-transfers/print/{id}', 'StockTransferController@printInvoice');
    Route::resource('stock-transfers', 'StockTransferController');
    
    Route::get('/opening-stock/add/{product_id}', 'OpeningStockController@add');
    Route::post('/opening-stock/save', 'OpeningStockController@save');

    //Customer Groups
    Route::resource('customer-group', 'CustomerGroupController');

    //Import opening stock
    Route::get('/import-opening-stock', 'ImportOpeningStockController@index');
    Route::post('/import-opening-stock/store', 'ImportOpeningStockController@store');

    //Sell return
    Route::resource('sell-return', 'SellReturnController');
    Route::get('sell-return/get-product-row', 'SellReturnController@getProductRow');
    Route::get('/sell-return/print/{id}', 'SellReturnController@printInvoice');
    Route::get('/sell-return/add/{id}', 'SellReturnController@add');
    
    //Backup
    Route::get('backup/download/{file_name}', 'BackUpController@download');
    Route::get('backup/delete/{file_name}', 'BackUpController@delete');
    Route::resource('backup', 'BackUpController', ['only' => [
        'index', 'create', 'store'
    ]]);


    Route::resource('selling-price-group', 'SellingPriceGroupController');
    Route::resource('notification-templates', 'NotificationTemplateController')->only(['index', 'store']);
    Route::get('notification/get-template/{transaction_id}/{template_for}', 'NotificationController@getTemplate');
    Route::post('notification/send', 'NotificationController@send');
    Route::get('/purchase-return/add/{id}', 'PurchaseReturnController@add');
    Route::resource('/purchase-return', 'PurchaseReturnController');


    //Rs Modules
    Route::get('/sr', 'SRController@index');
    Route::get('/sr/create', 'SRController@create');
    Route::post('/sr/store', 'SRController@store');
    Route::get('sr/edit/{id}', 'SRController@edit');
    Route::post('sr/update', 'SRController@update');
    Route::get('sr/delete/{id}', 'SRController@destroy');

    // Delivery man manangement
    Route::get('delivery_man', 'DeliveryManController@index');
    Route::get('delivery_man/create', 'DeliveryManController@create');
    Route::post('delivery_man/store', 'DeliveryManController@store');
    Route::get('delivery_man/edit/{id}', 'DeliveryManController@edit');
    Route::post('delivery_man/update', 'DeliveryManController@update');
    Route::get('delivery_man/delete/{id}', 'DeliveryManController@destroy');

    // product discount 
    Route::get('/discount', 'ProductDiscountController@index');
    Route::get('/discount/create', 'ProductDiscountController@create');
    Route::post('/discount/store', 'ProductDiscountController@store');
    Route::get('/discount/edit/{id}', 'ProductDiscountController@edit');
    Route::post('/discount/update', 'ProductDiscountController@update');
    Route::get('/discount/delete/{id}', 'ProductDiscountController@destroy');

    //quantity discount
    Route::get('/quantity-discount', 'ProductQuantityDiscountController@index');
    Route::get('/quantity-discount/create', 'ProductQuantityDiscountController@create');
    Route::post('/quantity-discount/store', 'ProductQuantityDiscountController@store');
    Route::get('/quantity-discount/edit/{id}', 'ProductQuantityDiscountController@edit');
    Route::post('/quantity-discount/update', 'ProductQuantityDiscountController@update');
    Route::get('/quantity-discount/delete/{id}', 'ProductQuantityDiscountController@destroy');


    //Restaurant module
    Route::group(['prefix' => 'modules'], function () {

        Route::resource('tables', 'Restaurant\TableController');
        Route::resource('modifiers', 'Restaurant\ModifierSetsController');
        
        //Map modifier to products
        Route::get('/product-modifiers/{id}/edit', 'Restaurant\ProductModifierSetController@edit');
        Route::post('/product-modifiers/{id}/update', 'Restaurant\ProductModifierSetController@update');
        Route::get('/product-modifiers/product-row/{product_id}', 'Restaurant\ProductModifierSetController@product_row');

        Route::get('/add-selected-modifiers', 'Restaurant\ProductModifierSetController@add_selected_modifiers');

        Route::get('/kitchen', 'Restaurant\KitchenController@index');
        Route::get('/kitchen/mark-as-cooked/{id}', 'Restaurant\KitchenController@markAsCooked');
        Route::post('/refresh-orders-list', 'Restaurant\KitchenController@refreshOrdersList');

        Route::get('/orders', 'Restaurant\OrderController@index');
        Route::get('/orders/mark-as-served/{id}', 'Restaurant\OrderController@markAsServed');
        Route::get('/data/get-pos-details', 'Restaurant\DataController@getPosDetails');
    });

    Route::get('bookings/get-todays-bookings', 'Restaurant\BookingController@getTodaysBookings');
    Route::resource('bookings', 'Restaurant\BookingController');
    Route::resource('raw_items', 'Restaurant\InternalKitchenController');
    Route::resource('dish_category', 'Restaurant\DishCategoryController');
    Route::resource('dish_list', 'Restaurant\DishListController');
    Route::get('raw_items/add/{id}', 'Restaurant\DishListController@createUsedRaw');
    Route::get('raw_items/view/{id}', 'Restaurant\DishListController@showRawItems');
    // Route::post('used_raw_items/store','Restaurant\DishListController@storeRaw')->name('storeusedRaw');
    Route::post('/used_raw_items/store',[
        'uses' => 'Restaurant\DishListController@storeRaw',
        'as' => 'storeusedRaws'
    ]);
});
