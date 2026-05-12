@extends('layout.default')
@section('mytitle', 'Wallet List')
@section('page', 'Wallet  /  List')

@section('content')
<div class="layout-px-spacing">
    <div class="row layout-top-spacing">        
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
            <div class="widget widget-card-four">
                <div class="widget-content">
                    <div class="w-content">
                        <div class="w-info">
                            {{-- <p class=""><small>{{env('CREDITS_TEXT')}} in Wallet</small></p> --}}
                            <p class=""><small>Credits in Wallet</small></p>
                            <h6 class="value" id="current_amount">--</h6>
                            {{-- <p class=""><small>Total Wallet</small></p> --}}
                        </div>
                        <div class="">
                            <div class="w-icon" style="background-color: #8dbf42;">
                                {!! env('INR_SYMBOL2') !!}
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
                            <p class=""><small>Credit to Admins </small></p>
                            <h6 class="value" id="amount_credit_to_admins">--</h6>
                            <!-- <p class=""><small>Total Wallet</small></p> -->
                        </div>
                        <div class="">
                            <div class="w-icon" style="background-color:blue">
                                {!! env('INR_SYMBOL2') !!}
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
                            <p class=""><small>Total Transaction</small></p>
                            <h6 class="value" id="totalRecords">--</h6>
                            <!-- <p class=""><small>Total Wallet</small></p>  -->
                        </div>
                        <div class="">
                            <div class="w-icon" style="background-color: orange">
                                {!! env('INR_SYMBOL2') !!}
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
                    {{-- <a href="{{route('addSadminWallet')}}" class="btn btn-primary mb-2">Add {{env('CREDITS_TEXT')}} +</a> --}}
                    <a href="{{route('addSadminWallet')}}" class="btn btn-primary mb-2">Add Credits +</a>
                </div>
                <div class="table-responsive mb-4 mt-4">

                    <table id="multi-column-ordering" class="table table-hover">
                        <thead>
                            <tr>
                                <th>Txn No.</th>
                                <th>Name</th>
                                {{-- <th>Current {{env('CREDITS_TEXT')}}</th> --}}
                                 <th>Current Credits</th>
                                <th>Email</th>
                                {{-- <th>Credit {{env('CREDITS_TEXT')}}</th> --}}
                                  <th>Credit Credits</th>
                                {{-- <th>Debit {{env('CREDITS_TEXT')}}</th> --}}
                                   <th>Debit Credits</th>
                                <th>Message</th>
                                <!-- <th>Status</th> -->
                                <th>Created Date</th>
                            </tr>
                        </thead>
                        <tbody id="tableItem">

                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Txn No.</th>
                                <th>Name</th>
                                 {{-- <th>Current {{env('CREDITS_TEXT')}}</th> --}}
                                  <th>Current Credits</th>
                                <th>Email</th>
                                {{-- <th>Credit {{env('CREDITS_TEXT')}}</th> --}}
                                  <th>Credit Credits</th>
                                {{-- <th>Debit {{env('CREDITS_TEXT')}}</th> --}}
                                   <th>Debit Credits</th>
                               
                                <th>Message</th>
                                <!-- <th>Status</th> -->
                                <th>Created Date</th>
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

  <script type="text/javascript">
    $(document).ready(function(){

      // DataTable
      $('#multi-column-ordering').DataTable({
         processing: true,
         serverSide: true,
         order: [[0, 'desc']],
         ajax: "{{route('getSadminWalletList')}}",
         columns: [
            { data: 'id' },
            { data: 'credit_for' },
            { data: 'wallet_amount' },
            { data: 'email' },
            { data: 'credit_amount' },
            { data: 'debit_amount' },
            
            // { data: 'wallet_logo' },
            // { data: 'credit_for' , orderable: false, searchable: false },
            { data: 'message' },
            // { data: 'status' },
            { data: 'created_at'},
         ],
         drawCallback: function (settings) {

            var response = settings.json;
            $('#total_amount_added').text(response.total_amount_added);
            $('#current_amount').text(response.current_amount);
            $('#amount_credit_to_admins').text(response.amount_credit_to_admins);
            $('#totalRecords').text(response.totalRecords);
            console.log(response);
            updateIcon()
        },
      });
    });
    </script>


<!-- footer script if required -->
@endsection
