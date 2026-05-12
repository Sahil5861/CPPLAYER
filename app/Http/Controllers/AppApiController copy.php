<?php

namespace App\Http\Controllers;

use App\Models\AdminPlan;
use App\Models\AdminSuperAdminPlan;
use App\Models\Channel;
use App\Models\Movie;
use App\Models\WebSeries;
use App\Models\WebSeriesSeason;
use App\Models\WebSeriesEpisode;
use App\Models\ContentNetwork;
use App\Models\TvChannel;
use App\Models\TvShow;
use App\Models\TvShowEpisode;
use App\Models\TvShowSeason;


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

    
    
    public function loginAccessUser($userData){
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

        // $json = file_get_contents("http://ipinfo.io/{$ipaddress}");
        $json = file_get_contents("http://ip-api.com/json/{$ipaddress}");
        // http://ip-api.com/json/
        $details = json_decode($json);

        // return $details;
        // print_r($details); exit;
        $auth_key = md5(uniqid() . $userData->id);
        Userauth::where('user_id',$userData->id)->update(['status'=>0]);
        $userauth = new Userauth();

        $userauth->auth_key = $auth_key;
        $userauth->user_id = $userData->id;
        $userauth->ip_address = $details->query;
        $userauth->browser = $browser;
        $userauth->city = @$details->city;
        $userauth->country = @$details->country;
        $userauth->postal = @$details->postal;
        $userauth->mac_address = $userData->mac_address;

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

            if ($data_user)
            {
                $plans = UserPlanDetails::where(['user_id'=>$data_user->id,'status'=>1])->whereDate('plan_end_date','>=',date('Y-m-d'))->orderBy('id','desc')->get();

                if(count($plans) == 0){
                    print_r(json_encode(array(
                        'status' => false,
                        'msg' => 'You have not active plan. Kindly recharge your account.s'
                    )));
                    exit;
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
                        $this->loginAccessUser($data_user);
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
                        $this->loginAccessUser($data_user);
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

        // $user_id = $this->get_user_id();
        // $channel = Channel::where('status',1)->orderBy('channel_number','asc')->whereNull('deleted_at')->get();
        // print_r(json_encode(array(
        //     'status' => true,
        //     'message' => 'All channels.',
        //     'data' =>$channel
        // )));
        // exit;

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
                'status')->where('status',1)->whereNull('deleted_at')->get();
                
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

        $movies = Movie::where('status', 1)->where('deleted_at', null)        
        ->with('networks')
        ->get();

        // print_r(json_encode(array(
        //     'status' => true,
        //     'message' => 'All active Movie.',
        //     'data' =>$movies
        // )));
        print_r(json_encode($movies));
        exit;
    }

    public function getAllWebSeries(Request $request){
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $series = WebSeries::where('status', 1)->where('deleted_at', null)        
        ->with('networks')
        ->get();

        // print_r(json_encode(array(
        //     'status' => true,
        //     'message' => 'All active Web series.',
        //     'data' =>$series
        // )));
        print_r(json_encode($series));
        exit;
    }

    public function getSeasons(Request $request, $id){
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $seasons = WebSeriesSeason::where('web_series_id', $id)->whereNull('deleted_at')->get();

        if ($seasons) {
            // print_r(json_encode(array(
            //     'status' => true,
            //     'message' => 'All Seasons.',
            //     'data' =>$seasons
            // )));    
            print_r(json_encode($seasons));
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

    public function getEpisodes(Request $request, $id, $type=0){
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $episodes = WebSeriesEpisode::where('season_id', $id)->whereNull('deleted_at')->get();

        if ($episodes) {
            // print_r(json_encode(array(
            //     'status' => true,
            //     'message' => 'All Episodes of Season '.$id.' .',
            //     'data' =>$episodes
            // )));      
            print_r(json_encode($episodes));
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



    protected function getMovieDetailsById($id){

        $movie = Movie::where('status', 1)->where('deleted_at', null)->where('id', $id)        
        ->with('networks')
        ->first();
        
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

    protected function getSeriesDetailsById($id){        

        $series = WebSeries::where('status', 1)->where('deleted_at', null)->where('id', $id)        
        ->with('networks')
        ->first();

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

        $tvChannels = TvChannel::where('deleted_at', null)->where('status',1)->get();

        if ($tvChannels) {
            
            print_r(json_encode($tvChannels));
        }
        else{
            print_r(json_encode([]));
        }
        exit;
    }

    public function getTvShows(Request $request,$channelId = null){
        if(!$channelId){
            echo "channel id required ";
            exit;
        }
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $tvShows = TvShow::where('deleted_at', null)->where('tv_channel_id',$channelId)->where('status',1)->get();

        if ($tvShows) {
            
            print_r(json_encode($tvShows));
        }
        else{
            print_r(json_encode([]));
        }
        exit;
    }

    public function getTvShowSeasons(Request $request,$showId = null){
        if(!$showId){
            echo "channel id required ";
            exit;
        }
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $tvShowSeasons = TvShowSeason::where('deleted_at', null)->where('show_id',$showId)->where('status',1)->get();

        if ($tvShowSeasons) {
            
            print_r(json_encode($tvShowSeasons));
        }
        else{
            print_r(json_encode([]));
        }
        exit;
    }


    public function getTvShowEpisodes(Request $request,$seasonId = null){
        if(!$seasonId){
            echo "channel id required ";
            exit;
        }
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $tvShowEpisodes = TvShowEpisode::where('deleted_at', null)->where('season_id',$seasonId)->where('status',1)->get();

        if ($tvShowEpisodes) {
            
            print_r(json_encode($tvShowEpisodes));
        }
        else{
            print_r(json_encode([]));
        }
        exit;
    }


}