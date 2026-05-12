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
                <div class="text-left" style="margin:10px 0; padding-top:20px;">
                    <p>
                        <a href="{{route('admin.webseries')}}">{{strtoupper('Webseries')}}</a>&nbsp; &gt;
                        <a href="{{route('admin.webseries.seasons', base64_encode($webseries->id)) }}">{{strtoupper($webseries->name)}}</a>&nbsp; &gt;
                        <a href="{{route('admin.webseries.seasons.episodes', base64_encode($season->id))}}">{{ strtoupper($season->Session_Name)}}</a> 
                    </p>
                </div>
                <div class="text-right">
                    <a href="{{url('webseriesepisodes-order/'.base64_encode($id))}}" class="btn btn-primary mb-2">Order Episodes</a>
                    <a href="{{url('add-web-series-season-episode/'.base64_encode($id))}}" class="btn btn-primary mb-2">Add +</a>
                    <button type="button" class="btn btn-secondary mb-2" data-toggle="modal" data-target="#addContentModal">
                        Import from Playlist
                    </button>  
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
                            <form id="importmoviesForm" method="POST" action="{{route('importPlaylistsereis')}}">
                            @csrf
                            <input type="hidden" name="seadon_id" id="seadson_id_playlist" value="{{$season->id}}">
                                <div class="form-group">
                                    <label for="networkName">Playlits Id</label>                                    
                                    <input type="text" class="form-control" name="playlist_id" id="playlist_id" required placeholder="Enter Playlist Id"> 
                                </div>                                
                                <div class="form-group">        
                                    <label for="status">Type</label>
                                    <select name="type" id="type" class="form-control select">
                                        <option value="0">Free</option>
                                        <option value="1">Premium</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        @error('size') {{ $message }} @enderror
                                    </div>
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
                    
                    <table id="multi-column-ordering" class="table table-hover" data-table="web_series_episoade">
                        <thead>
                            <tr>
                                <th class="editable-th" style="width: 300px;" data-column="Episoade_Name">Name</th>                                                                                                                         
                                <th>Thumbnail</th>
                                <th>Status</th>
                                <th>Play</th>
                                <th>Playlist Id</th>
                                <th>Source</th>
                                <th>Url</th>
                                {{-- <th>Downloadable</th> --}}
                                {{-- <th>Type</th> --}}
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
                                <th>Play</th>
                                <th>Playlist Id</th>
                                <th>Source</th>
                                <th>Url</th>
                                {{-- <th>Downloadable</th> --}}
                                {{-- <th>Type</th> --}}
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
            destroy: true, // destroy on re-initialize
            stateSave: true, 
            order: [[0, 'asc']],        
            ajax: {
                url: "{{route('getWebseriesEpisodesList', $id)}}",
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
            { data: 'Episoade_Name', width: '300'},                        
            { data: 'image',orderable: false, searchable: false },                        
            { data: 'status',orderable: false, searchable: false  },
            { data: 'play_btn', orderable: false, searchable: false },
            { data: 'playlist_id', width: '200px'},                        
            { data: 'source' },
            { data: 'url' },
            // { data: 'downloadable',orderable: false, searchable: false  },
            // { data: 'type',orderable: false, searchable: false  },
            { data: 'created_at' },
            { data: 'action', orderable: false, searchable: false },
            ],
            columnDefs: [
                {
                    targets: 0, // index of 'name' column
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
                setEditable();
            },
        });      
    });

    $('#select_status').on('change', function() {        
        $('#multi-column-ordering').DataTable().ajax.reload(null, false);
    });

    $('#select_playlist_id').on('change', function() {
        $('#multi-column-ordering').DataTable().ajax.reload(null, false);
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
        $('#d_title').text('Websereis Episode')
        $('#d_id').val(id);
        $('#delete_modal').modal('show');        
    }

    function delete_row(){
        var id = $('#d_id').val();
        $.ajax({
            type: 'POST',
            url: "{{route('webseries-episode.destroy')}}",
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
                            _token: "{{ csrf_token() }}"                
                        },
                        success: function(response){
                            if (response.success) {
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
    </script>

   
<!-- footer script if required -->
@endsection