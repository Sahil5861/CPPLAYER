@extends('layout.default')
@section('mytitle', 'URL Settings')
@if(isset($tvshow))
@section('page', 'URL Settings / Update')
@else
@section('page', 'URL Settings / Add')
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
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form  method="post" action="{{ route('url-settings-save') }}" enctype="multipart/form-data">
                    @csrf
                    @if (isset($url_setting))                        
                        <input type="hidden" name="id" value="{{isset($url_setting) ? $url_setting->id : ''}}">
                    @endif
                    

                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="content_network">Content Network*</label>    
                                <select name="content_network_id" id="content_network_id" class="form-control select" required>
                                    <option value="">--select--</option>
                                    @foreach ($content_networks as $item)
                                        <option value="{{ $item->id }}" 
                                            {{ ($url_setting->content_network_id ?? '') == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>    
                        </div>                        

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="content_network">Category*</label>    
                                <select name="content_type" id="content_type" class="form-control select" required>
                                    <option value="">--select--</option>
                                    <option value="1" {{ ($url_setting->content_type ?? '') == '1' ? 'selected' : '' }}>Movies</option>
                                    <option value="2" {{ ($url_setting->content_type ?? '') == '2' ? 'selected' : '' }}>WebSeries</option>
                                    <option value="4" {{ ($url_setting->content_type ?? '') == '4' ? 'selected' : '' }}>Tv Shows</option>
                                    <option value="5" {{ ($url_setting->content_type ?? '') == '5' ? 'selected' : '' }}>Tv Shows Pak</option>
                                    <option value="6" {{ ($url_setting->content_type ?? '') == '6' ? 'selected' : '' }}>Kids Shows</option>
                                    <option value="7" {{ ($url_setting->content_type ?? '') == '7' ? 'selected' : '' }}>Religious</option>
                                    <option value="8" {{ ($url_setting->content_type ?? '') == '8' ? 'selected' : '' }}>Sports</option>
                                    <option value="9" {{ ($url_setting->content_type ?? '') == '9' ? 'selected' : '' }}>Stage Shows Pak</option>
                                    <option value="10" {{ ($url_setting->content_type ?? '') == '10' ? 'selected' : '' }}>Laughter</option>
                                </select>
                            </div>    
                        </div>  
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="old_url">Old Url</label>
                                <input type="text" name="old_url" id="old_url" class="form-control" value="{{ isset($url_setting) ? $url_setting->old_url : '' }}"  placeholder="Old Url" required>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="old_url">New Url</label>
                                <input type="text" name="new_url" id="new_url" class="form-control" value="{{ isset($url_setting) ? $url_setting->new_url : '' }}" placeholder="New Url" required>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-primary submit-fn mt-4" type="submit">
                        Update
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
