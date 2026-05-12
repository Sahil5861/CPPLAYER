@extends('layout.default')
@section('mytitle', 'Manage Slider')
@if(isset($slider))
@section('page', 'Slider / Update')
@endif
@if(!isset($slider))
@section('page', 'Slider / Add')
@endif
@section('content')
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
                    <form id="user-form"  method="post" action="{{route('saveSlider')}}" enctype="multipart/form-data" novalidate class="simple-example" >
                        @csrf
                        <input type="hidden" name="slider_old_image" value="@if(isset($slider)){{$slider->image}}@endif">
                        <input type="hidden" name="id" value="@if(isset($slider)){{$slider->id}}@endif">
                        <div class="form-row">

                            {{-- <div class="col-md-6 mb-4">
                                <label for="fullName">Slider Type*</label>
                                <select name="content_type" id="add_slider_type" class="form-control">
                                    <option value="1"@if(isset($slider)){{$slider->content_type == '1' ? 'selected' : ''}} @endif>Movies</option>                                    
                                    <option value="2"@if(isset($slider)){{$slider->content_type == '2' ? 'selected' : ''}} @endif>Web Series</option>                                    
                                    <option value="5"@if(isset($slider)){{$slider->content_type == '5' ? 'selected' : ''}} @endif>Live Channels</option>
                                </select>
                                <div class="invalid-feedback">
                                    @error('content_type') {{ $message }} @enderror
                                </div>
                            </div> --}}

                            {{-- <div class="col-md-6 mb-4" id="add_cs_Movie_div">
            					<label class="control-label">Movie</label>
                                <select id="Movie_id" class="form-control select" onchange="(updatedata('1', this))" name="content_id_movie">
                                    <option value="">Select an Option</option>    
                                    @foreach ($movies as $movie)
                                        <option value="{{$movie->id}}" @if(isset($slider)) {{$slider->content_id == $movie->id ? 'selected' : ''}} @endif>{{$movie->name}}</option>
                                    @endforeach
                                </select>            					
            				</div>

                            <div class="col-md-6 mb-4" id="add_cs_Web_Series_div">
            					<label class="control-label">Web Series</label>
                                <select id="Web_Series_id" class="form-control select" name="content_id_series" style="width:100%;" onchange="(updatedata('2', this))">
                                    <option value="">Select Series</option> 
                                    <option value="" selected>Select Channel</option>      
                                    @foreach ($serieses as $series)
                                        <option value="{{$series->id}}" @if(isset($slider)){{$slider->content_id == $series->id ? 'selected' : ''}} @endif>{{$series->name}}</option>
                                    @endforeach
                                </select>            					                                
            				</div>

                            <div class="col-md-6 mb-4" id="add_cs_Live_Tv_div">
            					<label class="control-label">Live Channels</label>
                                <select id="Live_Tv_id" class="form-control select" name="content_id_channel" style="width:100%;" onchange="(updatedata('5', this))">
                                    <option value="" selected>Select Channel</option>    
                                    @foreach ($livechannels as $channel)
                                        <option value="{{$channel->id}}" @if(isset($slider)){{$slider->content_id == $channel->id ? 'selected' : ''}} @endif>{{$channel->channel_name}}</option>
                                    @endforeach
                                </select>            					                                
            				</div> --}}
                            
                            <div class="col-md-6 mb-4">
                                <label for="fullName">Title*</label>
                                <input type="text" class="form-control" id="title" name="title" placeholder="Title" value="{{old('title')}}@if(isset($slider)){{$slider->title}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('title') {{ $message }} @enderror
                                </div>
                            </div>

                            {{-- <div class="col-md-6 mb-4">
                                <label for="fullName">Description</label>
                                <textarea type="text" class="form-control" id="description" name="description" placeholder="Title" >{{old('description')}}@if(isset($slider)){{$slider->description}}@endif</textarea>
                                <div class="invalid-feedback">
                                    @error('description') {{ $message }} @enderror
                                </div>
                            </div> --}}
                            <div class="col-md-6 mb-4">
                                <label for="fullName">Image*</label>                                
                                <input type="text" class="form-control" id="image" name="image" placeholder="Banner" value="@if(isset($slider)){{$slider->banner}} @endif" required>
                                <div class="invalid-feedback">
                                    @error('title') {{ $message }} @enderror
                                </div>
                                @if(isset($slider))
                                <img src="{{$slider->banner}}" width="100px" style="margin-top: 5px;">
                                @endif
                            </div>                            

                            {{-- <div class="col-md-6 mb-4">
                                <label for="url">URL*</label>
                                <input type="text" name="url" id="url" class="form-control" value="@if(isset($slider)){{$slider->url}}@endif" placeholder="URL" required>
                            </div> --}}

                            {{-- <div class="col-md-6 mb-4">
                                <label for="url">Source Type</label>
                                <select name="source_type" id="source_type" class="form-control select">
                                    <option value="m3u8">M3U8</option>
                                    <option value="Youtube">Youtube</option>
                                </select>
                            </div>  --}}

                            {{-- <div class="col-md-6 mb-4">
                                <label for="source_type">Source Type</label>
                                <input type="text" name="source_type" id="source_type" class="form-control" value="@if(isset($slider)){{$slider->source_type}}@endif" >
                            </div> --}}
                            
                            <div class="col-md-6 mb-4">
                                <label for="fullName">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="1" @if(isset($slider) && $slider->status == 1){{'selected'}}@endif>Active</option>
                                    <option value="0" @if(isset($slider) && $slider->status == 0){{'selected'}}@endif>In-Active</option>
                                </select>
                                <div class="invalid-feedback">
                                    @error('status') {{ $message }} @enderror
                                </div>                                
                            </div>
                        </div>
                        @if(isset($slider))
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


@endsection

@section('footer')

<script>

    // let issetSlider = '{{isset($slider)}}' ? true : false;
    // if (issetSlider) {
    //     let contentType = '{{isset($slider) ? $slider->content_type : ''}}';
    //     if (contentType == 1) {
    //         document.getElementById('add_cs_Movie_div').removeAttribute("hidden");
    //         document.getElementById('add_cs_Live_Tv_div').setAttribute("hidden", "");
    //         document.getElementById('add_cs_Web_Series_div').setAttribute("hidden", "");
    //     }
    //     else if (contentType == 2) {
    //         document.getElementById('add_cs_Movie_div').setAttribute("hidden", "");
    //         document.getElementById('add_cs_Web_Series_div').removeAttribute("hidden");
    //         document.getElementById('add_cs_Live_Tv_div').setAttribute("hidden", "");
    //     }
    //     else{
    //         document.getElementById('add_cs_Movie_div').setAttribute("hidden", "");
    //         document.getElementById('add_cs_Web_Series_div').setAttribute("hidden", "");
    //         document.getElementById('add_cs_Live_Tv_div').removeAttribute("hidden", "");

    //     }
    // }
    // else{
    //     document.getElementById('add_cs_Live_Tv_div').setAttribute("hidden", "");
    //     document.getElementById('add_cs_Web_Series_div').setAttribute("hidden", "");
    // }
    $("#add_slider_type").change(function () {        
        // alert($(this).val())
        $('.select').val('').trigger('change'); // Clear previous selection
        $('.select').select2('destroy').select2({            
            placeholder: "Select an option",  // Optional placeholder            
        }); // Full re-init
        if ($(this).val() == 1 || $(this).val() == 2) {            
            if ($(this).val() == 1) {
                document.getElementById('add_cs_Movie_div').removeAttribute("hidden");
                document.getElementById('add_cs_Web_Series_div').setAttribute("hidden", "");
                document.getElementById('add_cs_Live_Tv_div').setAttribute("hidden", "");
            } else if ($(this).val() == 2) {
                document.getElementById('add_cs_Movie_div').setAttribute("hidden", "");
                document.getElementById('add_cs_Web_Series_div').removeAttribute("hidden");
                document.getElementById('add_cs_Live_Tv_div').setAttribute("hidden", "");
            }
        } else {
            
            document.getElementById('add_cs_Movie_div').setAttribute("hidden", "");
            document.getElementById('add_cs_Web_Series_div').setAttribute("hidden", "");
            document.getElementById('add_cs_Live_Tv_div').removeAttribute("hidden");
        } 
        
        
    });

    function updatedata(content_type, elem){
        // alert(content_type)
        let id = $(elem).val();        
        let url = '';
        
        if (content_type == 1) {
            url = '{{route("get-movie-by-id")}}';
        }
        else if (content_type == 2) {
            url = '{{route("get-sereis-by-id")}}';
        }
        else{
            url = '{{route("get-channel-by-id")}}';
        }

        $.ajax({
            type: 'GET',
            url: url,
            data: {id:id},
            success: function (response) {
                console.log(response);
                $('#title').val(response.title)                
                $('#url').val(response.url)      
                
                if (response.source_type != '') {
                    $('#source_type').show();    
                }
                $('#source_type').val(response.source_type)                
            }
        })
    }
    


</script>

<!-- footer script if required -->
@endsection
