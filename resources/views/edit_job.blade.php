@extends('layouts.frontsite')

@section('content')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script>
        $(function() {
                 $( "#project_name" ).autocomplete({
                        source: URL+"getProjectName",
                });

              $('#add-job-form').submit(function(){
                    var that = $(this);
                    $('#btn_submit').text('Saving...').prop('disabled',true);
                    // var formData = new FormData();
                    var data = new FormData(this);
                    $.ajax({
                            url:that.attr('action'),
                            type:'post',
                            data:data,
                            cache:false,
                            contentType: false,
                            processData: false,
                            success:function(result){
                                $('#btn_submit').text('Saved').prop('disabled',false);
                                alertModal('Job Status','Updated successfully.','reload');
                            }
                    });
                    return false;
                });

        });
  </script>
    <div class="wrapper">
        <form id="add-job-form" class="form-horizontal" method="POST" action="{{url('/add-jobs/update')}}" enctype="multipart/form-data">
            {{ csrf_field() }}


        <div class="page-holder">
            <h2 class="content-title">Edit Job [#{{  $jobs->id }}]</h2>
            <div class="pull-right">
                @if(Auth::user()->role_id != 4)
                    <a href="#" class="edit-form">Edit</a> | 
                    <a href="#" class="quick-upload-open" data-user="{{Auth::user()->role_id}}">Quick Upload</a>  |
                @elseif(Auth::user()->role_id == 4 && $jobs->status != 13)
                    <a href="#" class="quick-upload-open" data-user="{{Auth::user()->role_id}}">Upload Files</a> |
                @endif 
                 <a href="#" data-toggle="modal" data-target="#source-files-modal">Source Files</a>  
                <!-- | <a>Upload Source Files</a> -->
            </div>
            @if(session()->has('success'))
                <div class="alert alert-success">
                    {{ session()->get('success') }}
                </div>
            @endif<br/>
            <div class="clearfix"></div>

            <div class="col-sm-6 jobs">
                <table cellpadding="0" cellspacing="0" border="0" style="width:auto;">
                    <tr class="{{ Session::get('orig_user') != '' ? 'hidden' : '' }}">
                        <td>
                            <label for="job_title">Company *</label>
                        </td>
                        <td>
                            {{ $jobs->company_name }}
                            <input type="hidden" value="{{ $jobs->company_id }}" id="company" name="company" class="form-control price-compute">
                        </td>
                    </tr>
                    <tr>
                        <td>Turnaround Time</td>
                        <td>
                            {{ $jobs->turnaround_name }}
                            <input type="hidden" name="turnaround" id="turnaround" value="{{ $jobs->turnaround_id }}" class="price-compute form-control">
                        </td>

                    </tr>
                    <tr>
                        <td>
                            <label for="job_title">Project Name *</label>
                        </td>
                        <td>
                            <div class="{{ $errors->has('project_name') ? ' has-error' : '' }}">
                                <textarea class="form-control" name="project_name" id="project_name" required autofocus>{{$jobs->project_name}}</textarea>
                                <!-- <input type="text" class="form-control" name="project_name" id="project_name"  value="" required autofocus> -->
                            </div>
                        </td>


                    </tr>
                    <tr>
                        <td>Enter a Purchase Order or Unique Reference</td>
                        <td><input type="text" class="form-control" name="purchase_order" value="{{$jobs->purchase_order}}"/>
                        </td>
                    </tr>
                    @if(Auth::user()->role_id != 4)
                    <tr>
                        <td>Ordered By csr</td>
                        <td>
                            {{ $jobs->user_order }}
                            <input type="hidden" name="user_id" value="{{ $jobs->user_id }}"/>
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td>Companies House Registration Number</td>
                        <td><input type="text" class="form-control" name="registration_number" value="{{ $jobs->companies_house_registration_no }}"/>
                        </td>
                    </tr>
                    <tr>
                        {{--Accounting Standard--}}
                        <td>

                            <label for="taxonomy">Accounting Standard *</label><br/>
                            @foreach($taxonomy_group as $tg)
                                <label class="text-normal">
                                    <input type="radio" class="taxonomy_group price-compute" name="taxonomy_group" value="{{ $tg->id }}"  {{ $jobs->group == $tg->id ? 'checked' : ''}}>
                                         {{$tg->group}}
                                    </label>
                            @endforeach
                        </td>
                        <td>
                            <select id="taxonomy" class="form-control" name="taxonomy" class="price-compute"> </select>
                        </td>
                    </tr>

                    <tr id="tax_reference">
                        <td>Tax Reference</td>

                        <td>
                            <input type="text" class="form-control" name="tax_reference" />
                        </td>
                    </tr>

                        
                    <tr>
                        <td><strong>UTR Number</strong> (If Applicable)</td>
                        <td><input type="text" class="form-control" name="utr_number" value="{{ $jobs->utr_number }}"/>
                        </td>
                    </tr>
                    <tr class="hidden">
                        <td><strong>Were these accounts iXBRL tagged last year?</strong></td>
                        <td>
                            <input type="radio" value="1" name="tagged" >
                            <label for="tag1">Yes</label>
                            <input type="radio" value="2" name="tagged" checked>
                            <label for="tag2">No</label>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Do you require your statutory accounts to be Converted?</strong></td>
                        <td>
                            <input type="radio" value="1" name="statutory" data-work-type="statutory" class="price-compute work_radio" {{ $jobs->work_type == 1 ? 'checked' : '' }}>
                            <label for="tag1">Yes</label>
                            <input type="radio" value="2" name="statutory" data-work-type="statutory" class="price-compute work_radio" {{ $jobs->work_type == 1 ? '' : 'checked' }}>
                            <label for="tag2">No</label>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Do you require Tax Computation to be Converted?</strong></td>
                        <td>
                            <input type="radio" value="1" name="tax_computation" data-work-type="tax" class="price-compute work_radio" {{ $jobs->tax_computation_converted == 1 ? 'checked' : '' }}>
                            <label for="tag1">Yes</label>
                            <input type="radio" value="0" name="tax_computation" data-work-type="tax" class="price-compute work_radio" {{ $jobs->tax_computation_converted == 1 ? '' : 'checked' }}>
                            <label for="tag2">No</label>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="col-sm-6 jobs">
                <table cellpadding="0" cellspacing="0" border="0" style="width:auto;">
                    <tr>
                        <td><strong>Entity Dormant</strong></td>
                        <td>
                            <input type="radio" value="1" name="dormant" {{ $jobs->entity_dormant == 1 ? 'checked' : '' }}>
                            <label for="tag1">Yes</label>
                            <input type="radio" value="2" name="dormant" {{ $jobs->entity_dormant == 2 ? 'checked' : '' }}>
                            <label for="tag2">No</label>
                        </td>
                    </tr>
                    <tr>
                        <td>Year End</td>
                        <td class="date_input"><input type="text" class="datepicker form-control" name="year_end"  readonly="readonly" value="{{ date('d-m-Y',strtotime($jobs->year_end)) }}"/></td>
                    </tr>
                    <tr>
                        <td>Date Of Director's Report</td>
                        <td class="date_input"><input type="text" class="datepicker form-control" name="director_report_date" value="{{ date('d-m-Y',strtotime($jobs->date_of_director_report)) }}"/>
                        </td>
                    </tr>
                    <tr>
                        <td>Date Of Auditor's Report</td>
                        <td class="date_input"><input type="text" class="datepicker form-control" name="auditor_report_date"   value="{{ date('d-m-Y',strtotime($jobs->date_of_auditor_report)) }}"/>
                        </td>
                    </tr>
                    <tr>
                        <td>Approval Of Accounts Date</td>
                        <td class="date_input"><input type="text" class="datepicker form-control" name="account_approval_date" value="{{ date('d-m-Y',strtotime($jobs->approval_of_accounts_date)) }}" />
                        </td>
                    </tr>
                    <tr>
                        <td>Name of the Director Approving Accounts</td>
                        <td><input type="text" class="form-control" name="director_approving_account" value="{{ $jobs->name_of_director_approving_accounts }}"/>
                        </td>
                    </tr>
                    <tr>
                        <td>Name of the Director Signing Director's Report</td>
                       
                        <td><input type="text" class="form-control" name="director_signing_report"  value="{{ $jobs->name_of_director_signing }}"/>
                        </td>
                    </tr>
                

                    <script>
                        $(document).ready(function(){
                            var taxGroup = $('.taxonomy_group:checked').val();
                            getTaxonomy(taxGroup);

                            $('.taxonomy_group').click(function(){
                                var that = $(this);
                                $('#taxonomy').html('');

                                if(that.is(':checked') == true)
                                {
                                     getTaxonomy(that.val());
                                }
                            });
                            setTimeout(function(){
                                $('.work_radio:checked').click();

                            },300)
                            $('.work_radio').click(function(){
                                var that = $(this);
                                var type = that.attr('data-work-type');
                                var data = that.val();

                                if(that.is(':checked') == true)
                                {
                                    if(type == 'tax')
                                    {

                                        if(parseInt(data) == 1)
                                        {
                                            $('.tax_compute_row').removeClass('hidden');
                                            $('.tax_compute_row input').prop('disabled',false);
                                        }
                                        else
                                        {
                                            $('.tax_compute_row').addClass('hidden');
                                            $('.tax_compute_row input').prop('disabled',true);
                                        }
                                        
                                    }
                                    else
                                    {
                                        if(parseInt(data) == 1)
                                        {
                                             $('.statutory_row').removeClass('hidden');
                                            $('.statutory_row input').prop('disabled',false);
                                        }
                                        else
                                        {
                                            $('.statutory_row').addClass('hidden');
                                            $('.statutory_row input').prop('disabled',true);
                                        }
                                    }
                                   
                                }
                            });

                           setTimeout(function(){
                                $('.work_radio:checked').click();
                                 $('.page_field').change();

                            },300);
                            $('.page_field').change(function(){
                            var that = $(this);
                            
                            var page = that.val() != '' ? parseInt(that.val()) : 0;
                            var company = document.getElementById("company").value;
                                company = company != '' ? company : 0;
                            var turnaround = document.getElementById("turnaround").value;
                            var work_type = that.attr('data-work');
                            var taxonomy = $('#taxonomy option:selected').text().replace(/\//g,'~');
                            // var taxonomy = $('.taxonomy_group:checked').val();
                            var price = '0.00';
                            if(work_type == 'Statutory Account')
                            {
                                var stat = $('input[name="statutory"]:checked').val();
                                if(stat == 2)
                                {
                                    that.val('');
                                    that.closest('tr').find('.row_price').text(price);
                                    return false;
                                }

                            }

                            // // console.log(URL);
                            $.get(URL+'job-pricing/'+page+'/'+company+' /'+turnaround+'/'+work_type+'/'+taxonomy+'/')
                                    .done(function(result){
                                        var data = $.parseJSON(result);
                                        if(typeof data.total_price != 'undefined')
                                        {
                                            price = parseFloat(data.total_price).toFixed(2);
                                        }
                                        that.closest('tr').find('.row_price').text(price);
                                        $.get(URL+'company-country/'+company+'/')
                                        .done(function(country){
                                            var country_code = country[0].country;
                                            computeTotalPrice(country_code);
                                        })
                                    });
                            });

                    

                            $('.toggle-comment').click(function(){
                                $('#previous-comment').slideToggle(); 
                            });

                        });
                    

                        function getTaxonomy(taxGroup)
                        {
                            if(taxGroup == 2)
                            {
                                 $("#tax_reference").show();
                            }
                            else
                            {
                                 $("#tax_reference").hide();
                            }
                            var job_tax = '{{ $jobs->taxonomy }}' ;
                            $.get(URL+'getTaxGroup/'+taxGroup)
                                .done(function(result){
                                    if(result != '')
                                    {
                                        var data = $.parseJSON(result);
                                        var str = '';
                                        $.each(data,function(i,x){
                                            var tax_check = job_tax == x.id ? 'selected' : '';
                                            str += '<option '+tax_check+' value="'+x.id+'">'+x.name+'</option>';
                                        });

                                        $('#taxonomy').html(str);
                                        $('.page_field').change();
                                    }
                                })
                        }

                        function computeTotalPrice(country_code)
                                {
                                    var tprice = 0;
                                    $('.row_price').each(function(){
                                        var that = $(this);
                                        let page = that.closest('tr').find('.page_field').val();
                                   
                                            tprice += parseFloat(that.text());
                                     
                                        
                                    });
                                    var vat = 0;
                                        if(country_code == 222){
                                            vat = tprice * 0.20;
                                        } else {
                                            vat = 0
                                        }
                                    var total = parseFloat(vat) + parseFloat(tprice);
                                    var row = parseFloat(tprice) - parseFloat(vat)
                                    
                                    $('.row_price').attr('value',row.toFixed(2));
                                    $('.price_vat').attr('value',vat.toFixed(2));
                                    $('.total_price').attr('value',total.toFixed(2));
                                }
                    </script>

                    @php
                        $comment_view = 'hidden';
                    @endphp

                    @if(Auth::user()->role_id == 8 || Auth::user()->role_id == 2 || Auth::user()->role_id == 1)
                        @php
                            $comment_view = '';
                        @endphp
                    @endif
                    <tr class="{{ $comment_view }} comment-section">
                        <td>Send To :</td>
                        <td>
                            <select name="send_to" class="form-control comment-receiver">
                                <option value="client">Client</option>
                                <option value="vendor">Vendor</option>
                            </select>
                        </td>
                    </tr>
                    <tr  class="{{ $comment_view }} comment-section">
                        <td>New Comment</td>
                        <td>
                            <textarea class="form-control comment-area" name="comment" ></textarea>
                        </td>
                    </tr> 
                     <tr>
                     @if(Auth::user()->role_id != 4)
                        <td><small><button class="btn btn-link btn-sm toggle-comment comment_button {{ count($job_comment) > 0 ? '' : 'hidden' }}" type="button">(Show/Hide previous comments)</button></small></td>
                        @endif
                        <td><small><button type="button" class="styled_btn btn-dark comment_button {{ $comment_view }}" id="write_comment">Send Comment</button></small>
                        
                            @if($jobs->status != 9)
                            <button type="button" class="styled_btn hidden" id="cancel_job" data-toggle="confirmation" data-btn-ok-label=" Yes" data-btn-ok-class="btn btn-dark"
                                    data-placement="top"
                                    data-btn-cancel-label="No" data-btn-cancel-class="btn btn-danger"
                                    data-btn-cancel-icon-class="fa" data-btn-cancel-icon-content="close"
                                    data-title="Are you sure?" data-value="9">
                                <i class="fa fa-exclamation-triangle"></i> Cancel Job
                            </button>
                            @else
                            <button type="button" class="styled_btn hidden" id="cancel_job" data-toggle="confirmation" data-btn-ok-label=" Yes" data-btn-ok-class="btn btn-dark"
                                    data-placement="top"
                                    data-btn-cancel-label="No" data-btn-cancel-class="btn btn-danger"
                                    data-btn-cancel-icon-class="fa" data-btn-cancel-icon-content="close"
                                    data-title="Are you sure?" data-value="13">
                                <i class="fa fa-exclamation-triangle"></i> Complete Job - For Client Sign Off
                            </button>
                            @endif
                        </td>
                    </tr>
                </table>

                @if(count($job_comment) > 0)
                <div class="table-responsive" id="previous-comment">
                    <table class="table table-condensed" id="comment_table">
                        <thead>
                            <tr class="dark">
                                <th>Date/Time</th>
                                <th>Action</th>
                                <th>Comment</th>
                            </tr>
                        </thead>
                        <tbody class="comment-body">
                            @foreach($job_comment as $jc)
                                @if(Auth::user()->role_id != 4)
                                    <tr>
                                        <td>{{ date('M d,Y h:i a',strtotime($jc->date_added)) }}</td>
                                        <td>{{ $jc->action }}</td>
                                        <td class="full_width"><a href="#" data-toggle="popover" title="Comment" data-placement="left" data-trigger="focus" data-content="{{ $jc->comment }}">{{ $jc->comment }}</a></td>
                                    </tr>
                                @endif
                                @if($jc->tags == 2 && Auth::user()->role_id == 4)
                                    <tr>
                                        
                                        <td>{{ date('M d,Y h:i a',strtotime($jc->date_added)) }}</td>
                                        <td>{{ $jc->action }}</td>
                                        <td class="full_width"><a href="#" data-toggle="popover" title="Comment" data-placement="left" data-trigger="focus" data-content="{{ $jc->comment }}">{{ $jc->comment }}</a></td>
                                    </tr>
                                        @endif
                               
                                
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

            <script >
                $(document).ready(function() {
                    $(".file_upload").change(function(){
                        var that = $(this);
                        if (this.files && this.files[0]) {

                            var fileName = that.val().replace(/.*[\/\\]/, '');
                            var reader = new FileReader();
                            reader.onload = function (e) {
                                that.closest('td').find('.file_64').val(e.target.result);
                                that.closest('td').find('.file_name').val(fileName);
                                if(that.hasClass('hidden'))
                                {
                                    that.closest('td').find('.pull-left span').text(fileName);
                                }
                            }

                            console.log("File clean:" + fileName);



                            reader.readAsDataURL(this.files[0]);

                        }
                    });

                    $('.change_upload').click(function(){
                        var that = $(this);
                        that.closest('td').find('input[type="file"]').click();
                    });

                    $('.remove-source').click(function(){
                        var that = $(this);

                        if(confirm('Do you want to remove this file?'))
                        {
                            var fileId = that.attr('data-file');
                            var _token = '{{ csrf_token() }}';
                            $.post(URL+'remove-source-file',{'file_id':fileId,'_token':_token})  
                                .done(function(result){
                                    that.closest('td').find('.pull-left span').text('');
                                    that.closest('td').find('.file_upload').removeClass('hidden').prop('required',true);
                                    that.closest('td').find('.file_name').val('');
                                    that.closest('td').find('.file_source').val(0);
                                    that.closest('td').find('.pull-right').html('');
                                });
                        }
                        return false;
                    });
                    var job = $('input[name="job_id"]').val();
                    var status = 0;
                    
                    $('[data-toggle="confirmation"]').click(function(){
                        var that = $(this);
                        status = that.attr('data-value');
                        $('#write_comment').addClass('hidden');
                        $('#cancel_job').removeClass('hidden');
                    });

                 
                    $('[data-toggle="confirmation"]').confirmation({
                        onConfirm: function() { 
                            var comment = $('textarea[name="comment"]')
                            if($.trim(comment.val()) != '')
                            {
                                var _token = '{{ csrf_token() }}';
                                comment.css('border-color','#ccc');
                                $.post(URL+'status-job',{'_token':_token,'job_id':job,'status':status,'comment': $.trim(comment.val())})
                                    .done(function(result){
                                        alertModal('Job Status','Status Updated successfully.','reload')
                                    });
                            }
                            else
                            {
                                comment.css('border-color','#f05553');
                                comment.focus();
                            }
                            
                        },
                        onCancel: function() { return false;},
                    });
                    setTimeout(function(){
                       formViewing();
                    },300);
                    
                    $('.edit-form').click(function(){
                        var that = $(this);
                        if(that.hasClass('edit-mode'))
                        {
                            formViewing();
                            that.text('Edit');
                            that.removeClass('edit-mode');
                        }
                        else
                        {
                            formEditting();
                            that.text('Cancel Editting');
                            that.addClass('edit-mode');
                        }
                        return false;
                    });
                });
                
                function formEditting()
                {
                    $('#add-job-form .form-control,#add-job-form input[type="radio"]').prop('disabled',false);
                    $('#add-job-form button').not('#cancel_job').not('[value="back"]').removeClass('hidden');
                }

                function formViewing()
                {
                     $('#add-job-form .form-control,#add-job-form input[type="radio"]').prop('disabled',true);
                    $('#add-job-form button').not('#cancel_job').not('[value="back"]').not('.comment_button').addClass('hidden');
                    $('.comment-section').find('textarea,select').prop('disabled',false);
                }

            </script>

            <div class="clearfix"></div>
                <table class="table table-bordered {{ Auth::user()->role_id == 4 ? 'hidden' : '' }}" id="uploads">
                    <thead>
                        <tr>
                            <td class="datagrid_header"></td>
                            <td class="datagrid_header">Pages</td>
                            <td class="datagrid_header">Upload relevant Files(Word, Excel, PDF) </td>
                            <td class="datagrid_header" width="200">Price</td>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                    @php
                        $arr = array(
                                        array(
                                                'class_name' => 'statutory_row',
                                                'label' => 'Tell us how many pages are in your statutory accounts',
                                                'data-work' => 'Statutory Account'
                                        ),
                                        array(
                                                'class_name' => 'tax_compute_row hidden',
                                                'label' => 'Tell us how many pages are in your tax computations',
                                                'data-work' => 'Tax Computation'
                                        ),
                                    );
                        
                        
                    @endphp
                    @foreach($job_source as $j => $js)
                        @if($js->type == 0 && $js->is_removed == 0)
                            @php
                                $rw = $arr[$js->tax_computed];
                            @endphp

                            <tr  class="{{ $rw['class_name'] }}">
                                <td>{{$rw['label']}}</td>
                                <td width="10%">
                                    <div class="{{ $errors->has('page') ? ' has-error' : '' }}">
                                        <input type="text" class="page_field form-control" data-work="{{$rw['data-work']}}" name="page[]" value="{{$js->page_count}}" required>

                                    </div>

                                </td>
                                <td>
                                    <div class="pull-left">
                                        <span>
                                            {{$js->server_filename}}
                                        </span>
                                        <input class="file_upload form-control hidden" type="file" size="33" name="file[]">
                                    </div>
                                    <div class="pull-right">
                                        <span><button type="button" class="btn btn-link btn-sm change_upload"><i class="glyphicon glyphicon-cloud-upload"></i> change</button></span>
                                        <span class="hidden"><a href="#"><i class="glyphicon glyphicon-cloud-download"></i> download</a></span>
                                        <span><button type="button" class="btn btn-link btn-sm remove-source" data-file="{{$js->id}}"><i class="glyphicon glyphicon-trash"></i> remove</button></span>
                                    </div>
                                    <div class="clearfix"></div>
                                    <input class="file_64" type="hidden" name="file64[]" value=""/>
                                    <input class="file_name" type="hidden" name="file_name[]" value="{{ $js->server_filename }}"/>
                                    <input type="hidden" class="file_source form-cont" name="file_source[]" value="{{  $js->id }}">
                                    <input type="hidden" name="row_type[]" value="{{ $js->tax_computed }}">
                                </td>
                            <td class="row_price">0.00</td>
                            </tr>
                        @endif
                    @endforeach

<!--
                    @foreach($arr as $k => $rw)
                         @php
                            $removed = isset($job_source[$k]) ? $job_source[$k]->is_removed : 0;
                        @endphp

                        @if(isset($job_source[$k]) && $job_source[$k]->type == 0)
                        <tr  class="{{ $rw['class_name'] }}">
                            <td>{{$rw['label']}}</td>
                            <td width="10%">
                                <div class="{{ $errors->has('page') ? ' has-error' : '' }}">
                                    <input type="number" class="page_field form-control" data-work="{{  $rw['data-work']}}" name="page[]" value="{{ $removed == 0 ? (isset($job_source[$k]) ? $job_source[$k]->page_count : '') : '' }}" min="1" required>

                                </div>

                            </td>
                            <td>
                                <div class="pull-left">
                                    <span>
                                    @if($removed == 0)
                                        {{isset($job_source[$k]) ? $job_source[$k]->server_filename : ''}}
                                    @endif
                                    </span>
                                    <input class="file_upload form-control {{ $removed == 0 ? 'hidden' : '' }}" type="file" size="33" name="file[]" {{ $removed == 0 ? '' : 'required' }} >
                                </div>
                                <div class="pull-right {{ $removed == 0 ? '' : 'hidden' }}">
                                    <span><button type="button" class="btn btn-link btn-sm change_upload"><i class="glyphicon glyphicon-cloud-upload"></i> change</button></span>
                                    <span class="hidden"><a href="#"><i class="glyphicon glyphicon-cloud-download"></i> download</a></span>
                                    <span><button type="button" class="btn btn-link btn-sm remove-source" data-file="{{isset($job_source[$k]) ? $job_source[$k]->id : 0}}"><i class="glyphicon glyphicon-trash"></i> remove</button></span>
                                </div>
                                <div class="clearfix"></div>
                                <input class="file_64" type="hidden" name="file64[]" value=""/>
                                <input class="file_name" type="hidden" name="file_name[]" value="{{ $removed == 0 ? (isset($job_source[$k]) ? $job_source[$k]->server_filename : '') : '' }}"/>
                                <input type="hidden" class="file_source form-cont" name="file_source[]" value="{{ $removed == 0 ? (isset($job_source[$k]) ? $job_source[$k]->id : 0) : 0 }}">
                                <input type="hidden" name="row_type[]" value="{{ isset($job_source[$k]) ? $job_source[$k]->tax_computed : 0 }}">
                            </td>
                        <td class="row_price">0.00</td>
                        </tr>
                        @endif
                    @endforeach
                    -->
                     <tfoot>
                        <tr class="active">
                            <td colspan="3" class="text-right" ><strong>VAT</strong></td>
                        <td type="vat" class=""><input type="text" class="price_vat form-control" name="vat" value="{{ $jobs->tax_computation_price }}" readonly ></td>
                        </tr>
                        <tr class="active">
                            <td colspan="3" class="text-right" ><strong>Total</strong></td>
                            <td type="total" class=""><input type="text" class="total_price form-control" name="total_price" value="{{ $jobs->computed_price }}" readonly ></td>
                        </tr>
                    </tfoot>
                </table>
                <input type="hidden" value="{{ $jobs->id }}" name="job_id">

                <div class="form-group">
                    <div class="col-lg-6">
                        @if($jobs->status != 9)
                        <button type="button" class="styled_btn" data-toggle="confirmation" data-btn-ok-label=" Yes" data-btn-ok-class="btn btn-dark"
                                data-placement="top"
                                data-btn-cancel-label="No" data-btn-cancel-class="btn btn-danger"
                                data-btn-cancel-icon-class="fa" data-btn-cancel-icon-content="close"
                                data-title="Are you sure?" data-value="9">
                            <i class="fa fa-exclamation-triangle"></i> Cancel Job
                        </button>
                        @else
                        <button type="button" class="styled_btn" data-toggle="confirmation" data-btn-ok-label=" Yes" data-btn-ok-class="btn btn-dark"
                                data-placement="top"
                                data-btn-cancel-label="No" data-btn-cancel-class="btn btn-danger"
                                data-btn-cancel-icon-class="fa" data-btn-cancel-icon-content="close"
                                data-title="Are you sure?" data-value="13">
                            <i class="fa fa-exclamation-triangle"></i> Complete Job - For Client Sign Off
                        </button>
                        @endif

                        <!-- <button type="button" class="styled_btn" value="Cancel" onclick="window.location='{{url("/")}}'"><i class="fa fa-store"></i> Assign to Vendor</button> -->
                    </div>

                    <div class="col-lg-6 text-right">
                        <button type="button" class="styled_btn" value="back" onclick="window.location='{{url("/")}}'">Back to Jobs</button>
                        <button type="submit" class="styled_btn btn-dark" id="btn_submit">Save</button>
                    </div>
                </div>
               
            </div>
        </form>
    </div>

    @php
        $tmp = 123;
        $arr = ['Input Files','Supporting Files','iXBRL Tag File','Output Files','Revision Files'];  
    @endphp

    <!-- Modal -->
    <div id="quick-upload-modal" class="modal fade" role="dialog">
      <div class="modal-dialog modal-sm">
      <form id="quick-source-form" enctype="multipart/form-data" method="post" action="{{url('quick-upload')}}">
            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                <a type="button" class="close" data-dismiss="modal">&times;</a>
                <h4 class="modal-title">Quick Upload</h4>
              </div>
              <div class="modal-body">
                {{ csrf_field() }}
                <div class="form-group">
                    <label>Please tell us what kind of file you are trying to upload.</label>
                    <select class="form-control quick-type-list" name="file_type" required>
                        @foreach($arr as $k => $type)
                            @if(Auth::user()->role_id == 4 && $k > 2)
                                <option value="{{ $k }}">{{ $type }}</option>
                            @endif

                            @if(Auth::user()->role_id != 4 && $k != 3)
                                <option value="{{ $k }}">{{ $type }}</option>
                            @endif
                        @endforeach
                    </select>
                    <input type="hidden" name="source_id" value="0" class="source_id">
                    <input type="hidden" name="job_id" value="{{ $jobs->id }}">
                    <input type="hidden" name="file_content" class="quick-content">
                    <input type="hidden" name="file_name" class="quick-name">
                </div>
                <div class="form-group computation-group">
                    @if(Auth::user()->role_id != 4)
                        <label>Is this a tax computation file?</label>
                        <br/>
                        <label><input type="radio" name="tax_computation" class="quick-computation-tax" value="1" required/> Yes</label>
                        <label><input type="radio" checked name="tax_computation" class="quick-computation-tax" value="0" required/> No</label>
                    @else
                        <label><input type="radio" checked name="tax_computation" class="quick-computation-tax" value="0" required/> Statutory Accounts</label>
                        <label><input type="radio" name="tax_computation" class="quick-computation-tax" value="1" required/> Tax Computations</label>
                    @endif
                </div>
                <div class="form-group hidden">
                    <label>No. of Pages</label>
                    <input type="number" class="form-control quick-pages" name="pages" value="0" required>
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


    <div id="admin-upload-modal" class="modal fade" role="dialog">
      <div class="modal-dialog  }}">
      <form id="admin-source-form" enctype="multipart/form-data" method="post" action="{{url('admin-quick-upload')}}">
            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                <a type="button" class="close" data-dismiss="modal">&times;</a>
                <h4 class="modal-title">Quick Upload</h4>
              </div>
              <div class="modal-body">
                {{ csrf_field() }}
                <div class="form-group">
                    <label>Please tell us what kind of file you are trying to upload.</label>
                    <select class="form-control admin-type-list" name="file_type" required>
                        @foreach($arr as $k => $type)
                            @if($k != 3)
                                <option value="{{ $k }}">{{ $type }}</option>
                            @endif

                        @endforeach
                    </select>
                    <input type="hidden" name="job_id" value="{{ $jobs->id }}">
                </div>
                <div class="form-group for-input-file">
                    <div class="col-md-4">
                        <label><input type="checkbox" name="tax_category[]" value="0" checked class="category-checkbox" data-type="0" data-target=".account-browse"> Statutory Accounts</label>
                    </div>
                    <div class="col-md-8 account-browse">
                        <input type="file" class="form-control vendor-file" name="uploaded_file[]" required >
                        <input type="hidden" name="source_id[]" value="0" class="source_id">
                        <input type="hidden" name="file_content[]" class="quick-content">
                        <input type="hidden" name="file_name[]" class="quick-name">
                        <input type="hidden" name="page[]" class="stat-page">
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="form-group for-input-file">
                    <div class="col-md-4">
                        <label><input type="checkbox" name="tax_category[]" value="1" class="category-checkbox" data-type="1" data-target=".tax-browse"> Tax Computations</label>
                    </div>
                    <div class="col-md-8 tax-browse">
                        <input type="file" class="form-control vendor-file" name="uploaded_file[]" required disabled>
                        <input type="hidden" name="source_id[]" value="0" class="source_id" disabled>
                        <input type="hidden" name="file_content[]" class="quick-content" disabled>
                        <input type="hidden" name="file_name[]" class="quick-name" disabled>
                        <input type="hidden" name="page[]" class="tax-page">
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="form-group not-input-file hidden">
                    <div class="col-md-12" id="not-input-browse">
                        <input type="file" class="form-control vendor-file" name="uploaded_file[]" required disabled>
                        <input type="hidden" name="source_id[]" value="0" class="source_id" disabled>
                        <input type="hidden" name="file_content[]" class="quick-content" disabled>
                        <input type="hidden" name="file_name[]" class="quick-name" disabled>
                        <input type="hidden" name="page[]" class="tax-page">
                        <input type="hidden" name="tax_category[]" value="0"  disabled>
                    </div>
                    <div class="clearfix"></div>
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

    <div id="vendor-upload-modal" class="modal fade" role="dialog">
      <div class="modal-dialog {{ (Auth::user()->role_id != 4) ? 'modal-sm' : '' }}">
      <form id="vendor-source-form" enctype="multipart/form-data" method="post" action="{{url('vendor-quick-upload')}}">
            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                <a type="button" class="close" data-dismiss="modal">&times;</a>
                <h4 class="modal-title">Quick Upload</h4>
              </div>
              <div class="modal-body">
                {{ csrf_field() }}
                <div class="form-group">
                    <label>Please tell us what kind of file you are trying to upload.</label>
                    <select class="form-control quick-type-list vendor-type-list" name="file_type" required>
                        @foreach($arr as $k => $type)
                            @if($k > 2)
                                <option value="{{ $k }}">{{ $type }}</option>
                            @endif

                        @endforeach
                    </select>
                    <input type="hidden" name="job_id" value="{{ $jobs->id }}">
                </div>
                <div class="form-group output-group">
                    <div class="col-md-4">
                        <label><input type="checkbox" name="tax_category[]" value="0" checked class="category-checkbox" data-type="0" data-target=".account-browse"> Statutory Accounts</label>
                    </div>
                    <div class="col-md-8 account-browse" >
                        <input type="file" class="form-control vendor-file" name="uploaded_file[]" required >
                        <input type="hidden" name="source_id[]" value="0" class="source_id">
                        <input type="hidden" name="file_content[]" class="quick-content">
                        <input type="hidden" name="file_name[]" class="quick-name">
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="form-group output-group">
                    <div class="col-md-4">
                        <label><input type="checkbox" name="tax_category[]" value="1" class="category-checkbox" data-type="1" data-target=".tax-browse"> Tax Computations</label>
                    </div>
                    <div class="col-md-8 tax-browse" >
                        <input type="file" class="form-control vendor-file" name="uploaded_file[]" required disabled>
                        <input type="hidden" name="source_id[]" value="0" class="source_id" disabled>
                        <input type="hidden" name="file_content[]" class="quick-content" disabled>
                        <input type="hidden" name="file_name[]" class="quick-name" disabled>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="form-group revision-group hidden">
                    <div class="col-md-12" >
                       <input type="hidden" name="tax_category[]" value="0" class="category-checkbox" disabled>
                        <input type="file" class="form-control vendor-file" name="uploaded_file[]" required disabled>
                        <input type="hidden" name="source_id[]" value="0" class="source_id" disabled>
                        <input type="hidden" name="file_content[]" class="quick-content" disabled>
                        <input type="hidden" name="file_name[]" class="quick-name" disabled>
                    </div>
                    <div class="clearfix"></div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn" data-dismiss="modal" data-backdrop="false">Close</button>
                <button type="submit" class="btn btn-default quick-submittn btn-default quick-submit">Submit</button>
              </div>
            </div>
        </form>
      </div>
    </div>

    <!-- Modal -->
    <div id="source-files-modal" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <a type="button" class="close" data-dismiss="modal">&times;</a>
            <h4 class="modal-title">Job Source Files</h4>
          </div>
          <div class="modal-body">
                <div class="form-group">
                    <div class="table-responsive">
                         
                        <table class="table">
                            <tbody>
                            @if(!empty($job_source))
                                @php
                                    $marked_up = array();
                                @endphp
                                @foreach($job_source as $js)
                                    @if($tmp != $js->type)

                                        <tr class="dark">   
                                            <th colspan="3">{{ $arr[$js->type] }}</th>
                                        </tr>
                                         @php
                                            $tmp = $js->type;   
                                        @endphp
                                    @endif
                                    @if($js->is_removed == 0)
                                        <tr data-type="{{ $tmp }}" title="{{ $js->server_filename }}">
                                            <td class="file_name_cell" ><i class="fa fa-file"></i> 
                                                {{ $js->server_filename }}
                                                @if($js->type == 3)
                                                    @php
                                                        $marked_up['job'][] =  $js->job_id;
                                                        $marked_up['type'][] =  $js->type;
                                                        $marked_up['file'][] =  $js->server_filename;
                                                    @endphp
                                                @endif
                                            </td>
                                            <td>
                                                @if($js->type == 0)
                                                 (<span class="page-quick"><small>{{ $js->page_count }}</span> pages)</small>
                                                @endif
                                            </td>
                                            @if(Auth::user()->role_id != 4)
                                            <td>
                                                <button type="button" data-computed="{{ $js->tax_computed }}" data-source="{{ $js->id }}" class="btn btn-link btn-sm change_quick_upload"><i class="glyphicon glyphicon-cloud-upload"></i> change</button>
                                                <button type="button" class="btn btn-link btn-sm download_source" data-job="{{ $js->job_id }}" data-type="{{ $js->type }}" data-file="{{ $js->server_filename }}"><i class="glyphicon glyphicon-cloud-download"></i> download</button>
                                                <button type="button" class="btn btn-link btn-sm remove-source-quick" data-file="{{$js->id}}" data-placement="left" data-title="Delete File?"><i class="glyphicon glyphicon-trash"></i> remove</button>
                                            </td>
                                            @else
                                            <td>
                                                <button type="button" class="btn btn-link btn-sm download_source" data-job="{{ $js->job_id }}" data-type="{{ $js->type }}" data-file="{{ $js->server_filename }}"><i class="glyphicon glyphicon-cloud-download"></i> download</button>
                                                @if(Auth::user()->role_id == 4 && $js->type == 3)
                                                <button type="button" class="btn btn-link btn-sm remove-source-quick" data-file="{{$js->id}}" data-placement="left" data-title="Delete File?"><i class="glyphicon glyphicon-trash"></i> remove</button>
                                                @endif
                                            </td>
                                            @endif
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                        @if(!empty($marked_up))
                            <b>Marked up Review Files</b>
                            @foreach($marked_up['job'] as $k => $each)
                                <div>
                                    {{ $marked_up['file'][$k] }}
                                    @if(pathinfo($marked_up['file'][$k], PATHINFO_EXTENSION) == 'zip')
                                     - 
                                    <small>
                                        (<a class="view-xbrl-file" data-action="download" data-job="{{ $each }}" data-type="{{ $marked_up['type'][$k] }}" data-file="{{ $marked_up['file'][$k] }}">download</a> | <a class="view-xbrl-file" data-action="view" data-job="{{ $each }}" data-type="{{ $marked_up['type'][$k] }}" data-file="{{ $marked_up['file'][$k] }}">view</a>)
                                    </small>
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn" data-dismiss="modal" data-backdrop="false">Close</button>
          </div>
        </div>

      </div>
    </div>

    <script>
        $(function(){
            $('#write_comment').click(function(){
                var that = $(this);
                that.prop('disabled',true);
                var _token = '{{ csrf_token() }}';
                var comment = $('.comment-area').val();
                var tag = $('.comment-receiver').val() == 'client' ? 1 : 2;
                var job_id  = $('input[name="job_id"]').val();
                
                $.post(URL+'send_comment',{'_token':_token,'comment':coURLmment,'tag':tag,'job_id':job_id})
                    .done(function(result){
                        if(result != '')
                        {
                            var data = $.parseJSON(result);
                            $('.comment-body').append('<tr>'+
                                                            '<td>'+ data.date_time +'</td>'+
                                                            '<td>'+data.action+'</td>'+
                                                            '<td class="full_width">'+data.comment+'</td>'+
                                                        '</tr>');
                            $('.comment-area').val('');
                            that.prop('disabled',false);
                            $('#comment_table').removeClass('hidden');
                            alertModal('Comment Status','You comment has been sent successfully','');
                        } else {
                            alert('error')
                        }
                    })
                return false;
            });

             var job_page = $('.page_field');
                job_page.each(function(){
                   var these = $(this);
                   if(these.attr('data-work') == 'Statutory Account')
                   {
                        $('.stat-page').val(these.val());
                   }

                   if(these.attr('data-work') == 'Tax Computation')
                   {
                        $('.tax-page').val(these.val());
                   }
                });


            $('.vendor-type-list').change(function(){
                var that = $(this);
                var type = that.val();
                if(type != 3)
                {
                    $('.revision-group').find('input').prop('disabled',false);
                    $('.output-group').find('input').prop('disabled',true);
                    $('.output-group').addClass('hidden');
                    $('.revision-group').removeClass('hidden');
                }
                else
                {
                    $('.revision-group').find('input').prop('disabled',true);
                    $('.output-group').find('input').prop('disabled',false);
                    $('.output-group').removeClass('hidden');
                    $('.revision-group').addClass('hidden');
                }
            });
            $('.admin-type-list').change(function(){
                var that = $(this);
                var type = that.val();
                if(type == 0)
                {
                    $('.for-input-file').removeClass('hidden');
                    $('.for-input-file').find('input').prop('disabled',false);
                    $('.not-input-file').addClass('hidden');
                    $('.not-input-file').find('input').prop('disabled',true);
                }
                else
                {
                    $('.not-input-file').removeClass('hidden');
                    $('.not-input-file').find('input').prop('disabled',false);
                    $('.for-input-file').addClass('hidden');
                    $('.for-input-file').find('input').prop('disabled',true);
                }
            });

            $('.category-checkbox').click(function(){
                var that = $(this);
                var category = that.attr('data-type');
                var target = that.attr('data-target');
                if(that.is(':checked'))
                {
                    $(target).find('input').prop('disabled',false);
                }
                else
                {
                    $(target).find('input').prop('disabled',true);
                }
            });

            $('.view-xbrl-file').click(function(){
                var that = $(this);
                var job_id = that.attr('data-job');
                var file = that.attr('data-file');
                var type = that.attr('data-type');
                var action = that.attr('data-action');
                if(/[^.]+$/.exec(file) == 'zip')
                {
                    if(action == 'download')
                    {
                        window.location = URL+'getxbrl-file/'+job_id+'/'+type+'/'+file+'/'+action;
                    }
                    else
                    {
                        window.open(URL+'getxbrl-file/'+job_id+'/'+type+'/'+file+'/'+action,'_blank');
                    }
                }
                
                return false;
            });

            // $( function() {
            //         $( document ).tooltip();
            //     } );
               
                $(document).ready(function(){
                        $('[data-toggle="popover"]').popover();
                        });

            $('.quick-upload-open').click(function(){
                var type = $('.quick-type-list option:selected').val();
                var tax_compute = $('.quick-computation-tax:checked').val();
                var page = 0;
                var user_role = $(this).attr('data-user');
                
                if(user_role == 8)
                {
                    $('#admin-upload-modal').modal('show');
                }
                else if(user_role != 4)
                {
                    if(type == 0)
                    {
                        page = quickTaxCompute(tax_compute);
                    }
                    $('.quick-pages').val(page);
                    $('#quick-upload-modal').modal('show');
                }
                else
                {
                    $('.quick-pages').val(page);
                    $('#vendor-upload-modal').modal('show');
                }
                
            });
            
            $('.quick-computation-tax').change(function(){
                var that = $(this);
                var type = $('.quick-type-list option:selected').val();
                var page = 0;
                if(type == 0)
                {
                    page = quickTaxCompute(that.val())
                }
                $('.quick-pages').val(page);
            })

            $('.quick-type-list').change(function(){
                var that = $(this);
                if(that.val() != 0 && that.val() != 3)
                {
                    $('.computation-group').addClass('hidden');
                }
                else
                {
                    $('.computation-group').removeClass('hidden');
                }
            });

            $('.change_quick_upload').click(function(){
                var that = $(this);
                var source = that.attr('data-source');
                var type = that.closest('tr').attr('data-type');
                var computed = that.attr('data-computed');
                $('.source_id').val(source);
                $('.quick-computation-tax[value="'+computed+'"]').prop('checked',true);
                $('.quick-type-list option[value="'+type+'"]').prop('selected',true);
                $('#quick-upload-modal').modal('show');
                $('#source-files-modal [data-dismiss="modal"]').click();
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

            $('.vendor-file').change(function(){
                var that = $(this);
                var group = that.parent('div');
                if (this.files && this.files[0]) {

                    var fileName = that.val().replace(/.*[\/\\]/, '');
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        group.find('.quick-content').val(e.target.result);
                        group.find('.quick-name').val(fileName);
                        
                    }
                    
                    console.log("File clean:" + fileName);
                    reader.readAsDataURL(this.files[0]);

                }
            });

             $('#admin-source-form').submit(function(){
                var data = new FormData(this);
                $('.quick-submit').text('Uploading...').prop('disabled',true);
                $.ajax({
                        url:URL+'admin-quick-upload',
                        type:'post',
                        data:data,
                        cache:false,
                        contentType: false,
                        processData: false,
                        success:function(result){
                            if(result == 'Enter-Validate'){
                                $('.quick-submit').text('Submit').prop('disabled',false);
                                alertModal('Upload Status','Uploading failed. This File type is not Accepted','');
                            } else {
                                
                            alertModal('Upload Status','Uploaded successfully.','reload');
                            }
                        },
                        fail:function(){
                           
                        }
                 });
                return false;
            });

            $('#vendor-source-form').submit(function(){
                var data = new FormData(this);
                $('.quick-submit').text('Uploading...').prop('disabled',true);
                $.ajax({
                        url:URL+'vendor-quick-upload',
                        type:'post',
                        data:data,
                        cache:false,
                        contentType: false,
                        processData: false,
                        success:function(result){
                            console.log(result)
                            alertModal('Upload Status','Uploaded successfully.','reload');
                        },
                        error : function(err){
                        },
                        fail:function(){
                            $('.quick-submit').text('Submit').prop('disabled',false);
                            alertModal('Upload Status','Uploading failed. Please try again','');
                        }
                 });
                return false;
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
                            // if(result != '')
                            // {
                            //     formEditting();
                            //     var tax_compute = $('.quick-computation-tax:checked').val();
                            //     var data = $.parseJSON(result);


                            //     if(tax_compute == 0)
                            //     {
                            //         $('.statutory_row').find('.page_field').val(data.page);
                            //         $('.statutory_row').find('.file_name').val(data.file_name);
                            //     }
                            //     else
                            //     {
                            //         $('.tax_compute_row').find('.page_field').val(data.page);
                            //         $('.tax_compute_row').find('.file_name').val(data.file_name);
                            //     }
                            //     $('.page_field').change();
                            //     setTimeout(function(){
                            //         $('#add-job-form').submit();
                            //     });
                            // }
                            // else
                            // {
                                
                            // }
                        },
                        fail:function(){
                            $('.quick-submit').text('Submit').prop('disabled',false);
                            alertModal('Upload Status','Uploading failed. Please try again','');
                        }
                });
                return false;
            });

            $('.download_source').click(function(){
                var that = $(this);
                var file = that.attr('data-file');
                var job_id = that.attr('data-job');
                var type = that.attr('data-type');
                window.location = URL+'download-source/'+job_id+'/'+type+'/'+file;
            });

            $('.remove-source-quick').confirmation({
                onConfirm: function(event, element) { 
                    var source_id = element.attr('data-file');
                    var _token = '{{ csrf_token() }}';
                    $.post(URL+'remove-source-file',{'file_id':source_id,'_token':_token})  
                        .done(function(result){
                           element.closest('tr').remove();
                        });
                    return false;
                },
                onCancel: function(event, element) { return false;},
            });
        });

            function quickTaxCompute(type)
            {
                
                var page = 0;
                if(type == 0)
                {
                    page = $('.statutory_row').find('.page_field ').val();
                }
                else
                {
                    page = $('.tax_compute_row').find('.page_field ').val();
                }
                return page;
            }
    </script>

<style>
    label {
      display: inline-block;
      width: 5em;
    }
    </style>

@endsection
