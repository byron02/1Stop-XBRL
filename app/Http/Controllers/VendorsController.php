<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Vendors;
use App\Models\Company;
use App\Models\User;
use App\Models\UserRole;
use App\Models\Country;
use App\Models\Timezone;

use DB;
use Auth;
use Session;

class VendorsController extends FrontsiteController
{
    public function index()
    {
    	$users = User::select(DB::raw('users.*,user_statuses.name user_status,companies.name company_name'))
    			->leftJoin('user_statuses', 'users.status', '=', 'user_statuses.id')
    			->leftJoin('companies', 'companies.id', '=', 'users.company_id')
    			->orderBy('id','DESC')
				->where('role_id','=',4)
				->where('status','=',1);

		$countries = Country::select('*')
                    ->orderByRaw('name IN ("Ireland","United Kingdom") DESC')
                    ->orderBy('name','ASC')->get();
        $roles = UserRole::all();     
        $timezone = Timezone::all();
		$users = $users->paginate(15);

    	return view('layouts.vendors')
    				->with('vendors',$users)
		            ->with('roles', $roles)
		            ->with('timezone', $timezone)
		            ->with('countries', $countries);
    }

    public function showDeletedVendors()
    {
    	$users = User::select(DB::raw('users.*,user_statuses.name user_status,companies.name company_name'))
    			->leftJoin('user_statuses', 'users.status', '=', 'user_statuses.id')
    			->leftJoin('companies', 'companies.id', '=', 'users.company_id')
    			->orderBy('id','DESC')
				->where('role_id','=',4)
				->where('status','=',3);

		$users = $users->paginate(15);
    	return view('layouts.deleted_vendors')->with('users',$users);
    }
}
