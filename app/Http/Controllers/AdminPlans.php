<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Channel;
use App\Models\Genre;
use App\Models\PackageChannel;
use App\Models\Language;
use App\Models\AdminPlan;
use App\Models\SadminPlan;
use App\Models\AdminSuperAdminPlan;
use DB;

class AdminPlans extends Controller
{
    public function index()
    {
        return view('admin.admin_plan.index');
    }

    /* Process ajax request */
    public function getAdminPlanList(Request $request)
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
        $totalRecords = AdminPlan::select('count(*) as allcount')->where('user_id',\Auth::user()->id)->whereNull('admin_plans.deleted_at')->count();
        $inactiveRecords = AdminPlan::select('count(*) as allcount')->where('user_id',\Auth::user()->id)->where('status','0')->whereNull('admin_plans.deleted_at')->count();
        $activeRecords = AdminPlan::select('count(*) as allcount')->where('user_id',\Auth::user()->id)->where('status','1')->whereNull('admin_plans.deleted_at')->count();
        $deletedRecords = AdminPlan::select('count(*) as allcount')->where('user_id',\Auth::user()->id)->whereNotNull('admin_plans.deleted_at')->count();


        $totalRecordswithFilter = AdminPlan::select('count(*) as allcount')->where('user_id',\Auth::user()->id)->whereNull('admin_plans.deleted_at')
        ->orWhere('admin_plans.title', 'like', '%' . $searchValue . '%')
        ->count();

        // Get records, also we have included search filter as well
        $records = AdminPlan::orderBy($columnName, $columnSortOrder)
            // ->where('admin_plans.status', '=', 1)
            ->whereNull('admin_plans.deleted_at')
            ->where('admin_plans.title', 'like', '%' . $searchValue . '%')
            ->where('user_id',\Auth::user()->id)
            ->orWhere(function($query) use ($searchValue)
            {
                $query->where('admin_plans.description', 'like', '%' . $searchValue . '%')
                ->where('user_id',\Auth::user()->id)
                ->whereNull('admin_plans.deleted_at');

            })
            ->select('admin_plans.*')
            // ->leftJoin('plans', 'plans.id', '=', 'AdminPlan.AdminPlan_id')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();

        foreach ($records as $record) {
            if($record->status == 1){
                $status = '<a onchange="updateStatus(\''.url('adminPlan/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary mr-2"><input type="checkbox" value="1" checked id="accountSwitch{{$record->id}}"><span class="slider round"></span></label> </a>';
            }else{
                $status = '<a onchange="updateStatus(\''.url('adminPlan/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary   mr-2"><input type="checkbox" value="0" id="accountSwitch{{$record->id}}"><span class="slider round"></span></label></a>';
            }
            // if($record->discount_type == 'percent'){
            //      $discount = $this->getDiscountValue($record).' <small>(-'.$record->discount.'%)</small>';
            // }else{
            //     $discount = $this->getDiscountValue($record).' <small>(-INR'.$record->discount.')</small>';
            // }
            $data_arr[] = array(
                "title" => $record->title,
                "description" => $record->description,
                "price" => $record->price.' Credits',
                "total_price" => $record->total_price.' Credits',
                // "discount" => $discount,
                // "max_profit_percentage_for_admin" => $record->max_profit_percentage_for_admin,
                "status" => $status,

                "created_at" => date('j M Y h:i a',strtotime($record->created_at)),
                "action" => '<div class="action-btn">
                        <a  href="edit-adminPlan/'.base64_encode($record->id).'"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>
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

    public function addAdminPlan(){
        $this->data['super_admin_plans'] = SadminPlan::where('status',1)->whereNull('deleted_at')->with('getChannel')->get();
        return view('admin.admin_plan.add',$this->data);
    }


    public function add(Request $request){
        $request->validate([
            'title' => 'required',
            'price' => 'required',
            // 'description' => 'required',
            // 'super_admin_plan' => 'required',
            'plan_validity' => 'required',
            // 'discount' =>'required'
        ]);
                
        // print_r($request->all()); exit;
        // $maxvalue = 0;
        // $minvalue = 0;
        // foreach ($Plans as $key => $value) {
        //     // code...
        //     $maxvalue += (int)$value->max_profit_price_for_admin;
        //     $minvalue += (int)$value->min_profit_price_for_admin;
        // }

        // if($request->profit_price > $maxvalue){
        //     return back()->with('message',"Profit price should not be grater than ".'INR'.$maxvalue);
        // }else if($request->profit_price < $minvalue){
        //     return back()->with('message',"Profit price should not be less than ".'INR'.$minvalue);
        // }

        // if(($request->total_price > $Plans->plan_max_price) || ($request->price + $request->profit_price) > $Plans->plan_max_price){
        //     return back()->with('message',"Total price should not be grater than ".$Plans->plan_max_price.".");
        // }

        if(!empty($request->id)){
            // if(\Auth::user()->current_amount < $request->price){
            //     return back()->with('message','Insufficient Amount!!');
            // }

            $plan = AdminPlan::firstwhere('id',$request->id);
            $plan->title = $request->title;
            $plan->price = $request->price;
            $plan->description = $request->description;
            // $plan->max_profit_percentage_for_admin = $request->max_profit_percentage_for_admin;
            $plan->plan_validity = $request->plan_validity;
            // $plan->discount_type = $request->discount_type;
            // $plan->discount = $request->discount;
            // $plan->profit_percentage = $request->profit_percentage;
            $plan->profit_price = $request->profit_price ?? 0;
            $plan->max_price = $request->max_price;
            $plan->min_price = $request->min_price;
            // $plan->min_profit_percentage_for_reseller = $request->min_profit_percentage_for_reseller;
            // $plan->max_profit_percentage_for_reseller = $request->max_profit_percentage_for_reseller;
            // $plan->min_profit_price_for_reseller = $request->min_profit_price_for_reseller;
            // $plan->max_profit_price_for_reseller = $request->max_profit_price_for_reseller;
            $plan->total_price = $request->total_price;
            $plan->user_id = \Auth::user()->id;
            $plan->status = $request->status;
            if($plan->save()){
                // AdminSuperAdminPlan::where('admin_plan_id',$plan->id)->delete();
                // print_r($request->super_admin_plan); exit;
                // foreach ($super_admin_plan as $key => $s_plan) {
                //     $super_admin_plan = SadminPlan::find($s_plan);
                //     $adminSuperAdminPlan = new AdminSuperAdminPlan();
                //     $adminSuperAdminPlan->super_admin_plan_id = $s_plan;
                //     $adminSuperAdminPlan->admin_plan_id = $plan->id;
                //     $adminSuperAdminPlan->super_admin_plan_price = $super_admin_plan->price;
                //     $adminSuperAdminPlan->save();
                
                // }


                return back()->with('message','Plan updated successfully');
            }else{
                return back()->with('message','Plan not updated successfully');
            }

        }else{
            // if(\Auth::user()->current_amount < $request->price){
            //     return back()->with('message','Insufficient Amount!!');
            // }
            $plan = new AdminPlan();
            $plan->title = $request->title;
            $plan->price = $request->price;
            $plan->description = $request->description;
            // $plan->super_admin_plan = $request->super_admin_plan;
            $plan->plan_validity = $request->plan_validity;
            $plan->user_id = \Auth::user()->id;
            // $plan->discount_type = $request->discount_type;
            // $plan->discount = $request->discount;
            // $plan->discount_type = $request->discount_type;
            // $plan->profit_percentage = $request->profit_percentage;
            $plan->profit_price = $request->profit_price ?? 0;
            $plan->max_price = $request->max_price;
            $plan->min_price = $request->min_price;
            // $plan->min_profit_percentage_for_reseller = $request->min_profit_percentage_for_reseller;
            // $plan->max_profit_percentage_for_reseller = $request->max_profit_percentage_for_reseller;
            // $plan->min_profit_price_for_reseller = $request->min_profit_price_for_reseller;
            // $plan->max_profit_price_for_reseller = $request->max_profit_price_for_reseller;
            $plan->total_price = $request->total_price;
            $plan->status = $request->status;
            if($plan->save()){
                // PackageChannel::where('plan_id',$plan->id)->delete();
                // $super_admin_plan = explode(',',$request->super_admin_plan);
                // foreach ($super_admin_plan as $key => $s_plan) {
                //     $super_admin_plan = SadminPlan::find($s_plan);
                //     $adminSuperAdminPlan = new AdminSuperAdminPlan();
                //     $adminSuperAdminPlan->super_admin_plan_id = $s_plan;
                //     $adminSuperAdminPlan->admin_plan_id = $plan->id;
                //     $adminSuperAdminPlan->super_admin_plan_price = $super_admin_plan->price;
                //     $adminSuperAdminPlan->save();
                // }
                return back()->with('message','Plan added successfully');
            }else{
                return back()->with('message','Plan not added successfully');
            }
        }

    }

    public function editAdminPlan($id){
        $adminSuperAdminPlan = AdminSuperAdminPlan::where('admin_plan_id',base64_decode($id))->get();
        // echo '<pre>';
        // print_r($adminSuperAdminPlan ); exit;
        $this->data['admin_s_plan_ids'] = [];
        if(count($adminSuperAdminPlan) > 0){
            foreach ($adminSuperAdminPlan as $key => $obj) {
                $this->data['admin_s_plan_ids'][] = $obj->super_admin_plan_id;
            }
        }
        // echo base64_decode($id);
        // print_r($this->data['admin_s_plan_ids']); exit();
        $this->data['plan'] = AdminPlan::where('id',base64_decode($id))->first();

        $this->data['super_admin_plans'] = SadminPlan::where('status',1)->whereNull('deleted_at')->with('getChannel')->get();
        return view('admin.admin_plan.add',$this->data);

        // $this->data['channels'] = Channel::where('status',1)->get();
        // return view('admin.plan.add',$this->data);
    }


    public function destroy(Request $request){
        $plan = AdminPlan::where('id',base64_decode($request->id))->first();
        $plan->deleted_at = time();
        if($plan->save()){
            $resellerPlan = DB::table('reseller_admin_plans')->where('admin_plan_id',base64_decode($request->id))->first();
            if(isset($resellerPlan)){
                DB::table('reseller_plans')->where('id',$resellerPlan->reseller_plan_id)->update(['deleted_at'=>time()]);
            }
            echo json_encode(['message','Plan deleted successfully']);
        }else{
            echo json_encode(['message','Plan not deleted successfully']);
        }
    }

    public function updateStatus($id){
        $plan = AdminPlan::find(base64_decode($id));
        if($plan){
            $plan->status = $plan->status == '1' ? '0' : '1';
            $plan->save();
            echo json_encode(['message','Plan status updated successfully']);
        }else{
            echo json_encode(['message','Something went wrong!!']);
        }
    }

    // public function getPrice(Request $request){
    //     $plan_validity =  $request->plan_validity;
    //     $super_admin_plan_arr = explode(',',$request->super_admin_plan);

    //     if(!empty($super_admin_plan_arr)){
    //         $Plans = SadminPlan::whereIn('id',$super_admin_plan_arr)->get();
    //         $price = 0;
    //         foreach ($Plans as $key => $value) {
    //             $price += ($value->discount_type == 'flat') ?  $value->price - $value->discount : $value->price - (($value->price * $value->discount)/100);
    //             $price = ($price / 30) * $plan_validity;
    //         }
    //         print_r(json_encode(array('status'=> true, 'price'=> round($price,2))));
    //         exit;
    //     }else{
    //         print_r(json_encode(array('status'=> false, 'msg'=> 'super_admin_plan is empty')));
    //         exit;
    //     }
    // }

    public function getPrice(Request $request){
        $plan_validity =  $request->plan_validity;
        $super_admin_plan_arr = explode(',',$request->super_admin_plan);

        if(!empty($super_admin_plan_arr)){
            $Plans = SadminPlan::whereIn('id',$super_admin_plan_arr)->get();
            $price = 0;
            foreach ($Plans as $key => $value) {
                $price += ($value->price / $value->plan_validity) * $plan_validity;
            }
            print_r(json_encode(array('status'=> true, 'price'=> round($price,2))));
            exit;
        }else{
            print_r(json_encode(array('status'=> false, 'msg'=> 'super_admin_plan is empty')));
            exit;
        }
    }


    public function checkMaxSellingPriceForAdmin(Request $request){
        $plan_validity =  $request->plan_validity;
        $super_admin_plan_arr = explode(',',$request->super_admin_plan);
        $plan_price = $request->price;
        $profit_price = $request->profit_price;

        if(!empty($super_admin_plan_arr)){
            $Plans = SadminPlan::whereIn('id',$super_admin_plan_arr)->get();
            $maxvalue = 0;
            foreach ($Plans as $key => $value) {
                // $price = ($value->price / $value->plan_validity) * $plan_validity;
                $maxvalue +=$value->max_profit_price_for_admin;
            }

            if($maxvalue < $profit_price){
                print_r(json_encode(array("status"=>true,"less"=>false,"msg"=>"Profit price should not be grater than ".'INR'.round($maxvalue,2), 'profit_price' => round($profit_price,2))));
                exit;
            }else{
                print_r(json_encode(array("status"=>false,"msg"=>"Profit price is valid.", 'profit_price' => round($profit_price,2))));
                exit;
            }
        }else{
            print_r(json_encode(array("status"=>false,"msg"=>"super_admin_plan is empty")));
            exit;
        }

    }


    // public function checkMaxSellingPriceForAdmin(Request $request){
    //     $plan_validity =  $request->plan_validity;
    //     $super_admin_plan_arr = explode(',',$request->super_admin_plan);
    //     $profit_percentage = $request->profit_percentage;
    //     $plan_price = $request->price;

    //     if(!empty($super_admin_plan_arr) && $plan_price != ''){
    //         $Plans = SadminPlan::whereIn('id',$super_admin_plan_arr)->get();
    //         $maxvalue = 0;
    //         $minvalue = 0;
    //         foreach ($Plans as $key => $value) {
    //             $price = ($value->discount_type == 'flat') ?  $value->price - $value->discount : $value->price - (($value->price * $value->discount)/100);
    //             $price = ($price / $value->plan_validity) * $plan_validity;
    //             $maxvalue += ($price * $value->max_profit_percentage_for_admin) /100;
    //             $minvalue += ($price * $value->min_profit_percentage_for_admin) /100;
    //         }

    //         $profit_price = ($plan_price * $profit_percentage)/100;

    //         if($minvalue > $profit_price){
    //             print_r(json_encode(array("status"=>true,"less"=>true,"msg"=>"Price should not be less than ".'INR'.round($minvalue,2), 'profit_percentage' => $profit_percentage, 'profit_price' => round($profit_price,2))));
    //             exit;
    //         }elseif($maxvalue < $profit_price){
    //             print_r(json_encode(array("status"=>true,"less"=>false,"msg"=>"Price should not be grater than ".'INR'.round($maxvalue,2), 'profit_percentage' => $profit_percentage, 'profit_price' => round($profit_price,2))));
    //             exit;
    //         }else{
    //             print_r(json_encode(array("status"=>false,"msg"=>"price is valid.", 'profit_percentage' => $profit_percentage, 'profit_price' => round($profit_price,2))));
    //             exit;
    //         }
    //     }else{
    //         print_r(json_encode(array("status"=>false,"msg"=>"price or super_admin_plan is empty")));
    //         exit;
    //     }

    // }


    // public function getPlanDetails(Request $request){
    //     $super_admin_plan_arr = explode(',',$request->super_admin_plan);
    //     if(!empty($super_admin_plan_arr)){
    //         $plans = SadminPlan::where('id',$super_admin_plan_arr[0])->first();
    //         if($plans){
    //             print_r(json_encode(array(
    //                 'status' => true,
    //                 'title' => $plans->title,
    //                 'validity' => $plans->plan_validity,
    //                 'price' => $plans->price,
    //                 'description' => $plans->description,
    //                 'plan_max_price' => $plans->plan_max_price,
    //             )));
    //             exit;
    //         }else{
    //             print_r(json_encode(array(
    //                 'status' => false,
    //                 'message' => 'Something went wrong.'
    //             )));
    //             exit;
    //         }
    //     }
    // }

    public function getPlanDetails(Request $request){
        $super_admin_plan = $request->super_admin_plan;
        // if(!empty($super_admin_plan_arr)){
        //     $plans = SadminPlan::where('id',$super_admin_plan_arr)->first();
        //     if($plans){
        //         print_r(json_encode(array(
        //             'status' => true,
        //             'title' => $plans->title,
        //             'validity' => $plans->plan_validity,
        //             'price' => $plans->price,
        //             'description' => $plans->description,
        //             'plan_max_price' => $plans->plan_max_price,
        //         )));
        //         exit;
        //     }else{
        //         print_r(json_encode(array(
        //             'status' => false,
        //             'message' => 'Something went wrong.'
        //         )));
        //         exit;
        //     }
        // }

        return response()->json([
            'status' => true,
            'data' => SadminPlan::where('title',$super_admin_plan)->first()
        ]);


    }

    

    public function checkChannelsOfPlan(Request $request){
        $plan_id = $request->super_admin_plan;
        $output = '';
        foreach ($plan_id as $key => $value) {
            $channel[] = DB::select("SELECT channels.* FROM `channels` LEFT JOIN package_channels on package_channels.channel_id = channels.id where package_channels.plan_id = ".$value." GROUP BY channels.id");
        }
        if(isset($channel) && count($channel) > 0){
            foreach ($channel as $key => $value) {
                foreach ($channel[$key] as $key1 => $value1) {
                    $output .= '<option>'.$value1->channel_name.'</option>';
                }
            }
        }
        return $output; exit;
        // print_r($output); exit;
    }
}
