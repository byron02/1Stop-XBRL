@extends('layouts.frontsite')

@section('content')

    <div class="wrapper">
        <div class="page-holder">
            <h2 class="content-title">Jobs</h2>
            <div class="add-btn">
            @if(Auth::user()->role_id != 4)
                <a href="{{url('/add-jobs')}}" class="styled_btn submit_btn">Add Job</a>
                <a  class="styled_btn submit_btn roll_forward_previous" style="text-decoration:none">Roll Forward Previous Year</a>
            @endif
               @if(Auth::user()->role_id == 8 || Auth::user()->role_id == 4)
                    <a id="export-csv-jobs" class="styled_btn submit_btn" style="text-decoration:none">Export CSV</a>
                @endif
            </div>

            <div class="clear"></div>

            <div class="col-sm-8">
                <form class="form-inline" method="get" action="{{ url('search-jobs') }}">
                    <div class="form-group mb-15p">
                        <label>Filter By :</label>
                        <select id="search-by-selector" name="search_by" class="form-control">
                                <option value="" selected disabled>--Please Select--</option>
                                <option value="1" {{ request('search_by') == 1 ? 'selected' : '' }}>Status</option>
                                <option value="2" {{ request('search_by') == 2 ? 'selected' : '' }}>Query</option>
                        </select>
                    </div>
                    <div class="form-group mb-15p" style="display:none" id="search-by-query-tr">
                        <label>Search By : </label>
                         <select class="form-control" id="query-selector" name="search_by_query" required autofocus disabled>
                                @php
                                    $filters = [];
                                    $filters["id"] = "Id";
                                    $filters["project_name"] = "Project Name";
                                    $filters["company_name"] = "Company Name";
                                    $filters["date_added"] = "Date Added";
                                    $filters["due_date"] = "Due Date";
                                    $filters["by_month"] = "By Month";

                                @endphp  

                                @foreach ($filters as $key => $val)
                                     <option  {{ $key == request('search_by_query') ? 'selected' : '' }} value="{{$key}}">{{$val}}</option>
                                @endforeach
                            </select>
                        &nbsp;
                        <div class="input-group ">
                            <input type="text" id="query-input" class="form-control" name="query"  value="{{ request('query') }}" disabled/>
                            <input type="text" class="monthyearpicker form-control" id="query-month-input" name="query_month" value="{{ request('query_month') }}" disabled/>
                            <input type="text" class="datepicker form-control" id="query-date-input" name="query_date" value="{{ request('query_date') }}" disabled/>
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-default">
                                    <span class="glyphicon glyphicon-search"></span> Search
                                </button>
                            </span>
                        </div>
                    </div>

                    <div class="form-group mb-15p" style="display:block;" id="search-by-status-tr">
                        <select class="form-control" id="status-selector" name="search_by_status" required autofocus disabled>
                            @foreach($jobStatuses as $jobStatus)
                                @if(old('search_by_status') == $jobStatus->id)
                                    <option value="{{$jobStatus->id}}" selected>{{$jobStatus->name}}</option>;
                                @else
                                    <option value="{{$jobStatus->id}}">{{$jobStatus->name}}</option>;
                                @endif
                            @endforeach
                        </select>


                        &nbsp;
                        <input type="text" class="datepicker form-control" id="search_status_start_date_input" name="search_by_status_start_date" value="{{ request('search_by_status_start_date') }}" disabled/>
                         <div class="input-group ">
                            <input type="text" class="datepicker form-control" id="search_status_end_date_input" value="{{ request('search_by_status_end_date') }}" name="search_by_status_end_date" disabled/>
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-default">
                                    <span class="glyphicon glyphicon-search"></span> Search
                                </button>
                            </span>
                        </div>

                        <div class="{{ $errors->has('search_by_status_start_date') ? ' has-error' : '' }}">
                            

                            @if ($errors->has('search_by_status_start_date'))
                                <span class="help-block">
                                <strong>{{ $errors->first('search_by_status_start_date') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="{{ $errors->has('search_by_status_end_date') ? ' has-error' : '' }}">
                            

                            @if ($errors->has('search_by_status_end_date'))
                                <span class="help-block">
                                <strong>{{ $errors->first('search_by_status_end_date') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                </form>
                @if(Auth::user()->role_id == 8)
                    <div class="form-group form-inline">
                            Select Action
                            <select id="job-action" class="form-control">
                                <option value="Pending Sign-off">Complete for client sign of</option>
                                <option value="In Progress">Assign to vendor</option>
                                <option value="In Revision">Assign to back vendor</option>
                            </select>
                            <span class="vendor_row hidden">
                                &nbsp;
                                Select Vendor
                                <select id="vendor-selection" class="form-control"></select>
                            </span>
                            <button class="styled_btn submit_btn" type="button" id="status-update-btn">Update Status</button>
                            
                    </div> 
                @endif
            </div>
            @if(Auth::user()->role_id == 8 || Auth::user()->role_id == 4)
                <div class="col-sm-4 text-right pr-0">
                    <br/><br/><br/>
                    @php
                        $job_status_icon = array(
                                                    'fa-cart-plus',
                                                    'fa-hourglass-half',
                                                    'fa-edit',
                                                    'fa-share-square',
                                                );
                       
                    @endphp
                    @foreach($banner_status as $k => $bs)
                         <a class="styled_btn submit_btn job_status " href="{{ url('/?status='.$bs->id) }}"  data-toggle="tooltip" title="{{ $bs->name }}" data-placement="top" data-trigger="hover">
                            <i class="fa {{$job_status_icon[$k]}}"></i>
                            {{ $bs->job_count }}
                        </a> 
                    @endforeach
                </div>
            @endif
                <div class="clearfix"></div>
             @if(session('message'))
                <div class="alert alert-success alert-dismissible">
                  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                  <strong>[#{{session('job_id')}}]</strong> {{ session('message') }}
                </div>
            @endif
            @php
                $sort = request('sort') == 'asc' ? 'desc' : 'asc';
                $icon = array(
                                'nan' => 'fa-sort',
                                'asc' => 'fa-sort-up',
                                'desc' => 'fa-sort-down'
                            );

                $sort_by = request('order_by') != '' ? request('sort') : 'nan';
                $job_no_icon = request('order_by') == 'id' ? $icon[$sort_by] : 'fa-sort';
                $client_po_icon = request('order_by') == 'purchase_order' ? $icon[$sort_by] : 'fa-sort';
                $project_icon = request('order_by') == 'project_name' ? $icon[$sort_by] : 'fa-sort';
                $pricing_icon = request('order_by') == 'computed_price' ? $icon[$sort_by] : 'fa-sort';
                $company_icon = request('order_by') == 'companies.name' ? $icon[$sort_by] : 'fa-sort';
                $due_date_icon = request('order_by') == 'due_date' ? $icon[$sort_by] : 'fa-sort';
                $invoice_icon = request('order_by') == 'invoice_number' ? $icon[$sort_by] : 'fa-sort';
                $pages_icon = request('order_by') == 'total_pages_submitted' ? $icon[$sort_by] : 'fa-sort';
            @endphp
            <div class="table-responsive">
                @if(Auth::user()->role_id == 8)
                <table class="table table-condensed table-hover table-striped">
                    <thead>
                        <tr class="dark">
                            <th class="text-center">
                                <input type="checkbox" id="chk_all"></th>
                            <th>
                                <div class="pull-left">
                                    Job # 
                                </div>
                                <div class="pull-right">
                                    <a href="{{ url('/?order_by=id&sort='.$sort) }}">
                                        <i class="fa {{ $job_no_icon }}"></i>
                                    </a>
                                </div>
                                <div class="clearfix"></div>
                            </th>
                            <th>
                                Client PO
                                <div class="pull-right">
                                    <a href="{{ url('/?order_by=purchase_order&sort='.$sort) }}">
                                        <i class="fa {{ $client_po_icon }}"></i>
                                    </a>
                                </div>
                                <div class="clearfix"></div>
                            </th>
                            <th>
                                Project Name
                                <div class="pull-right">
                                    <a href="{{ url('/?order_by=project_name&sort='.$sort) }}">
                                        <i class="fa {{ $project_icon }}"></i>
                                    </a>
                                </div>
                                <div class="clearfix"></div>
                            </th>
                            <th>
                                Pricing Reference
                            </th>
                            <th>Pages</th>
                            <th>
                                Pricing
                                <div class="pull-right">
                                    <a href="{{ url('/?order_by=computed_price&sort='.$sort) }}">
                                        <i class="fa {{  $pricing_icon }}"></i>
                                    </a>
                                </div>
                                <div class="clearfix"></div>
                            </th>
                            <th>
                                Company
                                <div class="pull-right">
                                    <a href="{{ url('/?order_by=companies.name&sort='.$sort) }}">
                                        <i class="fa {{  $pricing_icon }}"></i>
                                    </a>
                                </div>
                                <div class="clearfix"></div>
                            </th>
                            <th>
                                Due Date
                                <div class="pull-right">
                                    <a href="{{ url('/?order_by=due_date&sort='.$sort) }}">
                                        <i class="fa {{ $due_date_icon }}"></i>
                                    </a>
                                </div>
                                <div class="clearfix"></div>
                            </th>
                            <th class="text-center">Status</th>
                            <th>
                                Invoice Number
                                <div class="pull-right">
                                    <a href="{{ url('/?order_by=invoice_number&sort='.$sort) }}">
                                        <i class="fa {{ $invoice_icon }}"></i>
                                    </a>
                                </div>
                                <div class="clearfix"></div>
                            </th>
                            <th>Revision</th>
                            @if(Auth::user()->role_id != 4)
                                <th>Roll Forward</th>
                            @endif
                        </tr>
                    </thead>
                    <form id="select-job-form" class="form-horizontal" method="get" action="{{url('/roll-forward')}}">
                        {{ csrf_field() }}
                    </form>
                    <tbody>
                    <?php
                        foreach($jobsMap as $job) {
                            echo '<tr class="job_rows '. (session('job_id') == $job['job_number'] ? 'success' : '') .'">';
                            echo '<td class="action-cell text-center"><input type="checkbox" name="action" class="job_checkbox" value="'.$job['job_number'].'"></td>';
                            echo '<td class = "job_number">'.$job['job_number'].'</td>';
                            echo '<td>'.$job['purchase_order'].'</td>';
                            echo '<td>'.$job['project_name'].'</td>';// Project Name
                            echo '<td class="text-center">'.($job['company_id'] == 43 || $job['company_id'] == 1211 ? 'B' : '').'</td>';
                            echo '<td>'. $job['total_pages_submitted'] .'</td>';
                            echo '<td>'. number_format($job['price'],2) .'</td>';// Pricing
                            $taxonomy = isset($job['taxonomy']) ? $job['taxonomy'] : "";
                            $companyName = isset($job['company_name']) ? $job['company_name'] : "";
                            echo '<td>'.$companyName.'</td>';// Company
                            echo '<td>'.date_format($job['due_date'], 'd-m-Y').'</td>';// Due date

                            $downloadValues = array($job['job_number'], $job['company_id']);
                            $statusId = isset($job['status_id']) ? $job['status_id'] : "";
                            $statusVendor = isset($job['status']) ? $job['status'] : "";

                            echo '<form id="download-invoice-form" class="form-horizontal" method="GET" action="/download">';
                    ?>

                    {{ csrf_field() }}

                    <?php
                        $invoice_no = strlen($job['invoice_number']) > 15 ? substr($job['invoice_number'],0,15).'...' : $job['invoice_number'];
                            echo '<td class="action-cell text-center">';
                                if($statusVendor == 'Pending Sign-off')
                                {   
                                    echo ' <button type="button" '.( $statusId != 6 ? 'disabled' : '' ).' data-toggle="tooltip" title="Download Finished File" data-placement="top" data-trigger="hover" class="styled_btn download-finished"><i class="fa fa-download"></i></button>';
                                }
                                else
                                {
                                    echo $statusVendor;
                                }
                            echo '</td>';
                            echo '</form>';
                            if($job['is_invoiced'] == 1)
                            {
                                 echo '<td><a data-toggle="tooltip" data-placement="top" data-trigger="hover" title="'.$job['invoice_number'].'.xls" href="'. url('download-invoice/'.$job['invoice_number'].'/'.$job['job_number']).'">'.  $invoice_no  .'</a></td>';
                            }
                            else
                            {
                                // echo '<td>'. $job['invoice_number'] .'</td>';
                                echo '<td></td>';
                            }
                            echo '<td class="action-cell text-center">';
                            echo '<button type="button" data-toggle="tooltip" title="Upload Revisions" data-placement="top" data-trigger="hover" class="styled_btn upload-revision"><i class="fa fa-upload"></i></button>';
                            echo '</td>';

                            if(Auth::user()->role_id != 4):
                            // echo '<td class="action-cell"><input class="roll-forward" type="submit" value="'.$job['job_number'].'" /></td>';
                                echo '<td class="action-cell"><button data-toggle="tooltip" title="Roll Forward job #'.$job['job_number'].'" data-placement="top" data-trigger="hover" class="styled_btn roll-forward" type="submit" value="'.$job['job_number'].'" /><i class="fa fa-forward"></i></button></td>';
                            endif;
                            echo '</tr>';
                        }
                    ?>
                    </tbody>
                    <div id="snackbar">Generating Invoice...</div>
                </table>
                @elseif(Auth::user()->role_id == 4)
                    <table class="table table-condensed table-hover table-striped">
                    <thead>
                        <tr class="dark">
                            <th>
                                <div class="pull-left">
                                    Job # 
                                </div>
                                <div class="pull-right">
                                    <a href="{{ url('/?order_by=id&sort='.$sort) }}">
                                        <i class="fa {{ $job_no_icon }}"></i>
                                    </a>
                                </div>
                                <div class="clearfix"></div>
                            </th>
                            <th>
                                Project Name
                                <div class="pull-right">
                                    <a href="{{ url('/?order_by=project_name&sort='.$sort) }}">
                                        <i class="fa {{ $project_icon }}"></i>
                                    </a>
                                </div>
                                <div class="clearfix"></div>
                            </th>
                             <th class="text-center">
                                Pricing Reference
                            </th>
                            <th>
                                Pages
                                <div class="pull-right">
                                    <a href="{{ url('/?order_by=total_pages_submitted&sort='.$sort) }}">
                                        <i class="fa {{  $pages_icon }}"></i>
                                    </a>
                                </div>
                                <div class="clearfix"></div>
                            </th>
                            <th>
                                Accounting Standards
                            </th>
                            <th>
                                Due Date
                                <div class="pull-right">
                                    <a href="{{ url('/?order_by=due_date&sort='.$sort) }}">
                                        <i class="fa {{ $due_date_icon }}"></i>
                                    </a>
                                </div>
                                <div class="clearfix"></div>
                            </th>
                            <th>
                                Status
                            </th>
                            
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        foreach($jobsMap as $job) {
                            echo '<tr class="job_rows '. (session('job_id') == $job['job_number'] ? 'success' : '') .'">';
                            echo '<td class = "job_number">'.$job['job_number'].'</td>';
                            echo '<td>'.$job['project_name'].'</td>';// Project Name
                            echo '<td class="text-center">'.($job['company_id'] == 43 || $job['company_id'] == 1211 ? 'B' : '').'</td>';
                            echo '<td>'. $job['total_pages_submitted'] .'</td>';
                            $taxonomy = isset($job['taxonomy']) ? $job['taxonomy'] : "";
                            echo '<td>'.$taxonomy.'</td>'; // Accounting Standard?
                            echo '<td>'.date_format($job['due_date'], 'd-m-Y').'</td>';// Due date

                            $downloadValues = array($job['job_number'], $job['company_id']);
                            $statusId = isset($job['status_id']) ? $job['status_id'] : "";
                            $statusVendor = isset($job['status']) ? $job['status'] : "";
                            echo '<td>';
                            echo $statusVendor;
                            echo '</td>';
                        }
                    ?>
                    </tbody>
                    <div id="snackbar">Generating Invoice...</div>
                </table>
                @else
                <table class="table table-condensed table-hover table-striped">
                    <thead>
                        <tr class="dark">
                            <th>
                                <div class="pull-left">
                                    Job # 
                                </div>
                                <div class="pull-right">
                                    <a href="{{ url('/?order_by=id&sort='.$sort) }}">
                                        <i class="fa {{ $job_no_icon }}"></i>
                                    </a>
                                </div>
                                <div class="clearfix"></div>
                            </th>
                            <th>
                                Project Name
                                <div class="pull-right">
                                    <a href="{{ url('/?order_by=project_name&sort='.$sort) }}">
                                        <i class="fa {{ $project_icon }}"></i>
                                    </a>
                                </div>
                                <div class="clearfix"></div>
                            </th>
                            <th>
                                Pricing
                                <div class="pull-right">
                                    <a href="{{ url('/?order_by=computed_price&sort='.$sort) }}">
                                        <i class="fa {{  $pricing_icon }}"></i>
                                    </a>
                                </div>
                                <div class="clearfix"></div>
                            </th>
                            <th>
                                Due Date
                                <div class="pull-right">
                                    <a href="{{ url('/?order_by=due_date&sort='.$sort) }}">
                                        <i class="fa {{ $due_date_icon }}"></i>
                                    </a>
                                </div>
                                <div class="clearfix"></div>
                            </th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Revision</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <form id="select-job-form" class="form-horizontal" method="get" action="{{url('/roll-forward')}}">
                        {{ csrf_field() }}
                    </form>
                    <tbody>
                    <?php
                        foreach($jobsMap as $job) {
                            echo '<tr class="job_rows '. (session('job_id') == $job['job_number'] ? 'success' : '') .'">';
                            echo '<td class = "job_number">'.$job['job_number'].'</td>';
                            echo '<td>'.$job['project_name'].'</td>';// Project Name
                            echo '<td>'. number_format($job['price'],2) .'</td>';// Pricing
                            $taxonomy = isset($job['taxonomy']) ? $job['taxonomy'] : "";
                            echo '<td>'.date_format($job['due_date'], 'd-m-Y').'</td>';// Due date

                            $downloadValues = array($job['job_number'], $job['company_id']);
                            $statusId = isset($job['status_id']) ? $job['status_id'] : "";
                            $statusVendor = isset($job['status']) ? $job['status'] : "";

                    ?>

                    {{ csrf_field() }}

                    <?php
                        $invoice_no = strlen($job['invoice_number']) > 15 ? substr($job['invoice_number'],0,15).'...' : $job['invoice_number'];

                                echo '<td class="action-cell text-center">';
                                if($statusVendor == 'Pending Sign-off')
                                {   
                                    echo ' <button type="button" '.( $statusId != 6 ? 'disabled' : '' ).' data-toggle="tooltip" title="Download Finished File" data-placement="top" data-trigger="hover" class="styled_btn download-finished"><i class="fa fa-download"></i></button>';
                                }
                                else
                                {
                                    echo $statusVendor;
                                }
                                // echo '<input name="job_number" class="download" type="image" src="img/download_completed.png" value="'.$job['job_number'].'"/>';
                                echo '</td>';
                            echo '<td class="action-cell text-center">';
                            echo '<button type="button" data-toggle="tooltip" title="Upload Revisions" data-placement="top" data-trigger="hover" class="styled_btn upload-revision"><i class="fa fa-upload"></i></button>';
                            echo '</td>';
                            echo '<td class="action-cell text-center">';
                           
                            echo '<button data-toggle="tooltip" title="Roll Forward job #'.$job['job_number'].'" data-placement="top" data-trigger="hover" class="styled_btn roll-forward" type="submit" value="'.$job['job_number'].'" ><i class="fa fa-forward"></i></button>';

                                 echo ' <button type="button" '. ($statusId == 6 ? '' : 'disabled') .' data-toggle="tooltip" title="Mark job as complete" data-placement="top" data-trigger="hover" data-job="'.$job['job_number'].'" class="styled_btn marked_complete"><i class="fa fa-clipboard-check"></i></button>';
                            echo '</td>
                                    </tr>';
                        }
                    ?>
                    </tbody>
                    <div id="snackbar">Generating Invoice...</div>
                </table>
                @endif
            </div>
            <div>
                <div class="pull-left">
                    Displaying {{ $jobs->firstItem() }} to {{ $jobs->lastItem() }} of {{ $jobs->total() }} Records(s)
                </div>
                <div class="pull-right">
                     @if(isset($jobs))
                        {{ $jobs->links() }}
                    @endif
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>

    <script>

        $(document).ready(function() {
            $('.marked_complete').click(function(){
                var that = $(this);
                var job_id = that.attr('data-job');
                var action = 'Sign Off (Job Completed)';
                var _token = '{{ csrf_token() }}';
                $.post(URL+'update-job-status',{'_token':_token,'action':action,'job_id': job_id,'vendor':0})
                    .done(function(result){
                        // console.log(result)
                        alertModal('Job Status','Update Successful.','reload');
                    });
            });

            $('.download-finished').click(function(){
                var that = $(this);
                var job = that.closest('tr').find('.job_number').text();
                console.log(URL+'get-job-file/'+job);
                $.get(URL+'get-job-file/'+job)
                    .done(function(result){
                        if(result != '')
                        {
                            var data = $.parseJSON(result);
                            if(data.length > 0)
                            {
                                for(var i = 0;i < data.length; i++)
                                {
                                    var x = data[i];
                                    
                                    $('#converted-'+x.tax_computed).removeClass('hidden').attr('data-job',x.job_id).attr('data-file',x.server_filename).attr('data-type',x.type);
                                    $('#review-'+x.tax_computed).removeClass('hidden').attr('href',URL+'getxbrl-file/'+x.job_id+'/'+x.type+'/'+x.server_filename+'/read').attr('target','_blank');
                                }
                            }
                        }
                        $('#finished-file-modal').modal('show');
                    })
            });

            $('.download-output').click(function(){
                var that = $(this);
                var job_id = that.attr('data-job');
                var file_name = that.attr('data-file');
                var source_type = that.attr('data-type');
                window.location = URL+'download-source/'+job_id+'/'+source_type+'/'+file_name;
                return false;
            });

            $('.upload-revision').click(function(){
                var that = $(this);

                var job_id = that.closest('tr').find('.job_number').text();
                $('.job_id').val(job_id);
                $('#quick-upload-modal').modal('show');
            });

            $(".quick-file").change(function(){
                var that = $(this);
                if (this.files && this.files[0]) {

                    var fileName = that.val().replace(/.*[\/\\]/, '');
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('.quick-content').val(e.target.result);
                        $('.quick-name').val(fileName);
                        
                    }
                    console.log("File clean:" + fileName);
                    reader.readAsDataURL(this.files[0]);

                }
            });
            
            $('#quick-source-form').submit(function(){
                var data = new FormData(this);
                $('.quick-submit').text('Uploading...').prop('disabled',true);
                $.ajax({
                        url:URL+'quick-upload',
                        type:'post',
                        data:data,
                        cache:false,
                        contentType: false,
                        processData: false,
                        success:function(result){
                            alertModal('Upload Status','Uploaded successfully.','reload');
                        },
                        fail:function(){
                            $('.quick-submit').text('Submit').prop('disabled',false);
                            alertModal('Upload Status','Uploading failed. Please try again','');
                        }
                });
                return false;
            });

            $('[data-toggle="tooltip"]').tooltip(); 

            $('#chk_all').unbind('click');
            $('#chk_all').bind('click',function(){
                var that = $(this);

                if(that.is(':checked') == true)
                {
                    that.closest('table').find('tbody tr').find('td:nth-child(1) input[type="checkbox"]').not(':checked').click();
                }
                else
                {
                    that.closest('table').find('tbody tr').find('td:nth-child(1) input[type="checkbox"]:checked').click();
                }

            });

            $('.roll_forward_previous').click(function(){
                var that = $(this);
                $.get(URL+'roll-forward-selection')
                    .done(function(result){
                        $('.modal-dialog').html(result);
                        $('#alert-modal-lg').modal('show');
                    });
                 
            });

            $('#export-csv-jobs').click(function(){
                var that = $(this);
                var link = that.attr('href');
                $.get(URL+'filter-export-jobs')
                    .done(function(result){
                        $('.modal-dialog').html(result);
                        $('#alert-modal-sm').modal('show');
                    });
                return false;
            });

            $('#job-action').change(function(){
                var that = $(this);
                if(that.val() != 'Signed Off Complete')
                {

                    if($('#vendor-selection option').length < 1)
                    {
                        var str = '';
                        $.get('get-vendors')
                            .done(function(result){
                                if(result.length > 0)
                                {
                                    var data = $.parseJSON(result);
                                    $.each(data,function(i,x){
                                        str += '<option value="'+x.id+'">'+x.vendor_name+'</option>';
                                    });
                                    $('#vendor-selection').html(str);
                                }
                            });
                    }
                    $('.vendor_row').removeClass('hidden');
                }
                else
                {
                    $('.vendor_row').addClass('hidden');
                }
            });

            $('#status-update-btn').click(function(){
                var that = $(this);
                var action = $('#job-action').val();
                var _token = '{{ csrf_token() }}';
                var vendor =  $('#vendor-selection').val();
                if($('.job_checkbox:checked').length > 0)
                {
                    var flag = 0;
                    vendor = vendor != null ? vendor : 0;
                    $('.job_checkbox:checked').each(function(){
                        var job = $(this).val();
                        $.post(URL+'update-job-status',{'_token':_token,'action':action,'job_id': job,'vendor':vendor})
                            .done(function(result){
                                // console.log(result);
                                alertModal('Job Status','Update Successful.','reload');
                            });
                           flag++;
                    });
                    
                    // location.reload();
                }
               
                return false;
            });

            $('.job_rows td').click(function(){
                var that = $(this);
                if(!that.hasClass('action-cell'))
                {
                    var jobId = that.parent('tr').find('.job_number').text();
                    window.location = URL+'edit-job/'+jobId+'/';
                }
        
            });

            handleSearchInputVisibility();

            $("#search-by-selector").on('change', function () {
                handleSearchInputVisibility();
            });

            $("#status-selector").on('change', function () {
               $(".help-block").remove();
            });

            $("#query-selector").on('change', function () {
                $(".help-block").remove();
                handleQueryInputVisibility()
            });

            $(".roll-forward").click(function(e){

                var id = $(this).val();

                var input = $("<input>").attr("type", "hidden")
                    .attr("name", "id")
                    .val(id);

                $("#select-job-form").append(input).submit();

                e.preventDefault();
            });

            function handleSearchInputVisibility() {

                var selected = $("#search-by-selector").find(":selected").val();

                if (selected == "1") {
                    $("#search-by-status-tr select").prop('disabled',false);
                    $("#search-by-query-tr select").prop('disabled',true);
                     $("#search-by-status-tr input").prop('disabled',false);
                    $("#search-by-query-tr input").prop('disabled',true);

                    $("#search-by-status-tr").css('display','block');
                    $("#search-by-query-tr").css('display','none');
                } else if (selected == "2") {
                    $("#search-by-status-tr select").prop('disabled',true);
                    $("#search-by-query-tr select").prop('disabled',false);
                    $("#search-by-status-tr input").prop('disabled',true);
                    $("#search-by-query-tr input").prop('disabled',false);

                    $("#search-by-status-tr").css('display','none');
                    $("#search-by-query-tr").css('display','block');
                    handleQueryInputVisibility();
                } else {
                    $("#search-by-status-tr select").prop('disabled',true);
                    $("#search-by-query-tr select").prop('disabled',true);
                    $("#search-by-status-tr input").prop('disabled',true);
                    $("#search-by-query-tr input").prop('disabled',true);


                    $("#search-by-status-tr").prop('disabled',true).css('display','none');
                    $("#search-by-query-tr").prop('disabled',true).css('display','none');
                }
            }

            function handleQueryInputVisibility() {

                var selected = $("#query-selector").find(":selected").val();

                if (selected == 'id' || selected == 'company_name' || selected == 'project_name') {
                    $("#query-month-input").prop('disabled',true).hide();
                    $("#query-date-input").prop('disabled',true).hide();
                    $("#query-input").prop('disabled',false).show();
                } else if (selected == 'by_month') {
                    $("#query-month-input").prop('disabled',false).show();
                    $("#query-date-input").prop('disabled',true).hide();
                    $("#query-input").prop('disabled',true).hide();
                } else {
                    $("#query-month-input").prop('disabled',true).hide();
                    $("#query-date-input").prop('disabled',false).show();
                    $("#query-input").prop('disabled',true).hide();
                }
            }

        });
    </script>

    <div id="quick-upload-modal" class="modal fade" role="dialog">
      <div class="modal-dialog modal-sm">
      <form id="quick-source-form" enctype="multipart/form-data" method="post" action="{{url('quick-upload')}}">
            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                <a type="button" class="close" data-dismiss="modal">&times;</a>
                <h4 class="modal-title">Upload Revision File</h4>
              </div>
              <div class="modal-body">
                {{ csrf_field() }}
                <div class="form-group hidden">
                    <label>Please select a file to upload</label>
                    <select class="form-control quick-type-list " name="file_type" required>
                        <option value="4">Revision Files</option>
                    </select>
                    <input type="hidden" name="source_id" value="0" class="source_id">
                    <input type="hidden" name="job_id" value="0" class="job_id">
                    <input type="hidden" name="file_content" class="quick-content">
                    <input type="hidden" name="file_name" class="quick-name">
                </div>
                <div class="form-group computation-group hidden">
                    <label>Is this a tax computation file?</label>
                    <br/>
                    <label><input type="radio" name="tax_computation" class="quick-computation-tax" value="1" required/> Yes</label>
                    <label><input type="radio" checked name="tax_computation" class="quick-computation-tax" value="0" required/> No</label>
                </div>
                <div class="form-group hidden">
                    <label>No. of Pages</label>
                    <input type="number" class="form-control" name="pages" value="0" required>
                </div>
                <div class="form-group">
                    <label>Please select a file to upload :</label>
                    <input type="file" class="form-control quick-file" name="uploaded_file" required>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn" data-dismiss="modal" data-backdrop="false">Close</button>
                <button type="submit" class="btn btn-default quick-submit">Submit</button>
              </div>
            </div>
        </form>
      </div>
    </div>


    <div id="finished-file-modal" class="modal fade" role="dialog">
      <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                <a type="button" class="close" data-dismiss="modal">&times;</a>
                <h4 class="modal-title">Your converted documents</h4>
              </div>
              <div class="modal-body">
                <div class="form-group">
                    <div class="col-xs-4">
                        Statutory Accounts
                    </div>
                    <div class="col-xs-2">
                        <a id="converted-0" href="#" class="hidden download-output">Get File</a>
                    </div>
                    <div class="col-xs-6">
                        Includes HMRC Compliant iXBRL(HTML) file and Excel Comment Report
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="form-group">
                    <div class="col-xs-4">
                        Tax Computation
                    </div>
                    <div class="col-xs-2">
                        <a id="converted-1" href="#" class="hidden download-output">Get File</a>
                    </div>
                    <div class="col-xs-6">
                        Includes HMRC Compliant iXBRL(HTML) file and Excel Comment Report
                    </div>
                    <div class="clearfix"></div>
                </div>
                <hr/>
                <div class="form-group text-center">
                    <h4>File showing iXBRL tags for review purposes</h4>
                </div>
                <div class="form-group">
                    <div class="col-xs-4">
                        Marked up review file for Stautory Accounts
                    </div>
                    <div class="col-xs-2">
                        <a id="review-0" class="hidden">View File</a>
                    </div>
                    <div class="col-xs-6">
                        This file is for your convenience and not for submission to HMRC
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="form-group">
                    <div class="col-xs-4">
                        Marked up review for Tax Computation
                    </div>
                    <div class="col-xs-2">
                        <a id="review-1" class="hidden">View File</a>
                    </div>
                    <div class="col-xs-6">
                        This file is for your convenience and not for submission to HMRC
                    </div>
                    <div class="clearfix"></div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn" data-dismiss="modal" data-backdrop="false">Close</button>
              </div>
            </div>
      </div>
    </div>

@endsection