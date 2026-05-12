{{--
    dragdrop_list.blade.php (partial)
    
    IMPORTANT: 
    - input name="ids[]"  (pehle numbers[] tha — change kiya)
    - main_div id = main_div_{channel_number}  (drag.js use karta hai)
    - c-index span shows current channel_number
--}}

<div id="left-defaults" class="row">
    @foreach ($dataForLoop as $key => $dcl)
        @if ($dcl)
            <div class="col-sm-3 d-md-flex d-block {{ $dcl->position_locked == 1 ? 'no-move' : 'move' }} channel-item"
                id="main_div_{{ $dcl->channel_number }}"
                data-id="{{ $dcl->id }}"
                data-locked="{{ $dcl->position_locked }}"
                style="padding: 5px;">

                <div class="media d-md-flex d-block text-sm-center text-center"
                    style="padding: 5px; border: 1px solid; padding: 5px 10px; border-radius: 5px; width: 100%;">

                    {{-- Channel number badge --}}
                    <span id="ch_{{ $key }}" class="c-index"
                        style="position: absolute; left: 10px; top: 15px;">
                        {{ $dcl->channel_number }}
                    </span>

                    <div class="media-body">
                        <div class="d-xl-flex d-block justify-content-between" style="position: relative;">
                            &nbsp;
                            <div style="width: 100%; margin-left: 20px;">
                                <h6 style="margin-bottom: 3px">{{ $dcl->channel_name }}</h6>

                                {{-- ✅ ids[] — channel ID bheja jaayega (numbers[] nahi) --}}
                                {{-- <input type="hidden" name="ids[]" value="{{ $dcl->id }}">
                                <input type="hidden" id="position_locked_{{ $key }}" name="position_locked[]" value="{{ $dcl->position_locked }}"> --}}
                            </div>

                            {{-- Lock / Unlock icon --}}
                            <div id="lock_{{ $key }}" style="position: absolute; right: 0; top: 0;">
                                @if ($dcl->position_locked == 1)
                                    <svg onclick="unlock({{ $key }})"
                                        xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="feather feather-lock">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                    </svg>
                                @else
                                    <svg onclick="lock({{ $key }})"
                                        xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="feather feather-unlock">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                        <path d="M7 11V7a5 5 0 0 1 9.9-1"></path>
                                    </svg>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
</div>