@extends('layout.default')
@section('mytitle', 'Manage TvShow')
@if(isset($tvchannel))
@section('page', 'TvShow / Update')
@else
@section('page', 'TvShow / Add')
@endif

@section('content')
<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
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

                <form id="tvchannel-form" method="post" action="{{ route('savesportscategory') }}" enctype="multipart/form-data" novalidate>
                    @csrf
                    <input type="hidden" name="id" value="{{ $sportcategory->id ?? '' }}">

                    <div class="form-row">
                        <!-- Name -->
                        <div class="col-md-6 mb-4">
                            <label for="name">Title</label>
                            <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $sportcategory->title ?? '') }}" required placeholder="Enter Sport Category">
                            <div class="invalid-feedback">
                                @error('name') {{ $message }} @enderror
                            </div>
                        </div>

                        <!-- Logo -->
                        {{-- <div class="col-md-6 mb-4">
                            <label for="logo">Logo</label>
                            <input type="text" class="form-control" id="logo" name="logo"
                                placeholder="Enter image path or URL"
                                value="{{ old('logo', $tvchannel->logo ?? '') }}">
                            @if(isset($tvchannel) && $tvchannel->logo)
                                <img src="/{{ $tvchannel->logo }}" width="40" style="margin-top: 5px;" alt="Logo Preview">
                            @endif
                            <div class="invalid-feedback">
                                @error('logo') {{ $message }} @enderror
                            </div>
                        </div> --}}

                        <!-- Status -->
                        <div class="col-md-6 mb-4">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="1" @if(old('status', $sportcategory->status ?? '') == 1) selected @endif>Active</option>
                                <option value="0" @if(old('status', $sportcategory->status ?? '') == 0) selected @endif>Inactive</option>
                            </select>
                            <div class="invalid-feedback">
                                @error('status') {{ $message }} @enderror
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-primary submit-fn mt-4" type="submit">
                        {{ isset($tvchannel) ? 'Update' : 'Add' }}
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@section('footer')
<!-- Custom JS if needed -->
@endsection
