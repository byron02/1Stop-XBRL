@extends('layouts.frontsite')

@section('content')

	    <div class="wrapper">
	        <div class="page-holder">
	            <h2 class="content-title">Logon as User</h2>
	            <div class="clearfix"></div>
	            <div class="col-lg-4">
	            	<form method="post" action="{{ route('login_as_user') }}">
	            		{{ csrf_field() }}
			          	<div class="form-group">
			          		<br/>
			          		<label>Company/Role</label>
			          		<select class="form-control" name="company" id="company-selection" required>
			          			<option selected disabled></option>
			          			<option value="vendor">--Vendor--</option>
			            		@foreach($company as $e)
			            			<option value="{{ $e->id }}">{{ $e->name }}</option>
			            		@endforeach
			            	</select>
			          	</div>
			          	<div class="form-group">
			          		<label>User</label> <small id="user-catcher"></small>
			          		<select class="form-control" id="user-selection" name="user"></select>
			          		 <small><i>Name:Username(Role)</i></small>
		          		</div>
		          		<div class="form-group">
		          			<button class="styled_btn " id="logon-user-btn">Logon as User</button>
		          		</div>
	          		</form>
          		</div>
          		<div class="clearfix"></div>
       		</div>
       	</div>


       	<script>
	       	$(function(){
	       		$('#company-selection').change(function(){
	       			let that = $(this);
	       			let company_id = that.val();
	       			$('#user-selection').html('<option>Loading...</option>');
	       			$.get(URL+'company-users/'+company_id)
	       				.done(function(result){
	       					let data = $.parseJSON(result);
	       					let str = '';
	       					if(data.length > 0)
	       					{
	       						$.each(data,function(i,x){
	       							str += '<option value="'+x.id+'">';
	       							str += x.first_name+' '+x.last_name+':'+x.username+'('+x.role+')';
	       							str += '</option>';
	       						});
	       						$('#user-catcher').text('');
	       						$('#logon-user-btn').prop('disabled',false).css('opacity','1');
	       					}
	       					else
	       					{
	       						$('#user-catcher').text('No users found.');
	       						$('#logon-user-btn').prop('disabled',true).css('opacity','.6');
	       					}
       						$('#user-selection').html(str);
	       				})
	       		
	       		});	
	       	})
       	</script>

@endsection