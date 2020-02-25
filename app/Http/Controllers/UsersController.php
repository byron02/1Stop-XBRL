<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invoice;
use App\Models\JobsInvoiceRecipient;
use App\Models\JobStatus;
use App\Models\Taxonomy;
use App\Models\User;
use App\Models\UserRole;
use App\Models\Country;
use App\Models\Timezone;
use App\Models\IpBlock;
use Illuminate\Http\Request;
use App\Models\Job;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Input;
use DB;
use Response;
use Session;
use Hash;
use Validator;

use Mail;



use App\Http\Traits\InvoiceGenerator;

class UsersController extends FrontsiteController
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
      use InvoiceGenerator;

    public function index() {

        $user_company = Auth::user()->company_id;

          

        $users = User::query()->join('companies', 'users.company_id', '=', 'companies.id')
                ->leftJoin('user_roles', 'users.role_id', '=', 'user_roles.id')
                ->leftJoin('user_statuses', 'users.status', '=', 'user_statuses.id');
                

         
        if(Auth::user()->role_id == 2)
         {
            $users = $users->where([
                                      ['users.company_id','=',Auth::user()->company_id],
                                      ['users.status','!=',3]
                                    ]);
         } 
         elseif(Auth::user()->role_id != 8 )
         {
            $users = $users->where('users.id','=',Auth::id());
         }

        $ip_address = User::where('company_id', '=', $user_company)
                                      ->select('ip_address', DB::raw('count(*) as total'))
                                      ->whereNotExists(function($query)
                                                 {
                                                     $query->select(DB::raw(1))
                                                           ->from('ip_block')
                                                           ->whereRaw('ip_block.ip_address = users.ip_address');
                                                 })
                                      ->groupBy('ip_address')
                                      ->paginate(5); 



        if(request()->has('filter_by')) {
            $search = request('search');

            $filter = 'users.'.request('filter_by');
            if(request('filter_by') == 'company_id')
            {
                $filter = 'companies.name';
            }
            elseif(request('filter_by') == 'role_id')
            {
              $filter = 'user_roles.name';
            }



            $users = $users->select('users.id', 'users.first_name', 'users.last_name', 'companies.name as company', 'users.job_title', 'user_roles.name as role', 'users.username', 'users.email', 'user_statuses.name as user_status', 'users.ip_address')
                    // ->where('users.company_id', '=', $user_company)
                    // ->where($filter,'LIKE',"'%".$search."%'")
                    ->whereRaw($filter." LIKE '%".$search."%' AND users.status != 3");
           
        }
        
        else {
            
            $users = $users->select('users.id', 'users.first_name', 'users.last_name', 'companies.name as company', 'users.job_title', 'user_roles.name as role', 'users.username', 'users.email', 'user_statuses.name as user_status', 'users.ip_address')
                    ->where('users.status', '!=', 3);
          
       }



      if(request('order_by') != '')
      {
          $users = $users->orderBy(request('order_by'),request('sort'));
      }
      else
      {
          $users = $users->orderBy('id','desc');
      }

       
       if (request()->has('export')) {
               $filename = "users.csv";
               $handle = fopen($filename, 'w+');
               fputcsv($handle, array('User_Id', 'First_Name', 'Last_Name', 'Company', 'Job_Title', 'Role', 'Username', 'Email', 'Status', 'IP_Address'));

               foreach($users as $row) {
                   fputcsv($handle, array($row['id'], $row['first_name'], $row['last_name'], $row['company'], $row['job_title'], $row['role']
                   , $row['username'], $row['email'], $row['status'], $row['ip_address']));
               }

               fclose($handle);

               $headers = array(
                   'Content-Type' => 'text/csv',
               );

               return Response::download($filename, 'users.csv', $headers);
       }


       if(request()->has('block_by')) {                             
           $blockIp = request('block_by');
           
           $this->blockIp($blockIp);
           return redirect('/users');


       }


       $users = $users->paginate(15);
        
        $countries = Country::select('*')
                    ->orderByRaw('name IN ("Ireland","United Kingdom") DESC')
                    ->orderBy('name','ASC')->get();
        $roles = UserRole::all();     
        $timezone = Timezone::all();
        return view('layouts.users')
            ->with('users', $users)
            ->with('ip_address', $ip_address)
            ->with('roles', $roles)
            ->with('timezone', $timezone)
            ->with('countries', $countries);


            
    }
    
    public function addUser(Request $request)
    {
      $rules = [
                'username' => 'required|string|max:255|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
               'password' => 'required|confirmed|min:6'
          ];
      
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){

            echo 'invalid';
            exit;
            // return back()->withErrors($validator);
        }
        
        $user = new User() ;
        $user->username = $request->get('username');
        $user->password =  md5($request->get('password'));
        $user->email = $request->get('email');
        $user->role_id = $request->get('role');
        $user->first_name = $request->get('fname');
        $user->last_name = $request->get('lname');
        $user->job_title = $request->get('job_title');
        $user->company_id = $request->get('vendors') != null ? 0 : Auth::user()->company_id;
        $user->address_line_1 = $request->get('address1');
        $user->address_line_2 = $request->get('address2');
        $user->address_line_3 = $request->get('address3');
        $user->city = $request->get('city');
        $user->country = $request->get('country');
        $user->post_code = $request->get('post_code');
        $user->telephone_number = $request->get('tel_number');
        $user->mobile_number = $request->get('mobile_number');
        $user->payment_method = 0;
        $user->timezone = $request->get('timezone');
        $user->status = 1;
        $user->last_login = date("Y-m-d h:i:s");
        $user->last_login_ip = '';
        $user->ip_address = '';
        $user->save();
        echo 'success';
        // Session::flash('msg','Post done successfully');
        // return redirect('/users');
     }

     public function ipUser(Request $request)
     {
          $user_company = Auth::user()->company_id;  
          $ip = $request->id;
          $usersIp = User::join('companies', 'users.company_id', '=', 'companies.id')
                  ->join('user_roles', 'users.role_id', '=', 'user_roles.id')
                  ->join('user_statuses', 'users.status', '=', 'user_statuses.id')
                  ->select('users.first_name', 'users.last_name', 'companies.name as company', 'users.job_title', 'users.email')
                  ->where('users.company_id', '=', $user_company)
                  ->where('users.ip_address', '=', $ip)
                  ->get(); 
            
              
          return Response::json($usersIp);
                    
              
           
        
     }

     public function blockIp($id)
     {
          


                

                $ipBlock = new IpBlock;
                
                $ipBlock->ip_address = $id;
                
                $ipBlock->save();



          
         
      }
      

    public function store(Request $request) {
        return "success";
    }


    public function gcloudUrl($file) {
        if($file) {
            return Storage::disk('gcs')->url('users/' . $file.'.xls');
        }
    }


    //ksm
    public function editUser($userId)
    {

        $countries = Country::select('*')
                    ->orderByRaw('name IN ("Ireland","United Kingdom") DESC')
                    ->orderBy('name','ASC')->get();
        $roles = UserRole::all();
        $timezone = Timezone::all();
        $userInfo = User::where('id','=',$userId)->get();
                return view('layouts.edit_user')
                    ->with('roles', $roles)
                    ->with('countries', $countries)
                    ->with('timezone', $timezone)
                    ->with('info',$userInfo); 
    }

    public function updateUser(Request $request)
    {
       $rules = [
                'username' => 'required|string|max:255',
                'email' => 'required|string|email|max:255',
          ];
  
        // $validator = Validator::make($request->all(),$rules);
        // if($validator->fails()){
        //    echo 'Invalid username or email.';
        //    exit;
        // }


        DB::table('users')->where('id',$request->input('user_id'))
        ->update([
                    'username' => $request->input('username'),
                    'email' => $request->input('email'),
                    'role_id' => $request->input('role'),
                    'first_name' => $request->input('fname'),
                    'last_name' => $request->input('lname'),
                    'job_title' => $request->input('job_title'),
                    'address_line_1' => $request->input('address1'),
                    'address_line_2' => $request->input('address2'),
                    'address_line_3' => $request->input('address3'),
                    'city' => $request->input('city'),
                    'country' => $request->input('country'),
                    'post_code' => $request->input('post_code'),
                    'telephone_number' => $request->input('tel_number'),
                    'mobile_number' => $request->input('mobile_number'),
                    'ip_address' => \Request::ip(),
                ]);
        echo 'success';
  
    }

    public function changeStatus($user_id,$action)
    {
        $status = $action == 'reject' ? 2 : 1;
        $user = User::where('id',$user_id)->update(['status' => $status]);

        // echo $user_id;
        if($status == 1)
        {
          $info = User::where('id',$user_id)->first();
          $companies = Company::where('id',$info->company_id)->update(['active' => 1]);


          $data = [
                            'first_name' => $info->first_name,
                            'last_name' => $info->last_name
                  ];
          $new_mail = [];
          $new_mail['subject'] = 'ServiceTrack 1stopxbrl - Successful Activation';
          $new_mail['to'] = [$info->email,'info@1stopxbrl.com'];
          Mail::send('mail.activated', $data, function ($m) use ($new_mail) {
              $m->from('no-reply@1stopxbrl.becre8v.com', '1STOPXBRL');
              $m->to($new_mail['to'])->subject($new_mail['subject']);
              // $m->bcc('info@1stopxbrl.com');
          });

          $arr = [];
          $arr['type'] = 'Activated User';
          $arr['date_sent'] = date('Y-m-d H:i:s');
          $arr['email_recipient'] = $info->email;
          $arr['email_cc'] = 'info@1stopxbrl.com';
          $this->saveEmailHistory($arr);

        }

        return $user;
    }

    public function iplockdown()
    {
      $ipblock = IpBlock::all();
      return view('layouts.ipblock')->with('ipaddress',$ipblock);
    }

    public function unblockIp($ip)
    {
      $ip = IpBlock::find($ip);
      $ip->delete();
      return redirect('iplockdown');

    }


    public function removeUser(Request $request)
    { 
      $user = DB::table('users')->where('id','=',$request->input('user_id'))->update(['status' => $request->input('status')]);
        // $user->delete();
    }

    public function showDeletedUsers()
    {
        $users = User::query()
              ->select(DB::raw('users.id,users.first_name,users.last_name,companies.name company,users.job_title,user_roles.name role,users.username,users.email,users.ip_address'))
              ->join('companies', 'users.company_id', '=', 'companies.id')
                ->join('user_roles', 'users.role_id', '=', 'user_roles.id')
                ->join('user_statuses', 'users.status', '=', 'user_statuses.id')
                ->orderBy('users.id','ASC');

       
            $users = $users->where('users.status','=',3);

         $users = $users->paginate(15);
        return view('layouts.deleted_users')->with('users',$users);
    }

    public function setNewPassword(Request $request)
    {
       $rules = ['new_pass' => 'required|min:6'];
        
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){

            echo 'invalid';
            exit;
            // return back()->withErrors($validator);
        }
        
        DB::table('users')->where('id','=',$request->input('user'))->update(['password' => md5($request->input('new_pass'))]);
        echo 'success';
    }

      public function saveEmailHistory($arr)
    {
         $email = DB::table('email_history')->insert(['type' => $arr['type'],'date_sent' => $arr['date_sent'],'email_recipient' => $arr['email_recipient'],'email_cc' => $arr['email_cc']]);
    }
    
}
