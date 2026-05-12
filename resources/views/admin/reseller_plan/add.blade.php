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
                              <li>Admin Plan / Price / Plan Validity are mendatory</li>
                          </ul>
                      </div>
                <!-- <div class="row"> -->
                    <form id="user-form"  method="post" action="{{route('saveResellerPlan')}}" enctype="multipart/form-data" novalidate class="simple-example" >
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
                                <select name="title" id="admin_plan" class="form-control select" required onchange="admin_plan1()">
                                    <option value="">Select Plan</option>
                                    @foreach ($admin_plans as $i => $plans)
                                        <option value="{{$plans->title}}"  {{ (isset($plan) && $plan->title == $plans->title) ? 'selected' : '' }}  data-id="{{$plans->id}}" data-title="{{$plans->title}}" data-counter="{{$i}}">{{$plans->title}}</option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="col-md-6 mb-4">
                                <label for="description">Description</label>
                                <textarea type="text" class="form-control" id="description" readonly name="description" placeholder="Description">{{old('description')}}@if(isset($plan)){{$plan->description}}@endif</textarea>
                                <div class="invalid-feedback">
                                    @error('description') {{ $message }} @enderror
                                </div>
                            </div>

                            {{-- <div class="col-md-6 mb-4">
                                <label for="fullName">Choose Admin Plan*</label>
                                <!-- <select multiple name="admin_plan[]" onchange="admin_plan1()" id="admin_plan" class="form-control select" required>
                                    <?php
                                    // foreach($admin_plans as $a_plan){
                                    //     if(isset($admin_plan_ids) && in_array($a_plan->id,$admin_plan_ids)){
                                    //         echo '<option value="'.$a_plan->id.'" selected>'.$a_plan->title.'</option>';
                                    //     }else{
                                    //         echo '<option value="'.$a_plan->id.'">'.$a_plan->title.'</option>';
                                    //     }

                                    // }
                                 ?>
                                </select> -->
                                <div>
                                    <input type="text" id="skill-input">
                                </div>
                                <input type="hidden" name="admin_plan" id="admin_plan">
                                <button type="button" class="btn btn-dark" id="select_plan" data-toggle="modal" data-target="#exampleModal">Select Plan</button>
                                <div class="invalid-feedback">
                                    @error('admin_plan') {{ $message }} @enderror
                                </div>
                            </div> --}}

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Plan Validity (In Days)*</label>
                                <input type="text" class="form-control" id="plan_validity" readonly onkeyup="plan_validity1()" name="plan_validity" placeholder="Plan Validity" value="{{old('plan_validity')}}@if(isset($plan)){{$plan->plan_validity}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('plan_validity') {{ $message }} @enderror
                                </div>
                                <div class="validity-error"></div>
                            </div>

                           <!--  <div class="col-md-6 mb-4">
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

                            <input type="hidden" class="form-control" id="admin_max_price">
                            <input type="hidden" class="form-control" id="admin_min_price">


                            <div class="col-md-6 mb-4">
                                <label for="fullName">Profit Price</label>
                                <input type="text" class="form-control" id="profit_price"  name="profit_price" placeholder="Profit Price" oninput="calculateTotal(this)" value="{{old('profit_price')}}@if(isset($plan)){{$plan->profit_price}}@endif">
                                <div class="invalid-feedback price-error">
                                    @error('profit_price') {{ $message }} @enderror
                                </div>
                                <div class="profit_price-error"></div>
                            </div>



                            <div class="col-md-6 mb-4">
                                <label for="fullName">Plan Price*</label>
                                <input type="text" class="form-control" id="price" readonly name="price" placeholder="Price" value="{{old('price')}}@if(isset($plan)){{$plan->price}}@endif" required>
                                <div class="invalid-feedback price-error">
                                    @error('price') {{ $message }} @enderror
                                </div>
                                <div class="price-error"></div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Total Price*</label>
                                <input type="text" class="form-control" id="total_price" readonly name="total_price" placeholder="Total Price" value="{{old('total_price')}}@if(isset($plan)){{$plan->total_price}}@endif" required>
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

</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

@if(isset($plan))
<script>
    // var admin_plan = $('#admin_plan').val();
    // $.ajax({
    //     url: "{{ url('get-channels-of-plan-reseller') }}",
    //     method: 'post',
    //     data: {
    //         'admin_plan': admin_plan,
    //         "_token": "{{ csrf_token() }}"
    //     },
    //     success: function(result){
    //         $('#channel').show();
    //         $('#channels').html(result)
    // }});




    
</script>
@endif

<script>
function calculateTotal(elem) {

    let priceInput = document.getElementById('price'); // Plan Price
    let profitInput = document.getElementById('profit_price');
    let totalInput = document.getElementById('total_price');

    let price = parseFloat(priceInput.value) || 0;
    let profit = parseFloat(profitInput.value.trim()) || 0;

    // Validation (only numbers allowed)
    if (isNaN(profit) || profit < 0) {
        profitInput.style.border = '1px solid red';
        totalInput.value = price.toFixed(2);
        return;
    } else {
        profitInput.style.border = '';
    }

    let total = price + profit;

    totalInput.value = total.toFixed(2);
}
</script>
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

    function disableSubmit(){
        $('.submit-fn').prop('disabled', true);
    }

    function enableSubmit(){
        $('.submit-fn').prop('disabled', false);
    }

    $('#profit_price').on('blur', function () {
        let max_value = parseFloat($('#admin_max_price').val());
        let min_value = parseFloat($('#admin_min_price').val());
        let currentVal = $(this).val();

        let errorbox = $('.profit_price-error');

        if (currentVal === "") return; // skip empty input

        currentVal = parseFloat(currentVal);
        enableSubmit();
        errorbox.html('');

        if (currentVal > max_value) {            
            errorbox.html(`Enter Amount less than or equal to ${max_value}`)
            // remove last typed character
            $(this).val($(this).val().slice(0, -1));
            disableSubmit();
        }

        if (currentVal < min_value) {            
            errorbox.html(`Enter Amount more than or equal to ${min_value}`)
            // remove last typed character
            $(this).val($(this).val().slice(0, -1));
            disableSubmit();
        }
    });

    function select(plan_id, plan_no, plan_name){
        $('#pill-vertical-t-'+plan_no).parent('li').addClass('active');
        $('#select-'+plan_no).hide();
        $('#unselect-'+plan_no).show();
        unselect(plan_id_arr[0], plan_no_arr[0],plan_name_arr[0])
        plan_id_arr = [];
        plan_name_arr = [];
        plan_no_arr = [];
        plan_id_arr.push(plan_id);
        plan_name_arr.push(plan_name)
        plan_no_arr.push(plan_no)
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

        document.getElementById('admin_plan').value = plan_id_arr;
        admin_plan1();
        
    }

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
        document.getElementById('admin_plan').value = plan_id_arr;
        admin_plan1();
    }

    function admin_plan1(){
        // plan_validity1();
        var admin_plan = $('#admin_plan').val();

        var admin_plan_id = $('#admin_plan option:selected').data('id');
        
        var profit_price = $('#profit_price').val() || 0;
        
        if(admin_plan.length){
            $.ajax({
              url: "{{ url('get-admin-plan-details') }}",
              method: 'post',
              data: {
                 'admin_plan': admin_plan,
                 'admin_plan_id': admin_plan_id,
                 "_token": "{{ csrf_token() }}"
              },
              success: function(result){
                 if(result.status){

                    console.log(result);

                    let data = result.data;
                    
                    $('#price').val(data.price);
                    // $('#title').val(data.title);
                    $('#plan_validity').val(data.plan_validity);
                    $('#description').val(data.description);
                    $('#plan_max_price').val(data.plan_max_price);

                    let max = data.max_price;
                    let min = data.min_price;
                    $('#admin_max_price').val(max) ;
                    $('#admin_min_price').val(min);
                    var total_price = data.price + profit_price;
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
    //     var admin_plan = $('#admin_plan').val();
    //     $.ajax({
    //         url: "{{ url('get-channels-of-plan-reseller') }}",
    //         method: 'post',
    //         data: {
    //             'admin_plan': admin_plan,
    //             "_token": "{{ csrf_token() }}"
    //         },
    //         success: function(result){
    //             $('#channel').show();
    //             $('#channels').html(result)
    //     }});
    // }

    function plan_validity1() {
        profit_price_check();
        var admin_plan = $('#admin_plan').val();
        var plan_validity = $('#plan_validity').val();
        if(admin_plan.length && plan_validity.trim() != ''){
            $.ajax({
              url: "{{ url('get-admin-plan-price') }}",
              method: 'post',
              data: {
                 'admin_plan': admin_plan,
                 'plan_validity': plan_validity,
                 "_token": "{{ csrf_token() }}"
              },
              // data: { "_token": "{{ csrf_token() }}", id : bd_id }
              success: function(result){
                 console.log(result);
                //  var res = JSON.parse(result);
                 if(result.status){
                    $('#price').val(result.price);
                    $('.price-error').text('');
                    // alert('res.msg');
                 }else{
                    $('.price-error').text(result.msg);
                 }
            }});
        }else{
            $('#pric-group-error').show();
            return;
        }

    }    
    $(document).ready(function(){
        $('#pill-vertical-t-0').parent('li').removeClass('done');
        $('#profit_percentage').keyup(function(){
            var profit_percentage = $(this).val();
            var price = $('#price').val();
            var admin_plan = $('#admin_plan').val();
            var plan_validity = $('#plan_validity').val();
            var profit_price = (price * profit_percentage) / 100;

            if(admin_plan.length && plan_validity.trim() != ''){
                $.ajax({
                url: "{{ url('check-max-selling-price-for-reseller') }}",
                method: 'post',
                data: {
                    'admin_plan': admin_plan,
                    'profit_percentage' : profit_percentage,
                    'profit_price': profit_price,
                    'plan_validity': plan_validity,
                    'price': price,
                    "_token": "{{ csrf_token() }}"
                },
                // data: { "_token": "{{ csrf_token() }}", id : bd_id }
                success: function(result){
                    console.log(result);
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

        // $('#profit_price').keyup(function(){
        //     var profit_price = $(this).val();
        //     var price = $('#price').val();
        //     var admin_plan = $('#admin_plan').val();
        //     var plan_validity = $('#plan_validity').val();
        //     var profit_percentage = (profit_price * 1000) / price;

        //     if(admin_plan.length && plan_validity.trim() != ''){
        //         $.ajax({
        //         url: "{{ url('check-max-selling-price-for-admin') }}",
        //         method: 'post',
        //         data: {
        //             'admin_plan': admin_plan,
        //             'profit_percentage' : profit_percentage,
        //             'profit_price': profit_price,
        //             'plan_validity': plan_validity,
        //             'price': price,
        //             "_token": "{{ csrf_token() }}"
        //         },
        //         // data: { "_token": "{{ csrf_token() }}", id : bd_id }
        //         success: function(result){
        //             // console.log(result);
        //             // var res = JSON.parse(result);
        //             if(result.status){
        //                 $('#profit_percentage').val(result.profit_percentage);
        //                 $('.profit_price-error').text(result.msg);
        //                 // alert('result.msg');
        //             }else{
        //                 $('#profit_percentage').val(result.profit_percentage);
        //                 $('.profit_price-error').text('');
        //             }
        //         }});
        //     }else{
        //         $('#pric-group-error').show();
        //         return;
        //     }
        // });

    })

    function profit_price_check(){
        var price = $('#price').val();
        var profit_price = $('#profit_price').val();
        var total_price = Number(price) + Number(profit_price);
        var plan_max_price = $('#plan_max_price').val();
        $('#total_price').val(total_price.toFixed(2));
        // if(total_price > plan_max_price){
        //     $('.total_price-error').html('Total price should not be greater than '+plan_max_price+'.')
        //     return false;
        // }else{
        //     $('.total_price-error').html('')
        // }
    }


</script>

@endsection

@section('footer')

@endsection
