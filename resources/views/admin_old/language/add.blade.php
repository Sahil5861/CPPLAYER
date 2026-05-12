@extends('layout.default')
@section('mytitle', 'Manage Language')
@if(isset($language))
@section('page', 'Language / Update')
@endif
@if(!isset($language))
@section('page', 'Language / Add')
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
                    <form id="user-form"  method="post" action="{{route('saveLanguage')}}" enctype="multipart/form-data" novalidate class="simple-example" >
                        @csrf
                        <input type="hidden" name="id" value="@if(isset($language)){{$language->id}}@endif">
                        <div class="form-row">
                            <div class="col-md-6 mb-4">
                                <label for="fullName">Title*</label>
                                <input type="text" class="form-control" id="title" name="title" placeholder="Title" value="{{old('title')}}@if(isset($language)){{$language->title}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('title') {{ $message }} @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Logo</label>
                                <input type="text" class="form-control" id="logo" name="logo" placeholder="Logo" value="{{old('logo')}}@if(isset($language)){{$language->logo}}@endif">

                                @if (isset($language))
                                    <img src="{{$language->logo}}" alt="image" width="100px" style="margin-top: 10px;">
                                @endif
                                <div class="invalid-feedback">
                                    @error('title') {{ $message }} @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="1" @if(isset($language) && $language->status == 1){{'selected'}}@endif>Active</option>
                                    <option value="0" @if(isset($language) && $language->status == 0){{'selected'}}@endif>De-Active</option>
                                </select>
                                <div class="invalid-feedback">
                                    @error('status') {{ $message }} @enderror
                                </div>
                                <!-- <div class="valid-feedback">
                                    Looks good!
                                </div> -->
                            </div>
                        </div>
                        @if(isset($language))
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
