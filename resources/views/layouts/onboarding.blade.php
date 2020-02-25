
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>ServiceTrack</title>

    <link href="{{ url('public/css/app.css') }}" rel="stylesheet">
    <link href="{{ url('public/css/frontsite.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{url('public/font-awesome/css/all.css')}}">

     <link href='http://fonts.googleapis.com/css?family=Roboto:400,300,700,500,100' rel='stylesheet' type='text/css'>
    
     <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

  </head>
  <body >
    <div class="container-fluid home-abstract">
        <div class="row mt-100">
            @yield('content')
        </div>
    </div>

    
    <script type="text/javascript">
      @yield('scripts')
    </script>

  </body>
</html>