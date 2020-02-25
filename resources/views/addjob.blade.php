@extends('layouts.frontsite')

@section('content')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    @php
        $search = '';
        if(Auth::user()->role_id != 8)
        {
             $search = '?company='.Auth::user()->company_id;
        }
    @endphp
  <script>
        $(function() {

                 $( "#project_name" ).autocomplete({
                        source: URL+"getProjectName{{$search}}",
                });

                 $('#project_name').change(function(){
                    var that = $(this);
                    $.get(URL+'roll-forward-by-projectname/'+that.val())
                        .done(function(result){
                            if(result.length > 0)
                            {
                                var data = $.parseJSON(result);
                                $('input[name="year_end"]').val(data.year_end);
                                $('input[name="purchase_order"]').val(data.purchase_order);
                                $('input[name="registration_number"]').val(data.companies_house_registration_no);
                            }
                        });
                 });

        });
  </script>
    <div class="wrapper">

        @if (session('message'))
            <p class="toast_message">
                {{ session('message') }}
            </p>
        @endif

        @if (session('fail_message'))
            <p class="toast_message_fail">
                {{ session('fail_message') }}
            </p>
        @endif

        <form id="add-job-form" class="form-horizontal" method="POST" action="{{url('/add-jobs/new')}}" enctype="multipart/form-data">
            {{ csrf_field() }}

        <?php
            $rolledForwardJob = session('rolledForwardJob');
            $rolledForwardJobFiles = session('rolledForwardJobFiles');

            if (!is_null(old('rolled_forward_job_files')) && !empty(old('rolled_forward_job_files'))){
                $rolledForwardJobFiles = old('rolled_forward_job_files');
                $rolledForwardJobFiles = collect(json_decode($rolledForwardJobFiles));
            }

            $yearEnd = (isset($rolledForwardJob) && $rolledForwardJob->year_end->timestamp > 0) ?
                $rolledForwardJob->year_end->toDateString() : old('year_end');

            $projectName = isset($rolledForwardJob) && !empty($rolledForwardJob->project_name) ?
                $rolledForwardJob->project_name : old('project_name');

            $houseRegistrationNumber = (isset($rolledForwardJob) && !empty($rolledForwardJob->companies_house_registration_no)) ?
                $rolledForwardJob->companies_house_registration_no : old('registration_number');

            $utrNumber = (isset($rolledForwardJob) && !empty($rolledForwardJob->utr_number)) ?
                $rolledForwardJob->utr_number : old('utr_number');

            $taggingLevel = (isset($rolledForwardJob) && !empty($rolledForwardJob->tagging_level)) ?
                $rolledForwardJob->tagging_level : old('tagged');

            $statutory = old('statutory');
            $taxComputation = old('tax_computation');
            $dormant = old('dormant');
        ?>

        @if(!is_null($rolledForwardJob) && !empty($rolledForwardJob))
        <input id="rolled_forward_job_id" type="hidden" name="rolled_forward_job_id" value="{{$rolledForwardJob->id}}" />
        @else
        <input id="rolled_forward_job_id" type="hidden" name="rolled_forward_job_id" value="{{old('rolled_forward_job_id')}}" />
        @endif

        @if(!is_null($rolledForwardJobFiles) && !empty($rolledForwardJobFiles))
        <input id="rolled_forward_job_files" type="hidden" name="rolled_forward_job_files" value="{{$rolledForwardJobFiles}}" />
        @endif

        <div class="page-holder">

            <div class="clear"></div>

            @if(is_null($rolledForwardJob) || empty($rolledForwardJob))
            <div class="roll-forward-btn" display="">
                <a href="{{url('/select-job')}}" class="styled_btn submit_btn hidden" style="text-decoration:none">
                <span class="btn_icon">ROLL FORWARD</span></a>
            </div>
            @endif

            <div class="clear"></div>

            <div class="col-sm-6 jobs">
                <table cellpadding="0" cellspacing="0" border="0" style="width:auto;">
                    <tr class="{{ Session::get('orig_user') != '' ? 'hidden' : '' }}">
                        <td>
                            <label for="job_title">Company *</label>
                        </td>
                        <td>
                            <div class="{{ $errors->has('company') ? ' has-error' : '' }}">
                                        <select class="form-control price-compute" name="company" required autofocus>
                                            @if(count($companies) > 1)
                                                <option value="">--Please Select--</option>
                                            @endif

                                            <?php

                                            foreach($companies as $company) {

                                                if ((isset($rolledForwardJob) && $rolledForwardJob->company == $company->id) ||
                                                        $company->id == old('company'))  {
                                                    echo '<option value="'.$company['id'].'" selected>'.$company['name'].'</option>';
                                                } else {
                                                    echo '<option value="'.$company['id'].'">'.$company['name'].'</option>';
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
                    </tr>
                    <tr>
                        <td>Turnaround Time</td>
                        <td><select name="turnaround" class="price-compute form-control">
                                <?php
                                foreach($turnaround as $turnaround) {

                                    if($turnaround['id'] == 4) {
                                        echo '<option value="'.$turnaround['id'].'" selected>'.$turnaround['name'].'</option>';
                                    }  else {
                                        echo '<option value="'.$turnaround['id'].'">'.$turnaround['name'].'</option>';
                                    }

                                }
                                ?>
                            </select>

                    </tr>
                    <tr>
                        <td>
                            <label for="job_title">Project Name *</label>
                        </td>
                        <td>
                            <div class="{{ $errors->has('project_name') ? ' has-error' : '' }}">
                            @if($projectName != '')
                                <textarea  class="form-control" name="project_name" required autofocus>{{$projectName}}</textarea>
                            @else
                                <input type="text" class="form-control" name="project_name" title="{{$projectName}}" id="project_name" value="{{$projectName}}" required autofocus>
                            @endif
                                

                                @if ($errors->has('project_name'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('project_name') }}</strong>
                                </span>
                                @endif
                            </div>
                        </td>


                    </tr>
                    <tr>
                        <td>Enter a Purchase Order or Unique Reference</td>
                        <td><input type="text" name="purchase_order" class="form-control" value="{{old('purchase_order')}}"/>
                        </td>
                    </tr>
                    <tr>
                        <td>Ordered By csr</td>
                        <td>
                            <select name="user_id" class="form-control ordered-csr">
                                <option></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Companies House Registration Number</td>
                        <td><input type="text" class="form-control" name="registration_number" value="{{$houseRegistrationNumber}}" />
                        </td>
                    </tr>
                    <tr>
                        {{--Accounting Standard--}}
                        <td>
                            <label for="taxonomy">Accounting Standard *</label>
                            <br/>
                            @foreach($taxonomy_group as $tg)
                                <label class="text-normal">
                                    <input type="radio" class="taxonomy_group price-compute" name="taxonomy_group" value="{{ $tg->id }}"  {{ $tg->group == 'HMRC' ? 'checked' : '' }}>
                                         {{$tg->group}}
                                    </label>
                            @endforeach
                        </td>
                        <td>
                            <select id="taxonomy" name="taxonomy" class="price-compute form-control"> </select>
                        </td>
                    </tr>

                    <tr id="tax_reference">
                        <td>Tax Reference</td>

                        <td>
                        @if(!is_null($rolledForwardJob) && !empty($rolledForwardJob))
                            <input type="text" name="tax_reference" class="form-control" value="{{$rolledForwardJob->tax_reference}}" />
                        @else
                            <input type="text" name="tax_reference" class="form-control" value="{{old('tax_reference')}}" />
                        @endif
                        </td>
                    </tr>

                        
                    <tr>
                        <td><strong>UTR Number</strong> (If Applicable)</td>
                        <td><input type="text" name="utr_number" class="form-control" value="{{$utrNumber}}" />
                        </td>
                    </tr>
                    <tr class="hidden">
                        <td><strong>Were these accounts iXBRL tagged last year?</strong></td>
                        <td>

                             {{-- $taggingLevel == 2 ? 'checked' : '' --}}
                            <input type="radio" value="1" name="tagged" >
                            <label for="tag1">Yes</label>
                            <input type="radio" value="2" name="tagged" checked>
                            <label for="tag2">No</label>
                           
                        </td>
                    </tr>
                   
                    <tr>
                        <td><strong>Do you require your statutory accounts to be Converted?</strong></td>
                        <td>
                             @php
                                $taxComputation = 2;
                                if(isset($rolledForwardJob) && !empty($rolledForwardJob))
                                {
                                    $statutory = $rolledForwardJob->work_type != 1 ? 2 : 1;
                                    $taxComputation = $rolledForwardJob->tax_computation_converted != 1 ? 2 : 1;
                                }
                            @endphp
                            <input type="radio" value="1" name="statutory" id="statutory_yes" data-work-type="statutory" class="price-compute work_radio" {{$statutory != 2 ? 'checked' : ''}}>
                            <label for="statutory_yes">Yes</label>
                            <input type="radio" value="2" name="statutory" id="statutory_no" data-work-type="statutory" class="price-compute work_radio" {{$statutory == 2 ? 'checked' : ''}}>
                            <label for="statutory_no">No</label>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Do you require Tax Computation to be Converted?</strong></td>
                        <td>
                            <input type="radio" value="1" name="tax_computation" id="tax_computation_yes" data-work-type="tax" class="price-compute work_radio" {{ $taxComputation != 2 ? 'checked' : '' }}>
                            <label for="tax_computation_yes">Yes</label>
                            <input type="radio" value="2" name="tax_computation" id="tax_computation_no" data-work-type="tax" class="price-compute work_radio"  {{ $taxComputation == 2 ? 'checked' : '' }}>
                            <label for="tax_computation_no">No</label>

                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="col-sm-6 jobs" style="float:right">
                <table cellpadding="0" cellspacing="0" border="0" style="width:auto;">
                    <tr>
                        <td><strong>Entity Dormant</strong></td>
                        <td>
                            <input type="radio" value="1" name="dormant" id="dormant_yes" {{ $dormant == 1 ? 'checked' : ''}}>
                            <label for="dormant_yes">Yes</label>
                            <input type="radio" value="2" name="dormant" id="dormant_no" {{ $dormant != 1 ? 'checked' : ''}}>
                            <label for="dormant_no">No</label>
            
                        </td>
                    </tr>
                    <tr>
                        <td>Year End</td>
                        <td class="date_input"><input type="text" class="datepicker form-control" name="year_end"  value="{{$yearEnd}}" readonly="readonly"/></td>
                    </tr>
                    <tr>
                        <td>Date Of Director's Report</td>
                        <td class="date_input"><input type="text" class="datepicker form-control" name="director_report_date"  value="{{old('director_report_date')}}" />
                        </td>
                    </tr>
                    <tr>
                        <td>Date Of Auditor's Report</td>
                        <td class="date_input"><input type="text" class="datepicker form-control" name="auditor_report_date"  value="{{old('auditor_report_date')}}" />
                        </td>
                    </tr>
                    <tr>
                        <td>Approval Of Accounts Date</td>
                        <td class="date_input"><input type="text" class="datepicker form-control" name="account_approval_date"  value="{{old('account_approval_date')}}" />
                        </td>
                    </tr>
                    <tr>
                        <td>Name of the Director Approving Accounts</td>
                        <td><input type="text" name="director_approving_account" class="form-control" value="{{old('director_approving_account')}}" />
                        </td>
                    </tr>
                    <tr>
                        <td>Name of the Director Signing Director's Report</td>
                        <td><input type="text" name="director_signing_report" class="form-control" value="{{old('director_signing_report')}}" />
                        </td>
                    </tr>

                    <script>
                        $(document).ready(function() {
                            var taxGroup = $('.taxonomy_group').val();
                            getTaxonomy(taxGroup);

                            $('.taxonomy_group').click(function(){
                                var that = $(this);
                                $('#taxonomy').html('');
                                if(that.is(':checked') == true)
                                {
                                    getTaxonomy(that.val());
                                }
                            });
                            // alert($('.work_radio[data-work-type="tax"][value="{{$taxComputation}}"]').val());
                            setTimeout(function(){
                                $('.work_radio[data-work-type="tax"][value="{{$taxComputation}}"]').click();
                            },300);
                            $('.work_radio').click(function(){
                                var that = $(this);
                                var type = that.attr('data-work-type');
                                var data = that.val();
                                if(that.is(':checked') == true)
                                {
                                    if(type == 'tax')
                                    {

                                        if(data == 1)
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
                                        if(data == 1)
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


                            @if((isset($rolledForwardJob)) && $rolledForwardJob->taxonomy == 7)
                                $("#tax_reference").show();
                            @elseif(!isset($rolledForwardJob) && old('taxonomy') == 7)
                                $("#tax_reference").show();
                            @else
                                $("#tax_reference").hide();
                            @endif


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

                            $.get(URL+'getTaxGroup/'+taxGroup)
                                .done(function(result){
                                    if(result != '')
                                    {
                                        var data = $.parseJSON(result);
                                        var str = '';
                                        $.each(data,function(i,x){
                                            str += '<option value="'+x.id+'">'+x.name+'</option>';
                                        });
                                        $('#taxonomy').html(str);
                                    }
                                })
                        }
                    </script>
                    <tr class="{{ Auth::user()->role_id == 8 || Auth::user()->role_id == 1 || Auth::user()->role_id == 2 }}">
                        <td>Add Comment</td>
                        <td><textarea class="form-control" name="comment" ></textarea></td>
                    </tr>
                </table>
            </div>
            <div class="clearfix"></div>

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
                });

            </script>

            <div class="clearfix"></div>

                <table class="table table-bordered" id="uploads">
                    <tr>
                        <td class="datagrid_header"></td>
                        <td class="datagrid_header">Pages</td>
                        <td class="datagrid_header">Upload relevant Files(Word, Excel, PDF) </td>
                        <td class="datagrid_header" width="200">Price</td>
                    </tr>
                     @php
                        $tcr = 'tax_compute_row '. ($taxComputation == 1 ? 'hidden' : '');
                        $arr = array(
                                        array(
                                                'class_name' => 'statutory_row',
                                                'label' => 'Tell us how many pages are in your statutory accounts',
                                                'data-work' => 'Statutory Account'
                                        ),
                                        array(
                                                'class_name' => $tcr,
                                                'label' => 'Tell us how many pages are in your tax computations',
                                                'data-work' => 'Tax Computation'
                                        ),
                                    );
                        $i = 0;
                    @endphp

                   
                        <tr  class="statutory_row">
                            <td>Tell us how many pages are in your statutory accounts   </td>
                            <td width="10%">
                                {{--<input type="number" name="page[]"  value="{{old('page[]')}}" required autofocus/>--}}


                                <div class="{{ $errors->has('page') ? ' has-error' : '' }}">

                                    <input type="number" class="page_field form-control" data-work="Statutory Account" name="page[]" value="{{ old('page[]') }}" min="1" required autofocus>

                                    @if ($errors->has('page'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('page') }}</strong>
                                    </span>
                                    @endif
                                </div>

                            </td>

                            <td>
                                <input class="file_64 " type="hidden" name="file64[]"/>
                                <input class="file_name" type="hidden" name="file_name[]" />
                                <input class="file_upload form-control" type="file" size="33" name="file[]" required>
                                <input type="hidden" name="row_type[]" value="0">

                            </td>
                            <td class="row_price">0.00</td>
                            
                        </tr>

                        <tr class="tax_compute_row hidden">
                            <td>Tell us how many pages are in your tax computations</td>
                            <td>
                                {{--<input type="number" name="page" value="{{old('page')}}" required autofocus/>--}}


                                <div class="{{ $errors->has('page') ? ' has-error' : '' }}">

                                    <input type="number" disabled class="page_field form-control" data-work="Tax Computation" name="page[]" value="{{ old('page[]') }}" min="1" required autofocus>

                                    @if ($errors->has('page'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('page') }}</strong>
                                    </span>
                                    @endif
                                </div>

                            </td>

                            <td>
                                <input class="file_64" disabled type="hidden" name="file64[]"/>
                                <input class="file_name" disabled type="hidden" name="file_name[]" />
                                <input class="file_upload form-control" disabled type="file" size="33" name="file[]" required>
                                 <input type="hidden" disabled name="row_type[]" value="1">

                            </td>
                            <td class="row_price">0.00</td>
                        </tr>
                    <tfoot>
                        <tr class="active">
                            <td colspan="3" class="text-right" ><strong>VAT</strong></td>
                            <td type="vat" class=""><input type="text" class="price_vat form-control" name="vat" value="0.00" readonly style="border:0;"></td>
                        </tr>
                        <tr class="active">
                            <td colspan="3" class="text-right" ><strong>Total</strong></td>
                            <td type="total" class=""><input type="text" class="total_price form-control" name="total_price" value="0.00" readonly style="border:0;"></td>
                        </tr>
                    </tfoot>
                </table>

        
                
            <div class="form-group text-right">
                <div class="col-lg-12">
                    <button type="button" class="styled_btn" value="Cancel" onclick="window.location='{{url("/")}}'">Cancel</button>
                    <button type="submit" class="styled_btn btn-dark" type="submit"  id="btn_submit">Save</button>
                </div>
            </div>
        </div>
        </form>
    </div>
    <script>
        $(document).ready(function(){
           
            $('#add-job-form').submit(function(){
                var that = $(this);
                var data = new FormData(this);
                $("#loading").show();
                $('#btn_submit').text('Saving...').prop('disabled',true);
                $.ajax({
                        url: that.attr('action'),
                        type: 'POST',
                        data: formData,
                        cache:false,
                        contentType: false,
                        processData: false,
                        success: function(result)
                        {
                            console.log('Success.. Redirect to job list...');
                        }

                    });

                return false;
            });


            setTimeout(function(){
                $('.price-compute').change();
            },300);
            $('.price-compute').change(function(){
                $('.page_field').change();
            });

            $('select[name="company"]').change(function(){
                var that = $(this);
                var company = that.val();
                $.get(URL+'get-users/'+company)
                    .done(function(result){
                        if(result != '')
                        {
                            var data = $.parseJSON(result);
                            var str = '';
                            for(var i = 0;i < data.length;i++)
                            {
                                var x = data[i];
                                str += '<option data-country="'+x.country+'" value="'+x.id+'">'+ x.first_name+' '+ x.last_name +'</option>';
                            }
                            $('.ordered-csr').html(str);
                        }
                    });
            });

            // $(".page_field").change(function(){
            //     var that = $(this);
            //     var page = that.val() != '' ? parseInt(that.val()) : 0;
                
            // });

            $('.page_field').change(function(){
                var that = $(this);
                var page = that.val() != '' ? parseInt(that.val()) : 0;
                var company = $('select[name="company"]').val();
                    company = company != '' ? company : 0;
                var turnaround = $('select[name="turnaround"]').val();
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

                // alert(URL+'job-pricing/'+page+'/'+company+'/'+turnaround+'/'+work_type+'/'+taxonomy+'/');
                console.log(URL+'job-pricing/'+page+'/'+company+'/'+turnaround+'/'+work_type+'/'+taxonomy+'/');
                $.get(URL+'job-pricing/'+page+'/'+company+'/'+turnaround+'/'+work_type+'/'+taxonomy+'/')
                    .done(function(result){
                        var data = $.parseJSON(result);
                        if(typeof data.total_price != 'undefined')
                        {
                            price = parseFloat(data.total_price).toFixed(2);
                        }
                        that.closest('tr').find('.row_price').text(price);
                        // console.log(URL+'company-country/'+company+'/')
                        $.get(URL+'company-country/'+company+'/')
                            .done(function(country){
                                 country_code = country[0].country;
                                computeTotalPrice(country_code);
                            })
                    });
            });
        });

      
        // $('#input_price').change(function(){
        //     var that = $(this);
        //     newValue = '';
        //     var newValue = that.val() != '' ? parseInt(that.val()) : 0;
        //     that.closest('tr').find('#input_value').text(newValue);
           
        //     console.log(newValue)
        // })


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
            $('.price_vat').attr('value',vat.toFixed(2));
            $('.total_price').attr('value',total.toFixed(2));
        }
    </script>
<div id="loading">
    <img alt="ServiceTrack" src="{{ url('public/img/loading.gif') }}">
</div>
    <style>div#loading {
            position: fixed;
            padding-top: 15%;
            left: 0;
            bottom: 0;
            right: 0;
        }#loading{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);text-align:center}</style>
@endsection

