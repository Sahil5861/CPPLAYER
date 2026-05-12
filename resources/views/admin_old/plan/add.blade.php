@extends('layout.default')
@section('mytitle', 'Manage Packages')
@if(isset($plan))
@section('page', 'Packages  / Update')
@endif
@if(!isset($plan))
@section('page', 'Packages  / Add')
@endif
@section('content')
<!-- <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"> -->

<style>
    .price-error{
        color: #e7515a;
        font-size: 13px;
        letter-spacing: 1px;
        margin-top: 5px;
        margin-left: 2px;
    }
</style>

<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
            <div class="widget-content widget-content-area br-6">
                @if(session()->has('message'))
                  <div class="alert alert-success alert-block">
                      <button type="button" class="close" data-dismiss="alert">×</button>
                      <strong>{{ session()->get('message') }}</strong>
                  </div>
                  @endif
                   @if ($errors->any())
                      <div class="alert alert-danger">
                          <ul>
                              @foreach ($errors->all() as $error)
                                  <li>{{ $error }}</li>
                              @endforeach
                          </ul>
                      </div>
                  @endif
                <!-- <div class="row"> -->
                    <form id="user-form"  method="post" action="{{route('saveSadminPlan')}}" enctype="multipart/form-data" novalidate class="simple-example" >
                        @csrf
                        <input type="hidden" name="id" value="@if(isset($plan)){{$plan->id}}@endif">

                        <div class="form-row">
                            <div class="col-md-6 mb-4">
                                <label for="fullName">Title*</label>
                                <input type="text" class="form-control" id="title" name="title" placeholder="Title" value="{{old('title')}}@if(isset($plan)){{$plan->title}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('title') {{ $message }} @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Plan Type</label>
                                <select name="plan_type" id="plan_type" class="form-control select">
                                    <option> select plan type</option>
                                    <option value="1" @if(isset($plan) && $plan->plan_type == 1){{'selected'}}@endif>Paid</option>
                                    <option value="0" @if(isset($plan) && $plan->plan_type == 0){{'selected'}}@endif>Trial</option>
                                </select>
                                <div class="invalid-feedback">
                                    @error('plan_type') {{ $message }} @enderror
                                </div>
                            </div>

                            <!-- <div class="col-md-6 mb-4">
                                <label for="fullName">Currency</label>
                                <input type="text" class="form-control" id="currency" name="currency" placeholder="Currency" value="@if(isset($plan)){{$plan->currency}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('currency') {{ $message }} @enderror
                                </div>
                            </div> -->


                            <div class="col-md-6 mb-4">
                                <label for="fullName">Price*</label>
                                <input type="text" class="form-control" onkeyup="check_price()" id="price" name="price" placeholder="Price" value="{{old('price')}}@if(isset($plan)){{$plan->price}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('price') {{ $message }} @enderror
                                </div>
                                <div class="price-error"></div>
                            </div>
                            @if(isset($plan) && $plan->net_admin_price != '')
                                @php $style = '' @endphp
                            @else
                                @php $style = 'display:none;' @endphp
                            @endif
                            {{-- <div class="col-md-6 mb-4 netadminprice" style="{{$style}}">
                                <label for="fullName">Net Admin Price*</label>
                                <input type="text" class="form-control" id="price" name="net_admin_price" placeholder="Net Admin Price" value="{{old('net_admin_price')}}@if(isset($plan)){{$plan->net_admin_price}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('net_admin_price') {{ $message }} @enderror
                                </div>
                            </div> --}}

                            <!-- <div class="col-md-6 mb-4">
                                <label for="fullName">Discount Type</label>
                                <select name="discount_type" id="discount_type" class="form-control select">
                                    <option value="flat" @if(isset($plan) && $plan->discount_type == 'flat'){{'selected'}}@endif>Flat</option>
                                    <option value="percent" @if(isset($plan) && $plan->discount_type == 'percent'){{'selected'}}@endif>Percentage</option>
                                </select>
                                <div class="invalid-feedback">
                                    @error('discount_type') {{ $message }} @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Discount</label>
                                <input type="text" class="form-control" id="discount" name="discount" placeholder="Discount" value="{{old('discount')}}@if(isset($plan)){{$plan->discount}}@endif">
                                <div class="invalid-feedback">
                                    @error('discount') {{ $message }} @enderror
                                </div>
                            </div> -->

                            <!-- <div class="col-md-6 mb-4">
                                <label for="fullName">Min Profit (%) for Admins</label>
                                <input type="text" class="form-control" onkeyup="min_profit_percent()" id="min_profit_percentage_for_admin" name="min_profit_percentage_for_admin" placeholder="Min Profit (%) for Admins" value="{{old('min_profit_percentage_for_admin')}}@if(isset($plan)){{$plan->min_profit_percentage_for_admin}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('min_profit_percentage_for_admin') {{ $message }} @enderror
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="fullName">Max Profit (%) for Admins</label>
                                <input type="text" class="form-control" onkeyup="max_profit_percent()" id="max_profit_percentage_for_admin" name="max_profit_percentage_for_admin" placeholder="Max Profit (%) for Admins" value="{{old('max_profit_percentage_for_admin')}}@if(isset($plan)){{$plan->max_profit_percentage_for_admin}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('max_profit_percentage_for_admin') {{ $message }} @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Min Profit Price for Admins</label>
                                <input type="text" class="form-control" onkeyup="min_profit_price()" id="min_profit_price_for_admin" name="min_profit_price_for_admin" placeholder="Min Profit Price for Admins" value="{{old('min_profit_price_for_admin')}}@if(isset($plan)){{$plan->min_profit_price_for_admin}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('min_profit_price_for_admin') {{ $message }} @enderror
                                </div>
                            </div>-->

                            <!-- <div class="col-md-6 mb-4" id="max_price">
                                <label for="fullName">Max Profit Price for Admins</label>
                                <input type="text" class="form-control" onkeyup="max_profit_price()" id="max_profit_price_for_admin" name="max_profit_price_for_admin" placeholder="Max Profit Price for Admins" value="{{old('max_profit_price_for_admin')}}@if(isset($plan)){{$plan->max_profit_price_for_admin}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('max_profit_price_for_admin') {{ $message }} @enderror
                                </div>
                            </div>  -->

                            
                            

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Plan Validity <small id="default_validity">(Default 30 days)</small></label>
                                <input type="number" class="form-control" id="plan_validity" name="plan_validity" value="{{old('plan_validity')}}@if(isset($plan)){{$plan->plan_validity}}@endif">
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="fullName">Status</label>
                                <select name="status" id="status" class="form-control select">
                                    <option value="1" @if(isset($plan) && $plan->status == 1){{'selected'}}@endif>Active</option>
                                    <option value="0" @if(isset($plan) && $plan->status == 0){{'selected'}}@endif>De-Active</option>
                                </select>
                                <div class="invalid-feedback">
                                    @error('status') {{ $message }} @enderror
                                </div>
                            </div>

                            <div class="col-md-12 mb-4">
                                <label for="description">Description</label>
                                <textarea type="text" class="form-control" id="description" name="description" placeholder="Description">{{old('description')}}@if(isset($plan)){{$plan->description}}@endif</textarea>
                                <div class="invalid-feedback">
                                    @error('description') {{ $message }} @enderror
                                </div>
                            </div>

                            
                            <!-- <div class="col-md-6 mb-4">
                                <label for="fullName">Plan Expire Date</label>
                                <input type="date" class="form-control" id="plan_expire" name="plan_expire" value="{{old('plan_expire')}}@if(isset($plan)){{$plan->plan_expire}}@endif" required>
                            </div> -->

                            {{-- <div class="col-md-6 mb-4">
                                <label for="fullName">Plan Max Price* </label>
                                <input type="text" class="form-control" placeholder="Plan Max Price" id="plan_max_price" name="plan_max_price" value="{{old('plan_max_price')}}@if(isset($plan)){{$plan->plan_max_price}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('plan_max_price') {{ $message }} @enderror
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="fullName">Genre*</label>
                                <select name="genre" id="genre" class="form-control select">
                                    <option value="all" @if(isset($plan) && $plan->status == 'all'){{'selected'}}@endif>All</option>
                                    @foreach($genres as $item)
                                    <option value="{{$item->id}}" @if(isset($plan) && $plan->genre == $item->id){{'selected'}}@endif>{{$item->title}}</option>
                                    @endforeach
                                </select>
                            </div> --}}
                        </div>
                        {{-- <div class="row">
                            <div class="col-12">
                                <label for="fullName">Channels*</label>
                            </div>
                        </div> --}}
                        {{-- <div class="row">
                            <div class="col-5">
                                <select name="from" id="undo_redo" class="form-control" size="12" multiple="multiple">
                                    <?php
                                        foreach($channels as $channel){
                                            if(isset($channel_ids) && in_array($channel->id,$channel_ids)){
                                                echo '<option value="'.$channel->id.'" selected>'.$channel->channel_number.'-'.$channel->channel_name.'</option>';
                                            }else{
                                                echo '<option value="'.$channel->id.'">'.$channel->channel_number.'-'.$channel->channel_name.'</option>';
                                            }

                                        }
                                     ?>
                                </select>
                            </div>

                            <div class="col-2">
                                <button type="button" id="undo_redo_undo" class="btn btn-primary btn-block">undo</button>
                                <!-- <button type="button" id="undo_redo_rightAll" class="btn btn-default btn-block"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevrons-right"><polyline points="13 17 18 12 13 7"></polyline><polyline points="6 17 11 12 6 7"></polyline></svg></button> -->
                                <button type="button" id="undo_redo_rightSelected" class="btn btn-default btn-block"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg></button>
                                <button type="button" id="undo_redo_leftSelected" class="btn btn-default btn-block"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-left"><polyline points="15 18 9 12 15 6"></polyline></svg></button>
                                <!-- <button type="button" id="undo_redo_leftAll" class="btn btn-default btn-block"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevrons-left"><polyline points="11 17 6 12 11 7"></polyline><polyline points="18 17 13 12 18 7"></polyline></svg></button> -->
                                <!-- <button type="button" id="undo_redo_redo" class="btn btn-warning btn-block">redo</button> -->
                            </div>

                            <div class="col-5">
                                <select name="channels[]" id="undo_redo_to" class="form-control" size="12" multiple="multiple" required></select>
                            </div>
                        </div> --}}
                        @if(isset($plan))
                        <button class="btn btn-primary submit-fn mt-2" type="submit">Update</button>
                        @else
                        <button class="btn btn-primary submit-fn mt-2" type="submit">Add</button>
                        @endif

                    </form>
                <!-- </div> -->
            </div>
        </div>
    </div>

</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>

    function check_price(){
        // var plan_type = $('#plan_type').val();
        // var price = $("#price").val();
        // var plan_max_price = $('#plan_max_price').val();
        // if(plan_type == 0 && Number(price) > Number(plan_max_price)){
        //     $('.price-error').html('price should not be greater than '+plan_max_price+'.');
        //     return false;   
        // }else{
        //     $('.price-error').html('');
        // }
    }

    //Admin Profit
    function min_profit_percent(){
        var price = $("#price").val();
        var min_profit_percent = $('#min_profit_percentage_for_admin').val();
        var min_profit_price = (price * min_profit_percent) / 100;
        $('#min_profit_price_for_admin').val(min_profit_price);
    }

    function max_profit_percent(){
        var price = $("#price").val();
        var max_profit_percent = $('#max_profit_percentage_for_admin').val();
        var max_profit_price = (price * max_profit_percent) / 100;
        $('#max_profit_price_for_admin').val(max_profit_price);
    }

    function min_profit_price(){
        var price = $("#price").val();
        var min_profit_price = $('#min_profit_price_for_admin').val();
        var min_profit_percent = (min_profit_price * 100) / price;
        $('#min_profit_percentage_for_admin').val(min_profit_percent);
    }

    function max_profit_price(){
        check_price();
    }

    $(document).ready(function(){

        if ($('#plan_type').val() == 0) {
            $('#default_validity').html('(Default 3 days)')
            $('#plan_validity').val(3).prop('disabled', true);
        }
        else{
            $('#default_validity').html('(Default 30 days)')
            $('#plan_validity').prop('disabled', false);
        }

        $('#plan_type').change(function (){

            if($(this).val() == 0){
                $('#max_price').show();
                $('#default_validity').html('(Default 3 days)')
                $('#plan_validity').val(3).prop('disabled', true);
                // $('.netadminprice').show();
            }else{
                $('#max_price').hide();
                $('#default_validity').html('(Default 30 days)')
                $('#plan_validity').prop('disabled', false);
                // $('.netadminprice').hide();
            }

            check_price();
        })

        $('#genre').change(function(){
            var genre = $(this).val();
            var html = '';
            $('#undo_redo').empty();
            // $('#undo_redo_to').empty();
            $.ajax({
            url: "{{ url('get-channel-by-genre') }}",
            method: 'post',
            data: {
                'genre': genre,
                "_token": "{{ csrf_token() }}"
            },
            success: function(result){
                var response = JSON.parse(result);
                $.each(response, function(index, value){
                    html += '<option value="'+value.id+'">'+value.channel_number+'-'+value.channel_name+'</option>';
                })
                $('#undo_redo').append(html);
            }
        });
        })
    });

</script>


@endsection

@section('footer')

<!-- footer script if required -->
@endsection
