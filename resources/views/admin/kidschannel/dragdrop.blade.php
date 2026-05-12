@extends('layout.default')
@section('mytitle', 'Admin List')
@section('page', 'Kids Channels  /  Order List')

@section('content')
<style type="text/css">
    .no-move .media{
        opacity: 0.8;
        background: #333;
    }
</style>
<!-- BEGIN PAGE LEVEL STYLES -->
    <link href="{{asset('theme/plugins/drag-and-drop/dragula/dragula.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('theme/plugins/drag-and-drop/dragula/example.css')}}" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL STYLES -->    

<div class="layout-px-spacing">
    <div class="row" id="cancel-row">
        <div class="col-lg-12 layout-spacing layout-top-spacing">
            <form id="order-form" method="post" action="{{ route('saveKidsChannelOrder') }}" enctype="multipart/form-data" novalidate class="simple-example">
                @csrf
                <div class="statbox widget box box-shadow">
                    <div class="widget-header">
                        <div class="row">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <h4>Set kids Show Order</h4>
                            </div>           
                        </div>
                    </div>
                    <div class="widget-content widget-content-area">
                        <div class='parent ex-1'>
                            <div class="row">
                                <div class="col-sm-12" id="_dragula" style="height: 400px; overflow-y: auto; border: 2px solid #828282">
                                    <div id='left-defaults' class="row">

                                        @foreach($dataForLoop as $key => $dcl)
                                        <div class="col-sm-3 d-md-flex d-block" id="main_div_{{$key}}" style="padding: 5px;">
                                            <div class="media d-md-flex d-block text-sm-left text-center" style="padding: 5px; border: 1px solid; padding: 5px 10px; border-radius: 5px; width: 100%;">
                                                <span id="ch_{{$key}}" class="c-index" style="position: relative;left: 0px;top: 0px; margin-right:8px; font-size:12px;">{{$dcl->order}}</span>
                                                {{-- <img alt="avatar" src="{{ $dcl->banner }}" class="img-fluid" style="width: 60px; margin-right: 5px;"> --}}
                                                <div class="media-body">
                                                    <div class="d-xl-flex d-block justify-content-between" style="position: relative;">
                                                        <div style="width: 100%; margin-left: 15px;">
                                                            <h6 style="margin-bottom: 3px; font-size: 12px">{{ $dcl->name }}</h6>
                                                            <input type="hidden" name="ids[]" value="{{$dcl->id}}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-12 text-center mb-2 mt-4">
                            <!-- Optional submit button if needed -->
                        </div>
                    </div>
                </div>  
            </form>
        </div>
    </div>
</div>
@endsection

@section('footer')
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="{{ asset('theme/plugins/drag-and-drop/dragula/dragula.min.js') }}"></script>
<script src="{{ asset('theme/plugins/drag-and-drop/dragula/autoscroller.js') }}"></script>
<script src="{{ asset('theme/plugins/drag-and-drop/dragula/custom-dragula-movie.js') }}"></script>
<!-- END PAGE LEVEL SCRIPTS -->
@endsection