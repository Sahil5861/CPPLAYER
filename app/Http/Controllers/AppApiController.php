<?php

namespace App\Http\Controllers;

use App\Models\AdminPlan;
use App\Models\AdminSuperAdminPlan;
use App\Models\Channel;
use App\Models\Movie;
use App\Models\AdultMovie;

use App\Models\WebSeries;
use App\Models\WebSeriesSeason;
use App\Models\WebSeriesEpisode;
use App\Models\ContentNetwork;
use App\Models\TvChannel;
use App\Models\TvShow;
use App\Models\TvShowEpisode;
use App\Models\TvShowSeason;

use App\Models\RelChannel;
use App\Models\RelShow;
use App\Models\RelshowsEpisode;

use App\Models\SportsCategory;
use App\Models\SportsTournament;
use App\Models\TournamentSeason;
use App\Models\TournamentMatches;
use App\Models\StageshowPak;
use App\Models\Laughterhow;
use App\Models\TvChannelPak;
use App\Models\TvShowPak;
use App\Models\TvShowSeasonPak;
use App\Models\TvShowEpisodePak;







use App\Models\MovieLink;
use App\Models\Genre;
use App\Models\Slider;
use App\Models\Userauth;
use App\Models\ClientUser;
use App\Models\PackageChannel;
use App\Models\ResellerAdminPlan;
use App\Models\ResellerPlan;
use App\Models\User;
use App\Models\SadminPlan;
use App\Models\UserPlanDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Storage;
use DB;



class AppApiController extends Controller
{

    public function __construct()
    {
    	header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Api-Key, auth-key");
        header('P3P: CP="CAO PSA OUR"'); // Makes IE to support cookies
        header("Content-Type: application/json; charset=utf-8");

    }

    public function getallheaders(Illuminate\Http\Request $request)
    {
        $headers = $request->header('Authorization');
        return $headers;
    }

    public function get_user_id()
    {

        $headers = getallheaders();
        // print_r($headers); exit;
        if (isset($headers['auth-key']) && $headers['auth-key'] != '')
        {

            $auth_key = $headers['auth-key'];

            $userData = Userauth::where('auth_key', '=', $auth_key)->where('status',1)->first();

            if ($userData)
            {


                return $userData->user_id;
            }
            else
            {
                print_r(json_encode(array(
                    "status" => false,
                    "msg" => "Invalid authentication. Please login again",
                    'login' => true
                )));
                exit;
            }

        }
        else
        {
            print_r(json_encode(array(
                "status" => false,
                "msg" => 'Auth key not found'
            )));
            exit;
        }
    }

    public function get_user_pin()
    {

        $headers = getallheaders();
        // print_r($headers); exit;
        if (isset($headers['auth-key']) && $headers['auth-key'] != '')
        {

            $auth_key = $headers['auth-key'];

            $userData = Userauth::where('auth_key', '=', $auth_key)->where('status',1)->first();

            if ($userData)
            {
                $userId = $userData->user_id;
                
                $userPin = User::where('id', '=', $userId)->first()->over18_pin;
                if ($userPin) {
                    return $userPin;
                }
                else{
                    print_r(json_encode(array(
                        "status" => false,
                        "msg" => 'User Pin Not Found'
                    )));
                }                
            }
            else
            {
                print_r(json_encode(array(
                    "status" => false,
                    "msg" => "Invalid authentication. Please login again",
                    'login' => true
                )));
                exit;
            }

        }
        else
        {
            print_r(json_encode(array(
                "status" => false,
                "msg" => 'Auth key not found for User Pin'
            )));
            exit;
        }
    }

    function getBrowser(){
        $browser = array("Navigator"            => "/Navigator(.*)/i",
                         "Firefox"              => "/Firefox(.*)/i",
                         "Internet Explorer"    => "/MSIE(.*)/i",
                         "Google Chrome"        => "/chrome(.*)/i",
                         "MAXTHON"              => "/MAXTHON(.*)/i",
                         "Opera"                => "/Opera(.*)/i",
                         );

        // print_r($browser);exit;
        $this->info= array();
        foreach($browser as $key => $value){
            if(preg_match($value,  request()->userAgent())){
                $this->info = array_merge($this->info,array("Browser" => $key));
                $this->info = array_merge($this->info,array(
                  // "Version" => $this->getVersion($key, $value, $this->agent)
                  ));
                break;
            }else{
                $this->info = array_merge($this->info,array("Browser" => "UnKnown"));
                $this->info = array_merge($this->info,array("Version" => "UnKnown"));
            }
        }
        return $this->info['Browser'];
      }

    
    
    
      public function loginAccessUser($userData, $type){
        $browser = $this->getBrowser();

        $ipaddress = '';
        $ipaddress = $_SERVER['REMOTE_ADDR'];
        // if (getenv('HTTP_CLIENT_IP')) $ipaddress = getenv('HTTP_CLIENT_IP');
        // else if (getenv('HTTP_X_FORWARDED_FOR')) $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        // else if (getenv('HTTP_X_FORWARDED')) $ipaddress = getenv('HTTP_X_FORWARDED');
        // else if (getenv('HTTP_FORWARDED_FOR')) $ipaddress = getenv('HTTP_FORWARDED_FOR');
        // else if (getenv('HTTP_FORWARDED')) $ipaddress = getenv('HTTP_FORWARDED');
        // else if (getenv('REMOTE_ADDR')) $ipaddress = getenv('REMOTE_ADDR');
        // else $ipaddress = 'UNKNOWN';

        $json = file_get_contents("http://ipinfo.io/{$ipaddress}");
        // $json = file_get_contents("http://ip-api.com/json/{$ipaddress}");
        // http://ip-api.com/json/
        $details = json_decode($json);

        // return $details;
        // print_r($details); exit;
        $auth_key = md5(uniqid() . $userData->id);
        Userauth::where('user_id',$userData->id)->update(['status'=>0]);
        $userauth = new Userauth();

        $userauth->auth_key = $auth_key;
        $userauth->user_id = $userData->id;
        $userauth->ip_address = $details->ip;
        $userauth->browser = $browser;
        $userauth->city = @$details->city;
        $userauth->country = @$details->country;
        $userauth->postal = @$details->postal;

        if ($type == 'tv') {
            $userauth->login_pin = $userData->login_pin;
            $userauth->mac_address = $userData->mac_address;
        }
        elseif ($type == 'app') {
            $userauth->login_pin = $userData->login_pin_app;
            $userauth->mac_address = $userData->mac_address_app;
        }

        $userauth->type = $type;

        $userauth->save();


        print_r(json_encode(array(
            "status" => true,
            "msg" => "Login Successfully",
            "result_auth_key" => $auth_key,
            'data' => $userData,
            'imageBaseUrl' => 'https://cnwprojects.com/'
        )));
        exit;

    }

    function login_pin(Request $req) {

        $post = json_decode(file_get_contents('php://input', 'r'));

        if (isset($post->login_pin) && $post->login_pin != '' && ((isset($post->mac_address) && $post->mac_address != '') || (isset($post->device_id) && $post->device_id != '')))
        {

            $login_pin = $post->login_pin;
            $mac_address = isset($post->mac_address) ? $post->mac_address : $post->device_id;

            // $password = md5($post->password);
            
            $data_user = ClientUser::where('login_pin','=',$login_pin)->first();            
            $created_by = $data_user ? $data_user->created_by : 0;  // id
            $creater = User::where('id',$created_by)->first();

            if (!$data_user || $data_user == null) {
                print_r(json_encode([
                    'status' => false,
                    'msg' => 'Invalid Login Pin'
                ]));
                exit;
            }


            if ($data_user)
            {
                $plans = UserPlanDetails::where(['user_id'=>$data_user->id,'status'=>1])->whereDate('plan_end_date','>=',date('Y-m-d'))->orderBy('id','desc')->get();

                if(count($plans) == 0){

                    $last_plan = UserPlanDetails::where(['user_id'=>$data_user->id])->orderBy('id','desc')->first();
                    $plan_id = $last_plan ? $last_plan->plan_id : 0;

                    $current_user_amount = $data_user->current_amount ?? 0;

                    $plan_details = null;

                    if ($creater->role == 2) {
                        $planDetails = AdminPlan::find($plan_id);
                    }
                    else if ($creater->role == 3) {
                        $planDetails = ResellerPlan::find($plan_id);
                    }

                    $price = $planDetails->total_price;

                    if ($current_user_amount >= $price && $plan_details) {
                        $plan = new UserPlanDetails();
                        $plan->user_id = $data_user->id;
                        $plan->plan_original_price = $price; // super admin price
                        $plan->plan_validity = $planDetails->plan_validity;
                        $plan->role = ($creater->role == 2 ? 'admin': ($creater->role == 3 ? 'reseller': 'netadmin'));

                        $plan->plan_purchase_price = $price;
                        $plan->plan_purchased_by = $created_by;
                        $plan_end_date=Date('Y-m-d H:i:s', strtotime('+'.$planDetails->plan_validity.' days'));
                        $plan->plan_end_date = $plan_end_date;
                        $plan->status = 1;
                        $plan->save();
                    }
                    else{
                        print_r(json_encode(array(
                            'status' => false,
                            'msg' => 'You have not active plan. Kindly recharge your account.s'
                        )));
                        exit;
                    }

                }

                if($data_user->mac_address == '' || $data_user->mac_address == null){
                    $data_user->fcm_token = $post->token;
                    $data_user->mac_address = $mac_address;
                    $data_user->save();
                    if($data_user->status=='2'){
                        print_r(json_encode(array(
                            "status" => false,
                            "msg" => "Your account is deactivated.",
                            'otp' => false
                        )));
                        exit;
                    }elseif($data_user->status=='3'){
                        print_r(json_encode(array(
                            "status" => false,
                            "msg" => "Your account blocked by admin.",
                            'otp' => false
                        )));
                        exit;
                    }else{
                        $this->loginAccessUser($data_user, 'tv');
                    }
                }else if($data_user->mac_address == $mac_address){
                    if($data_user->status=='2'){
                        print_r(json_encode(array(
                            "status" => false,
                            "msg" => "Your account is deactivated.",
                            'otp' => false
                        )));
                        exit;
                    }elseif($data_user->status=='3'){
                        print_r(json_encode(array(
                            "status" => false,
                            "msg" => "Your account blocked by admin.",
                            'otp' => false
                        )));
                        exit;
                    }else{
                        $this->loginAccessUser($data_user, 'tv');
                    }
                }else{
                    print_r(json_encode(array(
                        "status" => false,
                        "msg" => "Mac address not matched.",
                    )));
                    exit;
                }
            }else{
                print_r(json_encode(array(
                    "status" => false,
                    "msg" => "You entered invalid pin."
                )));
                exit;
            }

        }
        else
        {
            print_r(json_encode(array(
                "status" => false,
                "msg" => "Please enter login pin."
            )));
            exit;
        }
    }

    function login_pin_app(Request $req) {

        $post = json_decode(file_get_contents('php://input', 'r'));

        if (isset($post->login_pin_app) && $post->login_pin_app != '' && ((isset($post->mac_address_app) && $post->mac_address_app != '') || (isset($post->device_id) && $post->device_id != '')))
        {

            $login_pin_app = $post->login_pin_app;
            $mac_address_app = isset($post->mac_address_app) ? $post->mac_address_app : $post->device_id;
            // $password = md5($post->password);

            $data_user = ClientUser::where('login_pin_app','=',$login_pin_app)->first();

            if ($data_user)
            {
                $plans = UserPlanDetails::where(['user_id'=>$data_user->id,'status'=>1])->whereDate('plan_end_date','>=',date('Y-m-d'))->orderBy('id','desc')->get();

                if(count($plans) == 0){
                    print_r(json_encode(array(
                        'status' => false,
                        'msg' => 'You have not active plan. Kindly recharge your account.'
                    )));
                    exit;
                }
                if($data_user->mac_address_app == '' || $data_user->mac_address_app == null){
                    $data_user->fcm_token = $post->token;
                    $data_user->mac_address_app = $mac_address_app;
                    $data_user->save();
                    if($data_user->status=='2'){
                        print_r(json_encode(array(
                            "status" => false,
                            "msg" => "Your account is deactivated.",
                            'otp' => false
                        )));
                        exit;
                    }elseif($data_user->status=='3'){
                        print_r(json_encode(array(
                            "status" => false,
                            "msg" => "Your account blocked by admin.",
                            'otp' => false
                        )));
                        exit;
                    }else{
                        $this->loginAccessUser($data_user, 'app');
                    }
                }else if($data_user->mac_address_app == $mac_address_app){
                    if($data_user->status=='2'){
                        print_r(json_encode(array(
                            "status" => false,
                            "msg" => "Your account is deactivated.",
                            'otp' => false
                        )));
                        exit;
                    }elseif($data_user->status=='3'){
                        print_r(json_encode(array(
                            "status" => false,
                            "msg" => "Your account blocked by admin.",
                            'otp' => false
                        )));
                        exit;
                    }else{
                        $this->loginAccessUser($data_user, 'app');
                    }
                }else{
                    print_r(json_encode(array(
                        "status" => false,
                        "msg" => "Mac address not matched.",
                    )));
                    exit;
                }
            }else{
                print_r(json_encode(array(
                    "status" => false,
                    "msg" => "You entered invalid pin."
                )));
                exit;
            }

        }
        else
        {
            print_r(json_encode(array(
                "status" => false,
                "msg" => "Please enter login pin."
            )));
            exit;
        }
    }

    public function getActivePlan(){
        $user_id = $this->get_user_id();
        $plans = UserPlanDetails::where(['user_id'=>$user_id,'status'=>1])->whereDate('plan_end_date','>=',date('Y-m-d'))->orderBy('id','desc')->get();
        // $activePlan = [];
        if($plans){
            foreach ($plans as $key => $plan) {
                
                $admin = User::where('id',$plan->plan_purchased_by)->first();
                if($plan->plan_id > 9999){
                    if($admin->role == 2){
                        $activePlan = AdminPlan::where('id',$plan->plan_id)->where('status',1)->first();
                    }else{
                        $activePlan = ResellerPlan::where('id',$plan->plan_id)->where('status',1)->first();
                    }
                }else{
                    $activePlan = SadminPlan::where('id',$plan->plan_id)->where('status',1)->first();
                }
                $plan->planDetails = $activePlan;
            }
            
            
            if(count($plans) > 0){
                print_r(json_encode(array(
                    'status' => true,
                    'message' => 'Active Plan',
                    'data' => $plans
                )));
                exit;
            }else{
                print_r(json_encode(array(
                    'status' => false,
                    'message' => 'Plan not found.'
                )));
                exit;
            }
        }else{
            print_r(json_encode(array(
                'status' => false,
                'message' => 'No active plan found.'
            )));
            exit;
        }
    }
    
    public function checkPlan(){
        $user_id = $this->get_user_id();
        $plan = UserPlanDetails::where(['user_id'=>$user_id,'status'=>1])->whereDate('plan_end_date','>=',date('Y-m-d'))->orderBy('id','desc')->first();
        if(isset($plan->id)){
            print_r(json_encode(array(
                'status' => true,
                'plan'=> $plan,
                'message' => 'Have an active plan.'
            )));
            exit;
        }else{
            Userauth::where('user_id', $user_id)->update(['status'=> 0]);
            UserPlanDetails::where('user_id', $user_id)->update(['status'=> 0]);
            print_r(json_encode(array(
                'status' => false,
                'message' => 'No active plan found.'
            )));
            exit;
        }

    }

    public function getChannels(Request $request)
    {

        $user_id = $this->get_user_id();
        // $channel = Channel::where('status',1)->whereNull('deleted_at')->get();

        $plans = UserPlanDetails::where(['user_id'=>$user_id,'status'=>1])->whereDate('plan_end_date','>=',date('Y-m-d'))->orderBy('id','desc')->get();
        $channels = [];
        if($plans){
            foreach ($plans as $key => $plan) {
                
                $admin = User::where('id',$plan->plan_purchased_by)->first();
                if($admin->role == 2){
                    $superAdminPlan = AdminSuperAdminPlan::where('admin_plan_id',$plan->plan_id)->where('status',1)->get();
                    foreach ($superAdminPlan as $key => $value) {
                        $channels[] = PackageChannel::select('channels.*')->leftJoin('channels','channels.id','=','package_channels.channel_id')->where('package_channels.plan_id',$value->super_admin_plan_id)->whereNull('channels.deleted_at')->where('channels.status', 1)->orderBy('channels.channel_number','asc')->get();
                    }
                }else if($admin->role == 3){
                    $superAdminPlan = ResellerAdminPlan::select('admin_super_admin_plans.*')->leftJoin('admin_super_admin_plans','admin_super_admin_plans.admin_plan_id','=','reseller_admin_plans.admin_plan_id')->where('reseller_admin_plans.reseller_plan_id',$plan->plan_id)->get();
                    foreach ($superAdminPlan as $key => $value) {
                        $channels[] = PackageChannel::select('channels.*')->leftJoin('channels','channels.id','=','package_channels.channel_id')->where('package_channels.plan_id',$value->super_admin_plan_id)->whereNull('channels.deleted_at')->where('channels.status', 1)->orderBy('channels.channel_number','asc')->get();
                    }
                }else if($admin->role == 6){
                    $channels[] = DB::select("SELECT c.id, c.channel_number, c.channel_name, c.channel_logo,c.channel_bg,c.channel_language,c.channel_index,c.position_locked,c.status,c.channel_description,c.view_count,c.created_at, nc.link as channel_link FROM netadmin_channels nc LEFT JOIN channels c ON c.id = nc.channel_id WHERE nc.plan_id = ".$plan->plan_id." AND nc.link<>'' AND nc.status =1 AND c.deleted_at IS NULL ORDER BY c.channel_number ASC");
                }
            }
            $allChannels = [];
            foreach ($channels as $key => $chan) {
                // code...
                foreach ($chan as $key => $ch) {
                    // code...
                    $allChannels[] = $ch;
                }
            }
            if($allChannels){
                print_r(json_encode(array(
                    'status' => true,
                    'message' => 'All Channel',
                    'data' => $allChannels
                )));
                exit;
            }else{
                print_r(json_encode(array(
                    'status' => false,
                    'message' => 'Channel not found.'
                )));
                exit;
            }
        }else{
            print_r(json_encode(array(
                'status' => false,
                'message' => 'No active plan found.'
            )));
            exit;
        }

        // print_r(json_encode(array(
        //     'status' => true,
        //     'message' => 'All channels.',
        //     'data' =>$channel
        // )));
        // exit;
    }

    public function getFeaturedLiveTV(Request $request)
    {

        // $user_id = $this->get_user_id();
        // $channel = Channel::where('status',1)->orderBy('channel_number','asc')->whereNull('deleted_at')->get();
        // print_r(json_encode(array(
        //     'status' => true,
        //     'message' => 'All channels.',
        //     'data' =>$channel
        // )));
        // exit;

        $user_id = $this->get_user_id();
        $channels = Channel::select('id',
                'channel_number',
                'channel_name as name',
                'channel_description  as description',
                'channel_name as name',
                'channel_logo as banner',
                'channel_link as url',
                'stream_type',
                'genres',
                'status')->where('status',1)->whereNull('deleted_at')->orderBy('channel_number','asc');
                
        if(isset($_GET['records']) && $_GET['records'] > 0){
            $channels = $channels->limit($_GET['records'])->get();
        }else{
            $channels = $channels->get();
        }
                
        $groupedByGenres = [];

        // Loop through each channel
        foreach ($channels as $channel) {
            $genreList = explode(',', $channel->genres); // Split the genres string

            foreach ($genreList as $genre) {
                $genre = trim($genre); // Trim spaces
                if (!isset($groupedByGenres[$genre])) {
                    $groupedByGenres[$genre] = [];
                }
                $groupedByGenres[$genre][] = $channel;
            }
        }

        // $plans = UserPlanDetails::where(['user_id'=>$user_id,'status'=>1])->whereDate('plan_end_date','>=',date('Y-m-d'))->orderBy('id','desc')->get();
        // $channels = [];
        // if($plans){
        //     foreach ($plans as $key => $plan) {
                
        //         $admin = User::where('id',$plan->plan_purchased_by)->first();
        //         if($admin->role == 2){
        //             $superAdminPlan = AdminSuperAdminPlan::where('admin_plan_id',$plan->plan_id)->where('status',1)->get();
        //             foreach ($superAdminPlan as $key => $value) {
        //                 $channels[] = PackageChannel::select('id',
        //         'channel_number',
        //         'channel_name as name',
        //         'channel_description  as description',
        //         'channel_name as name',
        //         'channel_logo as banner',
        //         'channel_link as url',
        //         'status')->leftJoin('channels','channels.id','=','package_channels.channel_id')->where('package_channels.plan_id',$value->super_admin_plan_id)->whereNull('channels.deleted_at')->orderBy('channels.channel_number','asc')->get();
        //             }
        //         }else if($admin->role == 3){
        //             $superAdminPlan = ResellerAdminPlan::select('admin_super_admin_plans.*')->leftJoin('admin_super_admin_plans','admin_super_admin_plans.admin_plan_id','=','reseller_admin_plans.admin_plan_id')->where('reseller_admin_plans.reseller_plan_id',$plan->plan_id)->get();
        //             foreach ($superAdminPlan as $key => $value) {
        //                 $channels[] = PackageChannel::select('id',
        //         'channel_number',
        //         'channel_name as name',
        //         'channel_description  as description',
        //         'channel_name as name',
        //         'channel_logo as banner',
        //         'channel_link as url',
        //         'status')->leftJoin('channels','channels.id','=','package_channels.channel_id')->where('package_channels.plan_id',$value->super_admin_plan_id)->whereNull('channels.deleted_at')->orderBy('channels.channel_number','asc')->get();
        //             }
        //         }else if($admin->role == 6){
        //             $channels[] = DB::select("SELECT c.id, c.channel_number, c.channel_name  as name, c.channel_logo as banner ,c.channel_bg,c.channel_language,c.channel_index,c.position_locked,c.channel_description as description,c.view_count,c.created_at, nc.link as channel_link as url FROM netadmin_channels nc LEFT JOIN channels c ON c.id = nc.channel_id WHERE nc.plan_id = ".$plan->plan_id." AND nc.link<>'' AND nc.status =1 AND c.deleted_at IS NULL ORDER BY c.channel_number ASC");
        //         }
        //     }
        //     $allChannels = [];
        //     foreach ($channels as $key => $chan) {
        //         // code...
        //         foreach ($chan as $key => $ch) {
        //             // code...
        //             $allChannels[] = $ch;
        //         }
        //     }
        //     if($allChannels){
        //         print_r(json_encode(array(
        //             'status' => true,
        //             'message' => 'All Channel',
        //             'data' => $allChannels
        //         )));
        //         exit;
        //     }else{
        //         print_r(json_encode(array(
        //             'status' => false,
        //             'message' => 'Channel not found.'
        //         )));
        //         exit;
        //     }
        // }else{
        //     print_r(json_encode(array(
        //         'status' => false,
        //         'message' => 'No active plan found.'
        //     )));
        //     exit;
        // }
        print_r(json_encode($groupedByGenres));
        // print_r(json_encode(array(
        //     'status' => true,
        //     'message' => 'All channels.',
        //     'data' =>$channel
        // )));
        exit;
    }

    public function getSlider(Request $request){
        $user_id = $this->get_user_id();
        $slider = Slider::where('status',1)->whereNull('deleted_at')->get();
        // print_r(json_encode(array(
        //     'status' => true,
        //     'message' => 'All sliders.',
        //     'data' =>$slider
        // )));
        print_r(json_encode($slider));
        exit;
    }

    public function getCustomImageSlider(Request $request){
        $user_id = $this->get_user_id();
        $slider = Slider::where('status',1)->whereNull('deleted_at')->get();
        // print_r(json_encode(array(
        //     'status' => true,
        //     'message' => 'All sliders.',
        //     'data' =>$slider
        // )));
        print_r(json_encode($slider));
        exit;
    }


    public function pages(){
        $pages = \DB::table('pages')->where('id',1)->first();
        if($pages){
            print_r(json_encode(array(
                'status' => true,
                'pages' => $pages
            )));
            exit;
        }else{
            print_r(json_encode(array(
                'status' => false,
                'msg' =>  'something went wrong.'
            )));
            exit;
        }
    }

    public function getChannelsWithGenre(Request $request){

        $user_id = $this->get_user_id();
        $plans = UserPlanDetails::where(['user_id'=>$user_id,'status'=>1])->whereDate('plan_end_date','>=',date('Y-m-d'))->orderBy('id','desc')->get();
        // print_r($plans); exit();
        $channel = [];
        $channels = [];
        if(count($plans) > 0){
            $channelIds = [];
            foreach ($plans as $key => $plan) {
                $admin = User::where('id',$plan->plan_purchased_by)->first();
                if($plan->plan_id > 9999){
               
                    if($admin->role == 2){
                        $superAdminPlan = AdminSuperAdminPlan::where('admin_plan_id',$plan->plan_id)->where('status',1)->get();
                        foreach ($superAdminPlan as $key => $value) {
                            $channels[] = PackageChannel::select('channels.*')->leftJoin('channels','channels.id','=','package_channels.channel_id')->where('package_channels.plan_id',$value->super_admin_plan_id)->whereNull('channels.deleted_at')->orderBy('channels.channel_number','asc')->get();

                        }
                        
                        foreach ($channels as $key1 => $_channel) {
                            foreach ($_channel as $key => $ch) {
                                // code...
                                $channelIds[] = $ch->id;
                            }
                            
                        }
                        // print_r(json_encode($channelIds));
                        
                    }else{
                        $superAdminPlan = ResellerAdminPlan::select('admin_super_admin_plans.*')->leftJoin('admin_super_admin_plans','admin_super_admin_plans.admin_plan_id','=','reseller_admin_plans.admin_plan_id')->where('reseller_admin_plans.reseller_plan_id',$plan->plan_id)->get();
                        foreach ($superAdminPlan as $key => $value) {
                            $channels[] = PackageChannel::select('channels.*')->leftJoin('channels','channels.id','=','package_channels.channel_id')->where('package_channels.plan_id',$value->super_admin_plan_id)->whereNull('channels.deleted_at')->orderBy('channels.channel_number','asc')->get();
                        }
                        
                        foreach ($channels as $key1 => $_channel) {
                            foreach ($_channel as $key => $ch) {
                                // code...
                                $channelIds[] = $ch->id;
                            }
                        }
                        // print_r(json_encode($channelIds));
                        
                    }
                }else{
                    if($admin->role == 6){
                        $netadmin = $admin->role;
                        $channels[] = DB::select("SELECT c.id, c.channel_number, c.channel_name, c.channel_logo,c.channel_bg,c.channel_language,c.channel_index,c.position_locked,c.channel_description,c.view_count,c.created_at, nc.link as channel_link FROM netadmin_channels nc LEFT JOIN channels c ON c.id = nc.channel_id WHERE nc.plan_id = ".$plan->plan_id." AND nc.link<>'' AND nc.status =1 AND c.deleted_at IS NULL ORDER BY c.channel_number ASC");
                    }else{
                        $channels[] = PackageChannel::select('channels.*')->leftJoin('channels','channels.id','=','package_channels.channel_id')->where('package_channels.plan_id',$plan->plan_id)->whereNull('channels.deleted_at')->orderBy('channels.channel_number','asc')->get();
                    }

                    
                    
                    foreach ($channels as $key1 => $_channel) {
                        foreach ($_channel as $key => $ch) {
                            // code...
                            $channelIds[] = $ch->id;
                        }
                        
                    }
                }
            }

            // if(isset($netadmin)){
            //     $genre = Genre::with(['netadminchannels' => function($query) use ($channelIds)
            //     {
            //         $query->whereIn('channels.id', $channelIds);

            //     }])->where('status',1)->get();
            //     print_r(json_encode($genre)); exit;    
            // }

            // $genre = Genre::with(['channels' => function($query) use ($channelIds)
            // {
            //     $query->whereIn('channels.id', $channelIds);

            // }])->where('status',1)->get();

            // foreach ($genre as $key => $gen) {
            //     if(count($gen->channels) > 0){
            //         $channel[] = $gen;
            //     }
            // }


            if(isset($netadmin)){
                
                $genre = Genre::with(['channels' => function($query) use ($channelIds)
                {
                    $query->whereIn('channels.id', $channelIds);
    
                }])->where('status',1)->orderBy('index','asc')->get();

                foreach ($genre as $key => $gen) {
                    if(count($gen->channels) > 0){
                        foreach ($gen->channels as $key => $___channel) {
                            $d = DB::table('netadmin_channels')->where(['channel_id'=>$___channel->id,'user_id'=>$plan->plan_purchased_by])->first();
                            if($d){
                                $___channel->channel_link = $d->link;
                            }
                        }

                        $channel[] = $gen;
                    }
                }

                
                // print_r(json_encode($genre)); exit;    
            }else{

                $genre = Genre::with(['channels' => function($query) use ($channelIds)
                {
                    $query->whereIn('channels.id', $channelIds);
    
                }])->where('status',1)->orderBy('index','asc')->get();

                foreach ($genre as $key => $gen) {
                    if(count($gen->channels) > 0){
                        $channel[] = $gen;
                    }
                }
            } 
            $plans = UserPlanDetails::select(\DB::raw('UNIX_TIMESTAMP(plan_end_date) as plan_end_date'),'plan_id')->where(['user_id'=>$user_id,'status'=>1])->whereDate('plan_end_date','>=',date('Y-m-d'))->orderBy('id','desc')->get();
            
            print_r(json_encode(array(
                'status' => true,
                'message' => 'All channels with genre.',
                'data' => $channel,
                'plans'=>$plans
            )));
            exit;
        
        }else{
            print_r(json_encode(array(
                'status' => false,
                'message' => 'No active plan found.'
            )));
            exit;
        }
    }

    public function getChannelsWithGenreNew(Request $request){
        $user_id = $this->get_user_id();
        $plan = UserPlanDetails::where(['user_id'=>$user_id,'status'=>1])->whereDate('plan_end_date','>=',date('Y-m-d'))->orderBy('id','desc')->first();
        // print_r($plan); exit();
        $channel = [];
        $channels = [];
        if($plan){
            $admin = User::where('id',$plan->plan_purchased_by)->first();

            if($admin->role == 2){
                $superAdminPlan = AdminSuperAdminPlan::where('admin_plan_id',$plan->plan_id)->where('status',1)->get();
                foreach ($superAdminPlan as $key => $value) {
                    $channels[] = PackageChannel::select('channels.*')->leftJoin('channels','channels.id','=','package_channels.channel_id')->where('package_channels.plan_id',$value->super_admin_plan_id)->whereNull('channels.deleted_at')->orderBy('channels.channel_number','asc')->get();

                }
                $channelIds = [];
                foreach ($channels as $key1 => $_channel) {
                     foreach ($_channel as $key => $ch) {
                        // code...
                        $channelIds[] = $ch->id;
                    }
                }
                // print_r(json_encode($channelIds));
                $genre = Genre::with(['channels' => function($query) use ($channelIds)
                    {
                        $query->whereIn('channels.id', $channelIds);

                    }])->where('status',1)->get();
                
                foreach ($genre as $key => $gen) {
                    if(count($gen->channels) > 0){
                        $channel[] = $gen;
                    }
                    // code...
                }
            }else{
                $superAdminPlan = ResellerAdminPlan::select('admin_super_admin_plans.*')->leftJoin('admin_super_admin_plans','admin_super_admin_plans.admin_plan_id','=','reseller_admin_plans.admin_plan_id')->where('reseller_admin_plans.reseller_plan_id',$plan->plan_id)->get();
                foreach ($superAdminPlan as $key => $value) {
                    $channels[] = PackageChannel::select('channels.*')->leftJoin('channels','channels.id','=','package_channels.channel_id')->where('package_channels.plan_id',$value->super_admin_plan_id)->whereNull('channels.deleted_at')->orderBy('channels.channel_number','asc')->get();
                }
                $channelIds = [];
                foreach ($channels as $key1 => $_channel) {
                     foreach ($_channel as $key => $ch) {
                        // code...
                        $channelIds[] = $ch->id;
                    }
                }
                // print_r(json_encode($channelIds));
                $genre = Genre::with(['channels' => function($query) use ($channelIds)
                    {
                        $query->whereIn('channels.id', $channelIds);

                    }])->where('status',1)->get();
                
                foreach ($genre as $key => $gen) {
                    if(count($gen->channels) > 0){
                        $channel[] = $gen;
                    }
                    // code...
                }
            }
            
            $plans = UserPlanDetails::select(\DB::raw('UNIX_TIMESTAMP(plan_end_date) as plan_end_date'),'plan_id')->where(['user_id'=>$user_id,'status'=>1])->whereDate('plan_end_date','>=',date('Y-m-d'))->orderBy('id','desc')->get();
            
            print_r(json_encode(array(
                'status' => true,
                'message' => 'All channels with genre.',
                'data' => $channel,
                'plans'=>$plans
            )));
            exit;
        
        }else{
            print_r(json_encode(array(
                'status' => false,
                'message' => 'No active plan found.'
            )));
            exit;
        }
    }

    public function getChannelsWithGenrePopular(Request $request){
        // code...
        $user_id = $this->get_user_id();
        $plans = UserPlanDetails::where(['user_id'=>$user_id,'status'=>1])->whereDate('plan_end_date','>=',date('Y-m-d'))->orderBy('id','desc')->get();
        // print_r($plan); exit();
        $channel = [];
        $channels = [];
        if(count($plans) > 0){
            $channelIds = [];
            foreach ($plans as $key => $plan) {
                if($plan->plan_id > 9999){

                    $admin = User::where('id',$plan->plan_purchased_by)->first();

                    if($admin->role == 2){
                        $superAdminPlan = AdminSuperAdminPlan::where('admin_plan_id',$plan->plan_id)->where('status',1)->get();
                        foreach ($superAdminPlan as $key => $value) {
                            $channels[] = PackageChannel::select('channels.*')->leftJoin('channels','channels.id','=','package_channels.channel_id')->where('package_channels.plan_id',$value->super_admin_plan_id)->whereNull('channels.deleted_at')->orderBy('channels.channel_number','asc')->get();

                        }
                        
                        foreach ($channels as $key1 => $_channel) {
                            foreach ($_channel as $key => $ch) {
                                // code...
                                $channelIds[] = $ch->id;
                            }
                            
                        }
                        // print_r(json_encode($channelIds));
                        
                    }else{
                        $superAdminPlan = ResellerAdminPlan::select('admin_super_admin_plans.*')->leftJoin('admin_super_admin_plans','admin_super_admin_plans.admin_plan_id','=','reseller_admin_plans.admin_plan_id')->where('reseller_admin_plans.reseller_plan_id',$plan->plan_id)->get();
                        foreach ($superAdminPlan as $key => $value) {
                            $channels[] = PackageChannel::select('channels.*')->leftJoin('channels','channels.id','=','package_channels.channel_id')->where('package_channels.plan_id',$value->super_admin_plan_id)->whereNull('channels.deleted_at')->orderBy('channels.channel_number','asc')->get();
                        }
                        
                        foreach ($channels as $key1 => $_channel) {
                            foreach ($_channel as $key => $ch) {
                                // code...
                                $channelIds[] = $ch->id;
                            }
                        }
                        // print_r(json_encode($channelIds));
                        
                    }
                }else{
                        // $channels[] = PackageChannel::select('channels.*')->leftJoin('channels','channels.id','=','package_channels.channel_id')->where('package_channels.plan_id',$plan->plan_id)->whereNull('channels.deleted_at')->orderBy('channels.channel_number','asc')->get();
                    if($admin->role == 6){
                        $netadmin = $admin->role;
                        $channels[] = DB::select("SELECT c.id, c.channel_number, c.channel_name, c.channel_logo,c.channel_bg,c.channel_language,c.channel_index,c.position_locked,c.channel_description,c.view_count,c.created_at, nc.link as channel_link FROM netadmin_channels nc LEFT JOIN channels c ON c.id = nc.channel_id WHERE nc.plan_id = ".$plan->plan_id." AND nc.link<>'' AND nc.status =1 AND c.deleted_at IS NULL ORDER BY c.channel_number ASC");
                    }else{
                        $channels[] = PackageChannel::select('channels.*')->leftJoin('channels','channels.id','=','package_channels.channel_id')->where('package_channels.plan_id',$plan->plan_id)->whereNull('channels.deleted_at')->orderBy('channels.channel_number','asc')->get();
                    }
                    
                    
                    foreach ($channels as $key1 => $_channel) {
                        foreach ($_channel as $key => $ch) {
                            // code...
                            $channelIds[] = $ch->id;
                        }
                        
                    }
                }
            }

            $genre = Genre::with(['channelspopular' => function($query) use ($channelIds)
            {
                $query->whereIn('channels.id', $channelIds);

            }])->where('status',1)->orderBy('index','asc')->get();
        
            foreach ($genre as $key => $gen) {
                if(count($gen->channels) > 0){
                    $channel[] = $gen;
                }
                // code...
            }

            
            $plans = UserPlanDetails::select(\DB::raw('UNIX_TIMESTAMP(plan_end_date) as plan_end_date'),'plan_id')->where(['user_id'=>$user_id,'status'=>1])->whereDate('plan_end_date','>=',date('Y-m-d'))->orderBy('id','desc')->get();
            
            print_r(json_encode(array(
                'status' => true,
                'message' => 'All channels with genre.',
                'data' => $channel,
                'plans'=>$plans
            )));
            exit;
        
        }else{
            print_r(json_encode(array(
                'status' => false,
                'message' => 'No active plan found.'
            )));
            exit;
        }
    }



    public function getGenreChannels(Request $request){
        // code...
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));
        $channel = Genre::where('status',1)->where('id',$post->genre_id)->with('channels')->orderBy('index','asc')->get();
        print_r(json_encode(array(
            'status' => true,
            'message' => 'All channels with genre.',
            'data' =>$channel
        )));
        exit;
    }

    public function uploadProfile(){
        $post = json_decode(file_get_contents('php://input', 'r'));
        $user_id = $this->get_user_id();
        if(isset($post->image) && $post->image!=''){
            $user = ClientUser::where('id',$user_id)->first();
            // $imageName = time().'.jpg';
            $folderName = '/images/';
            $image_parts = explode(";base64,", $post->image);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);

            $imageName = uniqid() . time().'.'.$image_type;
            $destinationPath = $folderName.$imageName;
            $success = file_put_contents(public_path().$destinationPath, $image_base64);
            // $success = Storage::disk('s3')->putFileAs('images/' . $imageName, public_path().$destinationPath, ''); // old : $file
            // @unlink(public_path().$destinationPath);
            $user->profile_pic = $destinationPath;
            if($user->save()){
                print_r(json_encode(array(
                    'status' => true,
                    'path' => $destinationPath,
                    'message' => 'profile pic uploaded successfully.'
                )));
                exit;
            }else{
                print_r(json_encode(array(
                    'status' => false,
                    'message' => 'Something went wrong.'
                )));
                exit;
            }
        }else{
            print_r(json_encode(array(
                'status' => false,
                'message' => 'All field are required.'
            )));
            exit;
        }
    }

    public function updateProfile(){
        $post = json_decode(file_get_contents('php://input', 'r'));
        $user_id = $this->get_user_id();
        if(isset($post->name) && $post->name!='' && isset($post->email) && $post->email!='' && isset($post->mobile) && $post->mobile!=''){
            $user = ClientUser::where('id',$user_id)->first();

            $user->email = $post->email;
            $user->mobile = $post->mobile;
            $user->name = $post->name;
            if($user->save()){
                print_r(json_encode(array(
                    'status' => true,

                    'message' => 'profile uploaded successfully.'
                )));
                exit;
            }else{
                print_r(json_encode(array(
                    'status' => false,
                    'message' => 'Something went wrong.'
                )));
                exit;
            }
        }else{
            print_r(json_encode(array(
                'status' => false,
                'message' => 'All field are required.'
            )));
            exit;
        }
    }


    // new 11 june

    public function getAllMovies(Request $request){
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));
        $query = Movie::where('status', 1)->whereNull('deleted_at');

        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            // echo 'page set'; exit;
            $page = (int) $_GET['page'];
            $limit = isset($_GET['records']) && is_numeric($_GET['records']) ? (int) $_GET['records'] : 10;
    
            $movies = $query->paginate($limit, ['*'], 'page', $page);

            print_r(json_encode($movies->items()));
            // print_r(json_encode($movies));
        } else {

            if (isset($_GET['records']) && $_GET['records'] > 0) {
                $movies = $query->limit($_GET['records'])->get();
            } else {
                $movies = $query->get();
            }
            print_r(json_encode($movies));
            exit;
        }

    }

    public function getAllWebSeries(Request $request){
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $query = WebSeries::where('status', 1)->where('deleted_at', null)->with('networks');

        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $page = (int) $_GET['page'];
            $limit = isset($_GET['records']) && is_numeric($_GET['records']) ? (int) $_GET['records'] : 10;

            $sereis = $query->paginate($limit, ['*'], 'page', $page);

            print_r(json_encode($sereis->items()));
        }
        else{

            if(isset($_GET['records']) && $_GET['records'] > 0){
                $series = $query->limit($_GET['records'])->get();
            }else{
                $series = $query->get();
            }
    
            print_r(json_encode($series));
            exit;
        }
    }

    public function getSeasons(Request $request, $id){
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $seasons = WebSeriesSeason::where('web_series_id', $id)->whereNull('deleted_at');

        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $page = (int) $_GET['page'];
            $limit = isset($_GET['records']) && is_numeric($_GET['records']) ? (int) $_GET['records'] : 10;

            $seasons = $seasons->paginate($limit, ['*'], 'page', $page);

            if (!$seasons->items()) {
                print_r(json_encode([]));
                exit;
            }

            print_r(json_encode($seasons->items()));
        }
        else{
            if(isset($_GET['records']) && $_GET['records'] > 0){
                $seasons = $seasons->limit($_GET['records'])->get();
            }
            else{
                $seasons = $seasons->get();
            }
            if (!$seasons) {
                print_r(json_encode([]));
                exit;
            }
            print_r(json_encode($seasons));            
        }        

        exit;
    }

    public function getEpisodes(Request $request, $id, $type=0){
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $episodes = WebSeriesEpisode::where('season_id', $id)->whereNull('deleted_at');

        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $page = (int) $_GET['page'];
            $limit = isset($_GET['records']) && is_numeric($_GET['records']) ? (int) $_GET['records'] : 10;

            $episodes = $episodes->paginate($limit, ['*'], 'page', $page);
            // echo 'Page Set'; exit;

            if (!$episodes->items()) {
                print_r(json_encode([]));
                exit;
            }

            print_r(json_encode($episodes->items()));
        }
        else{
            if(isset($_GET['records']) && $_GET['records'] > 0){
                $episodes = $episodes->limit($_GET['records'])->get();
            }
            else{
                $episodes = $episodes->get();
            }

            if (!$episodes) {
                print_r(json_encode([]));
                exit;
            }
            print_r(json_encode($episodes));            
        }        
    }

    public function getWebSeriesDetails(Request $request, $webseries_id){
        // $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $webseries = WebSeries::where('id', $webseries_id)->where('status', 1)->where('deleted_at', null)->with('networks')->first();

        if ($webseries) {            
            $seasons = WebSeriesSeason::where('web_series_id', $webseries->id)->orderBy('season_order')->get();
            
            if (count($seasons) > 0) {                
                $web_series_seasons = [];
                foreach ($seasons as $key => $season) {
                    $web_series_seasons[] = $season;
                    $episodes = WebSeriesEpisode::where('season_id', $season->id)->orderBy('episoade_order')->get();                                

                    if (count($episodes) > 0) {                        
                        $web_episodes = [];
                        foreach ($episodes as $key => $episode) {
                            $web_episodes[] = $episode;
                        }
                        $season['episodes'] = $web_episodes;
                    }
                }    
                $webseries['seasons'] = $web_series_seasons;
            }

            print_r(json_encode($webseries));
        }
        else{
            print_r(json_encode([]));
        }
        

        
        // return response()->json([
        //     'webseries' => $webseries,
        // ]);

    }

    // public function getWebSeriesDetails(Request $request, $webseries_id){
    //     $webseries = WebSeries::with([
    //         'networks',
    //         'seasons.episodes' => function ($query) {
    //             $query->orderBy('episoade_order');
    //         }
    //     ])
    //     ->where('id', $webseries_id)
    //     ->where('status', 1)
    //     ->whereNull('deleted_at')
    //     ->first();

    //     if ($webseries) {
    //         // Sort seasons if needed
    //         $webseries->seasons = $webseries->seasons->sortBy('season_order')->values();

    //         return response()->json($webseries);
    //     }

    //     return response()->json([], 404);
    // }




    public function getMoviePlayLinks(Request $request, $id, $type=0){
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $movieLinks = MovieLink::where('movie_id', $id)->get();

        if ($movieLinks) {
            // print_r(json_encode(array(
            //     'status' => true,
            //     'message' => 'All Movie Links of movie id : '.$id.' .',
            //     'data' =>$movieLinks
            // )));    
            print_r(json_encode($movieLinks));
        }
        else{
            // print_r(json_encode(array(
            //     'status' => false,
            //     'message' => 'No Data found',
            //     'data' => []
            // )));
            print_r(json_encode([]));
        }
        exit;
    }

    public function getNetworks(Request $request){
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $networks = ContentNetwork::where('deleted_at', null)->get();

        if ($networks) {
            // print_r(json_encode(array(
            //     'status' => true,
            //     'message' => 'All Networks',
            //     'data' =>$networks
            // )));   
            print_r(json_encode($networks));
        }
        else{
            // print_r(json_encode(array(
            //     'status' => false,
            //     'message' => 'No Data found',
            //     'data' => []
            // )));
            print_r(json_encode([]));
        }
        exit;
    }

    public function getAllContentsOfNetwork(Request $request, $network_id){
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $contents = DB::table('content_network_log')->where('network_id', $network_id)->get();

        $jsonData = [];
        foreach ($contents as $key => $content) {
            # for movies
            if ($content->content_type == 1) {
                $newRow = $this->getMovieDetailsById($content->content_id);
                if($newRow != "") {
                    $jsonData[] = $newRow;
                }
            }
            else if ($content->content_type == 2){
                # for series
                $newRow = $this->getSeriesDetailsById($content->content_id);
                if($newRow != "") {
                    $jsonData[] = $newRow;
                }
            }
        }
        // print_r(json_encode(array(
        //     'status' => true,
        //     'message' => 'Contents with network id : '.$network_id,
        //     'data' =>$jsonData
        // ))); 
        print_r(json_encode($jsonData));
    }

    // 5 August 2025

    // bvjfgyjuty
    public function getGenreByContentNetwork(Request $request, $network_id){
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $movie_ids = DB::table('movie_content_network')->where('network_id', $network_id)->pluck('movie_id');

        if (count($movie_ids) > 0) {

            
            $genres = Movie::whereIn('id', $movie_ids)->pluck('genres')->filter()->toArray();
    
            $allGenres = collect($genres)->flatMap(fn($genre) => array_map('trim', explode(',', $genre)))->unique()->values(); // reindex
    
            if (count($allGenres) > 0) {            
                print_r(json_encode([
                    'status' => true,
                    'genres' => $allGenres
                ]));
                exit;
            }
            else{
                print_r(json_encode([
                    'status' => false,
                    'message' => 'No Genre Found !',
                ]));
                exit;
            }
        }
        else{
            print_r(json_encode([
                'status' => false,
                'message' => 'No Movies found with Content Id : '.$network_id
            ]));
            exit;
        }


    }
    // public function getAllContentsOfNetworkNew(Request $request, $network_id, $genre){
    //     $user_id = $this->get_user_id();
    //     $post = json_decode(file_get_contents('php://input', 'r'));

    //     // $contents = DB::table('content_network_log')->where('network_id', $network_id)->get();
    //     $contents = DB::table('content_network_log')->where('network_id', $network_id)->get();

    //     // print_r(json_encode($contents)); exit;

    //     $jsonData = [];
    //     $count = 0;
    //     foreach ($contents as $key => $content) {
    //         # for movies
    //         if ($content->content_type == 1) {
    //             $newRow = $this->getMovieDetailsById($content->content_id, $genre);
    //             if($newRow != "") {
    //                 $jsonData[] = $newRow;
    //                 $count +=1;
    //             }
    //         }
    //         else if ($content->content_type == 2){
    //             # for series
    //             $newRow = $this->getSeriesDetailsById($content->content_id, $genre);
    //             if($newRow != "") {
    //                 $jsonData[] = $newRow;
    //                 $count +=1;
    //             }
    //         }
    //     }
    //     print_r(json_encode($jsonData));
    // }

    public function getAllContentsOfNetworkNew(Request $request){
        $user_id = $this->get_user_id();
        // , $network_id, $genre = null
        $post = json_decode(file_get_contents('php://input', 'r'));
        $network_id = $post->network_id;
        $genre = $post->genre;

        // Get query parameters for pagination
        $page = (int) $request->query('page', 4);      // default page 1
        $perPage = (int) $request->query('records', 10); // default 10 records per page

        // Calculate offset
        $offset = ($page-1) * $perPage;

        // Fetch total count first (for pagination info)
        $totalContents = DB::table('content_network_log')
            ->where('network_id', $network_id)
            ->count();

            // print_r($perPage, $page); exit;

        // Fetch paginated content
        $contents = DB::table('content_network_log')
            ->where('network_id', $network_id)
            // ->offset($offset)
            // ->limit($perPage)
            ->get();

        // print_r($contents); exit;

        

        $jsonData = [];
        foreach ($contents as $content) {
            if ($content->content_type == 1) {
                // Movies
                $newRow = $this->getMovieDetailsById($content->content_id, $genre);                
                if ($newRow != "") {
                    $jsonData[] = $newRow;  
                }
            } elseif ($content->content_type == 2) {
                // Series
                $newRow = $this->getSeriesDetailsById($content->content_id, $genre);
                if ($newRow != "") {
                    $jsonData[] = $newRow;
                }
            }
        }

        // print_r(json_encode([
        //     'total' => $totalContents,
        //     'data' => $jsonData
        // ])); 
        // exit;

        // Prepare response with pagination info
        return response()->json([
            'current_page' => $page,
            'per_page' => $perPage,
            'total_records' => $totalContents,
            'returned_records' => count($jsonData),
            'data' => $jsonData
        ]);
    }




    protected function getMovieDetailsById($id, $genre=null){

        $query = Movie::where('deleted_at', null)->where('id', $id)        
        ->with('networks');

        if (!empty($genre)) {
            $query->where('genres', 'LIKE', '%' . $genre . '%');
        }
        
        $movie = $query->first();

        if ($movie) {
            return $movie;
        }
        else{
            return "";
        }
    }

    public function getMovieDetails($contentId){
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $movie = Movie::where('status', 1)->where('deleted_at', null)->where('id', $contentId)        
        ->with('networks')
        ->first();
        
        if ($movie) {
            // print_r(json_encode(array(
            //     'status' => true,
            //     'message' => 'Movie with id : '.$contentId,
            //     'data' =>$movie
            // ))); 
            print_r(json_encode($movie));
        }
        else{
            // print_r(json_encode(array(
            //     'status' => false,
            //     'message' => 'Data not found !',                
            // ))); 
            print_r(json_encode([]));
        }
        
    }

    protected function getSeriesDetailsById($id, $genre=null){        

        $query = WebSeries::where('status', 1)->where('deleted_at', null)->where('id', $id)        
        ->with('networks');

        if (!empty($genre)) {
            $query->where('genres', 'LIKE', '%' . $genre . '%');
        }
        $series = $query->first();

        if ($series) {
            return $series;
        }
        else{
            return "";
        }        
        exit;
    }


    public function searchContent($searchTerm, $type=0){
        // $post = json_decode(file_get_contents('php://input', true));
        $user_id = $this->get_user_id();
        $jsonData = [];

        // echo strlen($searchTerm); exit;
        // $searchTerm = urldecode($searchTerm);
        if(strlen($searchTerm) > 2) {
            // movies
            $movies = Movie::where('name', 'like', '%' . $searchTerm . '%')
            ->orWhere('description', 'like', '%' . $searchTerm . '%')            
            ->with('networks')
            ->get();

            // series
            $series = WebSeries::where('name', 'like', '%' . $searchTerm . '%')
            ->orWhere('description', 'like', '%' . $searchTerm . '%')
            ->with('networks')
            ->get();

            $channels = Channel::select(
                'id',
                'channel_name as name',
                'channel_description  as description',
                'channel_name as name',
                'channel_logo as banner',
                'channel_link as url',
                'status'
            )->where('channel_name', 'like', '%' . $searchTerm . '%')
            ->orWhere('channel_description', 'like', '%' . $searchTerm . '%')
            ->get();



            if (count($movies) > 0) {
                foreach ($movies as $key => $movie) {
                    $jsonData[] = $movie;
                }
            }


            if (count($series) > 0) {
                foreach ($series as $key => $serie) {
                    $jsonData[] = $serie;
                }
            }

            if (count($channels) > 0) {
                foreach ($channels as $key => $channel) {
                    $jsonData[] = $channel;
                }
            }

            if (!empty($jsonData)) {
                // print_r(json_encode(array(
                //     'status' => true,                    
                //     'data' =>$jsonData
                // ))); 
                print_r(json_encode($jsonData));
            }
            else{
                // print_r(json_encode(array(
                //     'status' => false,
                //     'message' => 'Data not found !',                    
                //     'data' => []
                // ))); 
                print_r(json_encode([]));
            }

        }
    }
    
    public function getTvChannels(Request $request){
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $tvChannels = TvChannel::where('deleted_at', null)->where('status',1);

        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $page = (int) $_GET['page'];
            $limit = isset($_GET['records']) && is_numeric($_GET['records']) ? (int) $_GET['records'] : 10;

            $tvChannels = $tvChannels->paginate($limit, ['*'], 'page', $page);
            // echo 'Page Set'; exit;

            if (!$tvChannels->items()) {
                print_r(json_encode([]));
                exit;
            }

            print_r(json_encode($tvChannels->items()));
        }
        else{
            if(isset($_GET['records']) && $_GET['records'] > 0){
                $tvChannels = $tvChannels->limit($_GET['records'])->get();
            }
            else{
                $tvChannels = $tvChannels->get();
            }

            if (!$tvChannels) {
                print_r(json_encode([]));
                exit;
            }
            print_r(json_encode($tvChannels));     
            exit;
        }

    }

    public function getTvShows(Request $request,$channelId = null){
        if(!$channelId){
            echo "channel id required ";
            exit;
        }
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $tvShows = TvShow::where('deleted_at', null)->where('tv_channel_id',$channelId)->where('status',1);

        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $page = (int) $_GET['page'];
            $limit = isset($_GET['records']) && is_numeric($_GET['records']) ? (int) $_GET['records'] : 10;

            $tvShows = $tvShows->paginate($limit, ['*'], 'page', $page);            

            if (!$tvShows->items()) {
                print_r(json_encode([]));
                exit;
            }

            print_r(json_encode($tvShows->items()));
        }
        else{
            if(isset($_GET['records']) && $_GET['records'] > 0){
                $tvShows = $tvShows->limit($_GET['records'])->get();
            }
            else{
                $tvShows = $tvShows->get();
            }

            if (!$tvShows) {
                print_r(json_encode([]));
                exit;
            }
            print_r(json_encode($tvShows));     
            exit;
        }
    }

    public function getTvShowSeasons(Request $request,$showId = null){
        if(!$showId){
            echo "channel id required ";
            exit;
        }
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $tvShowSeasons = TvShowSeason::where('deleted_at', null)->where('show_id',$showId)->where('status',1);

        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $page = (int) $_GET['page'];
            $limit = isset($_GET['records']) && is_numeric($_GET['records']) ? (int) $_GET['records'] : 10;

            $tvShowSeasons = $tvShowSeasons->paginate($limit, ['*'], 'page', $page);            
            
            if (!$tvShowSeasons->items()) {
                print_r(json_encode([]));
                exit;
            }

            print_r(json_encode($tvShowSeasons->items()));
        }
        else{
            if(isset($_GET['records']) && $_GET['records'] > 0){
                $tvShowSeasons = $tvShowSeasons->limit($_GET['records'])->get();
            }
            else{
                $tvShowSeasons = $tvShowSeasons->get();
            }

            if (!$tvShowSeasons) {
                print_r(json_encode([]));
                exit;
            }
            print_r(json_encode($tvShowSeasons));     
            exit;
        }
    }


    public function getTvShowEpisodes(Request $request,$seasonId = null){
        if(!$seasonId){
            echo "channel id required ";
            exit;
        }
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $tvShowEpisodes = TvShowEpisode::where('deleted_at', null)->where('season_id',$seasonId)->where('status',1);

        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $page = (int) $_GET['page'];
            $limit = isset($_GET['records']) && is_numeric($_GET['records']) ? (int) $_GET['records'] : 10;

            $tvShowEpisodes = $tvShowEpisodes->paginate($limit, ['*'], 'page', $page);            
            
            if (!$tvShowEpisodes->items()) {
                print_r(json_encode([]));
                exit;
            }

            print_r(json_encode($tvShowEpisodes->items()));
        }
        else{
            if(isset($_GET['records']) && $_GET['records'] > 0){
                $tvShowEpisodes = $tvShowEpisodes->limit($_GET['records'])->get();
            }
            else{
                $tvShowEpisodes = $tvShowEpisodes->get();
            }

            if (!$tvShowEpisodes) {
                print_r(json_encode([]));
                exit;
            }
            print_r(json_encode($tvShowEpisodes));     
            exit;
        }
    }

    // 30 june 2025

    public function getReligiousChannel(Request $request){
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $relChannels = RelChannel::where('deleted_at', null)->where('status',1);

        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $page = (int) $_GET['page'];
            $limit = isset($_GET['records']) && is_numeric($_GET['records']) ? (int) $_GET['records'] : 10;

            $relChannels = $relChannels->paginate($limit, ['*'], 'page', $page);            
            
            if (!$relChannels->items()) {
                print_r(json_encode([]));
                exit;
            }

            print_r(json_encode($relChannels->items()));
        }
        else{
            if(isset($_GET['records']) && $_GET['records'] > 0){
                $relChannels = $relChannels->limit($_GET['records'])->get();
            }
            else{
                $relChannels = $relChannels->get();
            }

            if (!$relChannels) {
                print_r(json_encode([]));
                exit;
            }
            print_r(json_encode($relChannels));     
            exit;
        }
    }

    public function getReligiousShows(Request $request,$channelId = null){
        if(!$channelId){
            echo "channel id required ";
            exit;
        }
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $relShows = RelShow::where('deleted_at', null)->where('channel_id',$channelId)->where('status',1);

        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $page = (int) $_GET['page'];
            $limit = isset($_GET['records']) && is_numeric($_GET['records']) ? (int) $_GET['records'] : 10;

            $relShows = $relShows->paginate($limit, ['*'], 'page', $page);            
            
            if (!$relShows->items()) {
                print_r(json_encode([]));
                exit;
            }

            print_r(json_encode($relShows->items()));
        }
        else{
            if(isset($_GET['records']) && $_GET['records'] > 0){
                $relShows = $relShows->limit($_GET['records'])->get();
            }
            else{
                $relShows = $relShows->get();
            }

            if (!$relShows) {
                print_r(json_encode([]));
                exit;
            }
            print_r(json_encode($relShows));     
            exit;
        }
    }

    public function getReligiousShowsEpisodes(Request $request,$showId = null){
        if(!$showId){
            echo "channel id required ";
            exit;
        }
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $relShowepisodes = RelshowsEpisode::where('deleted_at', null)->where('show_id',$showId)->where('status',1);

        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $page = (int) $_GET['page'];
            $limit = isset($_GET['records']) && is_numeric($_GET['records']) ? (int) $_GET['records'] : 10;

            $relShowepisodes = $relShowepisodes->paginate($limit, ['*'], 'page', $page);            
            
            if (!$relShowepisodes->items()) {
                print_r(json_encode([]));
                exit;
            }

            print_r(json_encode($relShowepisodes->items()));
        }
        else{
            if(isset($_GET['records']) && $_GET['records'] > 0){
                $relShowepisodes = $relShowepisodes->limit($_GET['records'])->get();
            }
            else{
                $relShowepisodes = $relShowepisodes->get();
            }

            if (!$relShowepisodes) {
                print_r(json_encode([]));
                exit;
            }
            print_r(json_encode($relShowepisodes));     
            exit;
        }
    }


    public function getsportCategories(Request $request){
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $categories = SportsCategory::where('deleted_at', null)->where('status',1);

        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $page = (int) $_GET['page'];
            $limit = isset($_GET['records']) && is_numeric($_GET['records']) ? (int) $_GET['records'] : 10;

            $categories = $categories->paginate($limit, ['*'], 'page', $page);            
            
            if (!$categories->items()) {
                print_r(json_encode([]));
                exit;
            }

            print_r(json_encode($categories->items()));
        }
        else{
            if(isset($_GET['records']) && $_GET['records'] > 0){
                $categories = $categories->limit($_GET['records'])->get();
            }
            else{
                $categories = $categories->get();
            }

            if (!$categories) {
                print_r(json_encode([]));
                exit;
            }
            print_r(json_encode($categories));     
            exit;
        }
    }

    public function getsportTournament(Request $request,$cateId = null){
        if(!$cateId){
            echo "channel id required ";
            exit;
        }
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $tournaments = SportsTournament::where('deleted_at', null)->where('sports_category_id',$cateId)->where('status',1);

        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $page = (int) $_GET['page'];
            $limit = isset($_GET['records']) && is_numeric($_GET['records']) ? (int) $_GET['records'] : 10;

            $tournaments = $tournaments->paginate($limit, ['*'], 'page', $page);            
            
            if (!$tournaments->items()) {
                print_r(json_encode([]));
                exit;
            }

            print_r(json_encode($tournaments->items()));
        }
        else{
            if(isset($_GET['records']) && $_GET['records'] > 0){
                $tournaments = $tournaments->limit($_GET['records'])->get();
            }
            else{
                $tournaments = $tournaments->get();
            }

            if (!$tournaments) {
                print_r(json_encode([]));
                exit;
            }
            print_r(json_encode($tournaments));     
            exit;
        }
    }


    public function getTouranamentSeasons(Request $request,$tournamentId = null){
        if(!$tournamentId){
            echo "tournament id required ";
            exit;
        }
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $touramentSeasons = TournamentSeason::where('deleted_at', null)->where('sports_tournament_id',$tournamentId)->where('status',1);

        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $page = (int) $_GET['page'];
            $limit = isset($_GET['records']) && is_numeric($_GET['records']) ? (int) $_GET['records'] : 10;

            $touramentSeasons = $touramentSeasons->paginate($limit, ['*'], 'page', $page);            
            
            if (!$touramentSeasons->items()) {
                print_r(json_encode([]));
                exit;
            }

            print_r(json_encode($touramentSeasons->items()));
        }
        else{
            if(isset($_GET['records']) && $_GET['records'] > 0){
                $touramentSeasons = $touramentSeasons->limit($_GET['records'])->get();
            }
            else{
                $touramentSeasons = $touramentSeasons->get();
            }

            if (!$touramentSeasons) {
                print_r(json_encode([]));
                exit;
            }
            print_r(json_encode($touramentSeasons));     
            exit;
        }
    }

    public function getTouranamentSeasonsEvents(Request $request,$seasonId = null){
        if(!$seasonId){
            echo "season id required ";
            exit;
        }
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $touramentSeasonEvents = TournamentMatches::where('deleted_at', null)->where('tournament_season_id',$seasonId)->where('status',1);

        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $page = (int) $_GET['page'];
            $limit = isset($_GET['records']) && is_numeric($_GET['records']) ? (int) $_GET['records'] : 10;

            $touramentSeasonEvents = $touramentSeasonEvents->paginate($limit, ['*'], 'page', $page);            
            
            if (!$touramentSeasonEvents->items()) {
                print_r(json_encode([]));
                exit;
            }

            print_r(json_encode($touramentSeasonEvents->items()));
        }
        else{
            if(isset($_GET['records']) && $_GET['records'] > 0){
                $touramentSeasonEvents = $touramentSeasonEvents->limit($_GET['records'])->get();
            }
            else{
                $touramentSeasonEvents = $touramentSeasonEvents->get();
            }

            if (!$touramentSeasonEvents) {
                print_r(json_encode([]));
                exit;
            }
            print_r(json_encode($touramentSeasonEvents));     
            exit;
        }
    }

    public function getAllAbove18Movies(Request $request, $pin){
        $above_18_pin = $this->get_user_pin();
        
        if ($pin != $above_18_pin) {
            print_r(json_encode([
                'status' => false,
                'message' => 'Invalid Pin'
            ]));
            exit;
        }
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));
        $query = AdultMovie::where('status', 1)
                ->whereNull('deleted_at')
                ->with('networks');
        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $page = (int) $_GET['page'];
            $limit = isset($_GET['records']) && is_numeric($_GET['records']) ? (int) $_GET['records'] : 10;
    
            $movies = $query->paginate($limit, ['*'], 'page', $page);

            print_r(json_encode($movies->items()));
        } else {

            if (isset($_GET['records']) && $_GET['records'] > 0) {
                $movies = $query->limit($_GET['records'])->get();
            } else {
                $movies = $query->get();
            }
            print_r(json_encode($movies));
            exit;
        }

        // print_r(json_encode(array(
        //     'status' => true,
        //     'message' => 'All active Movie.',
        //     'data' =>$movies
        // )));
        
    }

    // 18 july 2025

    // KidsChannel
    public function getKidsChannels(Request $request){
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $kidsChannels = KidsChannel::where('deleted_at', null)->where('status',1);

        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $page = (int) $_GET['page'];
            $limit = isset($_GET['records']) && is_numeric($_GET['records']) ? (int) $_GET['records'] : 10;

            $kidsChannels = $kidsChannels->paginate($limit, ['*'], 'page', $page);
            // echo 'Page Set'; exit;

            if (!$kidsChannels->items()) {
                print_r(json_encode([]));
                exit;
            }

            print_r(json_encode($kidsChannels->items()));
        }
        else{
            if(isset($_GET['records']) && $_GET['records'] > 0){
                $kidsChannels = $kidsChannels->limit($_GET['records'])->get();
            }
            else{
                $kidsChannels = $kidsChannels->get();
            }

            if (!$kidsChannels) {
                print_r(json_encode([]));
                exit;
            }
            print_r(json_encode($kidsChannels));     
            exit;
        }

    }



    // KidsShow
    public function getKidsShows(Request $request,$channelId = null){
        if(!$channelId){
            echo "channel id required ";
            exit;
        }
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $kidShows = KidsShow::where('deleted_at', null)->where('kid_channel_id',$channelId)->where('status',1);

        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $page = (int) $_GET['page'];
            $limit = isset($_GET['records']) && is_numeric($_GET['records']) ? (int) $_GET['records'] : 10;

            $kidShows = $kidShows->paginate($limit, ['*'], 'page', $page);            

            if (!$kidShows->items()) {
                print_r(json_encode([]));
                exit;
            }

            print_r(json_encode($kidShows->items()));
        }
        else{
            if(isset($_GET['records']) && $_GET['records'] > 0){
                $kidShows = $kidShows->limit($_GET['records'])->get();
            }
            else{
                $kidShows = $kidShows->get();
            }

            if (!$kidShows) {
                print_r(json_encode([]));
                exit;
            }
            print_r(json_encode($kidShows));     
            exit;
        }
    }


    // KidShowsSeason
    public function getKidsShowSeasons(Request $request,$showId = null){
        if(!$showId){
            echo "channel id required ";
            exit;
        }
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $kidShowSeasons = KidShowsSeason::where('deleted_at', null)->where('show_id',$showId)->where('status',1);

        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $page = (int) $_GET['page'];
            $limit = isset($_GET['records']) && is_numeric($_GET['records']) ? (int) $_GET['records'] : 10;

            $kidShowSeasons = $kidShowSeasons->paginate($limit, ['*'], 'page', $page);            
            
            if (!$kidShowSeasons->items()) {
                print_r(json_encode([]));
                exit;
            }

            print_r(json_encode($kidShowSeasons->items()));
        }
        else{
            if(isset($_GET['records']) && $_GET['records'] > 0){
                $kidShowSeasons = $kidShowSeasons->limit($_GET['records'])->get();
            }
            else{
                $kidShowSeasons = $kidShowSeasons->get();
            }

            if (!$kidShowSeasons) {
                print_r(json_encode([]));
                exit;
            }
            print_r(json_encode($kidShowSeasons));     
            exit;
        }
    }

    // KidshowsEpisode
    public function getKidShowEpisodes(Request $request,$seasonId = null){
        if(!$seasonId){
            echo "channel id required ";
            exit;
        }
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $kidShowEpisodes = KidshowsEpisode::where('deleted_at', null)->where('season_id',$seasonId)->where('status',1);

        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $page = (int) $_GET['page'];
            $limit = isset($_GET['records']) && is_numeric($_GET['records']) ? (int) $_GET['records'] : 10;

            $kidShowEpisodes = $kidShowEpisodes->paginate($limit, ['*'], 'page', $page);            
            
            if (!$kidShowEpisodes->items()) {
                print_r(json_encode([]));
                exit;
            }

            print_r(json_encode($kidShowEpisodes->items()));
        }
        else{
            if(isset($_GET['records']) && $_GET['records'] > 0){
                $kidShowEpisodes = $kidShowEpisodes->limit($_GET['records'])->get();
            }
            else{
                $kidShowEpisodes = $kidShowEpisodes->get();
            }

            if (!$kidShowEpisodes) {
                print_r(json_encode([]));
                exit;
            }
            print_r(json_encode($kidShowEpisodes));     
            exit;
        }
    }



    // TvChannelPak
    public function getTvChannelsPak(Request $request){
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $tvChannels = TvChannelPak::where('deleted_at', null)->where('status',1);

        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $page = (int) $_GET['page'];
            $limit = isset($_GET['records']) && is_numeric($_GET['records']) ? (int) $_GET['records'] : 10;

            $tvChannels = $tvChannels->paginate($limit, ['*'], 'page', $page);
            // echo 'Page Set'; exit;

            if (!$tvChannels->items()) {
                print_r(json_encode([]));
                exit;
            }

            print_r(json_encode($tvChannels->items()));
        }
        else{
            if(isset($_GET['records']) && $_GET['records'] > 0){
                $tvChannels = $tvChannels->limit($_GET['records'])->get();
            }
            else{
                $tvChannels = $tvChannels->get();
            }

            if (!$tvChannels) {
                print_r(json_encode([]));
                exit;
            }
            print_r(json_encode($tvChannels));     
            exit;
        }

    }

    // TvShowPak
    public function getTvShowsPak(Request $request,$channelId = null){
        if(!$channelId){
            echo "channel id required ";
            exit;
        }
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $tvShows = TvShowPak::where('deleted_at', null)->where('tv_channel_id',$channelId)->where('status',1);

        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $page = (int) $_GET['page'];
            $limit = isset($_GET['records']) && is_numeric($_GET['records']) ? (int) $_GET['records'] : 10;

            $tvShows = $tvShows->paginate($limit, ['*'], 'page', $page);            

            if (!$tvShows->items()) {
                print_r(json_encode([]));
                exit;
            }

            print_r(json_encode($tvShows->items()));
        }
        else{
            if(isset($_GET['records']) && $_GET['records'] > 0){
                $tvShows = $tvShows->limit($_GET['records'])->get();
            }
            else{
                $tvShows = $tvShows->get();
            }

            if (!$tvShows) {
                print_r(json_encode([]));
                exit;
            }
            print_r(json_encode($tvShows));     
            exit;
        }
    }

    // TvShowSeasonPak
    public function getTvShowSeasonsPak(Request $request,$showId = null){
        if(!$showId){
            echo "channel id required ";
            exit;
        }
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $tvShowSeasons = TvShowSeasonPak::where('deleted_at', null)->where('show_id',$showId)->where('status',1);

        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $page = (int) $_GET['page'];
            $limit = isset($_GET['records']) && is_numeric($_GET['records']) ? (int) $_GET['records'] : 10;

            $tvShowSeasons = $tvShowSeasons->paginate($limit, ['*'], 'page', $page);            
            
            if (!$tvShowSeasons->items()) {
                print_r(json_encode([]));
                exit;
            }

            print_r(json_encode($tvShowSeasons->items()));
        }
        else{
            if(isset($_GET['records']) && $_GET['records'] > 0){
                $tvShowSeasons = $tvShowSeasons->limit($_GET['records'])->get();
            }
            else{
                $tvShowSeasons = $tvShowSeasons->get();
            }

            if (!$tvShowSeasons) {
                print_r(json_encode([]));
                exit;
            }
            print_r(json_encode($tvShowSeasons));     
            exit;
        }
    }

    // TvShowEpisodePak
    public function getTvShowEpisodesPak(Request $request,$seasonId = null){
        if(!$seasonId){
            echo "channel id required ";
            exit;
        }
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $tvShowEpisodes = TvShowEpisodePak::where('deleted_at', null)->where('season_id',$seasonId)->where('status',1);

        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $page = (int) $_GET['page'];
            $limit = isset($_GET['records']) && is_numeric($_GET['records']) ? (int) $_GET['records'] : 10;

            $tvShowEpisodes = $tvShowEpisodes->paginate($limit, ['*'], 'page', $page);            
            
            if (!$tvShowEpisodes->items()) {
                print_r(json_encode([]));
                exit;
            }

            print_r(json_encode($tvShowEpisodes->items()));
        }
        else{
            if(isset($_GET['records']) && $_GET['records'] > 0){
                $tvShowEpisodes = $tvShowEpisodes->limit($_GET['records'])->get();
            }
            else{
                $tvShowEpisodes = $tvShowEpisodes->get();
            }

            if (!$tvShowEpisodes) {
                print_r(json_encode([]));
                exit;
            }
            print_r(json_encode($tvShowEpisodes));     
            exit;
        }
    }

    
    public function getAllStageShowsPak(Request $request){        
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));
        $query = StageshowPak::where('status', 1)->whereNull('deleted_at')->with('networks');
        
        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $page = (int) $_GET['page'];
            $limit = isset($_GET['records']) && is_numeric($_GET['records']) ? (int) $_GET['records'] : 10;
    
            $stageShows = $query->paginate($limit, ['*'], 'page', $page);

            print_r(json_encode($stageShows->items()));
        } else {

            if (isset($_GET['records']) && $_GET['records'] > 0) {
                $stageShows = $query->limit($_GET['records'])->get();
            } else {
                $stageShows = $query->get();
            }
            print_r(json_encode($stageShows));
            exit;
        }        
    }

    public function getAllLaughterShows(Request $request){

        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));
        $query = Laughterhow::where('status', 1)->whereNull('deleted_at')->with('networks');
        
        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $page = (int) $_GET['page'];
            $limit = isset($_GET['records']) && is_numeric($_GET['records']) ? (int) $_GET['records'] : 10;
    
            $laughterShows = $query->paginate($limit, ['*'], 'page', $page);

            print_r(json_encode($laughterShows->items()));
        } else {

            if (isset($_GET['records']) && $_GET['records'] > 0) {
                $laughterShows = $query->limit($_GET['records'])->get();
            } else {
                $laughterShows = $query->get();
            }
            print_r(json_encode($laughterShows));
            exit;
        }        
    }


    // public function getLiveTvGenreList(Request $request){
    //     $user_id = $this->get_user_id();
            
    //     $channels = Channel::where('channels.status', 1)
    //         ->whereNull('channels.deleted_at')
    //         ->leftJoin('languages', 'channels.channel_language', '=', 'languages.id')
    //         ->get([
    //             'channels.genres',
    //             'languages.id as language_id',
    //             'languages.title as language_title'
    //         ]);
    
    //     $genreLanguageMap = [];
    
    //     foreach ($channels as $channel) {
    //         $genres = array_map('trim', explode(',', $channel->genres));
    //         $languageId = $channel->language_id;
    //         $languageTitle = $channel->language_title;
    
    //         // Skip if language data is missing
    //         if (!$languageId || !$languageTitle) continue;
    
    //         foreach ($genres as $genre) {
    //             if (!isset($genreLanguageMap[$genre])) {
    //                 $genreLanguageMap[$genre] = [];
    //             }
    
    //             // Check if language already added
    //             $alreadyExists = collect($genreLanguageMap[$genre])->contains(function ($lang) use ($languageId) {
    //                 return $lang['id'] == $languageId;
    //             });
    
    //             if (!$alreadyExists) {
    //                 $genreLanguageMap[$genre][] = [
    //                     'id' => $languageId,
    //                     'title' => $languageTitle
    //                 ];
    //             }
    //         }
    //     }
    
    //     // Format final output
    //     $result = [];
    //     foreach ($genreLanguageMap as $genre => $languages) {
    //         $result[] = [
    //             'genre' => $genre,
    //             'languages' => $languages
    //         ];
    //     }
    
    //     return response()->json($result);
    // }

    public function getLiveTvGenreList(Request $request)
    {
        $user_id = $this->get_user_id();

        $channels = Channel::where('channels.status', 1)
            ->whereNull('channels.deleted_at')
            ->leftJoin('languages', 'channels.channel_language', '=', 'languages.id')
            ->get([
                'channels.genres',
                'languages.id as language_id',
                'languages.title as language_title'
            ]);

        $genreLanguageMap = [];
        $allLanguages = [];

        foreach ($channels as $channel) {
            // Split and clean genres
            $genres = array_filter(array_map('trim', explode(',', $channel->genres ?? '')));

            if (!$channel->language_id || !$channel->language_title) {
                continue;
            }

            // Collect ALL unique languages for "All"
            if (!collect($allLanguages)->contains('id', $channel->language_id)) {
                $allLanguages[] = [
                    'id'    => $channel->language_id,
                    'title' => $channel->language_title,
                ];
            }

            foreach ($genres as $genre) {
                if (!isset($genreLanguageMap[$genre])) {
                    $genreLanguageMap[$genre] = [];
                }

                if (!collect($genreLanguageMap[$genre])->contains('id', $channel->language_id)) {
                    $genreLanguageMap[$genre][] = [
                        'id'    => $channel->language_id,
                        'title' => $channel->language_title,
                    ];
                }
            }
        }

        // Convert to required array format
        $finalResult = [];

        // Add "All" first
        $finalResult[] = [
            'genre'     => 'All',
            'languages' => $allLanguages
        ];

        // Now add other genres
        foreach ($genreLanguageMap as $genre => $languages) {
            $finalResult[] = [
                'genre'     => $genre,
                'languages' => $languages
            ];
        }

        return response()->json([
            'status' => true,
            'data'   => $finalResult
        ]);
    }



    public function getAllLiveTV(Request $request){
        $user_id = $this->get_user_id();
        
        $post = json_decode(file_get_contents('php://input', 'r'));
        
        $genre = $post->genere;
        $languageId = $post->languageId;
    
        // Start query with join
        $query = Channel::where('channels.status', 1)
            ->whereNull('channels.deleted_at')
            ->leftJoin('languages', 'channels.channel_language', '=', 'languages.id')
            ->select(
                'channels.*',
                'languages.id as channel_language_id',
                'languages.title as channel_language_title'
            );
    
        // ✅ Genre Filter (from route param)
        if (!empty($genre)) {
            // Get all genres to validate
            $channel_genres = Channel::where('status', 1)->pluck('genres');
            $genresList = collect($channel_genres)
                ->filter()
                ->flatMap(fn($g) => explode(',', $g))
                ->map(fn($g) => trim($g))
                ->unique()
                ->values()
                ->all();
    
            if (in_array($genre, $genresList)) {
                $query->where('channels.genres', 'like', '%' . $genre . '%');
            } else {
                return response()->json([]); // invalid genre
            }
        }
    
        // ✅ Language Filter (from route param)
        if (!empty($languageId) && is_numeric($languageId)) {
            $query->where('languages.id', $languageId);
        }
    
        // Pagination logic
        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $page = (int) $_GET['page'];
            $limit = isset($_GET['records']) && is_numeric($_GET['records']) ? (int) $_GET['records'] : 10;
    
            $channels = $query->paginate($limit, ['*'], 'page', $page);
            return response()->json($channels->items());
        } else {
            if (isset($_GET['records']) && $_GET['records'] > 0) {
                $channels = $query->limit($_GET['records'])->get();
            } else {
                $channels = $query->get();
            }
            return response()->json($channels);
        }
    }

}
