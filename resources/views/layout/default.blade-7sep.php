<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title>Gigabit IPTV | @yield('mytitle')</title>
    <link rel="icon" type="image/x-icon" href="{{asset('theme/assets/img/favicon.ico')}}"/>
    <link href="{{asset('theme/assets/css/loader.css')}}" rel="stylesheet" type="text/css" />
    <script src="{{asset('theme/assets/js/loader.js')}}"></script>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">
    <link href="{{asset('theme/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('theme/assets/css/plugins.css')}}" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->

    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM STYLES -->
    <link href="{{asset('theme/plugins/apex/apexcharts.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('theme/assets/css/dashboard/dash_1.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('theme/assets/css/dashboard/dash_2.css')}}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" type="text/css" href="{{asset('theme/plugins/table/datatable/datatables.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('theme/assets/css/forms/theme-checkbox-radio.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('theme/plugins/table/datatable/dt-global_style.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('theme/plugins/table/datatable/dt-global_style.css')}}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="{{asset('theme/assets/css/forms/switches.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('theme/assets/css/elements/tooltip.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/styles.css')}}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">

    <link href="{{asset('theme/plugins/flatpickr/flatpickr.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('theme/plugins/flatpickr/custom-flatpickr.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('theme/assets/css/users/user-profile.css')}}" rel="stylesheet" type="text/css" />

    <script src="{{asset('theme/plugins/sweetalerts/promise-polyfill.js')}}"></script>
    <link href="{{asset('theme/plugins/sweetalerts/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('theme/plugins/sweetalerts/sweetalert.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('theme/assets/css/components/custom-sweetalert.css')}}" rel="stylesheet" type="text/css" />

    <link href="{{asset('theme/assets/css/scrollspyNav.css')}}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="{{asset('theme/plugins/jquery-step/jquery.steps.css')}}">

    <link href="{{asset('theme/plugins/tagInput/tags-input.css')}}" rel="stylesheet" type="text/css" />

    <!-- END PAGE LEVEL PLUGINS/CUSTOM STYLES -->


</head>
<body>
    <style type="text/css">
        .error{
            color: red;
        }
    </style>
    <div id="loader-screen" style="display:none;position: fixed;top: 0;left: 0;height: 100vh;width: 100vw;z-index: 99;">
        <div class="loader_2" style="margin: auto;"></div>
    </div>
    <!-- BEGIN LOADER -->
    <div id="load_screen"> <div class="loader"> <div class="loader-content">
        <div class="spinner-grow align-self-center"></div>
    </div></div></div>
    <!--  END LOADER -->

    <!--  BEGIN NAVBAR  -->
    <div>
      @include('layout.header')
    </div>
    <!--  END NAVBAR  -->

    <!--  BEGIN MAIN CONTAINER  -->
    <div class="main-container" id="container">

        <div class="overlay"></div>
        <div class="search-overlay"></div>

        <!--  BEGIN SIDEBAR  -->
        <div class="sidebar-wrapper sidebar-theme">
            @include('layout.sidebar')
        </div>
        <!--  END SIDEBAR  -->

        <!--  BEGIN CONTENT AREA  -->
        <div id="content" class="main-content">
            @yield('content')

            <div class="footer-wrapper">
                <div class="footer-section f-section-1">
                    <p class="">Copyright © 2020 <a target="_blank" href="https://designreset.com">DesignReset</a>, All rights reserved.</p>
                </div>
                <div class="footer-section f-section-2">
                    <p class="">Coded with <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-heart"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg></p>
                </div>
            </div>
        </div>
        <!--  END CONTENT AREA  -->

    </div>
    <!-- END MAIN CONTAINER -->


    <script src="{{asset('theme/plugins/flatpickr/flatpickr.js')}}"></script>
    <script src="{{asset('theme/plugins/flatpickr/custom-flatpickr.js')}}"></script>
    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <script src="{{asset('theme/assets/js/libs/jquery-3.1.1.min.js')}}"></script>
    <script src="{{asset('theme/bootstrap/js/popper.min.js')}}"></script>
    <script src="{{asset('theme/bootstrap/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('theme/plugins/perfect-scrollbar/perfect-scrollbar.min.js')}}"></script>
    <script src="{{asset('theme/assets/js/app.js')}}"></script>
    <script src="{{asset('theme/assets/js/multiselect.js')}}"></script>
    <script src="{{asset('theme/assets/js/elements/tooltip.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.js"></script>
    <script>
        $(document).ready(function() {
            App.init();
            // var f1 = document.getElementById('basicFlatpickr');
            // var f2 = document.getElementById('basicFlatpickr1');
            // f1.flatpickr({
            //     dateFormat: "d-m-Y",
            //     defaultDate: ["01-06-2022"],
            //     minDate: '01-06-2022'
            // });
            // f2.flatpickr({
            //     dateFormat: "d-m-Y",
            //     defaultDate: "today",
            //     minDate: '01-06-2022',
            //     maxDate: 'today',
            // });
        });
    </script>
    <script src="{{asset('theme/assets/js/custom.js')}}"></script>
    <!-- END GLOBAL MANDATORY SCRIPTS -->

    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->
    <script src="{{asset('theme/plugins/apex/apexcharts.min.js')}}"></script>
    <script src="{{asset('theme/assets/js/dashboard/dash_1.js?t='.time())}}"></script>
    <script src="{{asset('theme/assets/js/dashboard/dash_2.js')}}"></script>
    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->

    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="{{asset('theme/plugins/table/datatable/datatables.js')}}"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="{{asset('theme/plugins/sweetalerts/sweetalert2.min.js')}}"></script>
    <script src="{{asset('theme/plugins/sweetalerts/custom-sweetalert.js')}}"></script>
    
    <script src="{{asset('theme/assets/js/scrollspyNav.js')}}"></script>
    <script src="{{asset('theme/plugins/jquery-step/jquery.steps.min.js')}}"></script>
    <script src="{{asset('theme/plugins/jquery-step/custom-jquery.steps.js')}}"></script>
    
    <script src="{{asset('theme/plugins/tagInput/tags-input.js')}}"></script>


    <!-- END PAGE LEVEL SCRIPTS -->

    <div class="modal fade" id="delete_blog_modal">
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
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-primary" data-dismiss="modal">No</button>
              <button type="button" class="btn btn-danger" onclick="ajax_delete_item();" >Yes, Delete</button>
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <script type="text/javascript">
        // ['d_title','d_body','url_enpoint']
        var allPages = {
            "role" : ['Role','role','role'],
            "admin" : ['Admin','admin','admin'],
            "user" : ['User','user','user'],
            "language" : ['Language','language','language'],
            "genre" : ['Genre','genre','genre'],
            "channel" : ['Channel','channel','channel'],
            "sadminPlan" : ['Plan','plan','sadminPlan'],
            "adminPlan" : ['Plan','plan','adminPlan'],
            "slider" : ['Slider','slider','slider'],
            "reseller" : ['Reseller','reseller','reseller'],
        }
        //Delete building using ajax
        window.bd_id = 0;
        window.url_enpoint = '';
        function delete_item(id,page){
            bd_id = id;
            url_enpoint = allPages[page][2];
            $('#d_title').text(allPages[page][0])
            $('#d_body').text(allPages[page][1])
            $("#delete_blog_modal").modal('show');
        }

        function ajax_delete_item(){
            var request = $.ajax({
                            url: "/"+url_enpoint+"/destroy",
                            method: "POST",
                            data: { "_token": "{{ csrf_token() }}", id : bd_id }
                          });

            request.done(function( val ) {
                console.log(val);
                var data = jQuery.parseJSON(val);
                $("#delete_blog_modal").modal('hide');
                $( "#delete_bd_ms" ).html(data.message);
                $('#multi-column-ordering').DataTable().ajax.reload();
                // setTimeout(function(){location.reload(true);}, 2000);

            });
        }

        function updateStatus(url) {
            // body...
            // alert(id)
            var request = $.ajax({
                            url: url,
                            method: "GET"
                          });

            request.done(function( val ) {
                console.log(val);
                var data = jQuery.parseJSON(val);
                $("#delete_blog_modal").modal('hide');
                $( "#delete_bd_ms" ).html(data.message);
                $('#multi-column-ordering').DataTable().ajax.reload();
                // setTimeout(function(){location.reload(true);}, 2000);

            });
        }

        $(function(){
          $('#user-form').validate(

          ); //valdate end
        });
    </script>

    <script type="text/javascript">

        function updateIcon() {
            // alert('')
            $('li.previous > a').html('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>')

            $('li.next > a').html('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>')
        }

        $(document).ready(function() {
            $('.select').select2();
        });

        $(document).ready(function() {


            $('#undo_redo').multiselect({
                search: {
                    left: '<input type="text" name="q" class="form-control" placeholder="Search..." />',
                }
            })
            $('#undo_redo_rightSelected').click()
        });

    </script>
    @yield('footer')
</body>
</html>
