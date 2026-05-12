<?php

namespace App\Http\Controllers;

use App\Models\ResellerWallet;
use App\Models\AdminWallet;
use App\Models\User;
use Illuminate\Http\Request;
use DB;

class ResellerWallets extends Controller
{
    public function index(){
        return view('admin.wallet.reseller-wallet-list');
    }

    public function getResellerWalletList(Request $request) {
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

        $totalRecords = ResellerWallet::select('count(*) as allcount')->where('user_id',\Auth::user()->id)->orWhere('credit_amount_by',\Auth::user()->id)->count();

        $total_amount_added = ResellerWallet::where('user_id',\Auth::user()->id)->sum('credit_amount');

        $amount_credit_to_admins = ResellerWallet::where('credit_amount_by',\Auth::user()->id)->sum('debit_amount');

        $total_credit_number = ResellerWallet::select('count(*) as total_credit_number')->where('user_id', \Auth::user()->id)->count();
        $total_debit_number = ResellerWallet::select('count(*) as total_debit_number')->where('credit_amount_by', \Auth::user()->id)->count();


        $totalRecordswithFilter = DB::select("SELECT count(*) as allcount  FROM credit_debit_reseller_amounts cr LEFT JOIN clientusers cu ON IF(cr.credit_amount=0.00, cu.id=cr.user_id, cu.id=cr.credit_amount_by) WHERE cr.user_id = ".\Auth::user()->id." OR cr.credit_amount_by = ".\Auth::user()->id." AND cr.credit_amount LIKE '%".$searchValue."%' OR cr.debit_amount LIKE '%".$searchValue."%' AND cr.deleted_at IS NULL ORDER BY cr.created_at desc");

        // Get records, also we have included search filter as well
        // $records = ResellerWallet::orderBy($columnName, $columnSortOrder)
        //     ->where('credit_debit_reseller_amounts.credit_amount_by',\Auth::user()->id)
        //     ->whereNull('credit_debit_reseller_amounts.deleted_at')
        //     ->where('credit_debit_reseller_amounts.credit_amount', 'like', '%' . $searchValue . '%')
        //     ->orWhere(function($query) use ($searchValue)
        //     {
        //         $query->where('credit_debit_reseller_amounts.debit_amount', 'like', '%' . $searchValue . '%')
        //         ->whereNull('credit_debit_reseller_amounts.deleted_at')
        //         ->where('credit_debit_reseller_amounts.credit_amount_by',\Auth::user()->id);

        //     })
        //     ->orWhere(function ($query){
        //         $query->where('credit_debit_reseller_amounts.user_id', '=', \Auth::user()->id);
        //     })
        //     // ->orWhere('credit_debit_reseller_amounts.user_id', '=', \Auth::user()->id)
        //     ->select('credit_debit_reseller_amounts.*')->with('user')
        //     // ->leftJoin('wallets', 'wallets.id', '=', 'AdminWallet.AdminWallet_id')
        //     ->skip($start)
        //     ->take($rowperpage)
        //     ->get();

        $records = DB::select("SELECT *, cr.id as transaction_id  FROM credit_debit_reseller_amounts cr LEFT JOIN clientusers cu ON IF(cr.credit_amount=0.00, cu.id=cr.user_id, cu.id=cr.credit_amount_by) WHERE cr.user_id = ".\Auth::user()->id." OR cr.credit_amount_by = ".\Auth::user()->id." AND cr.credit_amount LIKE '%".$searchValue."%' OR cr.debit_amount LIKE '%".$searchValue."%' AND cr.deleted_at IS NULL ORDER BY cr.created_at desc LIMIT ".$rowperpage." OFFSET ".$start);

        $data_arr = array();

        // $admin = User::where('id', \Auth::user()->created_by)->first();

        foreach ($records as $record) {
            // if($record->status == 1){
            //     $status = '<a onchange="updateStatus(\''.url('sadminWallet/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary mr-2"><input type="checkbox" value="1" checked id="accountSwitch{{$record->id}}"><span class="slider round"></span></label> </a>';
            // }else{
            //     $status = '<a onchange="updateStatus(\''.url('sadminWallet/update-status',base64_encode($record->id)).'\')" href="javascript:void(0);"><label class="switch s-primary   mr-2"><input type="checkbox" value="0" id="accountSwitch{{$record->id}}"><span class="slider round"></span></label></a>';
            // }
            // $credit  = false;
            // if($record->user_id == \Auth::user()->id){
            //     // credit
            //     $credit  = true;
            // }else{
            //     // debit
            //     $credit  = false;
            // }
            $data_arr[] = array(
                "id" => $record->transaction_id,
                "email" => $record->email ,
                // "credit_amount" => $record->credit_amount && $credit ? '<span style="color:green">+'.$record->credit_amount.'</span>' : '--',
                // "debit_amount" => $record->credit_amount && !$credit ? '<span style="color:red">-'.$record->credit_amount.'</span>' : '--',
                "credit_amount" => $record->credit_amount && $record->credit_amount != '0.00' ? '<span style="color:green">+'.$record->credit_amount.'</span>' : '--',
                "debit_amount" => $record->debit_amount && $record->debit_amount != '0.00' ? '<span style="color:red">-'.$record->debit_amount.'</span>' : '--',
                "credit_for" => $record->name,
                // "wallet_amount" => $record['user']->current_amount,
                
                "message" => $record->message,
                // "status" => $status,

                "created_at" => date('j M Y h:i a',strtotime($record->created_at)),
                // "action" => '',
            );
        }
        // <a href="javascript:;" onclick="delete_item(\''.base64_encode($record->id).'\',\'sadminWallet\')"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>
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

    public function addResellerWallet(){
        $this->data['users'] = User::where(['role'=>3,'created_by'=>\Auth::user()->id, 'status' => 1])->whereNull('users.deleted_at')->get();
        return view('admin.wallet.reseller-wallet',$this->data);
    }

    public function add(Request $request){
        $request->validate([
            'amount' => 'required',
            'user_id' => 'required',
            // 'message' => 'required',
        ]);

        if(\Auth::user()->current_amount < $request->amount){
            return back()->with('message','Insufficient Amount!!');
        }

        $wallet_d = new AdminWallet();
        $wallet_d->debit_amount = $request->amount;
        $wallet_d->user_id = $request->user_id;
        $wallet_d->message = $request->message;
        $wallet_d->credit_amount_by = \Auth::user()->id;
        $wallet_d->save();

        $wallet = new ResellerWallet();
        $wallet->credit_amount = $request->amount;
        $wallet->user_id = $request->user_id;
        $wallet->message = $request->message;
        $wallet->credit_amount_by = \Auth::user()->id;
        if($wallet->save()){

            $admin = User::find(\Auth::user()->id);
            $admin->current_amount = $admin->current_amount - $request->amount;
            $admin->save();

            $admin = User::find($request->user_id);
            $admin->current_amount = $admin->current_amount + $request->amount;
            $admin->save();

            return back()->with('message','Wallet added successfully');
        }else{
            return back()->with('message','Wallet not added successfully');
        }
    }
}
