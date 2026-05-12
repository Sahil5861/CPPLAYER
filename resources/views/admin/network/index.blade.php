@extends('layout.default')
@section('mytitle', 'Admin List')
@section('page', 'Channels  /  List')

@section('content')
<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
            <div class="widget widget-card-four">
                <div class="widget-content">
                    <div class="w-content">
                        <div class="w-info">
                            <p class=""><small>Total Networks</small></p>
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
                            <p class=""><small>Active Networks</small></p>
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
                            <p class=""><small>Inactive Networks</small></p>
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
                            <p class=""><small>Deleted Networks</small></p>
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
                
                <div class="text-right">
                    <a href="{{url('content-network-order')}}" class="btn btn-primary mb-2">Order Networks</a>
                    <button type="button" class="btn btn-primary mb-2" data-toggle="modal" data-target="#addContentModal">
                        Add +
                    </button>
                </div>

                <?php 
                
                    $network_count = \App\Models\ContentNetwork::whereNull('deleted_at')->count();
                    $formatted = $network_count + 1;
                ?>
                {{-- add modal --}}
                <div class="modal fade" id="addContentModal" tabindex="-1" role="dialog" aria-labelledby="addContentModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                        
                        <div class="modal-header">
                            <h5 class="modal-title" id="addContentModalLabel">Add Content Network</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        
                        <div class="modal-body">
                            {{-- Your form goes here --}}
                            <form id="addContentForm" method="POST" action="{{route('savecontentnetwork')}}">
                            @csrf
                            <div class="form-group">
                                <label for="networkName">Network Name</label>
                                <input type="text" class="form-control" name="networkName" id="networkName" required>
                            </div>

                            <div class="form-group">
                                <label for="networkLogo">Network Logo</label>
                                <input type="url" class="form-control" name="networkLogo" id="networkLogo" required>
                            </div>

                            <div class="form-group">
                                <label for="networOrder">Network Order</label>
                                <input type="number" class="form-control" name="networOrder" id="networOrder" value="{{$formatted}}" required>
                            </div>
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control select">
                                    <option value="1">Active</option>
                                    <option value="0">De-Active</option>
                                </select>
                            </div>


                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" 
                                        name="is_content" 
                                        id="is_content" 
                                        class="form-check-input" 
                                        value="1">
                                    <label class="form-check-label" for="is_content" style="user-select: none">
                                        Is Content
                                    </label>
                                </div>
                            </div>




                            <!-- Add more fields here -->
                            </form>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="submit" form="addContentForm" class="btn btn-success">Save</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                        
                        </div>
                    </div>
                </div>


                {{-- edit modal --}}
                <div class="modal fade" id="editContentModal" tabindex="-1" role="dialog" aria-labelledby="editContentModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                        
                        <div class="modal-header">
                            <h5 class="modal-title" id="editContentModalLabel">Edit Content Network</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        
                        <div class="modal-body">
                            {{-- Your form goes here --}}
                            <form id="editContentForm" method="POST" action="{{route('savecontentnetwork')}}">
                            @csrf

                            <input type="hidden" name="id" id="networkId">
                            <div class="form-group">
                                <label for="networkName">Network Name</label>
                                <input type="text" class="form-control" name="networkName" id="networkName1" required>
                            </div>

                            <div class="form-group">
                                <label for="networkLogo">Network Logo</label>
                                <input type="url" class="form-control" name="networkLogo" id="networkLogo1" required>
                            </div>

                            <div class="form-group">
                                <label for="networOrder">Network Order</label>
                                <input type="number" class="form-control" name="networOrder" id="networOrder1" required>
                            </div>


                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" 
                                        name="is_content" 
                                        id="is_content1" 
                                        class="form-check-input" 
                                        value="1">
                                    <label class="form-check-label" for="is_content1" style="user-select: none">
                                        Is Content
                                    </label>
                                </div>
                            </div>
                            {{-- <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control select">
                                    <option value="1">Active</option>
                                    <option value="0">De-Active</option>
                                </select>
                            </div> --}}


                            <!-- Add more fields here -->
                            </form>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="submit" form="editContentForm" class="btn btn-success">Save</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                        
                        </div>
                    </div>
                </div>

                <div class="table-responsive mb-4 mt-4">
                    
                    <table id="multi-column-ordering" class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>                                                                                                                         
                                <th>Logo</th>                                                                                                                         
                                <th>Status</th>
                                <th>Network Order</th>
                                <th>Created Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tableItem">
                            
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Name</th>  
                                <th>Logo</th>                                                                                                                                                                                                                                                              
                                <th>Status</th>
                                <th>Network Order</th>
                                <th>Created Date</th>
                                <th>Action</th>
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


<script>
    function editNetwork(id){        
        $.ajax({
            type: "GET",
            url: "{{route('editNetwork')}}",
            data: {id:id},
            success: function(response){  
                let data = response; // use response directly
                console.log(response.network);
                $('#networkId').val(data.network.id);
                $('#networkName1').val(data.network.name);              
                $('#networkLogo1').val(data.network.logo);              
                $('#networOrder1').val(data.network.networks_order);              
                $('#is_content1').prop('checked', data.network.is_content == 1 ? true : false);
                $('#editContentModal').modal('show');
            }
        })
    }
</script>
<script type="text/javascript">
    $(document).ready(function(){        
      // DataTable
      $('#multi-column-ordering').DataTable({
         processing: true,
         serverSide: true,
         order: [[0, 'desc']],
         ajax: "{{route('getContentnetworkList')}}",
         columns: [
            { data: 'name' },                        
            { data: 'logo', orderable: false, searchable: false  },                        
            { data: 'status', orderable: false, searchable: false  },
            { data: 'networks_order'},
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
    </script>

   
<!-- footer script if required -->
@endsection