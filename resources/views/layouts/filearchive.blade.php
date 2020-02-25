@extends('layouts.frontsite')

@section('content')

    <?php
    error_reporting(-1); // reports all errors
    ini_set("display_errors", "1"); // shows all errors
    ini_set("log_errors", 1);
    ini_set("error_log", "/tmp/php-error.log");
    ?>
    <div class="wrapper">
        <div class="page-holder">
            <h2 class="content-title">File Archive</h2>
            
                <div class="datagrid_wrap">
                    ﻿
                        Filter By :
                        <a href="{{url('/filearchive/?filter_by=id')}}" style="color:black;">File Id</a> |
                        <a href="{{url('/filearchive/?filter_by=file_name')}}" style="color:black;">Filename</a> |
                        <a href="{{url('/filearchive/?filter_by=date_uploaded')}}" style="color:black;">Date Uploaded</a> |
                        <a href="{{url('/filearchive/?filter_by=uploaded_by')}}" style="color:black;">Uploaded By</a> |
                        
                        
                        <table class="email_list" id="userTable">
                            <tr>
                                <td class="datagrid_header" width="25px">FileID <a href="http://servicetrack.1stopxbrl.co.uk/csr/jobs/index/null/null/project_name/asc/0"><img src="http://servicetrack.1stopxbrl.co.uk/assets/images/icon_arrow.png"></a></td>
                                <td class="datagrid_header" width="25px">Filename</td>
                                <td class="datagrid_header" width="25px">Client / Department</td>
                                <td class="datagrid_header" width="115px">Date Uploaded<a href="http://servicetrack.1stopxbrl.co.uk/csr/jobs/index/null/null/project_name/asc/0"><img src="http://servicetrack.1stopxbrl.co.uk/assets/images/icon_arrow.png"></a></td>
                                <td class="datagrid_header" width="115px">Uploaded<a href="http://servicetrack.1stopxbrl.co.uk/csr/jobs/index/null/null/project_name/asc/0"><img src="http://servicetrack.1stopxbrl.co.uk/assets/images/icon_arrow.png"></a></td>
                            
                                 

                            </tr>

                            @foreach($files as $key => $file)
                                 <tr>
                                 <td>{{ $file->id }}</td>
                                 <td>{{ $file->file_name }}</td>
                                 <td></td>
                                 <td>{{ $file->date_uploaded }}</td>
                                 <td>{{ $file->uploaded_by }}</td>
                                 
                                 </tr>
                            @endforeach

                          
                        </table>
                        <a href="{{url('/filearchive/?export=1')}}" style="color:black;">Export as CSV</a>
                        <div class="pagination-container">

                        Displaying {{ $files->firstItem() }} to {{ $files->lastItem() }} of {{ $files->total() }} Records(s)
                        {{ $files->links() }}
                        </div>
                       
                </div>

            
              
            


            
        </div>

       

    </div>
    
   
@endsection