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
            <div class="form-group">
                <h2 class="content-title">Invoiced Jobs</h2>
                <div class="clearfix"></div>
            </div>
            <div class="form-group">
                <div class="pull-left">
                    <!-- <a id="fancybox-download-invoice" href="#invoice-content"> Download Invoices </a> | -->
                    <a class="download-modal"> Download Invoices </a> 
                    @if(Auth::user()->role_id == 4 || Auth::user()->role_id == 8)
                        | <a style="color:#000" href="{{route('accounting-sheet')}}">General Accounting Sheets </a>
                    @endif
                </div>
                <div class="pull-right">
                    <form class="form-inline">
                        <div class="form-group">
                            <label>Filter By : </label>
                            <select class="form-control" name="filter_by" id="filter_by">
                                <option {{ request('filter_by') == 'invoice_number' ? 'selected' : '' }}  value="invoice_number">Invoice Number</option>
                                <option {{ request('filter_by') == 'purchase_order' ? 'selected' : '' }} value="purchase_order">Client PO</option>
                                <option {{ request('filter_by') == 'project_name' ? 'selected' : '' }} value="project_name">Project Name</option>
                                <option {{ request('filter_by') == 'issued_to' ? 'selected' : '' }} value="issued_to">Issued To</option>
                                <option {{ request('filter_by') == 'company_name' ? 'selected' : '' }} value="company_name">Company Name</option>
                                <option {{ request('filter_by') == 'date' ? 'selected' : '' }} value="date">Date Range</option>
                            </select>
                            &nbsp;
                            <input type="text" class="form-control datepicker date_range hidden" value="{{ request('from_date') != '' ? request('from_date') :date('Y-m-01') }}" disabled placeholder="Start Date" name="from_date" value="{{ app('request')->input('from_date')}}">
                            <div class="input-group ">
                                <input type="text" class="form-control string_search" placeholder="Type your search here..." name="search" value="{{ app('request')->input('search')}}">
                                <input type="text" class="form-control datepicker date_range hidden" value="{{ request('to_date') != '' ? request('to_date') :date('Y-m-t') }}" disabled placeholder="End Date" name="to_date" value="{{ app('request')->input('to_date')}}">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-default">
                                        <span class="glyphicon glyphicon-search"></span> Search
                                    </button>
                                </span>
                            </div>
                            <div class="input-group hidden">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-default">
                                        <span class="glyphicon glyphicon-search"></span> Search
                                    </button>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="clearfix"></div>
            </div>
            @php
                $sort = request('sort') == 'asc' ? 'desc' : 'asc';
                $icon = array(
                                'nan' => 'fa-sort',
                                'asc' => 'fa-sort-up',
                                'desc' => 'fa-sort-down'
                            );

                $sort_by = request('order_by') != '' ? request('sort') : 'nan';
                $invoice_no_icon = request('order_by') == 'invoice_number' ? $icon[$sort_by] : 'fa-sort';
                $purchase_order_icon = request('order_by') == 'purchase_order' ? $icon[$sort_by] : 'fa-sort';
                $project_name_icon = request('order_by') == 'project_name' ? $icon[$sort_by] : 'fa-sort';
                $issued_icon = request('order_by') == 'users.first_name' ? $icon[$sort_by] : 'fa-sort';
                $companies_name_icon = request('order_by') == 'companies.name' ? $icon[$sort_by] : 'fa-sort';
                $date_imported_icon = request('order_by') == 'date_imported' ? $icon[$sort_by] : 'fa-sort';
            @endphp
            <div class="table-responsive">
                <table class="table table-condensed table-hover table-striped ">
                    <tr class="dark">
                        <td>
                            <div class="pull-left">
                                Invoice Number
                            </div>
                            <div class="pull-right">
                                <a href="{{ url('/invoices/?order_by=invoice_number&sort='.$sort) }}">
                                    <i class="fa {{ $invoice_no_icon }}"></i>
                                </a>
                            </div>
                            <div class="clearfix"></div>
                        </td>
                        <td>
                            <div class="pull-left">
                                Client PO
                            </div>
                            <div class="pull-right">
                                <a href="{{ url('/invoices/?order_by=purchase_order&sort='.$sort) }}">
                                    <i class="fa {{ $purchase_order_icon }}"></i>
                                </a>
                            </div>
                            <div class="clearfix"></div>
                        </td>
                        <td>
                            <div class="pull-left">
                                Project Name
                            </div>
                            <div class="pull-right">
                                <a href="{{ url('/invoices/?order_by=project_name&sort='.$sort) }}">
                                    <i class="fa {{ $project_name_icon }}"></i>
                                </a>
                            </div>
                            <div class="clearfix"></div>
                        </td>
                        <td>
                            <div class="pull-left">
                                Issued To
                            </div>
                            <div class="pull-right">
                                <a href="{{ url('/invoices/?order_by=users.first_name&sort='.$sort) }}">
                                    <i class="fa {{ $issued_icon }}"></i>
                                </a>
                            </div>
                            <div class="clearfix"></div>
                        </td>
                        <td>
                            <div class="pull-left">
                                Company Name
                            </div>
                            <div class="pull-right">
                                <a href="{{ url('/invoices/?order_by=companies.name&sort='.$sort) }}">
                                    <i class="fa {{ $companies_name_icon }}"></i>
                                </a>
                            </div>
                            <div class="clearfix"></div>
                        </td>
                        <td>
                            <div class="pull-left">
                                Date Created
                            </div>
                            <div class="pull-right">
                                <a href="{{ url('/invoices/?order_by=date_imported&sort='.$sort) }}">
                                    <i class="fa {{ $date_imported_icon }}"></i>
                                </a>
                            </div>
                            <div class="clearfix"></div>
                        </td>
                        <td class="text-center">Download</td>
                    </tr>
                    @if(isset($invoiceMap))
                        @foreach($invoiceMap as $invoice)
                            <tr>
                                <td>{{$invoice['invoice_number']}}</td>
                                <td>{{$invoice['client_po']}}</td>
                                <td>{{$invoice['project_name']}}</td>
                                <td>{{$invoice['issued_to']}}</td>
                                <td>{{$invoice['company_name']}}</td>
                                <td>{{ date('d-m-Y H:i:s',strtotime($invoice['date_imported'])) }}</td>
                                <form id="add-job-form" class="form-horizontal" method="GET" action="{{url('/download')}}">
                                    {{ csrf_field() }}
                                <td class="text-center">
                                    @if($invoice['is_invoiced'] == 1)
                                        <input name="full_file_path" alt="{{$invoice["file_full_path"]}}" value="{{$invoice["file_full_path"]}}" type="image" src="{{url('public/img/download_completed.png')}}"/> XLS
                                        <input name="file_name" value="{{$invoice["file_name"]}}" type="hidden" />
                                        |
                                            <a href="{{ url('downloadPDF/'.$invoice['invoice_number'].'/download/') }}" class="text-dark"><i class="glyphicon glyphicon-download-alt"></i> PDF</a>
                                        |
                                            <a href="{{ url('downloadPDF/'.$invoice['invoice_number'].'/view/') }}" target="_blank" class="text-dark"><i class="glyphicon glyphicon-file"></i> View</a>
                                    @endif
                                </td>
                                </form>
                            </tr>
                        @endforeach
                    @endif

                </table>
            </div>
            @if(isset($invoiceMap))
                <div class="pull-left">
                    Displaying {{ $invoices->firstItem() }} to {{ $invoices->lastItem() }} of {{ $invoices->total() }} Records(s)
                </div>
                <div class="pull-right">
                    {{ $invoices->links() }}
                </div>
                <div class="clearfix"></div>
            @endif


        </div>
    </div>

    <form id="download_zip_form" action="{{route('invoices-filter-download')}}" method="GET" >

        <input type="hidden" name="filename" id="download_zip"/>
    </form>

    <!-- download filter -->
    <div style="display:none" id="invoice-content">
        <select id="select_filter">
            <option>Select Filter</option>
            <option value="job_id">Job ID</option>
            <option value="invoice_range">Invoice Number Range</option>
            <option value="client">Client</option>
            <option value="date_range">Date Range</option>
        </select>

        <div id="content-choice">
            <!-- Job ID -->
            <div id="job_id" class="filter-content">
                <form action="{{route('invoices-filter-job-ids')}}" method="GET" >

                    <table>
                        <tr>
                            <td>ID</td>
                            <td><input  name="job_ids[]" type="text"/>

                                <span id="job_row_0" class="form-select-error">
                                </span>
                            </td>
                            <td>
                                <span class="minus">[ - ]</span>

                            </td>
                        </tr>



                    </table>

                    <a class="add styled_btn">Add Field</a>
                    <button type="submit">
                        Submit
                    </button>
                </form>

            </div>

            <!--Invoice Number Range-->
            <div id="invoice_range" class="filter-content">
                <form action="{{route('invoices-filter-invoice-range')}}" method="GET" >
                    <table>
                        <tr>
                            <td>Range Start</td>
                            <td>
                                <input name="invoice_start" type="text"/>
                                First four digits of invoice number only

                                <span id="help-block-invoice_number_start" class="form-select-error">
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>Range End</td>
                            <td>

                                <input name="invoice_end" type="text"/>
                                First four digits of invoice number only

                                <span id="help-block-invoice_number_end" class="form-select-error">
                                </span>
                            </td>
                        </tr>

                    </table>
                    <button type="submit">
                        Submit
                    </button>
                </form>
            </div>

            <!-- Client -->
            <div id="client" class="filter-content">
                <form action="{{route('invoices-filter-client')}}" method="GET">
                   <table>
                   <tr>
                       <td>Select Client</td>
                       <td>
                           <select class="form-control-2" name="company" required autofocus>
                               <option value="">--Please Select--</option>

                               <?php

                               foreach($companies as $company) {
                                   echo '<option value="'.$company['id'].'">'.$company['name'].'</option>';

                               }
                               ?>
                           </select>

                           <span id="help-block-client-company" class="form-select-error">
                       </td>
                   </tr>

                    <tr>
                        <td>From</td>
                        <td class="date_input">
                            <input type="text" class="datepicker" name="client_start_date"/>
                            <span id="help-block-client-start-date" class="form-select-error">
                        </td>
                    </tr>

                    <tr>
                        <td>To</td>
                        <td class="date_input"><input type="text" class="datepicker" name="client_end_date"/>
                            <span id="help-block-client-end-date" class="form-select-error">
                        </td>
                    </tr>
                   </table>


                    <button type="submit">
                        Submit
                    </button>
                </form>
            </div>

            {{--Date Range--}}
            <div id="date_range" class="filter-content">
                <form action="{{route('invoices-filter-date-range')}}" method="GET" >
                    <table>
                        <tr>
                            <td>From</td>
                            <td class="date_input">
                                <input type="text" class="datepicker" name="date_range_start_date"/>


                                <span id="help-block-date-range-start-date" class="form-select-error">
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td>To</td>
                            <td class="date_input">
                                <input type="text" class="datepicker" name="date_range_end_date"/>

                                <span id="help-block-date-range-end-date" class="form-select-error">
                                </span>
                            </td>
                        </tr>
                    </table>

                    <button type="submit">
                        Submit
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div id="snackbar">Zipping files..</div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>





    <script>
        $(function(){
            $('.filter-content').hide();
                $(document).ready(function() {
                    setTimeout(function(){
                        $('#filter_by').change();
                    },300);
                    $('#filter_by').change(function(){
                        var that = $(this);
                        if(that.val() == 'date')
                        {
                            $('.date_range').removeClass('hidden').prop('disabled',false);
                            $('.string_search').addClass('hidden').prop('disabled',true);
                        }
                        else
                        {
                            $('.date_range').addClass('hidden').prop('disabled',true);
                            $('.string_search').removeClass('hidden').prop('disabled',false);
                        }
                    });

                    var indexId = 0;
                    var jobIdElements = document.querySelectorAll('.job_ids');
                    for (var i = 0; i < jobIdElements.length; i++)
                        jobIdElements[i].id = 'job_ids.' + i;

                    $("#select_filter").change(function() {
                        $("#" + $(this).val()).show().siblings().hide();
                        $("#" + $(this).val()).addClass('active').siblings().removeClass('active');

                        var frm = $('.active form');
                        var btn = $('.active button');

                        btn.click(function (e) {

                            e.preventDefault();
                            var x = document.getElementById("snackbar");

                            // Add the "show" class to DIV
                            x.className = "show";


                            errorElements = document.getElementsByClassName('form-select-error');
                            for (var i = 0; i < errorElements.length; ++i) {
                                errorElements[i].innerHTML = '';
                            }


                            $.ajax({
                                type: frm.attr('method'),
                                url: frm.attr('action'),
                                data: frm.serialize(),
                                success: function (data) {
                                    if(data != 'invalid')
                                    {
                                        console.log('Submission was successful.');
                                        console.log(data);
                                        console.log(data['filename']);

                                        var input = $("#download_zip");
                                        input.val(data['filename']);


                                        $.fancybox.close();
                                        var x = document.getElementById("snackbar");
                                        x.className = x.className.replace("show", "");
                                        $('#download_zip_form').submit();
                                    }
                                    else
                                    {
                                        $('#snackbar').text('Failed');
                                        setTimeout(function(){
                                             $('#snackbar').text('Zipping files...');
                                             $('#snackbar').removeClass('show');
                                        },2000);
                                    }
                                   frm[0].reset();


                                },
                                error: function (xhr, request, error) {
                                    console.log('An error occurred.');

                                    var x = document.getElementById("snackbar");
                                    x.className = x.className.replace("show", "");

                                    console.log('readyState: ' + xhr.readyState);
                                    console.log('status: ' + xhr.status);
                                    console.log('response text: ' + xhr.responseText);

                                    try {
                                        responseJson = JSON.parse(xhr.responseText);

                                        if(responseJson['date_range_start_date']) {

                                            $('#help-block-date-range-start-date').text(responseJson['date_range_start_date']);
                                        }


                                        if(responseJson['date_range_end_date']) {

                                            $('#help-block-date-range-end-date').text(responseJson['date_range_end_date']);
                                        }

                                        if(responseJson['client_end_date']) {

                                            $('#help-block-client-end-date').text(responseJson['client_end_date']);
                                        }

                                        if(responseJson['client_start_date']) {

                                            $('#help-block-client-start-date').text(responseJson['client_start_date']);
                                        }

                                        if(responseJson['company']) {
                                            $('#help-block-client-company').text(responseJson['company']);
                                        }

                                        if(responseJson['invoice_start']) {
                                            $('#help-block-invoice_number_start').text(responseJson['invoice_start']);
                                        }

                                        if(responseJson['invoice_end']) {
                                            $('#help-block-invoice_number_end').text(responseJson['invoice_end']);
                                        }

                                        console.log("json size: " + indexId);

                                        for(var i = 0; i < (indexId + 1); i++) {
                                            if(responseJson["job_ids." + i]) {
                                                console.log("error messages: " + responseJson["job_ids." + i]);
                                                $('#job_row_' + i).text(responseJson["job_ids." + i]);
                                            } else {
                                                $('#job_row_' + i).text('');
                                            }
                                        }


                                    } catch( err) {
                                        console.log(err);
                                    }


                                },
                            });

                            return false;
                        });
                    });

                    $('.minus').click(function(){
                          $(this).parent('td').parent('tr').remove();
                    })

                    $('.add').click(function(){
                        indexId++;
                        $( "#job_id table" ).append(
                            $('<tr>' +
                                '<td>ID</td>' +
                                '<td>' +
                                    '<input name="job_ids[]" type="text"/> <span id="job_row_' + indexId + '" class="form-select-error"></span>' +
                                '</td>' +
                                '<td>' +
                                    '<span class="minus">[ - ]</span>' +
                                '</td>' +
                                '</tr>'));

                        $('.minus').click(function(){
                            $(this).parent('td').parent('tr').remove();
                        })
                    });
                });

            $('.download-modal').click(function(){
                $('.generated_invoice_body').html('');
                $('#download-bulk-invoice')[0].reset();
                $('#download-invoice-modal').modal('show');
                $('.ready-invoice').addClass('hidden');
                $('.generate-file').removeClass('hidden');
                $('.create-file').addClass('hidden');
                return false;
            });
            
            $('#download-bulk-invoice').submit(function(){
                var that = $(this);
                var data = that.serialize();

                $.post(URL+'invoices/generate-bulk-invoice',data)
                    .done(function(result){

                        if(result != '')
                        {

                            var data = $.parseJSON(result);
                            var batch = data['batch'];
                            
                            var str = '';
                            for(var i = 0; i < data['invoices'].length;i++)
                            {
                                var inv = data['invoices'][i];
                                str += '<tr>'+
                                            '<td><input type="checkbox" class="invoice-checkbox" value="'+inv.invoice_number+'"></td>'+
                                            '<td>'+ inv.invoice_number +'</td>'+
                                            '<td>'+ inv.project_name +'</td>'+
                                            '<td>'+ inv.date_created +'</td>'+
                                        '</tr>';
                               
                                // writeInvoiceToFile(batch,inv,data['invoices'].length,i);
                            }
                            $('.generated_invoice_body').html(str);
                            $('.ready-invoice').removeClass('hidden');
                            $('.generate-file').addClass('hidden');
                            $('.create-file').removeClass('hidden').attr('data-batch',batch);

                        }
                        else
                        {
                            $('.file-note').removeClass('hidden').addClass('alert-warning').html('<b>No invoice found.</b> Please adjust your filter.');
                            $('.generate-file').prop('disabled',false).html('Download Invoices');
                        }
                    });
                return false;
            });

            $('.create-file').click(function(){
                var that = $(this);
                var batch = that.attr('data-batch');
                var invoice_count = $('.invoice-checkbox:checked').length;
                $('.create-file').html('Generating Files...').prop('disabled',true);
                $('.file-note').removeClass('hidden').addClass('alert-warning').html('Generating invoices... Please wait.');
                if(invoice_count > 0)
                {
                    var i = 0;
                    $('.invoice-checkbox:checked').each(function(){
                        var invoice = $(this).val();
                        writeInvoiceToFile(batch,invoice,invoice_count,i);
                        $(this).closest('tr').addClass('success');
                        i++;
                    });
                    // $(this).closest('tr').removeClass('success');
                }
            });

            $('.checkall-invoice').click(function(){
                var that = $(this);
                var flag = false;
                if(that.is(':checked'))
                {
                    flag = true;
                }
                $('.invoice-checkbox').prop('checked',flag);
            });
            $('.invoice-client').change(function(){
                var that = $(this);
                $('.ready-invoice').addClass('hidden');
                $('.generated_invoice_body').html('');
                $('.generate-file').removeClass('hidden');
                $('.create-file').addClass('hidden');
            });
        });

        function writeInvoiceToFile(batch,invoice_number,max,ctr)
        {
            $.get(URL+'createPdfInvoice/'+batch+'/'+invoice_number)
                .done(function(result){
                    zipInvoicedFiles(batch,max,ctr);
                })
        }

        function zipInvoicedFiles(batch,max,ctr)
        {
            if((max-1) == ctr)
            {
                $.get(URL+'zip-directory/'+batch)
                    .done(function(result){
                       
                        $('.create-file').html('Download Invoices').prop('disabled',false);
                        downloadZipFile(batch);
                        $('.file-note').removeClass('hidden').removeClass('alert-warning').addClass('alert-success').html('<b>Success!</b> Click <b><a onClick="downloadZipFile(\''+batch+'\')">download</a></b> to save ');
                    });
            }
        }

        function downloadZipFile(batch)
        {
            $('.file-note').addClass('hidden').removeClass('alert-warning').removeClass('alert-success').html('');
            window.location = URL+'zip-download/'+batch;
        }
    </script>


    <!-- The Modal -->
    <div class="modal" id="download-invoice-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="download-bulk-invoice">
              <!-- Modal Header -->
                  <div class="modal-header">
                    <h4 class="modal-title pull-left">Download Invoice File</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                  </div>
                  <!-- Modal body -->
                  <div class="modal-body">
                        <div class="alert hidden file-note">

                        </div>
                        {{ csrf_field() }}
                        <div class="form-group hidden">
                            <label>Filter By</label>
                            <select class="form-control" name="filter">
                                <option value="job_id">Job ID</option>
                                <option value="invoice_number">Invoice Number Range</option>
                                <option selected value="client">Client</option>
                                <option value="date_range">Date Range</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6 px-0">
                                <label>Company</label>
                                <select class="form-control invoice-client" name="client">
                                    @foreach($companies as $cp)
                                        <option value="{{ $cp['id'] }}">{{ $cp['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>From Date</label>
                                <input type="text" class="form-control datepicker" name="from_date" value="" required/>
                            </div>
                            <div class="col-md-3 px-0">
                                <label>To Date</label>
                                <input type="text" class="form-control datepicker" name="to_date" value="" required/>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="form-group ready-invoice hidden">
                            <div class="table-responsive">
                                <table class="table table-condensed">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" class="checkall-invoice"></th>
                                            <th>Invoice Number</th>
                                            <th>Project</th>
                                            <th>Date Created</th>
                                        </tr>
                                    </thead>
                                    <tbody class="generated_invoice_body">
                                    </tbody>
                                </table>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                  </div>
                  <!-- Modal footer -->
                  <div class="modal-footer">
                    <button type="submit" class="btn btn-default generate-file" >Show Invoices</button>
                    <button type="button" class="btn btn-default create-file hidden" >Download Invoices</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                  </div>
                </form>
            </div>
        </div>
    </div>
    @endsection