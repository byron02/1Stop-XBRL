@extends('layouts.onboarding')

@section('content')
    
    <div class="col-sm-8 col-sm-offset-2 login-wrapper col-xs-10 col-xs-offset-1">
        <div class="col-sm-7 welcome-note hidden-xs">
            <h1>Welcome to ServiceTrack! </h1>

            <p>
                <b>Our unique XBRL delivery system</b> allows you to easily upload your source files (in whatever format you prepare them - we accept most formats) and download your converted documents. 
                For the HMRC and Companies House we convert into the following file types:
            </p>

            <ol class="mb-20">
                <li class="mb-20"> Legal entity (statutory) Accounts into iXBRL </li>
                <li> Corporation tax computation into iXBRL </li>
            </ol>


            <p>
                HMRC Recognised Vendor ID: 1698 </br>
                Now fully Recognised for Account and Tax Computation tagging </br>
                Authorised Filing Agent Number: J6507A </br>
            </p>

        </div>

        <div  id="form-container" class="col-sm-5 form-display">
            <br class="hidden-md">
            <img src="{{ url('public/img/xbrl_logo_2.png') }}" class="img-responsive form-logo">
            <h1 class="text-center"> Sign in to your account </h1>
                    <div class="col-lg-12">
                        <form method="POST" action="{{ route('login') }}">
                            {{ csrf_field() }}

                            <div class="form-group{{ $errors->has('username') ? 'has-error' : '' }}">
                                <label for="username"></label>

                                <input id="username" type="text" class="form-control input-lg"  placeholder="Username" name="username" value="{{ old('username') }}" required autofocus>

                                @if ($errors->has('username'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('username') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group{{ $errors->has('password') ? 'has-error' : '' }}">
                                <label for="password" ></label>
                                <input id="password" type="password" class="form-control input-lg" placeholder="Password" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group">
                                <button value="Login" type="submit" class="btn btn-1stop btn-dark w-100">Login</button>
                            </div>
                             <div class="pull-left">
                                <small><a class="forgot-password" href="{{ route('password.request') }}">Forgot Password?</a></small>
                            </div>
                            <div class="pull-right">
                                <small>Do you have an account? <a id="register-now" href="{{ url('/register') }}">Register Now!</a></small>
                            </div>
                            <div class="clearfix"></div>
                        </form>
                        <div class="cert-group">
                            <hr/>
                            <img src="{{ url('public/img/dashboard-logos.png') }}" class="img-responsive cert-logo">
                        </div>
                    </div>
                    
                </div>
                <div class="clearfix"> </div>
            </div>
            <div class="clearfix"></div>
           

@endsection

