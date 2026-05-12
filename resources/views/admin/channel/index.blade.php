@extends('layout.default')
@section('mytitle', 'Admin List')
@section('page', 'Channels / List')


@section('content')
    <div class="layout-px-spacing">
        <div class="row layout-top-spacing">
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
                <div class="widget widget-card-four">
                    <div class="widget-content">
                        <div class="w-content">
                            <div class="w-info">
                                <p class=""><small>Total Channels</small></p>
                                <h6 class="value" id="totalRecords">--</h6>
                                <!-- <p class=""><small>Total Channels</small></p> -->
                            </div>
                            <div class="">
                                <div class="w-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="feather feather-airplay">
                                        <path
                                            d="M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1">
                                        </path>
                                        <polygon points="12 15 17 21 7 21 12 15"></polygon>
                                    </svg>
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
                                <p class=""><small>Active Channels</small></p>
                                <h6 class="value" id="activeRecords">--</h6>
                                <!-- <p class=""><small>Total Channels</small></p> -->
                            </div>
                            <div class="">
                                <div class="w-icon" style="background-color: #8dbf42;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="feather feather-airplay">
                                        <path
                                            d="M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1">
                                        </path>
                                        <polygon points="12 15 17 21 7 21 12 15"></polygon>
                                    </svg>
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
                                <p class=""><small>Inactive Channels</small></p>
                                <h6 class="value" id="inactiveRecords">--</h6>
                                <!-- <p class=""><small>Total Channels</small></p> -->
                            </div>
                            <div class="">
                                <div class="w-icon" style="background-color:#e2a03f">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="feather feather-airplay">
                                        <path
                                            d="M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1">
                                        </path>
                                        <polygon points="12 15 17 21 7 21 12 15"></polygon>
                                    </svg>
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
                                <p class=""><small>Deleted Channels</small></p>
                                <h6 class="value" id="deletedRecords">--</h6>
                                <!-- <p class=""><small>Total Channels</small></p> -->
                            </div>
                            <div class="">
                                <div class="w-icon" style="background-color: #e7515a">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="feather feather-airplay">
                                        <path
                                            d="M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1">
                                        </path>
                                        <polygon points="12 15 17 21 7 21 12 15"></polygon>
                                    </svg>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">

                <div class="widget-content widget-content-area br-6">

                    <div id="delete_bd_ms"></div>
                    @if (session()->has('message'))
                        <div class="alert alert-success alert-block">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <strong>{{ session()->get('message') }}</strong>
                        </div>
                    @endif

                    <div class="row d-flex justify-content-start align-items-center mb-3">
                        <div class="col-md-3">
                            <select name="select_status" id="select_status" class="form-control w-25 select"
                                style="width: 25%;">
                                <option value="">--Filter by Status--</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="text-right">
                        <a href="{{ url('add-channel') }}" class="btn btn-primary mb-2">Add +</a>

                        {{-- Import CSV Button --}}
                        <button type="button" class="btn btn-secondary mb-2" data-toggle="modal"
                            data-target="#importCsvModal">
                            Import CSV
                        </button>

                        {{-- Import CSV Modal --}}
                        <div class="modal fade" id="importCsvModal" tabindex="-1" role="dialog"
                            aria-labelledby="importCsvModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-md" role="document">
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h5 class="modal-title" id="importCsvModalLabel">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                style="margin-right:6px; vertical-align:middle;">
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                <polyline points="17 8 12 3 7 8"></polyline>
                                                <line x1="12" y1="3" x2="12" y2="15">
                                                </line>
                                            </svg>
                                            Import CSV
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>

                                    <div class="modal-body">

                                        {{-- Step 1: Download Sample --}}
                                        <div class="mb-4 p-3"
                                            style="background:transparent; border-radius:8px; border:1px dashed #ced4da;">
                                            <a href="{{ url('download-sample-csv') }}"
                                                class="btn btn-outline-primary btn-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    style="margin-right:4px; vertical-align:middle;">
                                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                    <polyline points="7 10 12 15 17 10"></polyline>
                                                    <line x1="12" y1="15" x2="12" y2="3">
                                                    </line>
                                                </svg>
                                                Download Sample CSV
                                            </a>
                                        </div>

                                        {{-- Step 2: Upload File --}}
                                        <div>
                                            <form id="importCsvForm" enctype="multipart/form-data">
                                                @csrf
                                                <div class="form-group mb-2">
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="csv_file"
                                                            name="csv_file" accept=".csv" required>
                                                        <label class="custom-file-label text-left" for="csv_file">Choose
                                                            CSV file...</label>
                                                    </div>
                                                </div>

                                                {{-- Progress Bar --}}
                                                <div id="csv-progress-wrap" style="display:none;" class="mt-2">
                                                    <div class="progress" style="height:6px;">
                                                        <div id="csv-progress-bar"
                                                            class="progress-bar bg-success progress-bar-striped progress-bar-animated"
                                                            style="width:0%"></div>
                                                    </div>
                                                    <small class="text-muted mt-1 d-block"
                                                        id="csv-progress-text">Uploading...</small>
                                                </div>

                                                {{-- Alert --}}
                                                <div id="csv-alert" class="mt-2" style="display:none;"></div>
                                            </form>
                                        </div>

                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Cancel</button>
                                        <button type="button" class="btn btn-success" id="importCsvBtn">
                                            Upload & Import
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>


                        {{-- swap chanel number modal --}}
                        <div class="modal fade" id="swapChannelModal" tabindex="-1" role="dialog"
                            aria-labelledby="swapChannelModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="swapChannelModalLabel">Swap Channel Number</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="swapChannelForm" data-action="{{ route('swapChannelNumber') }}"
                                            class="text-left">
                                            @csrf
                                            <div class="form-group">
                                                <label for="old_channel_number">Current Channel Number</label>
                                                <input type="text" name="old_channel_number" class="form-control"
                                                    readonly id="swap_channel_id">
                                            </div>
                                            <div class="form-group">
                                                <label for="new_channel_number">New Channel Number</label>
                                                <input type="number" class="form-control" id="new_channel_number"
                                                    name="new_channel_number" required>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary" id="confirmSwapBtn">Confirm
                                            Swap</button>
                                    </div>
                                </div>
                            </div>
                        </div>


                        {{-- Show selected filename in label --}}
                    </div>
                    <div class="table-responsive mb-4 mt-4">

                        <table id="multi-column-ordering" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Logo</th>
                                    <th>Number</th>
                                    <!-- <th>Genre</th> -->
                                    <th>Language</th>
                                    <th>Link</th>
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
                                    <th>Logo</th>
                                    <th>Number</th>
                                    <!-- <th>Genre</th> -->
                                    <th>Language</th>
                                    <th>Link</th>
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
                    "url": "{{ route('getAdminList') }}",
                    // "type": "POST",
                    "data": function(d) {
                        console.log(d);
                        // d.parent_cat = $("#parent_cat").val();

                    }
                },

                columns: [{
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'mobile',
                        name: 'mobile'
                    },
                    {
                        data: 'address',
                        name: 'address'
                    },
                    {
                        data: 'city',
                        name: 'city'
                    },
                    {
                        data: 'country',
                        name: 'country'
                    },
                    {
                        data: 'company_name',
                        name: 'company_name'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],

            });

            $("input#search").on("keyup", function(event) {
                if ($('#search').val().length >= 3 || $('#search').val().length == 0) {
                    table.draw(), event.preventDefault()
                }
            });
            $("#btn-search").click(function(a) {
                table.draw(), a.preventDefault()
            });

        });
    </script> -->

    <script src="cdn.datatables.net/plug-ins/1.12.1/sorting/date-uk.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            // DataTable
            $('#multi-column-ordering').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [6, 'desc']
                ],
                pageLength: 500,

                lengthMenu: [
                    [10, 25, 50, 100, 500],
                    [10, 25, 50, 100, 500]
                ],
                // ajax: "{{ route('getChannelList') }}",
                ajax: {
                    url: "{{ route('getChannelList') }}",
                    data: function(d) {
                        let status = $('#select_status').val();


                        if (status !== '') {
                            d.status = status;
                        }
                    }


                },
                columns: [{
                        data: 'channel_name'
                    },
                    {
                        data: 'channel_logo',
                        orderable: false,
                    },
                    {
                        data: 'channel_number'
                    },
                    // { data: 'channel_logo' },
                    // { data: 'channel_genre' },
                    {
                        data: 'channel_language',
                        orderable: false
                    },
                    {
                        data: 'channel_link',
                        orderable: false
                    },
                    // { data: 'company_name' },
                    {
                        data: 'status'
                    },
                    {
                        data: 'created_at'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                drawCallback: function(settings) {

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

        $('[data-toggle="tooltip"]').tooltip({
            html: true
        });


        $(document).on('click', '.swap-btn', function() {
            var channelId = $(this).data('number');
            $('#swap_channel_id').val(channelId);
            $('#swapChannelModal').modal('show');
        });




        $(document).on('click', '#confirmSwapBtn', function() {

            let oldChannel = $('#swap_channel_id').val();
            let newChannel = $('#new_channel_number').val();

            // ❌ Empty check
            if (!newChannel) {
                alert('Please enter new channel number');
                return;
            }

            // ❌ Same number check
            if (oldChannel == newChannel) {
                alert('New channel number cannot be same as current');
                return;
            }

            // ❌ Only positive numbers
            if (newChannel <= 0) {
                alert('Channel number must be greater than 0');
                return;
            }

            let formData = $('#swapChannelForm').serialize();

            // 🔄 AJAX Call
            $.post($('#swapChannelForm').data('action'), formData, function(res) {
                if (res.success == true) {
                    $('#swapChannelModal').modal('hide');
                    $('#multi-column-ordering').DataTable().ajax.reload(null, false);
                    $('#swapChannelForm')[0].reset();

                    alert(res.message);
                } else {
                    alert(res.message || 'Something went wrong.');
                }
            }).fail(function() {
                // alert('Server error. Please try again.');
                
            });
        });
    </script>

    <script>
        // File name label update
        document.getElementById('csv_file').addEventListener('change', function() {
            var fileName = this.files[0] ? this.files[0].name : 'Choose CSV file...';
            this.nextElementSibling.textContent = fileName;
        });




        // AJAX Submit
        document.getElementById('importCsvBtn').addEventListener('click', function() {
            var fileInput = document.getElementById('csv_file');

            if (!fileInput.files.length) {
                showCsvAlert('danger', 'Please select a CSV file first.');
                return;
            }

            var formData = new FormData(document.getElementById('importCsvForm'));

            document.getElementById('csv-progress-wrap').style.display = 'block';
            document.getElementById('csv-alert').style.display = 'none';
            document.getElementById('importCsvBtn').disabled = true;

            $.ajax({
                url: "{{ url('import-csv') }}",
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                xhr: function() {
                    var xhr = new XMLHttpRequest();
                    xhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            var percent = Math.round((e.loaded / e.total) * 100);
                            $('#csv-progress-bar').css('width', percent + '%');
                            $('#csv-progress-text').text('Uploading... ' + percent + '%');
                        }
                    });
                    return xhr;
                },
                success: function(res) {

                    $('#csv-progress-bar').css('width', '100%');
                    $('#csv-progress-text').text('Processing...');

                    let html = `
                        <div class="alert alert-success mt-4">
                            ${res.message}
                        </div>
                    `;

                    // If failed rows exist
                    if (res.failed_count > 0) {

                        html += `
                            <div class="alert alert-warning mt-4" style="margin:1.5rem 0;">
                                <strong>${res.failed_count} rows failed</strong>
                                <button type="button" class="btn btn-sm btn-link p-0 ms-2" id="toggleFailed">
                                    View Details
                                </button>

                                <div id="failedRowsBox" style="display:none; margin-top:10px; overflow-x:auto;">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Channel Number</th> 
                                                <th>Error</th>                                                
                                            </tr>
                                        </thead>
                                        <tbody>
                        `;

                        res.failed_rows.forEach((row, index) => {
                            html += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${row.channel_number}</td>
                                    <td>${row.error ?? 'Invalid data'}</td>                                    
                                </tr>
                            `;
                        });

                        html += `
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        `;
                    }

                    $('#csv-alert').html(html).fadeIn();

                    // Toggle failed rows
                    $(document).on('click', '#toggleFailed', function() {
                        $('#failedRowsBox').slideToggle();
                    });

                    // Reset form
                    $('#importCsvForm')[0].reset();
                    $('.custom-file-label').text('Choose CSV file...');

                    if (typeof dataTable !== 'undefined') {
                        dataTable.ajax.reload(null, false);
                    }
                },
                error: function(xhr) {
                    var msg = xhr.responseJSON?.message || 'Something went wrong.';
                    showCsvAlert('danger', msg);
                },
                complete: function() {
                    document.getElementById('importCsvBtn').disabled = false;
                    setTimeout(() => {
                        document.getElementById('csv-progress-wrap').style.display = 'none';
                        $('#csv-progress-bar').css('width', '0%');
                    }, 2000);
                }
            });
        });

        function showCsvAlert(type, message) {
            var el = document.getElementById('csv-alert');
            el.className = 'mt-4 alert alert-' + type;
            el.textContent = message;
            el.style.display = 'block';
        }
    </script>


    <!-- footer script if required -->
@endsection
