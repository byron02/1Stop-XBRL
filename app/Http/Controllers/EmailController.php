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
use App\Models\IpBlock;
use App\Models\EmailHistory;
use Illuminate\Http\Request;
use App\Models\Job;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use DB;
use Response;
use Session;
use Hash;
use Mail;
use Illuminate\Support\Facades\Input;
class EmailController extends FrontsiteController
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $user_company = Auth::user()->company_id;

        
        $emails = EmailHistory::query();

         if(request()->has('search') && request()->has('filter_by')) {
               $emails = $emails->whereRaw(request('filter_by') ." LIKE '%". request('search') ."%'" );
              
               
          }

          if(request()->has('order_by'))
          {
            $emails = $emails->orderBy(request('order_by'),request('sort'));
          }

         if (request()->has('export')) {
                  $emails = $emails->get();
                  $filename = "emails.csv";
                  $handle = fopen($filename, 'w+');
                  fputcsv($handle, array('Email_ID', 'Type', 'Email_Recipient', 'Email_CC', 'Date_Sent', 'Attachments'));

                  foreach($emails as $row) {
                      fputcsv($handle, array($row['id'], $row['type'], $row['email_recipient'], $row['email_cc'], $row['date_sent'], $row['attachments']));
                  }

                  fclose($handle);

                  $headers = array(
                      'Content-Type' => 'text/csv',
                  );

                  return Response::download($filename, 'emails.csv', $headers);
          }
         
         else {
              $emails = $emails->orderBy('date_sent', 'desc');   
             
            
          }

          $emails = $emails->paginate(15);
        

          $emails->appends(Input::except('page'));

       


        return view('layouts.emails')
                    ->with('emails', $emails);


            
    }

    public function emailPost(Request $request) 
       {
        
        $this->validate($request, [ 'email' => 'required', 'message' => 'required' ]);

        $email = new EmailHistory;


        if(empty($request->get('file'))) {
           $email_attachments = 'none';

        }
        else {
           $email_attachments = $request->get('file');
        }
        $email->type = 'none';
        $email->date_sent = date("Y-m-d h:i:s");
        $email->email_recipient = $request->get('email');
        $email->email_cc = $request->get('cc_email');
        $email->email_attachments = $email_attachments;
        // $email->save();
         $file = Input::file('file');
       
        Mail::send('email_send',
         
           array(
                
               'user_name' => Auth::user()->first_name,
               'user_lastname' => Auth::user()->last_name,
               'user_subject' => Input::get('subject'),
               'user_message' => $request->get('message')
           ), function($message)
       {
           
           $from = Auth::user()->email;
           $to = Input::get('email');
           $subject = Input::get('subject');
           $file = Input::file('file');
           $message->from($from);
           $message->to($to)->subject($subject);
           if (!empty($file)) {
             $fileAttached = $file->getRealPath();
             $message->attach($fileAttached, [
                 'as' => $file->getClientOriginalName(), 
                 'mime' => $file->getMimeType()
             ]);
           }
           
           
           

          
       });
     
        return redirect('/emails');
       }
    
   
      

    public function store(Request $request) {
        return "success";
    }


    public function gcloudUrl($file) {
        if($file) {
            return Storage::disk('gcs')->url('users/' . $file.'.xls');
        }
    }
}
