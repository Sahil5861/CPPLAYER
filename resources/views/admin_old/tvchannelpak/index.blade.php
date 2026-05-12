@extends('layout.default')
@section('mytitle', 'Tv Channel List')
@section('page', 'Tv Channel / List')
@section('content')
<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
            <div class="widget-content widget-content-area br-6">
                <div id="delete_bd_ms"></div>

                @if(session()->has('message'))
                    <div class="alert alert-success alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>    
                        <strong>{{ session()->get('message') }}</strong>
                    </div>
                @endif

                <div class="row d-flex justify-content-start align-items-center">                    
                    <div class="col-md-2">
                        <select name="select_status" id="select_status" class="form-control w-25 select" style="width: 25%;">
                            <option value="">--Filter by Status--</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="text-right">
                    <a href="{{url('tv-channel-pak-order')}}" class="btn btn-primary mb-2">Order Channels</a>
                    <a href="{{ url('add-tv-channel-pak') }}" class="btn btn-primary mb-2">Add +</a>
                </div>

                <div class="table-responsive mb-4 mt-4">
                    <table id="multi-column-ordering" class="table table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Logo</th>
                                <th>Language</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Logo</th>
                                <th>Language</th>
                                <th>Description</th>
                                <th>Status</th>
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
<script>
$(document).ready(function() {
    var table = $('#multi-column-ordering').DataTable({
        processing: true,
        serverSide: true,
        order: [],
        language: {
            infoFiltered: ''
        },
        ajax: {
            url: "{{ route('getTvChannelpakList') }}",
            data: function(d) {
                let status = $('#select_status').val();
                let network_id = $('#select_netword_id').val();

                if (status !== '') {
                    d.status = status;
                }
            },
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { 
                data: 'logo', 
                name: 'logo',
                render: function(data) {
                    if (data) {
                        return '<img src="' + data + '" alt="Logo" height="40">';
                    } else {
                        return '—';
                    }
                },
                orderable: false,
                searchable: false
            },
            { data: 'language', name: 'language' },
            { 
                data: 'description', 
                name: 'description',
                render: function(data) {
                    return data ? data.substring(0, 50) + '...' : '';
                }
            },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        drawCallback: function(settings) {
            if (typeof updateIcon === 'function') {
                updateIcon(); // Only if your project uses Feather or similar
            }
        }
    });

    $("#search").on("keyup", function (event) {
        if ($('#search').val().length >= 3 || $('#search').val().length == 0) {
            table.draw();
            event.preventDefault();
        }
    });

    $("#btn-search").click(function (a) {
        table.draw();
        a.preventDefault();
    });
});
    
    $('#select_status').on('change', function() {                    
        $('#multi-column-ordering').DataTable().ajax.reload(null, false);
    });

    function deleteRowModal(id){ 
        $('#d_title').text('TV Channel Pak')
        $('#d_id').val(id);
        $('#delete_modal').modal('show');        
    }

    function delete_row(){
        var id = $('#d_id').val();
        $.ajax({
            type: 'POST',
            url: "{{route('tvchannelpak.destroy')}}",
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
</script>
@endsection
