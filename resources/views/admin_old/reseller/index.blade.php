@extends('layout.default')
@section('mytitle', 'Reseller List')
@section('page', 'Resellers / List')


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
                            <p class=""><small>Total Resellers</small></p>
                            <h6 class="value" id="totalRecords">--</h6>
                            <!-- <p class=""><small>Total Users</small></p> -->
                        </div>
                        <div class="">
                            <div class="w-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
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
                            <p class=""><small>Active Resellers</small></p>
                            <h6 class="value" id="activeRecords">--</h6>
                            <!-- <p class=""><small>Total Users</small></p> -->
                        </div>
                        <div class="">
                            <div class="w-icon" style="background-color: #8dbf42;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user-plus"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
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
                            <p class=""><small>Inactive Resellers</small></p>
                            <h6 class="value" id="inactiveRecords">--</h6>
                            <!-- <p class=""><small>Total Users</small></p> -->
                        </div>
                        <div class="">
                            <div class="w-icon" style="background-color:#e2a03f">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user-minus"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="23" y1="11" x2="17" y2="11"></line></svg>
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
                            <p class=""><small>Deleted Resellers</small></p>
                            <h6 class="value" id="deletedRecords">--</h6>
                            <!-- <p class=""><small>Total Users</small></p> -->
                        </div>
                        <div class="">
                            <div class="w-icon" style="background-color: #e7515a">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user-x"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="18" y1="8" x2="23" y2="13"></line><line x1="23" y1="8" x2="18" y2="13"></line></svg>
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
                @if(\Auth::user()->role!=1 && !Session::has('sadmin_id'))
                <div class="text-right">
                    <a href="{{url('add-reseller')}}" class="btn btn-primary mb-2">Add +</a>
                </div>
                @endif
                @if(\Auth::user()->role==1)
                <div class="text-right">
                    <select name="" class="form-control filter" id="filter">
                        <option value="">Filter By Admin</option>
                        @foreach($admins as $admin)
                        <option value="{{$admin->id}}">{{$admin->name}}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="table-responsive mb-4 mt-4">

                    <table id="multi-column-ordering" class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Wallet Amount</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <!-- <th>Address</th>
                                <th>City</th>
                                <th>Country</th>
                                <th>Company_name</th> -->
                                <th>Created Date</th>
                                <th>Status</th>
                                <th>Action</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="tableItem">

                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Name</th>
                                <th>Wallet Amount</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <!-- <th>Address</th>
                                <th>City</th>
                                <th>Country</th>
                                <th>Company_name</th> -->
                                <th>Created Date</th>
                                <th>Status</th>
                                <th>Action</th>
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
                "url": "{{route('getUserList')}}",
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

  <script type="text/javascript">
    $(document).ready(function(){

      // DataTable
        $('#multi-column-ordering').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{route('getResellerList')}}",
            columns: [
                { data: 'name' },
                { data: 'wallet_amount' },
                { data: 'email' },
                { data: 'mobile' },
                // { data: 'address' },
                // { data: 'city' },
                // { data: 'country' },
                // { data: 'company_name' },
                { data: 'created_at' },
                { data: 'status' },
                { data: 'action', orderable: false, searchable: false },
                {data: 'signIn'},
            ],
            drawCallback: function (settings) {

                var response = settings.json;
                $('#totalRecords').text(response.totalRecords);
                $('#activeRecords').text(response.activeRecords);
                $('#inactiveRecords').text(response.inactiveRecords);
                $('#deletedRecords').text(response.deletedRecords);
                console.log(response);
                updateIcon()

            },
        });

        $('#filter').change(function(){
            var table = $('#multi-column-ordering').DataTable();
            table.destroy();
            var admin = $(this).val();
            if(admin == '' || admin == null || admin == undefined){
                location.reload();
                return;
            }
            $('#multi-column-ordering').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    'type': 'POST',
                    'url': "{{route('getResellerListByAdmin')}}",
                    'data': {
                        "admin_id": admin, "_token": "{{ csrf_token() }}"
                    }
                },
                columns: [
                    { data: 'name' },
                    { data: 'email' },
                    { data: 'mobile' },
                    // { data: 'address' },
                    // { data: 'city' },
                    // { data: 'country' },
                    // { data: 'company_name' },
                    { data: 'created_at' },
                    { data: 'status' },
                    { data: 'action', orderable: false, searchable: false },
                    {data: 'signIn'},
                ],
                drawCallback: function (settings) {

                    var response = settings.json;
                    $('#totalRecords').text(response.totalRecords);
                    $('#activeRecords').text(response.activeRecords);
                    $('#inactiveRecords').text(response.inactiveRecords);
                    $('#deletedRecords').text(response.deletedRecords);
                    updateIcon()

                },
            });
        });

    });
    </script>

<!-- footer script if required -->
@endsection
