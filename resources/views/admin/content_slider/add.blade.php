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
                    <form id="user-form"  method="post" action="{{route('saveSlider.contentNetwork')}}" enctype="multipart/form-data" novalidate class="simple-example" >
                        @csrf
                        <input type="hidden" name="slider_old_image" value="@if(isset($slider)){{$slider->image}}@endif">
                        <input type="hidden" name="id" value="@if(isset($slider)){{$slider->id}}@endif">

                        <input type="hidden" name="content_network_id" id="content_network_id" value="@if(isset($slider)){{$slider->content_network_id}} @else{{$id}}@endif">
                        <div class="form-row">

                            
                            <div class="col-md-6 mb-4">
                                <label for="fullName">Title*</label>
                                <input type="text" class="form-control" id="title" name="title" placeholder="Title" value="{{old('title')}}@if(isset($slider)){{$slider->title}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('title') {{ $message }} @enderror
                                </div>
                            </div>
                            
                            
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
                            <div class="col-md-6 mb-4">
                                <label for="fullName">Slider For*</label>                                
                                <select name="slider_for" id="slider_for" class="form-control">
                                    <option value="movies" @if(isset($slider) && $slider->slider_for == "movies"){{'selected'}}@endif>Movie</option>
                                    <option value="webseries" @if(isset($slider) && $slider->slider_for == "webseries"){{'selected'}}@endif>Web Series</option>
                                    <option value="tvshows" @if(isset($slider) && $slider->slider_for == "tvshows"){{'selected'}}@endif>TV Shows</option>
                                    <option value="tvshowspak" @if(isset($slider) && $slider->slider_for == "tvshowspak"){{'selected'}}@endif>TV Shows Pak</option>
                                    <option value="kidchannels" @if(isset($slider) && $slider->slider_for == "kidchannels"){{'selected'}}@endif>Kids Channels</option>
                                    <option value="religiouschannels" @if(isset($slider) && $slider->slider_for == "religiouschannels"){{'selected'}}@endif>Religious Channels</option>
                                    <option value="sports" @if(isset($slider) && $slider->slider_for == "sports"){{'selected'}}@endif>Sports</option>
                                    <option value="stageshowspak" @if(isset($slider) && $slider->slider_for == "stageshowspak"){{'selected'}}@endif>Stage Shows</option>
                                    <option value="laughtershows" @if(isset($slider) && $slider->slider_for == "laughtershows"){{'selected'}}@endif>Laughter Shows</option>
                                </select>
                            </div>   
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
