@extends('layout.default')

@section('mytitle', isset($edit) ? 'Edit App Domain' : 'Add App Domain')
@section('page', isset($edit) ? 'Edit App Domain' : 'Add App Domain')

@section('content')
<div class="layout-px-spacing">

    @if(session()->has('message'))
        <div class="alert alert-success alert-block">
            <button type="button" class="close" data-dismiss="alert">×</button>    
            <strong>{{ session()->get('message') }}</strong>
        </div>
    @endif
    <h4 class="mb-4" style="margin-top: 20px;">App Domain Content Setting</h4>


    <form action="{{ route('admin.domain.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <input type="hidden" name="admin_id" value="{{$id}}">

        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <label for="domain">Domain</label>
                <input type="text" name="domain" class="form-control" placeholder="Enter domain" value="{{ old('domain', $edit->domain ?? '') }}" required>
            </div>

            <div class="col-md-6 mb-3">
            <label for="content">Content</label>
            <select name="content" class="form-control select2" required>
                <option value="India" {{ old('content', $edit->content ?? '') == 'India' ? 'selected' : '' }}>India</option>
                <option value="Worldwide" {{ old('content', $edit->content ?? '') == 'Worldwide' ? 'selected' : '' }}>Worldwide</option>
            </select>
            </div>


            <div class="col-md-6 mb-3">
                <label for="logo">Logo URL</label>
                <input type="text" name="logo" class="form-control" placeholder="https://example.com/logo.png" value="{{ old('logo', $edit->logo ?? '') }}">
            </div>

            <div class="col-md-6 mb-3">
                <label for="app_name">App Name</label>
                <input type="text" name="app_name" class="form-control" placeholder="Enter app name" value="{{ old('app_name', $edit->app_name ?? '') }}">
            </div>

            <div class="col-md-6 mb-3">
                <label for="theme_color">Theme Color</label>
                <input type="color" name="theme_color" class="form-control" placeholder="#ffffff or blue" value="{{ old('theme_color', $edit->theme_color ?? '') }}">
            </div>

            <div class="col-md-6 mb-3">
                <label for="live_channels">Live Channels</label>
                <select name="live_channels[]" class="form-control select" multiple>
                    @foreach($channels as $channel)
                        <option value="{{ $channel->id }}"
                            @if(isset($edit) && in_array($channel->id, $edit->live_channels)) selected @endif>
                            {{ $channel->channel_name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <hr>
        <h5 class="mb-3">Allow Sections</h5>
        <div class="row">
            @php
                $toggles = [
                    'movies' => 'Movies',
                    'webseries' => 'Web Series',
                    'tvshow' => 'TV Shows (India)',
                    'tvshow_pak' => 'TV Shows (Pakistan)',
                    'kids_show' => 'Kids Show',
                    'religious' => 'Religious',
                    'sports' => 'Sports',
                    'stage_shows' => 'Stage Shows',
                    'laughter_shows' => 'Laughter Shows',
                    'content_network' => 'Content Network',
                    'search' => 'Search'
                ];
            @endphp

            @foreach($toggles as $key => $label)
                <div class="col-md-3 mb-3">
                    <input type="hidden" name="{{ $key }}" value="0">
                    <label class="new-control new-checkbox checkbox-primary">
                        <input type="checkbox" class="new-control-input"
                            name="{{ $key }}" value="1"
                            @if(old($key, $edit->$key ?? true)) checked @endif>
                        <span class="new-control-indicator" style="user-select: none;"></span> {{ $label }}
                    </label>
                </div>
            @endforeach
        </div>


        <div class="mt-4">
            <button type="submit" class="btn btn-primary">{{ isset($edit) ? 'Update' : 'Submit' }}</button>
        </div>
    </form>
</div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>
@endsection
