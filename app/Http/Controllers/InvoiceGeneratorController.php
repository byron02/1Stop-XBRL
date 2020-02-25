<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use App\Models\JobStatus;
use App\Models\Country;
use App\Models\Invoice;
use App\Models\Pricing;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use PHPExcel_Worksheet_Drawing;
use App\Http\Traits\InvoiceGenerator;

class InvoiceGeneratorController extends FrontsiteController
{

    use InvoiceGenerator;

    public function index() {

        $companies = Company::orderBy('name', 'asc')->where('active',1)->get();
        $statuses = JobStatus::all();


        return view('invoicegenerator')
            ->with('companies', $companies)
            ->with('statuses', $statuses);
    }

    protected function validateSearchJobRequest(array $data) {
        return Validator::make($data, [
            'company' => 'required|string|max:255',
            'start_date' => 'required|date_format:d-m-Y|max:255|before:end_date',
            'end_date' => 'required|date_format:d-m-Y|max:255|after:start_date'
        ]);
    }

    public function searchJobs(Request $request) {

        $this->validateSearchJobRequest($request->all())->validate();

        $start = date('Y-m-d',strtotime($request['start_date']));
        $end = date('Y-m-d',strtotime($request['end_date']));
        $companyId = $request['company'];

        $startDateWithTime = $start.' 00:00:00';
        $endDateWithTime = $end.' 23:59:59';

        $searchedJobs = Job::where('company', $companyId)
            ->whereBetween('date_added', [$startDateWithTime, $endDateWithTime])
            ->where('status', '!=', 9)
            ->where('total_pages_submitted', '!=', 0)
            ->orderBy('date_added', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $searchedJobMap = [];

        foreach($searchedJobs as $searchedJob) {

            $searchedJobMapItem = array();
            $searchedJobMapItem["job_number"] = $searchedJob->id;
            $searchedJobMapItem["project_name"] = $searchedJob->project_name;
            $searchedJobMapItem["purchase_order"] = $searchedJob->purchase_order;
            $searchedJobMapItem["due_date"] = $searchedJob->due_date;
            $searchedJobMapItem['billable_date'] = $searchedJob->date_added;

            $company = Company::where('id', $searchedJob->company)->first();

            $pricingGridType = $company->pricing_grid;


            $vendorPrice = Pricing::where('pages', $searchedJob->total_pages_submitted)
                ->where('type', $pricingGridType)
                ->orderBy('created_at', 'DESC')
                ->first();
             
            $searchedJobMapItem["price"] = $vendorPrice['price'];

            if (!is_null($company) && !empty($company)) {
                $searchedJobMapItem["company"] = $company->name;
            } 
            
            $user = User::where('id', $searchedJob->order_by)->first();
            if (!is_null($user) && !empty($user)){
                $searchedJobMapItem["order_by"] = $searchedJob->order_by;
            }

            $status = JobStatus::where('id', $searchedJob->status)->first();
            if (!is_null($status) && !empty($status)) {
                $searchedJobMapItem["status"] = $status->name;
            }

            $searchedJobMap[$searchedJob->id] = $searchedJobMapItem;

        }
    
        return redirect('invoice-generator')->withInput()
                        ->with('searchedJobMap', $searchedJobMap)
                        ->with('companyId', $companyId);
    }

    public function changeJobsStatus(Request $request) {

        $jobs = $request['jobs-status-update'];
        $jobs = json_decode($jobs, true);
        $jobs = Job::whereIn('id', $jobs)->get();

        foreach($jobs as $job) {
            $job->status = $request['status'];
            $job->save();
        }

        $responseArray = ['status' => 200];
        return Response::json($responseArray);
    }

    public function generateJobsXlsx(Request $request) {
        // return Response::json($this->generateInvoice($request));
        return Response::json($this->forInvoiceGenerator($request));
    }

    public function downloadJobsXlsx(Request $request) {

        $targetFullStoragePath = storage_path("invoices/xls");
        $name = $request['filename'];

        return response()->download($targetFullStoragePath . '/'. $name, $name)->deleteFileAfterSend(true);
    }

}
