<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title>Acom TV | @yield('mytitle')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('theme/assets/img/acom-black.png') }}" />
    <link href="{{ asset('theme/assets/css/loader.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('theme/assets/js/loader.js') }}"></script>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">
    <link href="{{ asset('theme/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/assets/css/plugins.css') }}" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->

    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM STYLES -->
    <link href="{{ asset('theme/plugins/apex/apexcharts.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('theme/assets/css/dashboard/dash_1.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/assets/css/dashboard/dash_2.css') }}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" type="text/css" href="{{ asset('theme/plugins/table/datatable/datatables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('theme/assets/css/forms/theme-checkbox-radio.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('theme/plugins/table/datatable/dt-global_style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('theme/plugins/table/datatable/dt-global_style.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="{{ asset('theme/assets/css/forms/switches.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('theme/assets/css/elements/tooltip.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/styles.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">

    <link href="{{ asset('theme/plugins/flatpickr/flatpickr.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('theme/plugins/flatpickr/custom-flatpickr.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('theme/assets/css/users/user-profile.css') }}" rel="stylesheet" type="text/css" />

    <script src="{{ asset('theme/plugins/sweetalerts/promise-polyfill.js') }}"></script>
    <link href="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/plugins/sweetalerts/sweetalert.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/assets/css/components/custom-sweetalert.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('theme/assets/css/scrollspyNav.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="{{ asset('theme/plugins/jquery-step/jquery.steps.css') }}">

    <link href="{{ asset('theme/plugins/tagInput/tags-input.css') }}" rel="stylesheet" type="text/css" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- END PAGE LEVEL PLUGINS/CUSTOM STYLES -->

    <style>
        .custom-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .custom-modal.show {
            display: flex;
        }

        .modal-box {
            background: #020617;
            padding: 25px;
            border-radius: 14px;
            width: 350px;
            border: 1px solid #111827;
            text-align: center;
        }

        .modal-title {
            color: #e5e7eb;
            margin-bottom: 10px;
        }

        .modal-text {
            color: #9ca3af;
            margin-bottom: 20px;
        }

        .modal-actions {
            display: flex;
            gap: 10px;
            justify-content: center;
        }


        /* Toast Container */
        #customToast {
            min-width: 280px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            font-size: 14px;
            overflow: hidden;
        }

        /* Toast Body */
        #customToast .toast-body {
            padding: 12px 16px;
            font-weight: 500;
        }

        /* Close Button */
        #customToast .btn-close {
            filter: invert(1);
            opacity: 0.8;
        }

        #customToast .btn-close:hover {
            opacity: 1;
        }

        /* Success (Green) */
        #customToast.bg-success {
            background: linear-gradient(135deg, #28a745, #218838);
        }

        /* Error (Red) */
        #customToast.bg-danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
        }

        /* Warning (Yellow) */
        #customToast.bg-warning {
            background: linear-gradient(135deg, #ffc107, #e0a800);
            color: #000;
        }

        /* Smooth Animation */
        .toast.show {
            animation: slideIn 0.4s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>


</head>

<body>
    <style type="text/css">
        .error {
            color: red;
        }
    </style>
    <div id="loader-screen" style="display:none;position: fixed;top: 0;left: 0;height: 100vh;width: 100vw;z-index: 99;">
        <div @class(['loader_2']) style="margin: auto;"></div>
    </div>
    <!-- BEGIN LOADER -->
    <div id="load_screen">
        <div @class(['loader'])>
            <div @class(['loader-content'])>
                <div @class(['spinner-grow', 'align-self-center'])></div>
            </div>
        </div>
    </div>
    <!--  END LOADER -->

    <!--  BEGIN NAVBAR  -->
    <div>
        @include('layout.header')
    </div>
    <!--  END NAVBAR  -->

    <!--  BEGIN MAIN CONTAINER  -->
    <div @class(['main-container']) id="container">

        <div @class(['overlay'])></div>
        <div @class(['search-overlay'])></div>

        <!--  BEGIN SIDEBAR  -->
        <div @class(['sidebar-wrapper', 'sidebar-theme'])>
            @include('layout.sidebar')
        </div>
        <!--  END SIDEBAR  -->

        <!--  BEGIN CONTENT AREA  -->
        <div id="content" @class(['main-content'])>
            @yield('content')
            <style>
                .ytp-expand-pause-overlay .ytp-pause-overlay {
                    display: none !important;
                    visibility: hidden !important;
                }

                .ytp-chrome-controls .ytp-button.ytp-youtube-button,
                .ytp-small-mode .ytp-chrome-controls .ytp-button.ytp-youtube-button,
                .ytp-embed .ytp-chrome-controls .ytp-button.ytp-youtube-button,
                .ytp-embed.ytp-small-mode .ytp-chrome-controls .ytp-button.ytp-youtube-button,
                .ytp-dni.ytp-embed .ytp-chrome-controls .ytp-button.ytp-youtube-button {
                    display: none !important;
                    visibility: hidden !important;
                }

                .ytp-chrome-top-buttons {
                    display: none !important;
                    visibility: hidden !important;
                }
            </style>

            @include('partials.dt-loader')

            <div @class(['modal', 'fade']) id="videoModal" tabindex="-1" role="dialog"
                aria-labelledby="addContentModalLabel" aria-hidden="true">
                <div @class(['modal-dialog', 'modal-xl', 'modal-dialog-centered']) role="document">
                    <div @class(['modal-content'])>

                        <div @class(['modal-body'])>
                            <div @class(['container-fluid', 'p-3', 'bg-dark'])>
                                <video id="videoPlayer" width="100%" controls style="display:none;"></video>
                                <iframe frameborder="0" @class(['w-100']) style="height: 600px;"
                                    id="video-player" allowfullscreen allow="autoplay"></iframe>
                            </div>
                        </div>

                        <div @class(['modal-footer'])>
                            <button type="button" @class(['btn', 'btn-secondary']) data-dismiss="modal">Close</button>
                        </div>

                    </div>
                </div>
            </div>

            <div @class(['modal', 'fade']) id="revertModal" tabindex="-1">
                <div @class(['modal-dialog', 'modal-dialog-centered'])>
                    <div @class(['modal-content'])>

                        <div @class(['modal-header'])>
                            <h5 @class(['modal-title', 'text-dark'])>Revert Amount</h5>
                            <button type="button" @class(['btn-close'])
                                data-bs-dismiss="modal">&times;</button>
                        </div>

                        <div @class(['modal-body'])>

                            <p @class(['text-muted', 'mb-2'])>
                                Max revert amount: <strong id="maxAmountText"></strong>
                            </p>

                            <input type="number" id="revertInput" @class(['form-control'])
                                placeholder="Enter amount">

                            <small id="errorText" @class(['text-danger', 'd-none'])></small>

                        </div>

                        <div @class(['modal-footer'])>
                            <button @class(['btn', 'btn-secondary', 'btn-close']) data-bs-dismiss="modal">Cancel</button>
                            <button id="confirmRevert" @class(['btn', 'btn-danger'])>Revert</button>
                        </div>

                    </div>
                </div>
            </div>

            <div @class(['modal', 'fade']) id="freezeModal" tabindex="-1">
                <div @class(['modal-dialog', 'modal-dialog-centered'])>
                    <div @class(['modal-content'])>

                        <div @class(['modal-header'])>
                            <h5 @class(['modal-title', 'text-dark'])>Freeze This Account</h5>
                            <button type="button" @class(['btn-close'])
                                data-bs-dismiss="modal">&times;</button>
                        </div>

                        <div @class(['modal-body'])>

                            <p @class(['text-danger', 'fw-bold'])>
                                ⚠️ Warning: You are about to freeze this user account.
                            </p>

                            <p>
                                Once frozen:
                                <br>• User will be able to access the system but with minimum features
                                <br>• Features alike manage users and manage packages will not work
                                <br>• This action may affect active plans and balance usage
                            </p>

                            <p @class(['mb-0'])>
                                Are you sure you want to continue?
                            </p>

                            <small id="errorText" @class(['text-danger', 'd-none'])></small>

                        </div>

                        <div @class(['modal-footer'])>
                            <button @class(['btn', 'btn-secondary', 'btn-close']) data-bs-dismiss="modal">Cancel</button>
                            <button id="confirmFreeze" @class(['btn', 'btn-danger'])>Continue</button>
                        </div>

                    </div>
                </div>
            </div>

            <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
                <div id="customToast" class="toast align-items-center text-white bg-success border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body" id="toastMessage">
                            Success message
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto"
                            data-bs-dismiss="toast"></button>
                    </div>
                </div>
            </div>


            <div @class(['modal', 'fade']) id="videoModal_m3u8" tabindex="-1" role="dialog"
                aria-labelledby="addContentModalLabel" aria-hidden="true">
                <div @class(['modal-dialog', 'modal-xl', 'modal-dialog-centered']) role="document">
                    <div @class(['modal-content'])>

                        <div @class(['modal-body'])>
                            <div @class(['container-fluid', 'p-3', 'bg-dark'])>
                                <video id="videoPlayer" preload="metadata">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        </div>

                        <div @class(['modal-footer'])>
                            <button type="button" @class(['btn', 'btn-secondary']) data-dismiss="modal">Close</button>
                        </div>

                    </div>
                </div>
            </div>

            <div @class(['footer-wrapper'])>
                <div @class(['footer-section', 'f-section-1'])>
                    <p @class(['">Copyright', '©', '2020', '<a', 'target='])_blank" href="https://designreset.com">DesignReset</a>, All rights
                        reserved.</p>
                </div>
                <div @class(['footer-section', 'f-section-2'])>
                    <p @class(['">Coded', 'with', '<svg', 'xmlns='])http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" @class(['feather', 'feather-heart'])>
                        <path
                            d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z">
                        </path>
                        </svg>
                    </p>
                </div>
            </div>
        </div>
        <!--  END CONTENT AREA  -->

    </div>
    <!-- END MAIN CONTAINER -->


    <!-- <script src="{{ asset('theme/plugins/flatpickr/flatpickr.js') }}"></script> -->
    <!-- <script src="{{ asset('theme/plugins/flatpickr/custom-flatpickr.js') }}"></script> -->
    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="{{ asset('theme/assets/js/libs/jquery-3.1.1.min.js') }}"></script>
    <script src="{{ asset('theme/bootstrap/js/popper.min.js') }}"></script>
    <script src="{{ asset('theme/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('theme/assets/js/app.js') }}"></script>
    <script src="{{ asset('theme/assets/js/multiselect.js') }}"></script>
    <script src="{{ asset('theme/assets/js/elements/tooltip.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.js"></script>

    <script>
        let revertUrl = '';
        let maxAmount = 0;


        function showToast(message, type = 'success') {

            let toastEl = document.getElementById('customToast');

            // remove old classes
            toastEl.classList.remove('bg-success', 'bg-danger', 'bg-warning');

            // set new type
            if (type === 'success') {
                toastEl.classList.add('bg-success');
            } else if (type === 'error') {
                toastEl.classList.add('bg-danger');
            } else {
                toastEl.classList.add('bg-warning');
            }

            document.getElementById('toastMessage').innerText = message;

            let toast = new bootstrap.Toast(toastEl);
            toast.show();
        }


        function openRevertModal(el) {
            revertUrl = $(el).data('url');
            maxAmount = parseFloat($(el).data('amount'));

            $('#maxAmountText').text(maxAmount);
            $('#revertInput').val('');
            $('#errorText').addClass('d-none').text('');

            $('#revertModal').modal('show');
        }

        $(document).on('click', '.btn-close', function() {
            $('#revertModal').modal('hide');
        });


        let freezeActionUrl = null;
        let freezeModal = new bootstrap.Modal(document.getElementById('freezeModal'));

        function handleFreezeToggle(el) {

            let status = el.getAttribute('data-status');
            let url = el.getAttribute('data-url');

            if (status == "0") {
                // 👉 Open modal
                freezeActionUrl = url;

                freezeModal.show();

                // revert toggle UI
                el.checked = false;

            } else {
                // 👉 Direct unfreeze
                updateFreezeStatus(url);
            }
        }


        function updateFreezeStatus(url) {
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {

                    // alert(res.message || 'User frozen successfully');
                    // showToast(res.message || 'Status updated successfully', 'success');

                    $('#multi-column-ordering').DataTable().ajax.reload(null, false);

                },
                error: function() {
                    alert('Something went wrong');
                }
            });
        }


        $('#confirmFreeze').on('click', function() {

            let btn = $(this);
            btn.prop('disabled', true).text('Processing...');

            $.ajax({
                url: freezeActionUrl,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {

                    freezeModal.hide();

                    // alert(res.message || 'User frozen successfully');
                    // showToast(res.message || 'Status updated successfully', 'success');

                    $('#multi-column-ordering').DataTable().ajax.reload(null, false);
                },
                error: function() {
                    alert('Something went wrong');
                },
                complete: function() {
                    btn.prop('disabled', false).text('Freeze');
                }
            });

        });


        // CONFIRM CLICK
        $('#confirmRevert').on('click', function() {

            let value = parseFloat($('#revertInput').val());
            let error = $('#errorText');

            // ❌ validation
            if (!value || value <= 0) {
                error.text('Enter valid amount').removeClass('d-none');
                return;
            }

            if (value > maxAmount) {
                error.text('Amount cannot exceed ' + maxAmount).removeClass('d-none');
                return;
            }

            // disable button (prevent double click)
            let btn = $(this);
            btn.prop('disabled', true).text('Processing...');

            // ✅ AJAX CALL
            $.ajax({
                url: revertUrl,
                type: 'POST',
                data: {
                    amount: value,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    $('#revertModal').modal('hide');

                    // reload or update UI
                    location.reload();
                },
                error: function(err) {
                    error.text('Something went wrong').removeClass('d-none');
                },
                complete: function() {
                    btn.prop('disabled', false).text('Revert');
                }
            });

        });
    </script>
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

        function deleteMulti(table) {
            let checked = $('.row-checkbox:checked').map(function() {
                return $(this).val();
            }).get();


            if (checked.length === 0) {
                alert('Please select at least one record to delete.');
                return;
            }

            // Confirm before deleting
            if (!confirm('Are you sure you want to delete selected records?')) {
                return;
            }

            $.ajax({
                url: "{{ route('delete-multi') }}",
                type: 'POST',
                data: {
                    ids: checked,
                    table: table,
                    _token: $('meta[name="csrf-token"]').attr('content') // Laravel CSRF token
                },

                success: function(response) {
                    if (response.status == true) {
                        $('.success-message').html(`${response.message}`);
                        $('#alert-success').show();
                        dataTable.ajax.reload();
                        setTimeout(() => {
                            $('#alert-success').hide();
                        }, 2000);
                    } else {
                        console.log(response);
                        $('.error-message').html(`${response.message}`);
                        $('#alert-danger').show();
                        setTimeout(() => {
                            $('#alert-danger').hide();
                        }, 2000);
                    }
                }
            })
        }



        function updateMultistatus(table) {
            let status = $('#statusSelect').val();

            if (status == '') {
                return;
            }
            let checked = $('.row-checkbox:checked').map(function() {
                return $(this).val();
            }).get();


            if (checked.length === 0) {
                alert('Please select at least one record to delete.');
                return;
            }


            // Confirm before deleting
            if (!confirm('Are you sure you want to update records?')) {
                return;
            }


            $.ajax({
                url: "{{ route('update-multi-status') }}",
                type: 'POST',
                data: {
                    ids: checked,
                    table: table,
                    status: status,
                    _token: $('meta[name="csrf-token"]').attr('content') // Laravel CSRF token
                },

                success: function(response) {
                    if (response.status == true) {
                        $('.success-message').html(`${response.message}`);
                        $('#alert-success').show();
                        $('#statusSelect').val('').trigger('change');
                        dataTable.ajax.reload();
                        setTimeout(() => {
                            $('#alert-success').hide();
                        }, 2000);
                    } else {
                        console.log(response);
                        $('.error-message').html(`${response.message}`);
                        $('#alert-danger').show();
                        setTimeout(() => {
                            $('#alert-danger').hide();
                        }, 2000);
                    }
                }
            })




        }

        // Event listener for "Select All" checkbox
    </script>



    <script>
        document.addEventListener("DOMContentLoaded", function() {
            flatpickr(".flat_picker", {
                enableTime: true,
                enableSeconds: true,
                noCalendar: true,
                dateFormat: "H:i:s", // e.g., 13:45
                time_24hr: true,
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <script>
        function m3u8Player(url) {
            const video = document.getElementById('videoPlayer');
            const iframe = document.getElementById('video-player');

            // Hide iframe and show video tag
            iframe.style.display = 'none';
            video.style.display = 'block';

            // Clean up any previous HLS instance
            if (window.hls) {
                window.hls.destroy();
            }

            if (Hls.isSupported()) {
                window.hls = new Hls();
                window.hls.loadSource(url);
                window.hls.attachMedia(video);
                window.hls.on(Hls.Events.MANIFEST_PARSED, function() {
                    video.play();
                });
            } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                video.src = url;
                video.addEventListener('loadedmetadata', function() {
                    video.play();
                });
            } else {
                alert('Your browser does not support HLS playback');
                return;
            }

            $('#videoModal').modal('show');
        }

        function openVideoModal(element) {
            const videoId = element.getAttribute('data-video-id');
            const video = document.getElementById('videoPlayer');
            const iframe = document.getElementById('video-player');

            if (videoId && videoId.toLowerCase().endsWith('.m3u8')) {
                m3u8Player(videoId);
            } else {
                // Hide video, show iframe
                video.style.display = 'none';
                iframe.style.display = 'block';

                const videoSrc = `https://www.youtube.com/embed/${videoId}?autoplay=1`;
                iframe.src = videoSrc;
                $('#videoModal').modal('show');
            }

            // Clean up on modal close
            $('#videoModal').off('hidden.bs.modal').on('hidden.bs.modal', function() {
                video.pause();
                video.removeAttribute('src');
                video.load();

                iframe.src = '';

                if (window.hls) {
                    window.hls.destroy();
                    window.hls = null;
                }
            });
        }

        $('.close-video-modal').on('click', function() {
            $('#videoModal').modal('hide');
        });
    </script>

    <script src="{{ asset('theme/assets/js/custom.js') }}"></script>
    <!-- END GLOBAL MANDATORY SCRIPTS -->

    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->
    <script src="{{ asset('theme/plugins/apex/apexcharts.min.js') }}"></script>
    <script src="{{ asset('theme/assets/js/dashboard/dash_1.js?t=' . time()) }}"></script>
    <script src="{{ asset('theme/assets/js/dashboard/dash_2.js') }}"></script>
    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->

    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="{{ asset('theme/plugins/table/datatable/datatables.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="{{ asset('theme/plugins/sweetalerts/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/sweetalerts/custom-sweetalert.js') }}"></script>

    <script src="{{ asset('theme/assets/js/scrollspyNav.js') }}"></script>
    <script src="{{ asset('theme/plugins/jquery-step/jquery.steps.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/jquery-step/custom-jquery.steps.js') }}"></script>

    <script src="{{ asset('theme/plugins/tagInput/tags-input.js') }}"></script>


    <!-- END PAGE LEVEL SCRIPTS -->

    <div @class(['modal', 'fade']) id="delete_blog_modal">
        <div @class(['modal-dialog', 'action-modal'])>
            <div @class(['modal-content'])>
                <div @class(['modal-header'])>
                    <h5 @class(['modal-title']) id="d_title"></h5>
                    <button type="button" @class(['close', 'close-video-modal']) data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div @class(['modal-body'])>
                    <p id="delete_user_deletemsg">Do you want to delete this <span id="d_body"></span>?</p>
                </div>
                <div @class(['modal-footer', 'justify-content-between'])>
                    <button type="button" @class(['btn', 'btn-primary']) data-dismiss="modal">No</button>
                    <button type="button" @class(['btn', 'btn-danger']) onclick="ajax_delete_item();">Yes,
                        Delete</button>
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
            "role": ['Role', 'role', 'role'],
            "admin": ['Admin', 'admin', 'admin'],
            "reseller": ['Reseller', 'reseller', 'reseller'],
            "retailor": ['Retailor', 'retailor', 'retailor'],
            "user": ['User', 'user', 'user'],
            "language": ['Language', 'language', 'language'],
            "genre": ['Genre', 'genre', 'genre'],
            "channel": ['Channel', 'channel', 'channel'],
            "movie": ['Movie', 'movie', 'movie'],
            "movieLink": ['Movie Link', 'movie Link', 'movieLink'],
            "sadminPlan": ['Plan', 'plan', 'sadminPlan'],
            "adminPlan": ['Plan', 'plan', 'adminPlan'],
            "slider": ['Slider', 'slider', 'slider'],
            "reseller": ['Reseller', 'reseller', 'reseller'],
            "resellerPlan": ['Reseller Plan', 'reseller plan', 'resellerPlan'],
            "retailorPlan": ['Retailor Plan', 'retailor plan', 'retailorPlan'],
            "netadmin": ['NetAdmin', 'netadmin', 'netadmin'],
            "network": ['Network', 'network', 'network'],

        }
        //Delete building using ajax
        window.bd_id = 0;
        window.url_enpoint = '';

        function delete_item(id, page) {

            bd_id = id;
            url_enpoint = allPages[page][2];
            $('#d_title').text(allPages[page][0])
            $('#d_body').text(allPages[page][1])
            $("#delete_blog_modal").modal('show');
        }

        function ajax_delete_item() {

            // alert('hii !!');
            var request = $.ajax({
                url: url_enpoint + "/destroy",
                method: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    id: bd_id
                }
            });

            request.done(function(val) {

                // console.log(val);
                // console.log('-------------------');

                // var data = jQuery.parseJSON(val);

    
                $("#delete_blog_modal").modal('hide');


                $("#delete_bd_ms").html('Item Deleted successfully !');
                // $('#multi-column-ordering').DataTable().ajax.reload();
                if ($.fn.DataTable.isDataTable('#multi-column-ordering')) {
                    $('#multi-column-ordering').DataTable().ajax.reload(null, false); // ✅ stay on same page
                } else {
                    // fallback: reload full page
                    window.location.reload();
                }
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

            request.done(function(val) {
                console.log(val);
                var data = jQuery.parseJSON(val);
                $("#delete_blog_modal").modal('hide');
                $("#delete_bd_ms").html(data.message);
                // $('#multi-column-ordering').DataTable().ajax.reload();
                // setTimeout(function(){location.reload(true);}, 2000);
            });
        }

        $(function() {
            $('#user-form').validate(

            ); //valdate end
        });
    </script>

    <script type="text/javascript">
        function updateIcon() {
            // alert('')
            $('li.previous > a').html(
                '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" @class(['feather', 'feather-arrow-left'])><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>'
            )

            $('li.next > a').html(
                '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" @class(['feather', 'feather-arrow-right'])><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>'
            )
        }

        $(document).ready(function() {
            $('.select').select2();
        });

        $(document).ready(function() {


            $('#undo_redo').multiselect({
                search: {
                    left: '<input type="text" name="q" @class(['form-control']) placeholder="Search..." />',
                }
            })
            $('#undo_redo_rightSelected').click()
        });
    </script>

    @yield('footer')
</body>

</html>
