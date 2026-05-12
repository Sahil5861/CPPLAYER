@extends('layout.default')
@section('mytitle', 'Admin List')
@section('page', 'Channels / Order List')

@section('content')
    <style type="text/css">
        .no-move .media {
            opacity: 0.8;
            background: #333;
        }
    </style>
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link href="{{ asset('theme/plugins/drag-and-drop/dragula/dragula.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/plugins/drag-and-drop/dragula/example.css') }}" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL STYLES -->
    <div class="layout-px-spacing">

        <div class="row" id="cancel-row">
            <div class="col-lg-12 layout-spacing layout-top-spacing">
                <form id="order-form" method="post" action="{{ route('saveChannelOrders') }}" enctype="multipart/form-data"
                    novalidate class="simple-example">
                    @csrf
                    <div class="statbox widget box box-shadow">
                        <div class="widget-header">
                            <div class="row">
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <h4>Set Channel Order</h4>
                                </div>

                            </div>
                            <div class="row">

                                <div class="col-lg-7">
                                    <div class="card">
                                        <div class="card-body">
                                            <h4 style="display:flex; align-items:center; gap:8px;">
                                                Filters

                                                <svg onclick="resetFilters()" xmlns="http://www.w3.org/2000/svg"
                                                    data-toggle="tooltip"
                                                    title="Reset Filters"
                                                    width="18" height="18" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" style="cursor:pointer;">
                                                    <polyline points="23 4 23 10 17 10"></polyline>
                                                    <polyline points="1 20 1 14 7 14"></polyline>
                                                    <path
                                                        d="M3.51 9a9 9 0 0114.13-3.36L23 10M1 14l5.36 4.36A9 9 0 0020.49 15">
                                                    </path>
                                                </svg>

                                            </h4>
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <select class="form-control select" id="stream_type"
                                                            name="stream_type">
                                                            <option value="=">--Filter By Stream Type--</option>
                                                            <option value="M3u8">M3u8</option>
                                                            <option value="YoutubeLive">YoutubeLive</option>
                                                            <option value="Custom">Custom</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <select class="form-control select" id="genre" name="genre">
                                                            <option value="=">--Filter By Genre--</option>
                                                            @foreach ($genres as $genre)
                                                                <option value="{{ $genre->title }}">{{ $genre->title }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <select class="form-control select" id="language" name="language">
                                                            <option value="=">--Filter By Language--</option>
                                                            @foreach ($languages as $language)
                                                                <option value="{{ $language->id }}">{{ $language->title }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <div class="card">
                                        <div class="card-body">
                                            <h4>Range</h4>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <input type="number" class="form-control" id="min_range"
                                                        name="min_range" placeholder="Min Range" min="1"
                                                        value="1">
                                                </div>
                                                <div class="col-lg-6">
                                                    <input type="number" class="form-control" id="max_range"
                                                        name="max_range" placeholder="Max Range" readonly
                                                        value="{{ $total }}" min="1">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="widget-content widget-content-area">

                            <div class='parent ex-1'>
                                <div class="row">
                                    <input type="hidden" id="lockedChannels" name="lockedChannels"
                                        value="{{ json_encode($lockedChannels) }}" />
                                    <input type="hidden" id="start_no" name="start_no" value="0" />
                                    <input type="hidden" id="checkOrder" name="checkOrder" value="default" />
                                    <input type="hidden" id="new_channel_no" name="new_channel_no" value="default" />
                                    <input type="hidden" id="old_channel_no" name="old_channel_no" value="0" />
                                    <div class="col-sm-12" class='dragula' id="_dragula"
                                        style="height: 400px;overflow-y: auto;border: 2px solid #828282">

                                        <div id="dragDropContainer">
                                            {{-- Drag and Drop List will be loaded here via AJAX --}}

                                            @include('admin.channel.partials.dragdrop_list', [
                                                'dataForLoop' => $dataForLoop,
                                            ])
                                        </div>
                                        {{-- <div id='left-defaults' class="row" >
                                        @foreach ($dataForLoop as $key => $dcl)
                                        @if ($dcl)
                                        <div class="col-sm-3 d-md-flex d-block @if ($dcl->position_locked == 1) no-move @endif @if ($dcl->position_locked == 0) move @endif" id="main_div_{{$key}}" style="padding: 5px;">
                                            <div class="media d-md-flex d-block text-sm-center text-center" style="padding: 5px;border: 1px solid; padding: 5px 10px;border-radius: 5px;width: 100%;">
                                                 <span id="ch_{{$key}}" class="c-index" style="position: absolute;left: 10px;top: 15px;">{{$dcl->channel_number}}</span>
                                               
                                                <div class="media-body">
                                                    <div class="d-xl-flex d-block justify-content-between" style="position: relative;">
                                                    &nbsp;
                                                        <div class="" style="width: 100%;margin-left: 20px;">
                                                            <h6 class="" style="margin-bottom: 3px">{{$dcl->channel_name}}</h6>
                                                            
                                                            <input type="hidden" name="numbers[]" value="{{$dcl->id}}">
                                                            <input type="hidden" id="position_locked_{{$key}}" name="position_locked[]" value="{{$dcl->position_locked}}">
                                                        </div>
                                                     
                                                        <span style="position: absolute;right: 0;top: 5px;">&nbsp; &nbsp;<span id="lock_{{$key}}">@if ($dcl->position_locked == 0)<svg onclick="lock('{{$key}}')" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-unlock"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 9.9-1"></path></svg>@endif @if ($dcl->position_locked == 1) <svg onclick="unlock('{{$key}}')" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-lock"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg> @endif</span></span>
                                                       
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>
                                        @else
                                        <div class="col-sm-3 d-md-flex d-block no-move" id="main_div_{{$key}}" style="padding: 5px;">
                                            <div class="media d-md-flex d-block text-sm-left text-center" style="padding: 5px;border: 1px solid; padding: 5px 10px;border-radius: 5px;width: 100%;">
                                                 <span id="ch_{{$key}}" class="c-index" style="position: relative;left: 0px;top: 0px; margin-right:8px; font-size:12px;">{{$key}}</span>
                                                
                                                <div class="media-body">
                                                    <div class="d-xl-flex d-block justify-content-between" style="position: relative;">
                                                        
                                                        <div class="" style="width: 100%;margin-left: 20px;">
                                                            <h6 class="" style="margin-bottom: 3px">&nbsp;</h6>
                                                            <p class="" style="margin:0;opacity: 0.6;line-height: 10px;"><small>&nbsp;</small></p>
                                                            <input type="hidden" name="numbers[]" value="0">
                                                            <input type="hidden" id="position_locked_{{$key}}" name="position_locked[]" value="0"> 
                                                        </div>
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>
                                        @endif
                                        @endforeach                                        
                                    </div> --}}
                                    </div>

                                </div>
                            </div>


                            <div class="col-xl-12 text-center mb-2 mt-4">
                                <!-- <button class="btn btn-primary submit-fn mt-2" type="submit">Update Order</button> -->
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
    <!-- <script src="https://unpkg.com/dom-autoscroller@2.2.3/dist/dom-autoscroller.js"></script> -->
    <script src="{{ asset('theme/plugins/drag-and-drop/dragula/autoscroller.js') }}"></script>
    <script src="{{ asset('theme/plugins/drag-and-drop/dragula/custom-dragula.js') }}"></script>


    <script>
        let totalChannels = {{ $total }};

        // ✅ Load data on filter change
        function loadFilteredChannels() {

            console.log('I am calling !');
            let data = {
                stream_type: $('#stream_type').val(),
                genre: $('#genre').val(),
                language: $('#language').val(),
            };

            $('#dragDropContainer').html('Loading...');

            $.ajax({
                url: "{{ route('getChannelOrderList') }}",
                type: "GET",
                data: data,
                success: function(res) {

                    if(res.total > 0){

                        // 🔄 Update UI
                        $('#dragDropContainer').html(res.html);
    
                        // 🎯 Set range
                        totalChannels = res.total;
    
                        $('#min_range').val(1);
                        $('#max_range').val(totalChannels);
    
                        initDragula();
                    }
                    else{
                        $('#dragDropContainer').html('<div class="alert alert-info mt-2">No channels found for the selected filters.</div>');
                    }
                }
            });
        }


        // $(document).ready(function() {
        //     loadFilteredChannels();
        // });



        function resetFilters(){
            $('#stream_type').val('=').trigger('change');
            $('#genre').val('=').trigger('change');
            $('#language').val('=').trigger('change');

            loadFilteredChannels();
        }

        $('#min_range').on('input', function() {

            let min = parseInt($(this).val()) || 0;

            // if (min < 1) {
            //     min = 1;
            //     $(this).val(1);
            // }

            let newMax = totalChannels + min;

            $('#max_range').val(newMax);
        });

        // $('#stream_type, #genre', '#language').on('change', function() {
        //     alert('hii');
        //     loadFilteredChannels();
        // });

        $('#stream_type, #genre, #language').on('change', function() {
            loadFilteredChannels();
        });

        function initDragula() {
            dragula([document.getElementById('left-defaults')], {
                moves: function(el) {
                    return !el.classList.contains('no-move');
                }
            });
        }
    </script>
    <script type="text/javascript">
        function lock(id) {
            console.log(id)
            document.getElementById("start_no").value = 0;
            $('#position_locked_' + id).val(1)
            $('#main_div_' + id).addClass('no-move')
            $('#lock_' + id).html('<svg onclick="unlock(' + id +
                ')" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-lock"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>'
            )
            document.getElementById("order-form").submit();
        }

        function unlock(id) {
            console.log(id)
            document.getElementById("start_no").value = 0;
            $('#position_locked_' + id).val(0)
            $('#main_div_' + id).removeClass('no-move')
            $('#lock_' + id).html('<svg onclick="lock(' + id +
                ')" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-unlock"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 9.9-1"></path></svg>'
            )
            document.getElementById("order-form").submit();
        }
    </script>

    <!-- END PAGE LEVEL SCRIPTS -->
@endsection
