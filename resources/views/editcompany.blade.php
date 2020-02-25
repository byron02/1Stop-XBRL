@extends('layouts.frontsite')

@section('content')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>

<?php

    if (!is_null(session('company'))) {
        $company = session('company');
    }

    // to use this make sure that the input name and the property name of the object are equal
    function getValue($oldInputKey, $defaultObject) {
        return is_null(old($oldInputKey)) ? $defaultObject[$oldInputKey] : old($oldInputKey);
    }

?>

<div class="wrapper">

    @if (session('message'))
    <p class="toast_message">
        {{ session('message') }}
    </p>
    @endif

    @if (session('fail_message'))
        <p class="toast_message_fail">
            {{ session('fail_message') }}
        </p>
    @endif


    <div class="page-holder">

        <h2 class="content-title">Companies</h2> 

        <div class="clear"></div>

        <form id="company-edit-form" class="form-horizontal" method="POST" action="{{route('companies-update')}}">
            {{ csrf_field() }}

            <td><input type="hidden" class="form-control" name="id" value="{{getValue('id', $company)}}" /></td>

            <div id="company-edit" class="jobs">

                <table class="w-100">
    
                    <tr>
                        <td>Company Name *</td>
                        <td>Address Line 1 *</td>
                    </tr>
    
                    <tr>
                        
                        <td>
                            <div class="{{ $errors->has('name') ? ' has-error' : '' }}">

                                <input type="text" class="form-control" name="name" value="{{getValue('name', $company)}}" />
                
                                @if ($errors->has('name'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </td>
                    
                        <td>
                            <div class="{{ $errors->has('address1') ? ' has-error' : '' }}">

                                <input type="text" class="form-control" name="address1" value="{{getValue('address1', $company)}}" />

                                @if ($errors->has('address1'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('address1') }}</strong>
                                    </span>
                                @endif
                            </div>  
                        </td>
                           
                    </tr>
    
                    <tr>
                        <td>Address Line 2</td>
                        <td>Address Line 3</td>
                    </tr>
    
                    <tr>
                        <td><input type="text" class="form-control" name="address2" value="{{getValue('address2', $company)}}" /></td>
                        <td><input type="text" class="form-control" name="address3" value="{{getValue('address3', $company)}}" /></td>
                    </tr>
    
                    <tr>
                        <td>City *</td>
                        <td>Post Code *</td>
                    </tr>
    
                    <tr>
                        <td>
                            <div class="{{ $errors->has('city') ? ' has-error' : '' }}">

                                <input type="text" class="form-control" name="city" value="{{getValue('city', $company)}}" />
                                
                                @if ($errors->has('city'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('city') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </td>

                        <td>
                            <div class="{{ $errors->has('postcode') ? ' has-error' : '' }}">

                                <input type="text" class="form-control" name="postcode" value="{{getValue('postcode', $company)}}" />

                                @if ($errors->has('postcode'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('postcode') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </td>   
                    </tr>
    
                    <tr>
                        <td>Country</td>
                        <td>Phone *</td>
                    </tr>
    
                    <tr>
                        <td>
                            <select class="form-control" name="country">
    
                                <?php
                                    $selectedCountry  = !is_null(old('country')) ? old('country') : $company->country;
                                ?>
    
                                @foreach($countries as $country)
    
                                    @if($selectedCountry == $country->id))
                                        <option value="{{$country->id}}" selected>{{$country->name}}</option>
                                    @else
                                        <option value="{{$country->id}}">{{$country->name}}</option>
                                    @endif
    
                                @endforeach
                                
                            </select>
                        </td>

                        <td>
                            <div class="{{ $errors->has('phone') ? ' has-error' : '' }}">

                                <input type="text" class="form-control" name="phone" value="{{getValue('phone', $company)}}" />
    
                                @if ($errors->has('phone'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
    
                    <tr>
                        <td>Fax</td>
                        <td>Contact Email *</td>
                    </tr>
    
                    <tr>
                        <td><input type="text" class="form-control" name="fax" value="{{getValue('fax', $company)}}" /></td>

                        <td>
                            <div class="{{ $errors->has('email') ? ' has-error' : '' }}">

                                <input type="text" class="form-control" name="email" value="{{getValue('email', $company)}}" />
    
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>    
                        </td>
                    </tr>
    
                    <tr>
                        <td>Payment Method</td>
                        <td>Pricing Grid</td>
                    </tr>
    
                    <tr>
                        <td>
                            <select class="form-control" name="payment_method">
    
                            <?php
                                $selectedPaymentMethod = !is_null(old('payment_method')) ? old('payment_method') : $company->payment_method;
                            ?>
    
                            @foreach($paymentMethods as $paymentMethod)
    
                                @if($selectedPaymentMethod == $paymentMethod->id))
                                    <option value="{{$paymentMethod->id}}" selected>{{$paymentMethod->name}}</option>
                                @else
                                    <option value="{{$paymentMethod->id}}">{{$paymentMethod->name}}</option>
                                @endif
    
                            @endforeach
                            
                            </select>
                        </td>
                        <td>
                            <select class="form-control" name="pricing_grid">

                            <?php
                                $selectedPricingGrid = !is_null(old('pricing_grid')) ? old('pricing_grid') : $company->pricing_grid;
                            ?>

                            @foreach($pricingGrids as $pricingGrid)
                                @if($selectedPricingGrid == $pricingGrid->id)
                                    <option value="{{$pricingGrid->id}}" selected>{{$pricingGrid->name}}</option>
                                @else
                                    <option value="{{$pricingGrid->id}}">{{$pricingGrid->name}}</option>
                                @endif
                            @endforeach

                            </select>
                        </td>
                    </tr>
    
                    <tr>
                        <td>Pricing Reference *</td>
                        <td>Default Vendor</td>
                    </tr>
                    
                    <tr> 
                        <td>
                            <div class="{{ $errors->has('pricing_reference') ? ' has-error' : '' }}">

                                <input type="text" class="form-control" name="pricing_reference" value="{{getValue('pricing_reference', $company)}}" />
    
                                @if ($errors->has('pricing_reference'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('pricing_reference') }}</strong>
                                    </span>
                                @endif
                            </div>   
                        </td>
                        <td>
                            <select class="form-control" name="default_vendor">
                                <option value="" selected>Select Default Vendor</option>
                                @foreach($vendors as $vendor)
                                    @if($vendor->id == getValue('default_vendor', $company));
                                    <option value="{{$vendor->id}}" selected>{{$vendor->first_name}} {{$vendor->last_name}} ({{$vendor->username}})</option>
                                    @else
                                        <option value="{{$vendor->id}}">{{$vendor->first_name}} {{$vendor->last_name}} ({{$vendor->username}})</option>
                                    @endif
                                @endforeach
                            </select>
                        </td>

                    </tr>

                    <tr>
                        <td>Price Adjustment Rate</td>
                        <td style="display:none;" id="price-adjustment-rate-tr"></td>
                        <td>Type</td>
                    <tr>
                    
                    <tr>
                        <td>
                            <?php
                                $priceAdjustmentRates = [0, 5, 10, 15];
                            ?>

                            <select id="price-adjustment-rate-selector" class="form-control" name="discount_rate">

                                <?php
                                    $discoutRateExistOnList = false;
                                ?>

                                @foreach($priceAdjustmentRates as $priceAdjustmentRate)
                                    @if(getValue('discount_rate', $company) == $priceAdjustmentRate)
                                        <?php
                                            $discoutRateExistOnList = true;
                                        ?>
                                        <option value="{{$priceAdjustmentRate}}" selected>{{$priceAdjustmentRate}}</option>
                                    @else
                                        <option value="{{$priceAdjustmentRate}}">{{$priceAdjustmentRate}}</option>
                                    @endif
                                @endforeach

                                @if(!$discoutRateExistOnList)
                                    <option value="-1"selected>Others</option>
                                @else
                                    <option value="-1">Others</option>
                                @endif

                            <select>
                        </td>

                        <td style="display:none;" id="price-adjustment-rate-td">
                            @if(!$discoutRateExistOnList) 
                                <input name="price_adjustment_rate" class="form-control" type="number" min="0" value="{{$company->discount_rate}}"/>
                            @else
                                <input name="price_adjustment_rate" type="number" class="form-control" min="0"/>
                            @endif
                        </td>

                        <td>
                            <select class="form-control" name="adjustment_type">
                                @if(getValue('adjustment_type', $company))
                                    <option value="0">Discount</option>
                                    <option value="1" selected>Increase</option>
                                @else
                                    <option value="0" selected>Discount</option>
                                    <option value="1">Increase</option>
                                @endif
                            <select>
                        </td>
                    </tr>

                    <tr>
                        <td>Active
                        @if(getValue('active', $company))
                            <input type="checkbox" id="active-checkbox-input" name="active" checked="true" value="1" />
                        @else
                            <input type="checkbox" id="active-checkbox-input" name="active" value="0" />
                        @endif
                        </td>
                    </tr>
                </table>
                
            </div>

            <div class="clear"></div>
            
            <div class="form-group text-right">
                <button type="button" class="styled_btn" value="Cancel" onclick="window.location='{{url("/companies")}}'">Cancel</button>
                <button type="submit" class="styled_btn btn-dark" id="submit-btn">Save Changes</button>
            </div>
        
        </form>
    
    </div>

</div>

<script>

    $(document).ready(function() {
        $('#company-edit-form').submit(function(){
            var that = $(this);
            var data = that.serialize();
            var link = that.attr('action');

            $.post(link,data)
                .done(function(result){
                    alertModal('Company Edit','Updated successfully.','reload');
                });
            return false;
        });


        handleRateInputVisibility();
        
        $('#price-adjustment-rate-selector').on('change', function() {
            handleRateInputVisibility();
        })

        $("#active-checkbox-input").change(function() {
            var checked = $(this).is(':checked');
            $(this).val(checked ? 1 : 0);
        });

        function handleRateInputVisibility() {

            var selectedpriceAdjustmentRate = $("#price-adjustment-rate-selector").find(":selected").val();

            if (selectedpriceAdjustmentRate == -1){
                $("#price-adjustment-rate-td").show();
                $("#price-adjustment-rate-tr").show();
           } else {
               $("#price-adjustment-rate-td").hide();
               $("#price-adjustment-rate-tr").hide();
           }
        }
    });

</script>

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