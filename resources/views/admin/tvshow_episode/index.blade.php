@extends('layout.default')
@section('mytitle', 'Admin List')
@section('page', 'Show Episodes  /  List')

@section('content')
<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
            <div class="widget widget-card-four">
                <div class="widget-content">
                    <div class="w-content">
                        <div class="w-info">
                            <p class=""><small>Total Episodes</small></p>
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
                            <p class=""><small>Active Episodes</small></p>
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
                            <p class=""><small>Inactive Episodes</small></p>
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
                            <p class=""><small>Deleted Episodes</small></p>
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

                <div class="alert alert-success alert-block" id="alert-success" style="display: none;">
                    <button type="button" class="close" data-dismiss="alert">×</button>    
                    <strong class="success-message"></strong>
                </div>

                <div class="alert alert-danger alert-block" id="alert-danger" style="display: none;">
                    <button type="button" class="close" data-dismiss="alert">×</button>    
                    <strong class="error-message"></strong>
                </div>

                <div id="delete_bd_ms"></div>
                @if(session()->has('message'))
                    <div class="alert alert-success alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>    
                        <strong>{{ session()->get('message') }}</strong>
                    </div>
                @endif 
                                 
                
                <?php                     
                    $season = \App\Models\TvShowSeason::where('id', $id)->first();
                    $show = \App\Models\TvShow::where('id', $season->show_id)->first();
                    $channel = \App\Models\TvChannel::where('id', $show->tv_channel_id)->first();
                ?>

                <div class="text-left" style="margin: 10px 0;">
                    <p>
                        <a href="{{route('admin.tvchannel')}}">{{strtoupper('TV Channels')}}</a>&nbsp; &gt;                        
                        <a href="{{route('admin.tvshow', base64_encode($channel->id))}}">{{strtoupper($channel->name)}}</a>&nbsp; &gt;                        
                        <a href="{{route('admin.tvshow.season', base64_encode($show->id))}}">{{strtoupper($show->name)}}</a>&nbsp; &gt;                        
                        <a href="{{route('admin.tvshow.episode', base64_encode($id))}}">{{strtoupper($season->title)}}</a>
                    </p>

                    <div class="text-left" style="display: flex; justify-content:flex-end;align-items:center; gap:10px; width:30%;">                    
                </div>
                </div>

                <div class="row d-flex justify-content-start align-items-center">
                    <div class="col-md-3">
                        <select name="select_playlist_id" id="select_playlist_id" class="form-control w-25 select" style="width: 25%;">
                            <option value="">--Filter by Playlist Id--</option>
                            @foreach ($playlist_ids as $item)
                                <option value="{{$item}}">{{$item}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="select_status" id="select_status" class="form-control w-25 select" style="width: 25%;">
                            <option value="">--Filter by Status--</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div> 
                
                <div class="text-right" style="display: flex; justify-content:flex-end;align-items:center; gap:10px;">                    
                    <a href="{{ route('admin.tvshowepisode.order', base64_encode($id)) }}" class="btn btn-primary mb-2">Order Episode</a>
                    <a href="{{route('addTvShowEpisode', base64_encode($id))}}" class="btn btn-primary mb-2">Add +</a>
                    <div class="text-left">
                        <button type="button" class="btn btn-secondary mb-2" data-toggle="modal" data-target="#addContentModal">
                            Import from Playlist
                        </button>   

                        @if(request()->get('dev') == 'true')
                        <button type="button" class="btn btn-secondary mb-2" data-toggle="modal"
                            data-target="#importCsvModal">
                            Import CSV
                        </button>
                        @endif

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
                                            <a href="{{ url('tv-show-episode-download-sample-csv') }}"
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
                                                <input type="hidden" name="season_id" value="{{$id}}">
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
                    </div>                    
                </div>
                <div class="modal fade" id="addContentModal" tabindex="-1" role="dialog" aria-labelledby="addContentModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                            
                            <div class="modal-header">
                                <h5 class="modal-title" id="addContentModalLabel">Add Movies From Playlist</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            
                            <div class="modal-body">                                
                                <form id="importmoviesForm" method="POST" action="{{route('importtvshowsepisodeplaylits')}}">
                                @csrf
                                <input type="hidden" name="id" value="{{$id}}">
                                    <div class="form-group">
                                        <label for="networkName">Playlits Id</label>                                    
                                        <input type="text" class="form-control" name="playlist_id" id="playlist_id" required placeholder="Enter Playlist Id"> 
                                    </div>                                                                                              
                                </form>
                            </div>
                            
                            <div class="modal-footer">
                                <button type="submit" form="importmoviesForm" class="btn btn-success">Save</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                            
                            </div>
                        </div>
                    </div>
                <div class="table-responsive mb-4 mt-4">
                    
                    <table id="multi-column-ordering" class="table table-hover" data-table="shows_episodes">
                        <thead>
                            <tr>                  
                                <th>S.no</th>              
                                <th class="editable-th" data-column="title">Title</th>                                                                                                                         
                                <th>Thumbnail</th>                                                                                                                                                                                                                                                                                                                     
                                <th>Status</th>              
                                <th>Play</th>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               
                                <th>Duration</th>                                                                                                                                                                                                                                                                                                                                                                                                                                                    
                                <th>Playlist_id</th>                                                                                                                                                                                                                                                                                                                                                                                                                                                    
                                <th>Created Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tableItem">
                            
                        </tbody>
                        <tfoot>
                            <tr>      
                                <th>S.no</th>             
                                <th>Title</th>                                                                                                                         
                                <th>Thumbnail</th> 
                                <th>Status</th>                                                                                                                                                                                                                                                                                                                        
                                <th>Play</th>                                                                                                                                                                                                                                                                                                                        
                                <th>Duration</th>
                                <th>Playlist_id</th>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         
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
         stateSave: true, 
         order: [[0, 'asc']],
         pageLength: 500,

        lengthMenu: [
            [10, 25, 50, 100, 500],
            [10, 25, 50, 100, 500]
        ],
         ajax: {
            url :  "{{route('getTvShowEpisodeList', $id)}}",
            data: function(d) {
                // Only send when values are selected
                let playlist_id = $('#select_playlist_id').val();
                let status = $('#select_status').val();

                if (playlist_id !== '') {
                    d.playlist_id = playlist_id;
                }

                if (status !== '') {
                    d.status = status;
                }
            }
        },
         columns: [  
            { data: 'episode_number' },                                                          
            { data: 'title' },                                                          
            { data: 'thumbnail',orderable: false, searchable: false },                                                                                      
            { data: 'status' },            
            { data: 'play_btn' },            
            { data: 'duration' },            
            { data: 'playlist_id' },            
            { data: 'created_at' },
            { data: 'action', orderable: false, searchable: false },
         ],
         columnDefs: [
            {
                targets: 1, // index of 'name' column
                createdCell: function(td, cellData, rowData, row, col) {
                    // $(td).addClass('editable');
                    $(td).attr('data-id', rowData.id); // Set data-id attribute
                }
            },            
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
            setTimeout(() => {        
                setEditable();
            }, 500);
        },
      });        

        $('#select_status').on('change', function() {         
            $('#multi-column-ordering').DataTable().ajax.reload(null, false);
        });
        
    });

    function setEditable(){
    $('#multi-column-ordering thead th').each(function (index) {            
            
        if ($(this).hasClass('editable-th')) {                    
            $('#tableItem tr').each(function () {
                $(this).find('td').eq(index).addClass('editable');                                
            });
        }
    });
}


    function deleteRowModal(id){ 
        $('#d_title').text('TV Shows Episode')
        $('#d_id').val(id);
        $('#delete_modal').modal('show');        
    }

    function delete_row(){
        var id = $('#d_id').val();
        $.ajax({
            type: 'POST',
            url: "{{route('tvshowepisode.destroy')}}",
            data: {
                _token: '{{ csrf_token() }}',
                id:id
            },
            success: function(data){
                $('#delete_modal').modal('hide');
                // $('#multi-column-ordering').DataTable().ajax.reload();
                if ($.fn.DataTable.isDataTable('#multi-column-ordering')) {
                    $('#multi-column-ordering').DataTable().ajax.reload(null, false); // ✅ stay on same page
                } else {
                    // fallback: reload full page
                    window.location.reload();
                }
            }
        })
    }

    document.addEventListener('dblclick', function (event){
        const target = event.target
        if (target.classList.contains('editable')) {        
            if (target.querySelector('input')) return;
            const currentText = target.textContent.trim();
            const input = document.createElement('input'); 
            const id = target.getAttribute('data-id');       

            input.type = 'text';
            input.value = currentText;
            input.style.width = '100%';
            input.classList.add('form-control');
            input.setAttribute('data-id', id);

            target.textContent = '';
            target.appendChild(input);
            input.focus();

            input.addEventListener('blur', function () {
                const newValue = input.value.trim();
                target.textContent = newValue || currentText; // fallback to old value if empty
            });

            input.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    input.blur(); // triggers blur above
                    const table = document.getElementById('multi-column-ordering').getAttribute('data-table');                
                    const td = input.closest('td');


                    const column = document.querySelector('.editable-th').getAttribute('data-column');
                    let id = input.getAttribute('data-id');

                    console.log(id, table, column);
                    // return false;
                    

                    $.ajax({
                        method : 'POST',
                        url : "{{route('update-column')}}",                    
                        data: {
                            id : id,                        
                            table : table,
                            column : column,  
                            value : input.value, 
                            _token: "{{ csrf_token() }}" // ✅ Add this line                     
                        },
                        success: function(response){
                            if (response.success) {
                                const capitalizedColumn = column.charAt(0).toUpperCase() + column.slice(1);
                                $('.success-message').html(`${capitalizedColumn} updated successfully !`);
                                $('#alert-success').show();
                                setTimeout(() => {
                                    $('#alert-success').hide();
                                }, 2000);
                                // $('#multi-column-ordering').DataTable().ajax.reload();
                            }
                            else{
                                const capitalizedColumn = column.charAt(0).toUpperCase() + column.slice(1);
                                $('.error-message').html(response.message);
                                $('#alert-danger').show();
                                setTimeout(() => {
                                    $('#alert-danger').hide();
                                }, 2000);
                                target.textContent = currentText;
                                // $('#multi-column-ordering').DataTable().ajax.reload();
                            }
                        }
                    })
                    
                }
            });    
        }
    })
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
                url: "{{ url('import-csv-tv-show-episode') }}",
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
                    $('#csv-progress-text').text('Done!');
                    showCsvAlert('success', res.message || 'CSV imported successfully!');
                    document.getElementById('importCsvForm').reset();
                    document.querySelector('.custom-file-label').textContent = 'Choose CSV file...';
                    dataTable.ajax.reload(null, false);
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
            el.className = 'mt-2 alert alert-' + type;
            el.textContent = message;
            el.style.display = 'block';
        }
    </script>

   
<!-- footer script if required -->
@endsection