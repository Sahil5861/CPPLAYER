<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;

class ManageRole extends Controller
{
    public function index()
    {
        return view('admin.role.index');
    }

    /*get rolse by ajax*/
    public function getRoleList(Request $request)
    {
        $columns = array(
            0 =>'id',
            1 =>'title',
            // 2=> 'body',
            2=> 'created_at',
            // 4=> 'id',
        );

        $totalData = Role::whereNull('deleted_at')->count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        // $order = $columns[$request->input('order[0].column')];
        if($request->input('order.0.column')){
            $order = $columns[$request->input('order.0.column')];
        }else{
            $order = 'id';
        }
        if($request->input('order.0.column')){
            $dir = $request->input('order.0.dir');
        }else{
            $dir = 'desc';
        }
        

        if(empty($request->input('search.value')))
        {
        $roles = Role::offset($start)
        ->whereNull('deleted_at')
        ->limit($limit)
        ->orderBy($order,$dir)
        ->get();
        }
        else {
        $search = $request->input('search.value');

        $roles = Role::where('id','LIKE',"%{$search}%")
        ->whereNull('deleted_at')
        ->orWhere('title', 'LIKE',"%{$search}%")

        ->offset($start)
        ->limit($limit)
        ->orderBy($order,$dir)
        ->get();

        $totalFiltered = Role::where('id','LIKE',"%{$search}%")
        ->orWhere('title', 'LIKE',"%{$search}%")
        ->count();
        }

        $data = array();
        if(!empty($roles))
        {
            foreach ($roles as $role)
            {
                // $show = route('roles.show',$role->id);
                // $edit = route('roles.edit',$role->id);

                $rolesData['id'] = $role->id;
                $rolesData['title'] = $role->title;
                if($role->status == 1){
                    $rolesData['status'] = '<a onchange="updateStatus(\''.url('role/update-status',base64_encode($role->id)).'\')" href="javascript:void(0);"><label class="switch s-primary mr-2"><input type="checkbox" value="1" checked id="accountSwitch{{$role->id}}"><span class="slider round"></span></label> </a>';
                }else{
                    $rolesData['status'] = '<a onchange="updateStatus(\''.url('role/update-status',base64_encode($role->id)).'\')" href="javascript:void(0);"><label class="switch s-primary   mr-2"><input type="checkbox" value="0" id="accountSwitch{{$role->id}}"><span class="slider round"></span></label></a>';
                }
                
                $rolesData['created_at'] = date('j M Y h:i a',strtotime($role->created_at));
                // $rolesData['action'] = '<div class="action-btn"><a></a></div>';

                $rolesData['action'] = '<div class="action-btn">
                        <a  href="edit-role/'.base64_encode($role->id).'"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>
                        
                      </div>';

                $data[] = $rolesData;

            }
        }
        // <a href="javascript:;" onclick="delete_item(\''.base64_encode($role->id).'\',\'role\')"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>
        $json_Roles_data = array(
        "draw" => intval($request->input('draw')),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data"=> $data
        );

        echo json_encode($json_Roles_data);
    }

    public function addRole(){
        return view('admin.role.add');
    }


    public function add(Request $request){
        $request->validate([
            'title' => 'required',
        ]);

        if(!empty($request->id)){
            $role = Role::firstwhere('id',$request->id);
            $role->title = $request->title;
            $role->status = $request->status;
            if($role->save()){
                return back()->with('message','Role updated successfully');
            }else{
                return back()->with('message','Role not updated successfully');
            }
            
        }else{
            $role = new Role();
            $role->title = $request->title;
            $role->status = $request->status;
            if($role->save()){
                return back()->with('message','Role added successfully');
            }else{
                return back()->with('message','Role not added successfully');
            }
        }
        
    }

    public function editRole($id){ 
        $this->data['role'] = Role::where('id',base64_decode($id))->first();
        // print_r($this->data['role']);die;
        return view('admin.role.add',$this->data);
    }

    public function updateStatus($id){ 
        $role = Role::find(base64_decode($id));

        if($role){
            $role->status = $role->status == '1' ? '0' : '1';
            $role->save();
            echo json_encode(['message','Role status successfully']);
        }else{
            echo json_encode(['message','Something went wrong!!']);   
        }
    }

    public function destroy(Request $request){
        // $role = Role::firstwhere('id',$request->id);
        $role = Role::where('id',base64_decode($request->id))->first();
        $role->deleted_at = time();
        if($role->save()){
            echo json_encode(['message','Role deleted successfully']);
        }else{
            echo json_encode(['message','Role not deleted successfully']);
        }
    }
}
