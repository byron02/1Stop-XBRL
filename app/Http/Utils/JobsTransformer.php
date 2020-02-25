<?php

namespace App\Http\Utils;

use App\Models\Company;
use App\Models\JobStatus;
use App\Models\Pricing;
use App\Models\Taxonomy;
use App\Models\WorkType;


class JobsTransformer {

    static function transformToJobsMap($jobs) {

        $jobsMap = array();
        foreach ($jobs as $job) {

            $job_row = array();

            $job_row["job_number"] = $job->id;

            $job_row["is_invoiced"] = $job->is_invoiced;
            $job_row["project_name"] = $job->project_name;
            $job_row["due_date"] = $job->due_date;
            $status = JobStatus::where('id', $job->status)->get()->first();
            if ($status != null) {
                $job_row["status"] = $status['name'];
                $job_row["status_id"] = $status['id'];
            }

            $job_row["paid"] = $job->is_paid;
            $job_row["action"] = false;
            $job_row["purchase_order"] = $job->purchase_order;
            $job_row["price"] = $job->computed_price; //FIXME: which price??

            $job_row["company_name"] = $job->company_name;
            $job_row["company_id"] = $job->company;

            $taxonomy = Taxonomy::where('id', $job->taxonomy)->get()->first();
            if ($taxonomy['name'] != null) {
                $job_row['taxonomy'] = $taxonomy['name'];
            }

            // $vat = $job->computed_price * 0.20;
            $exclude = $job->computed_price - $job->tax_computation_price;

            $job_row["invoice_number"] = $job->invoice_number;
            $job_row["price"] = $exclude; //FIXME: which price??

            $workType = WorkType::where('id', $job->work_type)->get()->first();
            $job_row["work_type"] = $workType['name'];
            $company = Company::where('id', $job->company)->get()->first();
            $job_row["pricing_reference"] = $company['pricing_reference'];
            $job_row["total_pages_submitted"] = $job->total_pages_submitted;

            //get company of job
            //get pricing grid reference from company
            $pricingGridType = $company['pricing_grid'];

            // //get page from job
            // $vendorPrice = Pricing::where('pages', $job->total_pages_submitted)
            //     ->where('type', $pricingGridType)
            //     ->orderBy('created_at','DESC')
            //     ->first();

            // $job_row["price"] = $vendorPrice['price']; //FIXME: which price??

            $jobsMap[$job->id] = $job_row;
        }

        return $jobsMap;

    }

}
