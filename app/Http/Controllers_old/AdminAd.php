<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminAds;
use App\Models\SuperAdminAds;

class AdminAd extends Controller
{
    public function index()
    {
        return view('admin.admin_ads.index');
    }

    /* Process ajax request */
    public function getAdminAdsPlanList(Request $request)
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
        $totalRecords = AdminAds::select('count(*) as allcount')->whereNull('admin_ads.deleted_at')->where('user_id', \Auth::user()->id)->count();
        $inactiveRecords = AdminAds::select('count(*) as allcount')->where('status','0')->whereNull('admin_ads.deleted_at')->where('user_id', \Auth::user()->id)->count();
        $activeRecords = AdminAds::select('count(*) as allcount')->where('status','1')->whereNull('admin_ads.deleted_at')->where('user_id', \Auth::user()->id)->count();
        $deletedRecords = AdminAds::select('count(*) as allcount')->whereNotNull('admin_ads.deleted_at')->where('user_id', \Auth::user()->id)->count();


        $totalRecordswithFilter = AdminAds::select('count(*) as allcount')
        ->where(function ($query) use ($searchValue) {
            $query->whereNull('admin_ads.deleted_at')
                ->where('admin_ads.user_id', \Auth::user()->id)
                ->where('admin_ads.title', 'like', '%' . $searchValue . '%');
        })
        ->count();

        // Get records, also we have included search filter as well
        $records = AdminAds::orderBy($columnName, $columnSortOrder)
            // ->where('admin_ads.status', '=', 1)
            ->whereNull('admin_ads.deleted_at')
            ->where('admin_ads.title', 'like', '%' . $searchValue . '%')
            ->where('admin_ads.user_id', \Auth::user()->id)            
            ->select('admin_ads.*')
            // ->leftJoin('plans', 'plans.id', '=', 'AdminAds.AdminAds_id')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();

        foreach ($records as $record) {
            if($record->status == 1){
                $status = '<a onchange="updateStatus(\''.url('admin-ads-plan-update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary mr-2"><input type="checkbox" value="1" checked id="accountSwitch{{$record->id}}"><span class="slider round"></span></label> </a>';
            }else{
                $status = '<a onchange="updateStatus(\''.url('admin-ads-plan-update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary   mr-2"><input type="checkbox" value="0" id="accountSwitch{{$record->id}}"><span class="slider round"></span></label></a>';
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
                "profit_price" => 'INR'.$record->profit_price,
                "total_price" => 'INR'.$record->total_price,
                // "discount" => 'INR'.$discount,
                "max_profit_percentage_for_admin" => $record->max_profit_percentage_for_admin.'%',
                "status" => $status,

                "created_at" => date('j M Y h:i a',strtotime($record->created_at)),
                "action" => '<div class="action-btn">
                        <a  href="admin-ads-plan-edit/'.base64_encode($record->id).'"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>
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
        $super_admin_ads = SuperAdminAds::whereNull('deleted_at')->get();            
        return view('admin.admin_ads.add', compact('super_admin_ads'));
    }

    public function getAdDetails(Request $request){
        $id = $request->id;
        $super_admin_ad = SuperAdminAds::where('id', $id)->first();
        if ($super_admin_ad) {
            return response()->json([
                'status' => true,
                'data' => $super_admin_ad
            ]);
        }
        else{
            return response()->json([
                'status' => false,
                'message' => 'No Ad found !'
            ]);
        }
    }

    public function edit($id){   
        $this->data['super_admin_ads'] = SuperAdminAds::whereNull('deleted_at')->get();                         
        $id = base64_decode($id);
        $this->data['ad_plan'] = AdminAds::where('id', $id)->first();
        return view('admin.admin_ads.add', $this->data);
    }

    public function save(Request $request){

        // print_r($request->all()); exit;
        $request->validate([
            'title' => 'required',
            'profit_price' => 'required'                                
        ]);                
        $admin_ad = !empty($request->id) ? AdminAds::firstwhere('id',$request->id) : new AdminAds();                       

        $admin_ad->title = $request->input('title');
        $admin_ad->price = $request->input('price');
        $admin_ad->profit_price = $request->input('profit_price');
        $admin_ad->total_price = $request->input('total_price');
        $admin_ad->time_slot = $request->input('time_slot');
        $admin_ad->schedule = $request->input('schdule_time');        
        $admin_ad->status = $request->input('status');        
        $admin_ad->validity = $request->input('validity') ?? 30;        
        $admin_ad->user_id = \Auth::user()->id;

        // print_r($admin_ad); exit;

        if ($admin_ad->save()) {
            return redirect()->back()->with('message', !empty($request->id) ? 'Ad updated Successfully !!' :  'Ad added Successfully !!');
        }
        else{
            return redirect()->back()->with('error', 'something went wrong !');
        }
        

    }

    public function destroy(Request $request){
        $ad_plan = AdminAds::where('id',base64_decode($request->id))->first();
        $ad_plan->deleted_at = time();
        if($ad_plan->save()){        
            echo json_encode(['message','Ad Plan deleted successfully']);
        }else{
            echo json_encode(['message','Ad Plan not deleted successfully']);
        }
    }

    public function updateStatus($id){
        $ad_plan = AdminAds::find(base64_decode($id));
        if($ad_plan){
            $ad_plan->status = $ad_plan->status == '1' ? '0' : '1';
            $ad_plan->save();
            echo json_encode(['message','Ad Plan status updated successfully']);
        }else{
            echo json_encode(['message','Something went wrong!!']);
        }
    }
}
