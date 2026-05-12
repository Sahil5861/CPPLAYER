@extends('layout.default')
@section('mytitle', 'Manage User')
@if(isset($user))
@section('page', 'User / Update')
@endif
@if(!isset($user))
@section('page', 'User / Add')
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
                    <form id="user-form"  method="post" action="{{route('saveUser')}}" enctype="multipart/form-data" novalidate class="simple-example" >
                        @csrf
                        <input type="hidden" name="id" id="id" value="@if(isset($user)){{$user->id}}@endif">
                        <div class="form-row">
                            <div class="col-md-12">
                                <h4>Basic Info</h4>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="fullName">Name*</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Name" value="{{old('name')}}@if(isset($user)){{$user->name}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('name') {{ $message }} @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="{{old('email')}}@if(isset($user)){{$user->email}}@endif">
                                <div class="invalid-feedback">
                                    @error('email') {{ $message }} @enderror
                                </div>
                                <div class="emailError"></div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Mobile*</label>
                                <input type="tel" class="form-control" id="mobile" name="mobile" placeholder="Mobile" value="{{old('mobile')}}@if(isset($user)){{$user->mobile}}@endif" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>                                                                    
                                <div class="invalid-feedback">
                                    @error('mobile') {{ $message }} @enderror
                                </div>
                                <div class="mobileError"></div>
                            </div>

                            {{-- <div class="col-md-6 mb-4 verifyOTPDiv" style="display: none;">
                                <label for="fullName">Verify OTP*</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="otp" name="otp" placeholder="OTP">
                                    <div class="input-group-prepend">
                                        <button type="button" class="btn btn-sm btn-dark verifyOTP" onclick="verifyOTP()">Verify OTP</button>
                                    </div>
                                </div>
                                <div class="otpError"></div>
                            </div> --}}
                            <div class="col-md-12">
                                <h4>Address Info</h4>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="fullName">House/Flat No</label>
                                <input type="text" class="form-control" id="hf_number" name="hf_number" placeholder="House/Flat No" value="{{old('hf_number')}}@if(isset($user)){{$user->hf_number}}@endif">
                                <div class="invalid-feedback">
                                    @error('hf_number') {{ $message }} @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Street Number</label>
                                <input type="text" class="form-control" id="street_number" name="street_number" placeholder="Street Number" value="{{old('street_number')}}@if(isset($user)){{$user->street_number}}@endif">
                                <div class="invalid-feedback">
                                    @error('street_number') {{ $message }} @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Landmark</label>
                                <input type="text" class="form-control" id="landmark" name="landmark" placeholder="Landmark" value="{{old('landmark')}}@if(isset($user)){{$user->landmark}}@endif">
                                <div class="invalid-feedback">
                                    @error('landmak') {{ $message }} @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Country</label>
                                <input type="text" class="form-control" id="country" name="country" placeholder="Country" value="{{old('country')}}@if(isset($user)){{$user->country}}@endif">
                                <div class="invalid-feedback">
                                    @error('country') {{ $message }} @enderror
                                </div>
                            </div>

                            <div class="col-md-12 mb-4">
                                <label for="fullName">Address</label>
                                <textarea type="text" id="address"  name="address" class="form-control">{{old('address')}}@if(isset($user)){{$user->address}}@endif</textarea>

                                <!-- <input type="text" class="form-control" id="address" name="address" placeholder="Address" value="@if(isset($user)){{$user->address}}@endif" required> -->

                                <div class="invalid-feedback">
                                    @error('address') {{ $message }} @enderror
                                </div>
                            </div>

                            

                            <div class="col-md-6 mb-4">
                                <label for="fullName">City</label>
                                <input type="text" class="form-control" id="city" name="city" placeholder="City" value="{{old('city')}}@if(isset($user)){{$user->city}}@endif">
                                <div class="invalid-feedback">
                                    @error('city') {{ $message }} @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Pincode</label>
                                <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Pincode" value="{{old('pincode')}}@if(isset($user)){{$user->pincode}}@endif">
                                <div class="invalid-feedback">
                                    @error('pincode') {{ $message }} @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Add additional wallet amount</label>
                                <input type="text" class="form-control" id="wallet_amount" name="wallet_amount" placeholder="Add wallet amount" value="{{old('wallet_amount')}}@if(isset($user)){{$user->wallet_amount}}@endif">
                                <div class="invalid-feedback">
                                    @error('wallet_amount') {{ $message }} @enderror
                                </div>
                                @if(isset($user))<p style="color: green;">Current Wallet Amount: {{$user->current_amount}}</p>@endif
                            </div>
                            @if(isset($user))
                            <div class="col-md-6 mb-4">
                                <label for="fullName">Choose Plan</label>
                                <select onchange="checkWallet(this.value)" name="user_plan" id="user_plan" class="form-control select">
                                     <option value="">--Choose Plan--</option>
                                    <?php
                                    foreach($packages as $key => $plan){
                                        if(isset($user) && $user->user_plan_id == $plan['id']){
                                            echo '<option value="'.$plan['id'].'" selected>'.$plan['title'].' / ₹'.round($plan['total_price'],2).'</option>';
                                        }else{
                                            echo '<option value="'.$plan['id'].'">'.$plan['title'].' / ₹'.round($plan['total_price'],2).'</option>';
                                        }

                                    }
                                 ?>
                                </select>
                                <div class="invalid-feedback">
                                    @error('user_plan') {{ $message }} @enderror
                                </div>
                                <div class="price-error"></div>
                                @if(isset($user) && $userPlan)<p style="color: green;">Current Plan: 
                                    @for($i=0; $i<count($userPlan); $i++)
                                        {{$userPlan[$i] ? $userPlan[$i]['title'] : ''}}
                                    @endfor
                                </p>@endif
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Login Pin</label>
                                <input type="text" class="form-control" id="login_pin" name="login_pin" placeholder="Login Pin TV" value="{{old('login_pin')}}@if(isset($user)){{$user->login_pin}}@endif" disabled>
                                <div class="n-chk mt-1">
                                    <label class="new-control new-checkbox checkbox-primary">
                                      <input type="checkbox" name="updatePin" class="new-control-input">
                                      <span class="new-control-indicator"></span>Update Pin
                                    </label>
                                </div>
                                <div class="invalid-feedback">
                                    @error('login_pin') {{ $message }} @enderror
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="fullName">Login Pin (Mobile App)</label>
                                <input type="text" class="form-control" id="login_pin_app" name="login_pin_app" placeholder="Login Pin App" value="{{old('login_pin_app')}}@if(isset($user)){{$user->login_pin_app}}@endif" disabled>
                                <div class="n-chk mt-1">
                                    <label class="new-control new-checkbox checkbox-primary">
                                      <input type="checkbox" name="updateAppPin" class="new-control-input">
                                      <span class="new-control-indicator"></span>Update Pin
                                    </label>
                                </div>
                                
                                <div class="invalid-feedback">
                                    @error('login_pin_app') {{ $message }} @enderror
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="fullName">Over 18 Pin</label>
                                <input type="text" class="form-control" id="over18_pin" name="over18_pin" placeholder="Above 18 App" value="{{old('over18_pin')}}@if(isset($user)){{$user->over18_pin}}@endif" disabled>
                                <div class="n-chk mt-1">
                                    <label class="new-control new-checkbox checkbox-primary">
                                      <input type="checkbox" name="updateOver18Pin" class="new-control-input">
                                      <span class="new-control-indicator"></span>Update Pin
                                    </label>
                                </div>
                                
                                <div class="invalid-feedback">
                                    @error('over18_pin') {{ $message }} @enderror
                                </div>
                            </div>
                            @endif

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Status</label>
                                <select name="status" id="status" class="form-control select">
                                    <option value="1" @if(isset($user) && $user->status == 1){{'selected'}}@endif>Active</option>
                                    <option value="0" @if(isset($user) && $user->status == 0){{'selected'}}@endif>De-Active</option>
                                </select>
                                <div class="invalid-feedback">
                                    @error('status') {{ $message }} @enderror
                                </div>
                                <!-- <div class="valid-feedback">
                                    Looks good!
                                </div> -->
                            </div>
                        </div>
                        @if(isset($user))
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
        console.log(val);
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
                url: "{{ url('check-user-email') }}",
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
