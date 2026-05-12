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
</style>

@section('content')
<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
            <div class="widget widget-card-four">
                <div class="widget-content">
                    <div class="w-content">
                        <div class="w-info">
                            <p class=""><small>Total Laugter Shows</small></p>
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
                            <p class=""><small>Active Laugter Shows</small></p>
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
                            <p class=""><small>Inactive Laugter Shows</small></p>
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
                            <p class=""><small>Deleted Laugter Shows</small></p>
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
                <div class="text-right" style="display: flex; justify-content:flex-end;align-items:center; gap:10px;">
                    {{-- <select name="content_network" id="content_network" class="form-control w-25" style="width: 200px !important;" onchange="updateTable(this);">
                        <option value="">--Select--</option>
                        @foreach ($content_networks as $item)
                            <option value="{{$item->id}}">{{$item->name}}</option>
                        @endforeach
                    </select> --}}
                    <a href="{{url('laughtershow-order')}}" class="btn btn-primary mb-2">Order Laughter Shows</a>
                    <a href="{{url('add-laughter-show')}}" class="btn btn-primary mb-2">Add +</a>
                    <button type="button" class="btn btn-secondary mb-2" data-toggle="modal" data-target="#addContentModal">
                        Import from Playlist
                    </button>                                        
                </div>
                <div class="modal fade" id="addContentModal" tabindex="-1" role="dialog" aria-labelledby="addContentModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                        
                        <div class="modal-header">
                            <h5 class="modal-title" id="addContentModalLabel">Add Laugter Shows From Playlist</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        
                        <div class="modal-body">                                
                            <form id="importStageShowsForm" method="POST" action="{{route('importlaughtershows')}}">
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
                            <button type="submit" form="importStageShowsForm" class="btn btn-success">Save</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                        
                        </div>
                    </div>
                </div>
                <div class="table-responsive mb-4 mt-4">
                    
                    <table id="multi-column-ordering" class="table table-hover" data-table="laughter_show">
                        <thead>
                            <tr>
                                <th class="editable-th" data-column="name">Name</th>                                                                                                                         
                                <th>Banner Image</th>                                                                                                                         
                                <th>Status</th>
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
                                <th>Name</th>  
                                <th>Banner Image</th>                                                                                                                                                                                                                                                       
                                <th>Status</th>
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

@endsection

@section('footer')

<script type="text/javascript">
let dataTable;

function initializeDataTable(network_id = '') {
    dataTable = $('#multi-column-ordering').DataTable({
        processing: true,
        serverSide: true,
        destroy: true, // destroy on re-initialize
        stateSave: true,         
        order: [[4, 'desc']],
        pageLength: 500,

        lengthMenu: [
            [10, 25, 50, 100, 500],
            [10, 25, 50, 100, 500]
        ],
        ajax: {
            url: "{{ route('getLaughterShowsList') }}",
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
            { data: 'name', width: '400px'},                        
            { data: 'banner', orderable: false, searchable: false },                        
            { data: 'status', orderable: false, searchable: false },
            { data: 'play_btn', orderable: false, searchable: false },
            { data: 'playlist_id', orderable: true, searchable: true },
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
        drawCallback: function(settings) {
            var response = settings.json;
            console.log(response);
            
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

function deleteRowModal(id){ 
    $('#d_title').text('Laughter Shows Episode')
    $('#d_id').val(id);
    $('#delete_modal').modal('show');        
}

function delete_row(){
        var id = $('#d_id').val();
        $.ajax({
            type: 'POST',
            url: "{{route('laughtershow.destroy')}}",
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

function setEditable(){
    $('#multi-column-ordering thead th').each(function (index) {            
            
        if ($(this).hasClass('editable-th')) {
            console.log('hii');                
            $('#tableItem tr').each(function () {
                $(this).find('td').eq(index).addClass('editable');                                
            });
        }
    });
}

$(document).ready(function() {
    initializeDataTable();   
    setTimeout(() => {        
        setEditable();
    }, 500);     

    $('#select_playlist_id').on('change', function() {
        dataTable.ajax.reload();
    });


    $('#select_status').on('change', function() {         
        dataTable.ajax.reload(null, false);
    });
});



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