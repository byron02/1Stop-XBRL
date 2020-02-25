@extends('layouts.frontsite')

@section('content')

	    <div class="wrapper">
	        <div class="page-holder">
	        	<div class="form-group">
	            	<h2 class="content-title">Automation Configuration</h2>
	            	<div class="clearfix"></div>
	            </div>
		            <div class="table-responsive">
			            <table class="table">
			            	<thead>
					            <tr class="dark">
						            <td class="datagrid_header"></td>
						            <td class="datagrid_header">Status</td>
						            <td class="datagrid_header">Action/Change Status to</td>
					            </tr>
				            </thead>
				            <tbody>
				            @foreach($config as $e)
				            <tr>
					            <td><input class="config_check" type="checkbox" {{$e->is_deleted == 0 ? 'checked' : ''}}  value="{{ $e->id }}"></td>
					            <td>{{ ucwords(str_replace('_',' ',$e->key)) }}</td>
					            <td>{{ $e->action_status }}</td>
				            </tr>
				            @endforeach
				            </tbody>
			            </table>
		            </div>
          		</div>
       		</div>
       	</div>

       	<script>
	       	$(function(){
	       		$('.config_check').click(function(){
	       			var that = $(this);
	       			var config = that.val();
	       			var action = 'deleted';
	       			if(that.prop('checked') == true)
	       			{
	       				action = 'active';
	       			}

	       			$.get(URL+'config-setup/'+action+'/'+config)
	       				.done(function(result){
	       					alertModal('Configuration Status','Update done successfully!','');
	       				});
	       		});
	       	});
       	</script>
@endsection