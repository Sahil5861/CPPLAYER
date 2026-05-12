@extends('layout.default')
@section('mytitle', 'Manage Genre')
@if(isset($genre))
@section('page', 'Genre / Update')
@endif
@if(!isset($genre))
@section('page', 'Genre / Add')
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
                    <form id="user-form"  method="post" action="{{route('saveGenre')}}" enctype="multipart/form-data" novalidate class="simple-example" >
                        @csrf
                        <input type="hidden" name="id" value="@if(isset($genre)){{$genre->id}}@endif">
                        <div class="form-row">
                            <div class="col-md-6 mb-4">
                                <label for="fullName">Title</label>
                                <input type="text" class="form-control" id="title" name="title" placeholder="Title" value="{{old('title')}}@if(isset($genre)){{$genre->title}}@endif" required>
                                <div class="invalid-feedback">
                                    @error('title') {{ $message }} @enderror
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="description">Description</label>
                                <textarea type="text" class="form-control" id="description" name="description" placeholder="Description">{{old('description')}}@if(isset($genre)){{$genre->description}}@endif</textarea>
                                <div class="invalid-feedback">
                                    @error('description') {{ $message }} @enderror
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="fullName">Icon Image</label>
                                {{-- <input type="file" class="form-control" id="image" name="image" value="{{old('image')}}@if(isset($genre)){{$genre->image}}@endif" required> --}}
                                <input type="text" class="form-control" id="image" name="image" value="{{old('image')}}@if(isset($genre)){{$genre->image}}@endif">
                                @if(isset($genre) && $genre->image != '')
                                    <img style="position: absolute;margin-top: 5px;" src="{{$genre->image}}" width="30px">
                                @endif
                                <div class="invalid-feedback">
                                    @error('image') {{ $message }} @enderror
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="fullName">Background Image</label>
                                {{-- <input type="file" class="form-control" id="genre_bg" name="genre_bg" value="{{old('genre_bg')}}@if(isset($genre)){{$genre->genre_bg}}@endif" required> --}}
                                <input type="text" class="form-control" id="genre_bg" name="genre_bg" value="{{old('genre_bg')}}@if(isset($genre)){{$genre->genre_bg}}@endif">
                                @if(isset($genre) && $genre->genre_bg != '')
                                    <img style="position: absolute;margin-top: 5px;" src="{{$genre->genre_bg}}" width="30px">
                                @endif
                                <div class="invalid-feedback">
                                    @error('genre_bg') {{ $message }} @enderror
                                </div>
                            </div>



                            <div class="col-md-6 mb-4">
                                <label for="fullName">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="1" @if(isset($genre) && $genre->status == 1){{'selected'}}@endif>Active</option>
                                    <option value="0" @if(isset($genre) && $genre->status == 0){{'selected'}}@endif>De-Active</option>
                                </select>
                                <div class="invalid-feedback">
                                    @error('status') {{ $message }} @enderror
                                </div>
                                <!-- <div class="valid-feedback">
                                    Looks good!
                                </div> -->
                            </div>
                        </div>
                        @if(isset($genre))
                        <button class="btn btn-primary submit-fn mt-4" type="submit">Update</button>
                        @else
                        <button class="btn btn-primary submit-fn mt-4" type="submit">Add</button>
                        @endif

                    </form>
                <!-- </div> -->
            </div>
        </div>
    </div>

</div>
@endsection

@section('footer')

<!-- footer script if required -->
@endsection
