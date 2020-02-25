@extends('layouts.frontsite')

@section('content')

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>

    <div class="wrapper">

        <div class="page-holder">
            <div class="form-group">
                <h2 class="content-title">Pricing Grid</h2>
                <div class="clearfix"></div>
            </div>
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                <script>
                    $(function(){
                        setTimeout(function(){
                            $('[data-info="{{session('type')}}"]').click();
                        },100);
                    })
                </script>
            @endif
            <div class="form-group">
                {{ Request::get('grid') }}
                <div class="pull-left grid-button-group">
                <input type="hidden" id="grid-selected" value="{{ $grid_info[0]->id }}">
                @foreach($grid_info as $k => $info)
                    <button type="button" data-info="{{ $info->id }}" class="styled_btn getPricingGrid {{ $k ==  $active_grid  ? 'btn-danger' : '' }}">{{ $info->name }}</button>
                @endforeach
                </div>
                <div class="pull-right">
                    <a class="styled_btn submit_btn add-btn">
                        <span>
                            ADD PRICE
                        </span>
                    </a>
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="table-responsive">

                <table class="table table-condensed">
                    <thead>
                        <tr class="dark">
                            <td>Number of Pages</td>
                            <td>Price</td>
                            <td>Turnaround Time</td>
                            <td>Work Type</td>
                            <td>Taxonomy</td>
                            <td class="text-center">Actions</td>
                        </tr>
                    </thead>
                    <tbody class="pricing-body">
                        @foreach($pricingMapA as $pricingMapItem)
                            <tr>
                                <td>{{$pricingMapItem['floor_page_count']}} - {{$pricingMapItem['ceiling_page_count']}}</td>
                                <td>{{$pricingMapItem['price']}}</td>
                                <td>{{$pricingMapItem['turnaround_name']}}</td>
                                <td>{{$pricingMapItem['work_name']}}</td>
                                <td>{{$pricingMapItem['group_name']}}</td>
                                <td class="text-center">
                                    <button class="edit_btn styled_btn" value="{{$pricingMapItem['idpricing_grid']}}">Edit</button>
                                    <button class="delete-grid styled_btn" value="{{$pricingMapItem['idpricing_grid']}}"
                                        data-toggle="confirmation" data-btn-ok-label=" Yes" data-btn-ok-class="btn btn-dark"
                                        data-placement="top"
                                        data-btn-cancel-label="No" data-btn-cancel-class="btn btn-danger"
                                        data-btn-cancel-icon-class="fa" data-btn-cancel-icon-content="close"
                                        data-title="Are you sure?"
                                    >
                                        Delete
                                    </button>
                                </td>
                            </tr>

                        @endforeach
                    </tbody>

                </table>

            </div>
            <div>
                <div class="pull-left">
                    Displaying {{ $pricingMapA->firstItem() }} to {{$pricingMapA->lastItem()}} of {{ $pricingMapA->total() }} Records(s) | Items to Display :
                    <select name="limit" class="pricing-limit">
                        <option {{ request('limit') == 10 ? 'selected' : '' }}>10</option>
                        <option {{ request('limit') == 20 ? 'selected' : '' }}>20</option>
                        <option {{ request('limit') == 50 ? 'selected' : '' }}>50</option>
                        <option {{ request('limit') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <div class="pull-right">
                     @if(isset($pricingMapA))
                        {{ $pricingMapA->links() }}
                    @endif
                </div>
                <div class="clearfix"></div>
            </div>
        </div>

    </div>

    <div id="loading">
        <img alt="ServiceTrack" src="{{ url('public/img/loading.gif') }}">
    </div>
    <div style="display:none" id="add-price-content">

                <form class="form-horizontal" action="{{route('save-pricing')}}" method="post">

                    {{ csrf_field() }}

                    <table class="datagrid_wrap">
                        <tr>
                            <td>Floor Page</td>
                            <td>
                                <div class="{{ $errors->has('floor_page_count') ? ' has-error' : '' }}">
                                    <input type="number" name="floor_page_count" />
                                </div>

                                @if ($errors->has('floor_page_count'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('floor_page_count') }}</strong>
                                    </span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Ceiling Page</td>
                            <td>
                                <div class="{{ $errors->has('ceiling_page_count') ? ' has-error' : '' }}">
                                    <input type="number" name="ceiling_page_count" />
                                </div>

                                @if ($errors->has('ceiling_page_count'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('ceiling_page_count') }}</strong>
                                    </span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Work Around Time</td>
                            <td>
                                <select name="turnaround_time">
                                    @foreach($turnarounds as $turnaround)
                                        <option value="{{$turnaround->id}}">{{$turnaround->name}}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Work Type</td>
                            <td>
                                <select name="work_type">
                                    @foreach($worktypes as $worktype)
                                        <option value="{{$worktype->id}}">{{$worktype->name}}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Type</td>
                            <td>
                                <select name="type" id="grid_type">
                                    @foreach($grid_info as $gr)
                                        <option {{session('type') == $gr['id'] ? 'selected' : ''}} value="{{$gr['id']}}">{{$gr['name']}}</option>
                                    @endforeach
                                        <option value="new_grid">+ Add Grid</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Taxonomy Group</td>
                            <td>
                                <select name="taxonomy_group">
                                    @foreach($taxonomyGroups as $taxonomyGroup)
                                        <option value="{{$taxonomyGroup->id}}">{{$taxonomyGroup->name}}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Price</td>
                            <td>
                                <div class="{{ $errors->has('price') ? ' has-error' : '' }}">
                                    <input type="number" name="price" />
                                </div>

                                @if ($errors->has('price'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('price') }}</strong>
                                    </span>
                                @endif
                            </td>
                        </tr>

                    </table>

                    <table align="center">
                        <tbody><tr>
                            <td colspan="2">
                                <p align="center">
                                    <input class="styled_btn " type="submit" value="Add" id="btn_submit">&nbsp;&nbsp;
                                </p>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                </form>

            </div>

            <div style="display:none" id="edit-price-content">

                <form class="form-horizontal" action="{{action('PricingGridController@update')}}" method="post">

                    {{ csrf_field() }}

                    <table class="datagrid_wrap">
                        <tr>
                            <td>Floor Page</td>
                            <td>
                                <div class="{{ $errors->has('floor_page_count') ? ' has-error' : '' }}">
                                    <input id="floor_page_count_input" type="number" name="floor_page_count" />
                                </div>

                                @if ($errors->has('floor_page_count'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('floor_page_count') }}</strong>
                                    </span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Ceiling Page</td>
                            <td>
                                <div class="{{ $errors->has('ceiling_page_count') ? ' has-error' : '' }}">
                                    <input id="ceiling_page_count_input" type="number" name="ceiling_page_count" />
                                </div>

                                @if ($errors->has('ceiling_page_count'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('ceiling_page_count') }}</strong>
                                    </span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Work Around Time</td>
                            <td>
                                <select id="turnaround_time_select" name="turnaround_time">
                                    @foreach($turnarounds as $turnaround)
                                        <option value="{{$turnaround->id}}">{{$turnaround->name}}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Work Type</td>
                            <td>
                                <select id="work_type_select" name="work_type">
                                    @foreach($worktypes as $worktype)
                                        <option value="{{$worktype->id}}">{{$worktype->name}}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Type</td>
                            <td>
                                <select id="type_select" name="type">
                                    @foreach($grid_info as $gr)
                                        <option value="{{$gr['id']}}">{{$gr['name']}}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Taxonomy Group</td>
                            <td>
                                <select id="taxonomy_group_select" name="taxonomy_group">
                                    @foreach($taxonomyGroups as $taxonomyGroup)
                                        <option value="{{$taxonomyGroup->id}}">{{$taxonomyGroup->name}}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Price</td>
                            <td>
                                <div class="{{ $errors->has('price') ? ' has-error' : '' }}">
                                    <input id="price_input" type="number" name="price" />
                                </div>

                                @if ($errors->has('price'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('price') }}</strong>
                                    </span>
                                @endif
                            </td>
                        </tr>

                    </table>

                    <table align="center">
                        <tbody><tr>
                            <td colspan="2">
                                <p align="center">
                                    <input class="styled_btn" type="submit" value="Save" id="btn_submit">&nbsp;&nbsp;
                                </p>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <input id="price_id_input" name="price_id" type="hidden" />

                </form>

            </div>

    <!-- Modal -->
    <div id="gridType-modal" class="modal fade" role="dialog">
      <div class="modal-dialog modal-sm">
        <form id="grid_type_form">
            {{ csrf_field() }}
            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">New Grid</h4>
              </div>
              <div class="modal-body">
                    <div class="form-group">
                        <label>Grid Name</label> <small id="grid-error"></small>
                        <input type="text" class="form-control" name="grid" id="grid-field" placeholder="New Grid Name" required>
                    </div>
              </div>
              <div class="modal-footer">
                <button class="btn btn-primary">Add</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
            </div>
        </form>
      </div>
    </div>

    <script>
        $(document).ready(function () {
            $('.pricing-limit').change(function(){
                var that = $(this);
                var grid = $('.getPricingGrid.btn-danger').attr('data-info');
                
                window.location = URL+'pricing-grid/'+grid+'?limit='+that.val();
            });
            $('.getPricingGrid').click(function(){
                var that = $(this);
                var info = that.attr('data-info');
                window.location = URL+'pricing-grid/'+info
                // $('.getPricingGrid').removeClass('btn-danger');
                // that.addClass('btn-danger');
                // $('.pricing-body').html('loading...');
                // $('#loading').show();
                // $.get(URL+'pricing-grid-table/'+info+'/blade')
                //     .done(function(result){
                //         var str = '';
                //             var data = $.parseJSON(result);
                //             if(data.length > 0)
                //             {
                //                 $.each(data,function(i,x){
                //                     str += '<tr>'+
                //                                 '<td>'+ x.floor_page_count +' - '+ x.ceiling_page_count +'</td>'+
                //                                 '<td>'+ x.price.toFixed(2) +'</td>'+
                //                                 '<td>'+x.turnaround_name+'</td>'+
                //                                 '<td>'+x.work_name+'</td>'+
                //                                 '<td>'+x.group_name+'</td>'+
                //                                 '<td align="center">'+
                //                                     '<button class="edit_btn styled_btn" value="'+x.idpricing_grid+'">Edit</button>'+
                //                                 '</td>'+
                //                             '</tr>';
                //                 });
                //             }
                //             else
                //             {
                //                 str =   '<tr>'+
                //                             '<td colspan="6">No records found.</td>'+
                //                         '</tr>';
                //             }
                //         $('.pricing-body').html(str);
                //         $('#loading').hide();
                //         editGridData();
                //     });
            });


            addGrid();

            $('#grid_type_form').submit(function(){
                var that = $(this);
                var data = that.serialize();
                var grid = $('#grid-field').val();
                var flag = false;
                 $('#grid-error').text('');
                 var str = '';
                $('#grid_type option').each(function(){
                    if($(this).html() == grid)
                    {
                        flag = true;
                    }

                    if($(this).val() != 'new_grid')
                    {
                        str += '<option value="'+$(this).val()+'">'+$(this).html()+'</option>';
                    }
                });


                if(flag == true)
                {
                    that[0].reset();
                    $('#grid-error').text('Grid name already exists!');
                }
                
                $.post(URL+'add-grid',data)
                    .done(function(result){

                        if(result != '')
                        {
                            var x = $.parseJSON(result);
                            str += '<option value="'+x.id+'" selected>'+ x.name +'</option>';
                            str += '<option value="new_grid">+ Add Grid</option>';
                             $('#grid_type').html(str);
                             addGrid();
                             $('#gridType-modal').modal('hide');
                        }
                    })

              
                

                return false;
            });



            $(".add-btn").click(function () {
                var grid = $('.getPricingGrid.btn-danger').attr('data-info');
                $('#grid_type option[value="'+grid+'"]').prop('selected',true);
                $.fancybox({
                    href: '#add-price-content',
                });
            });

            editGridData();
            deleteGridData();

        });
        var price_grid = 0;
        var grid_row = '';
        function deleteGridData()
        {
            
            $(".delete-grid").click(function () {
                var that = $(this);
                price_grid = that.val();
                grid_row = that;
            });

             $('[data-toggle="confirmation"]').confirmation({
                onConfirm: function() { 
                     $.get(URL+'remove-grid/'+price_grid)
                        .done(function(result){
                            price_grid = 0;
                            alertModal('Pricing Grid Status','Pricing Deleted successfully.','');
                            grid_row.closest('tr').remove();
                        });
                    
                    
                },
                onCancel: function() { 
                         price_grid = 0;
                        return false;
                    },
            });
        }
    
        function editGridData()
        {
            $(".edit_btn").click(function () {

                $.fancybox({
                    href: '#edit-price-content',
                });
                var priceId = $(this).val();
                populatePricingForm(priceId);
            });
        }

         function addGrid()
        {
            $('#grid_type').change(function(){
                var that = $(this);

                if(that.val() == 'new_grid')
                {
                    $('#gridType-modal').modal('show');
                    that.find('option:nth-child(1)').prop('selected',true);
                    $('.fancybox-overlay').css({
                                                    'z-index':'1000'
                    });
                }
            });
        }

        function populatePricingForm(priceId) {
            $.get(URL+'/load-grid-info/'+priceId)
                .done(function(map){
                    var pricing = $.parseJSON(map);

                    $("#price_id_input").val(priceId);

                    $("#floor_page_count_input").val(pricing.floor_page_count);
                    $("#ceiling_page_count_input").val(pricing.ceiling_page_count);
                    $("#turnaround_time_select option[value="+pricing.turnaround_time+"]").prop('selected', true);
                    $("#work_type_select option[value="+pricing.work_type+"]").prop('selected', true);
                    $("#taxonomy_group_select option[value="+pricing.taxonomy_group+"]").prop('selected', true);
                    $("#type_select option[value="+pricing.pricing_info_id+"]").prop('selected', true);
                    $("#price_input").val(parseFloat(pricing.price));
                });

            }
    </script>

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