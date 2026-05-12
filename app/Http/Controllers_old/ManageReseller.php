<?php

namespace App\Http\Controllers;

use App\Models\AdminPlan;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;


class ManageReseller extends Controller
{
    public function index(){
        $this->data['admins'] = User::where('role',3)->get();
        return view('admin.reseller.index',$this->data);
    }

    public function getResellerList(Request $request){
        $loggedUser = \Auth::user();
        // echo $loggedUser->id; exit();
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
        if($loggedUser->role == 1){
            // Total records
            $totalRecords = User::select('count(*) as allcount')->where('role',3)->whereNull('users.deleted_at')->count();
            $inactiveRecords = User::select('count(*) as allcount')->where(['role' => 3,'status'=>'0'])->whereNull('users.deleted_at')->count();
            $activeRecords = User::select('count(*) as allcount')->where(['role' => 3,'status'=>'1'])->whereNull('users.deleted_at')->count();
            $deletedRecords = User::select('count(*) as allcount')->where('role',3)->whereNotNull('users.deleted_at')->count();

            $totalRecordswithFilter = User::select('count(*) as allcount')

            ->where('users.role',3)
            ->where('name', 'like', '%' . $searchValue . '%')

            ->orWhere(function($query) use ($searchValue,$loggedUser)
            {
                $query->where('users.city', 'like', '%' . $searchValue . '%')
                ->whereNull('users.deleted_at')
                ;

            })

            ->orWhere(function($query) use ($searchValue,$loggedUser)
            {
                $query->where('users.email', 'like', '%' . $searchValue . '%')
                ->whereNull('users.deleted_at')
                ;

            })

            ->orWhere(function($query)  use ($searchValue,$loggedUser)
            {
                $query->Where('users.mobile', 'like', '%' . $searchValue . '%')
                ->whereNull('users.deleted_at')
                ;

            })
            ->count();

            // Get records, also we have included search filter as well
            $records = User::orderBy($columnName, $columnSortOrder)

            ->where('role',3)
            ->whereNull('users.deleted_at')
            ->where('users.name', 'like', '%' . $searchValue . '%')
            ->orWhere(function($query) use ($searchValue,$loggedUser)
            {
                $query->where('users.city', 'like', '%' . $searchValue . '%')
                ->where('role',3)
                ->whereNull('users.deleted_at')
                ;

            })

            ->orWhere(function($query)  use ($searchValue,$loggedUser)
            {
                $query->Where('users.mobile', 'like', '%' . $searchValue . '%')
                ->where('role',3)
                ->whereNull('users.deleted_at')
                ;

            })
            ->orWhere(function($query)  use ($searchValue,$loggedUser)
            {
                $query->Where('users.email', 'like', '%' . $searchValue . '%')
                ->where('role',3)
                ->whereNull('users.deleted_at')
                ;

            })


            // ->orWhere('Users.description', 'like', '%' . $searchValue . '%')
            // ->orWhere('Users.contact_email', 'like', '%' . $searchValue . '%')
            ->select('users.*')
            // ->leftJoin('users', 'clientusers.id', '=', 'Channel.user_id')
            ->skip($start)
            ->take($rowperpage)
            ->get();
        }else{
            // Total records
            $totalRecords = User::select('count(*) as allcount')->where(['users.role'=>3,'users.created_by'=>$loggedUser->id])->whereNull('users.deleted_at')->count();
            $inactiveRecords = User::select('count(*) as allcount')->where(['users.role'=>3,'users.created_by'=>$loggedUser->id])->where('status','0')->whereNull('users.deleted_at')->count();
            $activeRecords = User::select('count(*) as allcount')->where(['users.role'=>3,'users.created_by'=>$loggedUser->id])->where('status','1')->whereNull('users.deleted_at')->count();
            $deletedRecords = User::select('count(*) as allcount')->where(['users.role'=>3,'users.created_by'=>$loggedUser->id])->whereNotNull('users.deleted_at')->count();

            $totalRecordswithFilter = User::select('count(*) as allcount')
            ->where('users.created_by',$loggedUser->id)->where('role',3)
            ->where('name', 'like', '%' . $searchValue . '%')

            ->orWhere(function($query) use ($searchValue,$loggedUser)
            {
                $query->where('users.city', 'like', '%' . $searchValue . '%')
                ->whereNull('users.deleted_at')
                ->where('users.created_by',$loggedUser->id);

            })

            ->orWhere(function($query) use ($searchValue,$loggedUser)
            {
                $query->where('users.email', 'like', '%' . $searchValue . '%')
                ->whereNull('users.deleted_at')
                ->where('users.created_by',$loggedUser->id);

            })

            ->orWhere(function($query)  use ($searchValue,$loggedUser)
            {
                $query->Where('users.mobile', 'like', '%' . $searchValue . '%')
                ->whereNull('users.deleted_at')
                ->where('users.created_by',$loggedUser->id);

            })
            ->count();

            // Get records, also we have included search filter as well
            $records = User::orderBy($columnName, $columnSortOrder)
            ->where('users.created_by',$loggedUser->id)->where('role',3)
            ->whereNull('users.deleted_at')
            ->where('users.name', 'like', '%' . $searchValue . '%')
            ->whereNull('users.deleted_at')
            ->orWhere(function($query) use ($searchValue,$loggedUser)
            {
                $query->where('users.city', 'like', '%' . $searchValue . '%')
                ->where('role',3)
                ->whereNull('users.deleted_at')
                ->where('users.created_by',$loggedUser->id);

            })

            ->orWhere(function($query)  use ($searchValue,$loggedUser)
            {
                $query->where('users.mobile', 'like', '%' . $searchValue . '%')
                ->where('role',3)
                ->whereNull('users.deleted_at')
                ->where('users.created_by',$loggedUser->id);

            })
            ->orWhere(function($query)  use ($searchValue,$loggedUser)
            {
                $query->where('users.email', 'like', '%' . $searchValue . '%')
                ->where('role',3)
                ->whereNull('users.deleted_at')
                ->where('users.created_by',$loggedUser->id);

            })


            // ->orWhere('Users.description', 'like', '%' . $searchValue . '%')
            // ->orWhere('Users.contact_email', 'like', '%' . $searchValue . '%')
            ->select('users.*')
            // ->leftJoin('users', 'clientusers.id', '=', 'Channel.user_id')
            ->skip($start)
            ->take($rowperpage)
            ->get();
        }

        $data_arr = array();

        foreach ($records as $record) {
            if($record->status == 1){
                $status = '<a onchange="updateStatus(\''.url('reseller/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary mr-2"><input type="checkbox" value="1" checked id="accountSwitch{{$record->id}}"><span class="slider round"></span></label> </a>';
            }else{
                $status = '<a onchange="updateStatus(\''.url('reseller/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary   mr-2"><input type="checkbox" value="0" id="accountSwitch{{$record->id}}"><span class="slider round"></span></label></a>';
            }
            $data_arr[] = array(
                "name" => $record->name,
                "email" => $record->email,
                "wallet_amount" => $record->current_amount,
                "mobile" => $record->mobile,
                // "role" => $record->role,
                "address" => $record->address,
                "city" => $record->city,
                "country" => $record->country,
                "company_name" => $record->company_name,
                "status" => $status,
                "created_at" => date('j M Y h:i a',strtotime($record->created_at)),
                "action" => '<div class="action-btn">
                        <a  href="edit-reseller/'.base64_encode($record->id).'"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>
                        <a href="javascript:;" onclick="delete_item(\''.base64_encode($record->id).'\',\'reseller\')"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>
                      </div>',
                "signIn" => '<a href="admin-signin/'.base64_encode($record->id).'" data-toggle="tooltip" data-placement="top" title="Sign IN"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg></a>',
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

    public function getResellerListByAdmin(Request $request){
        $draw = $request->get('draw');
        $totalRecords = User::select('count(*) as allcount')->where(['created_by'=>$request->admin_id,'role'=>3])->count();
        $inactiveRecords = User::select('count(*) as allcount')->where(['created_by'=>$request->admin_id,'role' => 3,'status'=>'0'])->whereNull('users.deleted_at')->count();
        $activeRecords = User::select('count(*) as allcount')->where(['created_by'=>$request->admin_id,'role' => 3,'status'=>'1'])->whereNull('users.deleted_at')->count();
        $deletedRecords = User::select('count(*) as allcount')->where(['created_by'=>$request->admin_id,'role'=>3])->whereNotNull('users.deleted_at')->count();
        $totalRecordswithFilter = User::select('count(*) as allcount')->where('users.created_by',$request->admin_id)->where('role',3)->count();

        $records = User::where('users.created_by',$request->admin_id)->where('role',3)->whereNull('users.deleted_at')->get();
        $data_arr = array();

        foreach ($records as $record) {
            if($record->status == 1){
                $status = '<a onchange="updateStatus(\''.url('reseller/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary mr-2"><input type="checkbox" value="1" checked id="accountSwitch{{$record->id}}"><span class="slider round"></span></label> </a>';
            }else{
                $status = '<a onchange="updateStatus(\''.url('reseller/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary   mr-2"><input type="checkbox" value="0" id="accountSwitch{{$record->id}}"><span class="slider round"></span></label></a>';
            }
            $data_arr[] = array(
                "name" => $record->name,
                "email" => $record->email,
                "mobile" => $record->mobile,
                // "role" => $record->role,
                "address" => $record->address,
                "city" => $record->city,
                "country" => $record->country,
                "company_name" => $record->company_name,
                "status" => $status,
                "created_at" => date('j M Y h:i a',strtotime($record->created_at)),
                "action" => '<div class="action-btn">
                        <a  href="edit-reseller/'.base64_encode($record->id).'"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>
                        <a href="javascript:;" onclick="delete_item(\''.base64_encode($record->id).'\',\'reseller\')"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>
                      </div>',
                "signIn" => '<a href="admin-signin/'.base64_encode($record->id).'" data-toggle="tooltip" data-placement="top" title="Sign IN"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg></a>',
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

    public function addReseller(){
        $this->data['roles'] = Role::where('status',1)->get();
        $this->data['packages'] = AdminPlan::where('user_id',\Auth::user()->id)->where('status',1)->get();
        return view('admin.reseller.add',$this->data);
    }

    public function add(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            // 'mobile' => 'required',
            // 'address' => 'required',
            // 'password' => 'required',
            // 'address' => 'required',
            // 'country' => 'required',
            // 'city' => 'required',
            // 'hf_number' => 'required',
            // 'street_number' => 'required',
            // 'pincode' => 'required',
            // 'landmark' => 'required',
        ]);

        if(!empty($request->id)){
            $emailExists = User::where('email',$request->email)->where('created_by',\Auth::user()->id)->where('id','!=',$request->id)->first();
            if($emailExists){
                return back()->with('error','This email already exists.');
            }
            $user = User::firstwhere('id',$request->id);

            $real_password = $request->password;
            if ($real_password) {                
                $password = bcrypt($real_password);
            }
            // $password = md5($hash_pass1);

            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile = $request->mobile ?? null;
            $user->address = $request->address ?? null;
            $user->hf_number = $request->hf_number ?? null;
            $user->street_number = $request->street_number ?? null;
            $user->landmark = $request->landmark ?? null;
            $user->country = $request->country ?? null;
            $user->city = $request->city ?? null;
            $user->pincode = $request->pincode ?? null;
            $user->company_name = $request->company_name ?? null;
            $user->password = $password ?? null;
            $user->real_password = $real_password ?? null;
            $user->role = 3;            
            $user->status = $request->status;
            if($user->save()){
                return back()->with('message','Reseller updated successfully');
            }else{
                return back()->with('message','Reseller not updated successfully');
            }

        }else{
            $emailExists = User::where('email',$request->email)->where('created_by',\Auth::user()->id)->first();
            if($emailExists){
                return back()->with('error','This email already exists.');
            }

            $user = new User();
            $real_password = $request->password;
            if ($real_password) {                
                $password = bcrypt($real_password);
            }
            // $password = md5($hash_pass1);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile = $request->mobile ?? null;
            $user->address = $request->address ?? null;
            $user->hf_number = $request->hf_number ?? '';
            $user->street_number = $request->street_number ?? '';
            $user->landmark = $request->landmark ?? '';
            $user->country = $request->country ?? '';
            $user->city = $request->city ?? '';
            $user->pincode = $request->pincode ?? '';
            $user->company_name = $request->company_name ?? '';
            $user->password = $password ?? '';
            $user->real_password = $real_password ?? '';
            $user->role = 3;
            $user->created_by = \Auth::user()->id;
            $user->status = $request->status;
            
            if($user->save()){
                return back()->with('message','Reseller added successfully');
            }else{
                return back()->with('message','Reseller not added successfully');
            }
        }

    }

    public function updateStatus($id){
        $user = User::find(base64_decode($id));


        if($user){
            $user->status = $user->status == '1' ? '0' : '1';
            $user->save();
            echo json_encode(['message','Reseller status successfully']);
        }else{
            echo json_encode(['message','Something went wrong!!']);
        }
    }

    public function editReseller($id){
        $this->data['user'] = User::where('id',base64_decode($id))->first();
        $this->data['roles'] = Role::where('status',1)->where('id','>',1)->get();
        return view('admin.reseller.add',$this->data);
    }

    public function destroy(Request $request){
        $user = User::where('id',base64_decode($request->id))->first();
        $user->deleted_at = time();
        if($user->save()){
            echo json_encode(['message','Admin deleted successfully']);
        }else{
            echo json_encode(['message','Admin not deleted successfully']);
        }
    }

    public function checkEmail(Request $request){
        $email = $request->email;
        $check = User::where('email',$email)->first();
        if($check){
            return false;
        }else{
            return true;
        }
    }
}
