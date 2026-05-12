@extends('layout.default')
@section('mytitle', 'Manage Packages')
@if(isset($plan))
@section('page', 'Packages  / Update')
@endif
@if(!isset($plan))
@section('page', 'Packages  / Add')
@endif
@section('content')
<style type="text/css">
    .validity-error,.price-error, .profit_price-error, .total_price-error{
        color: #e7515a;
        font-size: 13px;
        letter-spacing: 1px;
        margin-top: 5px;
        margin-left: 2px;
    }
    .action-buttons{
        display: flex;
        flex-direction: row;
        flex-wrap: nowrap;
        align-content: center;
        justify-content: space-between;
        align-items: center;
    }

    .steps > ul > .active a{
        background: #1d837a  !important;
        color: #ffffff  !important;
    }

    .tags-input-wrapper input { 
        margin: 0 auto; 
        display: none;
    }

    .tags-input-wrapper .tag a{
        display: none;
    }

    .tags-input-wrapper .tag{
        background-color: #1d837a !important;
        padding: 4px 3px 4px 3px;
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
                  <div class="alert alert-danger" id="pric-group-error" style="display: none;">
                          <ul>
                              <li>Super Admin Plan / Price / Plan Validity are mendatory</li>
                          </ul>
                      </div>
                <!-- <div class="row"> -->
                    <form id="user-form"  method="post" action="{{route('saveAdminPlan')}}" enctype="multipart/form-data" novalidate class="simple-example" >
                        @csrf
                        <input type="hidden" name="id" value="@if(isset($plan)){{$plan->id}}@endif">

                        <div class="form-row">                            
                            {{-- <div class="col-md-6 mb-4">
                                <label for="fullName">Title*</label>
                                <input type="text" class="form-control" id="title" readonly name="title" placeholder="Title" value="{{old('title')}}@if(isset($plan)){{$plan->title}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('title') {{ $message }} @enderror
                                </div>
                            </div> --}}

                            
                            <div class="col-md-6">
                                <label for="fullname">Admin Plan</label>
                                <select name="title" id="super_admin_plan" class="form-control select" required onchange="super_admin_plan1()">
                                    <option value="">Select Plan</option>
                                    @foreach ($super_admin_plans as $i => $plans)
                                        <option value="{{$plans->title}}"@if(isset($plan) && $plan->title == $plans->title) selected @endif data-id="{{$plans->id}}" data-title="{{$plans->title}}" data-counter="{{$i}}">{{$plans->title}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="description">Description</label>
                                <textarea type="text" class="form-control" readonly id="description" name="description" placeholder="Description">@if(isset($plan)){{$plan->description}}@endif</textarea>
                                <div class="invalid-feedback">
                                    @error('description') {{ $message }} @enderror
                                </div>
                            </div>
                            {{-- <div class="col-md-6 mb-4">
                                <label for="fullName">Choose Super Admin Plan*</label>                                
                                </select>
                                <div>
                                    <input type="text" id="skill-input">
                                </div>
                                <input type="hidden" name="super_admin_plan" id="super_admin_plan">
                                <button type="button" class="btn btn-dark" id="select_plan" data-toggle="modal" data-target="#exampleModal">Select Plan</button>
                                <div class="invalid-feedback">
                                    @error('super_admin_plan') {{ $message }} @enderror
                                </div>
                            </div> --}}
                        
                            <div class="col-md-6 mb-4">
                                <label for="fullName">Plan Validity (In Days)*</label>
                                <input type="text" class="form-control" readonly id="plan_validity" onkeyup="plan_validity1()" name="plan_validity" placeholder="Plan Validity" value="{{old('plan_validity')}}@if(isset($plan)){{$plan->plan_validity}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('plan_validity') {{ $message }} @enderror
                                </div>
                                <div class="validity-error"></div>
                            </div>

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
                                <label for="fullName">Max Selling Price for Admins</label>
                                <input type="text" class="form-control" id="max_selling_price_for_admin" name="max_selling_price_for_admin" placeholder="Max Selling Price for Admins" value="@if(isset($plan)){{$plan->max_selling_price_for_admin}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('max_selling_price_for_admin') {{ $message }} @enderror
                                </div>
                            </div> -->

                            <!-- <div class="col-md-6 mb-4">
                                <label for="fullName">Profit (%)</label>
                                <input type="text" class="form-control" id="profit_percentage" name="profit_percentage" placeholder="Profit (%)" value="{{old('profit_percentage')}}@if(isset($plan)){{$plan->profit_percentage}}@endif" >
                                <div class="invalid-feedback price-error">
                                    @error('profit_percentage') {{ $message }} @enderror
                                </div>
                                <div class="profit_percentage-error"></div>
                            </div> -->

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Profit Price</label>
                                <input type="text" class="form-control" id="profit_price" name="profit_price" placeholder="Profit Price" value="{{old('profit_price')}}@if(isset($plan)){{$plan->profit_price}}@endif" >
                                <div class="invalid-feedback price-error">
                                    @error('profit_price') {{ $message }} @enderror
                                </div>
                                <div class="profit_price-error"></div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Max Price</label>
                                <input type="text" class="form-control" id="max_price" name="max_price" placeholder="Max Price" value="{{old('max_price')}}@if(isset($plan)){{$plan->max_price}}@endif" >
                                <div class="invalid-feedback price-error">
                                    @error('max_price') {{ $message }} @enderror
                                </div>
                                <div class="profit_price-error"></div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Min Price</label>
                                <input type="text" class="form-control" id="min_price" name="min_price" placeholder="Min Price" value="{{old('min_price')}}@if(isset($plan)){{$plan->min_price}}@endif" >
                                <div class="invalid-feedback price-error">
                                    @error('min_price') {{ $message }} @enderror
                                </div>
                                <div class="profit_price-error"></div>
                            </div>


                            <div class="col-md-6 mb-4">
                                <label for="fullName">Plan Price*</label>
                                <input type="text" class="form-control" id="price" name="price" readonly placeholder="Price" value="{{old('price')}}@if(isset($plan)){{$plan->price}}@endif" required>
                                <div class="invalid-feedback price-error">
                                    @error('price') {{ $message }} @enderror
                                </div>
                                <div class="price-error"></div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Total Price*</label>
                                <input type="text" class="form-control" id="total_price" name="total_price" readonly placeholder="Total Price" value="{{old('total_price')}}@if(isset($plan)){{$plan->total_price}}@endif" required>
                                <div class="invalid-feedback price-error">
                                    @error('total_price') {{ $message }} @enderror
                                </div>
                                <div class="total_price-error"></div>
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

                            <!-- <div class="col-md-6 mb-4">
                                <label for="fullName">Min Profit (%) for Resellers</label>
                                <input type="text" class="form-control" onkeyup="min_profit_percent_for_reseller1()" id="min_profit_percentage_for_reseller" name="min_profit_percentage_for_reseller" placeholder="Min Profit (%) for Resellers" value="{{old('min_profit_percentage_for_reseller')}}@if(isset($plan)){{$plan->min_profit_percentage_for_reseller}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('min_profit_percentage_for_reseller') {{ $message }} @enderror
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="fullName">Max Profit (%) for Resellers</label>
                                <input type="text" class="form-control" onkeyup="max_profit_percent_for_reseller1()" id="max_profit_percentage_for_reseller" name="max_profit_percentage_for_reseller" placeholder="Max Profit (%) for Resellers" value="{{old('max_profit_percentage_for_reseller')}}@if(isset($plan)){{$plan->max_profit_percentage_for_reseller}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('max_profit_percentage_for_reseller') {{ $message }} @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Min Profit Price for Resellers</label>
                                <input type="text" class="form-control" onkeyup="min_profit_price_for_reseller1()" id="min_profit_price_for_reseller" name="min_profit_price_for_reseller" placeholder="Min Profit Price for Resellers" value="{{old('min_profit_price_for_reseller')}}@if(isset($plan)){{$plan->min_profit_price_for_reseller}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('min_profit_price_for_reseller') {{ $message }} @enderror
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="fullName">Max Profit Price for Resellers</label>
                                <input type="text" class="form-control" onkeyup="max_profit_price_for_reseller1()" id="max_profit_price_for_reseller" name="max_profit_price_for_reseller" placeholder="Max Profit Price for Resellers" value="{{old('max_profit_price_for_reseller')}}@if(isset($plan)){{$plan->max_profit_price_for_reseller}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('max_profit_price_for_reseller') {{ $message }} @enderror
                                </div>
                            </div> -->

                        </div>
                        <input type="hidden" id="plan_max_price">
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

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Plans</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="pill-vertical">
                        @php $i = 0; @endphp
                        @foreach($super_admin_plans as $plans)
                        <h3>{{$plans->title}}
                            <br><small>(INR{{$plans->price}}/- for {{$plans->plan_validity}} days)</small></h3>
                        <section>
                            <div class="action-buttons">
                                {{-- <h4>Channels</h4> --}}
                                <button type="button" class="btn btn-dark" id="select-{{$i}}" onclick="select('{{$plans->id}}','{{$i}}', '{{$plans->title}}')">Select</button>
                                <button type="button" style="display: none;" class="btn btn-dark" id="unselect-{{$i}}" onclick="unselect('{{$plans->id}}','{{$i}}', '{{$plans->title}}')">Unselect</button>
                            </div>
                            {{-- @foreach($plans->getChannel as $item)
                            <li>{{$item->channel_name}}</li>
                            @endforeach --}}
                        </section>
                        @php $i++; @endphp
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

@if(isset($plan))
<script>
    var super_admin_plans = "<?php print_r($super_admin_plans); ?>";
    var admin_s_plan_ids = "<?php // print_r($admin_s_plan_ids); ?>";
    super_admin_plans.forEach(function(value, index){
        console.log("value",value);
        admin_s_plan_ids.forEach(function(value1, index1){
        console.log("value1",value1);
            if(value.id == value1){
                // console.log(value1);
                select(value.id, index, value.title);
            }
        })
    })
</script>
@endif

<script>
    var plan_id_arr = [];
    var plan_name_arr = [];
    var plan_no_arr = [];
    var instance;

    
    $(document).ready(function(){
        $('li').removeClass('disabled');
        $('li').addClass('done');
        $('.actions').children('ul').hide();

        $("selector").steps({
            cssClass: 'pills wizard',
            stepsOrientation: "vertical"
        });

        instance = new TagsInput({
            selector: 'skill-input',
            duplicate: true
        });
        
    });

    $(document).ready(function () {        
        $('#pill-vertical-t-0').parent('li').removeClass('done');
    });

    // function select(plan_id, plan_no, plan_name){

        
    //     $('#pill-vertical-t-'+plan_no).parent('li').addClass('active');
    //     $('#select-'+plan_no).hide();
    //     $('#unselect-'+plan_no).show();
    //     unselect(plan_id_arr[0], plan_no_arr[0],plan_name_arr[0])
    //     plan_id_arr = [];
    //     plan_name_arr = [];
    //     plan_no_arr = [];
    //     plan_id_arr.push(plan_id);
    //     plan_name_arr.push(plan_name)
    //     plan_no_arr.push(plan_no)
    //     instance.destroy();
    //     instance = new TagsInput({
    //         selector: 'skill-input',
    //         duplicate: true
    //     });
    //     instance.addData(plan_name_arr)
    //     localStorage.setItem("plan_id", plan_id_arr);
    //     if(plan_name_arr.length > 0){
    //         document.getElementById('select_plan').innerHTML = 'Update Plan';
    //     }else{
    //         document.getElementById('select_plan').innerHTML = 'Select Plan'
    //     }

    //     document.getElementById('super_admin_plan').value = plan_id_arr;
    //     super_admin_plan1();
        
    // }    

    function unselect(plan_id, plan_no, plan_name){
        $('#pill-vertical-t-'+plan_no).parent('li').removeClass('active');
        // $('#pill-vertical-t-'+plan_no).parent('li').removeClass('current');
        $('#unselect-'+plan_no).hide();
        $('#select-'+plan_no).show();
        var index = plan_id_arr.indexOf(plan_id);
        if (index > -1) { 
            plan_id_arr.splice(index, 1); 
            plan_name_arr.splice(index, 1); 
        }
        instance.destroy();
        instance = new TagsInput({
            selector: 'skill-input',
            duplicate: true
        });
        instance.addData(plan_name_arr)
        localStorage.setItem("plan_id", plan_id_arr);
        if(plan_name_arr.length > 0){
            document.getElementById('select_plan').innerHTML = 'Update Plan';
        }else{
            document.getElementById('select_plan').innerHTML = 'Select Plan'
        }
        document.getElementById('super_admin_plan').value = plan_id_arr;
        super_admin_plan1();
    }

    function super_admin_plan1(){
        // plan_validity1();
        var super_admin_plan = $('#super_admin_plan').val();
        var profit_price = $('#profit_price').val();

        
        
        if(super_admin_plan.length){
            
            $.ajax({
              url: "{{ url('get-plan-details') }}",
              method: 'POST',
              data: {
                 'super_admin_plan': super_admin_plan,
                 "_token": "{{ csrf_token() }}"
              },
              success: function(result){
                console.log(result);
                
                 if(result.status == true){
                    data = result.data;

                    console.log(data.price, data.total_price);
                    
                    $('#price').val(data.price);                    
                    $('#plan_validity').val(data.plan_validity);
                    $('#description').val(data.description);
                    // $('#plan_max_price').val(data.plan_max_price);
                    var total_price = parseInt(data.price) + (parseInt(profit_price) || 0);
                    $('#total_price').val(total_price.toFixed(2));
                    $('.price-error').text('');
                 }else{
                    $('.price-error').text(data.msg);
                 }
            }});
        }else{
            $('#price').val('');
            $('#title').val('');
            $('#plan_validity').val('');
            $('#description').val('');
        }
    }

    // function getChannel(){
    //     var super_admin_plan = $('#super_admin_plan').val();
    //     $.ajax({
    //         url: "{{ url('get-channels-of-plan') }}",
    //         method: 'post',
    //         data: {
    //             'super_admin_plan': super_admin_plan,
    //             "_token": "{{ csrf_token() }}"
    //         },
    //         success: function(result){
    //             $('#channel').show();
    //             $('#channels').html(result)
    //     }});
    // }

    function plan_validity1() {
        var super_admin_plan = $('#super_admin_plan').val();
        var plan_validity = $('#plan_validity').val();
        
        if(super_admin_plan.length && plan_validity.trim() != ''){
            $.ajax({
              url: "{{ url('get-price') }}",
              method: 'post',
              data: {
                 'super_admin_plan': super_admin_plan,
                 'plan_validity': plan_validity,
                 "_token": "{{ csrf_token() }}"
              },
              success: function(result){
                 if(result.status){
                    $('#price').val(result.price);
                    $('.price-error').text('');
                 }else{
                    $('.price-error').text(result.msg);
                 }
            }});
        }else{
            $('#pric-group-error').show();
            return;
        }
    }

    //Reseller Profit
    function min_profit_percent_for_reseller1(){
        var price = parseInt($("#price").val()) + parseInt($('#profit_price').val());
        var min_profit_percent = $('#min_profit_percentage_for_reseller').val();
        var min_profit_price = (price * min_profit_percent) / 100;
        $('#min_profit_price_for_reseller').val(min_profit_price);
    }

    function max_profit_percent_for_reseller1(){
        var price = parseInt($("#price").val()) + parseInt($('#profit_price').val());
        var max_profit_percent = $('#max_profit_percentage_for_reseller').val();
        var max_profit_price = (price * max_profit_percent) / 100;
        $('#max_profit_price_for_reseller').val(max_profit_price);
    }

    function min_profit_price_for_reseller1(){
        var price = parseInt($("#price").val()) + parseInt($('#profit_price').val());
        var min_profit_price = $('#min_profit_price_for_reseller').val();
        var min_profit_percent = (min_profit_price * 100) / price;
        $('#min_profit_percentage_for_reseller').val(min_profit_percent);
    }

    function max_profit_price_for_reseller1(){
        var price = parseInt($("#price").val()) + parseInt($('#profit_price').val());
        var max_profit_price = $('#max_profit_price_for_reseller').val();
        var max_profit_percent = (max_profit_price * 100) / price;
        $('#max_profit_percentage_for_reseller').val(max_profit_percent);
    }

    $(document).ready(function(){
        $('#profit_percentage').keyup(function(){
            var profit_percentage = $(this).val();
            var price = $('#price').val();
            var super_admin_plan = $('#super_admin_plan').val();
            var plan_validity = $('#plan_validity').val();
            var profit_price = (price * profit_percentage) / 100;

            if(super_admin_plan.length && plan_validity.trim() != ''){
                $.ajax({
                url: "{{ url('check-max-selling-price-for-admin') }}",
                method: 'post',
                data: {
                    'super_admin_plan': super_admin_plan,
                    'profit_percentage' : profit_percentage,
                    'profit_price': profit_price,
                    'plan_validity': plan_validity,
                    'price': price,
                    "_token": "{{ csrf_token() }}"
                },
                // data: { "_token": "{{ csrf_token() }}", id : bd_id }
                success: function(result){
                    // console.log(result);
                    // var res = JSON.parse(result);
                    if(result.status){
                        $('#profit_price').val(result.profit_price);
                        $('.profit_price-error').text(result.msg);
                        // alert('result.msg');
                    }else{
                        $('#profit_price').val(result.profit_price);
                        $('.profit_price-error').text('');
                    }
                }});
            }else{
                $('#pric-group-error').show();
                return;
            }
        });

        $('#profit_price').keyup(function(){
            var profit_price = $(this).val();
            var price = $('#price').val();
            var super_admin_plan = $('#super_admin_plan').val();
            var plan_validity = $('#plan_validity').val();
            var plan_max_price = $('#plan_max_price').val();

            var total_price = parseInt(price) + (parseInt(profit_price) || 0);
            $('#total_price').val(total_price.toFixed(2));
            // change by me on 5 july
            // if(total_price > plan_max_price){
            //     $('.total_price-error').html('Total price should not be greater than '+plan_max_price+'.')
            //     return false;
            // }else{
            //     $('.total_price-error').html('')
            // }
            // change by me on 5 july

            // if(super_admin_plan.length && plan_validity.trim() != ''){
            //     $.ajax({
            //     url: "{{ url('check-max-selling-price-for-admin') }}",
            //     method: 'post',
            //     data: {
            //         'super_admin_plan': super_admin_plan,
            //         'profit_price': profit_price,
            //         'plan_validity': plan_validity,
            //         'price': price,
            //         "_token": "{{ csrf_token() }}"
            //     },
            //     success: function(result){
            //         if(result.status){
            //             $('.profit_price-error').text(result.msg);
            //         }else{
            //             $('.profit_price-error').text('');
            //         }
            //         var total_price = parseInt(price) + parseInt(result.profit_price);
            //         $('#total_price').val(total_price.toFixed(2));
            //         if(total_price > plan_max_price){
            //             $('.total_price-error').html('Total price should not be greater than '+plan_max_price+'.')
            //             return false;
            //         }else{
            //             $('.total_price-error').html('')
            //         }
            //     }});
            // }else{
            //     $('#pric-group-error').show();
            //     return;
            // }
        });

        // $('#profit_price').keyup(function (){
        //     var price = $('#price').val();
        //     var profit_price = $('#profit_price').val();
        //     var super_admin_plan = $('#super_admin_plan').val();
        //     // var total_price = Number(price) + Number(profit_price);
        //     // $('#total_price').val(total_price.toFixed(2));
        //     $.ajax({
        //         url: "{{ url('check-max-selling-price-for-admin') }}",
        //         method: 'post',
        //         data: {
        //             'super_admin_plan': super_admin_plan,
        //             'profit_price': profit_price,
        //             'plan_validity': plan_validity,
        //             'price': price,
        //             "_token": "{{ csrf_token() }}"
        //         },
        //         success: function (result){
        //             if(result.status){
        //                 console.log(1234);
        //             }
        //         }
        //     });
        //     // if(total_price > 130){
        //     //     $('.total_price-error').html('Total price should not be greater than 130.')
        //     //     return false;
        //     // }else{
        //     //     $('.total_price-error').html('')
        //     // }
        // });

    })


</script>

@endsection

@section('footer')
<!-- footer script start -->
<script type="text/javascript">

    // function validate() {
    //     // body...
    //     if($('#plan_validity').val().trim() < 30){
    //         $('.validity-error').text('Plan validity should be greater than and equal to 30 days')
    //         $('#plan_validity').val('')
    //         return;
    //     }

    //     var super_admin_plan = $('#super_admin_plan').val()
    //     var price = $('#price').val()
    //     var plan_validity = $('#plan_validity').val()
    //     var profit_percentage = $('#profit_percentage').val()
    //     var profit_price = $('#profit_price').val()
    //     if(super_admin_plan.length &&  price.trim() != '' && plan_validity.trim() != ''){
    //         $.ajax({
    //           url: "{{ url('check-max-selling-price-for-admin') }}",
    //           method: 'post',
    //           data: {
    //              'super_admin_plan': super_admin_plan,
    //              'price': price,
    //              'plan_validity': plan_validity,
    //              'profit_percentage': profit_percentage,
    //              'profit_price': profit_price,
    //              "_token": "{{ csrf_token() }}"
    //           },
    //           // data: { "_token": "{{ csrf_token() }}", id : bd_id }
    //           success: function(result){
    //              console.log(result);
    //              var res = JSON.parse(result);
    //              if(res.status){
    //                 $('#price').val('');
    //                 $('#profit_price').val(res.profit_price);
    //                 $('#profit_percentage').val(res.profit_percentage);
    //                 $('.price-error').text(res.msg);
    //                 // alert('res.msg');
    //              }else{
    //                 $('#user-form').submit();
    //              }
    //         }});
    //     }else{
    //         $('#pric-group-error').show();
    //         return;
    //     }
    // }
    // $('#plan_validity').on('blur', function() {
    //     if(this.value < 30){
    //         $('.validity-error').text('Plan validity should be greater than and equal to 30 days')
    //         $('#plan_validity').val('')
    //     }
    //     // body...
    // })
    // $('#super_admin_plan').on('change', function() {
    //     // console.log($('#super_admin_plan').val())
    //     var super_admin_plan = $('#super_admin_plan').val()
    //     var price = $('#price').val()
    //     var plan_validity = $('#plan_validity').val()

    //     $.ajax({
    //       url: "{{ url('check-max-selling-price-for-admin') }}",
    //       method: 'post',
    //       data: {
    //          'super_admin_plan': super_admin_plan,
    //          'price': price,
    //          'plan_validity': plan_validity,
    //          "_token": "{{ csrf_token() }}"
    //       },
    //       // data: { "_token": "{{ csrf_token() }}", id : bd_id }
    //       success: function(result){
    //          console.log(result);
    //          var res = JSON.parse(result);
    //          if(res.status){
    //             $('#price').val('');
    //             $('.price-error').text(res.msg);
    //             // alert('res.msg');
    //          }
    //       }});
    // });

    // $('#price').on('blur', function() {
    //     $('#loader-screen').css('display','inline-flex');
    //   // console.log($('#super_admin_plan').val())
    //   var super_admin_plan = $('#super_admin_plan').val()
    //   var price = $('#price').val();
    //   var plan_validity = $('#plan_validity').val()
    //   $.ajax({
    //       url: "{{ url('check-max-selling-price-for-admin') }}",
    //       method: 'post',
    //       data: {
    //          'super_admin_plan': super_admin_plan,
    //          'price': price,
    //          'plan_validity': plan_validity,
    //          "_token": "{{ csrf_token() }}"
    //       },
    //       // data: { "_token": "{{ csrf_token() }}", id : bd_id }
    //       success: function(result){
    //          console.log(result);
    //          $('#loader-screen').hide();
    //          var res = JSON.parse(result);
    //          if(res.status){
    //             $('#price').val('');
    //             // $('.price-error').text(res.msg);
    //             // alert('res.msg');
    //             $(".submit-fn").attr('disabled',true);
    //             $("div.price-error").text(res.msg);

    //          }else{
    //             $("div.price-error").text('');
    //             $(".submit-fn").attr('disabled',false);
    //          }
    //       }});
    // });
</script>
@endsection
