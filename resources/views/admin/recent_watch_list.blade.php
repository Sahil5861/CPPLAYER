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
        {{-- <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-12 layout-spacing">
            <div class="widget widget-card-four">
                <div class="widget-content">
                    <div class="w-content">
                        <div class="w-info">
                            <p class=""><small>Total Viewers</small></p>
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
        </div> --}}
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
                {{-- <div class="text-left" style="display: flex; justify-content:flex-end;align-items:center; gap:10px; width:30%;">
                </div> --}}
                {{-- <div class="text-left" style="display: flex; justify-content:flex-end;align-items:center; gap:10px;">
                    <select name="content_network" id="content_network" class="form-control w-25" style="width: 200px !important;" onchange="updateTable(this);">
                        <option value="">--Select--</option>
                        
                    </select>
                                                          
                </div>                 --}}
                <div class="table-responsive mb-4 mt-4">
                    
                    <table id="multi-column-ordering" class="table table-hover" data-table="movies">
                        <thead>
                            <tr>                                
                                <th>S.no</th>
                                <th>User</th>
                                <th>User Of</th>
                                <th>Video Title</th>                                                                                                                         
                                <th>Content Type</th>                                                                                                                         
                                <th>Category</th>  
                                <th>Play</th>                                                                                                                                                       
                                <th>Play Time</th>                                
                            </tr>
                        </thead>
                        <tbody id="tableItem">
                            
                        </tbody>
                        <tfoot>
                            <tr>                                
                                <th>S.no</th>
                                <th>User</th>
                                <th>User Of</th>
                                <th>Video Title</th>  
                                <th>Content Type</th>                                                                                                                                                                                                                                              
                                <th>Category</th> 
                                <th>Play</th>                                                                                                                                                        
                                <th>Play Time</th>                                
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

<script src="cdn.datatables.net/plug-ins/1.12.1/sorting/date-uk.js"></script>



<script type="text/javascript">



function initializeDataTable(network_id = '') {
    dataTable = $('#multi-column-ordering').DataTable({
        processing: true,
        serverSide: true,
        destroy: true, // destroy on re-initialize
        stateSave: true, 

        order: [[0, 'desc']],
        ajax: {
            url: "{{ route('getrecentWatchList') }}",            
        },
        columns: [                    
            { data: 's_no'},  
            { data: 'user_id'},  
            { data: 'created_by'},  
            { data: 'event_title'},                                    
            { data: 'content_type'},                                    
            { data: 'category', orderable: false, searchable: false },            
            { data: 'play_btn', orderable: false, searchable: false },            
            { data: 'server_time', orderable: false, searchable: false }
        ]       
        
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

    $('#select_netword_id').on('change', function() {
        dataTable.ajax.reload(null, false);
    });


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

</script>

   
<!-- footer script if required -->
@endsection