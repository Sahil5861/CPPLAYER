<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class ManageAdmin extends Controller
{
    public function index()
    {
        return view('admin.admin.index');
    }

    /* Process ajax request */
    public function getAdminList(Request $request)
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
        $totalRecords = User::select('count(*) as allcount')->where('role',2)->count();
        $inactiveRecords = User::select('count(*) as allcount')->where('role',2)->where('status','0')->whereNull('users.deleted_at')->count();
        $activeRecords = User::select('count(*) as allcount')->where('role',2)->where('status','1')->whereNull('users.deleted_at')->count();
        $deletedRecords = User::select('count(*) as allcount')->where('role',2)->whereNotNull('users.deleted_at')->count();

        $totalRecordswithFilter = User::select('count(*) as allcount')
        ->where('name', 'like', '%' . $searchValue . '%')
        ->where('role',2)
        ->orWhere(function($query) use ($searchValue)
        {
            $query->where('users.city', 'like', '%' . $searchValue . '%')
            ->where('role',2)
            ->whereNull('users.deleted_at');

        })

        ->orWhere(function($query)  use ($searchValue)
        {
            $query->Where('users.company_name', 'like', '%' . $searchValue . '%')
            ->where('role',2)
            ->whereNull('users.deleted_at');

        })
        ->count();

        // Get records, also we have included search filter as well
        $records = User::orderBy($columnName, $columnSortOrder)

            ->whereNull('users.deleted_at')
            ->where('users.name', 'like', '%' . $searchValue . '%')
            ->where('role',2)
            ->whereNull('users.deleted_at')
            ->orWhere(function($query) use ($searchValue)
            {
                $query->where('users.city', 'like', '%' . $searchValue . '%')
                ->where('role',2)
                ->whereNull('users.deleted_at');

            })

            ->orWhere(function($query)  use ($searchValue)
            {
                $query->Where('users.company_name', 'like', '%' . $searchValue . '%')
                ->where('role',2)
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
                $status = '<a onchange="updateStatus(\''.url('admin/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary mr-2"><input type="checkbox" value="1" checked id="accountSwitch{{$record->id}}"><span class="slider round"></span></label> </a>';
            }else{
                $status = '<a onchange="updateStatus(\''.url('admin/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary   mr-2"><input type="checkbox" value="0" id="accountSwitch{{$record->id}}"><span class="slider round"></span></label></a>';
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
                        <a  href="edit-admin/'.base64_encode($record->id).'"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a></div>':'<div class="action-btn">
                        <a  href="edit-admin/'.base64_encode($record->id).'"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>
                        <a href="javascript:;" onclick="delete_item(\''.base64_encode($record->id).'\',\'admin\')"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>
                        <a href="/app-domain/'.base64_encode($record->id).'" title="App Domain Content">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" 
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" 
                        class="feather feather-settings">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06
                            a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 
                            0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 
                            1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 
                            0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06a1.65 
                            1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 
                            0 0 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 
                            0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 
                            1.65 0 0 0 1.82.33h.09a1.65 1.65 0 0 0 1-1.51V3a2 
                            2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 
                            0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06a1.65 
                            1.65 0 0 0-.33 1.82v.09a1.65 1.65 0 0 0 1.51 1H21a2 
                            2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z">
                        </path>
                    </svg>
                </a>
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

    public function addAdmin(){
        $this->data['roles'] = Role::where('status',1)->where('id','>',1)->get();
        return view('admin.admin.add',$this->data);
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
            'password' => 'required',            
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
            $user->password = $real_password;
            $user->real_password = $real_password;
            $user->role = 2;
            $user->status = $request->status;
            
            $user->hf_number = $request->hf_number ?? null;
            $user->address = $request->address ?? null;
            $user->street_number = $request->street_number ?? null;
            $user->landmark = $request->landmark ?? null;
            $user->country = $request->country ?? null;
            $user->city = $request->city ?? null;
            $user->pincode = $request->pincode ?? null;
            $user->company_name = $request->company_name ?? null;
            
            if($user->save()){
                return back()->with('message','Admin updated successfully');
            }else{
                return back()->with('message','Admin not updated successfully');
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
            $user->password = $real_password;
            $user->real_password = $real_password;
            $user->role = 2;
            $user->status = $request->status;
            if($user->save()){
                return back()->with('message','Admin added successfully');
            }else{
                return back()->with('message','Admin not added successfully');
            }
        }

    }

    public function editAdmin($id){
        $this->data['user'] = User::where('id',base64_decode($id))->first();
        $this->data['roles'] = Role::where('status',1)->where('id','>',1)->get();
        return view('admin.admin.add',$this->data);
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

    public function adminLogin(Request $request, $id){
        $id = base64_decode($id);
        $data = User::find($id);

        Session::flush();

        session()->put('sadmin_id',\Auth::user()->id);
        session()->put('admin_role',\Auth::user()->role);

        $credentials = array(
            'email' => $data->email,
            'password' => $data->real_password,
            'status' => $data->status,
            'role' => array(1,2,3)
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
            'role' => array(1,2,3)
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
