@extends('layouts.onboarding')

@section('content')
    <div class="col-sm-6 col-sm-offset-3">
        <div class="panel panel-1stop border-radius-0">
            <img src="{{ url('img/xbrl_logo_2.png') }}" class="img-responsive form-logo">
            <div class="panel-heading">
                <h2 class="text-center">Create 1stopxbrl account</h2>
            </div>

            <div id="registration-form" class="panel-body">
                <form class="form-horizontal" method="POST" action="{{ route('register') }}">
                    {{ csrf_field() }}
                    <div class="col-md-10">
                        <!-- ******CREATE NEW PROFILE SECTION***** -->
                        <p class="section-title">Account Details</p>

                        <!-- Username -->
                        <div class="form-group ">
                            <div class="col-sm-6 {{ $errors->has('username') ? ' has-error' : '' }}">
                                <label for="username">Username *</label>
                                <input id="username" type="text" class="form-control" name="username" value="{{ old('username') }}" required autofocus>

                                @if ($errors->has('username'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('username') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col-sm-6 {{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email">Email Address *</label>
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!-- END Username -->
                        <!-- Password -->
                        <div class="form-group">
                            <div class="col-sm-6 {{ $errors->has('password') ? ' has-error' : '' }}">
                                <label for="password">Password *</label>
                                <input id="password" type="password" class="form-control" name="password" value="{{ old('password') }}" required autofocus>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col-sm-6 {{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                <label for="password_confirmation">Confirm Password *</label>
                                <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" value="{{ old('password_confirmation') }}" required autofocus>

                                @if ($errors->has('password_confirmation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!-- END Password -->

                    

                    <!-- ******PERSONAL INFORMATION (START) ***** -->
                    </br>    
                    <p class="section-title"> User Personal Information </p>

                        <!-- First Name -->
                    <div class="form-group">
                        <div class="col-sm-6 {{ $errors->has('first_name') ? 'has-error' : '' }}">
                            <label for="first_name">First Name *</label>
                            <input id="first_name" type="text" class="form-control" name="first_name" value="{{ old('first_name') }}" required autofocus>

                            @if ($errors->has('first_name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('first_name') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="col-sm-6 {{ $errors->has('last_name') ? ' has-error' : '' }}">
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
                            <div class="col-sm-6 {{ $errors->has('job_title') ? ' has-error' : '' }}">
                                <label for="job_title">Job Title *</label>
                                <input id="job_title" type="text" class="form-control" name="job_title" value="{{ old('job_title') }}" required autofocus>

                                @if ($errors->has('job_title'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('job_title') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col-sm-6 {{ $errors->has('company_name') ? ' has-error' : '' }}">
                                <label for="company_name">Company Name *</label>
                                <input id="company_name" type="text" class="form-control" name="company_name" value="{{ old('company_name') }}" required autofocus>

                                @if ($errors->has('company_name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('company_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!-- END job-title -->

                      

                        <!-- address_line_1 -->
                        <div class="form-group{{ $errors->has('address_line_1') ? ' has-error' : '' }}">
                            <div class="col-sm-3 ">
                                <label for="address_line_1">Address Line 1 *</label>
                            </div>

                            <div class="col-sm-6">
                                <input id="address_line_1" type="text" class="form-control" name="address_line_1" value="{{ old('address_line_1') }}" required autofocus>

                                @if ($errors->has('address_line_1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('address_line_1') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!-- END address_line_1 -->

                        <!-- address_line_2 -->
                        <div class="hidden form-group{{ $errors->has('address_line_2') ? ' has-error' : '' }}">
                            <div class="col-sm-3 ">
                                <label for="address_line_2">Address Line 2</label>
                            </div>

                            <div class="col-sm-6">
                                <input id="address_line_2" type="text" class="form-control" name="address_line_2" value="{{ old('address_line_2') }}" autofocus>

                                @if ($errors->has('address_line_2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('address_line_2') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!-- END address_line_2 -->

                        <!-- address_line_3 -->
                        <div class="hidden form-group{{ $errors->has('address_line_3') ? ' has-error' : '' }}">
                            <div class="col-sm-3 ">
                                <label for="address_line_3">Address Line 3</label>
                            </div>

                            <div class="col-sm-6">
                                <input id="address_line_3" type="text" class="form-control" name="address_line_3" value="{{ old('address_line_3') }}" autofocus>

                                @if ($errors->has('address_line_3'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('address_line_3') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!-- END address_line_3 -->

                        <!-- city -->
                        <div class="form-group{{ $errors->has('city') ? ' has-error' : '' }}">
                            <div class="col-sm-3 ">
                                <label for="city">City *</label>
                            </div>

                            <div class="col-sm-6">
                                <input id="city" type="text" class="form-control" name="city" value="{{ old('city') }}" required autofocus>

                                @if ($errors->has('city'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('city') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!-- END city -->

                        <!-- country -->
                        <div class="form-group{{ $errors->has('country') ? ' has-error' : '' }}">
                            <div class="col-sm-3 ">
                                <label for="country">Country *</label>
                            </div>

                            <div class="col-sm-6"  >

                                <select name="country" class="form-control">
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
                        </div>
                        <!-- END country -->

                        <!-- post_code -->
                        <div class="form-group{{ $errors->has('post_code') ? ' has-error' : '' }}">
                            <div class="col-sm-3 ">
                                <label for="post_code">Post Code *</label>
                            </div>

                            <div class="col-sm-6">
                                <input id="post_code" type="text" class="form-control" name="post_code" value="{{ old('post_code') }}" required autofocus>

                                @if ($errors->has('post_code'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('post_code') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!-- END post_code -->

                        <!-- mobile_number -->
                        <div class="form-group{{ $errors->has('telephone_number') ? ' has-error' : '' }}">
                            <div class="col-sm-3 ">
                                <label for="telephone_number">Telephone Number *</label>
                            </div>

                            <div class="col-sm-6">
                                <input id="telephone_number" type="text" class="form-control" name="telephone_number" value="{{ old('telephone_number') }}" required autofocus>

                                @if ($errors->has('telephone_number'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telephone_number') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!-- END mobile_number -->

                        <!-- mobile_number -->
                        <div class="form-group{{ $errors->has('mobile_number') ? ' has-error' : '' }}">
                            <div class="col-sm-3 ">
                                <label for="mobile_number">Mobile Number *</label>
                            </div>

                            <div class="col-sm-6">
                                <input id="mobile_number" type="text" class="form-control" name="mobile_number" value="{{ old('mobile_number') }}" required autofocus>

                                @if ($errors->has('mobile_number'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('mobile_number') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <!-- END mobile_number -->


                    <!-- ******PERSONAL INFORMATION (END) ***** -->


                    <!-- ******Timezone Details (START) ***** -->
                    </br>    
                    <p class="section-title"> Timezone Details </p>
                    <!-- timezone -->
                    <div class="form-group{{ $errors->has('timezone') ? ' has-error' : '' }}">

                        <div class="col-sm-3 ">
                            <label for="timezone">Select Timezone *</label>
                        </div>
                        <div class="col-sm-8">
                            @php
                                $half_tz = ceil(count($timezones) /2);
                            @endphp
                            @foreach($timezones as $k => $timezone)
                                @php 
                                    $timezoneNameImage = strtolower($timezone->name).'.png';
                                    if($k == $half_tz)
                                    {
                                        echo '<br/>';
                                    }
                                @endphp
                                
                                <label>
                                    <input type="radio" name="timezone" required value="{{$timezone->id}}">
                                    <img class="img_timezone" src="../img/{{$timezoneNameImage}}" />{{$timezone->name}}
                                </label>

                            @endforeach

                            @if ($errors->has('timezone'))
                                <span class="help-block">
                                <strong>{{ $errors->first('timezone') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <!-- END timezone -->

                <!-- ******Timezone Details (START) ***** -->
                    <div class="form-group">
                        <div class="col-sm-10 text-right">
                            <a href="{{ url('/') }}" class="btn btn-default">Cancel</a>
                            <button type="submit" class="btn btn-1stop btn-dark">Register</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

