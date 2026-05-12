@extends('layout.default')
@section('mytitle', 'Manage TvShow')
@if(isset($season))
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

                <form id="season-form" method="post" action="{{ route('saveTvShowSesonpak') }}" enctype="multipart/form-data" novalidate>
                    @csrf
                    @if (isset($season))                        
                        <input type="hidden" name="id" value="{{isset($season) ? $season->id : ''}}">
                    @endif

                    <input type="hidden" name="show_id" id="tv_channel_id" value="{{$id}}">

                    <div class="form-row">
                        <!-- Name -->
                        <div class="col-md-6 mb-4">
                            <label for="name">Title*</label>
                            <input type="text" class="form-control" id="title" name="title"
                                placeholder="Name"
                                value="{{ old('title', $season->title ?? '') }}" required>
                            <div class="invalid-feedback">
                                @error('name') {{ $message }} @enderror
                            </div>
                        </div>


                        {{-- <!-- Language -->
                        <div class="col-md-6 mb-4">
                            <label for="language">Language</label>
                            <input type="text" class="form-control" id="language" name="language"
                                placeholder="e.g. Hindi, English"
                                value="{{ old('language', $season->language ?? '') }}" required>
                            <div class="invalid-feedback">
                                @error('language') {{ $message }} @enderror
                            </div>
                        </div> --}}

                        <!-- Logo -->
                        <div class="col-md-6 mb-4">
                            <label for="thumbnail">Poster</label>
                            <input type="text" class="form-control" id="thumbnail" name="thumbnail"
                                placeholder="Enter image path or URL"
                                value="{{ old('logo', $season->poster ?? '') }}">
                            @if(isset($season) && $season->poster)
                                <img src="/{{ $season->poster }}" width="40" style="margin-top: 5px;" alt="Logo Preview">
                            @endif
                            <div class="invalid-feedback">
                                @error('thumbnail') {{ $message }} @enderror
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="name">Release Year</label>
                            <input type="text" class="form-control" id="release_year" name="release_year"
                                placeholder="Name"
                                value="{{ old('release_year', $season->release_year ?? '') }}" required>
                            <div class="invalid-feedback">
                                @error('release_year') {{ $message }} @enderror
                            </div>
                        </div>

                        
                        <!-- Status -->
                        <div class="col-md-6 mb-4">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="1" @if(old('status', $season->status ?? '') == 1) selected @endif>Active</option>
                                <option value="0" @if(old('status', $season->status ?? '') == 0) selected @endif>Inactive</option>
                            </select>
                            <div class="invalid-feedback">
                                @error('status') {{ $message }} @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="col-md-12 mb-4">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description"
                                placeholder="Description">{{ old('description', $season->description ?? '') }}</textarea>
                            <div class="invalid-feedback">
                                @error('description') {{ $message }} @enderror
                            </div>
                        </div>
                        
                    </div>

                    <button class="btn btn-primary submit-fn mt-4" type="submit">
                        {{ isset($season) ? 'Update' : 'Add' }}
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
