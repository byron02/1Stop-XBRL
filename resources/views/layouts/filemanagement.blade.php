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
            <h2 class="content-title">Backup Files</h2>
            
           

            
              
            <div class="datagrid_wrap">
                ﻿
                    Filter By :
                    <a href="{{url('/filemanagement/?filter_by=1')}}" style="color:black;">Input Files</a> |
                    <a href="{{url('/filemanagement/?filter_by=2')}}" style="color:black;">Output Files</a> |
                    <a href="{{url('/filemanagement/?filter_by=4')}}" style="color:black;">Revision Files</a> |
                    
                    <table class="email_list" id="userTable">
                        <tr>
                            <td class="datagrid_header" width="25px">ID <a href="http://servicetrack.1stopxbrl.co.uk/csr/jobs/index/null/null/project_name/asc/0"><img src="http://servicetrack.1stopxbrl.co.uk/assets/images/icon_arrow.png"></a></td>
                            <td class="datagrid_header" width="25px">File Name</td>
                            <td class="datagrid_header" width="25px">File Type</td>
                            <td class="datagrid_header" width="115px">Date of Backup<a href="http://servicetrack.1stopxbrl.co.uk/csr/jobs/index/null/null/project_name/asc/0"><img src="http://servicetrack.1stopxbrl.co.uk/assets/images/icon_arrow.png"></a></td>
                            <td class="datagrid_header" width="115px">Backup Range<a href="http://servicetrack.1stopxbrl.co.uk/csr/jobs/index/null/null/project_name/asc/0"><img src="http://servicetrack.1stopxbrl.co.uk/assets/images/icon_arrow.png"></a></td>
                             

                        </tr>


                        @foreach($files as $key => $file)
                             <tr>
                             <td>{{ $file->id }}</td>
                             <td>{{ $file->file_name }}</td>
                             <td style="text-align: center;">@if($file->type == 1)Input @elseif($file->type == 2) Output @elseif($file->type == 3) Created @elseif($file->type == 4) Revisions @endif
                             </td>
                             <td>{{ $file->date_created }}</td>
                             <td>{{ $file->date_to }} - {{ $file->date_from }}</td>
                             
                             </tr>
                        @endforeach
                      
                    </table>
                   
                    <div class="pagination-container">

                    Displaying {{ $files->firstItem() }} to {{ $files->lastItem() }} of {{ $files->total() }} Records(s)
                    {{ $files->links() }}
                    </div>
                   
            </div>


            
        </div>

       

    </div>
    
   
    @endsection