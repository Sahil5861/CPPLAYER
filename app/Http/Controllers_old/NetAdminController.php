<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\SadminPlan;
use App\Models\SadminWallet;
use App\Models\NetAdminWallet;
use DB;

class NetAdminController extends Controller
{
    public function index()
    {
        return view('admin.netadmin.index');
    }

    /* Process ajax request */
    public function getNetAdminList(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // total number of rows per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        // Total records
        $totalRecords = User::select('count(*) as allcount')->where('role',6)->count();
        $inactiveRecords = User::select('count(*) as allcount')->where('role',6)->where('status','0')->whereNull('users.deleted_at')->count();
        $activeRecords = User::select('count(*) as allcount')->where('role',6)->where('status','1')->whereNull('users.deleted_at')->count();
        $deletedRecords = User::select('count(*) as allcount')->where('role',6)->whereNotNull('users.deleted_at')->count();

        $totalRecordswithFilter = User::select('count(*) as allcount')
        ->where('name', 'like', '%' . $searchValue . '%')
        ->where('role',6)
        ->orWhere(function($query) use ($searchValue)
        {
            $query->where('users.city', 'like', '%' . $searchValue . '%')
            ->where('role',6)
            ->whereNull('users.deleted_at');

        })

        ->orWhere(function($query)  use ($searchValue)
        {
            $query->Where('users.company_name', 'like', '%' . $searchValue . '%')
            ->where('role',6)
            ->whereNull('users.deleted_at');

        })
        ->count();

        // Get records, also we have included search filter as well
        $records = User::orderBy($columnName, $columnSortOrder)

            ->whereNull('users.deleted_at')
            ->where('users.name', 'like', '%' . $searchValue . '%')
            ->where('role',6)
            ->whereNull('users.deleted_at')
            ->orWhere(function($query) use ($searchValue)
            {
                $query->where('users.city', 'like', '%' . $searchValue . '%')
                ->where('role',6)
                ->whereNull('users.deleted_at');

            })

            ->orWhere(function($query)  use ($searchValue)
            {
                $query->Where('users.company_name', 'like', '%' . $searchValue . '%')
                ->where('role',6)
                ->whereNull('users.deleted_at');

            })


            // ->orWhere('Users.description', 'like', '%' . $searchValue . '%')
            // ->orWhere('Users.contact_email', 'like', '%' . $searchValue . '%')
            ->select('users.*')
            // ->leftJoin('users', 'users.id', '=', 'Channel.user_id')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();

        foreach ($records as $record) {
            if($record->id == 1){
                $status = "Active";
            }
            elseif($record->status == 1){
                $status = '<a onchange="updateStatus(\''.url('netadmin/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary mr-2"><input type="checkbox" value="1" checked id="accountSwitch{{$record->id}}"><span class="slider round"></span></label> </a>';
            }else{
                $status = '<a onchange="updateStatus(\''.url('netadmin/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary   mr-2"><input type="checkbox" value="0" id="accountSwitch{{$record->id}}"><span class="slider round"></span></label></a>';
            }
            $data_arr[] = array(
                "name" => $record->name,
                "email" => $record->email,
                "mobile" => $record->mobile,
                "wallet_amount" => $record->current_amount,
                "role" => $record->role,
                "address" => $record->address,
                "city" => $record->city,
                "country" => $record->country,
                "company_name" => $record->company_name,
                "status" => $status,
                "created_at" => date('j M Y h:i a',strtotime($record->created_at)),
                "action" => $record->id == 1 ? '<div class="action-btn">
                        <a  href="edit-netadmin/'.base64_encode($record->id).'"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a></div>':'<div class="action-btn">
                        <a  href="edit-netadmin/'.base64_encode($record->id).'"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>
                        <a href="javascript:;" onclick="delete_item(\''.base64_encode($record->id).'\',\'netadmin\')"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>
                      </div>',
                "signIn" => '<a href="netadmin-signin/'.base64_encode($record->id).'" data-toggle="tooltip" data-placement="top" title="Sign IN"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg></a>',
            );
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr,
            "totalRecords" => number_format($totalRecords),
            "activeRecords" => number_format($activeRecords),
            "inactiveRecords" => number_format($inactiveRecords),
            "deletedRecords" => number_format($deletedRecords),
        );

        echo json_encode($response);
    }

    public function addNetAdmin(){
        $this->data['packages'] = SadminPlan::where('plan_type',0)->where('net_admin_price','!=','')->get(); 
        return view('admin.netadmin.add',$this->data);
    }

    public function updateStatus($id){
        $user = User::find(base64_decode($id));

        if($user){
            $user->status = $user->status == '1' ? '0' : '1';
            $user->save();
            echo json_encode(['message','Admin status successfully']);
        }else{
            echo json_encode(['message','Something went wrong!!']);
        }
    }


    public function add(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'mobile' => 'required',
            'address' => 'required',
            'password' => 'required',
            'address' => 'required',
            'country' => 'required',
            'city' => 'required',
            'hf_number' => 'required',
            'street_number' => 'required',
            'pincode' => 'required',
            'landmark' => 'required',
        ]);
        if(!empty($request->id)){
            $emailExists = User::where('email',$request->email)->where('created_by',\Auth::user()->id)->where('id','!=',$request->id)->first();
            if($emailExists){
                return back()->with('error','This email already exists.');
            }
            $user = User::firstwhere('id',$request->id);

            $real_password = $request->password;
            $password = bcrypt($real_password);
            // $password = md5($hash_pass1);

            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->address = $request->address;
            $user->hf_number = $request->hf_number;
            $user->street_number = $request->street_number;
            $user->landmark = $request->landmark;
            $user->country = $request->country;
            $user->city = $request->city;
            $user->pincode = $request->pincode;
            $user->company_name = $request->company_name;
            // $user->current_amount = $request->current_amount;
            $user->password = $real_password;
            $user->real_password = $real_password;
            $user->role = 6;
            $user->status = $request->status;
            if($user->save()){
                if($request->current_amount){

                    $wallet_d = new SadminWallet();
                    $wallet_d->debit_amount = $request->current_amount;
                    $wallet_d->user_id = $user->id;
                    $wallet_d->message = 'Net Admin Wallet';
                    $wallet_d->amount_method = 'Cash';
                    $wallet_d->credit_amount_by = \Auth::user()->id;
                    $wallet_d->save();

                    $wallet = new NetAdminWallet();
                    $wallet->credit_amount = $request->current_amount;
                    $wallet->user_id = $user->id;
                    $wallet->message = "Add recieve from superadmin in wallet (".$user->email.")";
                    $wallet->credit_amount_by = \Auth::user()->id;
                    $wallet->save();

                    $user->current_amount = $user->current_amount + $request->current_amount;
                    $user->save();
                }

                if(!empty($request->netadmin_plan)){
                    $plans = $request->netadmin_plan;
                    for ($i=0; $i <count($plans) ; $i++) {
                        $sadminplan = SadminPlan::where('plan_type',0)->where('net_admin_price','!=','')->where('id',$plans[$i])->first();
                        if($user->current_amount < $sadminplan->net_admin_price){
                            return back()->with('error','Insufficiant blance');            
                        }else{
                            DB::table('netadmin_plans_details')->insert(
                                [
                                    'plan_id' => $plans[$i],
                                    'user_id' => $user->id,
                                    'added_by' => \Auth::user()->id,
                                    'amount' => $sadminplan->net_admin_price
                                ]
                            );
                            // $user->current_amount = $user->current_amount - $sadminplan->net_admin_price;
                            // $user->save();

                            $channels = DB::select("SELECT c.* FROM package_channels pc LEFT JOIN channels c ON c.id = pc.channel_id WHERE pc.plan_id = ".$plans[$i]);
                            foreach ($channels as $key => $item) {
                                DB::table('netadmin_channels')->insert([
                                    'channel_id' => $item->id,
                                    'link' => '',
                                    'user_id' => $user->id
                                ]);
                            }
                        }
                    }
                }
                return back()->with('message','Net Admin updated successfully');
            }else{
                return back()->with('message','Net Admin not updated successfully');
            }

        }else{
            $emailExists = User::where('email',$request->email)->where('created_by',\Auth::user()->id)->first();
            if($emailExists){
                return back()->with('error','This email already exists.');
            }
            $user = new User();
            $real_password = $request->password;
            $password = bcrypt($real_password);
            // $password = md5($hash_pass1);

            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->created_by = \Auth::user()->id;
            $user->address = $request->address;
            $user->hf_number = $request->hf_number;
            $user->street_number = $request->street_number;
            $user->landmark = $request->landmark;
            $user->country = $request->country;
            $user->city = $request->city;
            $user->pincode = $request->pincode;
            $user->company_name = $request->company_name;
            // $user->current_amount = $request->current_amount;
            $user->password = $real_password;
            $user->real_password = $real_password;
            $user->role = 6;
            $user->status = $request->status;
            if($user->save()){
                if($request->current_amount){

                    $wallet_d = new SadminWallet();
                    $wallet_d->debit_amount = $request->current_amount;
                    $wallet_d->user_id = $user->id;
                    $wallet_d->message = 'Net Admin Wallet';
                    $wallet_d->amount_method = 'Cash';
                    $wallet_d->credit_amount_by = \Auth::user()->id;
                    $wallet_d->save();

                    $wallet = new NetAdminWallet();
                    $wallet->credit_amount = $request->current_amount;
                    $wallet->user_id = $user->id;
                    $wallet->message = "Add recieve from superadmin in wallet (".$user->email.")";
                    $wallet->credit_amount_by = \Auth::user()->id;
                    $wallet->save();

                    $user->current_amount = $user->current_amount + $request->current_amount;
                    $user->save();
                }
                if(!empty($request->netadmin_plan)){
                    $plans = $request->netadmin_plan;
                    for ($i=0; $i <count($plans) ; $i++) { 
                        $sadminplan = SadminPlan::where('plan_type',0)->where('net_admin_price','!=','')->where('id',$plans[$i])->first();
                        if($user->current_amount < $sadminplan->net_admin_price){
                            return back()->with('error','Insufficiant blance');            
                        }else{
                            DB::table('netadmin_plans_details')->insert(
                                [
                                    'plan_id' => $plans[$i],
                                    'user_id' => $user->id,
                                    'added_by' => \Auth::user()->id,
                                    'amount' => $sadminplan->net_admin_price
                                ]
                            );
                            // $user->current_amount = $user->current_amount - $sadminplan->net_admin_price;
                            // $user->save();

                            $channels = DB::select("SELECT c.* FROM package_channels pc LEFT JOIN channels c ON c.id = pc.channel_id WHERE pc.plan_id = ".$plans[$i]);
                            foreach ($channels as $key => $item) {
                                DB::table('netadmin_channels')->insert([
                                    'plan_id' => $plans[$i],
                                    'channel_id' => $item->id,
                                    'link' => '',
                                    'user_id' => $user->id
                                ]);
                            }
                        }
                    }
                }
                return back()->with('message','Net Admin added successfully');
            }else{
                return back()->with('message','Net Admin not added successfully');
            }
        }

    }

    public function editNetAdmin($id){
        $this->data['netadmin'] = User::where('id',base64_decode($id))->first();
        $this->data['existing_plan'] = DB::table('netadmin_plans_details')->select('super_admin_plans.title')->leftJoin('super_admin_plans', 'super_admin_plans.id','=','netadmin_plans_details.plan_id')->where('netadmin_plans_details.user_id',base64_decode($id))->get();
        $this->data['packages'] = SadminPlan::where('plan_type',0)->where('net_admin_price','!=','')->get(); 
        return view('admin.netadmin.add',$this->data);
    }

    public function destroy(Request $request){
        $user = User::where('id',base64_decode($request->id))->first();
        $user->deleted_at = time();
        if($user->save()){
            echo json_encode(['message','Net Admin deleted successfully']);
        }else{
            echo json_encode(['message','Net Admin not deleted successfully']);
        }
    }

    public function channels(){
        $this->data['total_channel'] = DB::select("SELECT COUNT(*) as total_channel FROM netadmin_channels WHERE user_id = ".\Auth::user()->id);
        $this->data['total_active_channel'] = DB::select("SELECT COUNT(*) as total_active_channel FROM netadmin_channels WHERE status = 1 AND user_id = ".\Auth::user()->id);
        $this->data['total_inactive_channel'] = DB::select("SELECT COUNT(*) as total_inactive_channel FROM netadmin_channels WHERE status = 0 AND user_id = ".\Auth::user()->id);
        $this->data['empty_link_channel'] = DB::select("SELECT COUNT(*) as empty_link_channel FROM netadmin_channels WHERE link = '' AND user_id = ".\Auth::user()->id);

        $this->data['channels'] = DB::select("SELECT c.channel_name,nc.link,nc.status,nc.id FROM netadmin_channels nc LEFT JOIN channels c ON c.id = nc.channel_id WHERE nc.user_id = ".\Auth::user()->id. " GROUP BY nc.channel_id");
        return view('admin.netadmin.channels',$this->data);
    }

    public function channelFilter($type){
        if($type == 'all'){
            return redirect()->route('netadmin.channels');
        }elseif($type == 'active'){
            $where = ' AND nc.status = 1';
        }elseif($type == 'inactive'){
            $where = ' AND nc.status = 0';
        }elseif($type == 'empty'){
            $where = ' AND nc.link = ""';
        }else{
            return redirect()->route('netadmin.channels');
        }
        $this->data['total_channel'] = DB::select("SELECT COUNT(*) as total_channel FROM netadmin_channels WHERE user_id = ".\Auth::user()->id);
        $this->data['total_active_channel'] = DB::select("SELECT COUNT(*) as total_active_channel FROM netadmin_channels WHERE status = 1 AND user_id = ".\Auth::user()->id);
        $this->data['total_inactive_channel'] = DB::select("SELECT COUNT(*) as total_inactive_channel FROM netadmin_channels WHERE status = 0 AND user_id = ".\Auth::user()->id);
        $this->data['empty_link_channel'] = DB::select("SELECT COUNT(*) as empty_link_channel FROM netadmin_channels WHERE link = '' AND user_id = ".\Auth::user()->id);

        $this->data['channels'] = DB::select("SELECT c.channel_name,nc.link,nc.status,nc.id FROM netadmin_channels nc LEFT JOIN channels c ON c.id = nc.channel_id WHERE nc.user_id = ".\Auth::user()->id. $where." GROUP BY nc.channel_id");
        return view('admin.netadmin.channels',$this->data);
    }

    public function updateChannelStatus($id){
        $channel = DB::table('netadmin_channels')->where('id',base64_decode($id))->first();

        if($channel){
            $status = $channel->status == '1' ? '0' : '1';
            DB::table('netadmin_channels')->where('id',base64_decode($id))->update(['status'=> $status]);
            return redirect()->route('netadmin.channels');
        }else{
            return redirect()->route('netadmin.channels');
        }
    }

    public function updateLink(Request $request){
        DB::table('netadmin_channels')->where('id',$request->id)->update(['link'=>$request->link]);
        redirect()->route('netadmin.channels');
    }

    public function wallet(){
        return view('admin.netadmin.wallet');
    }

    public function getNetAdminWalletList(Request $request){
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // total number of rows per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        // Total records
        // Total records

        $totalRecords = NetAdminWallet::select('count(*) as allcount')->where('user_id',\Auth::user()->id)->orWhere('credit_amount_by',\Auth::user()->id)->count();

        $total_amount_added = NetAdminWallet::where('user_id',\Auth::user()->id)->sum('credit_amount');

        $amount_credit_to_admins = NetAdminWallet::where('credit_amount_by',\Auth::user()->id)->sum('debit_amount');

        $total_credit_number = NetAdminWallet::select('count(*) as total_credit_number')->where('user_id', \Auth::user()->id)->count();
        $total_debit_number = NetAdminWallet::select('count(*) as total_debit_number')->where('credit_amount_by', \Auth::user()->id)->count();


        $totalRecordswithFilter = DB::select("SELECT count(*) as allcount  FROM net_admin_wallet cr LEFT JOIN clientusers cu ON IF(cr.credit_amount=0.00, cu.id=cr.user_id, cu.id=cr.credit_amount_by) WHERE cr.user_id = ".\Auth::user()->id." OR cr.credit_amount_by = ".\Auth::user()->id." AND cr.credit_amount LIKE '%".$searchValue."%' OR cr.debit_amount LIKE '%".$searchValue."%' ORDER BY cr.created_at desc");

        $records = DB::select("SELECT *, cr.id as transaction_id  FROM net_admin_wallet cr LEFT JOIN clientusers cu ON IF(cr.credit_amount=0.00, cu.id=cr.user_id, cu.id=cr.credit_amount_by) WHERE cr.user_id = ".\Auth::user()->id." OR cr.credit_amount_by = ".\Auth::user()->id." AND cu.name LIKE '%".$searchValue."%' ORDER BY cr.created_at desc LIMIT ".$rowperpage." OFFSET ".$start);

        $data_arr = array();

        foreach ($records as $record) {
            $data_arr[] = array(
                "id" => $record->transaction_id,
                "email" => $record->email ,
                "credit_amount" => $record->credit_amount && $record->credit_amount != '0.00' ? '<span style="color:green">+'.$record->credit_amount.'</span>' : '--',
                "debit_amount" => $record->debit_amount && $record->debit_amount != '0.00' ? '<span style="color:red">-'.$record->debit_amount.'</span>' : '--',
                "credit_for" => $record->name,
                "message" => $record->message,
                "created_at" => date('j M Y h:i a',strtotime($record->created_at)),
            );
        }
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter[0]->allcount,
            "aaData" => $data_arr,
            "current_amount" => number_format(\Auth::user()->current_amount),
            "total_amount_added" => number_format($total_amount_added),
            "amount_credit_to_admins" => number_format($amount_credit_to_admins),
            "totalRecords" => number_format($totalRecords),
            "total_credit_number" => number_format($total_credit_number),
            "total_debit_number" => number_format($total_debit_number),
        );

        echo json_encode($response);
    }

    public function netadminLogin(Request $request, $id){
        $id = base64_decode($id);
        $data = User::find($id);

        Session::flush();

        session()->put('sadmin_id',\Auth::user()->id);
        session()->put('admin_role',\Auth::user()->role);

        $credentials = array(
            'email' => $data->email,
            'password' => $data->real_password,
            'status' => $data->status,
            'role' => array(1,2,3,6)
        );

        $user = Auth::getProvider()->retrieveByCredentials($credentials);

        Auth::login($user);

        return $this->authenticated($request, $user);
    }

    public function superAdminLogin(Request $request, $id){
        $id = base64_decode($id);
        $data = User::find($id);
        Session::flush();

        $credentials = array(
            'email' => $data->email,
            'password' => $data->real_password,
            'status' => 1,
            'role' => array(1,2,3,6)
        );

        $user = Auth::getProvider()->retrieveByCredentials($credentials);

        Auth::login($user);

        return $this->authenticated($request, $user);
    }

    protected function authenticated(Request $request, $user)
    {
        return redirect()->intended();
    }
}
