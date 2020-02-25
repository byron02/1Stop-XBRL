<?php

namespace App\Http\Controllers;

use App\Models\LogOnAsUser;
use App\Models\Company;
use App\Models\User;

use Illuminate\Http\Request;
use DB;
use Auth;
use Session;


class LogOnAsUserController extends FrontsiteController 
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company = Company::orderBy('name','asc')->where('active',1)->get();
        return view('layouts.logon_as_user')->with('company',$company);
    }

   public function showCompanyUsers($company_id)
   {
        $users = DB::table('users')
                    ->select('users.*', 'user_roles.name as role')
                    ->leftJoin('user_roles', 'users.role_id', '=', 'user_roles.id');
                    
      if($company_id == 'vendor')
      {
        $users = $users->where('users.role_id','=','4');
      }
      else
      {
        $users = $users->where('company_id','=',$company_id);
      }           


        $users = $users->orderBy('users.first_name','ASC')->where('users.status','=',1)->get();

        echo json_encode($users);
   }

   public function loginUser(Request $request)
   {
        $new_user = User::find($request->input('user'));
        Session::put('orig_user', Auth::id() );
        Auth::login( $new_user );
        return redirect('/');
   }
   public function backToAdmin()
   {
        $orig = Session::get('orig_user');
        $admin = User::find($orig);
        Session::forget('orig_user');
        Auth::login($admin);
        return redirect('/');

   }
}
