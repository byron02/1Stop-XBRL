<?php

namespace App\Http\Controllers;

use App\Http\Utils\JobsCollector;
use App\Http\Utils\JobsTransformer;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\JobStatus;
use App\Models\InvoiceStatus;
use App\Models\Job;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Session;
use DB;
use Response;
use Illuminate\Support\Facades\Storage;
class HomeController extends FrontsiteController
{
    /**
     * Create a new controller instance.
     *
     * @return void

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if($user->status != 1)
        {
            $name = $user->first_name.' '.$user->last_name;
            Session::put('name',ucwords($name));
            Auth::logout();
            return redirect('/thankyou');
        }
        if (!Auth::check()) {
            // The user is logged in...
            return;
        }

        $bannerStatus = DB::table('job_status AS js')
                        ->select(DB::raw('js.*,COUNT(jb.id) job_count'))
                        ->leftJoin('jobs AS jb','jb.status','=','js.id')
                        ->whereRaw('js.name IN ("New Order","In Progress","In Revision","Revisions Submitted")')
                        ->groupBy('js.id');
        
        $jobs = JobsCollector::collectJobs();
        $limit = 100;

        if($user->role_id == 4)
        {
            $jobs = $jobs->where('vendor_id','=',$user->id);
            $bannerStatus =  $bannerStatus->where('jb.vendor_id','=',$user->id);
            $limit = 50;
        }
        elseif($user->role_id != 8)
        {
            $bannerStatus =  $bannerStatus->where('jb.company','=',$user->company_id);
            $jobs = $jobs->where('company','=',$user->company_id);
            $limit = 50;
        }

        $bannerStatus = $bannerStatus->get();

        $jobs = $jobs->select(DB::raw('jobs.*,invoices.invoice_number,companies.name as company_name'))
                ->leftJoin('invoices','invoices.job_id','=','jobs.id')
                ->leftJoin('companies','companies.id','=','jobs.company')
                ->groupBy('jobs.id');

        
        if(request('status') != null)
        {
           
            $jobs = JobsCollector::collectJobs()->orderBy('date_added', 'desc')
                    ->where('status','=',request('status'));

            if($user->role_id == 4)
            {
                $jobs = $jobs->select(DB::raw('jobs.*,invoices.invoice_number,companies.name as company_name'))
                        ->leftJoin('invoices','invoices.job_id','=','jobs.id')
                        ->leftJoin('companies','companies.id','=','jobs.company')
                        ->groupBy('jobs.id');
                $jobs = $jobs->where('jobs.vendor_id','=',$user->id);
                $limit = 50;
            }
            elseif($user->role_id != 8)
            {

                $jobs = $jobs->select(DB::raw('jobs.*,invoices.invoice_number,companies.name as company_name'))
                        ->leftJoin('invoices','invoices.job_id','=','jobs.id')
                        ->leftJoin('companies','companies.id','=','jobs.company')
                        ->groupBy('jobs.id');
                $jobs = $jobs->where('jobs.company','=',$user->company_id);
                $limit = 50;
            }
                    
                   
        }
        else
        {
            $jobs = $jobs->where('jobs.status','!=',9);
        }
       
        if(request('order_by') != '')
        {
            $jobs = $jobs->orderBy(request('order_by'),request('sort'));
        }
        else
        {
             $jobs = $jobs->orderBy('id','desc');
        }

        $jobs = $jobs->paginate($limit);    

        $jobs->appends(Input::except('page'));
        $jobsMap = JobsTransformer::transformToJobsMap($jobs);
        $jobStatus = JobStatus::all();
        
  
        return view('layouts.job_table')
            ->with('jobStatuses', $jobStatus)
            ->with('jobsMap', $jobsMap)
            ->with('banner_status',$bannerStatus)
            ->with('jobs', $jobs);
    }

    public function searchJobs(Request $request) {
        $user = Auth::user();
        $jobStatus = JobStatus::all();
        $bannerStatus = DB::table('job_status AS js')
                        ->select(DB::raw('js.*,COUNT(jb.id) job_count'))
                        ->leftJoin('jobs AS jb','jb.status','=','js.id')
                        ->whereRaw('js.name IN ("New Order","In Progress","In Revision","Revisions Submitted")')
                        ->groupBy('js.id')
                        ->get();

        $jobs = JobsCollector::collectJobs();
      
        if ($request['search_by'] == 1) {

            $this->validateSearchByStatusDates($request->all())
                ->setAttributeNames(array(
                    'search_by_status_start_date' => 'start date',
                    'search_by_status_end_date' => 'end date'
                ))
                ->validate();

            $jobStatusId = $request['search_by_status'];
            $startDate = Carbon::parse($request['search_by_status_start_date'])->format('Y-m-d');
            $endDate = Carbon::parse($request['search_by_status_end_date'])->format('Y-m-d');

            $searchedJobs =  $jobs->select(DB::raw('jobs.*,invoices.invoice_number,companies.name as company_name'))
                ->leftJoin('invoices','invoices.job_id','=','jobs.id')
                ->leftJoin('companies','companies.id','=','jobs.company')
                ->where('status', $jobStatusId)
                ->whereBetween('jobs.date_added', [$startDate, $endDate])
                ->orderBy('date_added', 'desc')
                ->orderBy('id', 'desc');

                
            if($user->role_id != 8)
            {
                $searchedJobs = $searchedJobs->where('jobs.company','=',$user->company_id);
            }

            $searchedJobs = $searchedJobs->paginate(50);
            $searchedJobs->appends(Input::except('page'));

            $jobsMap = JobsTransformer::transformToJobsMap($searchedJobs);

            return view('layouts.job_table')
                ->with('jobStatuses', $jobStatus)
                ->with('jobsMap', $jobsMap)
                ->with('banner_status',$bannerStatus)
                ->with('jobs', $searchedJobs);

        } else {

            $queryBy = $request['search_by_query'];
            $query = $request['query'];
            $queryDate = $request['query_date'];
            $queryMonth = $request['query_month'];

            if ($queryBy == "id" || $queryBy == "project_name") {
                if($queryBy == 'id')
                {
                    $queryBy = 'jobs.id';
                    
                }

                $searchedJobs =  $jobs->select(DB::raw('jobs.*,invoices.invoice_number,companies.name as company_name'))
                ->leftJoin('invoices','invoices.job_id','=','jobs.id')
                ->leftJoin('companies','companies.id','=','jobs.company')
                    ->where($queryBy, 'LIKE', '%'.$query.'%')
                    ->orderBy('date_added', 'desc')
                    ->orderBy('id', 'desc');


                if($user->role_id != 8)
                {
                    $searchedJobs = $searchedJobs->where('jobs.company','=',$user->company_id);
                }

                $searchedJobs = $searchedJobs->paginate(50);

                $searchedJobs->appends(Input::except('page'));

                $jobsMap = JobsTransformer::transformToJobsMap($searchedJobs);


                return view('layouts.job_table')
                    ->with('jobStatuses', $jobStatus)
                    ->with('jobsMap', $jobsMap)
                    ->with('banner_status',$bannerStatus)
                    ->with('jobs', $searchedJobs);

            } else if ($queryBy == "company_name") {

                $companyIds  = Company::where('name', 'LIKE', '%'.$query.'%')
                    ->pluck('id')
                    ->toArray();

                $searchedJobs = $jobs->select(DB::raw('jobs.*,invoices.invoice_number,companies.name as company_name'))
                ->leftJoin('invoices','invoices.job_id','=','jobs.id')
                ->leftJoin('companies','companies.id','=','jobs.company')
                    // ->whereIn('company', $companyIds)
                    ->where('companies.name','LIKE','%'.$query.'%')
                    ->groupBy('jobs.id')
                    ->orderBy('id', 'desc')
                    ->orderBy('date_added', 'desc');
                    


               if($user->role_id != 8)
                {
                    $searchedJobs = $searchedJobs->where('jobs.company','=',$user->company_id);
                }

                $searchedJobs = $searchedJobs->paginate(50);
                $searchedJobs->appends(Input::except('page'));

                $jobsMap = JobsTransformer::transformToJobsMap($searchedJobs);

            
                return view('layouts.job_table')
                    ->with('jobStatuses', $jobStatus)
                    ->with('jobsMap', $jobsMap)
                    ->with('banner_status',$bannerStatus)
                    ->with('jobs', $searchedJobs);

            } else if ($queryBy == "by_month") {

                $this->validateSearchByQueryMonth($request->all())
                    ->setAttributeNames(array(
                        'query_month' => 'month'
                    ))
                    ->validate();

                $date = date_format(Carbon::parse($queryMonth), "Y-m-d");
                $date = Carbon::parse($date);

                $startMonth = new Carbon($date->startOfMonth());
                $endMonth = new Carbon($date->endOfMonth());

                $searchedJobs =  $jobs->select(DB::raw('jobs.*,invoices.invoice_number,companies.name as company_name'))
                    ->leftJoin('invoices','invoices.job_id','=','jobs.id')
                     ->leftJoin('companies','companies.id','=','jobs.company')
                    ->whereBetween('due_date', [$startMonth, $endMonth])
                    ->orderBy('date_added', 'desc')
                    ->orderBy('id', 'desc');
                if($user->role_id != 8)
                {
                    $searchedJobs = $searchedJobs->where('jobs.company','=',$user->company_id);
                }

                $searchedJobs = $searchedJobs->paginate(50);
                $searchedJobs->appends(Input::except('page'));

                $jobsMap = JobsTransformer::transformToJobsMap($searchedJobs);

                return view('layouts.job_table')
                    ->with('jobStatuses', $jobStatus)
                    ->with('jobsMap', $jobsMap)
                    ->with('banner_status',$bannerStatus)
                    ->with('jobs', $searchedJobs);

            } else {

                $this->validateSearchByQueryDate($request->all())
                    ->setAttributeNames(array(
                        'query_date' => 'date'
                    ))
                    ->validate();

                $date = date_format(Carbon::parse($queryDate),'Y-m-d');
                $date = Carbon::parse($date);

                $searchedJobs =  $jobs->select(DB::raw('jobs.*,invoices.invoice_number,companies.name as company_name'))
                    ->leftJoin('invoices','invoices.job_id','=','jobs.id')
                    ->leftJoin('companies','companies.id','=','jobs.company')
                    ->where($queryBy, $date)
                    ->orderBy('date_added', 'desc')
                    ->orderBy('id', 'desc');
                if($user->role_id != 8)
                {
                    $searchedJobs = $searchedJobs->where('jobs.company','=',$user->company_id);
                }

                $searchedJobs = $searchedJobs->paginate(50);
                $searchedJobs->appends(Input::except('page'));

                $jobsMap = JobsTransformer::transformToJobsMap($searchedJobs);

                return view('layouts.job_table')
                    ->with('jobStatuses', $jobStatus)
                    ->with('jobsMap', $jobsMap)
                    ->with('banner_status',$bannerStatus)
                    ->with('jobs', $searchedJobs);
            }
        }

    }

    protected function validateSearchByStatusDates(array $data) {
        return Validator::make($data, [
            'search_by_status_start_date' => 'required|date_format:d-m-Y|max:255|before:search_by_status_end_date',
            'search_by_status_end_date' => 'required|date_format:d-m-Y|max:255|after:search_by_status_start_date'
        ]);
    }

    protected function validateSearchByQueryDate(array $data) {
        return Validator::make($data, [
            'query_date' => 'required|date_format:d-m-Y|max:255'
        ]);
    }

    protected function validateSearchByQueryMonth(array $data) {
        return Validator::make($data, [
            'query_month' => 'required|date_format:Y-m|max:255'
        ]);
    }

    public function exportJob($from = '',$to = '')
    {
        $jobInvoice = $this->getJobByInvoice('invoice',$from,$to);
        $jobNonInvoice = $this->getJobByNonInvoice('',$from,$to);
        $user = Auth::user()->toArray();
        if(isset($jobInvoice[0]) || isset($jobNonInvoice[0]))
        {
            $jobInvoice = $jobInvoice->toArray();
            $jobNonInvoice = $jobNonInvoice->toArray();

            $filename = 'csr_jobs_csv_'. date('YmdHis');
            $targetFullStoragePath = storage_path("sample");
            $arr = array('Country', 'Job Number', 'Project Name', 'Client PO', 'No of Pages', 'Gross Price', 'Net Price', 'Company', 'Due Date', 'Status Vendor','Invoice','Paid');
            if($user['role_id'] == 4)
            {
                  $arr = array('Job Number', 'Project Name', 'No of Pages','Company Number', 'Accounting Standards', 'Due Date', 'Status');  
            }
           Excel::create($filename, function($excel) use($arr,$jobInvoice,$jobNonInvoice,$user) {
                $excel->sheet('Sheet 1', function($sheet) use($arr,$jobInvoice,$user) {
                    $i = 0;
                    while(true)
                    {
                        if($i < count($arr))
                        {
                            $col = chr($i+65);
                            $sheet->cell($col.'1', $arr[$i]);
                        }
                        
                        if($i < count($jobInvoice))
                        {
                            if($user['role_id'] == 4)
                            {
                                $sheet->cell('A'.($i+2), $jobInvoice[$i]['id']);
                                $sheet->cell('B'.($i+2), $jobInvoice[$i]['project_name']);
                                $sheet->cell('C'.($i+2), $jobInvoice[$i]['total_pages_submitted']);
                                $sheet->cell('D'.($i+2), $jobInvoice[$i]['company']);
                                $sheet->cell('E'.($i+2), $jobInvoice[$i]['taxonomy_type']);
                                $sheet->cell('F'.($i+2), date('Y-m-d',strtotime($jobInvoice[$i]['due_date'])));
                                // $sheet->cell('F'.($i+2), date('M d,Y',strtotime($jobInvoice[$i]['due_date'])));
                                $sheet->cell('G'.($i+2), $jobInvoice[$i]['vendor_status']);
                            }
                            else
                            {
                                $sheet->cell('A'.($i+2), $jobInvoice[$i]['country_name']);
                                $sheet->cell('B'.($i+2), $jobInvoice[$i]['id']);
                                $sheet->cell('C'.($i+2), $jobInvoice[$i]['project_name']);
                                $sheet->cell('D'.($i+2), $jobInvoice[$i]['purchase_order']);
                                $sheet->cell('E'.($i+2), $jobInvoice[$i]['total_pages_submitted']);
                                $sheet->cell('F'.($i+2), $jobInvoice[$i]['computed_price']);
                                $sheet->cell('G'.($i+2), $jobInvoice[$i]['computed_price'] - $jobInvoice[$i]['tax_computation_price']);
                                $sheet->cell('H'.($i+2), $jobInvoice[$i]['company_name']);
                                $sheet->cell('I'.($i+2), date('Y-m-d',strtotime($jobInvoice[$i]['due_date'])));
                                $sheet->cell('J'.($i+2), $jobInvoice[$i]['vendor_status']);
                                $sheet->cell('K'.($i+2), $jobInvoice[$i]['invoice_number']);
                                $sheet->cell('L'.($i+2), ($jobInvoice[$i]['is_paid'] != 1 ? '' : 'Paid'));
                            }
                           
                           
                        }

                        if($i >= count($arr) && $i >= count($jobInvoice))
                        {
                            if($user['role_id'] == 4)
                            {
                                $sheet->setBorder('A1:G'.$i, 'thin');
                            }
                            else
                            {
                                $sheet->setBorder('A1:L'.$i, 'thin');
                            }
                            $sheet->row(1, function($row) {
                                    $row->setBackground('#e6b9b8');

                                });
                            break;
                        }
                        $i++;
                    }
                   
                    
                });

                $excel->sheet('Sheet 2', function($sheet) use($arr,$jobNonInvoice,$user) {

                    $i = 0;
                    while(true)
                    {
                        if($i < count($arr))
                        {
                            $col = chr($i+65);
                            $sheet->cell($col.'1', $arr[$i]);
                        }
                        
                        if($i < count($jobNonInvoice))
                        {
                            if($user['role_id'] == 4)
                            {
                                $sheet->cell('A'.($i+2), $jobNonInvoice[$i]['id']);
                                $sheet->cell('B'.($i+2), $jobNonInvoice[$i]['project_name']);
                                $sheet->cell('C'.($i+2), $jobNonInvoice[$i]['total_pages_submitted']);
                                $sheet->cell('D'.($i+2), $jobNonInvoice[$i]['company']);
                                $sheet->cell('E'.($i+2), $jobNonInvoice[$i]['taxonomy_type']);
                                $sheet->cell('F'.($i+2), date('Y-m-d',strtotime($jobNonInvoice[$i]['due_date'])));
                                $sheet->cell('G'.($i+2), $jobNonInvoice[$i]['vendor_status']);
                            }
                            else
                            {
                                $sheet->cell('A'.($i+2), $jobNonInvoice[$i]['country_name']);
                                $sheet->cell('B'.($i+2), $jobNonInvoice[$i]['id']);
                                $sheet->cell('C'.($i+2), $jobNonInvoice[$i]['project_name']);
                                $sheet->cell('D'.($i+2), $jobNonInvoice[$i]['purchase_order']);
                                $sheet->cell('E'.($i+2), $jobNonInvoice[$i]['total_pages_submitted']);
                                $sheet->cell('F'.($i+2), $jobNonInvoice[$i]['computed_price']);
                                $sheet->cell('G'.($i+2), $jobNonInvoice[$i]['computed_price'] -  $jobNonInvoice[$i]['tax_computation_price']);
                                $sheet->cell('H'.($i+2), $jobNonInvoice[$i]['company_name']);
                                $sheet->cell('I'.($i+2), date('Y-m-d',strtotime($jobNonInvoice[$i]['due_date'])));
                                $sheet->cell('J'.($i+2), $jobNonInvoice[$i]['vendor_status']);
                                $sheet->cell('K'.($i+2), 'N/A');
                                $sheet->cell('L'.($i+2), ($jobNonInvoice[$i]['is_paid'] != 1 ? '' : 'Paid'));
                            }
                           
                        }

                        if($i >= count($arr) && $i >= count($jobNonInvoice))
                        {
                            if($user['role_id'] == 4)
                            {
                                $sheet->setBorder('A1:G'.$i, 'thin');
                            }
                            else
                            {
                                $sheet->setBorder('A1:L'.$i, 'thin');
                            }
                            
                            $sheet->row(1, function($row) {
                                $row->setBackground('#e6b9b8');

                            });
                            break;
                        }
                        $i++;
                    }
                    // $sheet->setAllBorders('thin');
                    
                });

                $excel->setActiveSheetIndex(0);

           })->export('xls');
           // ->store('xls', $targetFullStoragePath);

            
            // $handle = fopen($filename, 'w+');
            // fputcsv($handle, array('Company Country', 'Job Number', 'Project Name', 'Client PO', 'No of Pages', 'Net Price', 'Gross Price', 'Company', 'Due Date', 'Status Vendor','Invoice','Paid'));
            
            // foreach($jobs as $k => $each)
            // {
            //     $arr = [];
            //     $arr[] = $each['country_name'];
            //     $arr[] = $each['id'];
            //     $arr[] = $each['project_name'];
            //     $arr[] = $each['purchase_order'];
            //     $arr[] = $each['total_pages_submitted'];
            //     $arr[] = $each['computed_price'];
            //     $arr[] = $each['computed_price'] - $each['tax_computation_price'];
            //     $arr[] = $each['company_name'];
            //     $arr[] = date('M d,Y',strtotime($each['due_date']));
            //     $arr[] = $each['vendor_status'];
            //     $arr[] = $each['invoice_number'] != '' ? $each['invoice_number'] : 'N/A';
            //     $arr[] = $each['is_paid'] != 1 ? '' : 'Paid';
            //     fputcsv($handle,$arr);
            // }

            // fclose($handle);
            // $headers = array(
            //                 'Content-Type' => 'text/csv',
            //             );
            // return Response::download($filename,  $filename, $headers);
           
        }

    }

    public function getJobByInvoice($invoice,$from,$to)
    {
        
        $operator = $invoice != '' ? '!=' : '=';
        $user = Auth::user();
        $jobs = Job::select(DB::raw('jobs.*,countries.name country_name,companies.name company_name,job_status.name vendor_status,invoices.invoice_number,taxonomy.name as taxonomy_type'))
                ->leftJoin('companies','companies.id','=','jobs.company')
                ->leftJoin('countries','companies.country','=','countries.id')
                ->leftJoin('job_status','job_status.id','=','jobs.status')
                ->leftJoin('taxonomy','taxonomy.id','=','jobs.taxonomy')
                ->leftJoin('invoices','invoices.job_id','=','jobs.id')
                ->where('jobs.status', '!=', '9')
                ->where('jobs.is_invoiced', '=', '1')
                ->where('companies.autosend_invoice', '=', '1')
                ->orderBy('jobs.due_date')
                ->groupBy('jobs.project_name');


        // $jobs =  $jobs->where('invoices.invoice_number',$operator,null);
        // $jobs =  $jobs->where('jobs.is_invoiced',$operator,1);

        if($from != '' && $to != '')
        {
            $from = Carbon::parse($from)->format('Y-m-d');
            $to = Carbon::parse($to)->format('Y-m-d');
            $from = date('Y-m-d',strtotime($from));
            $to = date('Y-m-d',strtotime($to));
            $jobs = $jobs->whereBetween('jobs.date_added', [$from, $to]);
        }

        if($user->role_id == 4)
        {
            $jobs = $jobs->where('jobs.vendor_id',$user->id);
            $jobs = $jobs->where('jobs.status','!=',9);
        }

        $jobs = $jobs->get();
        return $jobs;
    }

    public function getJobByNonInvoice($invoice,$from,$to)
    {
        
        $operator = $invoice != '' ? '!=' : '=';
        $user = Auth::user();
        $jobs = Job::select(DB::raw('jobs.*,countries.name country_name,companies.name company_name,job_status.name vendor_status,invoices.invoice_number,taxonomy.name as taxonomy_type'))
                ->leftJoin('companies','companies.id','=','jobs.company')
                ->leftJoin('countries','companies.country','=','countries.id')
                ->leftJoin('job_status','job_status.id','=','jobs.status')
                ->leftJoin('taxonomy','taxonomy.id','=','jobs.taxonomy')
                ->leftJoin('invoices','invoices.job_id','=','jobs.id')
                ->where('jobs.is_invoiced', '=', '0')
                ->where('jobs.status', '!=', '9')
                ->where('companies.autosend_invoice', '=', '0')
                ->orderBy('jobs.due_date')
                ->groupBy('jobs.project_name');


        // $jobs =  $jobs->where('invoices.invoice_number',$operator,null);
        // $jobs =  $jobs->where('jobs.is_invoiced',$operator,1);

        if($from != '' && $to != '')
        {
            $from = Carbon::parse($from)->format('Y-m-d');
            $to = Carbon::parse($to)->format('Y-m-d');
            $from = date('Y-m-d',strtotime($from));
            $to = date('Y-m-d',strtotime($to));
            $jobs = $jobs->whereBetween('jobs.date_added', [$from, $to]);
        }

        if($user->role_id == 4)
        {
            $jobs = $jobs->where('jobs.vendor_id',$user->id);
            $jobs = $jobs->where('jobs.status','!=',9);
        }

        $jobs = $jobs->get();
        return $jobs;
    }

    public function downloadInvoiceFile($invoice_number,$job_id)
    {
        $filename = $invoice_number.'.xls';
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
        return "File does not exist";
        
        // $invoice = Invoice::where([
        //                                 ['invoice_number','=',$invoice_number],
        //                                 ['job_id','=',$job_id],
        //                         ])->get();

        // if(isset($invoice[0]))
        // {
        //     $filename = $invoice_number.'.xls';
        //     $file = Storage::disk('gcs')->get('invoices/'.$filename);
        //     if(Storage::disk('gcs')->exists('invoices/'.$filename)) {

        //         $file = Storage::disk('gcs')->get('invoices/'.$filename);
        //         $targetFullStoragePath = storage_path("app/public/storage/{$filename}");
        //         $targetRelativeStoragePath = 'public/storage/'.$filename;
        //         Storage::disk('local')->put($targetRelativeStoragePath, $file);

        //         if(Storage::disk('local')->exists($targetRelativeStoragePath)) {
        //             return response()->download($targetFullStoragePath, $filename);
        //         }

        //         return "File does not exist";
        //     } else {
        //         return 'Error' . $filename;
        //     }
        // }
    }

    

    public function showJobRollbackSearch(Request $request)
    {

        $search =  $request->input('search');
        $str = "project_name LIKE '%".$search."%'";
        if($request->input('company_id') != 0)
        {
           $str .= ' AND jobs.company = '.$request->input('company_id');
        }
      
        $job = Job::select(DB::raw('jobs.id,jobs.project_name,companies.name'))
                ->leftJoin('companies','companies.id','=','jobs.company')
                // ->whereRaw("project_name LIKE '%".$search."%'")
                ->whereRaw($str)
                ->orderBy('jobs.id','desc')
                ->get();
        echo $job->toJson();
    }

    public function exportJobFilter()
    {
        return view('layouts.modal_content.export_filter');
    }

}
