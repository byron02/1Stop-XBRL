@extends('layouts.frontsite')

@section('content')

    <div class="wrapper">
        <div class="page-holder">
            <h2 class="content-title">List of Users</h2>
            <div class="clearfix"></div>
            <br/>
            <div>
                <div class="col-sm-6">
                    <form class="form-inline">
                        <div class="form-group">
                            <label>Filter By : </label>
                             <select name="filter_by" id="drop-filter" class="form-control">
                                    <option value="">Filter By</option>
                                    <option @if(request('filter_by') == 'id') selected @endif value="id">User ID</option>
                                    <option @if(request('filter_by') == 'first_name') selected @endif value="first_name">First Name</option>
                                    <option @if(request('filter_by') == 'last_name') selected @endif value="last_name">Last Name</option>
                                    <option @if(request('filter_by') == 'company_id') selected @endif value="company_id">Company</option>
                                    <option @if(request('filter_by') == 'job_title') selected @endif value="job_title">Job Title</option>
                                    <option @if(request('filter_by') == 'role_id') selected @endif value="role_id">User Role</option>
                                    <option @if(request('filter_by') == 'username') selected @endif value="username">Username</option>
                                    <option @if(request('filter_by') == 'email') selected @endif value="email">Email</option>
                                </select>
                            &nbsp;
                            <div class="input-group">
                                <input type="text" placeholder="Type here..." class="form-control" name="search" value="{{ request('search') }}" id="users-search">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-default">
                                        <span class="glyphicon glyphicon-search"></span> Search
                                    </button>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-sm-6 text-right pr-0">
                    <a class="styled_btn submit_btn" style="text-decoration:none" data-toggle="modal" name="userModal" data-target="#addUser">
                         Add New User
                   </a>
                    <a href="{{ route('iplockdown') }}" class="styled_btn " style="text-decoration:none">
                         Go to IP Address Management
                   </a>
                   <a href="{{ route('users/removed') }}" class="styled_btn " style="text-decoration:none">
                         Deleted Users
                   </a>
                </div>
                <div class="clearfix"></div>
            </div>

                <!-- Modal -->
                <form id="add-user-form" method="POST" action="{{ url('add-user') }}">
                        {{ csrf_field() }}                    
                    <div class="modal fade" id="addUser" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <h4 class="modal-title font-weight-bold">Add User</h4>
                                </div>
                                <div class="modal-body">
                                     <div class="form-group">
                                        <h4 data-toggler="toggled" data-target="#account_details_add"> <i class="glyphicon glyphicon-chevron-down"></i> Account Details</h4>  
                                    </div>
                                    <div id="account_details_add" class="toggled in">
                                        <div class="form-group">
                                            <label data-error="wrong" data-success="right">Username</label>
                                            <input type="text" id="username" name="username" class="form-control validate">
                                        </div>
                                        <div class="form-group">
                                            <label data-error="wrong" data-success="right">Password</label>
                                            <input type="password" id="password" name="password" class="form-control validate">
                                        </div>

                                        <div class="form-group">
                                            <label data-error="wrong" data-success="right">Confirm Password</label>
                                            <input type="password" id="confirm_password" name="password_confirmation" class="form-control validate">
                                        </div>
                                        <div class="form-group">
                                            <label data-error="wrong" data-success="right">Email Address</label>
                                            <input type="email" id="email" name="email" class="form-control validate">
                                        </div>

                                        <div class="form-group">
                                            <label data-error="wrong" data-success="right">User Role</label>
                                             <select class="form-control" name="role" style="font-size: 15px;">
                                                 @foreach($roles as $key => $role)
                                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                                 @endforeach
                                                 

                                             </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <h4 data-toggler="toggled" data-target="#personal_info_add"> <i class="glyphicon glyphicon-chevron-down"></i> User Personal Information</h4>  
                                    </div>
                                    <div id="personal_info_add" class="toggled in">
                                        <div class="form-group">
                                            <label data-error="wrong" data-success="right">First Name</label>
                                            <input type="text" id="fname" name="fname" class="form-control validate">
                                        </div>
                                        <div class="form-group">
                                            <label data-error="wrong" data-success="right">Last Name</label>
                                            <input type="text" id="lname" name="lname" class="form-control validate">
                                        </div>

                                        <div class="form-group">
                                            <label data-error="wrong" data-success="right">Job Title</label>
                                            <input type="text" id="job_title" name="job_title" class="form-control validate">
                                        </div>
                                        <div class="form-group">
                                            <label data-error="wrong" data-success="right">Address Line 1</label>
                                            <input type="text" id="address1" name="address1" class="form-control validate">
                                        </div>
                                        <div class="form-group">
                                            <label data-error="wrong" data-success="right">Address Line 2</label>
                                            <input type="text" id="address2" name="address2" class="form-control validate">
                                        </div>
                                        <div class="form-group">
                                            <label data-error="wrong" data-success="right">Address Line 3</label>
                                            <input type="text" id="address3" name="address3" class="form-control validate">
                                        </div>
                                        <div class="form-group">
                                            <label data-error="wrong" data-success="right">County</label>
                                            <input type="text" id="city" name="city" class="form-control validate">
                                        </div>
                                        <div class="form-group">
                                            <label data-error="wrong" data-success="right">Country</label>
                                             <select class="form-control" name="country" style="font-size: 15px;">
                                                 @foreach($countries as $key => $country)
                                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                                 @endforeach
                                                 

                                             </select>
                                        </div>
                                        <div class="form-group">
                                            <label data-error="wrong" data-success="right">Post Code</label>
                                            <input type="text" id="post_code" name="post_code" class="form-control validate">
                                        </div>
                                        <div class="form-group">
                                            <label data-error="wrong" data-success="right">Telephone Number</label>
                                            <input type="text" id="tel_number" name="tel_number" class="form-control validate">
                                        </div>
                                        <div class="form-group">
                                            <label data-error="wrong" data-success="right">Mobile Number</label>
                                            <input type="text" id="mobile_number" name="mobile_number" class="form-control validate">
                                        </div>
                                        <div class="form-group">
                                            <label>Timezone :</label>
                                            <div class="checkbox">
                                                @foreach($timezone as $tz)
                                                    @php
                                                        $timezoneNameImage = strtolower($tz->name).'.png';
                                                    @endphp
                                                    <label>
                                                        <input type="radio" name="timezone" value="{{ $tz->id }}" required> 
                                                        <img class="img_timezone" src="{{ url('public/img/'.$timezoneNameImage) }}"> {{ $tz->name }}
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="modal-footer d-flex justify-content-center">
                                    <button type="submit" class="styled_btn submit_btn" name="addUser">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                   </form>
                     <!-- End Modal -->
                  

                @php 
                    $order = request('ord') == 'asc' ? 'desc' : 'asc';
                @endphp
              
                <br/>
                 @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                <!-- <a href="{{url('/users/?filter_by=id&ord='.$order)}}" style="color:black;">User Id</a> |
                <a href="{{url('/users/?filter_by=first_name&ord='.$order)}}" style="color:black;">First Name</a> |
                <a href="{{url('/users/?filter_by=last_name&ord='.$order)}}" style="color:black;">Last Name</a> |
                <a href="{{url('/users/?filter_by=company_id&ord='.$order)}}" style="color:black;">Company</a> |
                <a href="{{url('/users/?filter_by=job_title&ord='.$order)}}" style="color:black;">Job Title</a> |
                <a href="{{url('/users/?filter_by=role_id&ord='.$order)}}" style="color:black;">User Role</a> |
                <a href="{{url('/users/?filter_by=username&ord='.$order)}}" style="color:black;">Username</a> |
                <a href="{{url('/users/?filter_by=email&ord='.$order)}}" style="color:black;">Email</a> | -->


                 @php
                    $sort = request('sort') == 'asc' ? 'desc' : 'asc';
                    $icon = array(
                                    'nan' => 'fa-sort',
                                    'asc' => 'fa-sort-up',
                                    'desc' => 'fa-sort-down'
                                );

                    $sort_by = request('order_by') != '' ? request('sort') : 'nan';
                    $id_icon = request('order_by') == 'id' ? $icon[$sort_by] : 'fa-sort';
                    $first_name_icon = request('order_by') == 'first_name' ? $icon[$sort_by] : 'fa-sort';
                    $company_icon = request('order_by') == 'companies.name' ? $icon[$sort_by] : 'fa-sort';
                    $job_title_icon = request('order_by') == 'job_title' ? $icon[$sort_by] : 'fa-sort';
                    $role_id_icon = request('order_by') == 'role_id' ? $icon[$sort_by] : 'fa-sort';
                    $username_icon = request('order_by') == 'username' ? $icon[$sort_by] : 'fa-sort';
                    $email_icon = request('order_by') == 'email' ? $icon[$sort_by] : 'fa-sort';
                    $status_icon = request('order_by') == 'status' ? $icon[$sort_by] : 'fa-sort';
                    $ip_address_icon = request('order_by') == 'ip_address' ? $icon[$sort_by] : 'fa-sort';
                @endphp

                <table class="table table-condensed table-hover" id="userTable">
                    <tr  class="dark">
                        <th>
                            <div class="pull-left">
                                User #
                            </div>
                            <div class="pull-right">
                                <a href="{{ url('/users/?order_by=id&sort='.$sort) }}">
                                    <i class="fa {{ $id_icon }}"></i>
                                </a>
                            </div>
                            <div class="clearfix"></div>
                        </th>
                        <th>
                            <div class="pull-left">
                                Name
                            </div>
                            <div class="pull-right">
                                <a href="{{ url('/users/?order_by=first_name&sort='.$sort) }}">
                                    <i class="fa {{ $first_name_icon }}"></i>
                                </a>
                            </div>
                            <div class="clearfix"></div>
                        </th>
                        <th>
                            <div class="pull-left">
                                Company
                            </div>
                            <div class="pull-right">
                                <a href="{{ url('/users/?order_by=companies.name&sort='.$sort) }}">
                                    <i class="fa {{ $company_icon }}"></i>
                                </a>
                            </div>
                            <div class="clearfix"></div>
                        </th>
                        <th>
                            <div class="pull-left">
                                Job Title
                            </div>
                            <div class="pull-right">
                                <a href="{{ url('/users/?order_by=job_title&sort='.$sort) }}">
                                    <i class="fa {{ $job_title_icon }}"></i>
                                </a>
                            </div>
                            <div class="clearfix"></div>
                        </th>
                        <th>
                            <div class="pull-left">
                                Role
                            </div>
                             <div class="pull-right">
                                <a href="{{ url('/users/?order_by=role_id&sort='.$sort) }}">
                                    <i class="fa {{ $role_id_icon }}"></i>
                                </a>
                            </div>
                            <div class="clearfix"></div>
                        </th>
                        <th>
                            <div class="pull-left">
                                Username
                            </div>
                             <div class="pull-right">
                                <a href="{{ url('/users/?order_by=username&sort='.$sort) }}">
                                    <i class="fa {{ $username_icon }}"></i>
                                </a>
                            </div>
                            <div class="clearfix"></div>
                        </th>
                        <th>
                            <div class="pull-left">
                                Email
                            </div>
                             <div class="pull-right">
                                <a href="{{ url('/users/?order_by=email&sort='.$sort) }}">
                                    <i class="fa {{ $email_icon }}"></i>
                                </a>
                            </div>
                            <div class="clearfix"></div>
                        </th>
                        <th>
                            <div class="pull-left">
                                Status
                            </div>
                            <div class="pull-right">
                                <a href="{{ url('/users/?order_by=status&sort='.$sort) }}">
                                    <i class="fa {{ $status_icon }}"></i>
                                </a>
                            </div>
                            <div class="clearfix"></div>
                        </th>
                        <th>
                            <div class="pull-left">
                                IP address
                            </div>
                             <div class="pull-right">
                                <a href="{{ url('/users/?order_by=ip_address&sort='.$sort) }}">
                                    <i class="fa {{ $ip_address_icon }}"></i>
                                </a>
                            </div>
                            <div class="clearfix"></div>
                        </th>
                        <th></th>

                    </tr>

                   @foreach($users as $key => $user)
                        <tr class="user-row">
                            <td class="id_column">{{ $user->id }}</td>
                            <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                            <td>{{ $user->company }}</td>
                            <td>{{ $user->job_title }}</td>
                            <td>{{ $user->role }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->user_status }}</td>
                            <td>{{ $user->ip_address }}</td>
                            <td class="remove-user"><a class="styled_btn remove-user"  href="#" data-toggle="confirmation" data-placement="left" data-title="Delete User" ><i class="glyphicon glyphicon-trash"></i></a></td>
                        </tr>
                   @endforeach

                </table>
               
                
                    <div class="pull-left">
                        Displaying {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} Records(s)
                        |  <a href="{{url('/users/?export=1')}}">Export as CSV</a>
                    </div>
                    <div class="pull-right">
                        {{ $users->appends(\Input::except('page'))->render() }}
                    </div>
                    <div class="clearfix"></div>


                     <div class="form-group">
                        <br/>
                        <h2 class="text-left">IP Address List</h2>
                         <div class="table-responsive">
                            <table class="table table-condensed" id="ipTable">
                                <tr class="dark">
                                    <td data-field="ip_address">IP Address </td>
                                    <td data-field="count">User Count</td>
                                </tr>
                                @foreach($ip_address as $key => $ip)
                                    <tr id="{{ $ip->ip_address }}">
                                        @if($ip->ip_address == '')
                                        <td>localhost</td>
                                        @else
                                        <td>{{ $ip->ip_address }}</td>
                                        @endif
                                        <td>{{ $ip->total }}</td>
                                        
                                    </tr>

                                @endforeach
                               
                            </table>
                        </div>
                        <div class="pagination-container">
                        Displaying {{ $ip_address->firstItem() }} to {{ $ip_address->lastItem() }} of {{ $ip_address->total() }} Records(s)
                        {{ $ip_address->links() }}
                        </div>
                    </div>
                </div>


            </div>


           

       <!--  modal for ip -->
       <div class="modal fade" id="ipModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
           <div class="modal-dialog" role="document">
               <div class="modal-content">
                   
                  
                   <div class="modal-body mx-3">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                        <h4>Users who uses this IP: <p id="ip"></p></h4><a id="ipLink" href="{{url('/users/?block_by=')}}">Block this IP</a>
                        <div id="Content"></div>
                        
                   </div>
                  
               </div>
           </div>
       </div>

    </div>


    

   
    <script>
    $.ajaxSetup( {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    } );

    function saveUsers() {
        $( '#add-user-form' ).submit( function( e ) {
            e.preventDefault();
            var select = $( this ).serialize();
            // POST to php script
            $.ajax( {
                type: 'POST',
                url: '/add-user',
                data: select
            }).then( function( data ) {
                if(data == 'invalid')
                {
                    alertModal('User Status','Invalid Input!','');
                    return false;
                }
                else
                {
                    alertModal('User Status','Successfully added.','reload');
                }
            } );
            return false;
        } );
    }

    $(document).ready(function()
    {  

         $('[data-toggler]').click(function(){
              var that = $(this);
              var target_class = that.attr('data-toggler');
              var target_id = that.attr('data-target');
              if($('.'+target_class).hasClass('in'))
              {
                  $(target_id).removeClass('in');
                  that.find('i').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-right');
              }
              else
              {
                  $(target_id).addClass('in');
                  that.find('i').removeClass('glyphicon-chevron-right').addClass('glyphicon-chevron-down');
              }

              $(target_id).toggle();
          });

        $('#drop-filter').change(function(){
            $('#users-search').val('');
        });

        saveUsers();


        $(document).find('[data-toggle="confirmation"]').confirmation({
            onConfirm: function(event, element) { 
                var user_id = element.closest('tr').find('.id_column').text();
                var _token = '{{ csrf_token() }}';
                $.ajax({
                      url: URL+'remove-user',
                      type:'POST',
                      data: {'_token':_token, 'user_id':user_id,'status':3},
                      success: function(result) {
                        alertModal('User Status','Deleted successfully.','reload');
                        }
                    });
                return false;
            },
            onCancel: function(event, element) { return false;},

        });

    });

    function searchIp() {
        $("#ipTable tr").click(function(){
          
                    
                    var $id = $(this).attr('id');
                    
                    $.ajax({
                                method: 'get',
                                url: '{{ url('ip-user/data?id=') }}' + $id,
                                dataType: 'json',
                                success: function (data) {
                                       $("#ipModal").modal();
                                       document.getElementById("ip").innerHTML = $id;
                                       $("#ipLink").attr("href", "/users/?block_by="+$id);    
                                     
                                       $('#Content').html('<table class="table"><thead><tr><th>Name</th><th>Email Address</th><th>Job Title</th><th>Company</th></tr></table>');
                                                    $.each(data, function(index, element) {
                                                    $('#Content table').append('<tr><td>' + element.first_name + '</td><td>' + element.email + '</td><td>' + element.job_title + '</td><td>' + element.company + '</td></tr>');
                                    });
                                        
                                },
                                error: function (data) {
                                }
                    });

                    
            
        });
    }
    

    $(document).ready(function()
    {
        searchIp();

        $('.user-row td:not(.remove-user)').click(function(){
            let that = $(this);
            let user =  that.closest('tr').find('.id_column').text();
           $.get(URL+'edit-user/'+user)
            .done(function(result){
                $('#edit-user').modal('show');
                $('#edit-user .modal-content').html(result);
                return false;
            }); 
        });

    });



    </script>
   
   
    @endsection