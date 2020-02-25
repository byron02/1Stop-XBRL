<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invoice;
use App\Models\JobsInvoiceRecipient;
use App\Models\JobStatus;
use App\Models\Taxonomy;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use App\Models\Job;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use DB;
use Zipper;
use File;

use App\Http\Traits\InvoiceGenerator;
class InvoicesController extends FrontsiteController
{
    use InvoiceGenerator;
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $roleTypes = [
            'CSR' => 3,
            'VENDOR' => 4,
            'CLIENT' => 1,
            'CLIENT_ADMIN' => 2,
            'CSR_ADMIN' => 8,
        ];


        $user = User::where('id', Auth::user()->id)->first();
        $role = UserRole::where('id', $user->role_id)->first();
        $companies = Company::orderBy('name', 'ASC');

        switch($role->id) {

            case $roleTypes['CSR']:

            case $roleTypes['CSR_ADMIN']:

                $invoices = new Invoice;

                break;

            case $roleTypes['VENDOR']:
                break;

            case $roleTypes['CLIENT']:

                $jobs = Job::where('user_id', Auth::user()->id)->pluck('id')->toArray();

                $invoices = Invoice::whereIn('job_id', $jobs);
                $companies = $companies->where('id',Auth::user()->company_id);
                break;

            case $roleTypes['CLIENT_ADMIN']:
                $jobs = Job::where('company', $user->company_id)->pluck('id')->toArray();

                $invoices = Invoice::whereIn('job_id', $jobs);
                $companies = $companies->where('id',Auth::user()->company_id);
                break;

            default:
                $invoices = Job::all();

        }
        $companies = $companies->where('autosend_invoice',1);
        $companies = $companies->get();
        
        if(request('filter_by') != '' && (request('search') != ''  || request('from_date')))
        {
            if(request('filter_by') == 'issued_to')
            {
                $column = 'CONCAT(users.first_name," ",users.last_name)';
            }
            elseif(request('filter_by') == 'company_name')
            {
                $column = 'companies.name';
            }
            else
            {
                $column = request('filter_by');
            }

            $filter = $column." LIKE '%".request('search')."%'";

            if(request('filter_by') == 'date')
            {
                $filter = "DATE(invoices.date_imported) BETWEEN '". date('Y-m-d',strtotime(request('from_date'))) ."' AND '". date('Y-m-d',strtotime(request('to_date'))) ."'";
            }

            $invoices = $invoices->whereRaw($filter);
        }
        $invoices = $invoices->select(DB::raw('invoices.*,jobs.project_name,jobs.purchase_order,companies.assign_invoice_to_project_name,jobs.is_invoiced,companies.name company_name,CONCAT(users.first_name," ",users.last_name) as issued_to'))
                    ->leftJoin('jobs','jobs.id','=','invoices.job_id')
                    ->leftJoin('companies','companies.id','=','jobs.company')
                    ->leftJoin('users','users.id','=','jobs.user_id');
        $invoices = $invoices->where('jobs.id','!=',null)->where('jobs.status','!=',9)->where('companies.autosend_invoice','1')->orderBy('invoices.job_id','desc');


        if(request('order_by'))
        {
            $invoices = $invoices->orderBy(request('order_by'),request('sort'));
        }
        else
        {
            $invoices = $invoices->orderBy('date_imported','DESC');
        }

        $invoices = $invoices->paginate(15);
        $invoice = $invoices->appends(Input::except('page'));
       
    
        foreach($invoices as $invoice) {
            $invoice_row = array();
            $invoice_row["is_paid"] =  $invoice->is_paid;
            $invoice_row["is_invoiced"] =  $invoice->is_invoiced;
            $invoice_row["invoice_number"] =  $invoice->invoice_number;
            $invoice_row["client_po"] = $invoice->purchase_order;
            $invoice_row["project_name"] = $invoice->project_name;
            $invoice_row["file_full_path"] = $this->gcloudUrl($invoice->invoice_number); 
            $invoice_row["issued_to"] = $invoice->issued_to; 
            $invoice_row["file_name"] = $invoice->invoice_number.'.xls';
            $invoice_row["company_name"] = $invoice->company_name;
            $invoice_row["date_imported"] = $invoice->date_imported;
            $invoiceMap[$invoice->id] = $invoice_row;
        
        }

        return view('layouts.invoices')
            ->with('invoiceMap', isset($invoiceMap) ? $invoiceMap : [])
            ->with('invoices', $invoices)
            ->with('companies', $companies)
            ->with('user_id', isset($targetInvoice) ? $targetInvoice["id"] : null);
    }

    public function store(Request $request) {
        return "success";
    }


    public function gcloudUrl($file) {
        if($file) {
            return Storage::disk('gcs')->url('invoices/' . $file.'.xls');
        }
    }

    public function downloadPDF($invoice_number,$type){
      $pdf = $this->invoicedPdf($invoice_number);
      if($type == 'view')
      {
          return $pdf->stream();
      }
      elseif($type == 'download')
      {
          return $pdf->download($invoice_number.'-'.time().'.pdf');
      }
      else
      {
        $content = $pdf->output();
        Storage::disk('public')->put('storage/pdf/'.$invoice_number.'.pdf', $content);
      }

    }

    public function createBatchInvoice($batch_no)
    {
        return File::makeDirectory('storage/app/public/storage/pdf/'.$batch_no, 0777, true);
        // return mkdir('storage/app/public/storage/pdf/'.$batch_no);
    }
    public function zipDirectory($batch)
    {
        $files = glob('storage/app/public/storage/pdf/'.$batch.'/*.pdf');
        Zipper::make('storage/app/public/storage/zip/'.$batch.'.zip')->add($files)->close();
    }

    public function generateBatchFolder()
    {
        $batch = 'batch_'. Auth::id() .'_'. time();
        $path = 'storage/app/public/storage/pdf/';
        $flag = 0;
        while(File::exists($path.$batch))
        {
            $batch = 'batch_'. Auth::id() .'_'. time();
        }
        $this->createBatchInvoice($batch);
        return $batch;
    }
    public function generateBulkFiles(Request $request)
    {
        $filter = $request->input('filter');
        $batch = $this->generateBatchFolder();
        
        if($filter == 'client')
        {
            $invoice = $this->getCompanyInvoice($request->input('client'),$request->input('from_date'),$request->input('to_date'));
            if(count($invoice) > 0)
            {
                $arr = array();
                $arr['batch'] = $batch;
                $arr['invoices'] = $invoice->toArray();
               
                echo json_encode($arr);
            }
        }
    }

    public function createPdfInvoice($batch,$invoice_number)
    {
        $pdf = $this->invoicedPdf($invoice_number);
        $content = $pdf->output();
        Storage::disk('public')->put('storage/pdf/'.$batch.'/'.$invoice_number.'-'.time().'.pdf', $content);
    }

    public function zipDownload($batch)
    {
        File::deleteDirectory('storage/app/public/storage/pdf/'.$batch);
        return response()->download('storage/app/public/storage/zip/'.$batch.'.zip', $batch.'.zip')->deleteFileAfterSend(true);
    }

    public function getCompanyInvoice($company_id,$from_date,$to_date)
    {
        $invoices = DB::table('invoices')->select(DB::raw('invoices.invoice_number,jobs.project_name,DATE_FORMAT(invoices.date_imported,"%d-%m-%Y") date_created'))
                    ->leftJoin('jobs','jobs.id','=','invoices.job_id')
                    ->leftJoin('companies','companies.id','=','jobs.company')
                    ->where('jobs.company',$company_id)
                    ->whereDate('invoices.date_imported','>=', date('Y-m-d',strtotime($from_date)))
                    ->whereDate('invoices.date_imported','<=',date('Y-m-d',strtotime($to_date)))
                    ->get();
        return $invoices;
    }

  
}
