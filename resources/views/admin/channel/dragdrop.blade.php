@extends('layout.default')
@section('mytitle', 'Admin List')
@section('page', 'Channels / Order List')

@section('content')
    <style type="text/css">
        .no-move .media {
            opacity: 0.8;
            background: #333;
        }

        /* ── Full screen loader overlay ── */
        #page-loader {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            z-index: 99999;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            gap: 16px;
        }

        #page-loader.active {
            display: flex;
        }

        #page-loader .loader-spinner {
            width: 56px;
            height: 56px;
            border: 5px solid rgba(255, 255, 255, 0.2);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.75s linear infinite;
        }

        #page-loader .loader-text {
            color: #fff;
            font-size: 15px;
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>

    <link href="{{ asset('theme/plugins/drag-and-drop/dragula/dragula.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/plugins/drag-and-drop/dragula/example.css') }}" rel="stylesheet" type="text/css" />

    {{-- ── FULL SCREEN LOADER ── --}}
    <div id="page-loader">
        <div class="loader-spinner"></div>
        <div class="loader-text" id="loader-text">Saving...</div>
    </div>

    <div class="layout-px-spacing">
        <div class="row" id="cancel-row">
            <div class="col-lg-12 layout-spacing layout-top-spacing">
                <form id="order-form" method="post" action="{{ route('saveChannelOrders') }}" enctype="multipart/form-data"
                    novalidate class="simple-example">
                    @csrf

                    {{-- Filter values — AJAX submit ke waqt sync honge --}}
                    <input type="hidden" id="form_stream_type" name="stream_type" value="">
                    <input type="hidden" id="form_genre" name="genre" value="">
                    <input type="hidden" id="form_language" name="language" value="">

                    <div class="statbox widget box box-shadow">
                        <div class="widget-header">
                            <div class="row d-flex align-items-center justify-content-between">
                                <div class="col-xl-4 col-md-4 col-sm-12 col-12">
                                    <h4>Set Channel Order</h4>
                                    <h4 class="text-muted total-count-display">Total Channels: {{ $total }}</h4>
                                </div>
                            </div>

                            <div class="row">
                                {{-- FILTERS --}}
                                <div class="col-lg-7">
                                    <div class="card border-0">
                                        <div class="card-body">
                                            <h4 style="display:flex; align-items:center; gap:8px;">
                                                Filters
                                                <svg onclick="resetFilters()" xmlns="http://www.w3.org/2000/svg"
                                                    data-toggle="tooltip" title="Reset Filters" width="18"
                                                    height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    style="cursor:pointer;">
                                                    <polyline points="23 4 23 10 17 10"></polyline>
                                                    <polyline points="1 20 1 14 7 14"></polyline>
                                                    <path
                                                        d="M3.51 9a9 9 0 0114.13-3.36L23 10M1 14l5.36 4.36A9 9 0 0020.49 15">
                                                    </path>
                                                </svg>
                                            </h4>
                                            <div class="row">
                                                <div class="col-lg-4 col-sm-4">
                                                    <div class="form-group">
                                                        <select class="form-control select" id="stream_type">
                                                            <option value="">--Filter By Stream Type--</option>
                                                            <option value="M3u8">M3u8</option>
                                                            <option value="YoutubeLive">YoutubeLive</option>
                                                            <option value="Custom">Custom</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-sm-4">
                                                    <div class="form-group">
                                                        <select class="form-control select" id="genre">
                                                            <option value="">--Filter By Genre--</option>
                                                            @foreach ($genres as $genre)
                                                                <option value="{{ $genre->title }}">{{ $genre->title }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-sm-4">
                                                    <div class="form-group">
                                                        <select class="form-control select" id="language">
                                                            <option value="">--Filter By Language--</option>
                                                            @foreach ($languages as $language)
                                                                <option value="{{ $language->id }}">{{ $language->title }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- RANGE --}}
                                <div class="col-lg-5 ">
                                    <div class="card px-2 border-0">
                                        <div class="card-body">
                                            <h4>Range</h4>
                                            <div class="row">
                                                <div class="col-lg-4 col-sm-4 mb-2">
                                                    <input type="number" class="form-control" id="min_range"
                                                        name="min_range" placeholder="Min Range" min="1"
                                                        value="{{ $min_channel_no }}">
                                                </div>
                                                <div class="col-lg-4 col-sm-4 mb-2">
                                                    <input type="number" class="form-control" id="max_range"
                                                        name="max_range" placeholder="Max Range" readonly
                                                        value="{{ $total + $min_channel_no - 1 }}" min="1">
                                                </div>

                                                <div class="col-lg-4 col-sm-4 mb-2">
                                                    <button type="button" class="btn btn-primary" id="ApplyRange"
                                                        onclick="applyOrder()">Apply</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="widget-content widget-content-area">
                            <div class="parent ex-1">
                                <div class="row">
                                    <input type="hidden" id="lockedChannels" name="lockedChannels"
                                        value="{{ json_encode($lockedChannels) }}" />
                                    <input type="hidden" id="start_no" name="start_no" value="0" />
                                    <input type="hidden" id="checkOrder" name="checkOrder" value="default" />
                                    <input type="hidden" id="new_channel_no" name="new_channel_no" value="default" />
                                    <input type="hidden" id="old_channel_no" name="old_channel_no" value="0" />

                                    <div class="col-sm-12" id="_dragula"
                                        style="height: 400px; overflow-y: auto; border: 2px solid #828282">
                                        <div id="dragDropContainer">
                                            @include('admin.channel.partials.dragdrop_list', [
                                                'dataForLoop' => $dataForLoop,
                                            ])
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('footer')
    <script src="{{ asset('theme/plugins/drag-and-drop/dragula/dragula.min.js') }}"></script>
    <script src="{{ asset('theme/plugins/drag-and-drop/dragula/autoscroller.js') }}"></script>

    <script>
        let totalChannels = {{ $total }};
        let drakeInstance = null;

        // ─────────────────────────────────────────────────────────────────
        // LOADER helpers
        // ─────────────────────────────────────────────────────────────────
        function showLoader(msg) {
            document.getElementById('loader-text').innerText = msg || 'Loading...';
            document.getElementById('page-loader').classList.add('active');
        }

        function hideLoader() {
            document.getElementById('page-loader').classList.remove('active');
        }

        // ─────────────────────────────────────────────────────────────────
        // DRAGULA INIT
        // ─────────────────────────────────────────────────────────────────
        function initDragula() {
            if (drakeInstance) {
                drakeInstance.destroy();
                drakeInstance = null;
            }

            var container = document.getElementById('left-defaults');
            if (!container) return;

            drakeInstance = dragula([container], {
                    moves: function(el) {
                        return !el.classList.contains('no-move');
                    }
                })
                .on('drag', function(el) {
                    el.classList.add('el-drag-ex-1');
                })
                .on('drop', function(el) {
                    el.classList.remove('el-drag-ex-1');

                    // 1. Visually update channel number badges
                    setTimeout(function() {
                        var minVal = parseInt($('#min_range').val()) || 1;
                        var i = minVal;
                        document.querySelectorAll('.c-index').forEach(function(item) {
                            item.innerHTML = i;
                            i++;
                        });
                    }, 300);

                    // 2. Submit via AJAX
                    setTimeout(function() {
                        var minRange = parseInt($('#min_range').val()) || 1;
                        var new_channel_no = Array.from(el.parentNode.children).indexOf(el) + minRange;
                        var old_channel_no = el.id.split('_')[2];

                        document.getElementById('new_channel_no').value = new_channel_no;
                        document.getElementById('old_channel_no').value = old_channel_no;
                        document.getElementById('start_no').value = minRange;

                        // Sync filter values
                        syncFilterHiddenFields();

                        var lockedPos = JSON.parse(document.getElementById('lockedChannels').value || '[]');
                        if (lockedPos.indexOf(new_channel_no) > -1) {
                            location.reload();
                            return;
                        }

                        submitOrderAjax('Saving order...');

                    }, 1500);
                })
                .on('cancel', function(el) {
                    el.classList.remove('el-drag-ex-1');
                });

            // AutoScroll
            if (typeof autoScroll !== 'undefined' && document.querySelector('#_dragula')) {
                autoScroll([document.querySelector('#_dragula')], {
                    margin: 20,
                    pixels: 10,
                    scrollWhenOutside: true,
                    autoScroll: function() {
                        return this.down && drakeInstance.dragging;
                    }
                });
            }
        }

        // ─────────────────────────────────────────────────────────────────
        // AJAX ORDER SUBMIT (used by drag-drop AND lock/unlock)
        // ─────────────────────────────────────────────────────────────────
        // function submitOrderAjax(loaderMsg) {
        //     showLoader(loaderMsg || 'Saving...');

        //     var formData = $('#order-form').serialize();

        //     $.ajax({
        //         url:  "{{ route('saveChannelOrders') }}",
        //         type: "POST",
        //         data: formData,
        //         success: function (res) {
        //             hideLoader();
        //             if (res.success) {
        //                 // Small toast — no page reload needed
        //                 showToast('success', res.message || 'Channel order updated!');
        //             } else {
        //                 showToast('error', res.message || 'Something went wrong.');
        //             }
        //         },
        //         error: function (xhr) {
        //             hideLoader();
        //             showToast('error', 'Server error. Please try again.');
        //             console.error(xhr.responseText);
        //         }
        //     });
        // }

        function submitOrderAjax(loaderMsg) {
            showLoader(loaderMsg || 'Saving...');

            // ✅ Normal form inputs (light data)
            let formData = $('#order-form').serializeArray();

            // Convert to object
            let payload = {};
            formData.forEach(item => {
                payload[item.name] = item.value;
            });

            // ✅ Heavy data manually collect
            let channels = [];

            $('#dragDropContainer .col-sm-3').each(function(index) {
                channels.push({
                    id: $(this).data('id'),
                    order: index + 1,
                    position_locked: $(this).data('locked')
                });
            });

            payload.channels = channels;

            $.ajax({
                url: "{{ route('saveChannelOrders') }}",
                type: "POST",
                contentType: "application/json",
                data: JSON.stringify(payload),

                success: function(res) {
                    hideLoader();
                    if (res.success) {
                        showToast('success', res.message || 'Channel order updated!');
                    } else {
                        showToast('error', res.message || 'Something went wrong.');
                    }
                },
                error: function(xhr) {
                    hideLoader();
                    showToast('error', 'Server error. Please try again.');
                    console.error(xhr.responseText);
                }
            });
        }

        function applyOrder() {
            var minRange = parseInt($('#min_range').val()) || 1;


            // check is any filter apply
            var isFilterApplied = $('#stream_type').val() || $('#genre').val() || $('#language').val();

            if (!isFilterApplied) {
                // showToast('error', 'Please apply at least one filter before applying order.');
                showConfirmToast(
                    'No filters applied. This will reorder all channels starting from the specified range. Do you want to continue?',
                    function(confirmed) {
                        if (confirmed) {
                            document.getElementById('start_no').value = minRange;
                            syncFilterHiddenFields();
                            submitOrderAjax('Applying new range...');

                            // ✅ UI update karo
                            setTimeout(function() {
                                let i = minRange;

                                document.querySelectorAll('.c-index').forEach(function(item) {
                                    item.innerHTML = i;
                                    i++;
                                });
                            }, 500);
                        }
                    });
                return;
            }

            document.getElementById('start_no').value = minRange;

            syncFilterHiddenFields();

            submitOrderAjax('Applying new range...');

            // ✅ UI update karo
            setTimeout(function() {
                let i = minRange;

                document.querySelectorAll('.c-index').forEach(function(item) {
                    item.innerHTML = i;
                    i++;
                });
            }, 500);
        }

        // ─────────────────────────────────────────────────────────────────
        // Simple toast notification (no dependency needed)
        // ─────────────────────────────────────────────────────────────────
        function showToast(type, msg) {
            var bg = type === 'success' ? '#28a745' : '#dc3545';
            var toast = $('<div>')
                .text(msg)
                .css({
                    position: 'fixed',
                    bottom: '30px',
                    right: '30px',
                    background: bg,
                    color: '#fff',
                    padding: '12px 22px',
                    borderRadius: '8px',
                    fontSize: '14px',
                    fontWeight: '500',
                    zIndex: 999999,
                    boxShadow: '0 4px 12px rgba(0,0,0,0.25)',
                    opacity: 0,
                    transition: 'opacity 0.3s',
                });
            $('body').append(toast);
            setTimeout(function() {
                toast.css('opacity', 1);
            }, 10);
            setTimeout(function() {
                toast.css('opacity', 0);
                setTimeout(function() {
                    toast.remove();
                }, 400);
            }, 3000);
        }


        function showConfirmToast(msg, onConfirm) {

            // Overlay
            var overlay = $('<div>').css({
                position: 'fixed',
                top: 0,
                left: 0,
                width: '100%',
                height: '100%',
                background: 'rgba(0,0,0,0.4)',
                zIndex: 999998,
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center'
            });

            // Modal Box
            var modal = $('<div>').html(`
        <div style="font-size:16px; margin-bottom:15px; color:#333;">
            ${msg}
        </div>
        <div style="display:flex; justify-content:flex-end; gap:10px;">
            <button id="confirm-no">Cancel</button>
            <button id="confirm-yes">Confirm</button>
        </div>
    `).css({
                background: '#fff',
                padding: '20px 24px',
                borderRadius: '10px',
                minWidth: '280px',
                maxWidth: '320px',
                boxShadow: '0 10px 25px rgba(0,0,0,0.2)',
                textAlign: 'left',
                animation: 'fadeInScale 0.2s ease'
            });

            overlay.append(modal);
            $('body').append(overlay);

            // Button styles
            modal.find('#confirm-yes').css({
                background: '#007bff',
                color: '#fff',
                border: 'none',
                padding: '6px 14px',
                borderRadius: '6px',
                cursor: 'pointer'
            });

            modal.find('#confirm-no').css({
                background: '#e9ecef',
                color: '#333',
                border: 'none',
                padding: '6px 14px',
                borderRadius: '6px',
                cursor: 'pointer'
            });

            // Events
            modal.find('#confirm-yes').on('click', function() {
                onConfirm(true);
                overlay.remove();
            });

            modal.find('#confirm-no').on('click', function() {
                onConfirm(false);
                overlay.remove();
            });

            // Click outside to close
            overlay.on('click', function(e) {
                if (e.target === this) {
                    overlay.remove();
                }
            });
        }

        // ─────────────────────────────────────────────────────────────────
        // Sync filter selects → hidden form fields
        // ─────────────────────────────────────────────────────────────────
        function syncFilterHiddenFields() {
            document.getElementById('form_stream_type').value = $('#stream_type').val() || '';
            document.getElementById('form_genre').value = $('#genre').val() || '';
            document.getElementById('form_language').value = $('#language').val() || '';
        }

        // ─────────────────────────────────────────────────────────────────
        // AJAX: load filtered channels
        // Response must include: { total, min_channel_no, html }
        // ─────────────────────────────────────────────────────────────────
        function loadFilteredChannels() {
            showLoader('Loading channels...');

            var data = {
                stream_type: $('#stream_type').val(),
                genre: $('#genre').val(),
                language: $('#language').val(),
            };

            $.ajax({
                url: "{{ route('getChannelOrderList') }}",
                type: "GET",
                data: data,
                success: function(res) {
                    hideLoader();

                    if (res.total > 0) {


                        totalChannels = res.total;

                        // ── KEY FIX: min_range = pehle filtered channel ka channel_number
                        //             max_range = min + total - 1
                        var minChannelNo = res.min_channel_no || 1; // backend se aayega
                        $('#min_range').val(minChannelNo);
                        $('#max_range').val(minChannelNo + totalChannels - 1);

                        $('.total-count-display').text('Total Channels: ' + totalChannels);

                        $('#dragDropContainer').html(res.html);

                        initDragula();
                    } else {
                        $('#dragDropContainer').html(
                            '<div class="alert alert-info mt-2">No channels found for the selected filters.</div>'
                        );
                    }
                },
                error: function() {
                    hideLoader();
                    $('#dragDropContainer').html(
                        '<div class="alert alert-danger mt-2">Error loading channels. Please try again.</div>'
                    );
                }
            });
        }

        // ─────────────────────────────────────────────────────────────────
        // Min range manual change → max auto update
        // ─────────────────────────────────────────────────────────────────
        $('#min_range').on('input', function() {
            var min = parseInt($(this).val()) || 1;
            if (min < 1) {
                min = 1;
                $(this).val(1);
            }
            $('#max_range').val(min + totalChannels - 1);
        });

        // ─────────────────────────────────────────────────────────────────
        // Filter change → reload
        // ─────────────────────────────────────────────────────────────────
        $('#stream_type, #genre, #language').on('change', function() {
            loadFilteredChannels();
        });

        // ─────────────────────────────────────────────────────────────────
        // Reset filters
        // ─────────────────────────────────────────────────────────────────
        function resetFilters() {
            $('#stream_type').val('').trigger('change');
            $('#genre').val('').trigger('change');
            $('#language').val('').trigger('change');
            // loadFilteredChannels();
        }

        // ─────────────────────────────────────────────────────────────────
        // Page load
        // ─────────────────────────────────────────────────────────────────
        $(document).ready(function() {
            initDragula();
        });
    </script>

    {{-- Lock / Unlock — bhi AJAX se submit hoga --}}
    <script type="text/javascript">
        function lock(id) {
            document.getElementById('start_no').value = parseInt($('#min_range').val()) || 1;
            $('#position_locked_' + id).val(1);
            $('#main_div_' + id).addClass('no-move');
            $('#lock_' + id).html(
                '<svg onclick="unlock(' + id +
                ')" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-lock"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>'
            );
            syncFilterHiddenFields();
            submitOrderAjax('Locking channel...');
        }

        function unlock(id) {
            document.getElementById('start_no').value = parseInt($('#min_range').val()) || 1;
            $('#position_locked_' + id).val(0);
            $('#main_div_' + id).removeClass('no-move');
            $('#lock_' + id).html(
                '<svg onclick="lock(' + id +
                ')" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-unlock"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 9.9-1"></path></svg>'
            );
            syncFilterHiddenFields();
            submitOrderAjax('Unlocking channel...');
        }
    </script>
@endsection
