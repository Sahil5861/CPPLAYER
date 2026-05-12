@extends('layout.default')
@section('mytitle', 'Manage Net Admin')
@if(isset($netadmin))
@section('page', 'Net Admin / Update')
@endif
@if(!isset($netadmin))
@section('page', 'Net Admin / Add')
@endif
@section('content')
<style type="text/css">
    .validity-error,.price-error{
        color: #e7515a;
        font-size: 13px;
        letter-spacing: 1px;
        margin-top: 5px;
        margin-left: 2px;
    }

    .mobileError, .otpError, .emailError{
        color: #e7515a;
        font-size: 13px;
        letter-spacing: 1px;
    }
    .fa {
        font-size: 1.2rem;
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
                  @if(session()->has('error'))
                  <div class="alert alert-danger alert-block">
                      <button type="button" class="close" data-dismiss="alert">×</button>
                      <strong>{{ session()->get('error') }}</strong>
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
                    <form id="netadmin-form"  method="post" action="{{route('saveNetAdmin')}}" enctype="multipart/form-data" novalidate class="simple-example" >
                        @csrf
                        <input type="hidden" name="id" id="id" value="@if(isset($netadmin)){{$netadmin->id}}@endif">
                        <div class="form-row">
                            <div class="col-md-6 mb-4">
                                <label for="fullName">Name*</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Name" value="{{old('name')}}@if(isset($netadmin)){{$netadmin->name}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('name') {{ $message }} @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Email*</label>
                                <input type="text" class="form-control" id="email" name="email" placeholder="Email" value="{{old('email')}}@if(isset($netadmin)){{$netadmin->email}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('email') {{ $message }} @enderror
                                </div>
                                <div class="emailError"></div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Password*</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Password" value="{{old('password')}}@if(isset($netadmin)){{$netadmin->real_password}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('email') {{ $message }} @enderror
                                </div>
                                <div class="emailError"></div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Mobile*</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="mobile" name="mobile" placeholder="Mobile" value="{{old('mobile')}}@if(isset($netadmin)){{$netadmin->mobile}}@endif" required>
                                    <div class="input-group-prepend">
                                        <button type="button" class="btn btn-sm btn-dark sendOTP" onclick="sendOTP()">Send OTP</button>
                                    </div>
                                </div>
                                <div class="invalid-feedback">
                                    @error('mobile') {{ $message }} @enderror
                                </div>
                                <div class="mobileError"></div>
                            </div>

                            <div class="col-md-6 mb-4 verifyOTPDiv" style="display: none;">
                                <label for="fullName">Verify OTP*</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="otp" name="otp" placeholder="OTP">
                                    <div class="input-group-prepend">
                                        <button type="button" class="btn btn-sm btn-dark verifyOTP" onclick="verifyOTP()">Verify OTP</button>
                                    </div>
                                </div>
                                <div class="otpError"></div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="fullName">House/Flat No*</label>
                                <input type="text" class="form-control" id="hf_number" name="hf_number" placeholder="House/Flat No" value="{{old('hf_number')}}@if(isset($netadmin)){{$netadmin->hf_number}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('hf_number') {{ $message }} @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Street Number*</label>
                                <input type="text" class="form-control" id="street_number" name="street_number" placeholder="Street Number" value="{{old('street_number')}}@if(isset($netadmin)){{$netadmin->street_number}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('street_number') {{ $message }} @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Landmark</label>
                                <input type="text" class="form-control" id="landmark" name="landmark" placeholder="Landmark" value="{{old('landmark')}}@if(isset($netadmin)){{$netadmin->landmark}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('landmak') {{ $message }} @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Address*</label>
                                <textarea type="text" id="address"  name="address" class="form-control">{{old('address')}}@if(isset($netadmin)){{$netadmin->address}}@endif</textarea>

                                <!-- <input type="text" class="form-control" id="address" name="address" placeholder="Address" value="@if(isset($netadmin)){{$netadmin->address}}@endif" required> -->

                                <div class="invalid-feedback">
                                    @error('address') {{ $message }} @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Country*</label>
                                <input type="text" class="form-control" id="country" name="country" placeholder="Country" value="{{old('country')}}@if(isset($netadmin)){{$netadmin->country}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('country') {{ $message }} @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="fullName">City*</label>
                                <input type="text" class="form-control" id="city" name="city" placeholder="City" value="{{old('city')}}@if(isset($netadmin)){{$netadmin->city}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('city') {{ $message }} @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Pincode*</label>
                                <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Pincode" value="{{old('pincode')}}@if(isset($netadmin)){{$netadmin->pincode}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('pincode') {{ $message }} @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Company Name</label>
                                <input type="text" class="form-control" id="company_name" name="company_name" placeholder="Company Name" value="{{old('company_name')}}@if(isset($netadmin)){{$netadmin->company_name}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('company_name') {{ $message }} @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Add additional wallet amount</label>
                                <input type="text" class="form-control" id="current_amount" name="current_amount" placeholder="Add wallet amount" value="{{old('current_amount')}}">
                                <div class="invalid-feedback">
                                    @error('current_amount') {{ $message }} @enderror
                                </div>
                                @if(isset($netadmin))<p style="color: green;">Current Wallet Amount: {{$netadmin->current_amount}}</p>@endif
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="fullName">Choose Plan</label>
                                <select multiple name="netadmin_plan[]" id="netadmin_plan" class="form-control select">
                                     <option value="">--Choose Plan--</option>
                                    <?php
                                    foreach($packages as $key => $plan){
                                        echo '<option value="'.$plan['id'].'">'.$plan['title'].' / ₹'.round($plan['net_admin_price'],2).'</option>';
                                    }
                                 ?>
                                </select>
                                @if(isset($existing_plan))<p style="color: green;">Current Plan: 
                                    @for($i=0; $i<count($existing_plan); $i++)
                                        {{$existing_plan[$i]->title}},
                                    @endfor
                                </p>@endif
                                <div class="invalid-feedback">
                                    @error('netadmin_plan') {{ $message }} @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Status</label>
                                <select name="status" id="status" class="form-control select">
                                    <option value="1" @if(isset($netadmin) && $netadmin->status == 1){{'selected'}}@endif>Active</option>
                                    <option value="0" @if(isset($netadmin) && $netadmin->status == 0){{'selected'}}@endif>De-Active</option>
                                </select>
                                <div class="invalid-feedback">
                                    @error('status') {{ $message }} @enderror
                                </div>
                                <!-- <div class="valid-feedback">
                                    Looks good!
                                </div> -->
                            </div>
                        </div>
                        @if(isset($netadmin))
                        <button class="btn btn-primary submit-fn mt-2" id="addBtn" type="submit">Update</button>
                        @else
                        <button class="btn btn-primary submit-fn mt-2" id="addBtn" type="submit">Add</button>
                        @endif

                    </form>
                <!-- </div> -->
            </div>
        </div>
    </div>

</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script type="text/javascript">
    function checkWallet(val) {
        // body...
        $.ajax({
          url: "{{ url('check-wallet-admin') }}",
          method: 'post',
          data: {
             'plan_id': val,
             'id': $('#id').val(),
             "_token": "{{ csrf_token() }}"
          },
          // data: { "_token": "{{ csrf_token() }}", id : bd_id }
          success: function(result){
             console.log(result);
             $('#loader-screen').hide();
             var res = JSON.parse(result);
             if(res.status){
                // $('#price').val('');
                // $('.price-error').text(res.msg);
                // alert('res.msg');
                $(".submit-fn").attr('disabled',true);
                $(".price-error").text(res.msg);

             }else{
                $(".price-error").text('');
                $(".submit-fn").attr('disabled',false);
             }
          }
        });
    }

    function sendOTP(){
        var mobile = $('#mobile').val();
        if(mobile == '' || mobile.lenght < 10 || mobile == undefined || mobile == null){
            $('.mobileError').html('Mobile Number is invalid');
            return;
        }
        var otp = Math.floor(100000 + Math.random() * 900000);
        localStorage.setItem('otp',otp);
        $('.sendOTP').html('Resend OTP');
        $('.verifyOTPDiv').show();
    }

    function verifyOTP(){
        var otp = $('#otp').val();
        if(otp == '' || (otp.lenght < 6 && otp.lenght > 6)  || otp == undefined || otp == null){
            $('.otpError').html('OTP is invalid');
            return;
        }

        if(localStorage.getItem('otp') == otp){
            let verify = '<i class="fa fa-check text-success"></i>';
            $('.sendOTP').html(verify);
            $('.sendOTP').attr('disabled',true);
            $('.verifyOTPDiv').hide();
        }
    }

    $(document).ready(function(){
        $('#email').keyup(function(){
            var email = $(this).val();
            $.ajax({
                url: "{{ url('check-netadmin-email') }}",
                method: 'post',
                data: {
                    'email': email,
                    "_token": "{{ csrf_token() }}"
                },
                success: function(result){
                    if(result){
                        $('.emailError').text('');
                        $('#addBtn').removeAttr('disabled', true);
                    }else{
                        $('.emailError').text('This email already exists');
                        $('#addBtn').attr('disabled', true);
                    }
                }
            });
        })
    })

</script>
@endsection

@section('footer')

<!-- footer script if required -->
@endsection
