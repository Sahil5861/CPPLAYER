<form id="user-form"  method="post" action="{{$actionRoute}}" enctype="multipart/form-data" novalidate class="simple-example" >
    @csrf
    <input type="hidden" name="id" value="@if(isset($ad_plan)){{$ad_plan->id}}@endif">

    <div class="form-row">        
        @if (Auth::user()->role == 2)            
        <div class="col-md-6 mb-4">
            <label for="super_user_id">Select super Admin Ad</label>
            <select name="super_admin_ad" id="super_admin_ad" class="form-control select">
                <option value="">--select--</option>
                @foreach($super_admin_ads as $item)
                
                <option value="{{$item->id}}">{{$item->title}} - ({{$price}})                     
                </option>
                @endforeach
            </select>
        </div>
        @endif
        <div class="col-md-6 mb-4">
            <label for="fullName">Title*</label>
            <input type="text" class="form-control" id="title" name="title" placeholder="Title" value="{{old('title')}}@if(isset($ad_plan)){{$ad_plan->title}}@endif" required>
            <div class="invalid-feedback">
                @error('title') {{ $message }} @enderror
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <label for="fullName">Schedule Time</label>
            <select name="schedule_time" id="schedule_time" class="form-control select">
                <option value="30">Every 30 min</option>
                <option value="60">Every hour</option>
            </select>
        </div>

        <div class="col-md-6 mb-4">
            <label for="fullName">Validity <small>(in days)</small></label>
            <input type="text" class="form-control" id="validity" name="validity" value="{{old('plan_validity')}}@if(isset($ad_plan)){{$ad_plan->validity}}@endif" oninput="this.value = this.value.replace(/[^0-9]/g,'')" placeholder="Default 30">
        </div>

        <div class="col-md-6 mb-4">
            <label for="fullName">Time Slot</label>
            <select name="time_slot" id="titme_slot" class="form-control select">
                <option value="10" {{isset($ad_plan) && $ad_plan->time_slot == 10 ? 'selected' : 'selected'}}>10 sec</option>
                <option value="5"  {{isset($ad_plan) && $ad_plan->time_slot == 5 ? 'selected' : ''}}>5 sec</option>
                <option value="20" {{isset($ad_plan) && $ad_plan->time_slot == 20 ? 'selected' : ''}}>20 sec</option>
                <option value="30" {{isset($ad_plan) && $ad_plan->time_slot == 30 ? 'selected' : ''}}>30 sec</option>
                <option value="40" {{isset($ad_plan) && $ad_plan->time_slot == 40 ? 'selected' : ''}}>40 sec</option>
            </select>
        </div>
    
        <div class="col-md-6 mb-4">
            <label for="fullName">Price*</label>
            <input type="text" class="form-control" onkeyup="check_price()" id="price" name="price" placeholder="Price" value="{{old('price')}}@if(isset($ad_plan)){{$ad_plan->price}}@endif" required>
            <div class="invalid-feedback">
                @error('price') {{ $message }} @enderror
            </div>
            <div class="price-error"></div>
        </div>
        @if(isset($plan) && $plan->net_admin_price != '')
            @php $style = '' @endphp
        @else
            @php $style = 'display:none;' @endphp
        @endif
        
        
        <div class="col-md-6 mb-4">
            <label for="fullName">Status</label>
            <select name="status" id="status" class="form-control select">
                <option value="1" @if(isset($plan) && $plan->status == 1){{'selected'}}@endif>Active</option>
                <option value="0" @if(isset($plan) && $plan->status == 0){{'selected'}}@endif>De-Active</option>
            </select>
            <div class="invalid-feedback">
                @error('status') {{ $message }} @enderror
            </div>
        </div>
    </div>                        
    @if(isset($plan))
    <button class="btn btn-primary submit-fn mt-2" type="submit">Update</button>
    @else
    <button class="btn btn-primary submit-fn mt-2" type="submit">Add</button>
    @endif

</form>

