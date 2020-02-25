<?php

namespace App\Http\Controllers;
use App\Models\Company;
use App\Models\User;
use App\Models\UserRole;
use App\Models\BackupFile;
use App\Models\Country;
use App\Models\InvoiceRecipient;

use Illuminate\Http\Request;
use App\Models\Job;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use DB;
use Response;
use Session;
use Hash;
use Mail;
use Validator;
use Illuminate\Support\Facades\Input;


class InvoiceRecipientController extends FrontsiteController
{
    	    
    public function index() {
    	$country = Country::orderBy('name','asc')->get();
    	$recipient = InvoiceRecipient::where('company_id','=',Auth::user()->company_id)->first();
       	return view('invoicerecipient')->with('country',$country)->with('recipient',$recipient);    
    }

    public function copyCompany()
    {
    	$company = DB::table('users')
					->select(
							DB::raw('companies.id company_id,CONCAT(users.first_name," ",users.last_name) full_name,
									job_title,address_line_1,
									address_line_2,
									address_line_3,
									users.city,post_code,telephone_number,mobile_number,users.email,
									companies.name company_name,users.country')
						)
					->leftJoin('companies','users.company_id','=','companies.id')
					->where('users.id','=',Auth::id())->get();
		echo $company->toJson();
    }

    public function saveRecipient(Request $request)
    {
    	$rules = array(
		 					'company_name' => 'required',
		 					'full_name' => 'required',
		 					'job_title' => 'required',
		 					'address_line_1' => 'required',
		 					'city' => 'required',
		 					'country' => 'required',
		 					'post_code' => 'required',
		 					'telephone_no' => 'required',
		 					'mobile_number' => 'required',
		 					'email_address' => 'required',
		 				);
 		$validator = Validator::make($request->all(),$rules);

 		if($validator->fails())
 		{
 			echo 'invalid';
		    die();
 		}

 		
		try{

			$recipient = InvoiceRecipient::where('company_id',$request->input('company_id'))->first();

	      	$invoice = new InvoiceRecipient();
	 		$invoice->company_id = $request->input('company_id');
			$invoice->company_name = $request->input('company_name');
			$invoice->fullname = $request->input('full_name');
			$invoice->job_title = $request->input('job_title');
			$invoice->address_line_1 = $request->input('address_line_1');
			$invoice->address_line_2 = $request->input('address_line_2');
			$invoice->address_line_3 = $request->input('address_line_3');
			$invoice->city = $request->input('city');
			$invoice->country = $request->input('country');
			$invoice->post_code = $request->input('post_code');
			$invoice->telephone_number = $request->input('telephone_no');
			$invoice->mobile_number = $request->input('mobile_number');
			$invoice->email = $request->input('email_address');
			if(empty($recipient))
			{
				$invoice->save();
			}
			else
			{
				DB::table('invoice_recipient')->where('id',$recipient->id)->update($invoice->toArray());
			
			}
			
			echo 'Successful';
	    }
	    catch(\Exception $e){
	       return $e->getMessage();
	    }
	   
    }
}
