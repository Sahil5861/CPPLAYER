@extends('layout.default')
@section('mytitle', 'Manage Channels')
@if(isset($channel))
@section('page', 'Channel  / Update')
@endif
@if(!isset($channel))
@section('page', 'Channel  / Add')
@endif
@section('content')

<style>
    .channel-error, .channel-number-error{
        color: #e7515a;
        font-size: 13px;
        letter-spacing: 1px;
    }
</style>

<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
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

                  {{-- <div class="text-left">
                    <a href="{{route('admin.kid-shows.episodes', base64_encode($id))}}" class="btn btn-primary mb-3">Back to List</a>
                  </div> --}}
                <!-- <div class="row"> -->                    
                    <form id="user-form"  method="post" action="{{route('savekidshowsEpisode', $id)}}" enctype="multipart/form-data" novalidate class="simple-example" >
                        @csrf
                        <input type="hidden" name="id" value="@if(isset($episode)){{$episode->id}}@endif">                        
                        <input type="hidden" name="season_id" id="season_id" value="{{isset($episode) ? $episode->season_id : $id}}">
                        <div class="form-row">
                            {{-- <div class="col-md-6 mb-4">
                                <label for="fullName">Channel Number*</label>
                                <input type="text" class="form-control" id="channel_number" onkeyup="channelNumber(this.value)" name="channel_number" placeholder="Channel Number" value="{{old('channel_number')}}@if(isset($channel)){{$channel->channel_number}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('channel_number') {{ $message }} @enderror
                                </div>
                                <div class="channel-number-error"></div>
                            </div> --}}

                            <div class="col-md-6 mb-4">
                                <label for="label">Episodes Name*</label>
                                <input type="text" class="form-control" id="name" onkeyup="channel(this.value)" name="name" placeholder="Episode Name" value="{{old('name')}}@if(isset($episode)){{$episode->Episoade_Name}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('label') {{ $message }} @enderror
                                </div>
                                <div class="channel-error"></div>
                            </div>

                            <?php 

                                $episode_count = \App\Models\KidshowsEpisode::whereNull('deleted_at')->where('season_id', $id)->count();
                                $formatted = $episode_count + 1;
                            
                            ?>
                            

                            <div class="col-md-6 mb-4">
                                <label for="order">Order*</label>
                                <input type="number" name="order" id="order" class="form-control" value="{{old('order', isset($episode) ? $episode->episoade_order : $formatted)}}" placeholder="0-9999">
                                <div class="invalid-feedback">
                                    @error('order') {{ $message }} @enderror
                                </div>
                                <div class="channel-error"></div>
                            </div>


                            

                            <div class="col-md-6 mb-4">
                                <label for="source">Source</label>
                                {{-- <input type="text" name="source" id="source" class="form-control" value="{{old('source', isset($episode) ? $episode->source : '')}}" placeholder="Source"> --}}
                                <select name="source" id="source" class="form-control select">
                                    <option value="youtube" @if(isset($episode) && $episode->source == 'youtube'){{'selected'}} @endif>Youtube</option>
                                    <option value="m3u8" @if(isset($episode) && $episode->source == 'm3u8'){{'selected'}} @endif>M3U8</option>
                                </select>
                                <div class="invalid-feedback">
                                    @error('trailer') {{ $message }} @enderror
                                </div>
                                <div class="channel-error"></div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="trailer">Url*</label>
                                <input type="text" name="source_url" id="source_url" class="form-control" value="{{old('trailer', isset($episode) ? $episode->url : '')}}" placeholder="Source Url" required>
                                <div class="invalid-feedback">
                                    @error('trailer') {{ $message }} @enderror
                                </div>
                                <div class="channel-error"></div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="trailer">Thumbnail Url</label>
                                <input type="url" name="thumbnail_url" id="thumbnail_url" class="form-control" value="{{old('thumbnail_url', isset($episode) ? $episode->episoade_image : '')}}" placeholder="Thumbnail Url">
                                <div class="invalid-feedback">
                                    @error('trailer') {{ $message }} @enderror
                                </div>
                                <div class="channel-error"></div>
                            </div>
                            

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Downloadable</label>
                                <select name="downloadable" id="downloadable" class="form-control select">
                                    <option value="0" @if(isset($episode) && $episode->downloadable == 0){{'selected'}}@endif>No</option>
                                    <option value="1" @if(isset($episode) && $episode->downloadable == 1){{'selected'}}@endif>Yes</option>
                                </select>
                                <div class="invalid-feedback">
                                    @error('status') {{ $message }} @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Status</label>
                                <select name="status" id="status" class="form-control select">
                                    <option value="1" @if(isset($episode) && $episode->status == 1){{'selected'}}@endif>Active</option>
                                    <option value="0" @if(isset($episode) && $episode->status == 0){{'selected'}}@endif>De-Active</option>
                                </select>
                                <div class="invalid-feedback">
                                    @error('status') {{ $message }} @enderror
                                </div>
                            </div>


                            <div class="col-md-6 mb-4">
                                <label for="status">Type</label>
                                <select name="type" id="type" class="form-control select">
                                    <option value="0" @if(isset($episode) && $episode->type == 0){{'selected'}}@endif>Free</option>
                                    <option value="1" @if(isset($episode) && $episode->type == 1){{'selected'}}@endif>Premium</option>
                                </select>
                                <div class="invalid-feedback">
                                    @error('size') {{ $message }} @enderror
                                </div>
                                <div class="channel-error"></div>
                            </div>

                            <div class="col-md-12 mb-4">
                                <label for="trailer">Description</label>
                                <textarea name="description" id="description" class="form-control" cols="30" rows="3">{{old('description', isset($episode) ? $episode->episoade_description : '')}}</textarea>
                                <div class="invalid-feedback">
                                    @error('description') {{ $message }} @enderror
                                </div>
                                <div class="channel-error"></div>
                            </div>

                            {{-- <div class="col-md-12 mb-4">                                
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="skip_available" name="skip_available" value="1"
                                        {{ isset($episode) && $episode->skip_available == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="skip_available" style="user-select: none; cursor: pointer;">Intro Skip Available</label>

                                    
                                </div>
                                <div class="invalid-feedback d-block">
                                    @error('skip_available') {{ $message }} @enderror
                                </div>
                            </div> --}}

                            {{-- <div class="col-md-6 mb-4">
                                <label for="intro_start">Intro Start</label>
                                <input type="text" name="intro_start" id="intro_start" class="form-control flat_picker" placeholder="Select Time" value="{{old('intro_start', isset($episode) ? $episode->intro_start : '')}}">
                                <div class="invalid-feedback">
                                    @error('intro_start') {{ $message }} @enderror
                                </div>
                                <div class="channel-error"></div>
                            </div> --}}

                            {{-- <div class="col-md-6 mb-4">
                                <label for="intro_end">Intro End</label>
                                <input type="text" name="intro_end" id="intro_end" class="form-control flat_picker" placeholder="Select Time" value="{{old('intro_end', isset($episode) ? $episode->intro_end : '')}}">
                                <div class="invalid-feedback">
                                    @error('intro_end') {{ $message }} @enderror
                                </div>
                                <div class="channel-error"></div>
                            </div> --}}

                            
                                                
                            {{-- <div class="col-md-6 mb-4">
                                <label for="channel_link">Channel Link*</label>
                                <input type="text" class="form-control" id="channel_link" name="channel_link" placeholder="Channel Link" value="{{old('channel_link')}}@if(isset($channel)){{$channel->channel_link}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('channel_link') {{ $message }} @enderror
                                </div>
                            </div> --}}
                            {{-- <div class="col-md-12 mb-4">
                                <label for="channel_description">Movie Description</label>
                                <textarea type="text" class="form-control" id="movie_description" name="movie_description" rows="3" placeholder="Movie Description">{{old('movie_description', isset($movie) ? $movie->description : '')}}</textarea>
                                <div class="invalid-feedback">
                                    @error('movie_description') {{ $message }} @enderror
                                </div>
                            </div> --}}
                        </div>
                        @if(isset($episode))
                        <button class="btn btn-primary submit-fn mt-2" type="submit">Update</button>
                        @else
                        <button class="btn btn-primary submit-fn mt-2" type="submit">Add</button>
                        @endif

                    </form>
                <!-- </div> -->
            </div>
        </div>
    </div>

</div>

<script>
    function channel(val){
        var channel_name = val;
        console.log(channel_name);
        $.ajax({
            url: "{{ url('check-channel-name') }}",
            method: 'post',
            data: {
                'channel_name': channel_name,
                "_token": "{{ csrf_token() }}"
            },
            success: function(result){
                if(result){
                    $('.channel-error').text('');
                }else{
                    $('.channel-error').text('Channel name not available');
                }
            }
        });
    }

    function channelNumber(val){
        var channel_number = val;
        $.ajax({
            url: "{{ url('check-channel-number') }}",
            method: 'post',
            data: {
                'channel_number': channel_number,
                "_token": "{{ csrf_token() }}"
            },
            success: function(result){
                if(result){
                    $('.channel-number-error').text('');
                }else{
                    $('.channel-number-error').text('Channel number already exists!!!');
                }
            }
        });
    }
</script>

@endsection

@section('footer')

<!-- footer script if required -->
@endsection
