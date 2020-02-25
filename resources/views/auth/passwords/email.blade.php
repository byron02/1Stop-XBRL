@extends('layouts.onboarding')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2 ">
             @if (session('status'))
                <div class="alert alert-success col-md-12">
                    <b>Success!</b> We have e-mailed your password reset link! <a href="{{ url('/') }}"><small>back to login page.</small></a>
                </div>
             @endif
            <div class="clearfix"></div>
            <div class="panel panel-reset ">
                <img src="{{ url('public/img/xbrl_logo_2.png') }}" class="img-responsive form-logo">
                <div class="panel-heading">
                    <h2>FORGOT YOUR PASSWORD?</h2>
                    <p class="text-center">
                        <small>Enter your email address and we'll send you a link to reset your password</small>
                    </p>
                </div>

                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="{{ route('password.email') }}">
                        {{ csrf_field() }}
                        <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                            <div class="col-md-8 col-md-offset-2">
                                <input id="email" type="email" class="form-control input-lg" placeholder="Email address" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <br/>
                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-2 text-center">
                                <a href="{{ url('/') }}" class="btn btn-default">
                                   Cancel
                                </a>
                                <button type="submit" class="btn btn-dark">
                                    Send Reset Link
                                </button>
                            </div>
                        </div>
                    </form>
                     <div class="cert-group">
                        <hr/>
                        <img src="{{ url('public/img/dashboard-logos.png') }}" class="img-responsive cert-logo">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
