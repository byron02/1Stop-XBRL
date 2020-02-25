@extends('layouts.frontsite')

@section('content')
	<div class="wrapper">
        <div class="page-holder">
            <h2 class="content-title">List of Deleted Users</h2>
            <div class="add-btn">
                <a href="{{ url('users') }}" class="styled_btn submit_btn" style="text-decoration:none">
                     Return to Users
               </a>
            </div>
            <div class="clearfix"></div>
            <hr/>
            <div class="table-responsive">
            	<table class="table table-condensed table-bordered table-striped">
	            	<thead>
		            	<tr class="dark">
		            		<th >User ID </th>
	                        <th >Name</th>
	                        <th>Company</th>
	                        <th >Job Title</th>
	                        <th>Role</th>
	                        <th>Username </th>
	                        <th>Email </th>
	                        <th>IP address </th>
	                        <th>Action</th>
		            	</tr>
	            	</thead>
	            	<tbody>
	            	@foreach($users as $key => $user)
		            	<tr>
		            		<td class="id_column">{{ $user->id }}</td>
                            <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                            <td>{{ $user->company }}</td>
                            <td>{{ $user->job_title }}</td>
                            <td>{{ $user->role }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->ip_address }}</td>
		            		<td><button class="btn btn-danger btn-sm" data-toggle="confirmation" data-placement="left" data-title="Restore User" >Restore</button></td>
		            	</tr>
		            @endforeach
	            	</tbody>
            	</table>
            </div>
            <div class="pull-left">
                Displaying {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} Records(s)
            </div>
            <div class="pull-right">
            	{{ $users->appends(\Input::except('page'))->render() }}
            </div>
            <div class="clearfix"></div>
        </div>
    </div>

    <script>
        $(function(){
            $(document).find('[data-toggle="confirmation"]').confirmation({
                onConfirm: function(event, element) { 
                    var user_id = element.closest('tr').find('.id_column').text();
                    var _token = '{{ csrf_token() }}';
                    $.ajax({
                          url: URL+'remove-user',
                          type:'POST',
                          data: {'_token':_token, 'user_id':user_id,'status':1},
                          success: function(result) {
                            alertModal('User Status','User restored successfully.','reload');
                            }
                        });
                    return false;
                },
                onCancel: function(event, element) { return false;},
                btnOkLabel:'Restore',

            });

        });
    </script>
@endsection