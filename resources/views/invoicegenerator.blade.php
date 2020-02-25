@extends('layouts.frontsite')

@section('content')

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>

    <?php
        $searchedJobMap = session('searchedJobMap');
        $companyId = session('companyId');
    ?>

    <div class="wrapper">

        <div class="page-holder">

            <h2 class="content-title">Invoice Generator</h2>

            <div class="clear"></div>

            <div style="padding-left:0">
                
                <table id="search">

                    <form id="job-search-form" action="{{route('invoice-generator-jobs')}}" method="POST">
                        {{ csrf_field() }}

                        <tr>

                            <td>
                                Company:
                            </td>

                            <td>    
                                <div class="{{ $errors->has('company') ? ' has-error' : '' }}">

                                <select class="form-control" name="company" required autofocus>

                                        <option value="">--Please Select--</option>
        
                                        <?php
        
                                            foreach($companies as $company) {
        
                                                if ($company->id == old('company')) {
                                                    echo '<option value="'.$company->id.'" selected>'.$company->name.'</option>';
                                                } else {
                                                    echo '<option value="'.$company->id.'">'.$company->name.'</option>';
                                                }
        
                                            }
        
                                        ?>
                
                                </select>

                                @if ($errors->has('company'))
                                <span class="help-block">
                                <strong>{{ $errors->first('company') }}</strong>
                                </span>
                                @endif

                                </div>

                            </td>

                            <td>
                                Start Date:
                            </td>
                                
                            <td class="date_input">
                                <div class="{{ $errors->has('start_date') ? ' has-error' : '' }}">
                                <input type="text" class="datepicker form-control" name="start_date" value="{{old('start_date')}}"/>

                                @if ($errors->has('start_date'))
                                <span class="help-block">
                                <strong>{{ $errors->first('start_date') }}</strong>
                                </span>
                                @endif

                                </div>
                            </td>

                            <td>
                                End Date:
                            </td> 
    
                            <td class="date_input">
                                <div class="{{ $errors->has('end_date') ? ' has-error' : '' }}">
                                <input type="text" class="datepicker form-control" name="end_date" value="{{old('end_date')}}"/>

                                @if ($errors->has('end_date'))
                                <span class="help-block">
                                <strong>{{ $errors->first('end_date') }}</strong>
                                </span>
                                @endif

                                </div>
                            </td>
    
                            <td>
                                <button id="job-search-btn" type="submit" class="styled_btn">
                                    Search
                                </button>
                            </td>
    
                        </tr>
            
                    </form>

                    <form id="job-download-invoice-form" action="{{route('invoice-download-job-xlsx')}}" method="GET" >
                        <input type="hidden" name="filename" id="job-download-invoice-input"/>
                    </form>

                   

                    <!-- <form id="job-status-update-form" action="{{route('invoice-generator-jobs-status')}}" method="POST">
                        {{ csrf_field() }}
            
                        <tr>
                        <td colspan = "4">
                        <table>    

                            <td>Batch Update:</td>

                            <td>
                                
                                <select class="form-control" name="status" required autofocus>

                                    <option value="">--Please Select--</option>
    
                                    <?php
    
                                        foreach($statuses as $status) {
    
                                            if ($status->id == old('status')) {
                                                echo '<option value="'.$status->id.'" selected>'.$status->name.'</option>';
                                            } else {
                                                echo '<option value="'.$status->id.'">'.$status->name.'</option>';
                                            }

                                        }
    
                                    ?>
    
                                </select>
                            </td>

                            <td>
                                <input id="job-status-update-input" type="hidden" name="jobs-status-update" />
                                <button id="job-status-update-btn" type="submit">
                                    Update
                                </button>
                            </td>

                        </table>
                        </td>
                        </tr>
                            
                    </form> -->

                </table>
                    
            </div>
             @if (!is_null($searchedJobMap) && !empty($searchedJobMap))
                <div class="form-group text-right">
                    <form id="job-generate-invoice-form" action="{{route('invoice-generate-job-xlsx')}}" method="POST">
                            {{ csrf_field() }}
                            <input id="company-generate-invoice-input" type="hidden" name="company-generate-invoice" value="{{$companyId}}"/>
                            <input id="job-generate-invoice-input" type="hidden" name="job-generate-invoice" />
                            <button id="job-generate-invoice-btn" type="submit" class="styled_btn">
                                Download
                            </button>
                            <button type="button" class="styled_btn" value="Reset" onclick="window.location='{{url("/invoice-generator")}}'">Reset</button>
                    </form>
                </div>
            @endif
            <div class="table-responsive">
                
                <table class="table table-condensed table-hover table-striped" id="jobs-table">

                    @if (!is_null($searchedJobMap) && !empty($searchedJobMap))
                    <thead>
                        <tr class="dark"> 
                            <td> <input type="checkbox" id="jobs-checkbox-all-input"></td>
                            <td>Job ID</td>
                            <td>Project Name</td>
                            <td>Client PO</td>
                            <td>Pricing</td>
                            <td>Company</td>
                            <td>Ordered By</td>
                            <td>Due Date</td>
                            <td>Billable Date</td>
                            <td>Vendor Status</td>
                        </tr>   
                    </thead>
                        <?php
                            foreach($searchedJobMap as $searchedJobMapItem) {
                                echo '<tr>';
                                echo '<td><input type="checkbox" id="'.$searchedJobMapItem['job_number'].'" class="jobs-checkbox-single"></td>';
                                echo '<td>'.$searchedJobMapItem['job_number'].'</td>';
                                echo '<td>'.$searchedJobMapItem['project_name'].'</td>';
                                echo '<td>'.$searchedJobMapItem['purchase_order'].'</td>';
                                echo '<td>'.$searchedJobMapItem['price'].'</td>';
                                echo '<td>'.$searchedJobMapItem['company'].'</td>';
                                echo '<td>'.$searchedJobMapItem['order_by'].'</td>';
                                echo '<td>'.date_format($searchedJobMapItem['due_date'], 'd-m-Y H:i:s').'</td>';
                                echo '<td>'.date_format( $searchedJobMapItem['billable_date'], 'd-m-Y H:i:s').'</td>';


                                if (isset($searchedJobMapItem['status']) && !empty($searchedJobMapItem['status'])) { 
                                    echo '<td>'.$searchedJobMapItem['status'].'</td>';
                                } else {
                                    echo '<td></td>';
                                }

                                echo '<tr>';
                            }

                        ?>
                    @elseif(old('company') != '')
                        <tr>
                            <td class="text-center">No records found. Please adjust your search criteria.</td>
                        </tr>
                    @endif

                </table>

            </div>

        </div>

        <div id="snackbar">Generating Invoice...</div>

        <div id="loading">
            <img alt="ServiceTrack" src="{{ url('public/img/loading.gif') }}">
        </div>

        <style>
            div#loading {
                position: fixed;
                padding-top: 15%;
                left: 0;
                bottom: 0;
                right: 0;
            }

            #loading {
                display:none;
                position:fixed;
                top:0; left:0; width:100%; height:100%;
                background:rgba(0,0,0,0.5);
                text-align:center
            }
        </style>

        <script>

            $(document).ready(function() {

                $("#job-search-btn").click(function (e) {
                    $("#loading").show();
                    $("#job-search-form").submit();
                    e.preventDefault();
                });
                
                 $("#jobs-checkbox-all-input").change(function() {
                    var checked = $(this).is(':checked');
                    $(".jobs-checkbox-single").prop('checked', checked);
                });

                $("#job-status-update-btn").click(function (e) {

                    var checkedJobIds = getSelectedJobs();
                    var form = $("#job-status-update-form");
                    var input = $("#job-status-update-input");

                    if (checkedJobIds.length > 0) {
                        input.val(JSON.stringify(checkedJobIds));
                    } else {
                        input.removeAttr('value');
                    }

                    $.ajax({
                        type: form.attr('method'),
                        url: form.attr('action'),
                        data: form.serialize(),

                        success: function (data) {},
                        error: function (xhr, request, error) {},
                    });

                    e.preventDefault();
                })

                $("#job-generate-invoice-btn").click(function (e) {

                    var x = document.getElementById("snackbar");
                    x.className = "show";

                    var checkedJobIds = getSelectedJobs();

                    var form = $("#job-generate-invoice-form");
                    var generateInput = $("#job-generate-invoice-input");
                    var downloadInput = $("#job-download-invoice-input");


                    if (checkedJobIds.length > 0) { 
                        generateInput.val(JSON.stringify(checkedJobIds));
                    } else {
                        alert("In order to generate invoice you must select at least one from the job search results.");
                        var x = document.getElementById("snackbar");
                        x.className = x.className.replace("show", "");
                        generateInput.removeAttr('value');
                        downloadInput.removeAttr('value');
                        e.preventDefault();
                        return;
                    }

                    
                    $.ajax({
                        type: form.attr('method'),
                        url: form.attr('action'),
                        data: form.serialize(),

                        success: function (data) {      
                            console.log(data)
                            var x = document.getElementById("snackbar");
                            x.className = x.className.replace("show", "");

                            if (!data['error']) {
                                var input = $("#job-download-invoice-input");
                                input.val(data['filename']);
                                $('#job-download-invoice-form').submit();

                            } else {
                                alert(data['error']);
                            }
                        },

                        error: function (xhr, request, error) {
                            var x = document.getElementById("snackbar");
                            x.className = x.className.replace("show", "");
                            console.log('Error log:' +xhr);
                        } 
                    }); 
                   
                    e.preventDefault();
                });

                function getSelectedJobs() {

                    var checkedJobIds = [];

                    $("#jobs-table [type='checkbox']").each(function(counter, checkbox) {

                        if (counter > 0) {

                            var checked = $(checkbox).prop('checked');
                            var checkedJobId = $(checkbox).prop('id');

                            if (checked == true) {
                                checkedJobIds.push(checkedJobId);
                            }
                        }
                    });

                    return checkedJobIds;
                }
            });

        </script>

    </div>

@endsection