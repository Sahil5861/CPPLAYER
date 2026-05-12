@extends('layout.default')
@section('mytitle', 'Manage TvShow')
@if(isset($tournament))
@section('page', 'Tournament / Update')
@else
@section('page', 'Tournament / Add')
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

                <form id="tournament-form" method="post" action="{{ route('savesportstournament') }}" enctype="multipart/form-data" novalidate>
                    @csrf
                    <input type="hidden" name="id" value="{{ isset($tournament) ? $tournament->id : '' }}">
                    <input type="hidden" name="sport_id" value="{{ $id}}">

                    <div class="form-row">
                        <!-- Name -->
                        <div class="col-md-6 mb-4">
                            <label for="name">Tournament Title</label>
                            <input type="text" class="form-control" id="title" name="title"
                                placeholder="Name"
                                value="{{ old('title', $tournament->title ?? '') }}" required>
                            <div class="invalid-feedback">
                                @error('title') {{ $message }} @enderror
                            </div>
                        </div>
                        
                        {{-- Sets order: uses existing value for edit, or calculates next order based on channel for add --}}
                        @php
                            $sports_cat_order = isset($tournament)
                                ? $tournament->sports_cat_order
                                : (\App\Models\SportsTournament::where('sports_category_id', $id)
                                    ->whereNull('deleted_at')
                                    ->max('sports_cat_order') ?? 0) + 1;
                        @endphp

                        <input type="hidden" name="sports_cat_order" value="{{ old('sports_cat_order', $sports_cat_order) }}">

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
           

                        <!-- Logo -->
                        <div class="col-md-6 mb-4">
                            <label for="logo">Logo</label>
                            <input type="text" class="form-control" id="logo" name="logo"
                                placeholder="Enter image path or URL"
                                value="{{ old('logo', $tournament->logo ?? '') }}">
                            @if(isset($tournament) && $tournament->logo)
                                <img src="{{ $tournament->logo }}" width="100" style="margin-top: 5px;" alt="Logo Preview">
                            @endif
                            <div class="invalid-feedback">
                                @error('logo') {{ $message }} @enderror
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6 mb-4">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="1" @if(old('status', $tournament->status ?? '') == 1) selected @endif>Active</option>
                                <option value="0" @if(old('status', $tournament->status ?? '') == 0) selected @endif>Inactive</option>
                            </select>
                            <div class="invalid-feedback">
                                @error('status') {{ $message }} @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="col-md-12 mb-4">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description"
                                placeholder="Description">{{ old('description', $tournament->description ?? '') }}</textarea>
                            <div class="invalid-feedback">
                                @error('description') {{ $message }} @enderror
                            </div>
                        </div>


                    </div>

                    <button class="btn btn-primary submit-fn mt-4" type="submit">
                        {{ isset($tournament) ? 'Update' : 'Add' }}
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
