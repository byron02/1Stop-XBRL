@extends('layouts.frontsite')

@section('content')
    <div class="wrapper">
        <div class="page-holder">
            <h2 class="content-title">Jobs</h2>
            <div id="dashboard_container">
                <div id="cont" style="">
                    <div class="filter-status" filter-value="2" title="New order">
                        <span class="name">New order</span><span class="job_count">0</span></div>
                    <div class="filter-status" filter-value="3" title="In Progress">
                        <span class="name">In Progress</span><span class="job_count">17</span></div>
                    <div class="filter-status" filter-value="4" title="In Revision">
                        <span class="name">In Revision</span><span class="job_count">0</span></div>
                    <div class="filter-status" filter-value="7" title="Revisions Submitted">
                        <span class="name">Revisions Submitted</span><span class="job_count">0</span></div>
                </div>
            </div>
            <div class="add-btn">
                <a href="{{url('/add-jobs')}}"
                   class="styled_btn submit_btn" style="text-decoration:none">
            <span><img src="http://servicetrack.1stopxbrl.co.uk/assets/images/plus.png"
                       class="btn_icon">ADD JOB</span></a>
            </div>
            <div class="clear"></div>
            <div style="padding-left:0">
                <table id="search">
                    <tr>
                        <td>Search by</td>
                        <td>
                            <select>
                                <option>--Please Select--</option>
                                <option>Status</option>
                                <option>Query</option>
                            </select>
                        </td>
                        <td>Select Action</td>
                        <td>
                            <select>
                                <option>Complete for client sign of</option>
                                <option>Assign to vendor</option>
                                <option>Assign to back vendor</option>

                            </select>
                        </td>
                        <td><a href="#" class="styled_btn submit_btn" style="padding:4px" id="batch_update">Update Status</a></td>

                    </tr>

                </table>
            </div>
            <div style="padding-right:0">
                <div class="pagination-container">

                    @if(isset($jobs))
                        {{ $jobs->links() }}
                    @endif

                </div>
            </div>
            <div class="clear"></div>
            <div class="datagrid_wrap">
                <table class="job_list">
                        <td class="datagrid_header" style="width:90px">Job No.<a href="http://servicetrack.1stopxbrl.co.uk/csr/jobs/index/null/null/jobs.id/asc/0"><img src="http://servicetrack.1stopxbrl.co.uk/assets/images/icon_arrow.png"></a></td>
                        <td class="datagrid_header">Project Name <a href="http://servicetrack.1stopxbrl.co.uk/csr/jobs/index/null/null/project_name/asc/0"><img src="http://servicetrack.1stopxbrl.co.uk/assets/images/icon_arrow.png"></a></td>
                        <td class="datagrid_header" style="width:75px">Pricing <a href="http://servicetrack.1stopxbrl.co.uk/csr/jobs/index/null/null/computed_price/asc/0"><img src="http://servicetrack.1stopxbrl.co.uk/assets/images/icon_arrow.png"></a></td>
                        <td class="datagrid_header" style="width:75px">Work Type <a href="http://servicetrack.1stopxbrl.co.uk/csr/jobs/index/null/null/computed_price/asc/0"><img src="http://servicetrack.1stopxbrl.co.uk/assets/images/icon_arrow.png"></a></td>
                        <td class="datagrid_header">Due Date <a href="http://servicetrack.1stopxbrl.co.uk/csr/jobs/index/null/null/jobs.due_date/asc/0"><img src="http://servicetrack.1stopxbrl.co.uk/assets/images/icon_arrow.png"></a></td>
                        <td class="datagrid_header">Status<a href="http://servicetrack.1stopxbrl.co.uk/csr/jobs/index/null/null/jobs.due_date/asc/0"><img src="http://servicetrack.1stopxbrl.co.uk/assets/images/icon_arrow.png"></a></td>
                        <td class="datagrid_header">Finished Files<a href="http://servicetrack.1stopxbrl.co.uk/csr/jobs/index/null/null/jobs.due_date/asc/0"><img src="http://servicetrack.1stopxbrl.co.uk/assets/images/icon_arrow.png"></a></td>
                        <td class="datagrid_header">Upload Revisions<a href="http://servicetrack.1stopxbrl.co.uk/csr/jobs/index/null/null/jobs.due_date/asc/0"><img src="http://servicetrack.1stopxbrl.co.uk/assets/images/icon_arrow.png"></a></td>
                        <td class="datagrid_header">Job Completed?<a href="http://servicetrack.1stopxbrl.co.uk/csr/jobs/index/null/null/jobs.due_date/asc/0"><img src="http://servicetrack.1stopxbrl.co.uk/assets/images/icon_arrow.png"></a></td>
                    </tr>

                    <form id="select-job-form" class="form-horizontal" method="POST" action="{{url('/roll-forward')}}">
                        {{ csrf_field() }}
                    </form>

                    <form id="job-download-invoice-form" action="{{route('invoice-download-job-xlsx')}}" method="GET" >
                        <input type="hidden" name="filename" id="job-download-invoice-input"/>
                    </form>

                    <form id="job-generate-invoice-form" action="{{route('invoice-generate-job-xlsx')}}" method="POST">
                        {{ csrf_field() }}

                        <input id="job-generate-invoice-input" type="hidden" name="job-generate-invoice" />
                        <input id="company-generate-invoice-input" type="hidden" name="company-generate-invoice" />

                        <?php
                            foreach($jobsMap as $job) {
                                echo '<tr>';
                                echo '<td id = "job_number">'.$job['job_number'].'</td>';
                                echo '<td>'.$job['project_name'].'</td>';// Project Name
                                echo '<td>'.$job['price'].'</td>';// Pricing
                                echo '<td></td>';// Work Type
                                echo '<td>'.$job['due_date'].'</td>';// Due date

                                $statusVendor = isset($job['status']) ? $job['status'] : "";
                                echo '<td>'.$statusVendor.'</td>';// status
                                 //Finished Files
                                echo '<td class = "grid_content_center">';
                                $statusId = isset($job['status_id']) ? $job['status_id'] : "";

                                $downloadValues = array($job['job_number'], $job['company_id']);
                                if ($statusId == 8) {
                                    echo '<input class="download" type="image" src="img/download_completed.png" value="'.json_encode($downloadValues).'"/>';
                                }
                                echo '</td>';
                                echo '<td></td>';// Upload Revisions
                                $statusVendor = isset($job['status']) ? $job['status'] : "";
                                echo '<td>'.$statusVendor.'</td>';// Job Completed?



                                echo '<td></td>';// Payment
                                echo '<td></td>';// Invoice Download

                                echo '</tr>';
                            }
                        ?>
                        
                    </form>

                    <div id="snackbar">Generating Invoice...</div>
                    
                    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
                    <script>

                        $(document).ready(function() {
                            
                            $(".download").click(function(e) {

                                var x = document.getElementById("snackbar");
                                x.className = "show";

                                var input = $(this);
                                var values = JSON.parse(input.val());
                             
                                if (values[0] != null && values[1] != null) {

                                    var form = $("#job-generate-invoice-form");
                                    var jobGenerateInput = $("#job-generate-invoice-input");
                                    var companyGenerateInput = $("#company-generate-invoice-input");

                                    var jobId = [values[0]];
                                    jobGenerateInput.val(JSON.stringify(jobId));
                                    companyGenerateInput.val(values[1]);

                                    console.log(jobGenerateInput);
                                    console.log(companyGenerateInput);
                                    console.log(form);
                                    
                                    $.ajax({
                                        type: form.attr('method'),
                                        url: form.attr('action'),
                                        data: form.serialize(),
                
                                        success: function (data) {    

                                            var x = document.getElementById("snackbar");
                                            x.className = x.className.replace("show", "");
                
                                            var input = $("#job-download-invoice-input");
                                            input.val(data['filename']);
                                            $('#job-download-invoice-form').submit();
                                        },
                
                                        error: function (xhr, request, error) {
                                            
                                            var x = document.getElementById("snackbar");
                                            x.className = x.className.replace("show", "");
                                        } 
                                    });

                                }
                                
                                e.preventDefault();
                            });

                            /**$('.job_list tr').click(function(){     
                                var id = $(this).find("td[id='job_number']").text();
                                var input = $("<input>").attr("type", "hidden")
                                    .attr("name", "id")
                                    .val(id);
                                $("#select-job-form").append(input).submit();
                            });**/


                            $(".roll-forward").click(function(e){
                                var id = $(this).val();
                                var input = $("<input>").attr("type", "hidden")
                                    .attr("name", "id")
                                    .val(id);
                                $("#select-job-form").append(input).submit();

                                e.preventDefault();
                            });
                        });
                    </script>
                    
                </table>
            </div>
        </div>
    </div>
@endsection