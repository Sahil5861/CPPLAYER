@extends('layout.default')
@section('mytitle', 'CDN Settings')
@section('page', 'CDN Settings')

<style>
.tag-input {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    padding: 6px;
    min-height: 45px;
    cursor: text;
}

.tag-input input {
    border: none;
    outline: none;
    flex: 1;
    min-width: 200px;
    width: max-content;
}

.tag {
    background: #0d6efd;
    color: #fff;
    padding: 4px 8px;
    border-radius: 20px;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.tag i {
    cursor: pointer;
    font-size: 12px;
}


.cdn-input-wrapper {
    background: #0b1220;
    border: 1px solid #1f2937;
    border-radius: 10px;
    padding: 10px;
}

.cdn-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 8px;
    max-height: 120px;
    overflow-y: auto;
}

.cdn-tag {
    background: #1d4ed8;
    color: #fff;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 13px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.cdn-tag i {
    cursor: pointer;
    font-size: 12px;
    opacity: 0.8;
}

.cdn-tag i:hover {
    opacity: 1;
}

.cdn-input {
    background: transparent;
    border: none;
    color: #fff;
    outline: none;
}

.cdn-input::placeholder {
    color: #9ca3af;
}


</style>
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

                <div class="card">
                    <div class="card-body">
                        <form action="{{route('cdn.save')}}" method="post">
                            @csrf     
                            
                            <input type="hidden" id="id" name="id" value="{{isset($cdn_settings) ? $cdn_settings->id : ''}}">
                            {{-- <div class="form-group">
                                <label>CDNs <small>(comma separated)</small>*</label>

                                <div id="tag-input" class="tag-input form-control">
                                    <input type="text" class="form-control" id="tag-text" placeholder="Type CDN & press comma">
                                </div>

                                <!-- Hidden field for form submit -->
                                <input 
                                    type="hidden" 
                                    name="urls" 
                                    id="urls" 
                                    value="{{ $cdn_settings->cdn_links ?? '' }}"
                                >
                            </div> --}}

                            <label class="form-label">
                                CDNs <small class="text-muted">(comma separated)</small>
                            </label>

                            @php
                                $urls = explode(',', $cdn_settings->cdn_links);
                            @endphp

                            <div class="cdn-input-wrapper">
                                <div id="cdnTags" class="cdn-tags"></div>

                                <input
                                    type="text"
                                    id="cdnInput"
                                    class="form-control cdn-input"
                                    placeholder="Paste CDN URL & press Enter"
                                >

                                <input type="hidden" name="cdn_links" id="cdnLinks"
                                    value="{{ $cdn_settings->cdn_links ?? '' }}">
                            </div>

                            <script>
                                window.existingCdns = "{{ $cdn_settings->cdn_links ?? '' }}";
                            </script>




                            <div class="form-check form-switch">
                                <input 
                                    class="form-check-input" 
                                    type="checkbox" 
                                    id="status" 
                                    name="status"
                                    {{$cdn_settings->status == 1 ? 'checked' : ''}}
                                >
                                <label class="form-check-label" for="status" style="user-select: none;">
                                    Enable these CDNs
                                </label>
                            </div>


                            <button class="btn btn-primary submit-fn mt-4" type="submit">Update</button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    
@endsection

@section('footer')
<script>
const cdnInput = document.getElementById("cdnInput");
const cdnTags = document.getElementById("cdnTags");
const cdnLinks = document.getElementById("cdnLinks");

let cdnList = [];

/* EDIT MODE INIT */
if (window.existingCdns) {
    cdnList = window.existingCdns
        .split(",")
        .map(v => v.trim())
        .filter(Boolean);

    renderTags();
}

cdnInput.addEventListener("keydown", function (e) {
    if (e.key === "Enter" || e.key === ",") {
        e.preventDefault();

        let value = cdnInput.value.trim().replace(",", "");
        if (!value || cdnList.includes(value)) {
            cdnInput.value = "";

            alert('hii');
            return;
        }

        cdnList.push(value);
        renderTags();
        cdnInput.value = "";
    }
});

function renderTags() {
    cdnTags.innerHTML = ""; // ✅ REQUIRED

    cdnList.forEach((url, index) => {
        const tag = document.createElement("span");
        tag.className = "cdn-tag";
        tag.innerHTML = `
            ${url}
            <i class="fa fa-times"></i>
        `;

        tag.querySelector("i").onclick = () => {
            cdnList.splice(index, 1);
            renderTags();
        };

        cdnTags.appendChild(tag);
    });

    // 🔑 Convert back to comma string for DB
    cdnLinks.value = cdnList.join(",");
}

</script>

@endsection
