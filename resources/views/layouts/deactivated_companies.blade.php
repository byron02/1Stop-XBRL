@extends('layouts.frontsite')

@section('content')
<div class="wrapper">
    <div class="page-holder">
        <h2 class="content-title">List of Deactivated Companies</h2> 
        <div class="add-btn">
            <a href="{{ url('companies') }}" class="styled_btn submit_btn" style="text-decoration:none">
                 Return to Companies
           </a>
        </div>
        <div class="clearfix"></div>
        <br/>
            <div class="table-responsive">
            	<table class="table table-condensed table-bordered table-striped">
	            	<thead>
		            	<tr class="dark">
		            		<th ># </th>
	                        <th>Company Name</th>
	                        <th>Email</th>
	                        <th class="text-center">Action</th>
		            	</tr>
	            	</thead>
	            	<tbody>
	            	@foreach($companies as $key => $cp)
		            	<tr>
		            		<td width="5%"  class="id_column">{{ $cp['id'] }}</td>
		            		<td>{{ $cp['name'] }}</td>
		            		<td>{{ $cp['email'] }}</td>
		            		<td width="10%" class="text-center"><button class="btn btn-danger btn-sm" data-toggle="confirmation" data-placement="left" data-title="Restore Company" >Restore</button></td>
		            	</tr>
		            @endforeach
	            	</tbody>
            	</table>
            </div>
            <div class="pull-left">
                Displaying {{ $companies->firstItem() }} to {{ $companies->lastItem() }} of {{ $companies->total() }} Records(s)
            </div>
            <div class="pull-right">
            	{{ $companies->appends(\Input::except('page'))->render() }}
            </div>
            <div class="clearfix"></div>

            <script>
		        $(function(){
		            $(document).find('[data-toggle="confirmation"]').confirmation({
                        onConfirm: function(event, element) { 
                            var company_id = element.closest('tr').find('.id_column').text();
                            var _token = '{{ csrf_token() }}';
                            $.ajax({
                                  url: URL+'deactivate-company',
                                  type:'POST',
                                  data: {'_token':_token, 'company_id':company_id,'status':1},
                                  success: function(result) {
                                    alertModal('Company Status','Restore successfully.','reload');
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