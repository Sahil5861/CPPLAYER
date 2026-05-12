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
                            <p class=""><small>Amount in Wallet</small></p>
                            <h6 class="value" id="current_amount">--</h6>
                            <!-- <p class=""><small>Total Wallet</small></p> -->
                        </div>
                        <div class="">
                            <div class="w-icon" style="background-color: #8dbf42;">
                                <svg id="SvgjsSvg1011" width="27" height="27" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs"><defs id="SvgjsDefs1012"></defs><g id="SvgjsG1013"><svg xmlns="http://www.w3.org/2000/svg" data-name="Layer 1" viewBox="0 0 24 24" width="27" height="27"><path d="M18,7H15.79a5.49,5.49,0,0,0-1-2H18a1,1,0,0,0,0-2H7A1,1,0,0,0,7,5h3.5a3.5,3.5,0,0,1,3.15,2H7A1,1,0,0,0,7,9h7a3.5,3.5,0,0,1-3.45,3H7a.7.7,0,0,0-.14,0,.65.65,0,0,0-.2,0,.69.69,0,0,0-.19.1l-.12.07,0,0a.75.75,0,0,0-.14.17,1.1,1.1,0,0,0-.09.14.61.61,0,0,0,0,.18A.65.65,0,0,0,6,13s0,0,0,0a.7.7,0,0,0,0,.14.65.65,0,0,0,0,.2.69.69,0,0,0,.1.19s0,.08.07.12l6,7a1,1,0,0,0,1.52-1.3L9.18,14H10.5A5.5,5.5,0,0,0,16,9h2a1,1,0,0,0,0-2Z" fill="#ffffff" class="color000 svgShape"></path></svg></g></svg>
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
                            <p class=""><small>Total Debit Transaction</small></p>
                            <h6 class="value" id="amount_credit_to_admins">--</h6>
                            <!-- <p class=""><small>Total Wallet</small></p> -->
                        </div>
                        <div class="">
                            <div class="w-icon" style="background-color:blue">
                                <svg id="SvgjsSvg1011" width="27" height="27" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs"><defs id="SvgjsDefs1012"></defs><g id="SvgjsG1013"><svg xmlns="http://www.w3.org/2000/svg" data-name="Layer 1" viewBox="0 0 24 24" width="27" height="27"><path d="M18,7H15.79a5.49,5.49,0,0,0-1-2H18a1,1,0,0,0,0-2H7A1,1,0,0,0,7,5h3.5a3.5,3.5,0,0,1,3.15,2H7A1,1,0,0,0,7,9h7a3.5,3.5,0,0,1-3.45,3H7a.7.7,0,0,0-.14,0,.65.65,0,0,0-.2,0,.69.69,0,0,0-.19.1l-.12.07,0,0a.75.75,0,0,0-.14.17,1.1,1.1,0,0,0-.09.14.61.61,0,0,0,0,.18A.65.65,0,0,0,6,13s0,0,0,0a.7.7,0,0,0,0,.14.65.65,0,0,0,0,.2.69.69,0,0,0,.1.19s0,.08.07.12l6,7a1,1,0,0,0,1.52-1.3L9.18,14H10.5A5.5,5.5,0,0,0,16,9h2a1,1,0,0,0,0-2Z" fill="#ffffff" class="color000 svgShape"></path></svg></g></svg>
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
                            <p class=""><small>Total Credit Transaction</small></p>
                            <h6 class="value" id="total_amount_added">--</h6>
                            <!-- <p class=""><small>Total Wallet</small></p> -->
                        </div>
                        <div class="">
                            <div class="w-icon" style="background-color:blue">
                                <svg id="SvgjsSvg1011" width="27" height="27" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs"><defs id="SvgjsDefs1012"></defs><g id="SvgjsG1013"><svg xmlns="http://www.w3.org/2000/svg" data-name="Layer 1" viewBox="0 0 24 24" width="27" height="27"><path d="M18,7H15.79a5.49,5.49,0,0,0-1-2H18a1,1,0,0,0,0-2H7A1,1,0,0,0,7,5h3.5a3.5,3.5,0,0,1,3.15,2H7A1,1,0,0,0,7,9h7a3.5,3.5,0,0,1-3.45,3H7a.7.7,0,0,0-.14,0,.65.65,0,0,0-.2,0,.69.69,0,0,0-.19.1l-.12.07,0,0a.75.75,0,0,0-.14.17,1.1,1.1,0,0,0-.09.14.61.61,0,0,0,0,.18A.65.65,0,0,0,6,13s0,0,0,0a.7.7,0,0,0,0,.14.65.65,0,0,0,0,.2.69.69,0,0,0,.1.19s0,.08.07.12l6,7a1,1,0,0,0,1.52-1.3L9.18,14H10.5A5.5,5.5,0,0,0,16,9h2a1,1,0,0,0,0-2Z" fill="#ffffff" class="color000 svgShape"></path></svg></g></svg>
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
                            <p class=""><small>Total Credit Transaction</small></p>
                            <h6 class="value" id="total_credit_number">--</h6>
                            <!-- <p class=""><small>Total Wallet</small></p>  -->
                        </div>
                        <div class="">
                            <div class="w-icon" style="background-color: orange">
                                <svg id="SvgjsSvg1011" width="27" height="27" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs"><defs id="SvgjsDefs1012"></defs><g id="SvgjsG1013"><svg xmlns="http://www.w3.org/2000/svg" data-name="Layer 1" viewBox="0 0 24 24" width="27" height="27"><path d="M18,7H15.79a5.49,5.49,0,0,0-1-2H18a1,1,0,0,0,0-2H7A1,1,0,0,0,7,5h3.5a3.5,3.5,0,0,1,3.15,2H7A1,1,0,0,0,7,9h7a3.5,3.5,0,0,1-3.45,3H7a.7.7,0,0,0-.14,0,.65.65,0,0,0-.2,0,.69.69,0,0,0-.19.1l-.12.07,0,0a.75.75,0,0,0-.14.17,1.1,1.1,0,0,0-.09.14.61.61,0,0,0,0,.18A.65.65,0,0,0,6,13s0,0,0,0a.7.7,0,0,0,0,.14.65.65,0,0,0,0,.2.69.69,0,0,0,.1.19s0,.08.07.12l6,7a1,1,0,0,0,1.52-1.3L9.18,14H10.5A5.5,5.5,0,0,0,16,9h2a1,1,0,0,0,0-2Z" fill="#ffffff" class="color000 svgShape"></path></svg></g></svg>
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
                            <p class=""><small>Total Debit Transaction</small></p>
                            <h6 class="value" id="total_debit_number">--</h6>
                            <!-- <p class=""><small>Total Wallet</small></p>  -->
                        </div>
                        <div class="">
                            <div class="w-icon" style="background-color: orange">
                                <svg id="SvgjsSvg1011" width="27" height="27" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs"><defs id="SvgjsDefs1012"></defs><g id="SvgjsG1013"><svg xmlns="http://www.w3.org/2000/svg" data-name="Layer 1" viewBox="0 0 24 24" width="27" height="27"><path d="M18,7H15.79a5.49,5.49,0,0,0-1-2H18a1,1,0,0,0,0-2H7A1,1,0,0,0,7,5h3.5a3.5,3.5,0,0,1,3.15,2H7A1,1,0,0,0,7,9h7a3.5,3.5,0,0,1-3.45,3H7a.7.7,0,0,0-.14,0,.65.65,0,0,0-.2,0,.69.69,0,0,0-.19.1l-.12.07,0,0a.75.75,0,0,0-.14.17,1.1,1.1,0,0,0-.09.14.61.61,0,0,0,0,.18A.65.65,0,0,0,6,13s0,0,0,0a.7.7,0,0,0,0,.14.65.65,0,0,0,0,.2.69.69,0,0,0,.1.19s0,.08.07.12l6,7a1,1,0,0,0,1.52-1.3L9.18,14H10.5A5.5,5.5,0,0,0,16,9h2a1,1,0,0,0,0-2Z" fill="#ffffff" class="color000 svgShape"></path></svg></g></svg>
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
                <!-- <div class="text-right">
                    <a href="{{route('addResellerWallet')}}" class="btn btn-primary mb-2">Add Amount +</a>
                </div> -->
                <div class="table-responsive mb-4 mt-4">

                    <table id="multi-column-ordering" class="table table-hover">
                        <thead>
                            <tr>
                                <th>Txn No.</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Credit Amount</th>
                                <th>Debit Amount</th>
                                <!-- <th>Credit to</th> -->
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
                                <th>Email</th>
                                <th>Credit Amount</th>
                                <th>Price</th>
                                <!-- <th>Credit to</th> -->
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
         ajax: "{{route('getResellerWalletList')}}",
         columns: [
            { data: 'id' },
            { data: 'credit_for' },
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
            $('#total_credit_number').text(response.total_credit_number);
            $('#total_debit_number').text(response.total_debit_number);
            console.log(response);
            updateIcon()
        },
      });
    });
    </script>


<!-- footer script if required -->
@endsection
