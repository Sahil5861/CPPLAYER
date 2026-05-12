@extends('layout.default')
@section('mytitle', 'Manage TvShow')
@if(isset($episode))
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

                <div class="text-left">
                    <a href="{{route('admin.tvshowpak.episode', base64_encode($id))}}" class="btn btn-sm btn-primary mb-3">Back to List</a>
                </div>

                <form id="episode-form" method="post" action="{{ route('saveTvShowEpisodepak') }}" enctype="multipart/form-data" novalidate>
                    @csrf
                    @if (isset($episode))                        
                        <input type="hidden" name="id" value="{{isset($episode) ? $episode->id : ''}}">
                    @endif

                    <input type="hidden" name="season_id" id="season_id" value="{{$id}}">

                    <div class="form-row">
                        <!-- Name -->
                        <div class="col-md-6 mb-4">
                            <label for="name">Title*</label>
                            <input type="text" class="form-control" id="title" name="title" placeholder="Title" value="{{ isset($episode) ? $episode->title : '' }}" required>
                            <div class="invalid-feedback">
                                @error('title') {{ $message }} @enderror
                            </div>
                        </div>

                        <?php                         
                            $count = \App\Models\TvShowEpisode::whereNull('deleted_at')->where('season_id', $id)->count();
                            $count = $count + 1;
                        ?>

                        <!-- Episode Number -->
                        <div class="col-md-6 mb-4">
                            <label for="name">Episode Number</label>
                            <input type="text" class="form-control" id="episode_number" name="episode_number" placeholder="Episode Number" value="{{ old('episode_number', $episode->episode_number ?? $count) }}" oninput="this.value = this.value.replace(/[^0-9]/g, '');" >
                            <div class="invalid-feedback">
                                @error('name') {{ $message }} @enderror
                            </div>
                        </div>

                        <!-- Logo -->
                        <div class="col-md-6 mb-4">
                            <label for="duration">Duration</label>
                            <input type="text" class="form-control" id="duration" name="duration" placeholder="Enter Duration in minutes" value="{{ old('logo', $episode->duration ?? '') }}" oninput="this.value = this.value.replace(/[^0-9]/g, '');">                            
                            <div class="invalid-feedback">
                                @error('thumbnail') {{ $message }} @enderror
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="streaming_type">Streaming Type</label>
                            <select name="streaming_type" id="streaming_type" class="form-control select">
                                <option value="youtube" @if(isset($episode) && $episode->streaming_type == 'youtube') selected @endif>Youtube</option>
                                <option value="m3u8" @if(isset($episode) && $episode->streaming_type == 'm3u8') selected @endif>m3u8</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="name">Video Url*</label>
                            <input type="text" class="form-control" id="url" name="video_url" placeholder="URL" value="{{ old('url', $episode->video_url ?? '') }}" required>
                            <div class="invalid-feedback">
                                @error('url') {{ $message }} @enderror
                            </div>
                        </div>

                        
                        <!-- Status -->
                        <div class="col-md-6 mb-4">
                            <label for="status">Thumbnail</label>
                            <input type="text" name="thumbnail" id="thumbnail" class="form-control" placeholder="Thumbnail Url" value="{{old('thumbnail', $episode->thumbnail ?? '')}}">
                            <div class="invalid-feedback">
                                @error('status') {{ $message }} @enderror
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="release_date">Release Date</label>
                            <input type="date" name="release_date" id="release_date" class="form-control" placeholder="Release Date" value="{{old('release_date', $episode->release_date ?? '')}}">
                            <div class="invalid-feedback">
                                @error('release_date') {{ $message }} @enderror
                            </div>
                        </div>

                        {{-- <div class="col-md-6 mb-4">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control select">
                                <option value="1" @if(isset($episode) && $episode->status == 1) selected @endif>Active</option>
                                <option value="0" @if(isset($episode) && $episode->status == 0) selected @endif>In active</option>
                            </select>
                        </div> --}}

                        <!-- Description -->
                        <div class="col-md-12 mb-4">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description"
                                placeholder="Description">{{ old('description', $episode->description ?? '') }}</textarea>
                            <div class="invalid-feedback">
                                @error('description') {{ $message }} @enderror
                            </div>
                        </div>
                        
                    </div>

                    <button class="btn btn-primary submit-fn mt-4" type="submit">
                        {{ isset($episode) ? 'Update' : 'Add' }}
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
