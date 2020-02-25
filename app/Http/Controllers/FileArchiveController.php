<?php

namespace App\Http\Controllers;
use App\Models\Company;
use App\Models\User;
use App\Models\UserRole;
use App\Models\JobsSourceFile;


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


class FileArchiveController extends FrontsiteController
{
    public function index() {
    	$user_company = Auth::user()->company_id;

    	$files = JobsSourceFile::query();
    	
    	if(request()->has('filter_by')) {
    	            $files = $files->orderBy(request('filter_by'), 'asc')->paginate(15);

    	   
    	 }
    	 elseif (request()->has('export')) {
    	          $files = $files->get();
    	          $filename = "fileArchive.csv";
    	          $handle = fopen($filename, 'w+');
    	          fputcsv($handle, array('File_ID', 'File_Name', 'Client/Department', 'Date_Uploaded', 'Uploaded_By'));

    	          foreach($files as $row) {
    	              fputcsv($handle, array($row['id'], $row['file_name'],  '', $row['date_uploaded'], $row['uploaded_by']));
    	          }

    	          fclose($handle);

    	          $headers = array(
    	              'Content-Type' => 'text/csv',
    	          );

    	          return Response::download($filename, 'fileArchive.csv', $headers);
    	  }
    	 
    	 else {
    	            $files = $files->paginate(15);   
    	     
    	    
    	  }


    	return view('layouts.filearchive')->with('files', $files);
    }
}
