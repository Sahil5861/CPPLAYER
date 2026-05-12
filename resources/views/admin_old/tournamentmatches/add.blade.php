@extends('layout.default')
@section('mytitle', 'Manage TvShow')
@if(isset($match))
@section('page', 'Matches / Update')
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
                <div class="text-left">
                    <a href="{{route('admin.sporttournamentseasonsepisodes', base64_encode($id))}}" class="btn btn-sm  btn-primary mb-3">Back to List</a>
                </div>

                <form id="tournamentseason-form" method="post" action="{{ route('savesportstournamentseasonepisodes') }}" enctype="multipart/form-data" novalidate>
                    @csrf
                    @if (isset($match))                        
                        <input type="hidden" name="id" value="{{isset($match) ? $match->id : ''}}">
                    @endif

                    <input type="hidden" name="tournament_season_id" id="tournament_season_id" value="{{$id}}">

                    <div class="form-row">
                        <!-- Name -->
                        <div class="col-md-6 mb-4">
                            <label for="name">Match Title</label>
                            <input type="text" class="form-control" id="match_title" name="match_title" placeholder="Match Title" value="{{ old('match_title', $match->match_title ?? '') }}" required>
                            <div class="invalid-feedback">
                                @error('season_title') {{ $message }} @enderror
                            </div>
                        </div>


                         {{-- Sets order: uses existing value for edit, or calculates next order based on match for add --}}
                        @php
                            $match_order = isset($match)
                                ? $match->match_order
                                : (\App\Models\TournamentMatches::where('tournament_season_id', $id)
                                    ->whereNull('deleted_at')
                                    ->max('match_order') ?? 0) + 1;
                        @endphp

                        <input type="hidden" name="match_order" value="{{ old('match_order', $match_order) }}">


                        <!-- Match Date -->
                        <div class="col-md-6 mb-4">
                            <label for="thumbnail">Match Date</label>
                            <input type="date" class="form-control" id="match_date" name="match_date" placeholder="Enter Match Date" value="{{isset($match) ? $match->match_date : ''}}">                            
                            <div class="invalid-feedback">
                                @error('match_date') {{ $message }} @enderror
                            </div>
                        </div>

                        <!-- Match Time -->
                        <div class="col-md-6 mb-4">
                            <label for="thumbnail">Match Time</label>
                            <input type="text" class="form-control flat_picker" id="match_time" name="match_time" placeholder="Enter Match Time" value="{{ old('match_time', $match->match_time ?? '') }}">                            
                            <div class="invalid-feedback">
                                @error('match_time') {{ $message }} @enderror
                            </div>
                        </div>

                        <!-- Streaming Info -->
                        <div class="col-md-6 mb-4">
                            <label for="status">Streaming Info</label>
                            <select name="streaming_info" id="streaming_info" class="form-control select">
                                <option value="">--Select Streaming Info--</option>
                                <option value="Live" @if(old('streaming_info', $match->streaming_info ?? '') == 'Live') selected @endif>Live</option>
                                <option value="Replay" @if(old('streaming_info', $match->streaming_info ?? '') == 'Replay') selected @endif>Replay</option>
                                <option value="Highlights" @if(old('streaming_info', $match->streaming_info ?? '') == 'Highlights') selected @endif>Highlights</option>
                            </select>
                            <div class="invalid-feedback">
                                @error('streaming_info') {{ $message }} @enderror
                            </div>
                        </div>


                        <div class="col-md-6 mb-4">
                            <label for="video_url">Video Url*</label>
                            <input type="text" name="video_url" id="video_url" class="form-control" required value="{{old('video_url', $match->video_url ?? '')}}" placeholder="Enter Video Url">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="video_url">Stream Type</label>
                            <select name="stream_type" id="stream_type" class="form-control">
                                <option value="youtube" @if(old('stream_type', $match->stream_type ?? '') == 'youtube') selected @endif>Youtube</option>
                                <option value="m3u8" @if(old('stream_type', $match->stream_type ?? '') == 'm3u8') selected @endif>M3U8</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="thumbnail_url">Thumbnail Url</label>
                            <input type="text" name="thumbnail_url" id="thumbnail_url" class="form-control" value="{{old('thumbnail_url', $match->thumbnail_url ?? '')}}" placeholder="Enter Thumbnail Url">
                        </div>

                        <!-- Streaming Info -->
                        <div class="col-md-6 mb-4">
                            <label for="status">Match Type</label>
                            <select name="match_type" id="match_type" class="form-control select">
                                <option value="">--Select Match Type--</option>
                                <option value="League" @if(old('streaming_info', $match->match_type ?? '') == 'League') selected @endif>League</option>
                                <option value="Knockout" @if(old('streaming_info', $match->match_type ?? '') == 'Knockout') selected @endif>Knockout</option>
                                <option value="Quarter Final" @if(old('streaming_info', $match->match_type ?? '') == 'Quarter Final') selected @endif>Quarter Final</option>
                                <option value="Semi Final" @if(old('streaming_info', $match->match_type ?? '') == 'Semi Final') selected @endif>Semi Final</option>
                                <option value="Final" @if(old('streaming_info', $match->match_type ?? '') == 'Final') selected @endif>Final</option>
                            </select>
                            <div class="invalid-feedback">
                                @error('streaming_info') {{ $message }} @enderror
                            </div>
                        </div>




                        <!-- Status -->
                        <div class="col-md-6 mb-4">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="1" @if(old('status', $match->status ?? '') == 1) selected @endif>Active</option>
                                <option value="0" @if(old('status', $match->status ?? '') == 0) selected @endif>Inactive</option>
                            </select>
                            <div class="invalid-feedback">
                                @error('status') {{ $message }} @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="col-md-12 mb-4">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description"
                                placeholder="Description">{{ old('description', $match->description ?? '') }}</textarea>
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
