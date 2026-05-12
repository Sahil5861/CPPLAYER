@extends('layout.default')
@section('mytitle', 'Admin List')
@section('page', 'Show Pak  /  List')

@section('content')
<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
            <div class="widget widget-card-four">
                <div class="widget-content">
                    <div class="w-content">
                        <div class="w-info">
                            <p class=""><small>Total Show Pak</small></p>
                            <h6 class="value" id="totalRecords">--</h6>
                            <!-- <p class=""><small>Total Channels</small></p> -->
                        </div>
                        <div class="">
                            <div class="w-icon">
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
                            <p class=""><small>Active Show Pak</small></p>
                            <h6 class="value" id="activeRecords">--</h6>
                            <!-- <p class=""><small>Total Channels</small></p> -->
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
                            <p class=""><small>Inactive Show Pak</small></p>
                            <h6 class="value" id="inactiveRecords">--</h6>
                            <!-- <p class=""><small>Total Channels</small></p> -->
                        </div>
                        <div class="">
                            <div class="w-icon" style="background-color:#e2a03f">
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
                            <p class=""><small>Deleted Show Pak</small></p>
                            <h6 class="value" id="deletedRecords">--</h6>
                            <!-- <p class=""><small>Total Channels</small></p> -->
                        </div>
                        <div class="">
                            <div class="w-icon" style="background-color: #e7515a">
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

                <?php 
                    $channel_id = $id;
                    $channel = \App\Models\TvChannelPak::where('id', $channel_id)->first();
                    $show = \App\Models\TvShowPak::where('id', $channel->id)->first();
                ?>
                
                <div class="text-left">
                    <p>
                        <a href="{{route('admin.tvchannelpak')}}">{{strtoupper('TV Channels Pak')}}</a>&nbsp; &gt;                        
                        <a href="{{route('admin.tvshowpak', base64_encode($channel_id))}}">{{strtoupper($channel->name)}}</a>&nbsp; &gt;                        
                    </p>
                </div>

                <div class="row d-flex justify-content-start align-items-center">                    
                    <div class="col-md-2">
                        <select name="select_status" id="select_status" class="form-control w-25 select" style="width: 25%;">
                            <option value="">--Filter by Status--</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                
                <div class="text-right">
                    <a href="{{url('tv-show-pak-order',base64_encode($id))}}" class="btn btn-primary mb-2">Order Show Pak</a>
                    <a href="{{route('addTvShowpak', base64_encode($id))}}" class="btn btn-primary mb-2">Add +</a>                    
                </div>
                <div class="table-responsive mb-4 mt-4">
                    
                    <table id="multi-column-ordering" class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>                                                                                                                         
                                <th>Thumbnail</th>   
                                <th>Description</th>                                                                                                                             
                                <th>Status</th>                                                                                                                             
                                <th>Created Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tableItem">
                            
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Name</th>                                                                                                                                
                                <th>Thumbnail</th>   
                                <th>Status</th>                                                                                                                                                                                          
                                <th>Description</th>                                                                
                                <th>Created Date</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="delete_modal">
        <div class="modal-dialog action-modal">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="d_title"></h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <p id="delete_user_deletemsg">Do you want to delete this <span id="d_body"></span>?</p>
            </div>
            <input type="hidden" name="id" id="d_id" value="">
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-primary" data-dismiss="modal">No</button>
              <button type="button" class="btn btn-danger" onclick="delete_row(this);" >Yes, Delete</button>
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

</div>

@endsection

@section('footer')
<!-- <script>
    $(document).ready(function() { 
      var table = $('#multi-column-ordering').DataTable({ 
            // "aLengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            "processing": true, //Feature control the processing indicator.
            "serverSide": true, //Feature control DataTables' server-side processing mode.
            "order": [], //Initial no order.
             "language": {
                "infoFiltered": '' 
            },
     
            // Load data for the table's content from an Ajax source
            "ajax": { 
                "url": "{{route('getAdminList')}}",
                // "type": "POST",
                "data": function ( d ) {
                    console.log(d);
                    // d.parent_cat = $("#parent_cat").val();
                            
                }
            },

            columns: [
                {data: 'name', name: 'name'},
                {data: 'email', name: 'email'},
                {data: 'mobile', name: 'mobile'},
                {data: 'address', name: 'address'},
                {data: 'city', name: 'city'},
                {data: 'country', name: 'country'},
                {data: 'company_name', name: 'company_name'},
                {data: 'status', name: 'status'},
                {data: 'created_at', name: 'created_at'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
          
        });

      $("input#search").on("keyup", function (event) {
            if ($('#search').val().length >= 3 || $('#search').val().length == 0) {
                table.draw(), event.preventDefault()
            }
        });
        $("#btn-search").click(function (a) {
            table.draw(), a.preventDefault()
        });

    });
    </script> -->

    <script src="cdn.datatables.net/plug-ins/1.12.1/sorting/date-uk.js"></script>

  <script type="text/javascript">
    $(document).ready(function(){        
      // DataTable
      $('#multi-column-ordering').DataTable({
         processing: true,
         serverSide: true,
         order: [[0, 'asc']],
        //  ajax: "{{route('getTvShowPakList', $id)}}",
        ajax: {
            url: "{{route('getTvShowPakList', $id)}}",
            data: function(d) {
                let status = $('#select_status').val();
                let network_id = $('#select_netword_id').val();

                if (status !== '') {
                    d.status = status;
                }
            },
        },
         columns: [
            { data: 'name' },                                                          
            { data: 'image',orderable: false, searchable: false },                                                
            { data: 'desc' },
            { data: 'status' },
            { data: 'created_at' },
            { data: 'action', orderable: false, searchable: false },
         ],
         drawCallback: function (settings) { 
            
            var response = settings.json;
            $('#totalRecords').text(response.totalRecords);
            $('#activeRecords').text(response.activeRecords);
            $('#inactiveRecords').text(response.inactiveRecords);
            $('#deletedRecords').text(response.deletedRecords);
            console.log(response);
            $('[data-toggle="tooltip"]').tooltip();
            updateIcon()
        },
      });
    });

    $('#select_status').on('change', function() {                    
        $('#multi-column-ordering').DataTable().ajax.reload(null, false);
    });

    function deleteRowModal(id){ 
        $('#d_title').text('TV Show Pak')
        $('#d_id').val(id);
        $('#delete_modal').modal('show');        
    }

    function delete_row(){
        var id = $('#d_id').val();
        $.ajax({
            type: 'POST',
            url: "{{route('tvshowpak.destroy')}}",
            data: {
                _token: '{{ csrf_token() }}',
                id:id
            },
            success: function(data){
                $('#delete_modal').modal('hide');
                $('#multi-column-ordering').DataTable().ajax.reload();
            }
        })
    }
    </script>

   
<!-- footer script if required -->
@endsection