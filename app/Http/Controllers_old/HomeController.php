<?php

namespace App\Http\Controllers;

use App\Models\AdminPlan;
use App\Models\AdminSuperAdminPlan;
use App\Models\AdminWallet;
use App\Models\ResellerWallet;
use App\Models\SadminPlan;
use App\Models\SadminWallet;
use App\Models\ResellerPlan;
use App\Models\User;
use App\Models\Role;
use App\Models\ClientUser;
use App\Models\AppDomainContent;
use App\Models\ResellerAdminPlan;
use App\Models\UserPlanDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;

class HomeController extends Controller
{
    public function index()
    {

        $user = Auth::user();
        $month = array('Jan'=>'01','Feb'=>'02','Mar'=>'03','Apr'=>'04','May'=>'05','Jun'=>'06','Jul'=>'07','Aug'=>'08','Sep'=>'09','Oct'=>'10','Nov'=>'11','Dec'=>'12');
        // $month = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');

        if($user->role == 1){
            //total users
            $this->data['total_admin'] = User::where(['role'=>2])->whereNull('deleted_at')->count();
            $this->data['total_reseller'] = User::where(['role'=>3])->whereNull('deleted_at')->count();
            $this->data['total_user'] = ClientUser::where(['role'=>4])->whereNull('deleted_at')->count();

            //total plan sold
            $this->data['total_plan_sold'] = AdminSuperAdminPlan::count();

            //wallet amount
            // $this->data['wallet'] = SadminWallet::select(DB::Raw('SUM(credit_amount) AS total_credit, SUM(debit_amount) AS total_debit, (SUM(credit_amount)-SUM(debit_amount)) as wallet_amount'))->first();
            $this->data['wallet'] = round($user->current_amount,2);

            //recently purchase plan
            // $this->data['recently_purchase_plan'] = AdminSuperAdminPlan::select('super_admin_plans.id','super_admin_plans.status','super_admin_plans.title','super_admin_plans.price','admin_super_admin_plans.created_at as purchase_at','count(admin_super_admin_plans.super_admin_plan_id) as total_plan_count')->leftJoin('super_admin_plans','admin_super_admin_plans.super_admin_plan_id', '=', 'super_admin_plans.id')->groupBy('admin_super_admin_plans.super_admin_plan_id')->orderBy('admin_super_admin_plans.id','desc')->limit(10)->get();
            $this->data['recently_purchase_plan'] = DB::select("SELECT super_admin_plans.id, super_admin_plans.status, super_admin_plans.title, super_admin_plans.price as total_price, admin_super_admin_plans.created_at as purchase_at, count(admin_super_admin_plans.super_admin_plan_id) as total_plan_count FROM admin_super_admin_plans LEFT JOIN super_admin_plans ON super_admin_plans.id = admin_super_admin_plans.super_admin_plan_id GROUP BY admin_super_admin_plans.super_admin_plan_id ORDER BY admin_super_admin_plans.id DESC LIMIT 10");
            

            //Revenue
            foreach ($month as $key => $value) {
                $monthlyRevenue[$key] = SadminWallet::whereMonth('created_at',$value)->whereYear('created_at',date('Y'))->sum('debit_amount');
                $monthlyPlanSell[$key] = UserPlanDetails::whereMonth('purchased_date',$value)->whereYear('created_at',date('Y'))->sum('plan_purchase_price');
            }
            $this->data['monthly_revenue'] = json_encode($monthlyRevenue);
            $this->data['monthly_plan_sell'] = json_encode($monthlyPlanSell);
            $this->data['total_revenue'] = array_sum($monthlyRevenue);

            $this->data['current_month_revenue'] = SadminWallet::whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->sum('debit_amount');
            $this->data['previous_month_revenue'] = SadminWallet::whereMonth('created_at',date('m', strtotime('-1 month')))->whereYear('created_at',date('Y'))->sum('debit_amount');

            $this->data['current_year_revenue'] = SadminWallet::whereYear('created_at',date('Y'))->sum('debit_amount');
            $this->data['previous_year_revenue'] = SadminWallet::whereYear('created_at',date('Y', strtotime('-1 year')))->sum('debit_amount');

            if($this->data['current_month_revenue'] > $this->data['previous_month_revenue']){
                $this->data['monthly_profit'] = round($this->data['current_month_revenue'],2) - round($this->data['previous_month_revenue'],2);
                $this->data['monthly_loss'] = 0;
                $monthly_profit_percentage = ($this->data['monthly_profit'] > 0 && $this->data['previous_month_revenue'] > 0) ? round(($this->data['monthly_profit'] / $this->data['previous_month_revenue']) * 100) : 0;
                $this->data['monthly_profit_percentage'] = round($monthly_profit_percentage,2);
                $this->data['monthly_loss_percentage'] = 0;
            }else{
                $this->data['monthly_profit'] = 0;
                $this->data['monthly_loss'] = round($this->data['previous_month_revenue'],2) - round($this->data['current_month_revenue'],2);
                $monthly_loss_percentage = ($this->data['monthly_loss'] > 0 && $this->data['current_month_revenue'] > 0) ? round(($this->data['monthly_loss'] / $this->data['current_month_revenue']) * 100) : 0;
                $this->data['monthly_loss_percentage'] = round($monthly_loss_percentage,2);
                $this->data['monthly_profit_percentage'] = 0;
            }

            if($this->data['current_year_revenue'] > $this->data['previous_year_revenue']){
                $this->data['yearly_profit'] = round($this->data['current_year_revenue'],2) - round($this->data['previous_year_revenue'],2);
                $this->data['yearly_loss'] = 0;
                $yearly_profit_percentage = round(($this->data['yearly_profit'] / ($this->data['previous_year_revenue'] > 0 ? $this->data['previous_year_revenue'] : 1)) / 100);
                $this->data['yearly_profit_percentage'] = round($yearly_profit_percentage,2);
                $this->data['yearly_loss_percentage'] = 0;
            }else{
                $this->data['yearly_profit'] = 0;
                $this->data['yearly_loss'] = round($this->data['previous_year_revenue'],2) - round($this->data['current_year_revenue'],2);
                $yearly_loss_percentage = round(($this->data['yearly_loss'] / ($this->data['current_year_revenue'] > 0 ? $this->data['current_year_revenue'] : 1)) * 100);
                $this->data['yearly_loss_percentage'] = round($yearly_loss_percentage,2);
                $this->data['yearly_profit_percentage'] = 0;
            }

        }elseif($user->role == 2){
            $this->data['total_reseller'] = User::where(['role'=>3, 'created_by'=> $user->id])->whereNull('deleted_at')->count();
            $this->data['total_user'] = ClientUser::where(['role'=>4, 'created_by'=> $user->id])->whereNull('deleted_at')->count();

            $reseller_purchased_plan = ResellerAdminPlan::where('admin_id',\Auth::user()->id)->count();
            $user_purchased_plan = UserPlanDetails::where('plan_purchased_by',\Auth::user()->id)->count();
            $this->data['total_plan_sold'] = $reseller_purchased_plan + $user_purchased_plan;

            // $this->data['wallet'] = AdminWallet::select(DB::Raw('SUM(credit_amount) AS total_credit, SUM(debit_amount) AS total_debit, (SUM(credit_amount)-SUM(debit_amount)) as wallet_amount'))->where('user_id',$user->id)->first();
            $this->data['wallet'] = round($user->current_amount,2);
            $monthlyProfit = [];
            foreach ($month as $key => $value) {
                $monthlyRevenue[$key] = UserPlanDetails::where('plan_purchased_by',\Auth::user()->id)->whereMonth('purchased_date',$value)->whereYear('purchased_date',date('Y'))->sum('plan_purchase_price');
                $monthlySuperAdminPrice = UserPlanDetails::where('plan_purchased_by',\Auth::user()->id)->whereMonth('purchased_date',$value)->whereYear('purchased_date',date('Y'))->sum('plan_original_price');
                if(isset($monthlyRevenue[$value]) && $monthlyRevenue[$value]){
                    $monthlyProfit[$key] = round($monthlyRevenue[$value],2) - round($monthlySuperAdminPrice,2);
                }

                $monthlyPlanSellReseller = ResellerAdminPlan::where('admin_id',\Auth::user()->id)->whereMonth('created_at',$value)->whereYear('created_at',date('Y'))->sum('admin_plan_price');
                $monthlyPlanSellUser = UserPlanDetails::where('plan_purchased_by',\Auth::user()->id)->whereMonth('purchased_date',$value)->whereYear('purchased_date',date('Y'))->sum('plan_purchase_price');
                $monthlyPlanSell[$key] = round($monthlyPlanSellReseller,2) + round($monthlyPlanSellUser,2);
            }
            $this->data['monthly_revenue'] = json_encode($monthlyRevenue);
            $this->data['monthly_profit'] = json_encode($monthlyProfit);
            $this->data['monthly_plan_sell'] = json_encode($monthlyPlanSell);
            $this->data['total_revenue'] = array_sum($monthlyRevenue);

            // $this->data['recently_purchase_plan'] = UserPlanDetails::select('admin_plans.id','admin_plans.title','admin_plans.price','user_plan_details.purchased_date as purchase_at','admin_plans.status')->leftJoin('admin_plans','user_plan_details.plan_id', '=', 'admin_plans.id')->where('user_plan_details.plan_purchased_by',$user->id)->groupBy('user_plan_details.plan_id')->orderBy('user_plan_details.id','desc')->limit(10)->get();
            $this->data['recently_purchase_plan'] = DB::select("SELECT admin_plans.id, admin_plans.title, admin_plans.price, admin_plans.total_price, user_plan_details.purchased_date as purchase_at, admin_plans.status, COUNT(user_plan_details.plan_id) as total_plan_count FROM `user_plan_details` LEFT JOIN admin_plans ON admin_plans.id = user_plan_details.plan_id WHERE user_plan_details.plan_purchased_by = ".$user->id." GROUP BY user_plan_details.plan_id ORDER BY user_plan_details.id DESC LIMIT 10");
            $this->data['recently_purchase_plan_reseller'] = DB::select("SELECT admin_plans.id, admin_plans.title, admin_plans.price, admin_plans.total_price, reseller_admin_plans.created_at as purchase_at, admin_plans.status, COUNT(reseller_admin_plans.reseller_plan_id) as total_plan_count FROM `reseller_admin_plans` LEFT JOIN admin_plans ON admin_plans.id = reseller_admin_plans.admin_plan_id WHERE reseller_admin_plans.admin_id = ".$user->id." GROUP BY reseller_admin_plans.reseller_plan_id ORDER BY reseller_admin_plans.id DESC LIMIT 10");
        }elseif($user->role == 3){
            $this->data['total_user'] = ClientUser::where(['role'=>4, 'created_by'=> $user->id])->whereNull('deleted_at')->count();
            $this->data['total_plan_sold'] = UserPlanDetails::where('plan_purchased_by',\Auth::user()->id)->count();

            // $this->data['wallet'] = ResellerWallet::select(DB::Raw('SUM(credit_amount) AS total_credit, SUM(debit_amount) AS total_debit, (SUM(credit_amount)-SUM(debit_amount)) as wallet_amount'))->where('user_id',$user->id)->first();
            $this->data['wallet'] = round($user->current_amount,2);

            foreach ($month as $key => $value) {
                $monthlyRevenue[$key] = UserPlanDetails::where('plan_purchased_by',\Auth::user()->id)->whereMonth('purchased_date',$value)->whereYear('purchased_date',date('Y'))->sum('plan_purchase_price');
                $monthlyAdminPrice = UserPlanDetails::where('plan_purchased_by',\Auth::user()->id)->whereMonth('purchased_date',$value)->whereYear('purchased_date',date('Y'))->sum('plan_original_price');
                if(isset($monthlyRevenue[$value]) && $monthlyRevenue[$value]){
                    $monthlyProfit[$key] = round($monthlyRevenue[$value],2) - round($monthlyAdminPrice,2);
                }
                // $monthlyProfit[$key] = $monthlyRevenue[$value] - $monthlyAdminPrice;
                $monthlyPlanSell[$key] = UserPlanDetails::where('plan_purchased_by',\Auth::user()->id)->whereMonth('purchased_date',$value)->whereYear('purchased_date',date('Y'))->sum('plan_purchase_price');
            }
            $this->data['monthly_revenue'] = json_encode($monthlyRevenue);
            $this->data['monthly_profit'] = isset($monthlyProfit) ? json_encode($monthlyProfit) : 0;
            $this->data['monthly_plan_sell'] = json_encode($monthlyPlanSell);
            $this->data['total_revenue'] = array_sum($monthlyRevenue);

            $this->data['recently_purchase_plan'] = DB::select("SELECT reseller_plans.id, reseller_plans.title, reseller_plans.price, user_plan_details.purchased_date as purchase_at, reseller_plans.status, COUNT(user_plan_details.plan_id) as total_plan_count FROM `user_plan_details` LEFT JOIN reseller_plans ON reseller_plans.id = user_plan_details.plan_id WHERE user_plan_details.plan_purchased_by = ".$user->id." GROUP BY user_plan_details.plan_id ORDER BY user_plan_details.id DESC LIMIT 10");
        }elseif($user->role == 6){
            $this->data['total_user'] = ClientUser::where(['role'=>4, 'created_by'=> $user->id])->whereNull('deleted_at')->count();
            $this->data['total_plan_sold'] = '';
            $this->data['wallet'] = round($user->current_amount,2);
            $this->data['monthly_revenue'] = '';
            $this->data['monthly_profit']  = '';
            $this->data['monthly_plan_sell'] = '';
            $this->data['total_revenue'] = '';
            $this->data['recently_purchase_plan'] = '';
            $this->data['total_reseller'] = '';

            $this->data['total_channel'] = DB::select("SELECT COUNT(*) as total_channel FROM netadmin_channels WHERE user_id = ".\Auth::user()->id);
            $this->data['total_active_channel'] = DB::select("SELECT COUNT(*) as total_active_channel FROM netadmin_channels WHERE status = 1 AND user_id = ".\Auth::user()->id);
            $this->data['total_inactive_channel'] = DB::select("SELECT COUNT(*) as total_inactive_channel FROM netadmin_channels WHERE status = 0 AND user_id = ".\Auth::user()->id);
            $this->data['empty_link_channel'] = DB::select("SELECT COUNT(*) as empty_link_channel FROM netadmin_channels WHERE link = '' AND user_id = ".\Auth::user()->id);
        }

        $this->data['startDate'] = '01-06-2022';
        $this->data['endDate'] = date('d-m-Y');

        return view('admin.dashboard',$this->data);
    }

    public function filter($startDate, $endDate){

        $startDate = base64_decode($startDate);
        $endDate = base64_decode($endDate);

        $new_start_date = date('01-m-Y',strtotime($startDate));
        $new_end_date = date('t-m-Y',strtotime($endDate));
        
        $user = Auth::user();
        $month = array('Jan'=>'01','Feb'=>'02','Mar'=>'03','Apr'=>'04','May'=>'05','Jun'=>'06','Jul'=>'07','Aug'=>'08','Sep'=>'09','Oct'=>'10','Nov'=>'11','Dec'=>'12');
        // $month = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');

        $startMonth = date('m',strtotime($startDate));
        $endMonth = date('m',strtotime($endDate));
        $startYear = date('Y',strtotime($startDate));
        $endYear = date('Y',strtotime($endDate));

        if($user->role == 1){
            //total users
            $admin = DB::select("SELECT COUNT(*) as total_user FROM `users` WHERE role = 2 and deleted_at is Null and DATE_FORMAT(created_at,'%d-%m-%Y') BETWEEN DATE(".$new_start_date.") AND DATE(".$new_end_date.")");
            $reseller = DB::select("SELECT COUNT(*) as total_user FROM `users` WHERE role = 3 and deleted_at is Null and DATE_FORMAT(created_at,'%d-%m-%Y') BETWEEN DATE(".$new_start_date.") AND DATE(".$new_end_date.")");
            $users = DB::select("SELECT COUNT(*) as total_user FROM `clientusers` WHERE role = 4 and deleted_at is Null and DATE_FORMAT(created_at,'%d-%m-%Y') BETWEEN DATE(".$new_start_date.") AND DATE(".$new_end_date.")");
            $this->data['total_admin'] = $admin[0]->total_user;
            $this->data['total_reseller'] = $reseller[0]->total_user;
            $this->data['total_user'] = $users[0]->total_user;

            //total plan sold
            $this->data['total_plan_sold'] = AdminSuperAdminPlan::whereBetween('created_at',[$new_start_date, $new_end_date])->count();


            //wallet amount
            // $this->data['wallet'] = SadminWallet::select(DB::Raw('SUM(credit_amount) AS total_credit, SUM(debit_amount) AS total_debit, (SUM(credit_amount)-SUM(debit_amount)) as wallet_amount'))->first();
            $this->data['wallet'] = $user->current_amount;

            //recently purchase plan
            $this->data['recently_purchase_plan'] = DB::select("SELECT super_admin_plans.id, super_admin_plans.status, super_admin_plans.title, super_admin_plans.price, admin_super_admin_plans.created_at as purchase_at, count(admin_super_admin_plans.super_admin_plan_id) as total_plan_count FROM admin_super_admin_plans LEFT JOIN super_admin_plans ON super_admin_plans.id = admin_super_admin_plans.super_admin_plan_id WHERE DATE_FORMAT(admin_super_admin_plans.created_at,'%d-%m-%Y') BETWEEN DATE(".$new_start_date.") AND DATE(".$new_end_date.") GROUP BY admin_super_admin_plans.admin_plan_id ORDER BY admin_super_admin_plans.id DESC limit 10");


            //Revenue
            foreach ($month as $key => $value) {
                if($value >= $startMonth && $value == $endMonth){
                    $walletAmount = DB::select('SELECT SUM(`debit_amount`) as total_amount FROM `credit_debit_amounts` WHERE MONTH(created_at) = '.$value.' AND YEAR(created_at) BETWEEN '.$startYear.' AND '.$endYear);
                    $monthlyRevenue[$key] = ($walletAmount[0]->total_amount > 0) ? $walletAmount[0]->total_amount : 0;
                    $planAmount = DB::select('SELECT SUM(`plan_purchase_price`) as total_amount FROM `user_plan_details` WHERE MONTH(purchased_date) = '.$value.' AND YEAR(purchased_date) BETWEEN '.$startYear.' AND '.$endYear);
                    $monthlyPlanSell[$key] = ($planAmount[0]->total_amount > 0) ? $planAmount[0]->total_amount : 0;
                }else{
                    $monthlyRevenue[$key] = 0;
                    $monthlyPlanSell[$key] = 0;
                }
            }

            $this->data['monthly_revenue'] = json_encode($monthlyRevenue);
            $this->data['monthly_plan_sell'] = json_encode($monthlyPlanSell);
            $this->data['total_revenue'] = array_sum($monthlyRevenue);

            $this->data['current_month_revenue'] = SadminWallet::whereMonth('created_at',date('m',strtotime($new_end_date)))->sum('debit_amount');
            $this->data['previous_month_revenue'] = SadminWallet::whereMonth('created_at',date('m', strtotime($new_start_date)))->sum('debit_amount');

            $this->data['current_year_revenue'] = SadminWallet::whereYear('created_at',date('Y',strtotime($new_end_date)))->sum('debit_amount');
            $this->data['previous_year_revenue'] = SadminWallet::whereYear('created_at',date('Y', strtotime($new_start_date)))->sum('debit_amount');

            if($this->data['current_month_revenue'] > $this->data['previous_month_revenue']){
                $this->data['monthly_profit'] = $this->data['current_month_revenue'] - $this->data['previous_month_revenue'];
                $this->data['monthly_loss'] = 0;
                $this->data['monthly_profit_percentage'] = ($this->data['monthly_profit'] > 0 && $this->data['previous_month_revenue'] > 0) ? round(($this->data['monthly_profit'] / $this->data['previous_month_revenue']) * 100) : 0;
                $this->data['monthly_loss_percentage'] = 0;
            }else{
                $this->data['monthly_profit'] = 0;
                $this->data['monthly_loss'] = $this->data['previous_month_revenue'] - $this->data['current_month_revenue'];
                $this->data['monthly_loss_percentage'] = ($this->data['monthly_loss'] > 0 && $this->data['current_month_revenue'] > 0) ? round(($this->data['monthly_loss'] / $this->data['current_month_revenue']) * 100) : 0;
                $this->data['monthly_profit_percentage'] = 0;
            }

            if($this->data['current_year_revenue'] > $this->data['previous_year_revenue']){
                $this->data['yearly_profit'] = $this->data['current_year_revenue'] - $this->data['previous_year_revenue'];
                $this->data['yearly_loss'] = 0;
                $this->data['yearly_profit_percentage'] = ($this->data['yearly_profit'] > 0 && $this->data['previous_year_revenue'] > 0) ? round(($this->data['yearly_profit'] / $this->data['previous_year_revenue']) * 100) : 0;
                $this->data['yearly_loss_percentage'] = 0;
            }else{
                $this->data['yearly_profit'] = 0;
                $this->data['yearly_loss'] = $this->data['previous_year_revenue'] - $this->data['current_year_revenue'];
                $this->data['yearly_loss_percentage'] = ($this->data['yearly_loss'] > 0 && $this->data['current_year_revenue'] > 0) ? round(($this->data['yearly_loss'] / $this->data['current_year_revenue']) * 100) : 0;
                $this->data['yearly_profit_percentage'] = 0;
            }

        }elseif($user->role == 2){

            $reseller = DB::select("SELECT COUNT(*) as total_user FROM `users` WHERE role = 3 and created_by = ".$user->id." and DATE_FORMAT(created_at,'%d-%m-%Y') BETWEEN DATE(".$new_start_date.") AND DATE(".$new_end_date.")");
            $users = DB::select("SELECT COUNT(*) as total_user FROM `clientusers` WHERE role = 4 and created_by = ".$user->id." and DATE_FORMAT(created_at,'%d-%m-%Y') BETWEEN DATE(".$new_start_date.") AND DATE(".$new_end_date.")");
            $this->data['total_reseller'] = $reseller[0]->total_user;
            $this->data['total_user'] = $users[0]->total_user;


            $reseller_purchased_plan = ResellerAdminPlan::where('admin_id',\Auth::user()->id)->whereBetween('created_at',[$new_start_date, $new_end_date])->count();
            $user_purchased_plan = UserPlanDetails::where('plan_purchased_by',\Auth::user()->id)->whereBetween('created_at',[$new_start_date, $new_end_date])->count();
            $this->data['total_plan_sold'] = $reseller_purchased_plan + $user_purchased_plan;

            // $this->data['wallet'] = AdminWallet::select(DB::Raw('SUM(credit_amount) AS total_credit, SUM(debit_amount) AS total_debit, (SUM(credit_amount)-SUM(debit_amount)) as wallet_amount'))->where('user_id',$user->id)->first();
            $this->data['wallet'] = $user->current_amount;
            $monthlyProfit = [];
            foreach ($month as $key => $value) {
                // $monthlyRevenue[$key] = UserPlanDetails::where('plan_purchased_by',\Auth::user()->id)->whereMonth('purchased_date',$value)->sum('plan_purchase_price');
                $walletAmount = DB::select('SELECT SUM(`plan_purchase_price`) as total_amount FROM `user_plan_details` WHERE plan_purchased_by = '.\Auth::user()->id.' AND MONTH(purchased_date) = '.$value.' AND YEAR(purchased_date) BETWEEN '.$startYear.' AND '.$endYear);
                $monthlyRevenue[$key] = ($walletAmount[0]->total_amount > 0) ? $walletAmount[0]->total_amount : 0;
                // $monthlySuperAdminPrice = UserPlanDetails::where('plan_purchased_by',\Auth::user()->id)->whereMonth('purchased_date',$value)->sum('plan_original_price');
                $walletAmount1 = DB::select('SELECT SUM(`plan_original_price`) as total_amount FROM `user_plan_details` WHERE plan_purchased_by = '.\Auth::user()->id.' AND MONTH(purchased_date) = '.$value.' AND YEAR(purchased_date) BETWEEN '.$startYear.' AND '.$endYear);
                $monthlySuperAdminPrice = $walletAmount1[0]->total_amount;
                if(isset($monthlyRevenue[$key]) && $monthlyRevenue[$key]){
                    $monthlyProfit[$key] = (($monthlyRevenue[$key] - $monthlySuperAdminPrice) > 0) ? ($monthlyRevenue[$key] - $monthlySuperAdminPrice) : 0;
                }

                // $monthlyPlanSellReseller = ResellerAdminPlan::where('admin_id',\Auth::user()->id)->whereMonth('created_at',$value)->sum('admin_plan_price');
                $planAmount = DB::select('SELECT SUM(`admin_plan_price`) as total_amount FROM `reseller_admin_plans` WHERE admin_id = '.\Auth::user()->id.' AND MONTH(created_at) = '.$value.' AND YEAR(created_at) BETWEEN '.$startYear.' AND '.$endYear);
                $monthlyPlanSellReseller = $planAmount[0]->total_amount;
                // $monthlyPlanSellUser = UserPlanDetails::where('plan_purchased_by',\Auth::user()->id)->whereMonth('purchased_date',$value)->sum('plan_purchase_price');
                $planAmount1 = DB::select('SELECT SUM(`plan_purchase_price`) as total_amount FROM `user_plan_details` WHERE plan_purchased_by = '.\Auth::user()->id.' AND MONTH(purchased_date) = '.$value.' AND YEAR(purchased_date) BETWEEN '.$startYear.' AND '.$endYear);
                $monthlyPlanSellUser = $planAmount1[0]->total_amount;
                $monthlyPlanSell[$key] = (($monthlyPlanSellReseller + $monthlyPlanSellUser) > 0) ? ($monthlyPlanSellReseller + $monthlyPlanSellUser) : 0;
            }
            $this->data['monthly_revenue'] = json_encode($monthlyRevenue);
            $this->data['monthly_profit'] = json_encode($monthlyProfit);
            $this->data['monthly_plan_sell'] = json_encode($monthlyPlanSell);
            $this->data['total_revenue'] = array_sum($monthlyRevenue);

            // $this->data['recently_purchase_plan'] = UserPlanDetails::select('admin_plans.id','admin_plans.title','admin_plans.price','user_plan_details.purchased_date as purchase_at','admin_plans.status')->leftJoin('admin_plans','user_plan_details.plan_id', '=', 'admin_plans.id')->where('user_plan_details.plan_purchased_by',$user->id)->groupBy('user_plan_details.plan_id')->orderBy('user_plan_details.id','desc')->whereBetween('user_plan_details.created_at',[$new_start_date, $new_end_date])->limit(10)->get();
            $this->data['recently_purchase_plan'] = DB::select("SELECT admin_plans.id, admin_plans.status, admin_plans.title, admin_plans.price, user_plan_details.purchased_date as purchase_at, COUNT(user_plan_details.plan_id) as total_plan_count FROM user_plan_details LEFT JOIN admin_plans ON admin_plans.id = user_plan_details.plan_id WHERE user_plan_details.plan_purchased_by = ".$user->id." AND DATE_FORMAT(user_plan_details.plan_purchased_by,'%d-%m-%Y') BETWEEN DATE(".$new_start_date.") AND DATE(".$new_end_date.") GROUP BY user_plan_details.plan_id ORDER BY user_plan_details.id DESC limit 10");
        }elseif($user->role == 3){
            $users = DB::select("SELECT COUNT(*) as total_user FROM `clientusers` WHERE role = 4 and created_by = ".$user->id." and DATE_FORMAT(created_at,'%d-%m-%Y') BETWEEN DATE(".$new_start_date.") AND DATE(".$new_end_date.")");
            $this->data['total_user'] = $users[0]->total_user;

            $this->data['total_plan_sold'] = UserPlanDetails::where('plan_purchased_by',\Auth::user()->id)->whereBetween('created_at',[$new_start_date, $new_end_date])->count();

            // $this->data['wallet'] = ResellerWallet::select(DB::Raw('SUM(credit_amount) AS total_credit, SUM(debit_amount) AS total_debit, (SUM(credit_amount)-SUM(debit_amount)) as wallet_amount'))->where('user_id',$user->id)->first();
            $this->data['wallet'] = $user->current_amount;

            foreach ($month as $key => $value) {
                // $monthlyRevenue[$key] = UserPlanDetails::where('plan_purchased_by',\Auth::user()->id)->whereMonth('purchased_date',$value)->sum('plan_purchase_price');
                $walletAmount = DB::select('SELECT SUM(`plan_purchase_price`) as total_amount FROM `user_plan_details` WHERE plan_purchased_by = '.\Auth::user()->id.' AND MONTH(purchased_date) = '.$value.' AND YEAR(purchased_date) BETWEEN '.$startYear.' AND '.$endYear);
                $monthlyRevenue[$key] = ($walletAmount[0]->total_amount > 0) ? $walletAmount[0]->total_amount : 0;
                // $monthlyAdminPrice = UserPlanDetails::where('plan_purchased_by',\Auth::user()->id)->whereMonth('purchased_date',$value)->sum('plan_original_price');
                $walletAmount1 = DB::select('SELECT SUM(`plan_original_price`) as total_amount FROM `user_plan_details` WHERE plan_purchased_by = '.\Auth::user()->id.' AND MONTH(purchased_date) = '.$value.' AND YEAR(purchased_date) BETWEEN '.$startYear.' AND '.$endYear);
                $monthlyAdminPrice = $walletAmount1[0]->total_amount;
                if(isset($monthlyRevenue[$key]) && $monthlyRevenue[$key]){
                    $monthlyProfit[$key] = $monthlyRevenue[$value] - $monthlyAdminPrice;
                }
                // $monthlyPlanSell[$key] = UserPlanDetails::where('plan_purchased_by',\Auth::user()->id)->whereMonth('purchased_date',$value)->sum('plan_purchase_price');
                $walletAmount3 = DB::select('SELECT SUM(`plan_purchase_price`) as total_amount FROM `user_plan_details` WHERE plan_purchased_by = '.\Auth::user()->id.' AND MONTH(purchased_date) = '.$value.' AND YEAR(purchased_date) BETWEEN '.$startYear.' AND '.$endYear);
                $monthlyPlanSell[$key] = ($walletAmount3[0]->total_amount) ? $walletAmount3[0]->total_amount : 0;
            }
            $this->data['monthly_revenue'] = json_encode($monthlyRevenue);
            $this->data['monthly_profit'] = isset($monthlyProfit) ? json_encode($monthlyProfit) : 0;
            $this->data['monthly_plan_sell'] = json_encode($monthlyPlanSell);
            $this->data['total_revenue'] = array_sum($monthlyRevenue);

            $this->data['recently_purchase_plan'] = DB::select("SELECT reseller_plans.id, reseller_plans.title, reseller_plans.price, user_plan_details.purchased_date as purchase_at, reseller_plans.status, COUNT(user_plan_details.plan_id) as total_plan_count FROM `user_plan_details` LEFT JOIN reseller_plans ON reseller_plans.id = user_plan_details.plan_id WHERE user_plan_details.plan_purchased_by = ".$user->id." AND DATE_FORMAT(user_plan_details.plan_purchased_by,'%d-%m-%Y') BETWEEN DATE(".$new_start_date.") AND DATE(".$new_end_date.") GROUP BY user_plan_details.plan_id ORDER BY user_plan_details.id DESC LIMIT 10");; 
        }

        $this->data['startDate'] = $startDate;
        $this->data['endDate'] = $endDate;

        return view('admin.dashboard',$this->data);
    }

    public function profile(){
        $user = Auth::user();
        $this->data['user'] = $user;
        $this->data['roles'] = Role::where('status',1)->where('id','>',1)->get();

        $edit = AppDomainContent::where('admin_id', $user->id)->first();
        $this->data['edit'] = $edit;
        
        return view('admin.profile',$this->data);
    }

    public function updateProfile(Request $request){
        
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'mobile' => 'required',
            'password' => 'required',            
        ]);

        $id = Auth::user()->id;
        $user = User::firstwhere('id',$id);

        $real_password = $request->password;
        $password = bcrypt($real_password);

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


        // code for domain content 

        $domain = AppDomainContent::where('admin_id', $user->id)->first();

        if ($domain) {            
            $domain->domain = $request->domain;
            $domain->content = $request->content;
            $domain->logo = $request->logo;
            $domain->app_name = $request->app_name;
            $domain->theme_color = $request->theme_color;            
            // Save the record (for both create or update)
            $domain->save();
        }


        if($user->save()){
            return back()->with('message','Profile updated successfully');
        }else{
            return back()->with('message','Profile not updated successfully');
        }

        
    }

}
