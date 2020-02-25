<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ClientsDepartment;
use App\Models\Company;
use App\Models\Job;
use App\Models\JobComment;
use App\Models\JobStatus;
use App\Models\TaxAuthority;
use App\Models\Taxonomy;
use App\Models\TaxonomyGroup;
use App\Models\Turnaround;
use App\Models\User;
use App\Models\UserRole;
use App\Models\JobsSourceFile;
use App\Models\Invoice;
use App\Models\Pricing;
use App\Models\AutomationConfig;
use App\Models\CancelledJob;
use App\Models\InvoiceRecipient;
use Mail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\InvoiceGenerator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;

use DB;
use Zipper;

class AddJobController extends FrontsiteController
{

    use InvoiceGenerator;

    private $DEFAULT_DATE = '1970-01-01';

    public function index()
    {
        $user = Auth::user();
        $taxonomyGroup = TaxonomyGroup::all();

        $turnaround = Turnaround::all();
        $taxAuthorities = TaxAuthority::all();

        $user->company_id;

        $companies = Company::orderBy('name', 'asc');

        if ($user->role_id != 8) {
            $companies = $companies->where('id', '=', $user->company_id);
        }
        return view('addjob')
            ->with('companies', $companies->get())
            ->with('turnaround', $turnaround)
            ->with('taxAuthorities', $taxAuthorities)
            ->with('taxonomy_group', $taxonomyGroup);
    }

    public function getTaxGroup($group)
    {
        $taxonomy = Taxonomy::where('group', '=', $group)->get();
        return $taxonomy->toJson();
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'company' => 'required|string|max:255',
            'project_name' => 'required|string|max:255',
            'taxonomy' => 'required|string|max:255',
            'page.*' => 'required|integer|min:1|max:255',
        ]);
    }

    public function store(Request $request, Job $targetJob, JobComment $targetComment)
    {

        // return $request['file_name'];
        // exit;

        //create job
        $this->DEFAULT_DATE = date_format(Carbon::now(), 'Y-m-d H:i:s');
        $this->validator($request->all())->validate();

        $job = [];
        $job['company'] = $this->getValueWithDefault($request, 'company', '');
        $job['turnaround'] = $this->getValueWithDefault($request, 'turnaround', '');
        $job['project_name'] = $request['project_name'];
        $job['purchase_order'] = $this->getValueWithDefault($request, 'purchase_order', '');
        $job['user_id'] = $request['user_id'];
        $job['companies_house_registration_no'] = $this->getValueWithDefault($request, 'registration_number', ''); //FIXME: what is registration_number in Job table?
        $job['taxonomy'] = $this->getValueWithDefault($request, 'taxonomy', '');
        $job['utr_number'] = $this->getValueWithDefault($request, 'utr_number', '');
        $job['tagging_level'] = $request['tagged'];
        $job['work_type'] = $request['statutory'];
        $job['entity_dormant'] = $request['dormant'];
        $job['year_end'] = Carbon::parse($this->getValueWithDefault($request, 'year_end', $this->DEFAULT_DATE))->format('Y-m-d');
        $job['date_of_director_report'] = Carbon::parse($this->getValueWithDefault($request, 'director_report_date', $this->DEFAULT_DATE))->format('Y-m-d');
        $job['date_of_auditor_report'] = Carbon::parse($this->getValueWithDefault($request, 'auditor_report_date', $this->DEFAULT_DATE))->format('Y-m-d');
        $job['approval_of_accounts_date'] = Carbon::parse($this->getValueWithDefault($request, 'approval_of_accounts_date', $this->DEFAULT_DATE))->format('Y-m-d');
        $job['name_of_director_approving_accounts'] = $this->getValueWithDefault($request, 'director_approving_account', '');
        $job['name_of_director_signing'] = $this->getValueWithDefault($request, 'director_signing_report', '');
        // $job['total_pages_submitted'] =$this->getValueWithDefault($request, 'page', '');
        $job['total_pages_submitted'] = array_sum($request->input('page'));
        $job['date_added'] = date('y-m-d');

        //Tax Authority
        $job['tax_authority_id'] = isset($request['tax_authority']) ? $request['tax_authority'] : 0;
        $job['tax_reference'] = isset($request['tax_reference']) && $request['tax_reference'] != '' ? $request['tax_reference'] : 0;

        //Date hacks
        $job['transaction_date'] = date('y-m-d');
        $job['last_reminder_sent_due_date'] = date('y-m-d');
        $job['last_reminder_sent_payment'] = date('y-m-d');

        // Hacks
        $job['order_by'] = $request['user_id'];
        //get the number of days from turnaround
        $daysTurnaround = Turnaround::where('id', $this->getValueWithDefault($request, 'turnaround', 2))->first();

        // $dueDate = Carbon::now()->addDays($daysTurnaround->number_of_days);
        $dueDate = Carbon::now()->addWeekDays($daysTurnaround->number_of_days);

        $job['due_date'] = date_format($dueDate, 'Y-m-d');
        $job['action'] = 1;
        $job['output'] = 1;
        $job['computed_price'] = $request['total_price'];
        $job['quoted_price'] = 0;
        $job['live_test_service'] = 0;
        $job['adjust_price'] = 0;
        $job['original_price'] = 0;
        $job['tax_computation_origianl_price'] = 0;
        $job['xbrl_file'] = 0;
        $job['tax_computation_price'] = $request['vat'];
        $job['tax_computation_converted'] = $request['tax_computation'];
        $job['status'] = 2; //?default status


        $config = AutomationConfig::where('key', 'new_order')->first();
        if ($config->is_active == 1) {
            $job['vendor_id'] = $config->default_vendor;
            $job['status'] = 3;
        }


        $company = Company::where('id', $job['company'])->first();
        $user = User::where('company_id', $company->id)->first();

        if (is_null($user)) {
            return redirect('add-jobs')->with('fail_message', 'Failed to add job. No' .
                ' user found associated with company: ' . $company->name);
        }

        $file = $request['file_name'];


        $department = ClientsDepartment::where('company_id', $request['company'])->get()->first();

        if ($department != null) {
            $job['department'] = $department['id'];
        } else {
            $job['department'] = 0;
        }

        $success = $targetJob->insert($job);

        if ($success) {
            $source_file_summary  = [];
            $addedJob = Job::orderBy('id', 'desc')->take(1)->first();

            if ($request->input('rolled_forward_job_id') != '') {
                $xbrl_source = JobsSourceFile::where('job_id', $request->input('rolled_forward_job_id'))->where('type', 3)->first();
                if (!empty($xbrl_source)) {
                    $xbrl_file = new JobsSourceFile();
                    $xbrl_file->file_name = $xbrl_source->file_name;
                    $xbrl_file->server_filename = $xbrl_source->server_filename;
                    $xbrl_file->page_count = $xbrl_source->page_count;
                    $xbrl_file->date_uploaded = $xbrl_source->date_uploaded;
                    $xbrl_file->uploaded_by = $xbrl_source->uploaded_by;
                    $xbrl_file->type = $xbrl_source->type;
                    $xbrl_file->is_removed = $xbrl_source->is_removed;
                    $xbrl_file->tax_computed = $xbrl_source->tax_computed;
                    $xbrl_file->job_id = $addedJob->id;
                    $xbrl_file->save();
                }
            }
            if ($request['file_name']) {
                foreach ($request['file_name'] as $k => $fname) {
                    // store $file to jobs source file associated with $addedJob->id
                    $jobSourceFile = new JobsSourceFile();
                    $jobSourceFile->job_id = $addedJob->id;
                    $jobSourceFile->file_name = $fname;
                    $jobSourceFile->server_filename = $file[$k];
                    $jobSourceFile->page_count = $request->input('page')[$k];
                    $jobSourceFile->date_uploaded = date('Y-m-d H:i:s');
                    $jobSourceFile->uploaded_by = $request['user_id'];
                    $jobSourceFile->type = 0;
                    $jobSourceFile->is_removed = 0;
                    $jobSourceFile->tax_computed = $request['row_type'][$k];
                    $jobSourceFile->save();

                    $source_file_summary['file_name'][] = $file[$k];
                    $source_file_summary['pages'][] = $request->input('page')[$k];
                }
            }

            if (!is_null($file) && !empty($file)) {

                try {

                    foreach ($file as $k => $e) {

                        $fileContent = File::get($request->file('file')[$k]);
                        $storage = Storage::disk('gcs');
                        $group = $this->getSourceFilePath($addedJob->id, 0);
                        $storage->put($group . $request->input('file_name')[$k], $fileContent);

                        //                 $storage = Storage::disk('gcs'); 
                        // //                $file = $this->handleFileNameSuffix($file, $storage);
                        //                 $storage->put($e, $request['file64'][$k]);
                    }
                } catch (\Exception $e) {
                    return redirect('add-jobs')->with('fail_message', 'failed to add job' . $e)->withInput();
                }
            }


            // if ($request['rolled_forward_job_id']) {

            //     $attachedFiles = JobsSourceFile::where('job_id', $request['rolled_forward_job_id'])->get();

            //     if (!is_null($attachedFiles) && !empty($attachedFiles)) {

            //         foreach($attachedFiles as $k => $attachedFile) {

            //             if ($request['file_name'][$k] && strcmp($request['file_name'][$k], $attachedFile->file_name) == 0) {
            //                 continue;
            //             }

            //             // store $attachedFile to jobs source file associated with $addedJob->id
            //             $jobSourceFile = new JobsSourceFile();
            //             $jobSourceFile->job_id = $addedJob->id;
            //             $jobSourceFile->file_name = $attachedFile->file_name;
            //             $jobSourceFile->server_filename = $attachedFile->server_filename;
            //             $jobSourceFile->page_count = $attachedFile->page_count;
            //             $jobSourceFile->date_uploaded = date('Y-m-d H:i:s');
            //             $jobSourceFile->uploaded_by = $request['user_id'];
            //             $jobSourceFile->type = 0;
            //             $jobSourceFile->is_removed = 0;
            //             $jobSourceFile->tax_computed = $request['row_type'][$k];
            //             $jobSourceFile->save();
            //         }
            //     }
            // }


            if ($request['comment']) {

                $comment = [];
                $comment['job_id'] = $addedJob['id'];
                $comment['comment'] = $request['comment'];
                $comment['tags'] = $request['tagged'];
                $comment['action'] = 'Job Created'; //FIXME: hack
                $comment['date_added'] = date('Y-m-d H:i:s');
                $targetComment->insert($comment);
                $company = Company::where('id', $addedJob->company)->get()->first();

                if (!is_null($company) && !empty($company)) {

                    $mail = [];
                    $mail['subject'] = $company->name . ' (Job ID: ' . $addedJob->id . ')';
                    $mail['comment'] = $comment['comment'];
                    $mail['to'] = explode(',', $user->email);
                    // $mail['to'] = 'info@1stopxbrl.com';
                    $mail['from'] = 'no-reply@1stopxbrl.com';
                    array_push($mail['to'], 'andrew.stewart@1stopxbrl.co.uk');


                    Mail::raw($mail['comment'], function ($message) use (&$mail) {
                        $message->subject($mail['subject']);
                        $message->from($mail['from'], '1STOPXBRL');
                        $message->to($mail['to']);
                        // $message->bcc('info@1stopxbrl.com');
                        // $message->bcc('andrew.stewart@1stopxbrl.co.uk');
                    });

                    $arr = [];
                    $arr['type'] = 'Job Comment';
                    $arr['date_sent'] = date('Y-m-d H:i:s');
                    $arr['email_recipient'] = $user->email;
                    $arr['email_cc'] = 'info@1stopxbrl.com';
                    $this->saveEmailHistory($arr);
                }
            }


            if ($job['company']) {
                $company = Company::where('id', $job['company'])->first();
                $autoInvoice = $company->autosend_invoice;

                if ($autoInvoice) {

                    $request = new \Illuminate\Http\Request();

                    $request->replace([
                        'job-generate-invoice' => json_encode(array($addedJob->id)),
                        'company-generate-invoice' => $company->id
                    ]);

                    $jsonResponse = $this->generateInvoice($request);

                    if (!isset($jsonResponse['error'])) {
                        $recipient = InvoiceRecipient::where('company_id', $job['company'])->first();
                        $bcc = [];

                        if (!empty($recipient)) {
                            $email = explode(',', $recipient->email);

                            array_push($email, 'info@1stopxbrl.com');
                            foreach ($email as $e) {
                                $bcc[] = $e;
                            }
                        }

                        $inv = explode('.', $jsonResponse['gcs_filename'])[0];
                        $pdf = $this->invoicedPdf($inv)->output();


                        $auth = Auth::user();
                        $targetFullStoragePath = storage_path("invoices/xls");
                        $emailFileName = $jsonResponse['gcs_filename'];
                        $invoiceFileName = $jsonResponse['filename'];
                        $invoiceFile = $targetFullStoragePath . '/' . $invoiceFileName;
                        $fileContents = file_get_contents($invoiceFile);

                        $mail = [];
                        $mail['subject'] = 'Invoice:' . basename($emailFileName, '.xls') . ', Job:' . $addedJob->id;
                        $mail['to'] = explode(',', $user->email);
                        // $mail['to'] = 'info@1stopxbrl.com';
                        // $mail['to'] = '1stopxbrl.dev@gmail.com';
                        $mail['from'] = 'no-reply@1stopxbrl.com';
                        $currentUser =  $auth->first_name . ' ' . $auth->last_name;


                        array_push($mail['to'], 'info@1stopxbrl.com');


                        // $mail['from'] = '1stopxbrl.dev@gmail.com';   
                        Mail::raw('1Stopxbrl Job notification.' . $currentUser . ' updated a job. Kindly see the attached invoice.', function ($message) use ($mail, $fileContents, $invoiceFileName,  $emailFileName, $bcc, $pdf, $inv) {
                            $message->subject($mail['subject']);
                            $message->from($mail['from'], '1Stopxbrl');
                            $message->to($mail['to']);
                            foreach ($bcc as $each) {
                                $message->bcc($each);
                            }
                            // $message->bcc('info@1stopxbrl.com');
                            $message->attachData($fileContents, $emailFileName);
                            $message->attachData($pdf, $inv . '.pdf');
                        });

                        $arr = [];
                        $arr['type'] = 'Invoice';
                        $arr['date_sent'] = date('Y-m-d H:i:s');
                        $arr['email_recipient'] = $company->email;
                        $arr['email_cc'] = 'info@1stopxbrl.com';
                        $this->saveEmailHistory($arr);

                        // File::delete($invoiceFile);
                    }
                }
                $data = [
                    'job' => $job,
                    'job_id' => $addedJob->id,
                    'source_files' => $source_file_summary
                ];

                $new_mail = [];
                $new_mail['subject'] = ' ServiceTrack 1stopxbrl New Job Request - Job ' . $addedJob->id;
                $new_mail['to'] = explode(',', $user->email);
                array_push($new_mail['to'], 'info@1stopxbrl.com');

                Mail::send('mail.new_job', $data, function ($m) use ($new_mail) {
                    $m->from('no-reply@1stopxbrl.becre8v.com', '1STOPXBRL');
                    $m->to($new_mail['to'])->subject($new_mail['subject']);
                    // $m->bcc('info@1stopxbrl.com');
                });



                if ($config->is_active == 1) {
                    $job['id'] = $addedJob->id;
                    // $vendor = User::where('id',$request->input('vendor'))->first();
                    $new_mail = [];
                    $new_mail['subject'] = 'Servicetrack 1stopXBRL Job Assigned - ' . $job['id'];
                    // $new_mail['to'] = $vendor->email;
                    $new_mail['to'] = ['vendor1stopxbrl@gmail.com'];
                    $data = ['job' => $job];
                    array_push($new_mail['to'], 'info@1stopxbrl.com');
                    $this->assignJobNotification($data, $new_mail);
                }


                $arr = [];
                $arr['type'] = 'New Job';
                $arr['date_sent'] = date('Y-m-d H:i:s');
                $arr['email_recipient'] = $user->email;
                $arr['email_cc'] = 'info@1stopxbrl.com';
                $this->saveEmailHistory($arr);
            }

            return redirect('/')->with('message', 'Successfully Added a Job!')->with('job_id', $addedJob->id);
            exit;
        }


        return redirect('add-jobs')->with('fail_message', 'failed to add job');
    }

    public function rollForward(Request $request)
    {

        $id = $this->getValueWithDefault($request, 'id', '');
        $rolledForwardJob = Job::where('id', $id)->first();
        $redirect = redirect('add-jobs');

        if ($rolledForwardJob != null) {

            $this->rollForwardYearEnd($rolledForwardJob);
            $attachedFiles = JobsSourceFile::where('job_id', $rolledForwardJob->id)->where('is_removed', 0)->orderBy('is_removed', 'asc')->orderBy('type', 'asc')->get();
            if (!is_null($attachedFiles) && !empty($attachedFiles)) {
                $redirect->with('rolledForwardJobFiles', $attachedFiles);
            }
        }

        return $redirect->with('rolledForwardJob', $rolledForwardJob);
    }

    function rollForwardYearEnd($rolledForwardJob)
    {
        if ($rolledForwardJob->year_end != null && $rolledForwardJob->year_end->timestamp > 0) {
            $yearEnd = $rolledForwardJob->year_end;
            $yearEnd->addYear();
            $rolledForwardJob->year_end = $yearEnd;
        }
    }

    function rollProject(Request $request)
    {
        $jobs = DB::table('jobs')
            ->select(DB::raw('purchase_order,companies_house_registration_no,DATE_FORMAT(DATE_ADD(year_end,INTERVAL 1 YEAR),"%Y-%m-%d") as year_end'))
            ->where('project_name', $request->projectname)->orderByRaw('DATE(year_end) desc')->limit(1)->get();

        if (count($jobs) > 0) {
            echo json_encode($jobs[0]);
        }
    }

    function getValueWithDefault(Request $request, $key, $default)
    {
        if ($request[$key]) {
            return $request[$key];
        }

        return $default;
    }

    function handleFileNameSuffix($file, $storage)
    {

        $actual_name = pathinfo($file, PATHINFO_FILENAME);
        $original_name = $actual_name;
        $extension = pathinfo($file, PATHINFO_EXTENSION);

        $i = 1;

        while ($storage->exists($actual_name . "." . $extension)) {
            $actual_name = (string) $original_name . ' ' . $i;
            $file = $actual_name . "." . $extension;
            $i++;
        }

        return $file;
    }

    public function getProjectName()
    {

        $project = Input::get('term');

        $jobs = Job::select('project_name')->groupBy('project_name');
        $whereArr = array(['project_name', 'LIKE', '%' . $project . '%']);
        if (request('company') != '') {
            $whereArr[] = ['company', '=', request('company')];
        }

        $jobs = $jobs->where($whereArr)->get();
        $arr = [];
        foreach ($jobs as $each) {
            $arr[] = $each['project_name'];
        }
        echo json_encode($arr);
    }

    public function getPricing($page, $company, $turnaround, $work_type, $taxonomy)
    {

        $excess_page  = 0;
        if ($page > 50) {
            $excess_page = $page - 50;
            $page = 50;
        }
        $taxonomy = str_replace('~', '/', $taxonomy);

        $price = DB::table('companies AS cp')
            // ->select(DB::raw('cp.id,cp.name,
            //             pg.price,(pg.price * (cp.discount_rate * 0.01 )) discount,
            //             (pg.price - (pg.price * (cp.discount_rate * 0.01 ))) total_price,
            //             pgi.name pricing_info')
            //          )
            ->select(DB::raw('IF(cp.adjustment_type = 0,(pg.price - (pg.price * (cp.discount_rate * 0.01 ))),(pg.price + (pg.price * (cp.discount_rate * 0.01 )))) total_price,pgi.id pricing_info_id'))
            ->leftJoin('pricing_grid_info AS pgi', 'pgi.id', '=', 'cp.pricing_grid')
            ->leftJoin('pricing_grid AS pg', 'pg.pricing_info_id', '=', 'pgi.id')
            ->leftJoin('taxonomy as tm', 'tm.id', '=', 'pg.taxonomy_group')
            ->whereRaw('cp.id = ' . $company . '
                                    AND
                                        (' . $page . ' BETWEEN pg.floor_page_count AND pg.ceiling_page_count)
                                    AND
                                        pg.turnaround_time = ' . $turnaround . '
                                    AND
                                        tm.name = "' . $taxonomy . '"
                                    AND
                                        pg.work_type = ' . "(SELECT id FROM work_types WHERE name = '" . $work_type . "')")
            ->get();
        // $query = DB::table('companies AS cp')
        //             // ->select(DB::raw('cp.id,cp.name,
        //             //             pg.price,(pg.price * (cp.discount_rate * 0.01 )) discount,
        //             //             (pg.price - (pg.price * (cp.discount_rate * 0.01 ))) total_price,
        //             //             pgi.name pricing_info')
        //             //          )
        //               ->select(DB::raw('(pg.price - (pg.price * (cp.discount_rate * 0.01 ))) total_price,pgi.id pricing_info_id'))
        //             ->leftJoin('pricing_grid_info AS pgi','pgi.id','=','cp.pricing_grid')
        //             ->leftJoin('pricing_grid AS pg','pg.pricing_info_id','=','pgi.id')
        //             ->leftJoin('taxonomy as tm','tm.id','=','pg.taxonomy_group')
        //             ->whereRaw('cp.id = '.$company.'
        //                             AND
        //                                 ('.$page.' BETWEEN pg.floor_page_count AND pg.ceiling_page_count)
        //                             AND
        //                                 pg.turnaround_time = '.$turnaround.'
        //                             AND
        //                                 tm.name = "'.$taxonomy.'"
        //                             AND
        //                                 pg.work_type = '."(SELECT id FROM work_types WHERE name = '".$work_type."')")
        //             ->toSql();

        $arr = [];
        if (!empty($price) && isset($price[0])) {
            $price = $price[0];
            $add = 0;

            $additional = DB::table('pricing_grid_config')->select('*')->where('pricing_id', $price->pricing_info_id)->get();
            if (!empty($additional) && isset($additional[0])) {
                $add += $excess_page * $additional[0]->price;
            }
        }
        $arr['total_price'] = $price->total_price + $add;

        
        // $arr['query'] = $query;
        echo json_encode($arr, true);
    }

    public function companyCountry($company)
    {
        $company = DB::table('companies')
            ->select(DB::raw('country'))
            ->where('id', "$company")
            ->get();
        return $company;
        exit;
    }

    public function editJobs($jobId)
    {
        $job = Job::where('jobs.id', '=', $jobId)
            ->select(DB::raw('jobs.*,CONCAT(users.first_name," ",users.last_name) user_order,turnaround.name turnaround_name,turnaround.id turnaround_id,taxonomy.group,job_comments.comment,job_comments.tags,companies.name company_name, companies.id company_id'))
            ->leftJoin('users', 'jobs.user_id', '=', 'users.id')
            ->leftJoin('companies', 'companies.id', '=', 'jobs.company')
            ->leftJoin('turnaround', 'turnaround.id', '=', 'jobs.turnaround')
            ->leftJoin('taxonomy', 'taxonomy.id', '=', 'jobs.taxonomy')
            ->leftJoin('job_comments', 'job_comments.job_id', '=', 'jobs.id')
            ->get();
        $taxonomyGroup = TaxonomyGroup::all();
        // $taxAuthorities = TaxAuthority::all();
        if (isset($job[0])) {
            $job = $job[0];
            $job_source = JobsSourceFile::where('job_id', '=', $job->id)->where('is_removed', 0)->orderBy('type', 'asc')->orderBy('tax_computed', 'asc')->get();
            $job_comment = JobComment::where('job_id', '=', $job->id)->get();

            return view('edit_job')
                ->with('jobs', $job)
                ->with('job_source', $job_source)
                ->with('job_comment', $job_comment)
                ->with('taxonomy_group', $taxonomyGroup);
        }
    }


    public function updateJobStatus(Request $request)
    {

        $status = JobStatus::where('name', $request->input('action'))->first();
        $set = ['status' => $status->id];

        if ($request->input('vendor') != 0) {
            $set['vendor_id'] = $request->input('vendor');
            $job = Job::where('id', $request->input('job_id'))->first()->toArray();
            $vendor = User::where('id', $request->input('vendor'))->first();
            $new_mail = [];
            $new_mail['subject'] = 'Servicetrack 1stopXBRL Job Assigned - ' . $job['id'];
            $new_mail['to'] = $vendor->email;
            $new_mail['to'] = ['vendor1stopxbrl@gmail.com'];
            $data = ['job' => $job];
            array_push($new_mail['to'], 'info@1stopxbrl.com');
            $this->assignJobNotification($data, $new_mail);
        }

        if ($request->input('action') == 'Pending Sign Off') {
            $job = Job::where('id', $request->input('job_id'))->first()->toArray();
            $company = Company::where('id', $job['company'])->first();
            $user = User::where('company_id', $company->id)->first();
            $link = env('APP_URL') . '/edit-job/' . $job['id'];
            $new_mail = [];
            $new_mail['subject'] = 'Servicetrack 1stopXBRL Job - ' . $job['id'] . ' - Pending Sign Off';
            $new_mail['to'] = explode(',', $user->email);
            array_push($new_mail['to'], 'info@1stopxbrl.com');
            $data = ['job' => $job, 'link' => $link, 'company_name' => $company->name];
            $this->completeJobNotification($data, $new_mail);
        }

        // if($request->input('action') == 'Sign Off (Job Completed)')
        // {
        //     $job = Job::where('id',$request->input('job_id'))->first()->toArray();
        //     $company = Company::where('id',$job['company'])->first();
        //     $user = User::where('company_id', $company->id)->first();
        //     $link = env('APP_URL').'/edit-job/'.$job['id'];
        //     $new_mail = [];
        //     $new_mail['subject'] = 'Servicetrack 1stopXBRL Job - '.$job['id'].' - Pending Sign-off';
        //     $new_mail['to'] = explode(',',$user->email);
        //     array_push($new_mail['to'],'info@1stopxbrl.com');
        //     $data = ['job' => $job,'link' => $link,'company_name' => $company->name];
        //     $this->completeJobNotification($data,$new_mail);

        // }
        DB::table('jobs')->where('id', '=', $request->input('job_id'))->update($set);
    }

    public function getVendors()
    {
        $vendor = DB::table('users AS u')
            ->selectRaw('u.id,CONCAT(u.first_name," ",u.last_name) vendor_name')
            ->leftJoin('user_roles AS ur', 'ur.id', 'u.role_id')
            ->whereRaw('LOWER(ur.name) = "vendor"')
            ->orderByRaw('u.first_name ASC,u.last_name ASC')
            ->where('u.status', '=', 1)
            ->get();
        echo $vendor->toJson();
    }

    public function removeSourceFile(Request $request)
    {
        DB::table('jobs_source_files')->where('id', '=', $request->input('file_id'))->update(['is_removed' => 1]);
    }

    public function updateJob(Request $request)
    {

        $data = [
            'company' => $request->input('company'),
            'project_name' => $request->input('project_name'),
            'purchase_order' => $request->input('purchase_order'),
            'work_type' => $request->input('statutory'),
            'turnaround' => $request->input('turnaround'),
            'computed_price' => $request->input('total_price'),
            'tax_computation_price' => $request->input('vat'),
            'total_pages_submitted' =>  array_sum($request->input('page')),
            'companies_house_registration_no' => $request->input('registration_number'),
            'taxonomy' => $request->input('taxonomy'),
            'tagging_level' => $request->input('tagged'),
            'entity_dormant' => $request->input('dormant'),
            'year_end' => Carbon::parse($request->input('year_end'))->format('Y-m-d'),
            'date_of_director_report' => Carbon::parse($request->input('director_report_date'))->format('Y-m-d'),
            'date_of_auditor_report' => Carbon::parse($request->input('auditor_report_date'))->format('Y-m-d'),
            'approval_of_accounts_date' => Carbon::parse($request->input('account_approval_date'))->format('Y-m-d'),
            'name_of_director_approving_accounts' => $request->input('director_approving_account'),
            'name_of_director_signing' => $request->input('director_signing_report'),
            'utr_number' => $request->input('utr_number'),
            'tax_computation_converted' => $request->input('tax_computation'),

        ];



        DB::table('jobs')->where('id', '=', $request->input('job_id'))->update($data);

        if ($request->input('comment') != '') {
            $receiver = $request->input('send_to');
            $send_to_email = 'info@1stopxbrl.com';
            $tag = 2;

            if ($receiver != 'client') {
                $job = Job::select('vendor_id')->where('id', '=', $request->input('job_id'))->first();

                $tag = 1;
            }

            if (!empty($send_to_email)) {
                $this->sendJobComment($send_to_email, $request->input('company'), $request->input('job_id'), $request->input('project_name'), $request->input('comment'), $tag);
            }
        }


        if (count($request->input('file_source')) > 0) {
            foreach ($request->input('file_source') as $i => $fs) {
                if ($request->input('file64')[$i] != '') {
                    $fileContent = File::get($request->file('file')[$i]);
                    $storage = Storage::disk('gcs');
                    $storage->put($request->input('file_name')[$i], $fileContent);
                }

                if ($fs != 0) {
                    if ($request['file_name'][$i] != '') {

                        $arr = [
                            'file_name' => $request->input('file_name')[$i],
                            'server_filename' => $request->input('file_name')[$i],
                            'page_count' => $request->input('page')[$i],

                        ];
                        DB::table('jobs_source_files')->where('id', '=', $fs)->update($arr);
                    }
                } else {
                    $jobSourceFile = new JobsSourceFile();
                    $jobSourceFile->job_id = $request->input('job_id');
                    $jobSourceFile->file_name = $request->input('file_name')[$i];
                    $jobSourceFile->server_filename = $request->input('file_name')[$i];
                    $jobSourceFile->page_count = $request->input('page')[$i];
                    $jobSourceFile->date_uploaded = date('Y-m-d H:i:s');
                    $jobSourceFile->uploaded_by = Auth::id();
                    $jobSourceFile->type = 0;
                    $jobSourceFile->is_removed = 0;
                    $jobSourceFile->tax_computed = $request->input('row_type')[$i];
                    $jobSourceFile->save();
                }
            }
        }
        if ($request->input('company')) {
            $company = Company::select('*')->where('id', '=', $request->input('company'))->first();
            $user = User::select('*')->where('company_id', '=', $request->input('company'))->first();

            $new_mail = [];
            $new_mail['subject'] = 'Servicetrack 1stopXBRL Job Assigned - ' . $request->input('job_id');
            // $new_mail['to'] = $vendor->email;
            $new_mail['to'] = ['vendor1stopxbrl@gmail.com'];
            $data = ['job' => $request->input('job_id')];
            array_push($new_mail['to'], 'info@1stopxbrl.com');
            $this->assignJobNotification($data, $new_mail);
        }

        if ($request->input('company')) {
            $company = Company::select('*')->where('id', '=', $request->input('company'))->first();
            $autoInvoice = $company->autosend_invoice;
            if ($autoInvoice) {
                $invoice = Invoice::select('*')->where('job_id', '=', $request->input('job_id'))->orderBy('date_imported', 'DESC')->first();

                $request->replace([
                    'job-generate-invoice' => json_encode(array($request->input('job_id'))),
                    'company-generate-invoice' => $company->id,
                    'invoice_number' => $invoice->invoice_number,
                ]);
                $jsonResponse = $this->generateInvoice($request);
            }
        }





        // return redirect()->back()->with('success', 'Update Successful.');
    }


    function overWriteInvoices(Request $request)
    {
        $job = DB::table('jobs as j')
            ->select(DB::raw('j.id,j.company,i.id invoice_id,i.invoice_number'))
            ->rightJoin('invoices as i', 'i.job_id', 'j.id')
            ->whereRaw("DATE(date_added) >= '2019-07-01' AND DATE(date_added) <= '2019-07-31'")
            // ->whereRaw('j.id = 6199')
            ->get();
        // echo '<pre>';
        foreach ($job as $i => $j) {
            $this->generateOverwriteInvoice($j->invoice_id, $j->id, $j->invoice_number, $j->company);
            // echo $i;
        }
    }


    function editJobStatus(Request $request, JobComment $targetComment)
    {

        $user = Auth::user();
        $jobs = Job::where('id', '=', $request->input('job_id'))->first();
        $company = Company::where('id', '=', $jobs->company)->first();
        $vendor = User::where('id', '=', $jobs->vendor_id)->first();

        DB::table('jobs')->where('id', '=', $request->input('job_id'))->update(['status' => $request->input('status')]);

        if ($request->input('comment')) {
            $comment = [];
            $comment['job_id'] = $request->input('job_id');
            $comment['comment'] = $request->input('comment');
            $comment['tags'] = 0;
            $comment['action'] = $request->input('status') == 9 ? 'Cancelled Job' : 'Complete Sign-off';
            $comment['date_added'] = date('Y-m-d H:i:s');
            $targetComment->insert($comment);
        }



        if ($request->input('status') == 9) {
            $cancel = new CancelledJob;
            $cancel->job_id = $request->input('job_id');
            $cancel->reason = $request->input('comment');
            $cancel->cancelled_by = $user->id;
            $cancel->date_cancelled = date('Y-m-d');
            $cancel->save();

            $data = ['job' => $jobs->toArray(), 'company_name' => $company->name, 'reason' => $request->input('comment')];
            $new_mail = [];
            $new_mail['subject'] = 'ServiceTrack 1stopXBRL Job ' . $request->input('job_id') . ' Cancelled';
            $new_mail['to'] = [];
            // $new_mail['bcc'] = explode(',',$company->email);
            // $new_mail['bcc'] = $company->email;

            array_push($new_mail['to'], 'info@1stopxbrl.com');
            if (!empty($vendor)) {
                array_push($new_mail['to'], 'vendor1stopxbrl@gmail.com');
                // $new_mail['to'] = $vendor->email;
                // $new_mail['bcc'] = 'info@1stopxbrl.com';
            }

            Mail::send('mail.cancel_job', $data, function ($m) use ($new_mail) {
                $m->from('no-reply@1stopxbrl.becre8v.com', '1STOPXBRL');
                // $m->to('info@1stopxbrl.com')->subject($new_mail['subject']);
                $m->to($new_mail['to'])->subject($new_mail['subject']);
            });
            $arr = [];
            $arr['type'] = 'Job Cancelled';
            $arr['date_sent'] = date('Y-m-d H:i:s');
            $arr['email_recipient'] = 'vendor1stopxbrl@gmail.com';
            $arr['email_cc'] = 'info@1stopxbrl.com';
            $this->saveEmailHistory($arr);

            $client_mail = [];
            $client_mail['subject'] = 'ServiceTrack 1stopXBRL Job ' . $request->input('job_id') . ' Cancelled';
            $client_mail['to'] = explode(',', $company->email);
            // $new_mail['bcc'] = $company->email;
            array_push($client_mail['to'], 'info@1stopxbrl.com');
            Mail::send('mail.cancel_job', $data, function ($m) use ($client_mail) {
                $m->from('no-reply@1stopxbrl.becre8v.com', '1STOPXBRL');
                // $m->to('info@1stopxbrl.com')->subject($new_mail['subject']);
                $m->to($client_mail['to'])->subject($client_mail['subject']);
            });
            $arr = [];
            $arr['type'] = 'Job Cancelled';
            $arr['date_sent'] = date('Y-m-d H:i:s');
            $arr['email_recipient'] = $user->email;
            $arr['email_cc'] = 'info@1stopxbrl.com';
            $this->saveEmailHistory($arr);
        }
    }

    public function downloadSourceFile($job_id, $source_type, $file_name)
    {
        $group = $this->getSourceFilePath($job_id, $source_type);
        $filename = $file_name;
        $exists = Storage::disk('gcs')->has($group . $filename);
        if ($exists) {
            $content = Storage::disk('gcs')->get($group . $filename);
            return $this->respondDownload($content, $filename);
        } else {
            return redirect()->back()->with('danger', 'File not found.');
        }
    }


    public function respondDownload($file, $filename)
    {

        $targetFullStoragePath = storage_path("app/public/storage/{$filename}");
        $targetRelativeStoragePath = 'public/storage/' . $filename;
        Storage::disk('local')->put($targetRelativeStoragePath, $file);

        if (Storage::disk('local')->exists($targetRelativeStoragePath)) {
            return response()->download($targetFullStoragePath, $filename)->deleteFileAfterSend(true);
        }
    }

    function quickUpload(Request $request)
    {
        $source_file = JobsSourceFile::where('id', $request->input('source_id'))->first();
        $file = $request->input('file_name');
        $job_source_files = '';
        $file_id = 0;
        if (!is_null($source_file)) {
            $update = array();
            // $update['page_count'] = $request->input('pages');
            $update['tax_computed'] = $request->input('tax_computation');
            $update['type'] = $request->input('file_type');
            $update['date_uploaded'] = date('Y-m-d H:i:s');
            if ($request->input('file_name') != '') {
                $update['file_name'] = $request->input('file_name');
                $update['server_filename'] = $request->input('file_name');
                $job_source_files = $request->input('file_name');
            } else {
                $file = $source_file->file_name;
                $job_source_files = $file;
            }
            $file_id = $request->input('source_id');
            DB::table('jobs_source_files')->where('id', $request->input('source_id'))->update($update);
        } else {
            if ($request->input('file_type') == 0 || Auth::user()->role_id == 4) {
                DB::table('jobs_source_files')
                    ->where('job_id', $request->input('job_id'))
                    ->where('type', $request->input('file_type'))
                    ->where('tax_computed', $request->input('tax_computation'))
                    ->update(['is_removed' => '1']);
            }

            $jobSourceFile = new JobsSourceFile();
            $jobSourceFile->job_id = $request->input('job_id');
            $jobSourceFile->file_name = $request->input('file_name');
            $jobSourceFile->server_filename = $request->input('file_name');
            $jobSourceFile->page_count = $request->input('pages');
            $jobSourceFile->date_uploaded = date('Y-m-d H:i:s');
            $jobSourceFile->uploaded_by = Auth::id();
            $jobSourceFile->type = $request->input('file_type');
            $jobSourceFile->is_removed = 0;
            $jobSourceFile->tax_computed = $request->input('tax_computation');
            $jobSourceFile->save();
            $job_source_files = $request->input('file_name');
            $file_id = $jobSourceFile->id;;
        }

        if ($request->input('file_type') == 4) {
            $update = array();
            $update['status'] = 4;
            DB::table('jobs')->where('id', $request->input('job_id'))->update($update);
        }


        if ($request->input('file_content') != '') {
            $fileContent = File::get($request->file('uploaded_file'));
            $storage = Storage::disk('gcs');

            $group = $this->getSourceFilePath($request->input('job_id'), $request->input('file_type'));

            $storage->put($group . $request->input('file_name'), $fileContent);
        }

        $user = Auth::user();
        $data = [];
        $job = Job::where('id', $request->input('job_id'))->first()->toArray();
        $company = Company::where('id', $job['company'])->first();
        $data['job'] = $job;
        $data['job_source'] = $job_source_files;

        $new_mail = [];
        $new_mail['to'] = ['info@1stopxbrl.com'];
        // $new_mail['to'] = explode(',',$company->email);

        if ($job['vendor_id'] != 0) {
            array_push($new_mail['to'], 'vendor1stopxbrl@gmail.com');
        }

        if ($user->role_id == 4) {
            $status = $this->autoConfig('submitted_by_vend');
            if ($status->is_active == 1) {
                $update = array();
                $update['status'] = 6;
                DB::table('jobs')->where('id', $request->input('job_id'))->update($update);
            }


            $new_mail['subject'] = ' Servicetrack 1stopXBRL File Upload in job ' . $job['id'];
            Mail::send('mail.file_upload', $data, function ($m) use ($new_mail) {
                // $m->from('no-reply@1stopxbrl.com', '1STOPXBRL');
                $m->to('info@1stopxbrl.com')->subject($new_mail['subject']);
                // $m->to($new_mail['to'], $user->email)->subject($new_mail['subject']);
                // $m->bcc('info@1stopxbrl.com');
            });

            $arr = [];
            $arr['type'] = 'File Uploaded';
            $arr['date_sent'] = date('Y-m-d H:i:s');
            // $arr['email_recipient'] = $company->email;
            $arr['email_recipient'] = 'info@1stopxbrl.com';
            $arr['email_cc'] = '';
            $this->saveEmailHistory($arr);
        } else {
            $data['file_id'] = $file_id;
            $new_mail['subject'] = 'Servicetrack 1stopXBRL - A new file was uploaded in job' . $job['id'];
            Mail::send('mail.new_file', $data, function ($m) use ($new_mail) {
                $m->from('no-reply@1stopxbrl.com', '1STOPXBRL');
                // $m->to('info@1stopxbrl.com')->subject($new_mail['subject']);
                $m->to($new_mail['to'])->subject($new_mail['subject']);
                // $m->bcc('info@1stopxbrl.com');
            });
            $arr = [];
            $arr['type'] = 'File Uploaded';
            $arr['date_sent'] = date('Y-m-d H:i:s');
            $arr['email_recipient'] = $company->email;
            $arr['email_cc'] = 'info@1stopxbrl.com';
            $this->saveEmailHistory($arr);
        }

        if ($request->input('file_type') == 0) {
            $result = array();
            $result['page'] = $request->input('pages');
            $result['file_name'] = $file;
            echo json_encode($result);
        }
    }
    function vendorQuickUpload(Request $request)
    {

        if (count($request->input('tax_category')) > 0) {
            $job_source_files = '';
            $file_id = '';
            $output_source = array();
            foreach ($request->input('tax_category') as $k => $category) {
                DB::table('jobs_source_files')
                    ->where('job_id', $request->input('job_id'))
                    ->where('type', $request->input('file_type'))
                    ->where('tax_computed', $category)
                    ->update(['is_removed' => '1']);


                $jobSourceFile = new JobsSourceFile();
                $jobSourceFile->job_id = $request->input('job_id');
                $jobSourceFile->file_name = $request->input('file_name')[$k];
                $jobSourceFile->server_filename = $request->input('file_name')[$k];
                $jobSourceFile->page_count = 0;
                $jobSourceFile->date_uploaded = date('Y-m-d H:i:s');
                $jobSourceFile->uploaded_by = Auth::id();
                $jobSourceFile->type = $request->input('file_type');
                $jobSourceFile->is_removed = 0;
                $jobSourceFile->tax_computed = $category;
                $jobSourceFile->save();
                $job_source_files .= $request->input('file_name')[$k] . ',';
                $file_id .= '#' . $jobSourceFile->id . ',';
                $output_source[] = $request->input('file_name')[$k];
                if ($request->input('file_content')[$k] != '') {
                    $fileContent = File::get($request->file('uploaded_file')[$k]);
                    $storage = Storage::disk('gcs');

                    $group = $this->getSourceFilePath($request->input('job_id'), $request->input('file_type'));

                    $storage->put($group . $request->input('file_name')[$k], $fileContent);
                }
            }

            $user = Auth::user();
            $update = array();
            $stats = 6;
            if ($request->input('file_type') == 4) {
                $stats = 4;
            }
            $update['status'] = $stats;
            DB::table('jobs')->where('id', $request->input('job_id'))->update($update);
            $data = [];
            $job = Job::where('id', $request->input('job_id'))->first()->toArray();
            $company = Company::where('id', $job['company'])->first();
            $user = User::where('id', $job["user_id"])->first();
            $data['job'] = $job;

            if ($request->input('file_type') == 3) {

                $data['job_source']  = $output_source;
                $new_mail['subject'] = 'Servicetrack 1stopXBRL - File upload in job #' . $job['id'];

                //Old Email
                $new_mail['to'] = 'info@1stopxbrl.com';
                //    $new_mail['to'] = explode(',',$user->email);
                //     Mail::send('mail.output_upload', $data, function ($m) use ($new_mail) {
                //     $m->from('no-reply@1stopxbrl.com', '1STOPXBRL');
                //     $m->to($new_mail['to'])->subject($new_mail['subject']);
                //     // $m->bcc('info@1stopxbrl.com');
                //     });
                Mail::send('mail.output_upload', $data, function ($m) use ($new_mail) {
                    $m->from('no-reply@1stopxbrl.becre8v.com', '1STOPXBRL');
                    $m->to($new_mail['to'])->subject($new_mail['subject']);
                    // $m->to($user->email)->subject($new_mail['subject']);
                    // $m->bcc('info@1stopxbrl.com');
                });


                $job = Job::where('id', $request->input('job_id'))->first()->toArray();
                $company = Company::where('id', $job['company'])->first();
                $user = User::where('company_id', $company->id)->first();
                $link = env('APP_URL') . '/edit-job/' . $job['id'];
                $new_mail = [];
                $new_mail['subject'] = 'Servicetrack 1stopXBRL Job - ' . $job['id'] . ' - Pending Sign-off';
                $new_mail['to'] = explode(',', $user->email);
                array_push($new_mail['to'], 'info@1stopxbrl.com');
                $data = ['job' => $job, 'link' => $link, 'company_name' => $company->name];
                $this->completeJobNotification($data, $new_mail);



                $arr = [];
                $arr['type'] = 'File Uploaded';
                $arr['date_sent'] = date('Y-m-d H:i:s');
                // $arr['email_recipient'] = $company->email;
                $arr['email_recipient'] = 'info@1stopxbrl.com';
                $arr['email_cc'] = 'info@1stopxbrl.com';
                $this->saveEmailHistory($arr);
            } else {


                $data['job_source'] = rtrim($job_source_files, ',');
                $data['file_id'] = rtrim($file_id, ',');
                $new_mail['subject'] = 'Servicetrack 1stopXBRL - A new file was uploaded in job ' . $job['id'];
                $new_mail['to'] = explode(',', $user->email);
                $new_mail['to'] = 'info@1stopxbrl.com';

                Mail::send('mail.new_file', $data, function ($m) use ($new_mail) {
                    $m->from('no-reply@1stopxbrl.becre8v.com', '1STOPXBRL');
                    $m->to($user->email)->subject($new_mail['subject']);
                    // $m->bcc('info@1stopxbrl.com');
                });

                // $new_mail['to'] = explode(',',$user->email);
                // Mail::send('mail.new_file', $data, function ($m) use ($new_mail) {
                //     $m->from('no-reply@1stopxbrl.becre8v.com', '1STOPXBRL');
                //     $m->to('info@1stopxbrl.com')->subject($new_mail['subject']);
                //     $m->to($new_mail['to'])->subject($new_mail['subject']);
                //     // $m->to($user->email)->subject($new_mail['subject']);
                //     $m->bcc('info@1stopxbrl.com');
                // });
                $arr = [];
                $arr['type'] = 'File Uploaded';
                $arr['date_sent'] = date('Y-m-d H:i:s');
                $arr['email_recipient'] = $user->email;
                $arr['email_cc'] = 'info@1stopxbrl.com';
                $this->saveEmailHistory($arr);
            }
        }
    }

    function adminQuickUpload(Request $request)
    {

        ini_set('post_max_size', '64M');
        ini_set('upload_max_filesize', '64M');

        $file = $request->input('file_name');
        $test = $request->input('file_name')[0];
        // $info = new SplFileInfo($test);
        $filename = pathinfo($test, PATHINFO_FILENAME);
        $ext = pathinfo($test, PATHINFO_EXTENSION);
        if ($ext == 'doc') {
            return response('Enter-Validate');
        } else {

            if (count($file) > 0) {
                $job_source_files = '';
                $file_id = '';
                foreach ($file as $k => $each) {
                    if ($request->input('file_type') == 0) {
                        DB::table('jobs_source_files')
                            ->where('job_id', $request->input('job_id'))
                            ->where('type', $request->input('file_type'))
                            ->where('tax_computed', $request->input('tax_category')[0])
                            ->update(['is_removed' => '1']);
                    }
                    $source = new JobsSourceFile;
                    $source->job_id = $request->input('job_id');
                    $source->file_name = $request->input('file_name')[0];
                    $source->server_filename = $request->input('file_name')[0];
                    $source->page_count = $request->input('page')[0] != '' ? $request->input('page')[0] : '0';
                    $source->date_uploaded = date('Y-m-d H:i:s');
                    $source->uploaded_by = Auth::id();
                    $source->type = $request->input('file_type');
                    $source->tax_computed = $request->input('tax_category')[0];
                    $source->is_removed = 0;
                    $source->save();
                    $job_source_files .= $request->input('file_name')[0] . ',';
                    $file_id .= '#' . $source->id . ',';

                    if ($request->input('file_content')[0] != '') {
                        $fileContent = File::get($request->file('uploaded_file')[0]);
                        $storage = Storage::disk('gcs');

                        $group = $this->getSourceFilePath($request->input('job_id'), $request->input('file_type'));

                        $storage->put($group . $each, $fileContent);
                    }
                }
                $data = [];
                $new_mail = [];
                $job = Job::where('id', $request->input('job_id'))->first()->toArray();

                $company = Company::where('id', $job['company'])->first();
                $data['job'] = $job;

                $data['job_source'] = rtrim($job_source_files, ',');
                $data['file_id'] = rtrim($file_id, ',');
                exit;
                $new_mail['subject'] = 'Servicetrack 1stopXBRL - A new file was uploaded in job' . $job['id'];
                $new_mail['to'] = explode(',', $user->email);


                Mail::send('mail.new_file', $data, function ($m) use ($new_mail) {
                    $m->from('no-reply@1stopxbrl.com', '1STOPXBRL');
                    // $m->to('info@1stopxbrl.com')->subject($new_mail['subject']);
                    $m->to($new_mail['to'])->subject($new_mail['subject']);
                    $m->bcc('info@1stopxbrl.com');
                });

                $arr = [];
                $arr['type'] = 'File Uploaded';
                $arr['date_sent'] = date('Y-m-d H:i:s');
                $arr['email_recipient'] = $company->email;
                $arr['email_cc'] = 'info@1stopxbrl.com';
                $this->saveEmailHistory($arr);
            }
        }
    }


    function getSourceFilePath($job_id, $source_type)
    {
        $path = md5($job_id) . '/' . md5($source_type) . '/';
        return $path;
    }

    public function showCompanyUsers($company_id)
    {
        $users = DB::table('users')
            ->select('users.*', 'user_roles.name as role')
            ->leftJoin('user_roles', 'users.role_id', '=', 'user_roles.id');


        $users = $users->where('company_id', '=', $company_id);
        $users = $users->orderBy('users.first_name', 'ASC')->get();

        echo json_encode($users);
    }

    function sampleWord()
    {
        // $name = basename(__FILE__, '.php');
        $source =  "^12 April 2016_Grafton GP 2015.docx";
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($source);
        $htmlWriter = new \PhpOffice\PhpWord\Writer\HTML($phpWord);

        $htmlWriter->save('test1doc.html');
    }

    function autoConfig($key = 'submitted_by_vend')
    {
        $auto = AutomationConfig::where('key', $key)->orderBy('action_status', 'asc')->first();
        return $auto;
    }

    function getConvertedFile($job_id, $tax_computation)
    {
        echo 123;
    }

    function getJobOutputFiles($job_id)
    {
        $source = JobsSourceFile::where('job_id', $job_id)->where('type', 3)->where('is_removed', 0)->orderBy('tax_computed', 'asc')->get()->toJson();
        echo $source;
    }

    public function readXbrlFile($job_id, $source_type, $file, $action)
    {
        $template = Storage::disk('public')->get('template/xbrl_template.html');
        $xbrl_script = $template;

        $path = $this->getSourceFilePath($job_id, $source_type);
        $group = storage_path('app/public/source_files/' . $path);
        Zipper::make($group . $file)->extractTo($group . '/xbrl/' . md5($job_id) . '/');
        $folders = Storage::disk('gcs')->files($path . '/xbrl/' . md5($job_id) . '/');
        $file_path = '';
        $file_name = '';
        if (!empty($folders)) {
            foreach ($folders as $each) {
                $pathinfo = pathinfo($each);
                if ($pathinfo['extension'] == 'html') {
                    $file_path = $each;
                    $file_name = $pathinfo['filename'];
                    break;
                }
            }
        }
        $file_name .= '_viewable_tags.html';
        $new_tags = $path . 'xbrl/' . md5($job_id) . '/' . $file_name;
        $fileContent = Storage::disk('gcs')->get($file_path);
        $content = $template . $fileContent;
        Storage::disk('gcs')->put($new_tags, $content);
        $viewable_tag = Storage::disk('gcs')->get($new_tags);
        if ($action == 'download') {
            return response()->download(storage_path('app/public/source_files/' . $new_tags));
        } else {
            return $viewable_tag;
        }
    }

    public function assignJobNotification($data, $new_mail)
    {
        Mail::send('mail.assign_job', $data, function ($m) use ($new_mail) {
            $m->from('no-reply@1stopxbrl.becre8v.com', '1STOPXBRL');
            $m->to($new_mail['to'])->subject($new_mail['subject']);
            // $m->bcc('info@1stopxbrl.com');
        });

        $arr = [];
        $arr['type'] = 'Job assigned to vendor';
        $arr['date_sent'] = date('Y-m-d H:i:s');
        $arr['email_recipient'] = $new_mail['to'];
        $arr['email_cc'] = 'info@1stopxbrl.com';
        $this->saveEmailHistory($arr);
    }

    public function sendCommentAddOn(Request $request)
    {

        $job = Job::where('id', $request->input('job_id'))->first();
        $send_to_email = 'andrew.stewart@1stopxbrl.co.uk';

        $job_comment = $this->sendJobComment($send_to_email, $job->company, $job->id, $job->project_name, $request->input('comment'), $request->input('tag'));

        $arr = array();
        $arr['date_time'] = date('M d,Y h:i a', strtotime($job_comment->date_added));
        $arr['action'] = $job_comment->action;
        $arr['comment'] = $job_comment->comment;

        echo json_encode($arr);
    }

    public function sendJobComment($send_to_email, $company_id, $job_id, $project_name, $comment, $tag)
    {

        $company_name = Company::select('name')->where('id', '=', $company_id)->first();

        if (!is_null($send_to_email) && !empty($send_to_email)) {

            $mail = [];
            $mail['subject'] = $company_name->name . '- Job Comment (Job ID: ' . $job_id . ')' . $project_name;
            $mail['comment'] = $comment;
            $mail['to'] = $send_to_email;
            $mail['from'] = 'no-reply@1stopxbrl.com';

            Mail::raw($mail['comment'], function ($message) use (&$mail) {
                $message->subject($mail['subject']);
                $message->from($mail['from'], '1STOPXBRL');
                $message->to($mail['to']);
                // $message->bcc('andrew.stewart@1stopxbrl.co.uk');
                // $message->bcc('info@1stopxbrl.com');
            });
            $arr = [];
            $arr['type'] = 'Job Comment';
            $arr['date_sent'] = date('Y-m-d H:i:s');
            $arr['email_recipient'] = $send_to_email;
            $arr['email_cc'] = 'andrew.stewart@1stopxbrl.co.uk';
            $this->saveEmailHistory($arr);
        }

        $job_comment = new JobComment;
        $job_comment->job_id = $job_id;
        $job_comment->comment = $comment;
        $job_comment->action = 'Add Comment';
        $job_comment->date_added = date('Y-m-d H:i:s');
        $job_comment->tags = $tag;
        $job_comment->save();
        return $job_comment;
    }

    public function completeJobNotification($data, $new_mail)
    {

        Mail::send('mail.job_complete', $data, function ($m) use ($new_mail) {
            $m->from('no-reply@1stopxbrl.com', '1STOPXBRL');
            $m->to($new_mail['to'])->subject($new_mail['subject']);
            // $m->bcc('info@1stopxbrl.com');
        });
        $arr = [];
        $arr['type'] = 'Job Completed';
        $arr['date_sent'] = date('Y-m-d H:i:s');
        $arr['email_recipient'] = is_array($new_mail['to']) ? implode(",", $new_mail['to']) : $new_mail['to'];
        $arr['email_cc'] = 'info@1stopxbrl.com';
        $this->saveEmailHistory($arr);
    }

    public function saveEmailHistory($arr)
    {
        $arr['email_recipient'] = is_array($arr['email_recipient']) ? implode(',', $arr['email_recipient']) : $arr['email_recipient'];
        $arr['email_cc']  = is_array($arr['email_cc']) ? implode(',', $arr['email_cc']) : $arr['email_cc'];
        $email = DB::table('email_history')->insert(['type' => $arr['type'], 'date_sent' => $arr['date_sent'], 'email_recipient' => $arr['email_recipient'], 'email_cc' => $arr['email_cc']]);
    }
}
