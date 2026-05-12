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
                <!-- <div class="row"> -->
                    <?php 
                        $kidshowName = \App\Models\KidsShow::where('id', $id)->first()->name;
                    ?>
                    <h3 class="text-primary mb-3">Add Seasons for {{$kidshowName}}</h3>
                    <form id="user-form"  method="post" action="{{route('saveKidShowsSeason')}}" enctype="multipart/form-data" novalidate class="simple-example" >
                        @csrf
                        <input type="hidden" name="id" value="@if(isset($season)){{$season->id}}@endif">                        
                        <input type="hidden" name="webseries_id" name="webseries_id" value="{{$id}}">
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
                                <label for="fullName">Season Name*</label>
                                <input type="text" class="form-control" id="name" onkeyup="channel(this.value)" name="name" placeholder="Web Series Season Name" value="{{old('name')}}@if(isset($season)){{$season->season_name}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('name') {{ $message }} @enderror
                                </div>
                                <div class="channel-error"></div>
                            </div>


                            <div class="col-md-6 mb-4">
                                <label for="order">Order *</label>
                                <input type="number" name="order" id="order" class="form-control" value="{{old('order', isset($season) ? $season->season_order : 0)}}" required min="0" placeholder="Order">
                                <div class="invalid-feedback">
                                    @error('runtime') {{ $message }} @enderror
                                </div>
                                <div class="channel-error"></div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="fullName">Banner*</label>
                                <input type="text" class="form-control" id="banner" name="banner" placeholder="Web Series Banner Image" value="{{old('banner')}}@if(isset($season)){{$season->banner}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('banner') {{ $message }} @enderror
                                </div>
                                <div class="channel-error"></div>
                                @if (isset($season) && ($season->banner != ''))
                                    <img src="{{$season->banner}}" alt="image" style="width: 150px;" class="my-2" >
                                @endif
                            </div>
                            
                    
                            <div class="col-md-6 mb-4">
                                <label for="fullName">Status</label>
                                <select name="status" id="status" class="form-control select">
                                    <option value="1" @if(isset($season) && $season->status == 1){{'selected'}}@endif>Active</option>
                                    <option value="0" @if(isset($season) && $season->status == 0){{'selected'}}@endif>De-Active</option>
                                </select>
                                <div class="invalid-feedback">
                                    @error('status') {{ $message }} @enderror
                                </div>
                            </div>
                            
                        </div>
                        @if(isset($season))
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
