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


/* Wrapper */
.cdn-wrapper {
    background: #0c1627;    
    border-radius: 10px;
}

/* Checkbox row */
.cdn-checkbox {
    color: #cfd8ff;
    font-weight: 500;
}

/* Table layout */
.cdn-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 10px;
}

/* Input styling */
.cdn-input {
    background: #16233c;
    border: 1px solid #1f2f4f;
    color: #fff;
    height: 42px;
    border-radius: 6px;
    font-size: 14px;
}

.cdn-input::placeholder {
    color: #8fa3c8;
}

.cdn-input:focus {
    background: #16233c;
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59,130,246,.2);
    color: #fff;
}

/* Button cell */
.action-cell {
    width: 110px;
}

/* Add More / Remove buttons */
.addMore {
    width: 100%;
    height: 42px;
    font-size: 13px;
    border-radius: 6px;
}

/* Remove button */
.removeRow {
    background: #dc2626;
    height: 42px;
    width: 100%;
    border: none;
}

.removeRow:hover {
    background: #b91c1c;
}

/* Responsive fix */
@media (max-width: 768px) {
    .cdn-table tr {
        display: flex;
        flex-direction: column;
    }

    .action-cell {
        width: 100%;
    }
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
                            
                            {{-- <div class="form-check form-switch">
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
                            </div> --}}

                            {{-- <div class="row">
                                <div class="col-md-3 mb-3">                                    
                                    <label class="new-control new-checkbox checkbox-primary">
                                        <input type="checkbox" class="new-control-input"
                                            name="status" value="1">
                                        <span class="new-control-indicator" style="user-select: none;"></span> <span class="mx-2" style="user-select: none;">Enable these CDNs</span>
                                    </label>
                                </div>
                                <div class="col-lg-12">
                                    <table id="domainTable">
                                        <tr>
                                            <td><input class="form-control" type="text" name="domains[0][domain_name]" required></td>
                                            <td><input class="form-control" type="url" name="domains[0][url]" required></td>
                                            <td><button class="btn btn-sm btn-primary" type="button" class="addMore">Add More</button></td>
                                        </tr>
                                    </table>
                                </div>
                            </div> --}}

                            <input type="hidden" name="id" value="{{isset($cdn_settings) ? $cdn_settings->id : ''}}">

                            <div class="row cdn-wrapper">
                                <div class="col-md-12 mb-3">                                    
                                    <label class="new-control new-checkbox checkbox-primary cdn-checkbox">
                                        <input type="checkbox" class="new-control-input" {{isset($cdn_settings) && $cdn_settings->status == 1 ? 'checked' : ''}}  name="status" value="1">
                                        <span class="new-control-indicator"></span>
                                        <span class="mx-2" style="user-select: none;">Enable these CDNs</span>
                                    </label>
                                </div>

                                <div class="col-lg-12">
                                    <table id="domainTable" class="cdn-table">

                                        @if (isset($cdn_settings) && count($cdn_settings->domains) > 0)
                                            
                                        @foreach ($cdn_settings->domains as $key => $item)
                                            <tr>
                                                <td>
                                                    <input class="form-control cdn-input" type="text"
                                                        name="domains[{{$key}}][domain_name]" placeholder="Domain name" value="{{$item->domain_name}}" required>
                                                </td>
                                                <td>
                                                    <input class="form-control cdn-input" type="text"
                                                        name="domains[{{$key}}][url]" placeholder="CDN URL" value="{{$item->url}}" required>
                                                </td>
                                                <td class="action-cell">

                                                    @if ($key == 0)                                                        
                                                    <button class="btn btn-sm btn-primary addMore" type="button">
                                                        <i class="fa fa-plus"></i>
                                                        Add More                                                    
                                                    </button>
                                                    @else 
                                                    <button class="btn btn-sm btn-danger removeRow" type="button" onclick="removeRow({{$item->id}}, this)">
                                                        <i class="fa fa-trash"></i>
                                                        Remove                                                  
                                                    </button>

                                                    @endif

                                                </td>
                                            </tr>
                                        @endforeach
                                        @else
                                            <tr>
                                                <td>
                                                    <input class="form-control cdn-input" type="text"
                                                        name="domains[0][domain_name]" placeholder="Domain name" required>
                                                </td>
                                                <td>
                                                    <input class="form-control cdn-input" type="text"
                                                        name="domains[0][url]" placeholder="CDN URL" required>
                                                </td>
                                                <td class="action-cell">
                                                    <button class="btn btn-sm btn-primary addMore" type="button">
                                                        <i class="fa fa-plus"></i>
                                                        Add More                                                    
                                                    </button>

                                                </td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
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
{{-- <script>
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
</script> --}}



<script>
    // add more script :


let index = 1;

document.addEventListener('click', function (e) {
    

    if (e.target.classList.contains('addMore')) {


        document.getElementById('domainTable')
            .insertAdjacentHTML('beforeend', `
            <tr>
                <td>
                    <input class="form-control cdn-input" type="text"
                        name="domains[${index}][domain_name]" placeholder="Domain name" required>
                </td>
                <td>
                    <input class="form-control cdn-input" type="text"
                        name="domains[${index}][url]" placeholder="CDN URL" required>
                </td>
                <td class="action-cell">
                    <button class="btn btn-sm btn-danger removeRow" type="button">
                        <i class="fa fa-trash"></i>
                        <span class="d-none d-md-inline ms-1">Remove</span>
                    </button>

                </td>
            </tr>
        `);

        // e.target.remove();
        index++;
    }

    if (e.target.classList.contains('removeRow')) {
        e.target.closest('tr').remove();
    }
});

function removeRow(id, elem){

    if (!confirm('Are you sure you want to remove this?')) {
        return;
    }

    $.ajax({
        url: "{{ route('cdn-settings.demoins.remove') }}",
        type: 'GET',
        data: { id: id },
        success: function (response) {
            console.log(response);
        }
    });

}
</script>
@endsection
