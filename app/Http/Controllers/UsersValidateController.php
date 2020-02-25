<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Mail;
use Illuminate\Support\Facades\Redirect;
use Session;

class RegisterController extends Controller
{

    public function check(Request $request) {
        return $request;
        exit;
        // if($request->get('username'))
        //     {
        //         $username = $request->get('username');
        //         $data = DB::table('users')
        //             ->where('username', $username)
        //             ->count();
    
        //         if($data > 0)
        //         {
        //             echo 'not_unique';
        //         }
        //         else 
        //         {
        //             echo 'unique';
        //         }
        //     }
    }

}