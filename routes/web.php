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

Route::post('register/check', 'Auth\RegisterController@check')->name('register.check');
Route::post('register/checkEmail', 'Auth\RegisterController@checkEmail')->name('register.checkEmail');
// Auth::routes();


//Route::get('/', function () {
//    return view('welcome');
//});

Auth::routes();
Route::get('/', 'HomeController@index')->name('home');

Route::get('/search-jobs', 'HomeController@searchJobs')->name('search-jobs');
Route::get('/select-search-jobs', 'SelectJobController@searchJobs')->name('select-search-jobs');
Route::get('/home', 'HomeController@index')->name('home');

Route::post('/companies', 'CompaniesController@store')->name("companies");
Route::get('/add-jobs', 'AddJobController@index');
Route::get('/select-job', 'SelectJobController@index');
Route::get('/roll-forward', 'AddJobController@rollForward')->name("roll-forward");
Route::get('/roll-forward-by-projectname/{projectname}', 'AddJobController@rollProject');


Route::get('/invoices', 'InvoicesController@index');

Route::post('/add-jobs/new', 'AddJobController@store');

//by carding
Route::get('/users', 'UsersController@index');
Route::post('/add-user', 'UsersController@addUser');
Route::get('/ip-user/{data?}', 'UsersController@ipUser');
Route::get('/iplockdown', 'UsersController@iplockdown')->name('iplockdown');
Route::get('/unblock-ip/{ip}', 'UsersController@unblockIp');

Route::get('/emails', 'EmailController@index');
Route::post('/send-email', 'EmailController@emailPost');

Route::get('/filemanagement', 'FileManagementController@index');

Route::get('/filearchive', 'FileArchiveController@index');

Route::get('/invoicerecipient', 'InvoiceRecipientController@index');


//end

Route::get('/download', 'DownloadController@download')->name("download");
//Route::get('/invoices-filter', 'DownloadController@download')->name("invoices-filter-job-ids");
Route::get('/download-invoices-filter', 'DownloadController@zipWithJobIdFilters')->name("invoices-filter-job-ids");
Route::get('/download-invoices-download', 'DownloadController@downloadWithFileName')->name("invoices-filter-download");
Route::get('/download-invoices-filter-range', 'DownloadController@downloadInvoiceNumber')->name("invoices-filter-invoice-range");
Route::get('/download-invoices-filter-date-range', 'DownloadController@downloadDateRange')->name("invoices-filter-date-range");
Route::get('/download-invoices-filter-client', 'DownloadController@downloadClient')->name("invoices-filter-client");
Route::get('/accounting-sheet', 'AccountingSheetController@index')->name("accounting-sheet");
Route::get('/accounting-date-range', 'AccountingSheetController@generateCsv')->name("accounting-date-range");
Route::get('/accounting-date-range-download', 'AccountingSheetController@downloadCsv')->name("accounting-date-range-download");
Route::get('/companies', 'CompaniesController@index')->name("companies");
Route::get('/invoice-generator', 'InvoiceGeneratorController@index');
Route::post('/invoice-generator-jobs', 'InvoiceGeneratorController@searchJobs')->name("invoice-generator-jobs");
Route::post('/invoice-generator-jobs-status', 'InvoiceGeneratorController@changeJobsStatus')->name("invoice-generator-jobs-status");
Route::post('/invoice-generate-job-xlsx', 'InvoiceGeneratorController@generateJobsXlsx')->name("invoice-generate-job-xlsx");
Route::get('/invoice-download-job-xlsx', 'InvoiceGeneratorController@downloadJobsXlsx')->name("invoice-download-job-xlsx");
Route::get('/companies-filter', 'CompaniesController@filter')->name("companies-filter");
Route::post('/companies-auto-invoice-update', 'CompaniesController@updateAutoInvoice')->name("companies-auto-invoice-update");
Route::post('/companies-assign-invoice-to-project-name-update', 'CompaniesController@udpateAssignInvoiceToProjectName')
	->name("companies-assign-invoice-to-project-name-update");
Route::get('/companies/edit', 'CompaniesController@edit');
Route::post('/companies-update', 'CompaniesController@update')->name("companies-update");
// Route::resource('pricing-grid', 'PricingGridController');
Route::get('/pricing-grid/{grid?}', 'PricingGridController@index');
Route::get('/remove-grid/{id}', 'PricingGridController@destroy');
Route::post('/save-pricing', 'PricingGridController@store')->name('save-pricing');
Route::post('/pricing-update', 'PricingGridController@update')->name("pricing-update");

//edit account
Route::get('/edit-user/{userId}', 'UsersController@editUser')->name('edit-user');
Route::post('/update-user', 'UsersController@updateUser')->name('update-user');

//logon as user
Route::get('logon_as_user', 'LogOnAsUserController@index')->name('logon_as_user');
Route::get('company-users/{company_id}', 'LogOnAsUserController@showCompanyUsers')->name('company-users');
Route::post('login_as_user', 'LogOnAsUserController@loginUser')->name('login_as_user');
Route::get('back_to_admin', 'LogOnAsUserController@backToAdmin')->name('back_to_admin');


//recipient
Route::get('/copy-company', 'InvoiceRecipientController@copyCompany')->name('copy-company');
Route::post('/save-recipient', 'InvoiceRecipientController@saveRecipient')->name('save-recipient');


Route::get('/thankyou', function () {
	return view('auth.thankyou');
})->name('thankyou');

Route::get('/change-status/{user}/{action}', 'UsersController@changeStatus')->name('change-status');

Route::get('/configuration', 'AutomationConfigControllers@index')->name('config');
Route::get('config-setup/{action}/{config}/', 'AutomationConfigControllers@configSetup');

Route::get('/export-jobs', 'HomeController@exportJob')->name('export-jobs');
Route::get('/export-jobs/{from}/{to}/', 'HomeController@exportJob');


Route::get('/downloadPDF/{invoice_number}/{type}', 'InvoicesController@downloadPDF');
Route::get('/createPdfInvoice/{batch}/{invoice_number}', 'InvoicesController@createPdfInvoice');
Route::get('/pdfView', function () {
	return view('pdf');
});

Route::get('/getTaxGroup/{group}', 'AddJobController@getTaxGroup');

Route::get('/getProjectName', 'AddJobController@getProjectName');

Route::post('add-grid', 'PricingGridController@addPricingInfo')->name('add-grid');

Route::get('/pricing-grid-table/{type}/{source}', 'PricingGridController@getPricingTable');
Route::get('/load-grid-info/{priceId}/', 'PricingGridController@loadPricingGridInfo');

Route::post('/remove-user', 'UsersController@removeUser')->name('remove-user');

Route::get('/job-pricing/{page}/{company}/{turnaround}/{work_type}/{taxonomy}', 'AddJobController@getPricing');
Route::get('/company-country/{company}/', 'AddJobController@companyCountry');
Route::get('/edit-job/{jobid}', 'AddJobController@editJobs');

Route::get('/download-uploadedJob', 'AddJobController@downloadUploadedFile');
Route::post('/update-job-status', 'AddJobController@updateJobStatus')->name('update-job-status');

Route::get('get-vendors', 'AddJobController@getVendors');
Route::post('remove-source-file', 'AddJobController@removeSourceFile')->name('remove-source-file');

Route::post('add-jobs/update', 'AddJobController@updateJob');
Route::get('download-invoice/{invoice_number}/{jobId}', 'HomeController@downloadInvoiceFile');

Route::get('overwrite-invoices', 'AddJobController@overWriteInvoices');


//Modal Contents
//
Route::get('roll-forward-selection', function () {
	return view('layouts.modal_content.roll_forward');
});

Route::post('rollback-search', 'HomeController@showJobRollbackSearch');
Route::get('filter-export-jobs', 'HomeController@exportJobFilter');
Route::get('users/removed', 'UsersController@showDeletedUsers')->name('users/removed');

Route::post('show-alert-notification', function () {
	return view('layouts.modal_content.alert');
});
Route::post('show-confirmation-notification', function () {
	return view('layouts.modal_content.confirmation');
});

// Route::get('/clear-cache', function() {
// 	$exitCode = Artisan::call('key:generate');
// 	// $exitCodeOne = Artisan::call('route:clear');
// 	// return what you want
// 	return 'Done!';
// });



Route::post('set-password', 'UsersController@setNewPassword')->name('set-password');
Route::post('status-job', 'AddJobController@editJobStatus');

Route::post('deactivate-company', 'CompaniesController@deactivateCompany');
Route::get('companies/deactivated', 'CompaniesController@disabledCompanies');

Route::post('invoices/generate-bulk-invoice', 'InvoicesController@generateBulkFiles');
Route::get('zip-directory/{batch}', 'InvoicesController@zipDirectory');
Route::get('zip-download/{batch}', 'InvoicesController@zipDownload');

Route::get('vendors', 'VendorsController@index');
Route::get('vendors/removed', 'VendorsController@showDeletedVendors')->name('vendors/removed');

Route::get('download-source/{job_id}/{source_type}/{file_name}', 'AddJobController@downloadSourceFile');
Route::post('vendor-quick-upload', 'AddJobController@vendorQuickUpload');
Route::post('admin-quick-upload', 'AddJobController@adminQuickUpload');
Route::post('quick-upload', 'AddJobController@quickUpload');
Route::get('get-users/{company_id}', 'AddJobController@showCompanyUsers');

Route::get('get-config', 'AddJobController@autoConfig');
Route::get('get-converted-file', 'AddJobController@getConvertedFile');
Route::get('get-job-file/{job_id}', 'AddJobController@getJobOutputFiles');
Route::get('getxbrl-file/{job_id}/{source_type}/{file}/{action}', 'AddJobController@readXbrlFile');

Route::post('send_comment', 'AddJobController@sendCommentAddOn');
