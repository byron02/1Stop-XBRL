@extends('layouts.frontsite')

@section('content')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<?php
    $filterBy = !isset($filterBy) ? "" : $filterBy;
    $query = !isset($query) ? "" : $query;
?>

<div class="wrapper">

    <div class="page-holder">

        <h2 class="content-title">Companies</h2> 

        <div class="clearfix"></div>

        <div class="col-md-6">
            <table id="search">
                <form id="company-search-form" class="form-horizontal" method="GET" action="{{route('companies-filter')}}">
                    {{ csrf_field() }}
                    <tr>
                        <td>Filter by</td>
                        <td>
                            <div class="{{ $errors->has('filter_by') ? ' has-error' : '' }}">
                                <select class="form-control" name="filter_by" required autofocus>
                                    <?php 
                                        $filters = [];
                                        $filters[""] = "--Please Select--";
                                        $filters["id"] = "Id";
                                        $filters["name"] = "Name";
                                        $filters["email"] = "Email";
                                        $filters["phone"] = "Phone";
                                        $filters["address1"] = "Address";
                                        $filters["city"] = "City";
                                        $filters["country"] = "Country";
                                        // $filters["payment_method"] = "Payment Method";

                                       

                                        foreach ($filters as $key => $val) {
                                            if ($filterBy == $key) {
                                                echo '<option value="'.$key.'" selected>'.$val.'</option>';                                         
                                            } else {
                                                echo '<option value="'.$key.'">'.$val.'</option>';                                     
                                            }
                                        }
                                    ?> 
                                </select>

                                @if ($errors->has('filter_by'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('filter_by') }}</strong>
                                    </span>
                                @endif
                            </div>  
                        </td>
                        <td>
                            <div class="{{ $errors->has('query') ? ' has-error' : '' }}">
                                <input type="text" name="query" value="{{$query}}" class="form-control"/>

                                @if ($errors->has('query'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('query') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <input class="styled_btn" type="submit" value="Search" id="search-btn" />
                        </td>
                    </tr>
                </form>
            </table>
        </div>
        <div class="col-md-6 text-right">
            <br/>
            <a href="{{ url('companies/deactivated') }}" class="styled_btn submit_btn" style="text-decoration:none">
                 Deactivated Companies
           </a>
        </div>
        <div class="clearfix"></div>
    
        <div class="table-responsive">
             @php
                $sort = request('sort') == 'asc' ? 'desc' : 'asc';
                $icon = array(
                                'nan' => 'fa-sort',
                                'asc' => 'fa-sort-up',
                                'desc' => 'fa-sort-down'
                            );

                $sort_by = request('order_by') != '' ? request('sort') : 'nan';
                $id_icon = request('order_by') == 'id' ? $icon[$sort_by] : 'fa-sort';
                $name_icon = request('order_by') == 'name' ? $icon[$sort_by] : 'fa-sort';
                $email_icon = request('order_by') == 'email' ? $icon[$sort_by] : 'fa-sort';
                $phone_icon = request('order_by') == 'phone' ? $icon[$sort_by] : 'fa-sort';
                $address_icon = request('order_by') == 'address1' ? $icon[$sort_by] : 'fa-sort';
                $date_added_icon = request('order_by') == 'date_added' ? $icon[$sort_by] : 'fa-sort';
                $price_grid_icon = request('order_by') == 'pricing_grid' ? $icon[$sort_by] : 'fa-sort';
            @endphp
            <table class="table table-hover" id="company_table">

                <form id="company-auto-invoice-update-form" class="form-horizontal" method="POST" action="{{route('companies-auto-invoice-update')}}">
                    {{ csrf_field() }}
                    <input type="hidden" name="auto_invoice" id="auto-invoice-input" />
                    <input type="hidden" name="company_id" id="company-input" />
                </form>

                <form id="company-assign-invoice-to-project-name" class="form-horizontal" method="POST" action="{{route('companies-assign-invoice-to-project-name-update')}}">
                    {{ csrf_field() }}
                    <input type="hidden" name="assign_invoice_to_project_name" id="assign-invoice-to-project-name-input" />
                    <input type="hidden" name="company_id_assign_invoice_to_project_name" id="company-id-assign-invoice-to-project-name-input" />
                </form>

                <form id="company-edit-form" class="form-horizontal" method="GET" action="{{url('/companies/edit')}}">
                    {{ csrf_field() }}
                </form>
                <thead>
                    <tr class="dark">
                        <td>
                            <div class="pull-left">
                                ID
                            </div>
                             <div class="pull-right">
                                <a href="{{ url('/companies/?order_by=id&sort='.$sort) }}">
                                    <i class="fa {{ $id_icon }}"></i>
                                </a>
                            </div>
                            <div class="clearfix"></div>
                        </td>
                        <td>
                            <div class="pull-left">
                                Name
                            </div>
                            <div class="pull-right">
                                <a href="{{ url('/companies/?order_by=name&sort='.$sort) }}">
                                    <i class="fa {{ $name_icon }}"></i>
                                </a>
                            </div>
                            <div class="clearfix"></div>
                        </td>
                        <td>
                            <div class="pull-left">
                                Email
                            </div>
                            <div class="pull-right">
                                <a href="{{ url('/companies/?order_by=email&sort='.$sort) }}">
                                    <i class="fa {{ $email_icon }}"></i>
                                </a>
                            </div>
                            <div class="clearfix"></div>
                        </td>
                        <td>
                            <div class="pull-left">
                                Phone
                            </div>
                            <div class="pull-right">
                                <a href="{{ url('/companies/?order_by=phone&sort='.$sort) }}">
                                    <i class="fa {{ $phone_icon }}"></i>
                                </a>
                            </div>
                            <div class="clearfix"></div>
                        </td>
                        <td>
                            <div class="pull-left">
                                Address
                            </div>
                            <div class="pull-right">
                                <a href="{{ url('/companies/?order_by=address1&sort='.$sort) }}">
                                    <i class="fa {{ $address_icon }}"></i>
                                </a>
                            </div>
                            <div class="clearfix"></div>
                        </td>
                        <td>
                            <div class="pull-left">
                                Date Registered
                            </div>
                            <div class="pull-right">
                                <a href="{{ url('/companies/?order_by=date_added&sort='.$sort) }}">
                                    <i class="fa {{ $date_added_icon }}"></i>
                                </a>
                            </div>
                            <div class="clearfix"></div>
                        </td>
                        <td>
                            <div class="pull-left">
                                Pricing Grid
                            </div>
                            <div class="pull-right">
                                <a href="{{ url('/companies/?order_by=pricing_grid&sort='.$sort) }}">
                                    <i class="fa {{ $price_grid_icon }}"></i>
                                </a>
                            </div>
                            <div class="clearfix"></div
>                        </td>
                        <!-- <td class="datagrid_header">Payment Method</td> -->
                        <td>Auto Invoice</td>
                        <td>Assign Invoice To Project Name</td>
                        <td></td>
                    <tr>
                </tr>
                <tbody>
                @if(isset($companyMap))
                    @foreach($companyMap as $companyMapItem)
                        <tr>
                            <td class="id_column">{{$companyMapItem["id"]}}</td>
                            <td>{{$companyMapItem["name"]}}</td>
                            <td>{{$companyMapItem["email"]}}</td>
                            <td>{{$companyMapItem["phone"]}}</td>
                            <td title="{{$companyMapItem["address"]}}">{{$companyMapItem["address"]}}</td>
                            <td>{{date_format($companyMapItem["date_registered"], 'd-m-Y')}}</td>
                            <td>{{$companyMapItem["pricing_grid"]}}</td>
                            <!-- <td>{{$companyMapItem["payment_method"]}}</td> -->
                            <td class="text-center">
                                <input value="{{$companyMapItem['id']}}" class="auto-invoice-checkbox" type="checkbox" {{ $companyMapItem["autosend_invoice"] != null ? 'checked' : ''}}>
                            </td>
                            <td class="text-center">
                                <input value="{{$companyMapItem['id']}}" class="assign-invoice-to-project-checkbox" type="checkbox" {{ $companyMapItem["assign_invoice_to_project_name"] != null ? 'checked' : '' }}>
                            </td>
                            <td class="remove-company"><a class="styled_btn remove-company"  href="#" data-toggle="confirmation" data-placement="left" data-title="Delete Company" ><i class="glyphicon glyphicon-trash"></i></a></td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>

            <script>
                $(document).ready(function() {

                    $("#search-btn").click(function(e) {
                        $("#loading").show();
                        $("#company-search-form").submit();
                        e.preventDefault();
                    });

                    $(".auto-invoice-checkbox").change(function(e) {
                        var checkboxInput = $(this);
                        var checked = checkboxInput.is(':checked');
                        var companyId = checkboxInput.val();

                        var autoInvoiceInput = $("#auto-invoice-input");
                        autoInvoiceInput.val(checked ? 1 : 0);

                        var companyInput = $("#company-input");
                        companyInput.val(companyId);

                        var form = $("#company-auto-invoice-update-form");

                        $.ajax({
                            type: form.attr('method'),
                            url: form.attr('action'),
                            data: form.serialize(),
    
                            success: function (data) {
                                 alertModal('Company Edit','Updated successfully.','');
                            },
                            error: function (xhr, request, error) {
                                console.log("Failed");
                            },
                        }); 
                    });

                    $(".assign-invoice-to-project-checkbox").change(function(e) {
                        var checkboxInput = $(this);
                        var checked = checkboxInput.is(':checked');
                        var companyId = checkboxInput.val();

                        var autoInvoiceInput = $("#assign-invoice-to-project-name-input");
                        autoInvoiceInput.val(checked ? 1 : 0);

                        var companyInput = $("#company-id-assign-invoice-to-project-name-input");
                        companyInput.val(companyId);

                        var form = $("#company-assign-invoice-to-project-name");

                        $.ajax({
                            type: form.attr('method'),
                            url: form.attr('action'),
                            data: form.serialize(),
    
                            success: function (data) {
                                console.log("Successful");
                            },
                            error: function (xhr, request, error) {
                                console.log("Failed");
                            },
                        });
                    });

                    $('#company_table tbody > tr td:not(.remove-company)').click(function(e) {  
                        if (e.target.type != "checkbox") {
                            var id = $(this).closest('tr').find('.id_column').text();
                            var input = $("<input>").attr("type", "hidden")
                                .attr("name", "id")
                                .val(id);
    
                            $("#company-edit-form").append(input).submit();

                        }
                    });

                     $(document).find('[data-toggle="confirmation"]').confirmation({
                        onConfirm: function(event, element) { 
                            var company_id = element.closest('tr').find('.id_column').text();
                            var _token = '{{ csrf_token() }}';
                            $.ajax({
                                  url: URL+'deactivate-company',
                                  type:'POST',
                                  data: {'_token':_token, 'company_id':company_id,'status':0},
                                  success: function(result) {
                                    alertModal('Company Status','Deleted successfully.','reload');
                                    }
                                });
                            return false;
                        },
                        onCancel: function(event, element) { return false;},

                    });

                });
            </script>

        </div>
        <div class="form-group">
             <div class="pull-left">
                    Displaying {{ $companies->firstItem() }} to {{ $companies->lastItem() }} of {{ $companies->total() }} Records(s)
                </div>
             <div class="pull-right">
                @if(isset($companies))
                {{ $companies->links() }}
                @endif
            </div>  
            <div class="clearfix"></div>
         </div>
    </div>

</div>
<div id="loading">
    <img alt="ServiceTrack" src="{{ url('public/img/loading.gif') }}">
</div>
<style>
    div#loading {
        position: fixed;
        padding-top: 15%;
        left: 0;
        bottom: 0;
        right: 0;
    }
    #loading{
        display:none;
        position:fixed;
        top:0;left:0;
        width:100%;
        height:100%;
        background:rgba(0,0,0,0.5);
        text-align:center
    }
</style>
@endsection