@extends('layouts.onboarding')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src='https://www.google.com/recaptcha/api.js'></script>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
@section('content')
    <div class="col-sm-6 col-md-offset-3 alert alert-danger error-message-alert hidden">
      <strong>Registration failed.</strong><br/>
      <ul class="error_message"></p>
    </div>
    <div class="clearfix"></div>
    <div class="col-sm-6 col-sm-offset-3 login-wrapper col-xs-10 col-xs-offset-1">
        <div class="col-sm-5 welcome-note hidden-xs">
            <h1>1Stopxbrl Sign up</h1>
            <p>Register now in 3 easy step. </p>
            <ul class="list-group">
              <li class="list-group-item active" data-nav="#account-details">
                   <span class="step-icon"><i class="fa fa-key"></i></span>
                    Account Details
                    <span class="step-icon-notif pull-right">
                        <i class="fa fa-chevron-right"></i>
                    </span>
                    <div class="clearfix"></div>
              </li>
              <li class="list-group-item" data-nav="#personal_information">
                  <span class="step-icon"><i class="fa fa-user"></i></span>
                    Personal Information
                    <span class="step-icon-notif pull-right hidden">
                        <i class="fa fa-chevron-right "></i>
                    </span>
                    <div class="clearfix"></div>
              </li>
              <li class="list-group-item" data-nav="#contact_details">
                   <span class="step-icon"><i class="fa fa-map-marker-alt"></i></span>
                    Contact Information
                    <span class="step-icon-notif pull-right hidden">
                        <i class="fa fa-chevron-right"></i>
                    </span>
                    <div class="clearfix"></div>
              </li>
            </ul>
            <p class="mt-100 text-center">
                <small>
                    <a class="text-light" href="{{ url('/') }}">
                        <i class="fa fa-undo"></i>
                        back to login page
                    </a>
                </small>
            </p>
        </div>

        <div  id="form-container" class="col-sm-7 form-display">
            <br class="hidden-md">
            <img src="{{ url('public/img/xbrl_logo_2.png') }}" class="img-responsive form-logo">
                    <div class="col-lg-12">
                        <form class="form-horizontal" method="POST" action="{{ route('register') }}" id="registration-form">
                            {{ csrf_field() }}
                            <div class="form-group" id="account-details">
                                <div class="col-md-12">
                                    <h2 class="text-left">Account Details </h2>
                                    <!-- Username -->
                                    <div class="form-group ">
                                        <div class="col-sm-12 {{ $errors->has('username') ? ' has-error' : '' }}">
                                            <label for="username">Username *</label>
                                            <input id="username" type="text" class="form-control" name="username" value="{{ old('username') }}" required autofocus>
                                            <span id="error_username"></span>

                                            <!-- @if ($errors->has('username'))
                                                <span id="error_username" class="help-block">
                                                    <strong>{{ $errors->first('username') }}</strong>
                                                </span>
                                            @endif -->
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-12 {{ $errors->has('email') ? ' has-error' : '' }}">
                                            <label for="email">Email Address *</label>
                                            <input id="email" type="email" name="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
                                            <span id="error_email"></span>
                                            <!-- @if ($errors->has('email'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('email') }}</strong>
                                                </span>
                                            @endif -->
                                        </div>
                                    </div>
                                    <!-- END Username -->
                                    <!-- Password -->
                                    <div class="form-group">
                                        <div class="col-sm-12 {{ $errors->has('password') ? ' has-error' : '' }}">
                                            <label for="password">Password *</label>
                                            <input id="password" type="password" class="form-control" name="password" value="{{ old('password') }}" required autofocus>

                                            @if ($errors->has('password'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('password') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-12 {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                            <label for="password_confirmation">Confirm Password *</label>
                                            <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" value="{{ old('password_confirmation') }}" required autofocus>

                                            @if ($errors->has('password_confirmation'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-lg-12 text-center">
                                            <button class="styled_btn step-btn" id="next_step" name="next_step" type="button" data-origin="#account-details" data-target="#personal_information">Continue to Next Step <i class="fa fa-chevron-right"></i> </button>
                                        </div>
                                    </div> 
                                </div>
                            </div>
                            

                            <!-- ******PERSONAL INFORMATION (START) ***** -->
                            </br>    
                            <div class="form-group hidden" id="personal_information">
                                <div class="col-lg-12">
                                    <h2 class="text-left">Personal Information </h2>

                                        <!-- First Name -->
                                    <div class="form-group">
                                        <div class="col-sm-12 {{ $errors->has('first_name') ? 'has-error' : '' }}">
                                            <label for="first_name">First Name *</label>
                                            <input id="first_name" type="text" class="form-control" name="first_name" value="{{ old('first_name') }}" required autofocus>

                                            @if ($errors->has('first_name'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('first_name') }}</strong>
                                                </span>
                                            @endif
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-12 {{ $errors->has('last_name') ? ' has-error' : '' }}">
                                            <label for="last_name">Last Name *</label>
                                            <input id="last_name" type="text" class="form-control" name="last_name" value="{{ old('last_name') }}" required autofocus>

                                            @if ($errors->has('last_name'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('last_name') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                        <!-- END First Name -->

                                    <!-- job-title -->
                                    <div class="form-group">
                                        <div class="col-sm-12 {{ $errors->has('job_title') ? ' has-error' : '' }}">
                                            <label for="job_title">Job Title *</label>
                                            <input id="job_title" type="text" class="form-control" name="job_title" value="{{ old('job_title') }}" required autofocus>

                                            @if ($errors->has('job_title'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('job_title') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                     <div class="form-group">
                                        <div class="col-sm-12 {{ $errors->has('company_name') ? ' has-error' : '' }}">
                                            <label for="company_name">Company Name *</label>
                                            <input id="company_name" type="text" class="form-control" name="company_name" value="{{ old('company_name') }}" required autofocus>

                                            @if ($errors->has('company_name'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('company_name') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <div class="col-lg-12 text-center">
                                            <button class="styled_btn step-btn" type="button" data-origin="#personal_information" data-target="#account-details" data-previous="#account-details">
                                                <i class="fa fa-chevron-left"></i> Back to Previous Step
                                            </button>
                                            <button class="styled_btn step-btn" type="button" data-origin="#personal_information" data-target="#contact_details">Continue to Next Step <i class="fa fa-chevron-right"></i> </button>
                                        </div>
                                    </div> 
                                </div>
                            </div>


                            <!-- ******Timezone Details (START) ***** -->
                            <div class="form-group hidden" id="contact_details">  
                                <h2 class="text-left">Contact Information</h2>
                                <!-- address_line_1 -->
                                <div class="form-group">
                                    <div class="col-sm-12 {{ $errors->has('address_line_1') ? ' has-error' : '' }}">
                                        <label for="address_line_1">Address Line 1 *</label>
                                        <input id="address_line_1" type="text" class="form-control" name="address_line_1" value="{{ old('address_line_1') }}" required autofocus>

                                        @if ($errors->has('address_line_1'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('address_line_1') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-12 {{ $errors->has('address_line_2') ? ' has-error' : '' }}">
                                        <label for="address_line_1">Address Line 2 </label>
                                        <input id="address_line_1" type="text" class="form-control" name="address_line_2" value="{{ old('address_line_2') }}"  autofocus>

                                        @if ($errors->has('address_line_2'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('address_line_2') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-12 {{ $errors->has('address_line_3') ? ' has-error' : '' }}">
                                        <label for="address_line_1">Address Line 3 </label>
                                        <input id="address_line_1" type="text" class="form-control" name="address_line_3" value="{{ old('address_line_3') }}"  autofocus>

                                        @if ($errors->has('address_line_3'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('address_line_3') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- city -->
                                <div class="form-group ">
                                    <div class="col-sm-6 {{ $errors->has('country') ? ' has-error' : '' }}" >
                                        <label for="country">Country *</label>
                                        <select name="country" class="form-control" required>
                                            <option selected disabled value="">Select Country</option>
                                        <?php
                                            foreach($countries as $country) {
                                               echo  '<option value="'.$country->id.'">'.$country->name.'</option>';
                                            }

                                            ?>
                                        </select>

                                        @if ($errors->has('country'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('country') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="pl-0 col-sm-3 {{ $errors->has('city') ? ' has-error' : '' }}">
                                        <label for="city">County *</label>
                                        <input id="city" type="text" class="form-control" name="city" value="{{ old('city') }}" required autofocus>

                                        @if ($errors->has('city'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('city') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="pl-0 col-sm-3 {{ $errors->has('post_code') ? ' has-error' : '' }}">
                                        <label for="post_code">Post Code *</label>
                                        <input id="post_code" type="text" class="form-control" name="post_code" value="{{ old('post_code') }}" required autofocus>

                                        @if ($errors->has('post_code'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('post_code') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <!-- END city -->


                           

                                <!-- mobile_number -->
                                <div class="form-group">
                                    <div class="col-sm-6 {{ $errors->has('telephone_number') ? ' has-error' : '' }}">
                                        <label for="telephone_number">Telephone Number *</label>
                                        <input id="telephone_number" type="text" class="form-control" name="telephone_number" value="{{ old('telephone_number') }}" required autofocus>

                                        @if ($errors->has('telephone_number'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('telephone_number') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-sm-6 {{ $errors->has('mobile_number') ? ' has-error' : '' }}">
                                        <label for="mobile_number">Mobile Number *</label>
                                        <input id="mobile_number" type="text" class="form-control" name="mobile_number" value="{{ old('mobile_number') }}" required autofocus>

                                        @if ($errors->has('mobile_number'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('mobile_number') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                <!-- END mobile_number -->

                                <!-- timezone -->
                                <div class="form-group">
                                    <div class="col-sm-12 {{ $errors->has('timezone') ? ' has-error' : '' }}">
                                         <label for="timezone">Select Timezone *</label><br/>
                                            @php
                                                $max = count($timezones);
                                                $half_tz = ceil(count($timezones) /2);
                                            @endphp
                                            @foreach($timezones as $k => $timezone)
                                                @php 
                                                    $timezoneNameImage = strtolower($timezone->name).'.png';
                                                @endphp
                                                 <div class="col-lg-3">
                                                    <label>
                                                        <input type="radio" class="" name="timezone" required value="{{$timezone->id}}">
                                                        <img class="img_timezone" src="public/img/{{$timezoneNameImage}}" />{{$timezone->name}}
                                                    </label>
                                                </div>

                                            @endforeach
                                        @if ($errors->has('timezone'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('timezone') }}</strong>
                                        </span>
                                        @endif
                                        <div class="clearfix"></div>
                                        <div class="text-center hidden">
                                            <br/>
                                            <small><label><input type="checkbox" class="term-check"> I agree to the <a href="#">Terms and Conditions</a></label></small>
                                        </div>
                                </div>
                                <br>
                                <div class="g-recaptcha col-sm-12" data-callback="correctCaptcha" data-expired-callback="capdisable"
                                        data-sitekey="{{env('GOOGLE_RECAPTCHA_KEY')}}">
                                </div>
                                <!-- END timezone -->
                                 <div class="form-group">
                                    <div class="col-lg-12 text-center">
                                        <br/>
                                        <button class="styled_btn step-btn" type="button" data-origin="#contact_details" data-target="#personal_information" data-previous="#personal_information">
                                            <i class="fa fa-chevron-left"></i> Back to Previous Step
                                        </button>
                                        <button class="styled_btn step-btn" type="submit" data-origin="#contact_details" data-target="#finish" id="button1">Finish Sign up <i class="fa fa-check"></i> </button>
                                    </div>
                                </div> 
                            </div>
                        </div>
                        <div class="form-group hidden" id="finish">
                            <div class="text-center mt-100">
                                <p>Saving your information.</p>
                                <p><i class="fa fa-spin fa-sync-alt fa-5x"></i></p>
                                <p>Please wait...</p>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-1stop btn-dark register-submit hidden">Register</button>
                    </form>
                </div>
            </div>
            <div class="clearfix"> </div>
        </div>
        <div class="clearfix"></div>
           
        <script>

                $("form").each(function() {
                    $(this).find(':input[type="submit"]').prop('disabled', true);
                });
                function correctCaptcha() {
                    $("form").each(function() {
                        $(this).find(':input[type="submit"]').prop('disabled', false);
                    });
                }
                function capdisable() {
                    $("form").each(function() {
                        $(this).find(':input[type="submit"]').prop('disabled', true);
                    });
                }
            $(function(){
               var reg_error = window.localStorage.getItem('reg_error');
               var form_data = window.localStorage.getItem('form_data');
                    // console.log(f_data);
               
               if(reg_error != null)
               {
                    var err = JSON.parse(reg_error);
                    var err_str = '';
                    for(var i in err)
                    {
                        err_str+= '<li>'+err[i]+'</li>';
                    }
                    $('.error_message').html(err_str);
                    $('.error-message-alert').removeClass('hidden');

                    var f_data = JSON.parse(form_data);
               
                    $.each(f_data,function(index,e){
                        if(index != 'password' && index != 'password_confirmation')
                        {
                            $('input[name="'+index+'"]').val(f_data[index]);
                        }

                        if(index == 'country')
                        {
                            $('select[name="'+index+'"] option[value="'+f_data[index]+'"]').prop('selected',true);
                        }

                        if(index == 'timezone')
                        {
                            $('input[value="'+f_data[index]+'"]').prop('checked',true);
                        }
                    });

                    window.localStorage.removeItem('reg_error');
                    window.localStorage.removeItem('form_data');
               }

               

                $('.term-check').change(function(){
                    if($(this).is(':checked'))
                    {
                        $('.step-btn').prop('disabled',false);
                    }
                    else
                    {
                        $('.step-btn').prop('disabled',true);
                    }
                });

                $('input[type="email"]').change(function(){
                    var that = $(this);
                    if(!isEmail(that.val()))
                    {
                        setTimeout(function(){
                           that.focus();
                        },300);
                        return false;
                    }
                });


                $('.step-btn').click(function(){
                    var that = $(this);
                    var target =  that.attr('data-target');
                    var origin =  that.attr('data-origin');
                    var previous = that.attr('data-previous');
                    var flag = false;

                    if(typeof previous == 'undefined')
                    {
                        $(origin).find('input').each(function(){
                            if($(this).val() == '')
                            {
                                flag = true;
                                $(this).focus();
                                return false;
                            }
                        });

                        $(origin).find('select').each(function(){
                            if($(this).val() == '')
                            {
                                flag = true;
                                $(this).focus();
                                return false;
                            }
                        });
                        
                        if(flag == true)
                        {
                            return false;
                        }
                    }
                   
                   if(target != '#finish')
                    {
                        $(origin).addClass('hidden');
                        $('[data-nav="'+origin+'"]').removeClass('active');
                        $('[data-nav="'+target+'"]').addClass('active');
                    }
                   if(typeof previous != 'undefined')
                   {
                        $(previous).removeClass('hidden');
                   }
                   else
                   {
                        if(target == '#finish')
                        {
                           $('.register-submit').click();
                           return false;
                        }
                        $('[data-nav="'+origin+'"]').find('.step-icon-notif i').removeClass('fa-chevron-right').addClass('fa-check');
                        $('[data-nav="'+target+'"]').find('.step-icon-notif').removeClass('hidden');
                        $(target).removeClass('hidden');
                   }
                  
                  
                });
            });

            function delete_cookie(name) {
                document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT;';
            }

            function isEmail(email) {
              var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
              return regex.test(email);
            }

            $(document).ready(function(){
                $('#username').blur(function(){
                    var error_username = '';
                    var username = $('#username').val();
                    var _token = $('input[name="_token"]').val();
                    var url = "{{ route('register.check') }}";

                    $.ajax({
                        url: url,
                        method: "POST",
                        data: {username: username, _token: _token},
                        success: function(result)
                        {
                            if(result == 'unique'){
                                $('#error_username').html('<label class="text-success">Username Available</label>');
                                $('#username').removeClass('has-error');
                                $('#next_step').attr('disabled',false);
                            } else {
                                $('#error_username').html('<label class="text-danger">Username Not Available</label>');
                                $('#email').addClass('has-error');
                                $('#next_step').attr('disabled',true);
                            }
                        }
                    })
                    
                });
            });

            $(document).ready(function(){
                $('#email').blur(function(){
                    var error_email = '';
                    var email = $('#email').val();
                    var _token = $('input[name="_token"]').val();
                    var url = "{{ route('register.checkEmail') }}";

                    $.ajax({
                        url: url,
                        method: "POST",
                        data: {email: email, _token: _token},
                        success: function(result)
                        {
                            if(result == 'unique'){
                                $('#error_email').html('<label class="text-success">Email Available</label>');
                                $('#email').removeClass('has-error');
                                $('#next_step').attr('disabled',false);
                            } else {
                                $('#error_email').html('<label class="text-danger">Email Not Available</label>');
                                $('#email').addClass('has-error');
                                $('#next_step').attr('disabled',true);
                            }
                        }
                    })
                    
                });
            });

        </script>
@endsection

