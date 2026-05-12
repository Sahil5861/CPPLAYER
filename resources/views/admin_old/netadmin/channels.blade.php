@extends('layout.default')
@section('mytitle', 'Net Admin List')
@section('page', 'Net Admins / List')

@section('content')

<style>
    .filter{
        display: inline-block;
        width: 30%;
    }
</style>

<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
            <div class="widget widget-card-four">
                <div class="widget-content">
                    <div class="w-content">
                        <div class="w-info">
                            <p class=""><small>Total Channel</small></p>
                            <h6 class="value" id="current_amount">{{$total_channel[0]->total_channel}}</h6>
                        </div>
                        <div class="">
                            <div class="w-icon" style="background-color: #8dbf42;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-airplay"><path d="M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1"></path><polygon points="12 15 17 21 7 21 12 15"></polygon></svg>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
            <div class="widget widget-card-four">
                <div class="widget-content">
                    <div class="w-content">
                        <div class="w-info">
                            <p class=""><small>Total Active Channel</small></p>
                            <h6 class="value" id="current_amount">{{ $total_active_channel[0]->total_active_channel }} </h6>
                            <!-- <p class=""><small>Total Wallet</small></p> -->
                        </div>
                        <div class="">
                            <div class="w-icon" style="background-color: #8dbf42;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-airplay"><path d="M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1"></path><polygon points="12 15 17 21 7 21 12 15"></polygon></svg>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
            <div class="widget widget-card-four">
                <div class="widget-content">
                    <div class="w-content">
                        <div class="w-info">
                            <p class=""><small>Total Inactive Channel</small></p>
                            <h6 class="value" id="current_amount">{{ $total_inactive_channel[0]->total_inactive_channel }} </h6>
                            <!-- <p class=""><small>Total Wallet</small></p> -->
                        </div>
                        <div class="">
                            <div class="w-icon" style="background-color: #8dbf42;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-airplay"><path d="M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1"></path><polygon points="12 15 17 21 7 21 12 15"></polygon></svg>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
            <div class="widget widget-card-four">
                <div class="widget-content">
                    <div class="w-content">
                        <div class="w-info">
                            <p class=""><small>Channel with empty link</small></p>
                            <h6 class="value" id="current_amount">{{ $empty_link_channel[0]->empty_link_channel }} </h6>
                            <!-- <p class=""><small>Total Wallet</small></p> -->
                        </div>
                        <div class="">
                            <div class="w-icon" style="background-color: #8dbf42;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-airplay"><path d="M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1"></path><polygon points="12 15 17 21 7 21 12 15"></polygon></svg>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">

            <div class="widget-content widget-content-area br-6">

                <div id="delete_bd_ms"></div>
                @if(session()->has('message'))
                    <div class="alert alert-success alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>{{ session()->get('message') }}</strong>
                    </div>
                @endif
                <div class="text-right">
                    <select name="" class="form-control filter" id="filter">
                        <option value="">Channel Filter</option>
                        <option value="all">All Channel</option>
                        <option value="active">Active</option>
                        <option value="inactive">InActive</option>
                        <option value="empty">Empty Link</option>
                    </select>
                </div>
                <div class="table-responsive mb-4 mt-4">

                    <table id="multi-column-ordering" class="table table-hover">
                        <thead>
                            <tr class="text-center">
                                <th class="text-left">Name</th>
                                <th>Link</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="tableItem">
                            @foreach($channels as $item)
                            @if($item->status == 1)
                               @php $status = '<a onchange="updateStatus1(\''.url('netadmin/update-channel-status',base64_encode($item->id)).'\')" href="javascript:void(0);"><label class="switch s-primary mr-2"><input type="checkbox" value="1" checked id="accountSwitch{{$item->id}}"><span class="slider round"></span></label> </a>'; @endphp
                            @else
                               @php $status = '<a onchange="updateStatus1(\''.url('netadmin/update-channel-status',base64_encode($item->id)).'\')" href="javascript:void(0);"><label class="switch s-primary   mr-2"><input type="checkbox" value="0" id="accountSwitch{{$item->id}}"><span class="slider round"></span></label></a>'; @endphp
                            @endif
                                <tr class="text-center">
                                    <td class="text-left">{{$item->channel_name}}</td>
                                    <td><input type="text" class="form-control" id="link-{{$item->id}}" value="{{$item->link}}"></td>
                                    <td>{!! $status !!}</td>
                                    <td><button class="btn btn-primary" onclick="updateLink({{$item->id}})">Update</button></td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Name</th>
                                <th>Link</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@section('footer')
  <script type="text/javascript">
    $(document).ready(function(){

      // DataTable
      $('#multi-column-ordering').DataTable({

      });

      $('#filter').change(function(){
        var value = $(this).val();
        var url = new URL(window.location.href).origin +"/channel-filter/"+value;
        window.location.href =url;
      })

    });

    function updateStatus1(url){
        window.location.href =url;
    }

    function updateLink(id){
        var linkValue = $('#link-'+id).val();
        $.ajax({
            url: "{{ url('update-channel-link') }}",
            method: 'post',
            data: {
                'id': id,
                'link': linkValue,
                "_token": "{{ csrf_token() }}"
            },
            success: function(result){
                location.reload();
            }
        });
    }

    </script>

@endsection
