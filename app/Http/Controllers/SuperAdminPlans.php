<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Channel;
use App\Models\Genre;
use App\Models\PackageChannel;
use App\Models\Language;
use App\Models\SadminPlan;
use App\Models\ChannelGenre;
use DB;

class SuperAdminPlans extends Controller
{
    public function index()
    {
        return view('admin.plan.index');
    }

    /* Process ajax request */
    public function getSadminPlanList(Request $request)
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
        $totalRecords = SadminPlan::select('count(*) as allcount')->whereNull('super_admin_plans.deleted_at')->count();
        $inactiveRecords = SadminPlan::select('count(*) as allcount')->where('status','0')->whereNull('super_admin_plans.deleted_at')->count();
        $activeRecords = SadminPlan::select('count(*) as allcount')->where('status','1')->whereNull('super_admin_plans.deleted_at')->count();
        $deletedRecords = SadminPlan::select('count(*) as allcount')->whereNotNull('super_admin_plans.deleted_at')->count();


        $totalRecordswithFilter = SadminPlan::select('count(*) as allcount')->whereNull('super_admin_plans.deleted_at')
        ->orWhere('super_admin_plans.title', 'like', '%' . $searchValue . '%')
        ->count();

        // Get records, also we have included search filter as well
        $records = SadminPlan::orderBy($columnName, $columnSortOrder)
            // ->where('super_admin_plans.status', '=', 1)
            ->whereNull('super_admin_plans.deleted_at')
            ->where('super_admin_plans.title', 'like', '%' . $searchValue . '%')
            ->orWhere(function($query) use ($searchValue)
            {
                $query->where('super_admin_plans.description', 'like', '%' . $searchValue . '%')
                ->whereNull('super_admin_plans.deleted_at');

            })
            ->select('super_admin_plans.*')
            // ->leftJoin('plans', 'plans.id', '=', 'SadminPlan.SadminPlan_id')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();

        foreach ($records as $record) {
            if($record->status == 1){
                $status = '<a onchange="updateStatus(\''.url('sadminPlan/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary mr-2"><input type="checkbox" value="1" checked id="accountSwitch{{$record->id}}"><span class="slider round"></span></label> </a>';
            }else{
                $status = '<a onchange="updateStatus(\''.url('sadminPlan/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary   mr-2"><input type="checkbox" value="0" id="accountSwitch{{$record->id}}"><span class="slider round"></span></label></a>';
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
                // "discount" => 'INR'.$discount,
                "max_profit_percentage_for_admin" => $record->max_profit_percentage_for_admin.'%',
                "status" => $status,

                "created_at" => date('j M Y h:i a',strtotime($record->created_at)),
                "action" => '<div class="action-btn">
                        <a  href="edit-sadminPlan/'.base64_encode($record->id).'"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a>
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

    public function addSadminPlan(){
        // $this->data['languages'] = Language::where('status',1)->get();
        $this->data['channels'] = Channel::where('status',1)->whereNull('deleted_at')->get();
        $this->data['genres'] = Genre::where('status',1)->whereNull('deleted_at')->get();
        return view('admin.plan.add',$this->data);
    }


    public function add(Request $request){
        $request->validate([
            'title' => 'required',
            'price' => 'required',
            'description' => 'sometimes',
            // 'max_profit_percentage_for_admin' => 'required',
            // 'max_profit_price_for_admin' => 'required',
            'plan_type' => 'required',
            // 'plan_max_price' => 'required',
        ]);
        // if(count($request->channels) < 1){
        //     return back()->with('error','Please select channels.');
        // }

        // $plan_max_price = DB::table('plan_max_price')->where('id',1)->first();

        // if($request->plan_type == 0 && $request->price > $plan_max_price->amount){
        //     return back()->with('message','price should not be greater than '.$plan_max_price->amount.'.');
        // }

        if(!empty($request->id)){

            $plan = SadminPlan::firstwhere('id',$request->id);
            $plan->title = $request->title;
            $plan->price = $request->price;
            // $plan->net_admin_price = $request->net_admin_price;
            $plan->description = $request->description ?? '';
            // $plan->min_profit_percentage_for_admin = $request->min_profit_percentage_for_admin;
            // $plan->max_profit_percentage_for_admin = $request->max_profit_percentage_for_admin;
            // $plan->min_profit_price_for_admin = $request->min_profit_price_for_admin;
            // $plan->max_profit_price_for_admin = $request->max_profit_price_for_admin;
            // $plan->currency = 'INR';
            // $plan->discount_type = $request->discount_type;
            // $plan->discount = $request->discount;
            // $plan->discount_type = $request->discount_type;
            $plan->plan_type = $request->plan_type;
            $plan->status = $request->status;
            // $plan->genre = $request->genre;
            // $plan->plan_max_price = $request->plan_max_price;
            

            $validity = 30;
            if ($request->plan_type == 1) {
                $validity = $request->plan_validity ? $request->plan_validity : 30;
            }
            else{
                $validity = 3;
            }
            $plan->plan_validity = $validity;
            if($plan->save()){
                // PackageChannel::where('plan_id',$plan->id)->delete();
                // foreach ($request->channels as $key => $channel) {
                //     $PackageChannel = new PackageChannel();
                //     $PackageChannel->channel_id = $channel;
                //     $PackageChannel->plan_id = $plan->id;
                //     $PackageChannel->save();
                // }

                return back()->with('message','Plan updated successfully');
            }else{
                return back()->with('message','Plan not updated successfully');
            }

        }else{


            $plan = new SadminPlan();
            $plan->title = $request->title;
            $plan->price = $request->price;
            // $plan->net_admin_price = $request->net_admin_price;
            $plan->description = $request->description ?? '';
            // $plan->min_profit_percentage_for_admin = $request->min_profit_percentage_for_admin;
            // $plan->max_profit_percentage_for_admin = $request->max_profit_percentage_for_admin;
            // $plan->min_profit_price_for_admin = $request->min_profit_price_for_admin;
            // $plan->max_profit_price_for_admin = $request->max_profit_price_for_admin;
            // $plan->currency = 'INR';
            // $plan->discount_type = $request->discount_type;
            // $plan->discount = $request->discount;
            // $plan->discount_type = $request->discount_type;
            $plan->plan_type = $request->plan_type;
            $plan->status = $request->status;
            // $plan->genre = $request->genre;
            // $plan->plan_max_price = $request->plan_max_price;
            // $plan->plan_validity = $request->plan_validity ? $request->plan_validity : 30;

            $validity = 30;
            if ($request->plan_type == 1) {
                $validity = $request->plan_validity ? $request->plan_validity : 30;
            }
            else{
                $validity = 3;
            }
            $plan->plan_validity = $validity;
            if($plan->save()){

                // foreach ($request->channels as $key => $channel) {
                //     $PackageChannel = new PackageChannel();
                //     $PackageChannel->channel_id = $channel;
                //     $PackageChannel->plan_id = $plan->id;
                //     $PackageChannel->save();
                // }
                return back()->with('message','Plan added successfully');
            }else{
                return back()->with('message','Plan not added successfully');
            }
        }

    }

    public function editSadminPlan($id){
        $packageChannel = PackageChannel::where('plan_id',base64_decode($id))->get();
        $this->data['genres'] = Genre::where('status',1)->whereNull('deleted_at')->get();
        $this->data['channel_ids'] = [];
        if($packageChannel){
            foreach ($packageChannel as $key => $obj) {
                $this->data['channel_ids'][] = $obj->channel_id;
            }
        }
        // echo base64_decode($id);
        // print_r($this->data['channel_ids']); exit();
        $this->data['plan'] = SadminPlan::where('id',base64_decode($id))->first();
        $this->data['channels'] = Channel::where('status',1)->whereNull('deleted_at')->get();
        return view('admin.plan.add',$this->data);
    }


    public function destroy(Request $request){
        $plan = SadminPlan::where('id',base64_decode($request->id))->first();
        $plan->deleted_at = time();
        if($plan->save()){
            $adminPlan = DB::table('admin_super_admin_plans')->where('super_admin_plan_id',base64_decode($request->id))->first();
            if(isset($adminPlan)){
                DB::table('admin_plans')->where('id',$adminPlan->admin_plan_id)->update(['deleted_at'=>time()]);

                $resellerPlan = DB::table('reseller_admin_plans')->where('admin_plan_id',$adminPlan->admin_plan_id)->first();
                if(isset($resellerPlan)){
                    DB::table('reseller_plans')->where('id',$resellerPlan->reseller_plan_id)->update(['deleted_at'=>time()]);
                }
            }

            echo json_encode(['message','Plan deleted successfully']);
        }else{
            echo json_encode(['message','Plan not deleted successfully']);
        }
    }

    public function updateStatus($id){
        $plan = SadminPlan::find(base64_decode($id));
        if($plan){
            $plan->status = $plan->status == '1' ? '0' : '1';
            $plan->save();
            echo json_encode(['message','Plan status updated successfully']);
        }else{
            echo json_encode(['message','Something went wrong!!']);
        }
    }

    public function getChannelByGenre(Request $request){
        if($request->genre == 'all'){
            $response = Channel::where('status',1)->whereNull('deleted_at')->get();
        }else{
            $response = ChannelGenre::select('channels.*')->leftJoin('channels', 'channels.id','=','channel_genre.channel_id')->where(['channel_genre.genre_id'=>$request->genre,'channel_genre.status'=>1])->get();
        }

        return json_encode($response); exit;
    }
}
