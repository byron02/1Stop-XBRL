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
                <h2 class="content-title">List of Emails</h2>
                <div class="clearfix"></div>
            </div>
           
            <div class="form-group"> 
                <div class="col-sm-2 px-0">
                    <select class="form-control" name="filter_by">
                        <option selected disabled>Filter By</option>
                        <option value="id">Log Id</option>
                        <option value="type">Type</option>
                        <option value="date_sent">Date Sent</option>
                        <option value="email_recipient">Email Recipient</option>
                        <option value="email_cc">Email CC</option>
                    </select>
                </div>
                <div class="col-sm-3">
                     <div class="input-group">
                        <input type="hidden" id="filter-me" value="{{ app('request')->input('filter_by') }}">
                        <input type="text" id="search-me" class="form-control" placeholder="Search&hellip;" value="{{ app('request')->input('search') }}">
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default search-by">Search</button>
                           
                        </span>
                    </div>
                </div>
                 <div class="col-sm-7 text-right pr-0">
                    <a class="styled_btn submit_btn" style="text-decoration:none" data-toggle="modal" name="emailModal" data-target="#contactModal">
                        Send Email
                    </a>
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
                    $id_icon = request('order_by') == 'id' ? $icon[$sort_by] : 'fa-sort';
                    $type_icon = request('order_by') == 'type' ? $icon[$sort_by] : 'fa-sort';
                    $date_sent_icon = request('order_by') == 'date_sent' ? $icon[$sort_by] : 'fa-sort';
                    $email_recipient_icon = request('order_by') == 'email_recipient' ? $icon[$sort_by] : 'fa-sort';
                    $email_cc_icon = request('order_by') == 'email_cc' ? $icon[$sort_by] : 'fa-sort';
                @endphp
                <table class="table table-condensed table-hover" id="userTable">
                    <thead>
                        <tr class="dark">
                            <td>
                                <div class="pull-left">
                                    ID
                                </div>
                                <div class="pull-right">
                                    <a href="{{ url('/emails/?order_by=id&sort='.$sort) }}">
                                        <i class="fa {{ $id_icon }}"></i>
                                    </a>
                                </div>
                                <div class="clearfix"></div>
                            </td>
                            <td>
                                <div class="pull-left">
                                    Type
                                </div>
                                <div class="pull-right">
                                    <a href="{{ url('/emails/?order_by=type&sort='.$sort) }}">
                                        <i class="fa {{ $type_icon }}"></i>
                                    </a>
                                </div>
                                <div class="clearfix"></div>
                            </td>
                            <td>
                                <div class="pull-left">
                                    Email Recipient
                                </div>
                                <div class="pull-right">
                                    <a href="{{ url('/emails/?order_by=email_recipient&sort='.$sort) }}">
                                        <i class="fa {{ $email_recipient_icon }}"></i>
                                    </a>
                                </div>
                                <div class="clearfix"></div>
                            </td>
                            <td>
                                <div class="pull-left">
                                    Email CC
                                </div>
                                <div class="pull-right">
                                    <a href="{{ url('/emails/?order_by=email_cc&sort='.$sort) }}">
                                        <i class="fa {{ $email_cc_icon }}"></i>
                                    </a>
                                </div>
                                <div class="clearfix"></div>
                            </td>
                            <td>
                                <div class="pull-left">
                                    Date Sent
                                </div>
                                <div class="pull-right">
                                    <a href="{{ url('/emails/?order_by=date_sent&sort='.$sort) }}">
                                        <i class="fa {{ $date_sent_icon }}"></i>
                                    </a>
                                </div>
                                <div class="clearfix"></div>
                            </td>
                            <td>Attachments</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($emails as $key => $email)
                             <tr>
                             <td>{{ $email->id }}</td>
                             <td>{{ $email->type }}</td>
                             <td>{{ $email->email_recipient }}</td>
                             <td>{!! html_entity_decode($email->email_cc) !!}</td>
                             <td>{{ $email->date_sent }}</td>
                             <td>{{ $email->attachments }}</td>
                             </tr>
                        @endforeach
                    </tbody>
                </table>
               
                
                <div class="pull-left">
                    Displaying {{ $emails->firstItem() }} to {{ $emails->lastItem() }} of {{ $emails->total() }} Records(s) |  
                    <a href="{{url('/emails/?export=1')}}" style="color:black;">Export as CSV</a>
                </div>
                <div class="pull-right">
                    {{ $emails->links() }}
                </div>
                <div class="clearfix"></div>
            </div>


            
        </div>

       <!-- mail modal
        --> <div id="contactModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="contactModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h4 class="modal-title">Send Email</h4>
                            </div>
                            <div class="modal-body">
                                <div class="containter">
                                    <div class="row">
                                        <form id="send-email-form" class="form-horizontal" method="POST" action="{{ url('send-email') }}" enctype="multipart/form-data">
                                            {{ csrf_field() }}
                                            <div class="col-xs-12">
                                                <div class="form-group">
                                                    <label for="InputEmail" class="col-lg-1 control-label">To:</label>
                                                    <div class="col-lg-9">
                                                        <input type="email" class="form-control" id="email" name="email"  required  >
                                                    </div>
                                                    <div class="col-lg-1 pl-0">
                                                        <a href="#" id="showCC" class="styled_btn">CC</a>
                                                    </div>
                                                    <div class="col-lg-1 pl-0">
                                                        <a href="#" id="showBCC" class="styled_btn">BCC</a>
                                                    </div>
                                                </div>
                                                <div class="form-group" id="subject">
                                                    <label for="subject" class="col-lg-1 control-label">Subject:</label>
                                                     <div class="col-lg-9">
                                                        <input type="text" class="form-control" id="subject" name="subject" required>
                                                    </div>
                                                </div>
                                                <div class="form-group" id="cc" style="display: none;">
                                                    <label for="InputMessage" class="col-lg-1 control-label">CC:</label>
                                                     <div class="col-lg-9">
                                                        <input type="email" class="form-control" id="emailcc" name="cc_email" >
                                                    </div>
                                                </div>
                                                <div class="form-group" id="bcc" style="display: none;">
                                                    <label for="InputMessage" class="col-lg-1 control-label">BCC:</label>
                                                     <div class="col-lg-9">
                                                        <input type="email" class="form-control" id="emailbcc" name="bcc_email">
                                                    </div>
                                                </div>
                                                <div class="form-group" id="Bcc">
                                                    <label for="InputMessage" class="col-lg-1 control-label">Message:</label>
                                                     <div class="col-lg-11">
                                                         <textarea name="message" id="message" class="form-control" rows="5" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                <label for="InputFile" class="col-lg-1 control-label">Browse:</label>
                                                   
                                                </label>
                                                <div class="col-lg-11">
                                                     <input type="file" class="form-control" id="file" name="file">                                               
                                                </div>

                                               
                                            </div>
                                            <div class="pull-right">
                                             <button type="button" class="styled_btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
                                             <button type="submit" name="submit" id="submit" class="styled_btn btn-dark">Submit</button>
                                            </div>
                                        </form>
                                        
                                    </div>

                                </div>
                            </div><!-- End of Modal body -->
                        </div><!-- End of Modal content -->
                    </div><!-- End of Modal dialog -->
                </div><!-- End of Modal -->

    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script>
   
    $(document).ready(function(){
       
          
            $("#showCC").click(function(){
                
                if($('#cc').is(':visible'))
                {
                    $("#cc").hide();
                }
                else {
                    $("#cc").show();
                }
                
            });

            $("#showBCC").click(function(){
                if($('#bcc').is(':visible'))
                {
                    $("#bcc").hide();
                }
                else {
                    $("#bcc").show();
                }
                
            });


            var filterMe = 'email_recipient'
            $('.search-by').click(function(){
                var search = $('#search-me').val();
                filterMe = $('#filter-me').val() != '' ? $('#filter-me').val() : filterMe;
                window.location = URL+'emails?filter_by='+filterMe+'&search='+search;
            });
       
    });
    </script>
   
    @endsection