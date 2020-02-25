<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateAccountingSheetRequest;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoiceStatus;
use App\Models\JobStatus;
use App\Models\Country;
use App\Models\CancelledJob;
use App\Models\Taxonomy;
use App\Models\User;
use App\Models\UserRole;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Job;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;

class AccountingSheetController extends FrontsiteController
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return view('layouts.accounting_sheet');
    }

    public function generateCsv(GenerateAccountingSheetRequest $request) {

        $targetFullStoragePath = storage_path("accounting/csv");


        // $name = Carbon::now()->toDateTimeString();
        $name = 'accounting_sheet_'.time();
        $columnHeaders = array('*ContactName',
            'EmailAddress',
            'POAddressLine1',
            'POAddressLine2',
            'POAddressLine3',
            'POAddressLine4',
            'POCity',
            'PORegion',
            'POPostalCode',
            'POCountry',
            '*InvoiceNumber',
            'Reference',
            '*InvoiceDate',
            '*DueDate',
            //Removed 2 columns here
            'Total',
            //Insert InventoryCode
            'InventoryCode',
            '*Description',
            '*Pages',
            '*Quantity',
            '*UnitAmount',
            'Discount',
            '*AccountCode',
            '*TaxType',
            'TaxAmount',
            // 'TrackingName1',
            // 'TrackingOption1',
            // 'TrackingName2',
            // 'TrackingOption2',
            //Insert 2 columns
            // 'Currency',
            // 'BrandingTheme'
            );


        Excel::create($name, function($excel) use($columnHeaders, $request)  {

            $excel->sheet('Sheetname', function($sheet) use($columnHeaders, $request) {

                $sheet->appendRow($columnHeaders);


                $start = Carbon::parse($request['start_date'])->format('Y-m-d');
                $end = Carbon::parse($request['end_date'])->format('Y-m-d');

                $startDateWithTime = $start.' 00:00:00';
                $endDateWithTime = $end.' 23:59:59';
               
                
                $invoicesStatus = InvoiceStatus::whereBetween('date_created',
                    [$startDateWithTime, $endDateWithTime]);


                $invoicesStatu2s = Invoice::whereBetween('date_imported',
                    [$startDateWithTime, $endDateWithTime]);

                $cancelledJobs = CancelledJob::all();

                $invoices = Invoice::whereIn('invoice_number',  $invoicesStatus->pluck('invoice_number')->toArray())->orWhereIn('invoice_number',  $invoicesStatu2s->pluck('invoice_number')->toArray())->get();

                $invoices = $this->filterAuthorization(Auth::user()->id, $invoices->pluck('invoice_number')->toArray());

                if($invoices->count() > 0) {
                    foreach($invoices as $invoice) {
                        $data = array();
                        $accountCode = '';
                        $taxType = '';
                        $jobId = $invoice['job_id'];
                        $job = Job::where('id', $jobId)->whereNotIn('id', $cancelledJobs->pluck('job_id'))->where('is_invoiced', 1)->groupBy('project_name')->orderBy('due_date', 'desc')->get()->first();
                        // $company = Company::where('id', $job['company'])->where('autosend_invoice', '1')->groupBy('name')->get()->first();
                        $company = Company::where('id', $job['company'])->where('autosend_invoice', '1')->groupBy('name')->get()->first();
                        $country = Country::where('id', $company['country'])->get()->first();

                        if($country['is_eu_based'] == 1 && $country['id'] == 222){
                            $accountCode = '200';
                            $taxType = '20% (VAT on Income)';
                        }

                        if($country['is_eu_based'] == 1 && $country['id'] != 222){
                            $accountCode = '201';
                            $taxType = 'No VAT';
                        }

                        if($country['is_eu_based'] == 0 && $country['id'] != 222){
                            $accountCode = '202';
                            $taxType = 'Zero Rated Income';
                        }
                        
                        $invoiceStatus = InvoiceStatus::where('invoice_number')->get()->first();
                        if($company['name'] == '') continue;
                        $data[] = $company['name'];
                        $data[] = '';
                        $data[] = '';
                        $data[] = '';
                        $data[] = '';
                        $data[] = '';
                        $data[] = '';
                        $data[] = '';
                        $data[] = '';
                        $data[] = '';
                        // $data[] = $company['email'];
                        // $data[] = $company['address1'];
                        // $data[] = $company['address2'];
                        // $data[] = $company['address3'];
                        // $data[] = ''; //POAddressLine4
                        // $data[] = $company['city'];
                        // $data[] = $company['region'];
                        // $data[] = $company['postcode'];
                        // $data[] = $company['country'];
                        $data[] = $invoice['invoice_number'];
                        $data[] = $job['project_name'];
                        // $data[] = $invoiceStatus['date_created'];
                        // $data[] = date('d/m/Y',strtotime($invoice['date_imported'])) != '01/01/1970' ? " ".date('d/m/Y',strtotime($invoice['date_imported'])) : '';
                        // $data[] = trim($invoice['date_imported']) == '-0001-11-30 00:00:00' ? '' : " ".date('d/m/Y',strtotime($invoice['date_imported']));
                        // $data[] = date('d/m/Y',strtotime($job['due_date'])) != '01/01/1970' ? " ".date('d/m/Y',strtotime($job['due_date'])) : '';// due date
                        $data[] = date('d/m/Y',strtotime($job['due_date'])) != '01/01/1970' ? " ".Carbon::parse($job['due_date'])->format('Y-m-d') : '';// due date
                        // $data[] = date('d/m/Y',strtotime($job['due_date'])) != '01/01/1970' ? " ".date('d/m/Y',strtotime($job['due_date'])) : '';// due date
                        $data[] = date('d/m/Y',strtotime($job['due_date'])) != '01/01/1970' ? " ".Carbon::parse($job['due_date'])->format('Y-m-d') : '';// due date
                        //Removed 2 columns
                        $data[] = '';// Total
                        //New field
                        $data[] = ''; //[NEW] InventoryCode
                        $data[] = $job['project_name']; //Description
                        $data[] = $job['total_pages_submitted'];
                        $data[] = '1';// Quantity
                        $data[] = $job['computed_price'] - $job['tax_computation_price'];// Unit Amount
                        $data[] = '';// Discount
                        // $data[] = $company['discount_rate'];// Discount
                        $data[] = $accountCode;// Account Code (200?)
                        $data[] = $taxType;// TaxType
                        $data[] = '';// TaxAmount
                        // $data[] = '';// TrackingName1
                        // $data[] = '';// TrackingOption1
                        // $data[] = '';// TrackingName2
                        // $data[] = '';// TrackingOption2
                        // //Insert 2 columns
                        // $data[] = '';//Currency
                        // $data[] = ''; //BrandingTheme

                        $sheet->appendRow($data);
                    }
                }

            });

        })->store('csv', $targetFullStoragePath);


        $responseArray = ['filename' => $name.'.csv', 'status' => '200'];

        return Response::json($responseArray);
//        return response()->download($targetFullStoragePath . '/'. $name.'.csv', $name.'.csv');
    }

    public function downloadCsv(Request $request) {
        $targetFullStoragePath = storage_path("accounting/csv");
        $name = $request['filename'];

        return response()->download($targetFullStoragePath . '/'. $name, $name)->deleteFileAfterSend(true);
    }

    public function store(Request $request) {
        return "success";
    }

    function filterAuthorization($userId, $invoicesArray) {
        $roleTypes = [
            'CSR' => 3,
            'VENDOR' => 4,
            'CLIENT' => 1,
            'CLIENT_ADMIN' => 2,
            'CSR_ADMIN' => 8,
        ];


        $user = User::where('id', $userId)->first();

        $role = UserRole::where('id', $user->role_id)->first();


        switch($role->id) {
            case $roleTypes['CSR']:
            case $roleTypes['CSR_ADMIN']:
                $invoices = Invoice::whereIn('invoice_number', $invoicesArray)->get();
                break;
            case $roleTypes['VENDOR']:

                break;

            case $roleTypes['CLIENT']:


                $jobs = Job::where('user_id', $userId)->pluck('id')->toArray();


                $invoices = Invoice::whereIn('job_id', $jobs)->
                whereIn('invoice_number',$invoicesArray)->get();
                break;

            case $roleTypes['CLIENT_ADMIN']:


                $jobs = Job::where('company', $user->company_id)->pluck('id')->toArray();


                $invoices = Invoice::whereIn('job_id', $jobs)->
                whereIn('invoice_number',$invoicesArray)->get();
                break;

            default:
                $invoices = Job::all();

        }

        return $invoices;

    }
}
