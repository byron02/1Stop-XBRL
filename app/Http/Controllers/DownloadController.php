<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientInvoicesRequest;
use App\Http\Requests\InvoiceDateRange;
use App\Http\Requests\InvoiceNumberRequest;
use App\Http\Requests\JobIdsRequest;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoiceStatus;
use App\Models\JobStatus;
use App\Models\Taxonomy;
use App\Models\User;
use App\Models\UserRole;
use Carbon\Carbon;
use Chumper\Zipper\Facades\Zipper;
use Illuminate\Http\Request;
use App\Models\Job;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function download(Request $request) {
 
        if ($request['job_number']) {
            
            $jobId = $request['job_number'];
            $job = Job::where('id', $jobId)->first();
            $invoice = Invoice::where('id', $job->invoice)->first();

 
            if (!is_null($invoice)) {
                $filename = $invoice->invoice_number.'.xls';
            } else {
                return "File does not exist";
            }
            
        } else {
            $filename = $request['file_name'];   
        }
        $f = explode('.',$filename);
        $old_invoice = InvoiceStatus::where('invoice_number', $f[0])->first();
        if(!empty($old_invoice))
        {
            $trim_filename = explode('/',$old_invoice->invoice);
            $filename = end($trim_filename);
        }

        if(Storage::disk('gcs')->exists('invoices/'.$filename)) {

            $file = Storage::disk('gcs')->get('invoices/'.$filename);

            $targetFullStoragePath = storage_path("app/public/storage/{$filename}");
            $targetRelativeStoragePath = 'public/storage/'.$filename;
            Storage::disk('local')->put($targetRelativeStoragePath, $file);

            if(Storage::disk('local')->exists($targetRelativeStoragePath)) {
                return response()->download($targetFullStoragePath, $filename);
            }

            return "File does not exist";
        } else {
            return 'Error' . $filename;
        }
    }

    public function zipWithJobIdFilters(JobIdsRequest $request) {

        $invoices = Invoice::whereIn('job_id',  $request['job_ids'])->get();
        if(!isset($invoices[0]))
        {
            echo 'invalid';
            exit; 
        }
        else
        {
            $invoices = $this->filterAuthorization(Auth::user()->id, $invoices->pluck('invoice_number')->toArray());
            return $this->zipInvoices($invoices);
        }


    }

    public function downloadInvoiceNumber(InvoiceNumberRequest $request) {

        $start = $request['invoice_start'];
        $end = $request['invoice_end'];

        if(strlen($start) > 3) {
            $trimmedStart = substr($start, 0, 4);
        } else {
            $trimmedStart = $start;
        }

        if(strlen($end) > 3) {

            $trimmedEnd = substr($end, 0, 4);
            $trimmedEnd = $trimmedEnd + 1;
        } else {
            $trimmedEnd = $end;
        }
        $invoices = Invoice::where('invoice_number', '>=', $trimmedStart.'%')
                            ->where('invoice_number', '<=', $trimmedEnd.'%' )->get();

        $invoices = $this->filterAuthorization(Auth::user()->id, $invoices->pluck('invoice_number')->toArray());
        return $this->zipInvoices($invoices);

    }

    public function downloadClient(ClientInvoicesRequest $request) {

        $companyId = $request['company'];


        $start = Carbon::parse($request['client_start_date'])->format('Y-m-d');
        $end = Carbon::parse($request['client_end_date'])->format('Y-m-d');

        $startDateWithTime = $start.' 00:00:00';
        $endDateWithTime = $end.' 23:59:59';

        $invoicesDateFilteredArray = InvoiceStatus::whereBetween('date_created',
            [$startDateWithTime, $endDateWithTime])
            ->pluck('invoice_number')->toArray();

        $jobIds = Job::where('company', $companyId)->pluck('id')->toArray();

        $invoices = Invoice::whereIn('job_id',  $jobIds)
                ->whereIn('invoice_number', $invoicesDateFilteredArray)->get();

       if(isset($invoices[0]))
       {
            $invoices = $this->filterAuthorization(Auth::user()->id, $invoices->pluck('invoice_number')->toArray());
            return $this->zipInvoices($invoices);
       }
       else
       {
            echo 'invalid';
            exit; 
       }
       

    }

    public function downloadDateRange(InvoiceDateRange $request) {


        $start = Carbon::parse($request['date_range_start_date'])->format('Y-m-d');
        $end = Carbon::parse($request['date_range_end_date'])->format('Y-m-d');

        $startDateWithTime = $start.' 00:00:00';
        $endDateWithTime = $end.' 23:59:59';

        $invoicesArray = InvoiceStatus::whereBetween('date_created',
            [$startDateWithTime, $endDateWithTime])->pluck('invoice_number')->toArray();

        $invoices = Invoice::whereIn('invoice_number',  $invoicesArray)->get();

       
        if(isset($invoices[0]))
        {
             $invoices = $this->filterAuthorization(Auth::user()->id, $invoices->pluck('invoice_number')->toArray());
             return $this->zipInvoices($invoices);
        }
        else
        {
            echo 'invalid';
            exit;
        }
       
        

    }

    /**
     * @param $invoices
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function zipInvoices($invoices)
    {
        $files = array();
        foreach ($invoices as $invoice) {

            $filename = $invoice['invoice_number'] . '.xls';

            if (Storage::disk('gcs')->exists('invoices/' . $filename)) {

                $file = Storage::disk('gcs')->get('invoices/' . $filename);

                $targetFullStoragePath = storage_path("app/public/storage/{$filename}");
                $targetRelativeStoragePath = 'public/storage/' . $filename;
                Storage::disk('local')->put($targetRelativeStoragePath, $file);


                if (Storage::disk('local')->exists($targetRelativeStoragePath)) {
                    $files[] = $targetFullStoragePath;
                }

            }
        }



        // $name = Carbon::now()->toDateTimeString();
        $name = 'invoices_'. time();
        $targetFullStoragePath = storage_path("invoices/zip");
        Zipper::make($targetFullStoragePath . '/'.$name.'.zip')->add($files)->close();

        $responseArray = ['filename' => $name.'.zip', 'status' => '200'];
         
        return Response::json($responseArray);

    }

    function downloadWithFileName(Request $request) {
        $filename = $request['filename'];
        $targetFullStoragePath = storage_path("invoices/zip");
        return response()->download($targetFullStoragePath . '/'.$filename, $filename)->deleteFileAfterSend(true);
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
