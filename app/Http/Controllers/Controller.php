<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use DB;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public function getDiscountValue($obj)
    {
        // code...
        if($obj->discount_type == 'flat'){
            return $obj->price - $obj->discount;
        }else{
            $discount = ($obj->discount / 100) * $obj->price;
            return $obj->price - $discount;   
        }
    }

    public function planMaxPrice(){
        $this->data['plan_max_price'] = DB::table('plan_max_price')->where('id',1)->first();
        return view('admin.plan_max_price',$this->data);
    }

    public function updatePlanMaxPrice(Request $request){
        $request->validate([
            'amount' => 'required',
        ]);

        $plan_max_price = DB::table('plan_max_price')->where('id',1)->update(['amount' => $request->amount]);
        // $plan_max_price->amount = $request->amount;
        if($plan_max_price){
            return back()->with('message','Plan max price updated successfully');
        }else{
            return back()->with('message','Plan max price not updated successfully');
        }

    }
}
