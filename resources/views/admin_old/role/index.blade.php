@extends('layout.default')
@section('mytitle', 'Manage Roles')
@section('page', 'Roles / List')
@section('content')
<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
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
                    <a href="{{url('add-role')}}" class="btn btn-primary mb-2">Add +</a>
                </div>
                <div class="table-responsive mb-4 mt-4">
                    <table id="multi-column-ordering" class="table table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>Role Id</th>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Created Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tableItem">
                            
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Role Id</th>
                                <th>Title</th>
                                <th>Status</th>
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

<script>
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
                "url": "{{route('getRoleList')}}",
                // "type": "POST",
                "data": function ( d ) {
                    console.log(d);
                            // d.parent_cat = $("#parent_cat").val();
                            // d.sub_cat = $("#sub_cat").val();
                            // d.sub_subcat = $("#sub_subcat").val();
                            // d.search_date = $("#publish").val();

                            // alert(d.parent_cat);
                            // alert(d.sub_cat);
                            // alert(d.sub_subcat);
                }
            },

            columns: [
                {data: 'id', name: 'id'},
                // {data: 'email', "render": function (data, type, row) {
                //  return '<a href="/user/' + row.id +'">' + data + '</a>';
                //     },
                // },
                {data: 'title', name: 'title'},
                {data: 'status', name: 'status'},
                {data: 'created_at', name: 'created_at'},
                
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            drawCallback: function (settings) { 
            
                
                updateIcon()
            },
     
            //Set column definition initialisation properties.
            // "columnDefs": [
            //     { 
            //         "className": "select-checkboxes",
            //         "targets": [ 0 ], //first column / numbering column
            //         "orderable": false //set not orderable
                    
            //     },
            // ],
            // "columnDefs": [{
            //     "defaultContent": "-",
            //     "targets": "_all"
            //   }],
            // "aoColumnDefs": [ { "bSortable": false, "aTargets": [ 0,1,2,3] } ]
     
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

        // $('#multi-column-ordering').DataTable({
        //     "oLanguage": {
        //         "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
        //         "sInfo": "Showing page _PAGE_ of _PAGES_",
        //         "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
        //         "sSearchPlaceholder": "Search...",
        //        "sLengthMenu": "Results :  _MENU_",
        //     },
        //     "stripeClasses": [],
        //     "lengthMenu": [7, 10, 20, 50],
        //     "pageLength": 7,
        //     columnDefs: [ {
        //         targets: [ 0 ],
        //         orderData: [ 0, 1 ]
        //     }, {
        //         targets: [ 1 ],
        //         orderData: [ 1, 0 ]
        //     }, {
        //         targets: [ 4 ],
        //         orderData: [ 4, 0 ]
        //     } ]
        // });

        
    </script>
<!-- footer script if required -->
@endsection