<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Company;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Mail;
use DB;
use Illuminate\Support\Facades\Redirect;
use Session;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    function index()
    {
        return view('register');
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $rules = [
                'username' => 'required|string|max:255|unique:users',
                'email' => 'required|string|email|max:255',
               'password' => 'required|confirmed|min:6'
          ];
      
        $validator = Validator::make($data,$rules);
        
       
        if($validator->fails()){
            $error_message = json_encode($validator->errors()->all());
            $form_data = json_encode($data);
           echo '<script>
                    window.localStorage.setItem("reg_error", JSON.stringify('.$error_message.'));
                    window.localStorage.setItem("form_data", JSON.stringify('.$form_data.'));
                    window.location = "/register";
                </script>';

            //FIXme: return redirect()->back() not working.
           exit;
        }


//        return User::create([
//            'name' => $data['name'],
//            'email' => $data['email'],
//            'password' => bcrypt($data['password']),
//        ]);
       $company = Company::create([
                       'name' => $data['company_name'],
                       'address1'  => $data['address_line_1'],
                        'address2'  => $data['address_line_2'],
                        'address3'  => $data['address_line_3'],
                        'city'  => $data['city'],
                        'country'  => $data['country'],
                        'postcode'  => $data['post_code'],
                        'phone'  => $data['telephone_number'],
                        'email'  => $data['email'],
                        'timezone'  => $data['timezone'],
                        'date_added' => date('Y-m-d H:i:s'),
                        'region' => '',
                        'religion' => '',
                        'fax'  => '',
                        'adjustment_type'  => 0,
                        'default_vendor'  => 0,
                        'pricing_reference'  => 0,
                        'pricing_grid'  => 0
                   ]);
//        $companies = Company::all()->first();

        $user = User::create([
            'username' => $data['username'],
            'password'  => md5($data['password']),
            'first_name'  => $data['first_name'],
            'last_name'  => $data['last_name'],
            'job_title'  => $data['job_title'],
            'company_id'  => $company->id,
            'address_line_1'  => $data['address_line_1'],
            'address_line_2'  => $data['address_line_2'],
            'address_line_3'  => $data['address_line_3'],
            'city'  => $data['city'],
            'country'  => $data['country'],
            'post_code'  => $data['post_code'],
            'telephone_number'  => $data['telephone_number'],
            'mobile_number'  => $data['mobile_number'],
            'email'  => $data['email'],
            'payment_method'  => 0, //FIXME: should have value
            'timezone'  => $data['timezone'],
            'last_login'  => date('Y-m-d H:i:s'), //FIXME: actual format
            'last_login_ip'  => \Request::ip(), //FIXME: actual IP?
            'ip_address' => \Request::ip(),
//            'status' => $data['username'],
            'role_id' => 2
//            'ip_address => "localhost",
        ]);

       
        // Mail::send('email', $data, function($message) use ($data)
        // {
        //     $message->from('no-reply@1stopxbrl.becre8v.com', "Service Track - XBRL Global Delivery System");
        //     $message->subject("Welcome - Service Track - XBRL Global Delivery System");
        //     $message->to($data['email']);
        // });
        $email = [$data['email'],'andrew.stewart@1stopxbrl.co.uk'];
        $data['email'] = $email;
        $arr = ['data' => $data];
        Mail::send('mail.new_account', $data, function($message) use ($data)
        {
            $message->from('no-reply@1stopxbrl.becre8v.com', "Service Track - XBRL Global Delivery System");
            $message->subject("Welcome - Service Track - XBRL Global Delivery System");
            $message->to($data['email']);
        });

        return $user;
    }

    public function check(Request $request) {
        $unique = 'unique';
        $not_unique = 'not_unique';
       
        if($request->get('username'))
            {
            $username = $request->get('username');
                $data = DB::table('users')
                    ->where('username', $username)
                    ->count();

                if($data > 0)
                {
                    return $not_unique;
                }
                else 
                {
                    return $unique;
                }
            }
    }

    public function checkEmail(Request $request) {
        $unique = 'unique';
        $not_unique = 'not_unique';
       
        if($request->get('email'))
            {
            $email = $request->get('email');
                $data = DB::table('users')
                    ->where('email', $email)
                    ->count();

                if($data > 0)
                {
                    return $not_unique;
                }
                else 
                {
                    return $unique;
                }
            }
    }


}
