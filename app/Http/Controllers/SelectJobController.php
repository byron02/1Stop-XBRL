<?php

namespace App\Http\Controllers;

use App\Http\Utils\JobsCollector;
use App\Http\Utils\JobsTransformer;
use App\Models\Company;
use App\Models\JobStatus;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class SelectJobController extends FrontsiteController
{
    public function index() {

        if (!Auth::check()) {
            // The user is logged in...
            return;
        }

        $jobs = JobsCollector::collectJobs()
            ->orderBy('date_added', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(50);

        $jobsMap = JobsTransformer::transformToJobsMap($jobs);

        $jobStatus = JobStatus::all();

        return view('selectjob')
            ->with('jobStatuses', $jobStatus)
            ->with('jobs', $jobs)
            ->with('jobsMap', $jobsMap);
    }

    public function searchJobs(Request $request) {

        $jobStatus = JobStatus::all();

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

            $searchedJobs = JobsCollector::collectJobs()
                ->where('status', $jobStatusId)
                ->whereBetween('date_added', [$startDate, $endDate])
                ->orderBy('date_added', 'desc')
                ->orderBy('id', 'desc')
                ->paginate(50);

            $searchedJobs->appends(Input::except('page'));

            $jobsMap = JobsTransformer::transformToJobsMap($searchedJobs);

            return view('selectjob')
                ->with('jobStatuses', $jobStatus)
                ->with('jobsMap', $jobsMap)
                ->with('jobs', $searchedJobs);

        } else {

            $queryBy = $request['search_by_query'];
            $query = $request['query'];
            $queryDate = Carbon::parse($request['query_date'])->format('Y-m-d');
            $queryMonth = $request['query_month'];

            if ($queryBy == "id" || $queryBy == "project_name") {

                $searchedJobs = JobsCollector::collectJobs()
                    ->where($queryBy, 'LIKE', '%'.$query.'%')
                    ->orderBy('date_added', 'desc')
                    ->orderBy('id', 'desc')
                    ->paginate(50);

                $searchedJobs->appends(Input::except('page'));

                $jobsMap = JobsTransformer::transformToJobsMap($searchedJobs);

                return view('selectjob')
                    ->with('jobStatuses', $jobStatus)
                    ->with('jobsMap', $jobsMap)
                    ->with('jobs', $searchedJobs);

            } else if ($queryBy == "company_name") {

                $companyIds  = Company::where('name', 'LIKE', '%'.$query.'%')
                    ->pluck('id')
                    ->toArray();

                $searchedJobs = JobsCollector::collectJobs()
                    ->whereIn('company', $companyIds)
                    ->orderBy('date_added', 'desc')
                    ->orderBy('id', 'desc')
                    ->paginate(50);

                $searchedJobs->appends(Input::except('page'));

                $jobsMap = JobsTransformer::transformToJobsMap($searchedJobs);

                return view('selectjob')
                    ->with('jobStatuses', $jobStatus)
                    ->with('jobsMap', $jobsMap)
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

                $searchedJobs = JobsCollector::collectJobs()
                    ->whereBetween('due_date', [$startMonth, $endMonth])
                    ->orderBy('date_added', 'desc')
                    ->orderBy('id', 'desc')
                    ->paginate(50);

                $searchedJobs->appends(Input::except('page'));

                $jobsMap = JobsTransformer::transformToJobsMap($searchedJobs);

                return view('selectjob')
                    ->with('jobStatuses', $jobStatus)
                    ->with('jobsMap', $jobsMap)
                    ->with('jobs', $searchedJobs);

            } else {

                $this->validateSearchByQueryDate($request->all())
                    ->setAttributeNames(array(
                        'query_date' => 'date'
                    ))
                    ->validate();

                $date = date_format(Carbon::parse($queryDate),'Y-m-d');
                $date = Carbon::parse($date);

                $searchedJobs = JobsCollector::collectJobs()
                    ->where($queryBy, $date)
                    ->orderBy('date_added', 'desc')
                    ->orderBy('id', 'desc')
                    ->paginate(50);

                $searchedJobs->appends(Input::except('page'));

                $jobsMap = JobsTransformer::transformToJobsMap($searchedJobs);

                return view('selectjob')
                    ->with('jobStatuses', $jobStatus)
                    ->with('jobsMap', $jobsMap)
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

}
