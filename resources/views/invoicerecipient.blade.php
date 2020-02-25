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
            <h2 class="content-title">Add Invoice Recipient</h2>
            <div class="datagrid_wrap">
                <a href="#" id="copy-info">Click here if the information you wanted to input is the same as your information</a>    ﻿
               
            </div>
            <form id="recipient_form">
                {{ csrf_field() }}
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Company Name *</label>
                        <input type="text" class="form-control" name="company_name" id="company_name" value="{{ isset($recipient) ? $recipient->company_name : ''}}">
                        <input type="hidden" class="form-control" name="company_id" id="company_id" value="{{ isset($recipient) ? $recipient->company_id : 0}}">
                    </div>
                    <div class="form-group">
                        <label>Full name of the person responsible for receiving invoices *</label>
                        <input type="text" class="form-control" name="full_name" id="full_name" value="{{ isset($recipient) ? $recipient->fullname : ''}}">
                    </div>
                    <div class="form-group">
                        <label>Job Title *</label>
                        <input type="text" class="form-control" name="job_title" id="job_title" value="{{ isset($recipient) ? $recipient->job_title : ''}}">
                    </div>
                    <div class="form-group">
                        <label>Address Line 1 *</label>
                        <input type="text" class="form-control" name="address_line_1" id="address_line_1" value="{{ isset($recipient) ? $recipient->address_line_1 : ''}}">
                    </div>
                    <div class="form-group">
                        <label>Address Line 2</label>
                        <input type="text" class="form-control" name="address_line_2" id="address_line_2" value="{{ isset($recipient) ? $recipient->address_line_2 : ''}}">
                    </div>
                    <div class="form-group">
                        <label>Address Line 3</label>
                        <input type="text" class="form-control" name="address_line_3" id="address_line_3" value="{{ isset($recipient) ? $recipient->address_line_3 : ''}}">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label>City *</label>
                        <input type="text" class="form-control" name="city" id="city" value="{{ isset($recipient) ? $recipient->city : ''}}">
                    </div>
                     <div class="form-group">
                        <label>Country *</label>
                        <select class="form-control" name="country" id="country" >
                            <option></option>
                            @foreach($country as $ct)
                                <option {{ isset($recipient) && $recipient->country == $ct->id? 'selected' : ''}} value="{{ $ct->id }}">{{ $ct->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Post Code *</label>
                        <input type="text" class="form-control" name="post_code" id="post_code" value="{{ isset($recipient) ? $recipient->post_code : ''}}">
                    </div>
                    <div class="form-group">
                        <label>Telephone Number *</label>
                        <input type="text" class="form-control" name="telephone_no" id="telephone_number" value="{{ isset($recipient) ? $recipient->telephone_number : ''}}">
                    </div>
                    <div class="form-group">
                        <label>Mobile Number *</label>
                        <input type="text" class="form-control" name="mobile_number" id="mobile_number" value="{{ isset($recipient) ? $recipient->mobile_number : ''}}">
                    </div>
                    <div class="form-group">
                        <label>Email Address *</label>
                        <input type="text" class="form-control" name="email_address" id="email" value="{{ isset($recipient) ? $recipient->email : ''}}">
                        <small>
                            allows comma seperated email addresses
                            e.g. bob@company.com,charlie@company.com
                        </small>
                    </div>
                </div>
                <div class="clearfix"></div>
                <hr/>
                <div class="text-center">
                     <button class="btn btn-danger">Submit</button>
                     @if(!isset($recipient))
                      <input type="reset" class="btn btn-default" value="Reset Form">
                     @endif
                     <a href="{{url('/')}}" class="btn btn-default" type="button">Cancel</a>
                </div>
            </form>
        </div>


    </div>

    <script>
    $(function(){
        $('#recipient_form').submit(function(){
            var that = $(this);
            var data = that.serialize();
            $.post(URL+'save-recipient',data)
                .done(function(result){
                    alertModal('Invoice Recipient Status','Updated successfully.','reload');
                });
            return false;
        });

        $('#copy-info').click(function(){
            $.get(URL+'copy-company')
                .done(function(result){
                    if(result != '')
                    {
                        var data = $.parseJSON(result);
                        $.each(data[0],function(i,x){
                            if(i != 'country')
                            {
                                $('#'+i).val(x);
                            }
                            else
                            {
                                 $('#'+i+' option[value="'+x+'"]').prop('selected',true);
                            }
                        });
                    }
                });
            return false;
        });
    })
    </script>
    
    @endsection