@extends('layouts.onboarding')

@section('content')
<div class="col-sm-6 col-sm-offset-3 login-wrapper">
        <img src="{{ url('public/img/xbrl_logo_2.png') }}" class="img-responsive form-logo">
        <div class="panel content-login mb-0">
                <div class="col-sm-8 col-sm-offset-2">
                    <h1><br/>Hi {{ Session::get('name') }},</h1>

                    <p>
                        <br/>
                        Thank you for registering with Service Track. Your account is now on queue for review and activation by our Customer Service Representatives.
                        We will notify you once your account is live.
                    </p>

                   

                    <p>
                       Best Regards,<br/>
                       The 1StopXBRL Team
                    </p>

                </div>
                <div class="clearfix"> </div>
                 <div class="text-center">             
                    <hr/>
                    <img src="{{ url('public/img/dashboard-logos.png') }}">
                    <hr/>
                </div>

        </div>
    </div>
</div>
@endsection

