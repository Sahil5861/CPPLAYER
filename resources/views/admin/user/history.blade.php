@extends('layout.default')
@section('mytitle', 'User Profile')
@section('page', 'Users / Profile')

<style>
    .fa-user{
        padding: 10px;
    }

    .user-info p{
        margin-right: 20px;
    }

    .fa-rupee-sign, .fa-lock, .fa-map-marker, .fa-envelope, .fa-mobile{
        font-size: 16px;
    }
</style>

@section('content')
<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div class="col-xl-4 col-lg-6 col-md-5 col-sm-12 layout-top-spacing">
            <div class="user-profile layout-spacing">
                <div class="widget-content widget-content-area">
                    <div class="d-flex justify-content-between">
                        <h3 class="">Profile</h3>
                        <a href="#" class="mt-2 edit-profile"> <i class="fa fa-user"></i> </a>
                    </div>
                    <div class="text-center user-info">
                        <!-- <img src="assets/img/90x90.jpg" alt="avatar"> -->
                        <p class="">{{$user->name}}</p>
                    </div>
                    <div class="user-info-list">

                        <div class="">
                            <ul class="contacts-block list-unstyled">
                                <li class="contacts-block__item" title="Wallet Amount">
                                    <i class="fa fa-rupee-sign"></i>&nbsp; {{$user->current_amount}},
                                </li>
                                <li class="contacts-block__item" title="Login Pin">
                                    <i class="fa fa-lock"></i>&nbsp; {{$user->login_pin}}
                                </li>
                                <li class="contacts-block__item" title="Address">
                                    <i class="fa fa-map-marker"></i>&nbsp; {{$user->hf_number}}, {{$user->street_number}}, {{$user->landmark}}, {{$user->address}}
                                </li>
                                <li class="contacts-block__item" title="Email">
                                    <i class="fa fa-envelope" aria-hidden="true"></i>&nbsp; {{$user->email}}</a>
                                </li>
                                <li class="contacts-block__item" title="Mobile">
                                    <i class="fa fa-mobile" aria-hidden="true"></i>&nbsp; {{$user->mobile}}
                                </li>
                                <li class="contacts-block__item" title="Mac Address">
                                    <i class="fa fa-user-secret" aria-hidden="true"></i>&nbsp; {{$user->mac_address}}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-8 col-lg-6 col-md-7 col-sm-12  layout-top-spacing">
            <div class="col-xl-12 col-lg-12 mb-3">
                <div class="widget-content  br-6">
                    <div class="d-flex justify-content-between">
                        <h3 class="">User Additional Info</h3>
                    </div>  
                    <div class="table-responsive my-4">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th scope="row" style="width: 30%; color:#009688;">Login Pin</th>
                                    <td>{{ $user->login_pin ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th style="color: #009688;" scope="row">Login Pin App</th>
                                    <td>{{ $user->login_pin_app ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th style="color: #009688;" scope="row">Over 18 Pin</th>
                                    <td>{{ $user->over18_pin ?? 'N/A' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div> 
            </div>
            <div class="col-xl-12 col-lg-12 mb-3">
                <div class="widget-content widget-content-area br-6">
                    <div class="d-flex justify-content-between">
                        <h3 class="">Plan History</h3>
                    </div>
                    <div class="table-responsive mb-4 mt-4">
                        <table id="multi-column-ordering" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Plan</th>
                                    <th>Purchased By</th>
                                    <th>Price</th>
                                    <th>Purchased Date</th>
                                    <th>Expire Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="tableItem">
                                @foreach($user_plan_details as $item)
                                <tr>
                                    <td>{{$item['plan']}}</td>
                                    <td>{{$item['plan_purchased_by']}}</td>
                                    <td>{{$item['plan_purchase_price']}}</td>
                                    <td>{{date('j M Y',strtotime($item['plan_purchased_date']))}}</td>
                                    <td>{{date('j M Y',strtotime($item['plan_end_date']))}}</td>
                                    <td>{!! $item['status'] !!}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Plan</th>
                                    <th>Purchased By</th>
                                    <th>Price</th>
                                    <th>Purchased Date</th>
                                    <th>Expire Date</th>
                                    <th>Status</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5 col-md-5 col-sm-12 layout-top-spacing">
            <div class="widget-content widget-content-area br-6">
                <div class="d-flex justify-content-between">
                    <h3 class="">Mac Address</h3>
                </div>
                <div class="table-responsive mb-4 mt-4">
                    <table id="multi-column-ordering1" class="table table-hover">
                        <thead>
                            <tr>
                                <th>Mac Address</th>
                                <th>Status</th>
                                <th>Pin</th>
                                {{-- <th>Logged In At</th> --}}
                            </tr>
                        </thead>
                        <tbody id="tableItem">
                            @foreach($authusers as $item)
                            @php
                            $status = ($item->status == 1) ? '<span class="text-success">Active</span>' : '<span class="text-secondary">Inactive</span>'
                            @endphp
                            <tr>
                                <td>{{$item->mac_address}}</td>
                                <td>{!! $status !!}</td>
                                <td>{{$item->login_code}}</td>
                                {{-- <td>{{date('j M Y h:i a',strtotime($item->created_at))}}</td> --}}
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Mac Address</th>
                                <th>Status</th>
                                {{-- <th>Logged In At</th> --}}
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-8 col-lg-7 col-md-7 col-sm-12  layout-top-spacing">
            <div class="widget-content widget-content-area br-6">
                <div class="d-flex justify-content-between">
                    <h3 class="">Users Auth</h3>
                </div>
                <div class="table-responsive mb-4 mt-4">
                    <table id="multi-column-ordering2" class="table table-hover">
                        <thead>
                            <tr>
                                <th>IP Address</th>
                                <th>City Country</th>                                
                                <th>Postal</th>
                                <th>Status</th>
                                <th>Logged In At</th>
                            </tr>
                        </thead>
                        <tbody id="tableItem">
                            @foreach($authusers as $item)
                            @php
                            $status = ($item->status == 1) ? '<span class="text-success">Active</span>' : '<span class="text-secondary">Inactive</span>'
                            @endphp
                            <tr>
                                <td>{{$item->ip_address}}</td>
                                <td>{{$item->city}} - {{$item->country}}</td>                                
                                <td>{{$item->postal}}</td>
                                <td>{!! $status !!}</td>
                                <td>{{date('j M Y h:i a',strtotime($item->created_at))}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>IP Address</th>
                                <th>City Country</th>                                
                                <th>Postal</th>
                                <th>Status</th>
                                <th>Logged In At</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <hr>

        <div class="col-xl-4 col-lg-5 col-md-5 col-sm-12 layout-top-spacing">
            <div class="widget-content widget-content-area br-6">
                <div class="d-flex justify-content-between">
                    <h3 class="">Mac Address App</h3>
                </div>
                <div class="table-responsive mb-4 mt-4">
                    <table id="multi-column-ordering3" class="table table-hover">
                        <thead>
                            <tr>
                                <th>Mac Address</th>
                                <th>Status</th>
                                <th>Pin</th>
                                {{-- <th>Logged In At</th> --}}
                            </tr>
                        </thead>
                        <tbody id="tableItem">
                            @foreach($authusers_app as $item)
                            @php
                            $status = ($item->status == 1) ? '<span class="text-success">Active</span>' : '<span class="text-secondary">Inactive</span>'
                            @endphp
                            <tr>
                                <td>{{$item->mac_address}}</td>
                                <td>{!! $status !!}</td>
                                <td>{{$item->login_pin}}</td>
                                {{-- <td>{{date('j M Y h:i a',strtotime($item->created_at))}}</td> --}}
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Mac Address</th>
                                <th>Status</th>
                                {{-- <th>Logged In At</th> --}}
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-8 col-lg-7 col-md-7 col-sm-12  layout-top-spacing">
            <div class="widget-content widget-content-area br-6">
                <div class="d-flex justify-content-between">
                    <h3 class="">Users Auth</h3>
                </div>
                <div class="table-responsive mb-4 mt-4">
                    <table id="multi-column-ordering4" class="table table-hover">
                        <thead>
                            <tr>
                                <th>IP Address</th>
                                <th>City Country</th>                                
                                <th>Postal</th>
                                <th>Status</th>
                                <th>Logged In At</th>
                            </tr>
                        </thead>
                        <tbody id="tableItem">
                            @foreach($authusers_app as $item)
                            @php
                            $status = ($item->status == 1) ? '<span class="text-success">Active</span>' : '<span class="text-secondary">Inactive</span>'
                            @endphp
                            <tr>
                                <td>{{$item->ip_address}}</td>
                                <td>{{$item->city}} - {{$item->country}}</td>                                
                                <td>{{$item->postal}}</td>
                                <td>{!! $status !!}</td>
                                <td>{{date('j M Y h:i a',strtotime($item->created_at))}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>IP Address</th>
                                <th>City Country</th>                                
                                <th>Postal</th>
                                <th>Status</th>
                                <th>Logged In At</th>
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
  <script type="text/javascript">
    $(document).ready(function(){

      // DataTable
      $('#multi-column-ordering').DataTable({
      });
      $('#multi-column-ordering1').DataTable({
      });
      $('#multi-column-ordering2').DataTable({
      });
      $('#multi-column-ordering3').DataTable({
      });
      $('#multi-column-ordering4').DataTable({
      });
    });
    </script>

<!-- footer script if required -->
@endsection
