<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminAdsPlan;
use App\Models\SuperAdminAds;

class AdminAdPlan extends Controller
{
    public function index()
    {
        return view('admin.admin_ads_plan.index');
    }

    /* Process ajax request */
    public function getAdminAdsList(Request $request)
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
        // Total records
        $totalRecords = AdminAdsPlan::select('count(*) as allcount')->whereNull('admin_ads_plan.deleted_at')->where('user_id', \Auth::user()->id)->count();
        $inactiveRecords = AdminAdsPlan::select('count(*) as allcount')->where('status','0')->whereNull('admin_ads_plan.deleted_at')->where('user_id', \Auth::user()->id)->count();
        $activeRecords = AdminAdsPlan::select('count(*) as allcount')->where('status','1')->whereNull('admin_ads_plan.deleted_at')->where('user_id', \Auth::user()->id)->count();
        $deletedRecords = AdminAdsPlan::select('count(*) as allcount')->whereNotNull('admin_ads_plan.deleted_at')->where('user_id', \Auth::user()->id)->count();


        $totalRecordswithFilter = AdminAdsPlan::select('count(*) as allcount')
        ->where(function ($query) use ($searchValue) {
            $query->whereNull('admin_ads_plan.deleted_at')
                ->where('admin_ads_plan.user_id', \Auth::user()->id)
                ->where('admin_ads_plan.title', 'like', '%' . $searchValue . '%');
        })
        ->count();

        // Get records, also we have included search filter as well
        $records = AdminAdsPlan::orderBy($columnName, $columnSortOrder)
            // ->where('admin_ads_plan.status', '=', 1)
            ->whereNull('admin_ads_plan.deleted_at')
            ->where('admin_ads_plan.title', 'like', '%' . $searchValue . '%')
            ->where('admin_ads_plan.user_id', \Auth::user()->id)            
            ->select('admin_ads_plan.*')
            // ->leftJoin('plans', 'plans.id', '=', 'AdminAdsPlan.AdminAdsPlan_id')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();

        foreach ($records as $record) {
            if($record->status == 1){
                $status = '<a onchange="updateStatus(\''.url('admin-ads-update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary mr-2"><input type="checkbox" value="1" checked id="accountSwitch{{$record->id}}"><span class="slider round"></span></label> </a>';
            }else{
                $status = '<a onchange="updateStatus(\''.url('admin-ads-update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary   mr-2"><input type="checkbox" value="0" id="accountSwitch{{$record->id}}"><span class="slider round"></span></label></a>';
            }
            // if($record->discount_type == 'percent'){
            //      $discount = $this->getDiscountValue($record).' <small>(-'.$record->discount.'%)</small>';
            // }else{
            //     $discount = $this->getDiscountValue($record).' <small>(-INR'.$record->discount.')</small>';
            // }
            $data_arr[] = array(
                "title" => $record->title,
                "description" => $record->description,
                "price" => 'INR'.$record->price,
                "image" => '<img src="'.$record->image.'" width="100px">',
                // "discount" => 'INR'.$discount,
                "max_profit_percentage_for_admin" => $record->max_profit_percentage_for_admin.'%',
                "status" => $status,

                "created_at" => date('j M Y h:i a',strtotime($record->created_at)),
                "action" => '<div class="action-btn">
                        <a  href="admin-ads-edit/'.base64_encode($record->id).'"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>
                        <a href="javascript:;" onclick="deleteRowModal(\''.base64_encode($record->id).'\')"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>
                      </div>',
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

    public function create(){  
        $this->data['super_admin_ads'] = SuperAdminAds::whereNull('deleted_at')->get();              
        return view('admin.admin_ads_plan.add', $this->data);
    }

    public function edit($id){                
        $id = base64_decode($id);
        $this->data['super_admin_ads'] = SuperAdminAds::whereNull('deleted_at')->get();
        $this->data['ad_plan'] = AdminAdsPlan::where('id', $id)->first();
        return view('admin.admin_ads_plan.add', $this->data);
    }

    public function save(Request $request){
        $request->validate([
            'title' => 'required',
            'price' => 'required'                                
        ]);  
        ;
        
        
        $admin_ad_plan = !empty($request->id) ? AdminAdsPlan::firstwhere('id',$request->id) : new AdminAdsPlan();                       

        
        $admin_ad_plan->title = $request->input('title');
        $admin_ad_plan->super_admin_ad = $request->input('super_admin_ad');
        $admin_ad_plan->price = $request->input('price');
        $admin_ad_plan->time_slot = $request->input('time_slot');
        $admin_ad_plan->schedule = $request->input('schedule_time');        
        $admin_ad_plan->validity = $request->input('validity') ?? 30;        
        $admin_ad_plan->image = $request->input('image') ?? '';        
        $admin_ad_plan->user_id = \Auth::user()->id;

        // print_r($admin_ad_plan); exit;

        if ($admin_ad_plan->save()) {
            return redirect()->back()->with('message', !empty($request->id) ? 'Ad updated Successfully !!' :  'Ad added Successfully !!');
        }
        else{
            return redirect()->back()->with('error', 'something went wrong !');
        }
        

    }

    public function destroy(Request $request){
        $ad_plan = AdminAdsPlan::where('id',base64_decode($request->id))->first();
        $ad_plan->deleted_at = time();
        if($ad_plan->save()){        
            echo json_encode(['message','Ad Plan deleted successfully']);
        }else{
            echo json_encode(['message','Ad Plan not deleted successfully']);
        }
    }

    public function updateStatus($id){
        $ad_plan = AdminAdsPlan::find(base64_decode($id));
        if($ad_plan){
            $ad_plan->status = $ad_plan->status == '1' ? '0' : '1';
            $ad_plan->save();
            echo json_encode(['message','Ad Plan status updated successfully']);
        }else{
            echo json_encode(['message','Something went wrong!!']);
        }
    }
}
