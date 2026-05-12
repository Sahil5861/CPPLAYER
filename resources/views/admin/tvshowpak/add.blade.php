@extends('layout.default')
@section('mytitle', 'Manage TvShow')
@if(isset($tvshow))
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
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form id="tvshow-form" method="post" action="{{ route('saveTvShowpak') }}" enctype="multipart/form-data">
                    @csrf
                    @if (isset($tvshow))                        
                        <input type="hidden" name="id" value="{{isset($tvshow) ? $tvshow->id : ''}}">
                    @endif

                    <input type="hidden" name="tv_channel_id" id="tv_channel_id" value="{{$id}}">

                    <div class="form-row">
                        <!-- Name -->
                        <div class="col-md-6 mb-4">
                            <label for="name">Name*</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Name" value="{{ old('name', $tvshow->name ?? '') }}" required>
                            <div class="invalid-feedback">
                                @error('channel_name') {{ $message }} @enderror
                            </div>                            
                        </div>

                        {{-- Sets order: uses existing value for edit, or calculates next order based on channel for add --}}
                        @php
                            $order = isset($tvshow)
                                ? $tvshow->order
                                : (\App\Models\TvShowPak::where('tv_channel_id', $id)
                                    ->whereNull('deleted_at')
                                    ->max('order') ?? 0) + 1;                                
                        @endphp

                        <input type="hidden" name="order" value="{{ old('order', $order) }}">

                        <!-- Logo -->
                        <div class="col-md-6 mb-4">
                            <label for="thumbnail">Thumbnail</label>
                            <input type="text" class="form-control" id="thumbnail" name="thumbnail" placeholder="Enter image path or URL" value="{{ old('logo', $tvshow->thumbnail ?? '') }}">
                            @if(isset($tvshow) && $tvshow->thumbnail)
                                <img src="{{ $tvshow->thumbnail }}" width="70" style="margin-top: 5px;" alt="Logo Preview">
                            @endif
                            <div class="invalid-feedback">
                                @error('thumbnail') {{ $message }} @enderror
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="genre">Genre*</label>                            
                            <select name="genre[]" id="genre" class="form-control select" multiple required>
                                @foreach ($genres as $genre)
                                    <option value="{{$genre->title}}"
                                        @if (isset($tvshow) && in_array($genre->title, $currentGenres)) selected @endif>
                                        {{$genre->title}}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">
                                @error('genre') {{ $message }} @enderror
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <?php 
                                $countries = \DB::table('countries')->whereNull('deleted_at')->get();                                                                                                   
                            ?>

                            <label for="fullName">Hidden Countries</label>
                            <select name="hidden_countries[]" id="hidden_countries" multiple class="form-control select">                                     
                                <?php
                                foreach($countries as $country){
                                    if(isset($movieContries) && in_array($country->country_code,$movieContries)){
                                        echo '<option value="'.$country->country_code.'" selected>'.$country->country_name.'</option>';
                                    }else{
                                        echo '<option value="'.$country->country_code.'">'.$country->country_name.'</option>';
                                    }
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">
                                @error('hidden_countries') {{ $message }} @enderror
                            </div>                                
                        </div>

                        <!-- Status -->
                        <div class="col-md-6 mb-4">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="1" @if(old('status', $tvshow->status ?? '') == 1) selected @endif>Active</option>
                                <option value="0" @if(old('status', $tvshow->status ?? '') == 0) selected @endif>Inactive</option>
                            </select>
                            <div class="invalid-feedback">
                                @error('status') {{ $message }} @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="col-md-12 mb-4">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description"
                                placeholder="Description">{{ old('description', $tvshow->description ?? '') }}</textarea>
                            <div class="invalid-feedback">
                                @error('description') {{ $message }} @enderror
                            </div>
                        </div>
                        
                    </div>

                    <button class="btn btn-primary submit-fn mt-4" type="submit">
                        {{ isset($tvshow) ? 'Update' : 'Add' }}
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
