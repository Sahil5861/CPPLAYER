@extends('layout.default')
@section('mytitle', 'Manage TvShow')
@if(isset($channel))
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

                <form id="channel-form" method="post" action="{{ route('admin.RelChannel.save') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $channel->id ?? '' }}">

                    <div class="form-row">
                        <!-- Name -->
                        <div class="col-md-6 mb-4">
                            <label for="label">Name*</label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="Name"
                                value="{{ old('name', $channel->name ?? '') }}" required>
                            <div class="invalid-feedback">
                                @error('label') {{ $message }} @enderror
                            </div>
                        </div>

                        @php
                            $maxOrder = \App\Models\RelChannel::whereNull('deleted_at')->max('rel_order') ?? 0;
                            $nextOrder = $maxOrder + 1;
                        @endphp

                        <input type="hidden" name="rel_order" value="{{ old('rel_order', isset($rel_channels) ? $rel_channels->rel_order : $nextOrder) }}">
                        @error('rel_order') <div class="invalid-feedback">{{ $message }}</div> @enderror

                        <!-- Language -->
                        <div class="col-md-6 mb-4">
                            <label for="language">Language</label>
                            <select name="language" id="language" class="form-control select">
                                <option value="">--Select Language--</option>
                                @foreach ($languages as $language)
                                    <option value="{{$language->title}}" @if(isset($channel) && $channel->language == $language->title) selected @endif>{{$language->title}}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">
                                @error('language') {{ $message }} @enderror
                            </div>
                        </div>

                        <!-- Logo -->
                        <div class="col-md-6 mb-4">
                            <label for="logo">Logo</label>
                            <input type="text" class="form-control" id="logo" name="logo"
                                placeholder="Enter image path or URL"
                                value="{{ old('logo', $channel->logo ?? '') }}">
                            @if(isset($channel) && $channel->logo)
                                <img src="{{ $channel->logo }}" width="40" style="margin-top: 5px;" alt="Logo Preview">
                            @endif
                            <div class="invalid-feedback">
                                @error('logo') {{ $message }} @enderror
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="genre">Content Networks</label>                            
                            <select name="content_networks[]" id="content_networks" class="form-control select" multiple>
                                @foreach ($content_networks as $content_network)
                                    <option value="{{$content_network->id}}"
                                        @if (isset($channel) && in_array($content_network->id, $current_networks)) selected @endif>
                                        {{$content_network->name}}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">
                                @error('genre') {{ $message }} @enderror
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6 mb-4">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="1" @if(old('status', $channel->status ?? '') == 1) selected @endif>Active</option>
                                <option value="0" @if(old('status', $channel->status ?? '') == 0) selected @endif>Inactive</option>
                            </select>
                            <div class="invalid-feedback">
                                @error('status') {{ $message }} @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="col-md-12 mb-4">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description"
                                placeholder="Description">{{ old('description', $channel->description ?? '') }}</textarea>
                            <div class="invalid-feedback">
                                @error('description') {{ $message }} @enderror
                            </div>
                        </div>


                    </div>

                    <button class="btn btn-primary submit-fn mt-4" type="submit">
                        {{ isset($channel) ? 'Update' : 'Add' }}
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
