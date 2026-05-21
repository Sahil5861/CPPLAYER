@extends('layout.default')
@section('mytitle', 'Admin List')
@section('page', 'Channels  /  List')
<style>
    #content_network{
        width: 200px !important;
    }
    input{
        text-align: left;
    }

    /* Full Screen Loader */
    #dt-loader-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.45);
        z-index: 99999;
        justify-content: center;
        align-items: center;
    }
    #dt-loader-overlay.active {
        display: flex;
    }
    .dt-loader-box {
        background: transparent;
        border-radius: 12px;
        padding: 30px 40px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 14px;
        /* box-shadow: 0 8px 30px rgba(0,0,0,0.2); */
    }
    .dt-spinner {
        width: 44px;
        height: 44px;
        border: 4px solid #e0e0e0;
        border-top-color: #4361ee;
        border-radius: 50%;
        animation: dt-spin 0.75s linear infinite;
    }
    @keyframes dt-spin {
        to { transform: rotate(360deg); }
    }
    .dt-loader-text {
        font-size: 14px;
        color: #555;
        font-weight: 500;
        margin: 0;
    }
</style>


@section('content')

{{-- Full Screen DataTable Loader --}}
<div id="dt-loader-overlay">
    <div class="dt-loader-box">
        <div class="dt-spinner"></div>
        <p class="dt-loader-text">Loading data, please wait...</p>
    </div>
</div>

<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
            <div class="widget widget-card-four">
                <div class="widget-content">
                    <div class="w-content">
                        <div class="w-info">
                            <p class=""><small>Total Movies</small></p>
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
                            <p class=""><small>Active Movies</small></p>
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
                            <p class=""><small>Inactive Movies</small></p>
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
                            <p class=""><small>Deleted Movies</small></p>
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
                <div class="row d-flex justify-content-start align-items-center mb-3">
                    <div class="col-md-3">
                        <select name="select_playlist_id" id="select_playlist_id" class="form-control w-25 select" style="width: 25%;">
                            <option value="">--Filter by Playlist Id--</option>
                            @foreach ($playlist_ids as $item)
                                <option value="{{$item}}">{{$item}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="select_status" id="select_status" class="form-control w-25 select" style="width: 25%;">
                            <option value="">--Filter by Status--</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="select_netword_id" id="select_netword_id" class="form-control w-25 select" style="width: 25%;">
                            <option value="">--Filter by Content Netword--</option>
                            @foreach ($networks as $item)
                                <option value="{{$item->id}}">{{$item->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <select name="select_stream_type" id="select_stream_type" class="form-control w-25 select" style="width: 25%;">
                            <option value="">--Filter Stream Type--</option>
                            <option value="YoutubeLive">Youtube</option>
                            <option value="M3U8">M3U8</option>                            
                            <option value="MKV">MKV</option>                            
                        </select>
                    </div>  
                    
                    <div class="col-md-2">
                        <select name="select_type" id="select_type" class="form-control w-25 select" style="width: 25%;">
                            <option value="">--Filter Type--</option>                            
                            <option value="recents">Recents</option>                            
                        </select>
                    </div>  

                </div>
                <div class="row d-flex justify-content-start align-items-center mb-3">
                    <div class="col-md-3">
                        <select name="movie_network_type" id="movie_networl_type" class="form-control w-100">
                            <option value="">--Filter By SD Type --</option>
                            <option value="1">SD</option>
                            <option value="0">Non SD</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                            <select name="statusSelect" id="statusSelect" class="form-control" onchange="updateMultistatus('movies')">
                                <option value="">--Update Status of Selected--</option>
                                <option value="1">Active</option>
                                <option value="0">In-active</option>
                            </select>
                        </div>

                </div>                                    
                {{-- <div class="text-left" style="display: flex; justify-content:flex-end;align-items:center; gap:10px; width:30%;">
                </div> --}}



                <div class="text-right" style="display: flex; justify-content:flex-end;align-items:center; gap:10px;">                    
                    <a href="{{url('add-movie')}}" class="btn btn-primary mb-2">Add +</a>
                    <a href="#" class="btn btn-danger mb-2" onclick="deleteMulti('movies');">Delete Multiple</a>

                    
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
                                            <a href="{{ url('adult-movie-download-sample-csv') }}"
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
                            <form id="importmoviesForm" method="POST" action="{{route('importmovies')}}">
                            @csrf
                                <div class="form-group">
                                    <label for="networkName">Playlits Id</label>                                    
                                    <input type="text" class="form-control" name="playlist_id" id="playlist_id" required placeholder="Enter Playlist Id"> 
                                </div>
                                <div class="form-group">        
                                    <label for="networkName">Content Networks</label>                                
                                    <select name="content_network[]" id="content_networks" multiple class="form-control select">                                    
                                    <?php
                                        foreach($content_networks as $network){
                                            echo '<option value="'.$network->id.'">'.$network->name.'</option>';
                                        }
                                    ?>
                                    </select>
                                </div>

                                <div class="form-group">        
                                    <label for="genre">Movie Genre</label>                                
                                    <select name="genre[]" id="genre" multiple class="form-control select">                                    
                                    <?php
                                        foreach($genres as $genre){
                                            echo '<option value="'.$genre->title.'">'.$genre->title.'</option>';
                                        }
                                    ?>
                                    </select>
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
                    
                    <table id="multi-column-ordering" class="table table-hover" data-table="movies">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="check-all"></th>
                                
                                {{-- <th>Id</th> --}}
                                <th class="editable-th" data-column="name">Name</th>                                                                                                                         
                                <th>Banner Image</th>                                                                                                                         
                                <th>Status</th>
                                <th>Is Recent</th>
                                <th>Stream Type</th>
                                <th>Play</th>
                                <th>Playlist Id</th>
                                <th>Created Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tableItem">
                            
                        </tbody>
                        <tfoot>
                            <tr>
                                @if ($deleteMulti)                                    
                                <th></th>
                                @endif
                                {{-- <th>Id</th> --}}
                                <th>Name</th>  
                                <th>Banner Image</th>                                                                                                                                                                                                                                                       
                                <th>Status</th>
                                <th>Is Recent</th>
                                <th>Stream Type</th>
                                <th>Play</th>
                                <th>Playlist Id</th>
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

<script src="cdn.datatables.net/plug-ins/1.12.1/sorting/date-uk.js"></script>



<script type="text/javascript">
let dataTable;
const target = 1;

console.log(target);




function initializeDataTable(network_id = '') {
    dataTable = $('#multi-column-ordering').DataTable({
        processing: true,
        serverSide: true,
        destroy: true, // destroy on re-initialize
        // stateSave: true, 

        order: [[6  + Number(target), 'desc']],
        pageLength: 100,

        lengthMenu: [
            [10, 25, 50, 100, 500],
            [10, 25, 50, 100, 500]
        ],
        ajax: {
            url: "{{ route('getMovieList') }}",
            beforeSend: function() {
                //$('#dt-loader-overlay').addClass('active');
                dtLoaderShow();
            },
            data: function(d) {
                // Only send when values are selected
                let playlist_id = $('#select_playlist_id').val();
                let network_id = $('#select_netword_id').val();
                let stream_type = $('#select_stream_type').val();
                let type = $('#select_type').val();
                let movie_networl_type = $('#movie_networl_type').val();
                let status = $('#select_status').val();

                if (playlist_id !== '') {
                    d.playlist_id = playlist_id;
                }

                if (network_id !== '') {
                    d.network_id = network_id;
                }
                if (stream_type !== '') {
                    d.stream_type = stream_type;
                }

                if (type !== '') {
                    d.type = type;
                }

                if (movie_networl_type !== '') {
                    d.movie_networl_type = movie_networl_type;
                }

                if (status !== '') {
                    d.status = status;
                }
            }
        },
        columns: [
            
            {
                data: 'id', // use 'id' to get value for checkbox
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return `<input type="checkbox" class="row-checkbox" value="${data}">`;
                }
            },
            // { data: 'id', width: '50px'},  
            { data: 'name', width: '300px'},                        
            { data: 'banner', orderable: false, searchable: false },                        
            { data: 'status', orderable: false, searchable: false },
            { data: 'recent', orderable: false, searchable: false },
            { data: 'stream_type', searchable: true, orderable: false},
            { data: 'play_btn', orderable: false, searchable: false },
            { data: 'playlist_id', orderable: true, searchable: true },
            { data: 'created_at' },
            { data: 'action', orderable: false, searchable: false },
        ],
        columnDefs: [
            {
                targets: target, // index of 'name' column
                createdCell: function(td, cellData, rowData, row, col) {
                    // $(td).addClass('editable');
                    $(td).attr('data-id', rowData.id); // Set data-id attribute
                }
            },            
        ],
        drawCallback: function(settings) {
            // $('#dt-loader-overlay').removeClass('active');
            dtLoaderHide();
            var response = settings.json;
            console.log('Datatabel reload !!');
            
            $('#totalRecords').text(response.totalRecords);
            $('#activeRecords').text(response.activeRecords);
            $('#inactiveRecords').text(response.inactiveRecords);
            $('#deletedRecords').text(response.deletedRecords);
            $('[data-toggle="tooltip"]').tooltip();
            updateIcon();
            setEditable();
        }
    });
}

$('#check-all').on('click', function() {
    let isChecked = $(this).prop('checked');
    $('.row-checkbox').prop('checked', isChecked);
});

// If any individual checkbox is unchecked, uncheck the "check-all"
$(document).on('change', '.row-checkbox', function() {
    if (!$(this).prop('checked')) {
        $('#check-all').prop('checked', false);
    } else if ($('.row-checkbox:checked').length === $('.row-checkbox').length) {
        $('#check-all').prop('checked', true);
    }
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
    $('#d_title').text('Movies')
    $('#d_id').val(id);
    $('#delete_modal').modal('show');        
}


function delete_row(){
    var id = $('#d_id').val();
    $.ajax({
        type: 'POST',
        url: "{{route('movie.destroy')}}",
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

$(document).ready(function() {
    initializeDataTable();   
    setTimeout(() => {        
        setEditable();
    }, 500);     
});

    $('#select_status').on('change', function() {                    
        dataTable.ajax.reload(null, false);
    });


    $('#select_playlist_id').on('change', function() {        
        dataTable.ajax.reload(null, false);
    });

    $('#select_stream_type').on('change', function() {        
        dataTable.ajax.reload(null, false);
    });

    $('#select_type').on('change', function() {        
        dataTable.ajax.reload(null, false);
    });

    $('#movie_networl_type').on('change', function (){
        dataTable.ajax.reload(null, false);
    })

    $('#select_netword_id').on('change', function() {
        dataTable.ajax.reload(null, false);
    });


document.addEventListener('dblclick', function (event){
    const target = event.target
    if (target.classList.contains('editable')) {        
        if (target.querySelector('input')) return;
        const currentText = target.textContent.trim();
        const input = document.createElement('input'); 
        const id = target.getAttribute('data-id');   
        
        console.log('---------');        
        console.log(id, target);
        

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
                        if (response.success == true) {
                            const capitalizedColumn = column.charAt(0).toUpperCase() + column.slice(1);
                            $('.success-message').html(`${capitalizedColumn} updated successfully !`);
                            $('#alert-success').show();
                            setTimeout(() => {
                                $('#alert-success').hide();
                            }, 2000);
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

function updateMovieStatus(url) {    
    var request = $.ajax({
                    url: url,
                    method: "GET"
                    });

    request.done(function( val ) {        
        var data = jQuery.parseJSON(val);
        $("#delete_blog_modal").modal('hide');
        $( "#delete_bd_ms" ).html(data.message);
        // $('#multi-column-ordering').DataTable().ajax.reload();
        // setTimeout(function(){location.reload(true);}, 2000);

    });
}


function updateIsRecent(url) {    
    var request = $.ajax({
                    url: url,
                    method: "GET"
                    });

    request.done(function( val ) {        
        var data = jQuery.parseJSON(val);
        $("#delete_blog_modal").modal('hide');
        $( "#delete_bd_ms" ).html(data.message);
        // $('#multi-column-ordering').DataTable().ajax.reload();
        // setTimeout(function(){location.reload(true);}, 2000);

    });
}

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
                url: "{{ url('import-csv-movies') }}",
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