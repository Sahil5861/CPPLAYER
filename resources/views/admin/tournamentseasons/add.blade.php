@extends('layout.default')
@section('mytitle', 'Manage TvShow')
@if(isset($tournamentseason))
@section('page', 'Season / Update')
@else
@section('page', 'Season / Add')
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

                <form id="tournamentseason-form" method="post" action="{{ route('savesportstournamentseason') }}" enctype="multipart/form-data" novalidate>
                    @csrf
                    @if (isset($tournamentseason))                        
                        <input type="hidden" name="id" value="{{isset($tournamentseason) ? $tournamentseason->id : ''}}">
                    @endif

                    <input type="hidden" name="sports_tournament_id" id="sports_tournament_id" value="{{$id}}">

                    <div class="form-row">
                        <!-- Name -->
                        <div class="col-md-6 mb-4">
                            <label for="name">Season Title</label>
                            <input type="text" class="form-control" id="season_title" name="season_title" placeholder="Season Title" value="{{ old('season_title', $tournamentseason->season_title ?? '') }}" required>
                            <div class="invalid-feedback">
                                @error('season_title') {{ $message }} @enderror
                            </div>
                        </div>

                        {{-- Sets season order: existing value for edit, or next available based on show ID for add --}}
                        @php
                            $showId = $id; // decoded and passed from controller
                            $seasonOrder = isset($season)
                                ? $season->season_order
                                : (\App\Models\TournamentSeason::where('sports_tournament_id', $showId)->whereNull('deleted_at')->max('season_order') ?? 0) + 1;
                        @endphp
                        <input type="hidden" name="season_order" value="{{ old('season_order', $seasonOrder) }}">


                        {{-- <!-- Language -->
                        <div class="col-md-6 mb-4">
                            <label for="language">Language</label>
                            <input type="text" class="form-control" id="language" name="language"
                                placeholder="e.g. Hindi, English"
                                value="{{ old('language', $tournamentseason->language ?? '') }}" required>
                            <div class="invalid-feedback">
                                @error('language') {{ $message }} @enderror
                            </div>
                        </div> --}}

                        <!-- Logo -->
                        <div class="col-md-6 mb-4">
                            <label for="thumbnail">Logo</label>
                            <input type="text" class="form-control" id="logo" name="logo"
                                placeholder="Enter image path or URL"
                                value="{{ old('logo', $tournamentseason->logo ?? '') }}">
                            @if(isset($tournamentseason) && $tournamentseason->logo)
                                <img src="{{ $tournamentseason->logo }}" width="70" style="margin-top: 5px;" alt="Logo Preview">
                            @endif
                            <div class="invalid-feedback">
                                @error('thumbnail') {{ $message }} @enderror
                            </div>
                        </div>


                        <!-- Logo -->
                        <div class="col-md-6 mb-4">
                            <label for="thumbnail">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" placeholder="Enter Start Date" value="{{ old('start_date', $tournamentseason->start_date ?? '') }}">                            
                            <div class="invalid-feedback">
                                @error('thumbnail') {{ $message }} @enderror
                            </div>
                        </div>

                        <!-- Logo -->
                        <div class="col-md-6 mb-4">
                            <label for="thumbnail">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" placeholder="Enter End Date" value="{{ old('end_date', $tournamentseason->end_date ?? '') }}">                            
                            <div class="invalid-feedback">
                                @error('thumbnail') {{ $message }} @enderror
                            </div>
                        </div>


                        <!-- Status -->
                        <div class="col-md-6 mb-4">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="1" @if(old('status', $tournamentseason->status ?? '') == 1) selected @endif>Active</option>
                                <option value="0" @if(old('status', $tournamentseason->status ?? '') == 0) selected @endif>Inactive</option>
                            </select>
                            <div class="invalid-feedback">
                                @error('status') {{ $message }} @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="col-md-12 mb-4">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description"
                                placeholder="Description">{{ old('description', $tournamentseason->description ?? '') }}</textarea>
                            <div class="invalid-feedback">
                                @error('description') {{ $message }} @enderror
                            </div>
                        </div>
                        
                    </div>

                    <button class="btn btn-primary submit-fn mt-4" type="submit">
                        {{ isset($tournamentseason) ? 'Update' : 'Add' }}
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
