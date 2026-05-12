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

                @if(session()->has('error'))
                  <div class="alert alert-danger alert-block">
                      <button type="button" class="close" data-dismiss="alert">×</button>
                      <strong>{{ session()->get('error') }}</strong>
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
                <!-- <div class="row"> -->

                    @php
                        $last_channel_number = \App\Models\Channel::max('channel_number');
                    @endphp
                    <form id="user-form"  method="post" action="{{route('saveChannel')}}" enctype="multipart/form-data" novalidate class="simple-example" >
                        @csrf
                        <input type="hidden" name="id" value="@if(isset($channel)){{$channel->id}}@endif">
                        <input type="hidden" name="channel_logo_old" value="@if(isset($channel)){{$channel->channel_logo}}@endif">
                        <div class="form-row">                            

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Channel Number*</label>
                                <input type="number" class="form-control" id="channel_number" name="channel_number" placeholder="Channel Number eg: {{$last_channel_number+1}}" value="{{old('channel_number')}}@if(isset($channel)){{$channel->channel_number}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('channel_number') {{ $message }} @enderror
                                </div>
                                <div class="channel-error"></div>
                            </div>


                            <div class="col-md-6 mb-4">
                                <label for="fullName">Channel Name*</label>
                                <input type="text" class="form-control" id="channel_name" onkeyup="channel(this.value)" name="channel_name" placeholder="Channel Name" value="{{old('channel_name')}}@if(isset($channel)){{$channel->channel_name}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('channel_name') {{ $message }} @enderror
                                </div>
                                <div class="channel-error"></div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="channel_logo">Channel Logo</label>
                                <input type="url" name="channel_logo" id="channel_logo" class="form-control" required placeholder="Channel Logo" value="{{old('channel_logo')}}@if(isset($channel)){{$channel->channel_logo}}@endif">
                                <div class="invalid-feedback">
                                    @error('channerl_logo') {{ $message }} @enderror
                                </div>
                            </div>

                            

                            {{-- <div class="col-md-6 mb-4">
                                <label for="fullName">Channel Logo* </label>
                                <input type="file" class="form-control" id="channel_logo" name="channel_logo" placeholder="Email" value="{{old('channel_logo')}}@if(isset($channel)){{$channel->channel_logo}}@endif">
                                @if(isset($channel) && $channel->channel_logo!='')
                                    <img style="position: absolute;" src="{{asset($channel->channel_logo)}}" width="30px">
                                @endif
                                <div class="invalid-feedback">
                                    @error('channel_logo') {{ $message }} @enderror
                                </div>
                            </div> --}}

                            {{-- <div class="col-md-6 mb-4">
                                <label for="fullName">Channel Background Image* </label>
                                <input type="file" class="form-control" id="channel_bg" name="channel_bg" placeholder="Email" value="{{old('channel_bg')}}@if(isset($channel)){{$channel->channel_bg}}@endif">
                                @if(isset($channel) && $channel->channel_bg!='')
                                    <img style="position: absolute;" src="{{asset($channel->channel_bg)}}" width="30px">
                                @endif
                                <div class="invalid-feedback">
                                    @error('channel_bg') {{ $message }} @enderror
                                </div>
                            </div> --}}

                            

                            <div class="col-md-6 mb-4">
                                <label for="channel_bg">Channel Background</label>
                                <input type="url" name="channel_bg" id="channel_bg" class="form-control" required placeholder="Channel Background" value="{{old('channel_bg')}}@if(isset($channel)){{$channel->channel_bg}}@endif">
                                <div class="invalid-feedback">
                                    @error('channel_bg') {{ $message }} @enderror
                                </div>
                            </div>

                            <!-- <div class="col-md-6 mb-4">
                                <label for="fullName">Password</label>
                                <input type="text" class="form-control" id="password" name="password" placeholder="Password" value="@if(isset($channel)){{$channel->real_password}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('password') {{ $message }} @enderror
                                </div>
                            </div>-->

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Genre*</label>
                                <select name="channel_genre[]" id="channel_genre" multiple class="form-control select" required>
                                     <!-- <option value="">--Select Genre--</option> -->
                                    <?php
                                    foreach($genres as $genre){
                                        if(isset($channelGenre) && in_array($genre->title,$channelGenre)){

                                             echo '<option value="'.$genre->title.'" selected>'.$genre->title.'</option>';

                                        }else{
                                            echo '<option value="'.$genre->title.'">'.$genre->title.'</option>';
                                        }
                                    }
                                 ?>
                                </select>
                                <div class="invalid-feedback">
                                    @error('channel_genre') {{ $message }} @enderror
                                </div>
                                <!-- <div class="valid-feedback">
                                    Looks good!
                                </div> -->
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Language*</label>
                                <select name="channel_language" id="channel_language" class="form-control select">
                                     <option value="">--Select Language--</option>
                                    <?php
                                    foreach($languages as $language){
                                        if(isset($channel) && $channel->channel_language == $language->id){
                                             echo '<option value="'.$language->id.'" selected>'.$language->title.'</option>';

                                        }else{
                                            echo '<option value="'.$language->id.'">'.$language->title.'</option>';
                                        }
                                    }
                                 ?>
                                </select>
                                <div class="invalid-feedback">
                                    @error('channel_language') {{ $message }} @enderror
                                </div>
                                <!-- <div class="valid-feedback">
                                    Looks good!
                                </div> -->
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <label for="stream_type">Stream Type*</label>
                                <select name="stream_type" id="stream_type" class="form-control"  onchange="toggleBackupUrl(this)">
                                    <option value="M3u8" @if(isset($channel) && $channel->stream_type == 'M3u8'){{'selected'}} @endif>M3u8</option>
                                    <option value="YoutubeLive" @if(isset($channel) && $channel->stream_type == 'YoutubeLive'){{'selected'}} @endif>YoutubeLive</option>
                                    <option value="Custom" @if(isset($channel) && $channel->stream_type == 'Custom'){{'selected'}} @endif>Custom</option>
                                </select>
                                <div class="invalid-feedback">
                                    @error('stream_type') {{ $message }} @enderror
                                </div>
                            </div>

                             <script>
                                function toggleBackupUrl(elem) {
                                    const value  = elem.value;                                    
                                    const bkurlDiv = document.getElementById('bkUrldiv');

                                    if (value == 'M3u8') {
                                        bkurlDiv.style.display = 'block';
                                    }
                                    else{
                                        bkurlDiv.style.display = 'none';
                                    }
                                }
                            </script>
                            

                            <div class="col-md-6 mb-4">
                                <label for="channel_link">Channel Link*</label>
                                <input type="text" class="form-control" id="channel_link" name="channel_link" placeholder="Channel Link" value="{{old('channel_link')}}@if(isset($channel)){{$channel->channel_link}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('channel_link') {{ $message }} @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-4" id="bkUrldiv" style=" display: {{isset($channel) && $channel->stream_type != 'M3u8' ? 'none' : 'block' }}">
                                <label for="trailer">Backup Url</label>
                                <input type="text" name="bk_url" id="bk_url" class="form-control" value="{{old('trailer', isset($channel) ? $channel->backup_url : '')}}" placeholder="Backup Url">
                                <div class="invalid-feedback">
                                    @error('channel_link') {{ $message }} @enderror
                                </div>
                                <div class="channel-error"></div>
                            </div>



                            <div class="col-md-6 mb-4">
                                <label for="fullName">Status</label>
                                <select name="status" id="status" class="form-control select">
                                    <option value="1" @if(isset($channel) && $channel->status == 1){{'selected'}}@endif>Active</option>
                                    <option value="0" @if(isset($channel) && $channel->status == 0){{'selected'}}@endif>De-Active</option>
                                </select>
                                <div class="invalid-feedback">
                                    @error('status') {{ $message }} @enderror
                                </div>
                            </div>

                            <div class="col-md-12 mb-3">                                    
                                <label class="new-control new-checkbox checkbox-primary cdn-checkbox">
                                    <input type="checkbox" class="new-control-input" {{isset($channel) && $channel->sport_flag == 1 ? 'checked' : ''}}  name="sport_flag" value="1">
                                    <span class="new-control-indicator"></span>
                                    <span class="mx-2" style="user-select: none;">Check for Sports Live</span>
                                </label>
                            </div>

                            <div class="col-md-12 mb-4">
                                <label for="channel_description">Channel Description</label>
                                <textarea type="text" class="form-control" id="channel_description" name="channel_description" placeholder="Channel Description">{{old('channel_description')}}@if(isset($channel)){{$channel->channel_description}}@endif</textarea>
                                <div class="invalid-feedback">
                                    @error('channel_description') {{ $message }} @enderror
                                </div>
                            </div>
                        </div>
                        @if(isset($channel))
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
