<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;

class UsersValidateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('register');
    }

    function check(Request, $request)
    {
        if($request->get('username'))
        {
            $username = $request->get('username');
            $data = DB::table('users')
                ->where('username', $username)
                ->count();

            if($data > 0)
            {
                echo 'not_unique';
            }
            else 
            {
                echo 'unique';
            }
        }
    }


}