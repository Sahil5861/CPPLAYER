<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ClientUser;
use App\Models\Genre;
use App\Models\PackageChannel;
use App\Models\Language;
use App\Models\AdminWallet;
use App\Models\Channel;

class AdminWallets extends Controller
{
    public function index()
    {
        // echo \Auth::user()->id; exit();
        return view('admin.wallet.admin-wallet');
    }

    /* Process ajax request */
    public function getAdminWalletList(Request $request)
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

        $totalRecords = AdminWallet::select('count(*) as allcount')->where('user_id',\Auth::user()->id)->orWhere('user_id',\Auth::user()->id)->count();

        $total_credit_number = AdminWallet::select('count(*) as total_credit_number')->where('user_id', \Auth::user()->id)->count();
        $total_debit_number = AdminWallet::select('count(*) as total_debit_number')->where('credit_amount_by', \Auth::user()->id)->count();

        $total_amount_added = AdminWallet::where('user_id',\Auth::user()->id)->sum('credit_amount');

        $amount_credit_to_admins = AdminWallet::where('credit_amount_by',\Auth::user()->id)->sum('debit_amount');


        $totalRecordswithFilter = AdminWallet::select('count(*) as allcount')
            ->where('credit_debit_admin_amounts.credit_amount_by',\Auth::user()->id)
            ->whereNull('credit_debit_admin_amounts.deleted_at')
            ->where('credit_debit_admin_amounts.credit_amount', 'like', '%' . $searchValue . '%')
            ->orWhere(function($query) use ($searchValue)
            {
                $query->where('credit_debit_admin_amounts.debit_amount', 'like', '%' . $searchValue . '%')
                ->whereNull('credit_debit_admin_amounts.deleted_at')
                ->where('credit_debit_admin_amounts.credit_amount_by',\Auth::user()->id);

            })
            ->orWhere('credit_debit_admin_amounts.user_id', '=', \Auth::user()->id)


        ->count();

        // Get records, also we have included search filter as well
        $records = AdminWallet::orderBy($columnName, $columnSortOrder)
            ->where('credit_debit_admin_amounts.credit_amount_by',\Auth::user()->id)
            ->whereNull('credit_debit_admin_amounts.deleted_at')
            ->where('credit_debit_admin_amounts.credit_amount', 'like', '%' . $searchValue . '%')
            ->orWhere(function($query) use ($searchValue)
            {
                $query->where('credit_debit_admin_amounts.debit_amount', 'like', '%' . $searchValue . '%')
                ->whereNull('credit_debit_admin_amounts.deleted_at')
                ->where('credit_debit_admin_amounts.credit_amount_by',\Auth::user()->id);

            })
            ->orWhere('credit_debit_admin_amounts.user_id', '=', \Auth::user()->id)
            ->select('credit_debit_admin_amounts.*')->with('user')
            // ->leftJoin('wallets', 'wallets.id', '=', 'AdminWallet.AdminWallet_id')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();

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
                "id" => $record->id,
                "email" => $record['user'] ? $record['user']->email : 'User deleted' ,
                // "credit_amount" => $record->credit_amount && $credit ? '<span style="color:green">+'.$record->credit_amount.'</span>' : '--',
                // "debit_amount" => $record->credit_amount && !$credit ? '<span style="color:red">-'.$record->credit_amount.'</span>' : '--',
                "credit_amount" => $record->credit_amount ? '<span style="color:green">+'.$record->credit_amount.'</span>' : '--',
                "debit_amount" => $record->debit_amount ? '<span style="color:red">-'.$record->debit_amount.'</span>' : '--',
                "credit_for" => $record['user'] ? $record['user']->name : 'User deleted',
                "wallet_amount" => $record['user'] ? $record['user']->current_amount : '0',
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
            "iTotalDisplayRecords" => $totalRecordswithFilter,
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

    public function addAdminWallet(){
        // $this->data['languages'] = Language::where('status',1)->get();
        $this->data['users'] = User::where('created_by',\Auth::user()->id)->where('status',1)->get();
        return view('admin.wallet.add',$this->data);
    }


    public function add(Request $request){
        $request->validate([
            'amount' => 'required',
            'user_id' => 'required',
            // 'message' => 'required',
        ]);

        if(!empty($request->id)){

            // $wallet = AdminWallet::firstwhere('id',$request->id);
            // $wallet->credit_amount = $request->amount;
            // $wallet->user_id = $request->user_id;
            // $wallet->message = $request->message;
            // if($wallet->save()){
            //     if(\Auth::user()->id == $request->user_id){
            //         $user = User::find($request->user_id);
            //         $user->current_amount = $user->current_amount + $request->credit_amount;
            //     }else{
            //         $superadmin = User::find(\Auth::user()->id);
            //         $superadmin->current_amount = $superadmin->current_amount - $request->credit_amount;

            //         $user = User::find($request->user_id);
            //         $user->current_amount = $user->current_amount + $request->credit_amount;
            //     }

            //     return back()->with('message','Wallet updated successfully');
            // }else{
                return back()->with('message','Wallet not updated successfully');
            // }

        }else{
            // debit record
            if($request->user_id != 1){
                $wallet_d = new AdminWallet();
                $wallet_d->debit_amount = $request->amount;
                $wallet_d->user_id = $request->user_id;
                $wallet_d->message = $request->message;
                $wallet_d->credit_amount_by = \Auth::user()->id;
                $wallet_d->save();
            }


            // credit record
            $wallet = new AdminWallet();
            $wallet->credit_amount = $request->amount;
            $wallet->user_id = $request->user_id;
            $wallet->message = $request->message;
            $wallet->credit_amount_by = \Auth::user()->id;
            if($wallet->save()){

                $superadmin = User::find(\Auth::user()->id);
                $superadmin->current_amount = $superadmin->current_amount - $request->amount;
                $superadmin->save();

                $user = ClientUser::find($request->user_id);
                $user->current_amount = $user->current_amount + $request->amount;
                $user->save();
                return back()->with('message','Wallet added successfully');
            }else{
                return back()->with('message','Wallet not added successfully');
            }
        }

    }

    public function editAdminWallet($id){
        $packageChannel = PackageChannel::where('wallet_id',base64_decode($id))->get();
        $this->data['channel_ids'] = [];
        if($packageChannel){
            foreach ($packageChannel as $key => $obj) {
                $this->data['channel_ids'][] = $obj->channel_id;
            }
        }
        // echo base64_decode($id);
        // print_r($this->data['channel_ids']); exit();
        $this->data['wallet'] = AdminWallet::where('id',base64_decode($id))->first();
        $this->data['channels'] = Channel::where('status',1)->get();
        return view('admin.wallet.add',$this->data);
    }


    public function destroy(Request $request){
        $wallet = AdminWallet::where('id',base64_decode($request->id))->first();
        $wallet->deleted_at = time();
        if($wallet->save()){
            echo json_encode(['message','Wallet deleted successfully']);
        }else{
            echo json_encode(['message','Wallet not deleted successfully']);
        }
    }

    public function updateStatus($id){
        $wallet = AdminWallet::find(base64_decode($id));
        if($wallet){
            $wallet->status = $wallet->status == '1' ? '0' : '1';
            $wallet->save();
            echo json_encode(['message','Wallet status updated successfully']);
        }else{
            echo json_encode(['message','Something went wrong!!']);
        }
    }
}
