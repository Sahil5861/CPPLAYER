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
                    {{-- @include('layout.ad_plan_form', ['actionRoute' => route('admin.admin.ads.save')]) --}}

                    <form id="user-form"  method="post" action="{{route('admin.admin.adsplan.save')}}" enctype="multipart/form-data" novalidate class="simple-example" >
                        @csrf
                        <input type="hidden" name="id" value="@if(isset($ad_plan)){{$ad_plan->id}}@endif">
                        

                        <div class="form-row">
                            <div class="col-md-6 mb-4">
                            <label for="fullName">Choose Super Admin Ads*</label>                                
                            <select class="form-control select" id="super_admin_ad" name="super_admin_ad">
                                <option value="">--Select--</option>
                                @foreach ($super_admin_ads as $item)
                                    <option value="{{$item->id}}"
                                        @if (isset($ad_plan) && ($ad_plan->super_admin_ad_id == $item->id))
                                            selected
                                        @endif
                                        >{{$item->title}}</option>
                                @endforeach
                            </select>
                        </div>
                            <div class="col-md-6 mb-4">
                                <label for="title">Title*</label>
                                <input type="text" class="form-control" id="title" name="title" placeholder="Title" value="{{old('title')}}@if(isset($ad_plan)){{$ad_plan->title}}@endif" required readonly>
                                <div class="invalid-feedback">
                                    @error('title') {{ $message }} @enderror
                                </div>
                            </div>
                            

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Schedule Time</label>                                
                                <input type="text" name="schdule_time" id="schedule_time" class="form-control" readonly value="{{old('schedule_time')}} @if(isset($ad_plan)) {{$ad_plan->schedule}} @endif">
                            </div>                            
                            
                            <div class="col-md-6 mb-4">
                                <label for="fullName">Validity <small>(in days)</small></label>
                                <input type="text" class="form-control" id="validity" name="validity" value="{{old('plan_validity')}}@if(isset($ad_plan)){{$ad_plan->validity}}@endif" oninput="this.value = this.value.replace(/[^0-9]/g,'')" readonly>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Time Slot</label>
                                <input type="text" name="time_slot" id="time_slot" class="form-control" readonly value="{{old('time_slot')}} @if(isset($ad_plan)) {{$ad_plan->time_slot}} @endif">
                            </div>
                        
                            <div class="col-md-6 mb-4">
                                <label for="fullName">Price*</label>
                                <input type="text" class="form-control" onkeyup="check_price()" id="price" name="price" placeholder="Price" value="{{old('price')}}@if(isset($ad_plan)){{$ad_plan->price}}@endif" required readonly>
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

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Profit Price*</label>
                                <input type="text" class="form-control" oninput="this.value = this.value.replace(/[^0-9]/g, ''); total_price.value = (parseFloat(this.value) || 0) + parseFloat(price.value)" id="profit_price" name="profit_price" placeholder="Propfit Price" value="{{old('profit_price')}}@if(isset($ad_plan)){{$ad_plan->profit_price}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('price') {{ $message }} @enderror
                                </div>
                                <div class="price-error"></div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Total Price*</label>
                                <input type="text" class="form-control" onkeyup="check_price()" id="total_price" name="total_price" placeholder="Total Price" value="{{old('profit_price')}}@if(isset($ad_plan)){{$ad_plan->total_price}}@endif" required readonly>
                                <div class="invalid-feedback">
                                    @error('price') {{ $message }} @enderror
                                </div>
                                <div class="price-error"></div>
                            </div>

                            
                            
                            <div class="col-md-6 mb-4">
                                <label for="fullName">Status</label>
                                <select name="status" id="status" class="form-control select">
                                    <option value="1" @if(isset($ad_plan) && $ad_plan->status == 1){{'selected'}}@endif>Active</option>
                                    <option value="0" @if(isset($ad_plan) && $ad_plan->status == 0){{'selected'}}@endif>De-Active</option>
                                </select>
                                <div class="invalid-feedback">
                                    @error('status') {{ $message }} @enderror
                                </div>
                            </div>
                        </div>                        
                        @if(isset($ad_plan))
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
@endsection

@section('footer')

<script>

    $('#super_admin_ad').on('change',  function (){
        let id = $(this).val();
        $.ajax({
            type: 'GET',
            url: "{{ url('get-ad-details') }}",
            data: {id: id},
            success: function (response) {
                let data = response.data;
                $('#title').val(data.title);            
                $('#price').val(data.price);            
                $('#schedule_time').val(data.schedule);            
                $('#validity').val(data.validity);
                $('#time_slot').val(data.time_slot);
            }
        })
    })

    function super_admin_plan1(){
        // plan_validity1();
        var super_admin_plan = $('#super_admin_plan').val();
        var profit_price = $('#profit_price').val();
        if(super_admin_plan.length){
            $.ajax({
              url: "{{ url('get-ad-details') }}",
              method: 'post',
              data: {
                 'super_admin_plan': super_admin_plan,
                 "_token": "{{ csrf_token() }}"
              },
              success: function(result){
                 if(result.status){
                    $('#price').val(result.price);
                    $('#title').val(result.title);
                    $('#plan_validity').val(result.validity);
                    $('#description').val(result.description);
                    // $('#plan_max_price').val(result.plan_max_price);
                    var total_price = parseInt(result.price) + (parseInt(profit_price) || 0);
                    $('#total_price').val(total_price.toFixed(2));
                    $('.price-error').text('');
                 }else{
                    $('.price-error').text(result.msg);
                 }
            }});
        }else{
            $('#price').val('');
            $('#title').val('');
            $('#plan_validity').val('');
            $('#description').val('');
        }
    }
</script>

<!-- footer script if required -->
@endsection
