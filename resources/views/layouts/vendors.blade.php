@extends('layouts.frontsite')

@section('content')

		    <div class="wrapper">
		        <div class="page-holder">
		            <h2 class="content-title">Vendors List</h2>
		            <div class="clearfix"></div>
		            <br/>
		            <div class="form-group">
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
		                         ADD VENDOR
		                   </a>
		                   <a href="{{ route('vendors/removed') }}" class="styled_btn " style="text-decoration:none">
		                         Deleted Vendors
		                   </a>
		                </div>
		                <div class="clearfix"></div>
		            </div>
		            <div class="table-responsive">
			            <table class="table">
			            	<thead>
				            	<tr>
				            		<th>User ID</th>
				            		<th>Name</th>
				            		<th>Email</th>
				            		<th>Status</th>
				            		<th></th>
				            	</tr>
			            	</thead>
				            <tbody>
				            	@foreach($vendors as $vend)
					            <tr class="user-row">
					            	<td class="id_column"><?=$vend['id']?></td>
					            	<td><?=ucwords($vend['first_name'].' '.$vend['last_name'])?></td>
					            	<td><?=$vend['email']?></td>
					            	<td><?=$vend['user_status']?></td>
					            	<td class="remove-user"><a class="styled_btn remove-user"  href="#" data-toggle="confirmation" data-placement="left" data-title="Delete Vendor" ><i class="glyphicon glyphicon-trash"></i></a></td>
					            </tr>
					            @endforeach
				            </tbody>
			            </table>
		            </div>
		            <div class="pull-left">
                        Displaying {{ $vendors->firstItem() }} to {{ $vendors->lastItem() }} of {{ $vendors->total() }} Records(s)
                    </div>
                    <div class="pull-right">
                        {{ $vendors->appends(\Input::except('page'))->render() }}
                    </div>
                    <div class="clearfix"></div>
	       		</div>
	       	</div>

	       	 <!-- Modal -->
            <form id="add-user-form" method="POST" action="{{ url('add-user') }}">
                        {{ csrf_field() }}                    
                    <div class="modal fade" id="addUser" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <input type="hidden" name="vendors" value="1">
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
                                            <input type="text" id="username" name="username" class="form-control validate" required>
                                        </div>
                                        <div class="form-group">
                                            <label data-error="wrong" data-success="right">Password</label>
                                            <input type="password" id="password" name="password" class="form-control validate" required>
                                        </div>

                                        <div class="form-group">
                                            <label data-error="wrong" data-success="right">Confirm Password</label>
                                            <input type="password" id="confirm_password" name="password_confirmation" class="form-control validate" required>
                                        </div>
                                        <div class="form-group">
                                            <label data-error="wrong" data-success="right">Email Address</label>
                                            <input type="email" id="email" name="email" class="form-control validate" required>
                                        </div>

                                        <div class="form-group hidden">
                                            <label data-error="wrong" data-success="right">User Role</label>
                                             <select class="form-control" name="role" style="font-size: 15px;">
                                                 @foreach($roles as $key => $role)
                                                    <option {{ $role->id == 4 ? 'selected' : '' }} value="{{ $role->id }}">{{ $role->name }}</option>
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
                                            <input type="text" id="fname" name="fname" class="form-control validate" required>
                                        </div>
                                        <div class="form-group">
                                            <label data-error="wrong" data-success="right">Last Name</label>
                                            <input type="text" id="lname" name="lname" class="form-control validate" required>
                                        </div>

                                        <div class="form-group">
                                            <label data-error="wrong" data-success="right">Job Title</label>
                                            <input type="text" id="job_title" name="job_title" class="form-control validate" required>
                                        </div>
                                        <div class="form-group">
                                            <label data-error="wrong" data-success="right">Address Line 1</label>
                                            <input type="text" id="address1" name="address1" class="form-control validate" required>
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
                                            <label data-error="wrong" data-success="right">City</label>
                                            <input type="text" id="city" name="city" class="form-control validate" required>
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
                                            <input type="text" id="post_code" name="post_code" class="form-control validate" required>
                                        </div>
                                        <div class="form-group">
                                            <label data-error="wrong" data-success="right">Telephone Number</label>
                                            <input type="text" id="tel_number" name="tel_number" class="form-control validate" required>
                                        </div>
                                        <div class="form-group">
                                            <label data-error="wrong" data-success="right">Mobile Number</label>
                                            <input type="text" id="mobile_number" name="mobile_number" class="form-control validate" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Timezone :</label>
                                            <div class="checkbox">
                                                @foreach($timezone as $tz)
                                                    @php
                                                        $timezoneNameImage = strtolower($tz->name).'.png';
                                                    @endphp
                                                    <label>
                                                        <input type="radio" {{ $tz->name == 'GMT' ? 'checked' : '' }} name="timezone" value="{{ $tz->id }}" required> 
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

	       	<script>
		       	$(function(){
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
                    
                    
		       		 $(document).find('[data-toggle="confirmation"]').confirmation({
			            onConfirm: function(event, element) { 
			                var user_id = element.closest('tr').find('.id_column').text();
			                var _token = '{{ csrf_token() }}';
			                $.ajax({
			                      url: URL+'remove-user',
			                      type:'POST',
			                      data: {'_token':_token, 'user_id':user_id,'status':3},
			                      success: function(result) {
			                        alertModal('Vendor Status','Deleted successfully.','reload');
			                        }
			                    });
			                return false;
			            },
			            onCancel: function(event, element) { return false;},

			        });

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
		       		saveUsers();
		       	});

		       	function saveUsers() {
			        $( '#add-user-form' ).submit(function(){
			            var select = $(this).serialize();
                        $.post(URL+'add-user',select)
                            .done(function(result){
                                if(result == 'invalid')
                                {
                                    alertModal('User Status','Invalid Input!','');
                                    return false;
                                }
                                else
                                {
                                    alertModal('User Status','Successfully added.','reload');
                                }
                            });
			            return false;
			        });
			    }
	       	</script>
@endsection