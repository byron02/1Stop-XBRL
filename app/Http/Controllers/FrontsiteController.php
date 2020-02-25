<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Auth;
use View;
use Session;
class FrontsiteController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {

        $this->middleware('auth');

        $this->middleware(function ($request, $next) {
            $this->user= Auth::user();

            $this->shareMenus();
            return $next($request);
        });
    }

    private function shareMenus()
    {
        $roleTypes = [
            'CSR' => 3,
            'VENDOR' => 4,
            'CLIENT' => 1,
            'CLIENT_ADMIN' => 2,
            'CSR_ADMIN' => 8,
        ];

        $menus = [
            'jobs' => false,
            'invoice-generator' => false,
            'companies' => false,
            'logon-as-user' => false,
            'pricing-grid' => false,
            'configuration' => false,
            'emails' => false,

            'file-management' => false,
            'invoices' => false,
            'users' => false,
            'file-archive' => false,
            'invoice-recipient' => false,
            'clients-departments' => false,
            'admin-mode' => false,
            'vendors' => false

        ];



        if (!Auth::check()) {
            // The user is logged in...
            return;
        }

        $user = User::where('id', Auth::user()->id)->first();

        $role = UserRole::where('id', $user->role_id)->first();


        switch($role->id) {
            case $roleTypes['CSR_ADMIN']:
                $menus['logon-as-user'] = true;
                 $menus['users'] = true;
                 $menus['vendors'] = true;
                 $menus['configuration'] = true;
            case $roleTypes['CSR']:
                $menus['jobs'] = true;
                $menus['invoice-generator'] = true;
                $menus['companies'] = true;
                $menus['pricing-grid'] = true;
                $menus['configuration'] = true;
                $menus['emails'] = true;
                $menus['file-management'] = false;
                $menus['invoices'] = true;
                break;
            case $roleTypes['VENDOR']:
                $menus['jobs'] = true;
                break;


            case $roleTypes['CLIENT_ADMIN']:
                // $menus['file-management'] = true;
                $menus['invoices'] = true;
                $menus['users'] = true;
                $menus['file-archive'] = false;
                $menus['invoice-recipient'] = true;
                $menus['clients-department'] = true;
            case $roleTypes['CLIENT']:
                $menus['jobs'] = true;
                $menus['file-archive'] = false;
                $menus['invoice-recipient'] = true;
                break;

        }

        
        $orig = Session::get('orig_user');
        if(!empty($orig))
        {
            $menus['admin-mode'] = true;
        }

        View::share('menusMap', $menus);
        View::share('user_role', $role->name);
    }


    public function getAuthUserId() {
        return Auth::user()->id;
    }
}
