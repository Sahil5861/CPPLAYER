@extends('layout.default')
@section('mytitle', 'Helplines')
@if(isset($tvshow))
@section('page', 'Helplines / Update')
@else
@section('page', 'Helplines / Add')
@endif

@section('content')
<div class="layout-px-spacing">
    <div class="row layout-top-spacing">

        <div class="col-12">
            @if(session()->has('message'))
            <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ session()->get('message') }}</strong>
            </div>
            @endif
    
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
        <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
            <div class="widget-content widget-content-area br-6">

                

                <form  method="post" action="{{ route('help-settings-save') }}" enctype="multipart/form-data">
                    @csrf
                    @if (isset($helplines))                        
                        <input type="hidden" name="id" value="{{isset($helplines) ? $helplines->id : ''}}">
                    @endif
                    

                    <div class="form-row">
                        <div class="col-12">
                            <h4>Update Helplines</h4>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="content_network">WhatsApp*</label>       
                                <input type="text" name="whatsapp" id="whatsapp" class="form-control" value="{{ isset($helplines) ? $helplines->whatsapp_url : '' }}"  placeholder="WhatsApp Url" required>                            
                            </div>    
                        </div>     
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="content_network">Telegram</label>       
                                <input type="text" name="telegram" id="telegram" class="form-control" value="{{ isset($helplines) ? $helplines->telegram_url : '' }}"  placeholder="Telegram Url">                            
                            </div>    
                        </div>  
                    </div>

                    <button class="btn btn-primary submit-fn mt-4" type="submit">
                        Update
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection

@section('footer')
<!-- Custom JS if needed -->
@endsection
