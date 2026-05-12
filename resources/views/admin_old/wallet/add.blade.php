@extends('layout.default')
@section('mytitle', 'Manage Wallet')
@if(isset($wallet))
@section('page', 'Wallet  / Update')
@endif
@if(!isset($wallet))
@section('page', 'Wallet  / Add')
@endif
@section('content')
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
                    <form id="user-form"  method="post" action="{{route('saveSadminWallet')}}" enctype="multipart/form-data" novalidate class="simple-example" >
                        @csrf
                        <input type="hidden" name="id" value="@if(isset($wallet)){{$wallet->id}}@endif">

                        <div class="form-row">

                            <!-- <div class="col-md-6 mb-4">
                                <label for="fullName">Currency</label>
                                <input type="text" class="form-control" id="currency" name="currency" placeholder="Currency" value="@if(isset($wallet)){{$wallet->currency}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('currency') {{ $message }} @enderror
                                </div>
                            </div> -->

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Amount*</label>
                                <input type="text" class="form-control" id="amount" name="amount" placeholder="Amount" value="{{old('amount')}}@if(isset($wallet)){{$wallet->amount}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('amount') {{ $message }} @enderror
                                </div>
                            </div>


                            <div class="col-md-6 mb-4">
                                <label for="fullName">Amount Recieve Method (Eg: Cash/WU)*</label>
                                <input type="text" class="form-control" id="amount_method" name="amount_method" placeholder="Amount recieve method" value="{{old('amount_method')}}@if(isset($wallet)){{$wallet->amount_method}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('amount_method') {{ $message }} @enderror
                                </div>
                            </div>


                            <div class="col-md-6 mb-4">
                                <label for="fullName">Select Admin</label>
                                <select name="user_id" id="channels" class="form-control select">
                                     <!-- <option value="">--Select Channels--</option> -->
                                    <?php
                                    foreach($users as $user){
                                        if(isset($user_ids) && in_array($user->id,$user_ids)){
                                            echo '<option value="'.$user->id.'" selected>'.$user->name.'</option>';
                                        }else{
                                            echo '<option value="'.$user->id.'">'.$user->name.'</option>';
                                        }

                                    }
                                 ?>
                                </select>
                                <div class="invalid-feedback">
                                    @error('user') {{ $message }} @enderror
                                </div>
                                <!-- <div class="valid-feedback">
                                    Looks good!
                                </div> -->
                            </div>



                            <div class="col-md-6 mb-4">
                                <label for="message">Message</label>
                                <textarea type="text" class="form-control" id="message" name="message" placeholder="Message">{{old('message')}}@if(isset($wallet)){{$wallet->message}}@endif</textarea>
                                <div class="invalid-feedback">
                                    @error('message') {{ $message }} @enderror
                                </div>
                            </div>


                        </div>
                        @if(isset($wallet))
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


@endsection

@section('footer')

<!-- footer script if required -->
@endsection
