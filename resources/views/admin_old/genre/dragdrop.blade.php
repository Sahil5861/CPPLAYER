@extends('layout.default')
@section('mytitle', 'Admin List')
@section('page', 'Channels  /  Order List')

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
            <form id="order-form"  method="post" action="{{route('saveGenreOrders')}}" enctype="multipart/form-data" novalidate class="simple-example" >
                @csrf
                <div class="statbox widget box box-shadow">
                    <div class="widget-header">
                        <div class="row">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <h4>Set Genre Order</h4>
                            </div>           
                        </div>
                    </div>
                    <div class="widget-content widget-content-area">

                        <div class='parent ex-1'>
                            <div class="row">
                                <input type="hidden" id="start_no" name="start_no" value="0" />
                                <input type="hidden" id="checkOrder" name="checkOrder" value="default" />
                                <input type="hidden" id="new_channel_no" name="new_channel_no" value="default" />
                                <input type="hidden" id="old_channel_no" name="old_channel_no" value="0" />
                                <div class="col-sm-12" class='dragula' id="_dragula2" style="height: 400px;overflow-y: auto;border: 2px solid #828282">
                                    <div id='left-defaults-1' class="row" >

                                        @foreach($genres as $key => $dcl)
                                        <div class="col-sm-3 d-md-flex d-block move" id="main_div_{{$key}}" style="padding: 5px;">
                                            <div class="media d-md-flex d-block text-sm-left text-center" style="padding: 5px;border: 1px solid; padding: 5px 10px;border-radius: 5px;width: 100%;">
                                                 <span id="ch_{{$key}}" class="c-index" style="position: absolute;right: 10px;top: 15px;">{{$dcl->index}}</span>
                                                <img alt="avatar" src="{{asset($dcl->image)}}" class="img-fluid " style="width: 60px;margin-right: 5px;">
                                                <div class="media-body">
                                                    <div class="d-xl-flex d-block justify-content-between" style="position: relative;">
                                                    &nbsp;
                                                        <div class="" style="width: 100%;margin-left: 20px;">
                                                            <h6 class="" style="margin-bottom: 3px">{{$dcl->title}}</h6>
                                                            <input type="hidden" name="numbers[]" value="{{$dcl->id}}">
                                                            
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
                            <button class="btn btn-primary submit-fn mt-2" type="submit">Update Order</button>
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
<script src="{{asset('theme/plugins/drag-and-drop/dragula/dragula.min.js')}}"></script>
<!-- <script src="https://unpkg.com/dom-autoscroller@2.2.3/dist/dom-autoscroller.js"></script> -->
<script src="{{asset('theme/plugins/drag-and-drop/dragula/autoscroller.js')}}"></script>
<script src="{{asset('theme/plugins/drag-and-drop/dragula/custom-dragula.js')}}"></script>
<script type="text/javascript">
    // function lock(id){
    //     console.log(id)
    //     document.getElementById("start_no").value = 0;
    //     $('#position_locked_'+id).val(1)
    //     $('#main_div_'+id).addClass('no-move')
    //     $('#lock_'+id).html('<svg onclick="unlock('+id+')" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-lock"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>')
    //     document.getElementById("order-form").submit();
    // }
    // function unlock(id){
    //     console.log(id)
    //     document.getElementById("start_no").value = 0;
    //     $('#position_locked_'+id).val(0)
    //     $('#main_div_'+id).removeClass('no-move')
    //     $('#lock_'+id).html('<svg onclick="lock('+id+')" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-unlock"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 9.9-1"></path></svg>')
    //     document.getElementById("order-form").submit();
    // }
</script>

<!-- END PAGE LEVEL SCRIPTS -->
@endsection
