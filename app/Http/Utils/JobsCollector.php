<?php
/**
 * Created by PhpStorm.
 * User: kirby
 * Date: 06/02/2018
 * Time: 3:52 PM
 */

namespace App\Http\Utils;


use App\Models\Job;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Auth;

class JobsCollector {

    static function collectJobs() {

        $roleTypes = [
            'CSR' => 3,
            'VENDOR' => 4,
            'CLIENT' => 1,
            'CLIENT_ADMIN' => 2,
            'CSR_ADMIN' => 8,
        ];

        $user = User::where('id', Auth::user()->id)->first();

        $role = UserRole::where('id', $user->role_id)->first();

        switch ($role->id) {

            case $roleTypes['CSR']:

            case $roleTypes['CSR_ADMIN']:

                $jobs = new Job();

                break;

            case $roleTypes['VENDOR']:

                $jobs = Job::where('vendor_id', Auth::user()->id);

                break;

            case $roleTypes['CLIENT']:

                $jobs = Job::where('user_id', Auth::user()->id);

                break;

            case $roleTypes['CLIENT_ADMIN']:

                $jobs = Job::where('company', $user->company_id);

                break;

            default:

                $jobs = new Job();

        }

        return $jobs;
    }

}