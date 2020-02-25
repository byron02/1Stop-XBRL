<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" >
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>ServiceTrack</title>

    <link href="{{ url('public/css/app.css') }}" rel="stylesheet">
    <!-- <link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css"> -->

    <link rel="stylesheet" href="{{ url('public/css/jquery.fancybox.css') }}"/>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="{{url('public/font-awesome/css/all.css')}}">
    <!-- <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous"> -->
    <link href="{{ url('public/css/frontsite.css') }}" rel="stylesheet">
     <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    
     <link href='http://fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css'>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>

    

    <![endif]-->
    
     <script>
       var URL = "<?=URL::to('/')?>/";
     </script>
  </head>
  <body>
  <div class="wrapper">
    <nav class="navbar navbar-1stop navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle text-light" data-toggle="collapse" data-target="#myNavbar">
            <i class="fa fa-ellipsis-v"></i>
          </button>
          <a class="navbar-brand" href="{{ url('/') }}"><img src="{{ url('public/img/1stopxbrl-inline.png') }}" class="img-responsive brand-image"></a>
        </div>
        <div class="collapse navbar-collapse" id="myNavbar">
          <ul class="nav navbar-nav navbar-right">
            <li><a href="#"><span class="glyphicon glyphicon-user"></span> Account : {{strtoupper($user_role)}}</a></li>
            <li><a href="#" id="account-admin"><i class="fa fa-cog"></i></a></li>
            <li><a href="http://1stopxbrl.gcloud/logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
          </ul>
        </div>
      </div>
    </nav>
    <nav id="sidenav" class="">
        @php
          $firstname = Auth::user()->first_name;
          $lastname = Auth::user()->last_name;
        @endphp
        <!-- Brand and toggle get grouped for better mobile display -->
        <ul class="nav navbar-nav">
          <li class="hidden-minimize sidenav-logo"><img onclick="window.location = '{{url('/')}}'" alt="ServiceTrack" src="{{ url('public/img/servicetrack-logo-hq.png') }}"></li>
          <li class="user-section hidden-minimize">
            <a >
              <div class="circle">
                <span class="initials">{{ $firstname[0].$lastname[0] }}</span>
              </div>
              {{$firstname.' '.$lastname}} 
             </a>
             <!-- <span class="arrow-left"></span> -->
            <!-- <ul class="submenu">
              <li><a href="#" id="account-admin">Account {{$user_role}}</a></li>
              <li><a href="http://1stopxbrl.gcloud/logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a></li>
            </ul> -->
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">{{ csrf_field() }}</form>

          </li>
          <li class="show-minimize hidden">&nbsp;</li>
        @if(isset($menusMap))

            @if ($menusMap['admin-mode'])
              <li>
                <a href="{{ url('/back_to_admin') }}">
                  <span class="side-menu-icon"><i class="fa fa-undo"></i></span>
                  <span class="side-menu-label">Back to Admin Mode</span>
                </a>
              </li>
            @endif

            @if ($menusMap['jobs'])
              <li>
                <a href="{{url('/')}}" class="{{ Request::path() == '/' ? 'active' : ''}}">
                  <span class="side-menu-icon"><i class="fa fa-briefcase"></i></span>
                  <span class="side-menu-label">Jobs</span>
                </a>
              </li>

            @endif

            @if ($menusMap['invoice-generator'])
              <li>
                <a href="{{url('/invoice-generator')}}" class="{{ Request::path() == 'invoice-generator' ? 'active' : ''}}">
                  <span class="side-menu-icon"><i class="fa fa-file-invoice"></i></span>
                  <span class="side-menu-label">Invoice Generator</span>
                </a>
              </li>
            @endif

            @if ($menusMap['companies'])
              <li>
                <a href="{{url('/companies')}}" class="{{ Request::path() == 'companies' ? 'active' : ''}}">
                  <span class="side-menu-icon"><i class="fa fa-building"></i></span>
                  <span class="side-menu-label">Companies</span>
                </a>
              </li>
            @endif

             @if ($menusMap['users'])
              <li>
                <a href="{{url('/users')}}" class="{{ Request::path() == 'users' ? 'active' : ''}}">
                  <span class="side-menu-icon"><i class="fa fa-users"></i></span>
                  <span class="side-menu-label">Users</span>
                </a>
              </li>
            @endif
            
            @if ($menusMap['vendors'])
              <li>
                <a href="{{url('/vendors')}}" class="{{ Request::path() == 'vendors' ? 'active' : ''}}">
                  <span class="side-menu-icon"><i class="fa fa-shopping-bag"></i></span>
                  <span class="side-menu-label">Vendors</span>
                </a>
              </li>
            @endif
            
            @if ($menusMap['logon-as-user'])
              <li>
                <a href="{{ url('/logon_as_user') }}" class="{{ Request::path() == 'logon_as_user' ? 'active' : ''}}">
                  <span class="side-menu-icon"><i class="fa fa-user"></i></span>
                  <span class="side-menu-label">Logon as user</span>
                </a>
              </li>
            @endif

            @if ($menusMap['pricing-grid'])
                <li>
                  <a href="{{url('/pricing-grid')}}" class="{{ Request::path() == 'pricing-grid' ? 'active' : ''}}">
                    <span class="side-menu-icon"><i class="fa fa-money-check-alt"></i></span>
                    <span class="side-menu-label">Pricing Grid</span>
                  </a>
                </li>
            @endif

            @if ($menusMap['configuration'])
            <li>
              <a href="{{url('/configuration')}}" class="{{ Request::path() == 'configuration' ? 'active' : ''}}">
                <span class="side-menu-icon"><i class="fa fa-sliders-h"></i></span>
                <span class="side-menu-label">Configuration</span>
              </a>
            </li>
            @endif

            @if ($menusMap['emails'])
              <li>
                <a href="{{url('/emails')}}" class="{{ Request::path() == 'emails' ? 'active' : ''}}">
                  <span class="side-menu-icon"><i class="fa fa-envelope"></i></span>
                  <span class="side-menu-label">Emails</span>
                </a>
              </li>
            @endif


            @if ($menusMap['file-management'])
              <li>
                <a href="{{url('/filemanagement')}}" class="{{ Request::path() == 'filemanagement' ? 'active' : ''}}">
                  <span class="side-menu-icon"><i class="fa fa-file-archive"></i></span>
                  <span class="side-menu-label">File Management</span>
                </a>
              </li>
            @endif


            @if ($menusMap['clients-departments'])
              <li>
                <a href="#">
                  <span class="side-menu-icon"><i class="fa fa-sitemap"></i></span>
                  <span class="side-menu-label">Clients/Departments</span>
                </a>
              </li>
            @endif


            @if ($menusMap['file-archive'])
              <li>
                <a href="{{ url('/filearchive') }}" class="{{ Request::path() == 'filearchive' ? 'active' : ''}}">
                  <span class="side-menu-icon"><i class="fa fa-inbox"></i></span>
                  <span class="side-menu-label">File Archive</span>
                </a>
              </li>
            @endif


            @if ($menusMap['invoices'])
            <li>
              <a href="{{url('/invoices')}}" class="{{ Request::path() == 'invoices' ? 'active' : ''}}">
                <span class="side-menu-icon"><i class="fa fa-file-invoice-dollar"></i></span>
                <span class="side-menu-label">Invoices</span>
              </a>
            </li>
            @endif

            @if ($menusMap['invoice-recipient'])
            <li>
              <a href="{{url('/invoicerecipient')}}" class="{{ Request::path() == 'invoicerecipient' ? 'active' : ''}}">
                <span class="side-menu-icon"><i class="fa fa-file-import"></i></span>
                <span class="side-menu-label">Invoice Recipient</span>
              </a>
            </li>
            @endif

          @endif

        </ul>

      </nav>
      <div class="sidebar-menu">
          <i class="fa fa-bars"></i>
      </div>
    </div>
    <div class="content-wrapper">
      @yield('content')
    </div>

    <div class="modal fade" id="edit-user" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

            </div>
        </div>
    </div>
    <script src="{{ url('public/js/app.js') }}"></script>
    <!-- <script src="{{ url('js/frontsite.js') }}"></script> -->
    
    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
     <script src="{{ url('public/js/bootstrap-confirmation.js') }}" type="text/javascript"></script>
    <script>
        var menus = '';
        $(function() {
          if(localStorage.getItem("sidenav") == 'min')
          {
            if($('.sidebar-menu').hasClass('in') == false)
            {
              setTimeout(function(){
                $('.sidebar-menu').click();
              },100);
            }
          }
            menus = $('#sidenav .nav ').html();
            $('.sidebar-menu').click(function(){
              var that = $(this);
              var description = that.parent('a').html();

              if(that.hasClass('in'))
              {
                  localStorage.removeItem("sidenav");
                  that.removeClass('in');
                  $('.show-minimize').removeClass('hidden');
                  $('#sidenav').css('width','280px');
                  $('#sidenav .nav.navbar-nav li a').css('padding-left','30px');
                  $('.content-wrapper').css({
                                              'left' : '280px',
                                              'width' : 'calc(100% - 280px)'
                                        });
                  that.css('left','280px');
                  setTimeout(function(){
                     $("#sidenav .nav.navbar-nav li a").popover('destroy');
                    $('#sidenav .nav').find('.side-menu-label').removeClass('hidden');
                    $('#sidenav .nav').find('.hidden-minimize').removeClass('hidden');
                  },300);
                  
              }
              else
              {
                  localStorage.setItem("sidenav", "min");
                  that.addClass('in');
                  $('.content-wrapper').css({
                                              'left' : '60px',
                                              'width' : 'calc(100% - 60px)'
                                        });

                  $('#sidenav .nav').find('.hidden-minimize').addClass('hidden');
                  $('#sidenav .nav').find('.side-menu-label').addClass('hidden');
                  $('.show-minimize').removeClass('hidden');
                  $('#sidenav .nav.navbar-nav li a').attr('data-toggle','popover');
                  $("#sidenav .nav.navbar-nav li a").popover({
                                                          'content' : function(){
                                                              return '<b>'+$(this).find('.side-menu-label').text()+'</b>';
                                                          },
                                                          'show' : true,
                                                          'placement' : 'right',
                                                          'html' : true,
                                                          'trigger' : 'hover'
                                                      });


                  $('#sidenav .nav.navbar-nav li a').css('padding-left','20px');
                  $('#sidenav').css('width','60px');
                  that.css('left','0px');
              }
                
            });



            $( ".datepicker" ).datepicker({ dateFormat: 'dd-mm-yy' });

            $(".monthyearpicker").datepicker({
                dateFormat: 'MM yy',
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,

                onClose: function(dateText, inst) {
                    var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                    var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                    $(this).val($.datepicker.formatDate('yy-mm', new Date(year, month, 1)));
                }
            });

            $(".monthyearpicker").focus(function () {
                $(".ui-datepicker-calendar").hide();
                $("#ui-datepicker-div").position({
                    my: "center top",
                    at: "center bottom",
                    of: $(this)
                });
            });
        });

        function iconPopover()
        {

        }
    </script>

    <script type="text/javascript">
      @yield('scripts')
    </script>

    <script src="{{ url('public/js/jquery.fancybox.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function(){


          $('#account-admin').click(function(){
              $.get(URL+'edit-user/'+ {{ Auth::id() }})
                .done(function(result){
                    $('#edit-user').modal('show');
                    $('#edit-user .modal-content').html(result);
                    return false;
                }); 
          });

            $("#fancybox-download-invoice").fancybox({
                openEffect: "none",
                closeEffect: "none",
                'width': 300,
                'height': 300,
                'autoSize': false,
                afterClose: function () {
                    parent.location.reload(true);
                }

            });
           

           
        });

        function alertModal(title,message,action)
        {
          var _token = '{{ csrf_token() }}';
          $.post(URL+'show-alert-notification',{'title':title,'message':message,'_token':_token,'action':action})
            .done(function(result){
                $('#notification-modal-sm .modal-dialog').html(result);
                $('#notification-modal-sm').modal('show');
            });
        }



    </script>


    <!-- Modal -->
    <div id="alert-modal-lg" class="modal fade" role="dialog">
      <div class="modal-dialog modal-lg">

      </div>
    </div>

    <div id="alert-modal-sm" class="modal fade" role="dialog">
      <div class="modal-dialog modal-sm">

      </div>
    </div>

    <div id="notification-modal-sm" class="modal fade" role="dialog">
      <div class="modal-dialog modal-sm">

      </div>
    </div>




  </body>
</html>