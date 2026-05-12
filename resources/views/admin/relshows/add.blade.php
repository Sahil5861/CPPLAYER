@extends('layout.default')
@section('mytitle', 'Manage TvShow')
@if(isset($relshow))
@section('page', 'TvShow / Update')
@else
@section('page', 'TvShow / Add')
@endif

relshows
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

                @if (isset($relshow))
                    <a href="{{route('admin.Relshows',base64_encode($id))}}" class="btn btn-primary mb-3">Back To List</a>
                @endif

                <form id="relshow-form" method="post" action="{{ route('admin.Relshows.save') }}" enctype="multipart/form-data">
                    @csrf
                    @if (isset($relshow))                        
                        <input type="hidden" name="id" value="{{isset($relshow) ? $relshow->id : ''}}">
                    @endif

                    <input type="hidden" name="channel_id" id="channel_id" value="{{$id}}">

                    <div class="form-row">
                        {{-- Sets order: uses existinglue for edit, or calculates next order based on channel for add --}}
                        @php
                            $rel_order = isset($shows)
                                ? $shows->rel_order
                                : (\App\Models\RelShow::where('channel_id', $id)
                                    ->whereNull('deleted_at')
                                    ->max('rel_order') ?? 0) + 1;
                        @endphp

                        <input type="hidden" name="rel_order" value="{{ old('rel_order', $rel_order) }}">

                        <!-- Name -->
                        <div class="col-md-6 mb-4">
                            <label for="name">Title*</label>
                            <input type="text" class="form-control" id="title" name="title"
                                placeholder="Name"
                                value="{{ old('name', $relshow->title ?? '') }}" required>
                            <div class="invalid-feedback">
                                @error('name') {{ $message }} @enderror
                            </div>
                        </div>


                        {{-- <!-- Language -->
                        <div class="col-md-6 mb-4">
                            <label for="language">Language</label>
                            <input type="text" class="form-control" id="language" name="language"
                                placeholder="e.g. Hindi, English"
                                value="{{ old('language', $relshow->language ?? '') }}" required>
                            <div class="invalid-feedback">
                                @error('language') {{ $message }} @enderror
                            </div>
                        </div> --}}

                        <!-- Logo -->
                        <div class="col-md-6 mb-4">
                            <label for="thumbnail">Thumbnail</label>
                            <input type="text" class="form-control" id="thumbnail" name="thumbnail"
                                placeholder="Enter image path or URL"
                                value="{{ old('logo', $relshow->thumbnail ?? '') }}">
                            @if(isset($relshow) && $relshow->thumbnail)
                                <img src="{{ $relshow->thumbnail }}" width="70" style="margin-top: 5px;" alt="Logo Preview">
                            @endif
                            <div class="invalid-feedback">
                                @error('thumbnail') {{ $message }} @enderror
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="genre">Genre</label>
                            {{-- <input type="text" class="form-control" id="genre" name="genre"placeholder="Genre"value="{{ old('genre', $relshow->genre ?? '') }}" required> --}}
                            <select name="genre[]" id="genre" class="form-control select" multiple>
                                @foreach ($genres as $genre)
                                    <option value="{{$genre->title}}"
                                        @if (isset($relshow) && in_array($genre->title, $currentGenres)) selected @endif>
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
                                <option value="1" @if(old('status', $relshow->status ?? '') == 1) selected @endif>Active</option>
                                <option value="0" @if(old('status', $relshow->status ?? '') == 0) selected @endif>Inactive</option>
                            </select>
                            <div class="invalid-feedback">
                                @error('status') {{ $message }} @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="col-md-12 mb-4">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description"
                                placeholder="Description">{{ old('description', $relshow->description ?? '') }}</textarea>
                            <div class="invalid-feedback">
                                @error('description') {{ $message }} @enderror
                            </div>
                        </div>
                        
                    </div>

                    <button class="btn btn-primary submit-fn mt-4" type="submit">
                        {{ isset($relshow) ? 'Update' : 'Add' }}
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
