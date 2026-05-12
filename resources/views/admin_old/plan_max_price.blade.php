@extends('layout.default')
@section('mytitle', 'Plan Max Price')
@section('page', 'Plan Max Price')
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
                    <form id="user-form"  method="post" action="{{route('updatePlanMaxPrice')}}" enctype="multipart/form-data" novalidate class="simple-example" >
                        @csrf
                        <div class="form-row">
                            <div class="col-md-6 mb-4">
                                <label for="fullName">Amount</label>
                                <input type="text" class="form-control" id="title" name="amount" placeholder="Amount" value="{{old('amount')}}@if(isset($plan_max_price)){{$plan_max_price->amount}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('amount') {{ $message }} @enderror
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary submit-fn mt-2" type="submit">Update</button>
                    </form>
            </div>
        </div>
    </div>

</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

@endsection

@section('footer')

<!-- footer script if required -->
@endsection
