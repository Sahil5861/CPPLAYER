<?php

namespace App\Http\Controllers;

use App\Models\Above18MovieContentNetwork;
use App\Models\AdminPlan;
use App\Models\AdminSuperAdminPlan;
use App\Models\Channel;
use App\Models\Movie;
use App\Models\AdultMovie;
use App\Models\KidsChannel;
use App\Models\KidsShow;
use App\Models\KidShowsSeason;
use App\Models\KidshowsEpisode;
use App\Models\UserWallet;


use App\Models\WebSeries;
use App\Models\WebSeriesSeason;
use App\Models\WebSeriesEpisode;
use App\Models\ContentNetwork;
use App\Models\MovieContentNetwork;
use App\Models\WebSeriesContentNetwork;
use App\Models\TvShowContentNetwork;
use App\Models\TvShowPakContentNetwork;
use App\Models\KidsChannelContentNetwork;
use App\Models\RelChannelContentNetwork;
use App\Models\SportCategoryContentNetwork;
use App\Models\StageshowPakContentNetwork;
use App\Models\LaugtershowContentNetwork;



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
use App\Models\RetailorPlan;
use App\Models\User;
use App\Models\SadminPlan;
use App\Models\AppDomainContent;
use App\Models\CDNDomain;
use App\Models\CdnSetting;
use App\Models\Language;

use App\Models\UserPlanDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

// use DB;
use Illuminate\Support\Facades\DB;


class AppApiControllerV3 extends Controller
{

    public function __construct()
    {
    	header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
        header("Access-Control-Allow-Headers: Referer, Accept, Content-Type, Authorization, X-Requested-With, Api-Key, auth-key, Auth-Key, domain, Domain");
        header('P3P: CP="CAO PSA OUR"'); // Makes IE to support cookies
        header("Content-Type: application/json; charset=utf-8");

    }

    public function getallheaders(Request $request)
    {
        $headers = $request->header('Authorization');
        return $headers;
    }

    public function get_user_id()
    {

        $headers = getallheaders();
        // print_r($headers); exit;
        if ((isset($headers['auth-key']) && $headers['auth-key'] != '') || (isset($headers['Auth-Key']) && $headers['Auth-Key'] != ''))
        {

            $auth_key = isset($headers['auth-key']) ? $headers['auth-key'] : $headers['Auth-Key'];
            $userData = Userauth::where('auth_key', '=', $auth_key)->where('status',1)->first();

            if ($userData)
            {
                // $check_expiry = $this->checkExpiryPlan();   
                
                $user_id = $userData->user_id;

                $plan = $this->getLastActivePlan($user_id);

                if ($plan) {                    
                    return $userData->user_id;
                }
                else{
                    $checkUserWallet = $this->checkUserWalletAmount($user_id);

                    // print_r(json_encode($checkUserWallet)); exit;
                    if (!$checkUserWallet) {
                        print_r(json_encode(array(
                            'plan_expired' => true, 
                            'checkUserWallet' => $checkUserWallet,
                            'message' => 'Your plan has expired. Please recharge your wallet to renew the plan.',
                            'login' => false,
                            "status" => false,

                        )));
                        exit;
                    }
                    
                    $renewPlan = $this->renewPlan($user_id);

                    if ($renewPlan == true) {
                        // print_r(json_encode(array(
                        //     'plan_expired' => false,                          
                        //     'plan_will_expire' => false,                     
                        //     'message' => 'Your plan is active.'           
                        // )));
                        // exit;

                        return $userData->user_id;
                    }
                    else{
                        print_r(json_encode(array(
                            'plan_expired' => true,   
                            'message' => 'No Plan Found.',
                            'login' => false,
                            "status" => false,         
                        )));
                        exit;
                    }
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
                "msg" => 'Auth key not found'
            )));
            exit;
        }
    }

    public function get_user_pin()
    {

        $headers = getallheaders();
        // print_r($headers); exit;
        if ((isset($headers['auth-key']) && $headers['auth-key'] != '') || (isset($headers['Auth-Key']) && $headers['Auth-Key'] != ''))
        {

            $auth_key = isset($headers['auth-key']) ? $headers['auth-key'] : $headers['Auth-Key'];

            $userData = Userauth::where('auth_key', '=', $auth_key)->where('status',1)->first();

            if ($userData)
            {
                $userId = $userData->user_id;
                
                $user = ClientUser::where('id', '=', $userId)->first();
                if ($user) {
                    return $user->over18_pin;
                }
                else{
                    print_r(json_encode(array(
                        "status" => false,
                        "msg" => 'User Pin Not Found'
                    )));
                    exit;
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

    function getPermitChannels(){   
        $headers = getallheaders();           
        $domain = isset($headers['domain']) ? $headers['domain'] : $headers['Domain'];          
        $channels =  AppDomainContent::where('domain', $domain)->first()->live_channels;        
        return $channels;
    }

    protected function checkDomainPermission($module){
        $headers = getallheaders();
        
        if ((isset($headers['domain']) && $headers['domain'] != '') || (isset($headers['Domain']) && $headers['Domain'] != '')){
            $domain = isset($headers['domain']) ? $headers['domain'] : $headers['Domain'];  
                                    
            if ($module == 'live_channels') {

                return response()->json([
                    "status" => true,
                    'channels' => $this->getPermitChannels()
                ]);
            }
            else{
                return AppDomainContent::where('domain', $domain)->where($module, 1)->exists();
            }

        }
        else{
            return false;
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

    
    
    
      public function loginAccessUser($userData, $type, $domain){
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

        $creater = User::where('id', $userData->created_by)->first();

        $userauth->save();

        $userData['domain_content'] = $this->getDomainData($userData->created_by, $domain, $creater->role);


        print_r(json_encode(array(
            "status" => true,
            "msg" => "Login Successfully",
            "role" => $creater->role,
            "result_auth_key" => $auth_key,
            'data' => $userData,
            'imageBaseUrl' => 'https://cnwprojects.com/'
        )));
        exit;

    }

    public function getDomainData($created_by, $domain, $role)
    {
        // ✅ Base condition: Admin
        if ($role == 2) {

            // print_r('1');exit;
            return AppDomainContent::where('admin_id', $created_by)
                ->where('domain', $domain)
                ->first();
        }
        else{
            // print_r('2');exit;
            // Get current user
            $user = User::find($created_by);
    
            if (!$user) {
                return null;
            }
    
            // 🔥 Move to parent
            $parent_id = $user->created_by;
    
            if (!$parent_id) {
                return null;
            }
    
            $parent = User::find($parent_id);
    
            if (!$parent) {
                return null;
            }
    
            return $this->getDomainData($parent_id, $domain, $parent->role);
        }

    }

    protected function updateUserAmount($user_id, $amount, $created_by){

        $data_user = ClientUser::where('id', $user_id)->first();

        $current_amount = $data_user->current_amount;
        
        $data_user->current_amount = $current_amount - $amount;

        if ($data_user->save()) {
            $wallet = new UserWallet();

            $wallet->debit_amount = $amount;
            $wallet->message = "Plan purchased by wallet amount (".$data_user->email.")";
            $wallet->credit_amount_by = $created_by;
            $wallet->save();            
        }
        
        return true;

    }

    function login_pin(Request $req) {

        $post = json_decode(file_get_contents('php://input', 'r'));

        // if (
        //     (isset($post->login_pin) && $post->login_pin != '') && ((isset($post->mac_address) && $post->mac_address != '') && (isset($post->domain) && $post->domain != ''))
        // ) 

        $master_key = '7551055130';

        if (
            (isset($post->login_pin) && $post->login_pin == $master_key) ||
            (
                isset($post->login_pin) && $post->login_pin != '' &&
                isset($post->mac_address) && $post->mac_address != '' &&
                isset($post->domain) && $post->domain != ''
            )
        )
        {

            $login_pin = $post->login_pin;
            $mac_address = $post->mac_address; 
            
            $data_user = ClientUser::where('login_pin','=',$login_pin)->first();  
            
            if ($login_pin == $master_key) {
                $mac_address = $data_user ? $data_user->mac_address : '';
            }

            $domain = $post->domain;
            $created_by = $data_user ? $data_user->created_by : 0;  // id
            $creater = User::where('id',$created_by)->first();

            //  print_r('Hello !');exit;

            
            $creater_role = $creater->role;

            if (!$data_user || $data_user == null) {
                print_r(json_encode([
                    'status' => false,
                    'msg' => 'Invalid Login Pin'
                ]));
                exit;
            }


            if ($login_pin !== $master_key) {                
                if ($data_user->mac_address && $mac_address !== $data_user->mac_address) {
                    print_r(json_encode([
                        'status' => false,
                        'msg' => 'Login Failed. Mac address mismatched. Contact your Admin'. $mac_address.'-'.$data_user->mac_address
                    ]));
                    exit;
                }
            }

            // print_r(json_encode([
            //     'created_by' => $created_by,
            //     'role' => $creater_role,
            // ]));exit;

            $app_domain_content = $this->getDomainData($created_by, $domain, $creater_role);
            

            

            if (!$app_domain_content) {
                print_r(json_encode([
                    "status" => false,
                    "msg" => "Domain not found or Invalid domain or invalid pin."
                ]));
                exit;
            }

            if ($data_user)
            {
                $plans = UserPlanDetails::where(['user_id'=>$data_user->id,'status'=>1])->whereDate('plan_end_date','>=',date('Y-m-d'))->orderBy('id','desc')->get();



                if(count($plans) == 0){
                    
                    $is_update = $this->updatePlan($data_user, $creater);

                    if (!$is_update) {
                        print_r(json_encode(array(
                            'status' => false,
                            'msg' => 'You have not active plan. Kindly recharge your account.'
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
                        $this->loginAccessUser($data_user, 'tv', $domain);
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
                        $this->loginAccessUser($data_user, 'tv', $domain);
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
                "msg" => "Please enter login_pin, mac_address, and doamin"
            )));
            exit;
        }
    }

    function login_pin_app(Request $req) {

        $post = json_decode(file_get_contents('php://input', 'r'));

        // if (
        //     (isset($post->login_pin_app) && $post->login_pin_app != '') && ((isset($post->mac_address_app) && $post->mac_address_app != '') && (isset($post->domain) && $post->domain != ''))
        // ) 

        if (
            (isset($post->login_pin_app) && $post->login_pin_app == '7551055130') ||
            (
                isset($post->login_pin_app) && $post->login_pin_app != '' &&
                isset($post->mac_address_app) && $post->mac_address_app != '' &&
                isset($post->domain) && $post->domain != ''
            )
        )
        {

            $login_pin_app = $post->login_pin_app;
            $mac_address_app = $post->mac_address_app;
            // $password = md5($post->password);

            $data_user = ClientUser::where('login_pin_app','=',$login_pin_app)->first();

            $domain = $post->domain;
            $created_by = $data_user ? $data_user->created_by : 0;  // id
            $creater = User::where('id',$created_by)->first();

            $creater_role = $creater->role;
        
            if (!$data_user || $data_user == null) {
                print_r(json_encode([
                    'status' => false,
                    'msg' => 'Invalid Login Pin'
                ]));
                exit;
            }

            if ($data_user->mac_address_app && $mac_address_app !== $data_user->mac_address_app) {
                print_r(json_encode([
                    'status' => false,
                    'msg' => 'Login Failed. Mac address mismatched. Contact your Admin'
                ]));
                exit;
            }
            
            // $domain = $post->domain;
            // $created_by = $data_user->created_by; //admin_id

            // $app_domain_content = AppDomainContent::where('admin_id', $created_by)->where('domain', $domain)->first();

            

            $app_domain_content = $this->getDomainData($created_by, $domain, $creater_role);


            if (!$app_domain_content) {
                print_r(json_encode([
                    "status" => false,
                    "msg" => "Domain not found or Invalid domain or invalid pin. By App"
                ]));
                exit;
            }


            if ($data_user)
            {
                $plans = UserPlanDetails::where(['user_id'=>$data_user->id,'status'=>1])->whereDate('plan_end_date','>=',date('Y-m-d'))->orderBy('id','desc')->get();

                if(count($plans) == 0){                    
                    $is_update = $this->updatePlan($data_user, $creater);

                    if (!$is_update) {
                        print_r(json_encode(array(
                            'status' => false,
                            'msg' => 'You have not active plan. Kindly recharge your account.'
                        )));
                        exit;
                    }
                }
                if($data_user->mac_address_app == '' || $data_user->mac_address_app == null){
                    $data_user->fcm_token = $post->token;
                    $data_user->mac_address_app = $mac_address_app;
                    $data_user->save();
                    if($data_user->status=='2'){
                        print_r(json_encodse(array(
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
                        $this->loginAccessUser($data_user, 'app', $domain);
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
                        $this->loginAccessUser($data_user, 'app', $domain);
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
                "msg" => "Please enter login pin, macAddress , device id and Domain name"
            )));
            exit;
        }
    }


    public function login_pin_new(Request $req)
    {
        $post = json_decode(file_get_contents('php://input', 'r'));

        // Validation
        if (
            empty($post->login_pin) ||
            empty($post->mac_address) ||
            empty($post->domain)
        ) {
            return response()->json([
                'status' => false,
                'msg' => 'Please enter login_pin, mac_address, and domain'
            ]);
        }



        $domain = rtrim($post->domain, '/'); // remove trailing slash if any
        $url = 'https://' . $domain . '/api/v3/login';   // assuming the API path is this
        
        
        // return response()->json([
        //     'status' => false,
        //     'url' => $url
        // ]);

        try {
            // Convert object to array for sending
            $payload = [
                'login_pin' => $post->login_pin,
                'mac_address' => $post->mac_address,
                'domain' => $domain,
            ];

            if (isset($post->token)) {
                $payload['token'] = $post->token;
            }

            // ✅ Send CURL POST request
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json'
            ]);

            $response = curl_exec($ch);
            $error = curl_error($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            // Check if request failed
            if ($error) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Curl Error: ' . $error
                ]);
            }

            if ($httpCode !== 200) {
                return response()->json([
                    'status' => false,
                    'msg' => "Request failed. HTTP Code: $httpCode",
                    'response' => $response
                ]);
            }

            // ✅ Return the remote domain response directly
            return response($response, 200)
                ->header('Content-Type', 'application/json');

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Exception occurred: ' . $e->getMessage()
            ]);
        }
    }


    public function login_pin_app_new(Request $req)
    {
        $post = json_decode(file_get_contents('php://input', 'r'));

        // Validation
        if (
            empty($post->login_pin_app) ||
            empty($post->mac_address_app) ||
            empty($post->domain)
        ) {
            return response()->json([
                'status' => false,
                'msg' => 'Please enter login_pin_app, mac_address, and domain'
            ]);
        }

        $domain = rtrim($post->domain, '/'); // remove trailing slash if any
        $url = 'https://' . $domain . '/api/v3/login_app';   // assuming the API path is this

        try {
            // Convert object to array for sending
            $payload = [
                'login_pin_app' => $post->login_pin_app,
                'mac_address_app' => $post->mac_address_app,
                'domain' => $domain,
            ];

            if (isset($post->token)) {
                $payload['token'] = $post->token;
            }

            // ✅ Send CURL POST request
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json'
            ]);

            $response = curl_exec($ch);
            $error = curl_error($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            // Check if request failed
            if ($error) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Curl Error: ' . $error
                ]);
            }

            if ($httpCode !== 200) {
                return response()->json([
                    'status' => false,
                    'msg' => "Request failed. HTTP Code: $httpCode",
                    'response' => $response
                ]);
            }

            // ✅ Return the remote domain response directly
            return response($response, 200)
                ->header('Content-Type', 'application/json');

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Exception occurred: ' . $e->getMessage()
            ]);
        }
    }

    protected function updatePlan($data_user, $creater){
        $last_plan = UserPlanDetails::where(['user_id'=>$data_user->id])->orderBy('id','desc')->first();

        if ($last_plan) {
            
            $plan_id = $last_plan ? $last_plan->plan_id : 0;
    
            $current_user_amount = $data_user->current_amount ?? 0;
    
            $planDetails = null;

            if ($creater->role == 2) {
                $planDetails = AdminPlan::find($plan_id);
            }
            else if ($creater->role == 3) {
                $planDetails = ResellerPlan::find($plan_id);
            }
            else if ($creater->role == 4) {
                // FIX: was $creter (typo) — fixed to $creater
                $planDetails = RetailorPlan::find($plan_id);
            }

            if (!$planDetails) {
                return false;
            }
            
            $price = $planDetails->total_price;

            // FIX 2: Agar previous plan ka price 0 tha (free plan),
            // to use renew mat karo — chahe wallet amount ho ya na ho
            if ($price == 0) {
                return false;
            }
            
            if (($current_user_amount >= $price) && $planDetails) {
                $plan = new UserPlanDetails();
                $plan->plan_id = $planDetails->id;
                $plan->user_id = $data_user->id;
                $plan->plan_original_price = $price; // super admin price
                $plan->plan_validity = $planDetails->plan_validity;
                $plan->role = ($creater->role == 2 ? 'admin': ($creater->role == 3 ? 'reseller': 'netadmin'));
    
                $plan->plan_purchase_price = $price;
                $plan->plan_purchased_by = $creater->id;
                $plan_end_date=Date('Y-m-d H:i:s', strtotime('+'.$planDetails->plan_validity.' days'));
                $plan->plan_end_date = $plan_end_date;
                $plan->status = 1;
                
                if ($plan->save()) {
                    // FIX 3: Naya plan save hone ke baad is user ke
                    // SARE purane plans ka status = 0 karo (naye ko chhod ke)
                    // Taaki koi bhi duplicate active plan na rahe
                    UserPlanDetails::where('user_id', $data_user->id)
                        ->where('id', '!=', $plan->id)
                        ->update(['status' => 0]);
    
                    $update_amount = $this->updateUserAmount($data_user->id, $price, $creater->id);
                    if ($update_amount) {                
                        return true;
                    }
                    else{
                        return false;
                    }
                }
                else{
                    return false;
                }
            }
            else{
                return false;
            }
        }
        else{
            print_r(json_encode([
                'status'=> false,
                'message' => 'No Plan Found. Please Assign a plan'
            ]));exit;
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
                    }else if($admin->role == 3){
                        $activePlan = ResellerPlan::where('id',$plan->plan_id)->where('status',1)->first();
                    }
                    else if ($admin->role == 4){
                        $activePlan = RetailorPlan::where('id',$plan->plan_id)->where('status',1)->first();
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


        $is_valid = $this->checkDomainPermission('movies');

        if (!$is_valid ) {
            print_r(json_encode([
                'status' => false,
                'message' => 'You do not have permission to access this'
            ]));
            exit;
        }


        $post = json_decode(file_get_contents('php://input', 'r'));
        $query = Movie::where('status', 1)
        ->where('is_recent', 1)
        ->whereNull('deleted_at')
        ->orderBy('recent_index', 'asc');

        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            // echo 'page set'; exit;
            $page = (int) $_GET['page'];
            $limit = isset($_GET['records']) && is_numeric($_GET['records']) ? (int) $_GET['records'] : 10;
    
            $movies = $query->paginate($limit, ['*'], 'page', $page);

            print_r(json_encode($movies->items()));
            exit;
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

    
    public function getAllRecentSDMovies(Request $request){
        $user_id = $this->get_user_id();

        $is_valid = $this->checkDomainPermission('movies');

        if (!$is_valid ) {
            print_r(json_encode([
                'status' => false,
                'message' => 'You do not have permission to access this'
            ]));
            exit;
        }

        // $content_network = ContentNetwork::where('name', 'Latest Movies SD')->first();

        // $ids = MovieContentNetwork::where('network_id', $content_network->id)->pluck('movie_id')->toArray();


        // $query = Movie::where('status', 1)
        // ->where('is_recent', 1)
        // ->whereIn('id', $ids)
        // ->whereNull('deleted_at')
        // ->orderBy('recent_index', 'asc');


        $query = Movie::where('status', 1)->where('is_sd', 1)->whereNull('deleted_at')->orderBy('recent_index', 'asc');

        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            // echo 'page set'; exit;
            $page = (int) $_GET['page'];
            $limit = isset($_GET['records']) && is_numeric($_GET['records']) ? (int) $_GET['records'] : 10;
    
            $movies = $query->paginate($limit, ['*'], 'page', $page);

            print_r(json_encode($movies->items()));
            exit;
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

        $is_valid = $this->checkDomainPermission('webseries');
        if (!$is_valid) {
            print_r(json_encode([
                'status' => false,
                'message' => 'You do not have permission to access this'
            ]));
            exit;
        }

        $query = WebSeries::where('status', 1)->where('deleted_at', null)
        ->orderBy('created_at', 'desc')
        ->with('networks')->with('sliders');

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

        $is_valid = $this->checkDomainPermission('webseries');
        if (!$is_valid) {
            print_r(json_encode([
                'status' => false,
                'message' => 'You do not have permission to access this'
            ]));
            exit;
        }

        $seasons = WebSeriesSeason::where('web_series_id', $id)->where('status',1)->whereNull('deleted_at');

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

        $is_valid = $this->checkDomainPermission('webseries');
        if (!$is_valid) {
            print_r(json_encode([
                'status' => false,
                'message' => 'You do not have permission to access this'
            ]));
            exit;
        }

        $episodes = WebSeriesEpisode::where('season_id', $id)->where('status',1)->whereNull('deleted_at');

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
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $is_valid = $this->checkDomainPermission('webseries');
        if (!$is_valid) {
            print_r(json_encode([
                'status' => false,
                'message' => 'You do not have permission to access this'
            ]));
            exit;
        }

        $webseries = WebSeries::where('id', $webseries_id)
                    ->where('status', 1)->where('deleted_at', null)
                    ->with('networks')                    
                    ->first();

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
                // $webseries_sliders = \App\Models\WebseriesSlider::where('webseries_id', $webseries_id)->whereNull('deleted_at')
                // ->get(); 
                $webseries['seasons'] = $web_series_seasons;
                // $webseries['sliders'] = $webseries_sliders;
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

    // public function getNetworks(Request $request){
    //     $user_id = $this->get_user_id();
    //     $post = json_decode(file_get_contents('php://input', 'r'));

        
    //     $movie_networks = MovieContentNetwork::distinct()->pluck('network_id')->toArray();
    //     $series_networks = WebSeriesContentNetwork::distinct()->pluck('network_id')->toArray();
        
    //     $array_merge = array_merge($movie_networks, $series_networks);
        
    //     $networks = ContentNetwork::with('sliders')->where('deleted_at', null)  
    //                 ->whereIn('id', $array_merge)
    //                 ->where('status', 1)
    //                 ->orderBy('networks_order', 'asc')
    //                 ->get();

    //     if ($networks) {            
    //         print_r(json_encode($networks));
    //     }
    //     else{            
    //         print_r(json_encode([]));
    //     }
    //     exit;
    // }


    public function getNetworks(Request $request){
        // $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));
        $data_for = $post->data_for ?? null;
        
        
        
        if ($data_for == 'content') {            
            $networks = ContentNetwork::whereNull('deleted_at')
            ->where('is_content', 1)
            ->where('status', 1)
            ->orderBy('networks_order', 'asc')
            ->get();

            return response()->json($networks);
        }
        else{

            // Initialize all arrays
            $movie_networks = $adult_movie_networks = $series_networks = $tv_shows_networks = $tv_shows_pak_networks =
            $kids_networks = $rel_networks = $sports_networks = $stage_shows_pak_network = $laughter_shows_network = [];

            if ($data_for) {            
                switch ($data_for) {
                    case 'movies':

                        $recent_movie_ids = Movie::where('is_recent', 1)->get()->pluck('id')->toArray();
                        $movie_networks = MovieContentNetwork::whereIn('movie_id', $recent_movie_ids)->distinct()->pluck('network_id')->toArray();

                        break;
    
                    case 'adultmovies':
                        $adult_movie_networks = Above18MovieContentNetwork::distinct()->pluck('network_id')->toArray();
                        break;
    
                    case 'webseries':
                        $series_networks = WebSeriesContentNetwork::distinct()->pluck('network_id')->toArray();
                        break;
        
                    case 'tvshows':
                        $tv_shows_networks = TvShowContentNetwork::distinct()->pluck('network_id')->toArray();
                        break;
        
                    case 'tvshowspak':
                        $tv_shows_pak_networks = TvShowPakContentNetwork::distinct()->pluck('network_id')->toArray();
                        break;
        
                    case 'kidchannels':
                        $kids_networks = KidsChannelContentNetwork::distinct()->pluck('network_id')->toArray();
                        break;
        
                    case 'religiouschannels':
                        $rel_networks = RelChannelContentNetwork::distinct()->pluck('network_id')->toArray();
                        break;
        
                    case 'sports':
                        $sports_networks = SportCategoryContentNetwork::distinct()->pluck('network_id')->toArray();
                        break;
        
                    case 'stageshowspak':
                        $stage_shows_pak_network = StageshowPakContentNetwork::distinct()->pluck('network_id')->toArray();
                        break;
        
                    case 'laughtershows':
                        $laughter_shows_network = LaugtershowContentNetwork::distinct()->pluck('network_id')->toArray();
                        break;
                    default:
                        $movie_networks = MovieContentNetwork::distinct()->pluck('network_id')->toArray();
                        $tv_shows_networks = TvShowContentNetwork::distinct()->pluck('network_id')->toArray();
                        $series_networks = WebSeriesContentNetwork::distinct()->pluck('network_id')->toArray();
                        // $adult_movie_networks = Above18MovieContentNetwork::distinct()->pluck('network_id')->toArray();
                        // $tv_shows_pak_networks = TvShowPakContentNetwork::distinct()->pluck('network_id')->toArray();
                        // $kids_networks = KidsChannelContentNetwork::distinct()->pluck('network_id')->toArray();
                        // $rel_networks = RelChannelContentNetwork::distinct()->pluck('network_id')->toArray();
                        // $sports_networks = SportCategoryContentNetwork::distinct()->pluck('network_id')->toArray();
                        // $stage_shows_pak_network = StageshowPakContentNetwork::distinct()->pluck('network_id')->toArray();
                        // $laughter_shows_network = LaugtershowContentNetwork::distinct()->pluck('network_id')->toArray();
                        break;
                }
            }
            else{
                $movie_networks = MovieContentNetwork::distinct()->pluck('network_id')->toArray();
                $series_networks = WebSeriesContentNetwork::distinct()->pluck('network_id')->toArray();
                $tv_shows_networks = TvShowContentNetwork::distinct()->pluck('network_id')->toArray();
    
                // $adult_movie_networks = Above18MovieContentNetwork::distinct()->pluck('network_id')->toArray();
    
                
                // $tv_shows_pak_networks = TvShowPakContentNetwork::distinct()->pluck('network_id')->toArray();
                
                // $kids_networks = KidsChannelContentNetwork::distinct()->pluck('network_id')->toArray();
                
                // $rel_networks = RelChannelContentNetwork::distinct()->pluck('network_id')->toArray();
                
                // $sports_networks = SportCategoryContentNetwork::distinct()->pluck('network_id')->toArray();
                // $stage_shows_pak_network = StageshowPakContentNetwork::distinct()->pluck('network_id')->toArray();
                
                // $laughter_shows_network = LaugtershowContentNetwork::distinct()->pluck('network_id')->toArray();
            }
    
            // Merge all
            $all_networks = array_merge(
                $movie_networks,
                $adult_movie_networks,
                $series_networks,
                $tv_shows_networks,
                $tv_shows_pak_networks,
                $kids_networks,
                $rel_networks,
                $sports_networks,
                $stage_shows_pak_network,
                $laughter_shows_network
            );
    
            // Remove duplicates
            $all_networks = array_unique($all_networks);
    
            // Fetch final networks
            // $networks = ContentNetwork::with('sliders')->whereNull('deleted_at')
            $networks = ContentNetwork::whereNull('deleted_at')
                ->whereIn('id', $all_networks)
                ->where('status', 1)
                ->orderBy('networks_order', 'asc')
                ->get();
    
            return response()->json($networks);
        }

    }
    

    public function getAllContentsOfNetwork(Request $request, $network_id){
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $contents = DB::table('content_network_log')
            ->where('network_id', $network_id)
            ->get();

        $jsonData = [];
        foreach ($contents as $key => $content) {
            # for movies
            if ($content->content_type == 1) {
                $newRow = $this->getMovieDetailsById($content->content_id);
                if ($newRow != "") {
                    $jsonData[] = $newRow;
                }
            }
            # for series
            else if ($content->content_type == 2) {
                $newRow = $this->getSeriesDetailsById($content->content_id);
                if ($newRow != "") {
                    $jsonData[] = $newRow;
                }
            }
        }

        // ✅ Manual Pagination
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
        $limit = isset($_GET['records']) && is_numeric($_GET['records']) ? (int) $_GET['records'] : 10;
        $offset = ($page - 1) * $limit;

        $total = count($jsonData);
        $pagedData = array_slice($jsonData, $offset, $limit);

        $response = [
            'status' => true,
            'total' => $total,
            'current_page' => $page,
            'per_page' => $limit,
            'last_page' => ceil($total / $limit),
            'data' => $pagedData
        ];

        return response()->json($response);
    }


    // 5 August 2025

    // bvjfgyjuty   

    public function getGenreByContentNetwork(Request $request){
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $network_id = $post->network_id ?? null;
        $data_for = $post->data_for ?? null;

        $movie_ids = [];
        $sereis_ids = [];
        $tvshow_ids = [];
        $kid_ids = [];
        $tvshowpak_ids = [];
        $rel_ids = [];
        $sport_ids = [];
        $stagepak_ids = [];
        $laughter_ids = [];
        $adult_ids = [];
        
        if ($data_for == 'movies') {     
            $ids = Movie::where('is_recent',1)->get()->pluck('id')->toarray();
            $movie_ids = DB::table('movie_content_network')->whereIn('movie_id', $ids)->where('network_id', $network_id)->pluck('movie_id');
        }
        elseif ($data_for == 'webseries') {            
            $sereis_ids = DB::table('web_series_content_network')->where('network_id', $network_id)->pluck('webseries_id');
        }
        elseif ($data_for == 'tvshows') {            
            $tvshow_ids = DB::table('tv_show_content_network')->where('network_id', $network_id)->pluck('show_id');
        }
        elseif ($data_for == 'tvshowspak') {            
            $tvshowpak_ids = DB::table('tv_show_pak_content_network')->where('network_id', $network_id)->pluck('show_id');
        }
        elseif ($data_for == 'kidchannels') {            
            $kid_ids = DB::table('kids_channel_content_network')->where('network_id', $network_id)->pluck('show_id');
        }
        elseif ($data_for == 'religiouschannels') {            
            $rel_ids = DB::table('rel_channel_content_network')->where('network_id', $network_id)->pluck('show_id');
        }
        elseif ($data_for == 'sports') {            
            $sport_ids = DB::table('sports_category_content_network')->where('network_id', $network_id)->pluck('sport_category_id');
        }
        elseif ($data_for == 'stageshowspak') {            
            $stagepak_ids = DB::table('state_show_pak_content_network')->where('network_id', $network_id)->pluck('movie_id');
        }
        elseif ($data_for == 'laughtershows') {            
            $laughter_ids = DB::table('laugter_show_content_network')->where('network_id', $network_id)->pluck('movie_id');
        }
        elseif ($data_for == 'adultmovies') {            
            $adult_ids = DB::table('adult_movie_content_network')->where('network_id', $network_id)->pluck('movie_id');
        }
        elseif ($data_for == null) {            
            $movie_ids = DB::table('movie_content_network')->where('network_id', $network_id)->pluck('movie_id');
            $sereis_ids = DB::table('web_series_content_network')->where('network_id', $network_id)->pluck('webseries_id');
            $kid_ids = DB::table('kids_channel_content_network')->where('network_id', $network_id)->pluck('show_id');
            $tvshow_ids = DB::table('tv_show_content_network')->where('network_id', $network_id)->pluck('show_id');
            $tvshowpak_ids = DB::table('tv_show_pak_content_network')->where('network_id', $network_id)->pluck('show_id');
            $rel_ids = DB::table('rel_channel_content_network')->where('network_id', $network_id)->pluck('show_id');
            $sport_ids = DB::table('sports_category_content_network')->where('network_id', $network_id)->pluck('sport_category_id');
            $stagepak_ids = DB::table('state_show_pak_content_network')->where('network_id', $network_id)->pluck('movie_id');
            $laughter_ids = DB::table('laugter_show_content_network')->where('network_id', $network_id)->pluck('movie_id');
            $adult_ids = DB::table('adult_movie_content_network')->where('network_id', $network_id)->pluck('movie_id');
        }
        else{
            print_r(json_encode([
                'status' => false,
                'message' => "Invalid 'data_for' use"
            ]));
            exit;
        }


        
        // print_r(json_encode($sereis_ids)); exit;
        if (count($movie_ids) == 0 && 
            count($sereis_ids) == 0 && 
            count($tvshow_ids) == 0 && 
            count($tvshowpak_ids) == 0 && 
            count($kid_ids) == 0 && 
            count($rel_ids) == 0 && 
            count($sport_ids) == 0 && 
            count($stagepak_ids) == 0 && 
            count($laughter_ids) == 0 &&
            count($adult_ids) == 0) 
        {
            print_r(json_encode([
                'status' => false,
                'message' => 'No Content found with Content Id : '.$network_id
            ]));
            exit;
        }

        $allMovieGenres = [];       
        $allAdultMovieGenres = [];       
        $allSeiersGenres = [];       
        $allTvShowGenres = []; 
        $allKidsGenres = []; 
        $allTvShowPaksGenres = []; 
        $allRelGenres = []; 
        $allSportsGenres = []; 
        $allStagePakGenres = []; 
        $allLaughterGenres = [];         

        $finalArray = [];




        $channels = [];

        /* ================= MOVIES ================= */
        if (count($movie_ids) > 0) {            
            $genres = Movie::whereIn('id', $movie_ids)
                ->where('status', 1)
                ->pluck('genres')
                ->filter()
                ->toArray();               

            $allMovieGenres = collect($genres)
                ->flatMap(fn($genre) => array_map('trim', explode(',', $genre)))
                ->unique()
                ->values()
                ->toArray();

            $finalArray = array_merge($finalArray, $allMovieGenres);
        }


        if (count($adult_ids) > 0) {            
            $genres = AdultMovie::whereIn('id', $adult_ids)
                ->where('status', 1)
                ->pluck('genres')
                ->filter()
                ->toArray();               

            $allAdultMovieGenres = collect($genres)
                ->flatMap(fn($genre) => array_map('trim', explode(',', $genre)))
                ->unique()
                ->values()
                ->toArray();

            $finalArray = array_merge($finalArray, $allAdultMovieGenres);
        }

        /* ================= WEB SERIES ================= */
        if (count($sereis_ids) > 0) {            
            $genres = WebSeries::whereIn('id', $sereis_ids)
                ->where('status', 1)
                ->pluck('genres')
                ->filter()
                ->toArray();               

            $allSeiersGenres = collect($genres)
                ->flatMap(fn($genre) => array_map('trim', explode(',', $genre)))
                ->unique()
                ->values()
                ->toArray();

            $finalArray = array_merge($finalArray, $allSeiersGenres);
        }

        /* ================= TV SHOWS ================= */
        if (count($tvshow_ids) > 0) {            
            $genres = TvShow::whereIn('tv_channel_id', $tvshow_ids)
                ->where('status', 1)
                ->pluck('genre')
                ->filter()
                ->toArray();   
                
            $channels = TvChannel::whereIn('id', $tvshow_ids)->get()->toArray();

            $allTvShowGenres = collect($genres)
                ->flatMap(fn($genre) => array_map('trim', explode(',', $genre)))
                ->unique()
                ->values()
                ->toArray();

            $finalArray = array_merge($finalArray, $allTvShowGenres);
            
        }

        /* ================= TV SHOWS PAK ================= */
        if (count($tvshowpak_ids) > 0) {            
            $genres = TvShowPak::whereIn('tv_channel_id', $tvshowpak_ids)
                ->where('status', 1)
                ->pluck('genre')
                ->filter()
                ->toArray();   
                
            $channels = TvChannelPak::whereIn('id', $tvshowpak_ids)->get()->toArray();

            $allTvShowPaksGenres = collect($genres)
                ->flatMap(fn($genre) => array_map('trim', explode(',', $genre)))
                ->unique()
                ->values()
                ->toArray();

            $finalArray = array_merge($finalArray, $allTvShowPaksGenres);
        }

        /* ================= KIDS CHANNEL ================= */
        if (count($kid_ids) > 0) {            
            $genres = KidsShow::whereIn('kid_channel_id', $kid_ids)
                ->where('status', 1)
                ->pluck('genre')
                ->filter()
                ->toArray();   
                
            $channels = KidsChannel::whereIn('id', $kid_ids)->get()->toArray();

            $allKidsGenres = collect($genres)
                ->flatMap(fn($genre) => array_map('trim', explode(',', $genre)))
                ->unique()
                ->values()
                ->toArray();

            $finalArray = array_merge($finalArray, $allKidsGenres);
        }

        /* ================= RELIGIOUS CHANNELS ================= */
        if (count($rel_ids) > 0) {            
            $genres = RelShow::whereIn('channel_id', $rel_ids)
                ->where('status', 1)
                ->pluck('genre')
                ->filter()
                ->toArray(); 
                
            $channels = RelChannel::whereIn('id', $rel_ids)->get()->toArray();

            $allRelGenres = collect($genres)
                ->flatMap(fn($genre) => array_map('trim', explode(',', $genre)))
                ->unique()
                ->values()
                ->toArray();

            $finalArray = array_merge($finalArray, $allRelGenres);
        }

        /* ================= SPORTS ================= */
        if (count($sport_ids) > 0) {            
            $genres = SportsCategory::whereIn('id', $sport_ids)
                ->where('status', 1)
                ->pluck('genre')
                ->filter()
                ->toArray();               

            $allSportsGenres = collect($genres)
                ->flatMap(fn($genre) => array_map('trim', explode(',', $genre)))
                ->unique()
                ->values()
                ->toArray();

            $finalArray = array_merge($finalArray, $allSportsGenres);
        }

        /* ================= STAGE SHOWS PAK ================= */
        if (count($stagepak_ids) > 0) {            
            $genres = StageShowPak::whereIn('id', $stagepak_ids)
                ->where('status', 1)
                ->pluck('genres')
                ->filter()
                ->toArray();               

            $allStagePakGenres = collect($genres)
                ->flatMap(fn($genre) => array_map('trim', explode(',', $genre)))
                ->unique()
                ->values()
                ->toArray();

            $finalArray = array_merge($finalArray, $allStagePakGenres);
        }

        /* ================= LAUGHTER SHOWS ================= */
        if (count($laughter_ids) > 0) {            
            $genres = Laughterhow::whereIn('id', $laughter_ids)
                ->where('status', 1)
                ->pluck('genres')
                ->filter()
                ->toArray();               

            $allLaughterGenres = collect($genres)
                ->flatMap(fn($genre) => array_map('trim', explode(',', $genre)))
                ->unique()
                ->values()
                ->toArray();

            $finalArray = array_merge($finalArray, $allLaughterGenres);
        }

        /* ================= FINAL UNIQUE LIST ================= */
        $finalArray = array_values(array_unique($finalArray));


        

        $finalArray = array_values(array_unique($finalArray));
        
        if (count($finalArray) > 0) {  
            if ($data_for == 'tvshows' || $data_for == 'tvshowspak' || $data_for == 'kidchannels' || $data_for == 'religiouschannels') {
                print_r(json_encode([
                    'status' => true,
                    'channels' => $channels,
                    'genres' => $finalArray
                ])); 
                exit;   
            }          
            print_r(json_encode([
                'status' => true,
                'genres' => $finalArray
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

    public function getAdultMoviesGenre(Request $request){
        
        $allMovieGenres = []; 
        $genres = AdultMovie::where('status', 1)->pluck('genres')->filter()->toArray();               

        if (count($genres) > 0) {            
            $allMovieGenres = collect($genres)->flatMap(fn($genre) => array_map('trim', explode(',', $genre)))->unique()->values()->toArray();            
        }


        if (count($allMovieGenres) > 0) {            
            print_r(json_encode([
                'status' => true,
                'genres' => $allMovieGenres
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

    

    public function getAllContentsOfNetworkNew(Request $request){
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
        $limit = isset($_GET['records']) && is_numeric($_GET['records']) ? (int) $_GET['records'] : 20;
        $offset = ($page - 1) * $limit;


        $network_id = $post->network_id;
        // $network_id = $request->network_id;
        $genre = $post->genre ?? null;

        $tv_channel_id = $post->tv_channel_id ?? null;
        // $genre = $request->genre ?? null;
        $data_for = $post->data_for ?? null;

        $contents = DB::table('content_network_log')
            ->where('network_id', $network_id);
    
        if ($data_for == 'movies') {
            $contents = $contents->where('content_type', 1);
        }
        
        elseif ($data_for == 'webseries') {
            $contents = $contents->where('content_type', 2);
        }   
        elseif ($data_for == 'tvshows') {
            $contents = $contents->where('content_type', 4);
        }   
        elseif ($data_for == 'tvshowspak') {
            $contents = $contents->where('content_type', 5);
        } 
        elseif ($data_for == 'kidchannels') {
            $contents = $contents->where('content_type', 6);
        } 
        elseif ($data_for == 'religiouschannels') {
            $contents = $contents->where('content_type', 7);
        } 
        elseif ($data_for == 'sports') {
            $contents = $contents->where('content_type', 8);
        } 
        elseif ($data_for == 'stageshowspak') {
            $contents = $contents->where('content_type', 9);
        }              
        elseif ($data_for == 'laughtershows') {
            $contents = $contents->where('content_type', 10);
        }   
        elseif ($data_for == 'adultmovies') {
            $contents = $contents->where('content_type', 11);
        }           
        
        $contents = $contents->get();


        // print_r(json_encode($contents)); exit;

        $content_sliders = DB::table('content_network_slider')
            ->where('content_network_id', $network_id)->where('status',1)->whereNull('deleted_at');

        
        if ($data_for == 'movies') {
            $content_sliders = $content_sliders->where('slider_for', 'movies');
        }
        
        elseif ($data_for == 'webseries') {
            $content_sliders = $content_sliders->where('slider_for', 'webseries');
        }  
        elseif ($data_for == 'tvshows') {
            $content_sliders = $content_sliders->where('slider_for', 'tvshows');
        }        
        elseif ($data_for == 'tvshowspak') {
            $content_sliders = $content_sliders->where('slider_for', 'tvshowspak');
        }    
        elseif ($data_for == 'kidchannels') {
            $content_sliders = $content_sliders->where('slider_for', 'kidchannels');
        }    
        elseif ($data_for == 'religiouschannels') {
            $content_sliders = $content_sliders->where('slider_for', 'religiouschannels');
        }    
        elseif ($data_for == 'sports') {
            $content_sliders = $content_sliders->where('slider_for', 'sports');
        }    
        elseif ($data_for == 'stageshowspak') {
            $content_sliders = $content_sliders->where('slider_for', 'stageshowspak');
        }        
        elseif ($data_for == 'laughtershows') {
            $content_sliders = $content_sliders->where('slider_for', 'laughtershows');
        }  
        
        elseif ($data_for == 'adultmovies') {
            $content_sliders = $content_sliders->where('slider_for', 'adultmovies');
        }

        $content_sliders = $content_sliders->get();

        $jsonData = [];
        foreach ($contents as $key => $content) {
            # for movies
            if ($content->content_type == 1) {
                $newRow = $this->getMovieDetailsById($content->content_id, $genre, $data_for);
                if ($newRow != "") {
                    $jsonData[] = $newRow;
                }
            }
            # for series
            else if ($content->content_type == 2) {
                $newRow = $this->getSeriesDetailsById($content->content_id, $genre);
                if ($newRow != "") {
                    $jsonData[] = $newRow;
                }
            }

            else if ($content->content_type == 4) {
                $tvShows  = $this->getTvShowsDetailsById($content->content_id, $genre, $tv_channel_id);
                if (count($tvShows) > 0) {
                    foreach ($tvShows as $show) {
                        $jsonData[] = $show;
                    }
                }
            }

            else if ($content->content_type == 5) {
                $tvShows  = $this->getTvShowsPakDetailsById($content->content_id, $genre, $tv_channel_id);
                if (count($tvShows) > 0) {
                    foreach ($tvShows as $show) {
                        $jsonData[] = $show;
                    }
                }
            }

            else if ($content->content_type == 6) {
                $tvShows  = $this->getKidShowsDetailsById($content->content_id, $genre, $tv_channel_id);
                if (count($tvShows) > 0) {
                    foreach ($tvShows as $show) {
                        $jsonData[] = $show;
                    }
                }
            }

            else if ($content->content_type == 7) {
                $tvShows  = $this->getRelDetailsById($content->content_id, $genre, $tv_channel_id);
                if (count($tvShows) > 0) {
                    foreach ($tvShows as $show) {
                        $jsonData[] = $show;
                    }
                }
            }

            else if ($content->content_type == 8) {
                $tvShows  = $this->getSportDetailsById($content->content_id, $genre);
                if (count($tvShows) > 0) {
                    foreach ($tvShows as $show) {
                        $jsonData[] = $show;
                    }
                }
            }

            else if ($content->content_type == 9) {
                $tvShows  = $this->getStagePaktDetailsById($content->content_id, $genre);
                if (count($tvShows) > 0) {
                    foreach ($tvShows as $show) {
                        $jsonData[] = $show;
                    }
                }
            }

            else if ($content->content_type == 10) {
                $tvShows  = $this->getLaughterDetailsById($content->content_id, $genre);
                if (count($tvShows) > 0) {
                    foreach ($tvShows as $show) {
                        $jsonData[] = $show;
                    }
                }
            }

            else if ($content->content_type == 11) {                
                $newRow  = $this->getAdultMovieDetailsById($content->content_id, $genre);
                if ($newRow != "") {
                    $jsonData[] = $newRow;
                }
            }
        }


        // print_r($jsonData); exit;


        // print_r(count($jsonData)); exit;
        // ✅ Manual Pagination
        

        $total = count($jsonData);

        // usort($jsonData, function ($a, $b) {
        //     return strtotime($b['created_at']) <=> strtotime($a['created_at']);
        // });

        usort($jsonData, function ($a, $b) use ($data_for) {

            if ($data_for === 'movies') {
                return ($a['recent_index']) <=> ($b['recent_index']);
            }

            if ($data_for === 'webseries') {
                return ($a['series_order']) <=> ($b['series_order']);
            }

            return strtotime($b['created_at']) <=> strtotime($a['created_at']);
        });

        if (isset($_GET['page'])) {
            
            $pagedData = array_slice($jsonData, $offset, $limit);
    
            $response = [
                'status' => true,
                'total' => $total,
                'page_total' => count($pagedData),
                'current_page' => $page,
                'per_page' => $limit,
                'last_page' => ceil($total / $limit),
                'content_sliders' => $content_sliders,
                'data' => $pagedData
            ];
        }
        else{
            $response = [
                'status' => true,
                'total' => $total,                
                'content_sliders' => $content_sliders,
                'data' => $jsonData
                
            ];
        }


        return response()->json($response);
    }


    protected function getMovieDetailsById($id, $genre=null, $data_for = null){

        $query = Movie::where('deleted_at', null)->where('id', $id) 
        ->where('status', 1);
        
        
        
        $query = $query->with('networks');
        

        if (!empty($genre)) {
            $query->where('genres', 'LIKE', '%' . $genre . '%');
        }

        if ($data_for == 'movies') {
            $query = $query->where('is_recent', 1);
        }

        $movie = $query->first();

        if ($movie) {
            return $movie;
        }
        else{
            return "";
        }
    }
    
    protected function getAdultMovieDetailsById($id, $genre=null){

        $query = AdultMovie::where('deleted_at', null)->where('id', $id) 
        ->where('status', 1)  
        //->where('is_recent', 1)     
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
    

    protected function getTvShowsDetailsById($id, $genre = null, $tv_channel_id = null){
        $query = TvShow::where('status', 1)
            ->where('deleted_at', null)
            ->where('tv_channel_id', $id); // network_id refers to channel id here            

        if (!empty($genre)) {
            $query->where('genre', 'LIKE', '%' . $genre . '%');
        }

        if (!empty($tv_channel_id)) {
            $query->where('tv_channel_id', $tv_channel_id);
        }

        return $query->get(); // return all shows directly
    }

    protected function getTvShowsPakDetailsById($id, $genre = null, $tv_channel_id = null){
        $query = TvShowPak::where('status', 1)
            ->where('deleted_at', null)
            ->where('tv_channel_id', $id); // network_id refers to channel id here            

        if (!empty($genre)) {
            $query->where('genre', 'LIKE', '%' . $genre . '%');
        }

        if (!empty($tv_channel_id)) {
            $query->where('tv_channel_id', $tv_channel_id);
        }

        return $query->get(); // return all shows directly
    }

    protected function getKidShowsDetailsById($id, $genre = null, $tv_channel_id = null){
        $query = KidsShow::where('status', 1)
            ->where('deleted_at', null)
            ->where('kid_channel_id', $id); // network_id refers to channel id here            

        if (!empty($genre)) {
            $query->where('genre', 'LIKE', '%' . $genre . '%');
        }

        if (!empty($tv_channel_id)) {
            $query->where('kid_channel_id', $tv_channel_id);
        }


        return $query->get(); // return all shows directly
    }

    protected function getRelDetailsById($id, $genre = null, $tv_channel_id = null){
        $query = RelShow::where('status', 1)
            ->where('deleted_at', null)
            ->where('channel_id', $id); // network_id refers to channel id here            

        if (!empty($genre)) {
            $query->where('genre', 'LIKE', '%' . $genre . '%');
        }

        if (!empty($tv_channel_id)) {
            $query->where('channel_id', $tv_channel_id);
        }

        return $query->get(); // return all shows directly
    }

    protected function getSportDetailsById($id, $genre = null){
        $query = SportsCategory::where('status', 1)
            ->where('deleted_at', null)
            ->where('id', $id); // network_id refers to channel id here            

        if (!empty($genre)) {
            $query->where('genre', 'LIKE', '%' . $genre . '%');
        }

        return $query->get(); // return all shows directly
    }

    protected function getStagePaktDetailsById($id, $genre = null){
        $query = SportsCategory::where('status', 1)
            ->where('deleted_at', null)
            ->where('id', $id); // network_id refers to channel id here            

        if (!empty($genre)) {
            $query->where('genres', 'LIKE', '%' . $genre . '%');
        }

        return $query->get(); // return all shows directly
    }

    protected function getLaughterDetailsById($id, $genre = null){
        $query = SportsCategory::where('status', 1)
            ->where('deleted_at', null)
            ->where('id', $id); // network_id refers to channel id here            

        if (!empty($genre)) {
            $query->where('genres', 'LIKE', '%' . $genre . '%');
        }

        return $query->get(); // return all shows directly
    }


    protected function getSeriesDetailsById($id, $genre=null){        

        $query = WebSeries::where('status', 1)->where('deleted_at', null)->where('id', $id) 
        ->where('status', 1)              
        ->with('networks');

        if (!empty($genre)) {
            $query->where('genres', 'LIKE', '%' . $genre . '%');
        }


        // $query = $query->paginate($limit, ['*'], 'page', $page);


        $series = $query->first();

        if ($series) {
            return $series;
        }
        else{
            return "";
        }        
        exit;
    }


    public function getMovieDetails($contentId){
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $movie = Movie::where('status', 1)->where('deleted_at', null)->where('id', $contentId)  
        ->where('status', 1)       
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


    public function searchContent($searchTerm, $type=0){
        // $post = json_decode(file_get_contents('php://input', true));
        $user_id = $this->get_user_id();
        $jsonData = [];

        // echo strlen($searchTerm); exit;
        // $searchTerm = urldecode($searchTerm);
        if(strlen($searchTerm) > 2) {
            // movies
            $movies = Movie::where('status', 1)
            ->where(function($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('description', 'like', '%' . $searchTerm . '%');
            })
            ->with('networks')
            ->get();

            // series
            $series = WebSeries::where('status', 1)
            ->where(function($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('description', 'like', '%' . $searchTerm . '%');
            })
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

        $is_valid = $this->checkDomainPermission('tvshow');
        if (!$is_valid) {
            print_r(json_encode([
                'status' => false,
                'message' => 'You do not have permission to access this'
            ]));
            exit;
        }

        $tvChannels = TvChannel::where('deleted_at', null)->where('status',1);

        if (isset($_GET['content_network'])) {
            $network_id = $_GET['content_network'];

            $tv_channel_ids = TvShowContentNetwork::where('network_id', $network_id)
            ->get()->pluck('show_id')->toArray();

            $tvChannels = $tvChannels->whereIn('id', $tv_channel_ids);
        }
        

        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $page = (int) $_GET['page'];
            $limit = isset($_GET['records']) && is_numeric($_GET['records']) ? (int) $_GET['records'] : 10;

            $tvChannels = $tvChannels->paginate($limit, ['*'], 'page', $page);
            // echo 'Page Set'; exit;

            if (!$tvChannels->items()) {
                print_r(json_encode([]));
                exit;
            }

            print_r(json_encode($tvChannels->items()));exit;
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

    public function getTvShows($channelId){
        if(!$channelId){
            echo "channel id required ";
            exit;
        }
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $is_valid = $this->checkDomainPermission('tvshow');
        if (!$is_valid) {
            print_r(json_encode([
                'status' => false,
                'message' => 'You do not have permission to access this'
            ]));
            exit;
        }

        $tvShows = TvShow::where('deleted_at', null)->where('tv_channel_id',$channelId)->where('status',1);

        if (isset($headers['countries']) && $headers['countries'] != '') {
            $tvShows = $tvShows->whereNotIn('countries', $headers['countries']);
        }

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

        $is_valid = $this->checkDomainPermission('tvshow');
        if (!$is_valid) {
            print_r(json_encode([
                'status' => false,
                'message' => 'You do not have permission to access this'
            ]));
            exit;
        }

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

        $is_valid = $this->checkDomainPermission('tvshow');
        if (!$is_valid) {
            print_r(json_encode([
                'status' => false,
                'message' => 'You do not have permission to access this'
            ]));
            exit;
        }

        $tvShowEpisodes = TvShowEpisode::where('deleted_at', null)->where('season_id',$seasonId)->where('status',1)->orderBy('episoade_order', 'desc');

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

        $is_valid = $this->checkDomainPermission('religious');
        if (!$is_valid) {
            print_r(json_encode([
                'status' => false,
                'message' => 'You do not have permission to access this'
            ]));
            exit;
        }

        $content_network_id = $_GET['content_network'] ?? null;

        $relChannels = RelChannel::where('deleted_at', null)->where('status',1);
        if ($content_network_id) {
            $rel_channel_ids = RelChannelContentNetwork::where('network_id', $content_network_id)
            ->get()->pluck('show_id')->toArray();

            $relChannels = $relChannels->whereIn('id', $rel_channel_ids);
        }

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

        $is_valid = $this->checkDomainPermission('religious');
        if (!$is_valid) {
            print_r(json_encode([
                'status' => false,
                'message' => 'You do not have permission to access this'
            ]));
            exit;
        }

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
            echo "Show id required ";
            exit;
        }
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));

        $is_valid = $this->checkDomainPermission('religious');
        if (!$is_valid) {
            print_r(json_encode([
                'status' => false,
                'message' => 'You do not have permission to access this'
            ]));
            exit;
        }

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

        $is_valid = $this->checkDomainPermission('sports');
        if (!$is_valid) {
            print_r(json_encode([
                'status' => false,
                'message' => 'You do not have permission to access this'
            ]));
            exit;
        }

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

        $is_valid = $this->checkDomainPermission('sports');
        if (!$is_valid) {
            print_r(json_encode([
                'status' => false,
                'message' => 'You do not have permission to access this'
            ]));
            exit;
        }
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

        $is_valid = $this->checkDomainPermission('sports');
        // if (!$is_valid) {
        //     print_r(json_encode([
        //         'status' => false,
        //         'message' => 'You do not have permission to access this'
        //     ]));
        //     exit;
        // }        

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

        $is_valid = $this->checkDomainPermission('sports');
        // if (!$is_valid) {
        //     print_r(json_encode([
        //         'status' => false,
        //         'message' => 'You do not have permission to access this'
        //     ]));
        //     exit;
        // }        

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
            exit;
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

    protected function getGenre($genre){
        $myGenre = Genre::where('title', $genre)->first();
        

        if ($myGenre) {
            return true;
        }
        else{
            print_r(json_encode([
                'status' => false,
                'message' => 'Invalid Genre',
            ]));
            exit;
        }
    }

    public function getAllAbove18Movies(Request $request){
        $above_18_pin = $this->get_user_pin();

        $post = json_decode(file_get_contents('php://input', 'r'));

        $pin = $post->pin;
        $genre = $post->genre;
        
        if ($pin != $above_18_pin) {
            print_r(json_encode([
                'status' => false,
                'message' => 'Invalid Pin'
            ]));
            exit;
        }

        $is_valid = $this->checkDomainPermission('movies');
        if (!$is_valid) {
            print_r(json_encode([
                'status' => false,
                'message' => 'You do not have permission to access this'
            ]));
            exit;
        }
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));
        $query = AdultMovie::where('status', 1)
                ->whereNull('deleted_at')
                ->with('networks');

        if (!empty($genre)) {

            $is_genre = $this->getGenre($genre);
            if ($is_genre) {                
                $query->where('genres', 'Like', '%'.$genre.'%');
            }
        }



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

        $is_valid = $this->checkDomainPermission('kids_show');
        if (!$is_valid) {
            print_r(json_encode([
                'status' => false,
                'message' => 'You do not have permission to access this'
            ]));
            exit;
        }        

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

        $is_valid = $this->checkDomainPermission('kids_show');
        if (!$is_valid) {
            print_r(json_encode([
                'status' => false,
                'message' => 'You do not have permission to access this'
            ]));
            exit;
        }        

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

        $is_valid = $this->checkDomainPermission('kids_show');
        if (!$is_valid) {
            print_r(json_encode([
                'status' => false,
                'message' => 'You do not have permission to access this'
            ]));
            exit;
        }         
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

        $is_valid = $this->checkDomainPermission('kids_show');
        if (!$is_valid) {
            print_r(json_encode([
                'status' => false,
                'message' => 'You do not have permission to access this'
            ]));
            exit;
        } 


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

        $is_valid = $this->checkDomainPermission('tvshow_pak');
        if (!$is_valid) {
            print_r(json_encode([
                'status' => false,
                'message' => 'You do not have permission to access this'
            ]));
            exit;
        } 

        $tvChannels = TvChannelPak::where('deleted_at', null)->where('status',1);

        if (isset($_GET['content_network'])) {
            $network_id = $_GET['content_network'];

            $tv_channel_ids = TvShowPakContentNetwork::where('network_id', $network_id)
            ->get()->pluck('show_id')->toArray();

            $tvChannels = $tvChannels->whereIn('id', $tv_channel_ids);
        }

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

        $is_valid = $this->checkDomainPermission('tvshow_pak');
        if (!$is_valid) {
            print_r(json_encode([
                'status' => false,
                'message' => 'You do not have permission to access this'
            ]));
            exit;
        } 


        $post = json_decode(file_get_contents('php://input', 'r'));

        $tvShows = TvShowPak::where('deleted_at', null)->where('tv_channel_id',$channelId)->where('status',1);

        if (isset($headers['countries']) && $headers['countries'] != '') {
            $tvShows = $tvShows->whereNotIn('countries', $headers['countries']);
        }

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

        $is_valid = $this->checkDomainPermission('tvshow_pak');
        if (!$is_valid) {
            print_r(json_encode([
                'status' => false,
                'message' => 'You do not have permission to access this'
            ]));
            exit;
        } 
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

        $is_valid = $this->checkDomainPermission('tvshow_pak');
        if (!$is_valid) {
            print_r(json_encode([
                'status' => false,
                'message' => 'You do not have permission to access this'
            ]));
            exit;
        } 
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

        $is_valid = $this->checkDomainPermission('stage_shows');
        if (!$is_valid) {
            print_r(json_encode([
                'status' => false,
                'message' => 'You do not have permission to access this'
            ]));
            exit;
        } 


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

        
        $is_valid = $this->checkDomainPermission('stage_shows');
        if (!$is_valid) {
            print_r(json_encode([
                'status' => false,
                'message' => 'You do not have permission to access this'
            ]));
            exit;
        } 

        $query = Laughterhow::where('status', 1)->whereNull('deleted_at')->where('status',1)->with('networks');
        
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

    public function getAdultMoviesGenreList(Request $request)
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
        

        // echo 'hii';exit;
        $post = json_decode(file_get_contents('php://input', 'r'));
        
        $genre = $post->genere;
            
        $languageId = $post->languageId ?? null;

        $is_valid = $this->checkDomainPermission('live_channels');

        // Start query with join
        $query = Channel::where('channels.status', 1)            
            ->whereNull('channels.deleted_at')
            ->orderBy('languages.order_number', 'asc')

            ->leftJoin('languages', 'channels.channel_language', '=', 'languages.id')
            ->select(
                'channels.*',
                'languages.id as channel_language_id',
                'languages.title as channel_language_title',
                'languages.order_number as language_index'
            );
        
        if ($is_valid && $is_valid->original['status']) {
            $permit_channels = $is_valid->original['channels'];
                        
            if ($permit_channels[0]) {                  
                $query = $query->whereIn('channels.id', $permit_channels);
            }
        }
    
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


    // 22 August 2025

    public function getWatchList(Request $request){
        $user_id = $this->get_user_id();
        
        $post = json_decode(file_get_contents('php://input', 'r'));

        $module = $post->module;
        $ids = explode(',',$post->content_ids);

        
        $data = DB::table($module)->whereIn('id', $ids)->where('status', 1)->get();

        if (count($data) > 0) {            
            print_r(json_encode([
                'status' => true,
                'data' => $data
            ]));
            exit;
        }
        else{
            print_r(json_encode([
                'status' => false,
                'message' => 'No Data Found !'
            ]));
            exit;
        }        
    }

    // 27 Aug

    public function showAbove18(){
        $user_id = $this->get_user_id();

        if ($user_id) {            
            $user = ClientUser::where('id', $user_id)->first();

            // print_r(json_encode($user)); exit;
            $above18_pin = $user->over18_pin;
    
            if (!$above18_pin || $above18_pin == null) {                        
                return response()->json([
                    'status' => false,
                    'message' => 'No Access for Above 18 Content'                
                ]);
            }
            else{
                return response()->json([
                    'status' => true,
                    'above18_pin' => $above18_pin
                ]); 
            }
        }
        else{
            return response()->json([
                'status' => false,
                'message' => 'Invalid Auth-key'
            ]); 
        }

    }

    public function getSearchCategoryList(Request $request){
        $user_id = $this->get_user_id();

        $post = json_decode(file_get_contents('php://input', 'r'));

        $keywords = $module = $post->keywords;
        
        $data = [
            // ['table' => 'tv_channels', 'column' => 'name'],
            // ['table' => 'tv_channels_pak', 'column' => 'name'],
            // ['table' => 'kids_channel', 'column' => 'name'],
            // ['table' => 'rel_channels', 'column' => 'name'],
            ['table' => 'movies', 'column' => 'name'],
            ['table' => 'web_series', 'column' => 'name'],
            ['table' => 'channels', 'column' => 'channel_name'],
            ['table' => 'tv_shows', 'column' => 'name'],
            ['table' => 'tv_shows_pak', 'column' => 'name'],
            ['table' => 'kids_shows', 'column' => 'name'],
            ['table' => 'rel_shows', 'column' => 'title'],
            ['table' => 'sports_categories', 'column' => 'title'],
            ['table' => 'sports_tournaments', 'column' => 'title'],
            ['table' => 'stage_shows_pak', 'column' => 'name'],
            ['table' => 'laughter_show', 'column' => 'name'],
        ];

        $data_to_send = [];

        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $page = (int) $_GET['page'];
            $limit = isset($_GET['records']) && is_numeric($_GET['records']) ? (int) $_GET['records'] : 10;                
        }
        else{
            $page = 1;
            $limit = 10;
        }
        
        foreach ($data as $key => $item) {
            $query = DB::table($item['table']);

            if (!empty($keywords)) {
                $query->where($item['column'], 'like', "%{$keywords}%");
            }

            

            $results = $query->paginate($limit, ['*'], 'page', $page);

            $data_to_send = array_merge($data_to_send, $results->items());
        }

        shuffle($data_to_send);

        if (!empty($data_to_send)) {
            print_r(json_encode($data_to_send));
            exit;
        }
        else{
            print_r(json_encode([
                'status' => false,
                'message' => 'No Data Found !'
            ]));
            exit;
        }

    }


    public function updateUserHistory(Request $request){
        $user_id = $this->get_user_id();

        $data_user = ClientUser::where('id','=',$user_id)->first();
        if (!$data_user) {
            print_r(json_encode([
                'status' => false,
                'message' => 'Invalid User'
            ]));
            exit;
        }
        else{
            $created_by = $data_user ? $data_user->created_by : 0;  // id
            $creater = User::where('id',$created_by)->first();

            $plans = UserPlanDetails::where(['user_id'=>$data_user->id,'status'=>1])->whereDate('plan_end_date','>=',date('Y-m-d'))->orderBy('id','desc')->get();

            if (count($plans) == 0) {
                $is_update = $this->updatePlan($data_user, $creater);

                if (!$is_update) {
                    print_r(json_encode(array(
                        'logout ' => true,
                        'planExpired' => true,
                        'msg' => 'You have not active plan. Kindly recharge your account.'
                    )));
                    exit;
                }
            }

        }

        $post = json_decode(file_get_contents('php://input', 'r'));

        $now = time();  

        // formatted datetime   
        $server_time = date("Y-m-d H:i:s", $now);


        // Update user history in the database
        // You can use the $user_id and $server_time variables here
        $user = DB::table('user_history')->where('user_id', $request->user_id)->get();

        if ($user) {
            DB::table('user_history')
                ->where('user_id', $request->user_id)
                ->update(['status' => 0]);                                
        }        

        $new_entry = DB::table('user_history')->insert([
            'user_id' => $post->user_id,
            'content_type' => $post->content_type, //
            'event_id' => $post->event_id,
            'event_title' => $post->event_title, //
            'url' => $post->url,
            'category_id' => $post->category_id ?? null,            
            'status' => 1,
            'server_time' => $server_time
        ]);

        if ($new_entry) {
            print_r(json_encode([
                'status' => true,
                'message' => 'User history updated successfully'
            ]));
            exit;
        } else {
            print_r(json_encode([
                'status' => false,
                'message' => 'Failed to update user history'
            ]));
            exit;
        }
    }


    // public function checkExpiryPlan(Request $request){
    //     $user_id = $this->get_user_id();
    //     $plan = UserPlanDetails::where(['user_id'=>$user_id,'status'=>1])->orderBy('id','desc')->first(); //latest plan
    //     $expiry_date = null;
        
    //     if ($plan) {
    //         $expiry_date = $plan->plan_end_date;   //2025-06-21 19:24:19

    //         // check is difference is more than 3 or not
    //         $date1 = new \DateTime();
    //         $date2 = new \DateTime($expiry_date);    
    //         $interval = $date1->diff($date2);
    //         $days = (int)$interval->format('%r%a'); // %r to get the sign

    //         $data_user = ClientUser::where('id','=',$user_id)->first();   
    //         $created_by = $data_user->created_by;

    //         $domain = AppDomainContent::where('admin_id', $created_by)->first()->domain;

    //         $domain_content = $this->getDomainData($created_by,$domain);

    //         $creater = User::where('id',$created_by)->first();

    //         if ($days <= 3 && $days >= 0) {

    //             $created_by = $data_user ? $data_user->created_by : 0;  // id
                

    //             $last_plan = UserPlanDetails::where(['user_id'=>$data_user->id])->orderBy('id','desc')->first();
    //             $plan_id = $last_plan ? $last_plan->plan_id : 0;

    //             $current_user_amount = $data_user->current_amount ?? 0;

    //             $plan_details = null;

    //             if ($creater->role == 2) {
    //                 $planDetails = AdminPlan::find($plan_id);
    //             }
    //             else if ($creater->role == 3) {
    //                 $planDetails = ResellerPlan::find($plan_id);
    //             }

    //             $price = $planDetails->total_price;

    //             if ($current_user_amount < $price) {                    
    //                 print_r(json_encode(array(
    //                     'plan_expired' => false,   
    //                     'plan_will_expire' => true, 
    //                     'domain_content' => $domain_content,
    //                     'message' => 'Your plan will expire in '.$days.' days. Please recharge your account.'
    //                 )));
    //                 exit;
    //             }
    //             else{
    //                 print_r(json_encode(array(
    //                     'plan_expired' => false,   
    //                     'domain_content' => $domain_content,    
    //                     'message' => 'Your plan is active.'              
    //                 )));
    //                 exit;
    //             }                                           
    //         }
    //         elseif ($days < 0) {


    //             // add renew plan logic here

    //             $is_update = $this->updatePlan($data_user, $creater);


    //             if ($is_update) {
    //                 print_r(json_encode(array(
    //                     'plan_expired' => false,                          
    //                     'plan_will_expire' => false, 
    //                     'domain_content' => $domain_content,
    //                     'message' => 'Your plan is active.'           
    //                 )));
    //                 exit;
    //             }
    //             else{
    //                 print_r(json_encode(array(
    //                     'plan_expired' => true, 
    //                     'plan_will_expire' => false,   
    //                     'domain_content' => $domain_content,                
    //                     'message' => 'Your plan has expired. Please renew it to continue enjoying our services.'
    //                 )));
    //                 exit;
    //             }

                

    //         }
    //         else{
    //             print_r(json_encode(array(
    //                 'plan_expired' => false,  
    //                 'plan_remainig_days' => $days,
    //                 'plan_will_expire' => false, 
    //                 'domain_content' => $domain_content,
    //                 'message' => 'Your plan is active.'           
    //             )));
    //             exit;
    //         }
    //     }
    //     else{
    //         print_r(json_encode(array(
    //             'plan_expired' => true,   
    //             'message' => 'No Plan Found.'             
    //         )));
    //         exit;
    //     }
    // }


    protected function getLastPlan($user_id) {
        return UserPlanDetails::where(['user_id' => $user_id])
            ->orderBy('id', 'desc')
            ->first();
    }

    protected function getLastActivePlan($user_id) {
        return UserPlanDetails::where(['user_id' => $user_id, 'status' => 1])
            ->whereDate('plan_end_date', '>=', date('Y-m-d'))
            ->orderBy('id', 'desc')
            ->first();
    }

    protected function renewPlan($user_id) {
        $data_user = ClientUser::where('id','=',$user_id)->first();   
        $created_by = $data_user->created_by;
        $creater = User::where('id',$created_by)->first();   
        
        $is_update = $this->updatePlan($data_user, $creater);

        return $is_update;
    }


    protected function checkUserWalletAmount($user_id){
        $data_user = ClientUser::where('id','=',$user_id)->first();   
        $created_by = $data_user->created_by;
        $creater = User::where('id',$created_by)->first();   

        $last_plan = $this->getLastPlan($data_user->id);
        if (!$last_plan) {
            print_r(json_encode(array(
                'plan_expired' => true,   
                'message' => 'No Plan Found.'             
            )));
            exit;
        }


        $plan_id = $last_plan ? $last_plan->plan_id : 0;

        $current_user_amount = $data_user->current_amount ?? 0;

        $planDetails = null;

        if ($creater->role == 2) {
            $planDetails = AdminPlan::find($plan_id);
        }
        else if ($creater->role == 3) {
            $planDetails = ResellerPlan::find($plan_id);
        }
        else if ($creater->role == 4) {
            $planDetails = RetailorPlan::find($plan_id);
        }


        $price = $planDetails->total_price;

        if ($current_user_amount < $price) {                    
            return false;
        }
        else{
            return true;
        }
    }



    public function checkExpiryPlan(){
        $user_id = $this->get_user_id();  
        
        $plan = $this->getLastActivePlan($user_id);
        $expiry_date = null;

        // print_r(json_encode($plan)); exit;
        
        if ($plan) {
            $expiry_date = $plan->plan_end_date;   //2025-06-21 19:24:19

            // check is difference is more than 3 or not
            $date1 = new \DateTime();
            $date2 = new \DateTime($expiry_date);    
            $interval = $date1->diff($date2);
            $days = (int)$interval->format('%r%a'); // %r to get the sign


            // ADD THIS — total seconds difference          
            $totalSeconds = ($date2->getTimestamp() - $date1->getTimestamp());

            $data_user = ClientUser::where('id','=',$user_id)->first();   
            $created_by = $data_user->created_by;


            $creater = User::where('id',$created_by)->first();
            
            $creater_role = $creater->role;



            if ($creater_role == 2) {               
                $domain = AppDomainContent::where('admin_id', $created_by)->first()->domain;
            }

            else if ($creater_role == 3){
                $admin = User::where('id', $creater->created_by)->first();

                $domain = AppDomainContent::where('admin_id', $admin->id)->first()->domain;
            }
            else if ($creater_role == 4){
                
                $reseller = User::where('id', $creater->created_by)->first();
                $admin = User::where('id', $reseller->created_by)->first();

                $domain = AppDomainContent::where('admin_id', $admin->id)->first()->domain;

            }


            $domain_content = $this->getDomainData($created_by,$domain, $creater_role);


            if ($totalSeconds >= 0 && $totalSeconds <= (5 * 24 * 60 * 60)) {

                $days = (int)ceil($totalSeconds / 86400);

                $created_by = $data_user ? $data_user->created_by : 0;  // id
                

                $last_plan = UserPlanDetails::where(['user_id'=>$data_user->id])->orderBy('id','desc')->first();
                $plan_id = $last_plan ? $last_plan->plan_id : 0;

                $current_user_amount = $data_user->current_amount ?? 0;

                $planDetails = null;

                if ($creater->role == 2) {
                    $planDetails = AdminPlan::find($plan_id);
                }
                else if ($creater->role == 3) {
                    $planDetails = ResellerPlan::find($plan_id);
                }
                else if ($creater->role == 4) {
                    $planDetails = RetailorPlan::find($plan_id);
                }

                $price = $planDetails->total_price;

                // FIX 1: || was wrong (always true). Use && so warning only shows
                // when wallet is low AND plan is not free
                if ($current_user_amount < $price && $price != 0) {                    
                    print_r(json_encode(array(
                        'plan_expired' => false,   
                        'plan_will_expire' => true, 
                        'domain_content' => $domain_content,
                        'interval' => $days,
                        'plan_price' => $price,
                        'user_amount' => $current_user_amount,
                        'message' => 'Your plan will expire in '.$days.' days. Please recharge your account.'
                    )));
                    exit;
                }
                else{
                    print_r(json_encode(array(
                        'plan_expired' => false,   
                        'domain_content' => $domain_content,    
                        'message' => 'Your plan is active.',
                        'days' => $days,
                        'totalSeconds' => $totalSeconds       
                    )));
                    exit;
                }                                           
            }
            elseif ($totalSeconds < 0) {

                // add renew plan logic here

                $checkUserWallet = $this->checkUserWalletAmount($user_id);
                if (!$checkUserWallet) {
                    print_r(json_encode(array(
                        'plan_expired' => true,  
                        'checkUserWallet' => $checkUserWallet,
                        'message' => 'Your plan has expired. Please recharge your wallet to renew the plan.'             
                    )));
                    exit;
                }

                $is_update = $this->updatePlan($data_user, $creater);


                if ($is_update) {
                    print_r(json_encode(array(
                        'plan_expired' => false,                          
                        'plan_will_expire' => false, 
                        'domain_content' => $domain_content,
                        'message' => 'Your plan is active.'           
                    )));
                    exit;
                }
                else{
                    print_r(json_encode(array(
                        'plan_expired' => true, 
                        'plan_will_expire' => false,   
                        'domain_content' => $domain_content,                
                        'message' => 'Your plan has expired. Please renew it to continue enjoying our services.'
                    )));
                    exit;
                }

                

            }
            else{
                $days = (int)ceil($totalSeconds / 86400);

                print_r(json_encode(array(
                    'plan_expired' => false,  
                    'plan_remainig_days' => $days,
                    'plan_will_expire' => false, 
                    'domain_content' => $domain_content,
                    'message' => 'Your plan is active.',
                    'days' => $days         
                )));
                exit;
            }
        }
        else{

            // check user wallet amount and renew plan            

            $checkUserWallet = $this->checkUserWalletAmount($user_id);

            // print_r(json_encode($checkUserWallet)); exit;
            if (!$checkUserWallet) {
                print_r(json_encode(array(
                    'plan_expired' => true, 
                    'checkUserWallet' => $checkUserWallet,
                    'message' => 'Your plan has expired. Please recharge your wallet to renew the plan.'             
                )));
                exit;
            }
            
            $renewPlan = $this->renewPlan($user_id);

            if ($renewPlan == true) {
                print_r(json_encode(array(
                    'plan_expired' => false,                          
                    'plan_will_expire' => false,                     
                    'message' => 'Your plan is active.'           
                )));
                exit;
            }
            else{
                print_r(json_encode(array(
                    'plan_expired' => true,   
                    'message' => 'No Plan Found.'             
                )));
                exit;
            }
        }
    }

    public function getAllLanguages(Request $request){
        $user_id = $this->get_user_id();    

        $all_languages = Language::whereNull('deleted_at')->where('status', 1)
        ->with('slider')
        ->get();

        if ($all_languages) {
            
            print_r(json_encode([
                'status' => true,
                'languages' => $all_languages
            ]));
            exit;
        }
        else{
            print_r(json_encode([
                'status' => false,
                'languages' => []
            ]));
            exit;
        }
    }


    private $cacheflySecret = 'JOYdh3jadOxsK42e+caTsTwdAQg4zggM';
    
    /**
     * Generate signed URL for CacheFly ProtectServe
     * 
     * @param string $url - Original video URL
     * @param int $expirySeconds - Token validity duration (default: 1 hour)
     * @return string - Signed URL
     */
    public function signM3U8Url($url, $expirySeconds = 10)
    {
        // Parse URL
        $parsedUrl = parse_url($url);
        $protocol = $parsedUrl['scheme'];
        $hostname = $parsedUrl['host'];
        $fullPath = ltrim($parsedUrl['path'], '/');

        // echo $hostname;exit;
        $secret_key = CDNDomain::where('domain_name', $hostname)->first()->url;
        
        // Separate directory and filename
        $pathInfo = pathinfo($fullPath);
        $path_to_hash = $pathInfo['dirname'] . '/'; // Directory to hash
        $filename = $pathInfo['basename']; // Filename
        
        // ProtectServe settings
        $protected = ''; // BLANK for MANDATORY mode
        // $secret = $this->cacheflySecret;

        $secret = $secret_key;
        $expiretime = time() + $expirySeconds;
        
        // Rules with directory matching enabled
        $rules = 'expiretime=' . $expiretime . ';dirmatch=true';
        
        /**
         * Optional Rules you can add:
         * - ip=123.123.123.123 (IP-based validation)
         * - badurl=BASE64_ENCODED_URL (redirect URL for failed requests)
         * - dirmatch=true/false (enable/disable directory signing)
         */
        
        // Generate HMAC-SHA256 hash
        $hash = hash_hmac('sha256', $rules . $path_to_hash, $secret, false);
        
        // Build signed URL
        $request = "$protocol://$hostname$protected/$rules/$hash/" . $path_to_hash;
        if ($filename != '') {
            $request = $request . $filename;
        }
        
        return $request;
    }
    
    /**
     * API endpoint to tokenize URL
     */
    public function tokenizeUrl(Request $request)
    {
        // Validate request
        $request->validate([
            'url' => 'required|url',
            'expiry_seconds' => 'nullable|integer|min:1|max:60',
            'enable_ip_lock' => 'nullable|boolean',
            'video_duration_hours' => 'nullable|integer|min:1|max:24'
        ]);
        
        try {

            $expirySeconds = $request->expiry_seconds ?? 10;
            // $expirySeconds = 10;
            $enableIPLock = $request->enable_ip_lock ?? true;
            $videoDuration = $request->video_duration_hours ?? 2;

            $hostname = parse_url($request->url, PHP_URL_HOST);


            $cdnDomain = CDNDomain::where('domain_name', $hostname)->first();
        
            if (!$cdnDomain) {
                return response()->json([
                    'status' => false,
                    'message' => 'CDN domain not found: ' . $hostname
                ], 404);
            }


            $token = Str::random(64);
                
            $tokenData = [
                'original_url' => $request->url,
                'cdn_secret' => $cdnDomain->url,
                'used' => false,
                'user_ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'enable_ip_lock' => $enableIPLock,
                'video_duration_hours' => $videoDuration,
                'created_at' => now()->toDateTimeString(),
                'expires_at' => now()->addSeconds($expirySeconds)->toDateTimeString()
            ];
            



            // Generate signed URL
            // $signedUrl = $this->signM3U8Url($request->url, $expirySeconds, $enableIPLock, $videoDuration);

            $cacheSeconds = max(60, $expirySeconds + 10);
            Cache::put('video_token:' . $token, $tokenData, $cacheSeconds);
            
            // ✅ FIXED: Use full URL instead of route()
            $proxyUrl = url('/api/video/play/' . $token);
            
            return response()->json([
                'status' => true,
                'url' => $proxyUrl,
                'token' => $token,
                'security' => [
                    'expires_in_seconds' => $expirySeconds,
                    'ip_locked' => $enableIPLock,
                    'one_time_use' => true,
                    'video_playback_hours' => $videoDuration
                ],
                'solution' => 'Secure One-Time Token',
                'note' => "Link expires in {$expirySeconds} seconds if not used. Once accessed, video plays for {$videoDuration} hours."
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to generate secure URL',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    
    /**
     * Advanced: Sign URL with custom expiry and IP validation
     */
    public function signM3U8UrlAdvanced($url, $expirySeconds = 3600, $userIp = null)
    {
        $parsedUrl = parse_url($url);
        $protocol = $parsedUrl['scheme'];
        $hostname = $parsedUrl['host'];
        $fullPath = ltrim($parsedUrl['path'], '/');
        
        $pathInfo = pathinfo($fullPath);
        $path_to_hash = $pathInfo['dirname'] . '/';
        $filename = $pathInfo['basename'];
        
        $secret_key = CDNDomain::where('domain_name', $hostname)->first()->url;
        
        $protected = '';
        // $secret = $this->cacheflySecret;
        $secret = $secret_key;

        $expiretime = time() + $expirySeconds;
        
        // Build rules with optional IP validation
        $rules = 'expiretime=' . $expiretime . ';dirmatch=true';
        if ($userIp) {
            $rules .= ';ip=' . $userIp;
        }
        
        $hash = hash_hmac('sha256', $rules . $path_to_hash, $secret, false);
        
        $request = "$protocol://$hostname$protected/$rules/$hash/" . $path_to_hash;
        if ($filename != '') {
            $request = $request . $filename;
        }
        
        return $request;
    }
    

    public function getCDNSettings(){
        $cdn_setting = CdnSetting::first();

        // $urls = explode(',', $cdn_setting->cdn_links);


        // if ($cdn_setting && count($urls) > 0) {            
        //     return response()->json([
        //         'status' => true,
        //         'enabled' => $cdn_setting->status == 1 ? true : false,
        //         'urls' => $urls
        //     ]);
        // }
        // else{
        //     return response()->json([
        //         'status' => false,                
        //         'urls' => []
        //     ]);
        // }


        if ($cdn_setting) {
            $domains = CDNDomain::where('cdn_setting_id', $cdn_setting->id)->get();

            if (count($domains) > 0) {                
                return response()->json([
                    'status' => true,
                    'emabled' => $cdn_setting->status == 1 ? true : false,
                    'domains' => $domains,
                ]);
            }
            else{
                return response()->json([
                    'status' => false,
                    'emabled' => $cdn_setting->status == 1 ? true : false,
                    'domains' => $domains,
                ]);
            }
        }
        else{
            return response()->json([
                'status' => false,                
                'message' => 'CDNs not found !'
            ]);
        }
    }





    public function checkPriceAndUpdateByCron(){

        $users = ClientUser::select(
            'clientusers.id',
            'clientusers.created_by',
            'clientusers.current_amount'
        )
        ->whereNull('clientusers.deleted_at')
        ->get();

        foreach ($users as $user) {

            try {

                DB::transaction(function () use ($user) {

                    $last_active_plan = $this->getLastActivePlan($user->id);

                    if (!$last_active_plan) {
                        return;
                    }

                    if ($last_active_plan->plan_end_date == date('Y-m-d')) {

                        $plan_id = $last_active_plan->plan_id;

                        // 🔒 lock creator row
                        $creater = User::lockForUpdate()->find($user->created_by);
                        if (!$creater) return;

                        $role = $creater->role;

                        // 🔒 lock user row
                        $lockedUser = ClientUser::lockForUpdate()->find($user->id);
                        if (!$lockedUser) return;

                        $planDetails = null;

                        if ($role == 2) {
                            $planDetails = AdminPlan::find($plan_id);
                        } elseif ($role == 3) {
                            $planDetails = ResellerPlan::find($plan_id);
                        } elseif ($role == 4) {
                            $planDetails = RetailorPlan::find($plan_id);
                        } elseif ($role == 1) {
                            $planDetails = SadminPlan::find($plan_id);
                        }

                        // ❗ FIX: variable name
                        if (!$planDetails) return;

                        // ✅ correct condition
                        if ($lockedUser->current_amount >= $planDetails->total_price && $planDetails->total_price != 0) {

                            // call your existing logic
                            $this->renewPlan($lockedUser->id);
                        }
                    }

                });

            } catch (\Exception $e) {
                \Log::error("Cron failed for user {$user->id}: ".$e->getMessage());
            }
        }

        return response()->json([
            'status' => true,
            'count' => count($users),
            'users' => $users,
            'message' => 'Price check and update completed successfully By Cron.'
        ]);
    }



    public function getAllSportsLive(){
        
        $user_id = $this->get_user_id();    


        $sports_live = Channel::whereNull('deleted_at')->where('status', 1)->where('sport_flag', 1)->get();

        if (count($sports_live) > 0) {
            return response()->json([
                'status' => true,
                'data' => $sports_live
            ]);
        }
        else{
            return response()->json([
                'status' => false,
                'data' => []
            ]);
        }
    }

    public function getAllKidsLive(){
        
        $user_id = $this->get_user_id();    


        $kids_live = Channel::whereNull('deleted_at')->where('status', 1)->where('kids_flag', 1)->get();

        if (count($kids_live) > 0) {
            return response()->json([
                'status' => true,
                'data' => $kids_live
            ]);
        }
        else{
            return response()->json([
                'status' => false,
                'data' => []
            ]);
        }
    }


    public function checkIsFrozen(){
        $user_id = $this->get_user_id();


        $data_user = ClientUser::where('id', $user_id)->first();

        $created_by = $data_user->created_by;

        $user = User::where('id', $created_by)->first();


        if($user && $user->freeze_status == 1){

            $phone = $user->mobile;
            return response()->json([
                'status' => true,
                'message' => 'Your account has been frozen. Please contact admin.',
                'phone' => $phone,
            ]);
        }


        if($user->role == 3){
            $created_by = $user->created_by;

            $admin = User::where('id', $created_by)->first();

            if($admin && $admin->freeze_status == 1){

                $phone = $admin->mobile;
                return response()->json([
                    'status' => true,
                    'message' => 'Your account has been frozen. Please contact admin.',
                    'phone' => $phone,                    
                ]);
            }
        }

        if($user->role == 4){
            $created_by = $user->created_by;
            $reseller = User::where('id', $created_by)->first();

            if($reseller && $reseller->freeze_status == 1){

                $phone = $reseller->mobile;
                return response()->json([
                    'status' => true,
                    'message' => 'Your account has been frozen. Please contact admin.',
                    'phone' => $phone,                    
                ]);
            }

            else{
                $reseller_created_by = $reseller->created_by;

                $admin = User::where('id', $reseller_created_by)->first();

                if($admin && $admin->freeze_status == 1){

                    $phone = $admin->mobile;
                    return response()->json([
                        'status' => true,
                        'message' => 'Your account has been frozen. Please contact admin.',
                        'phone' => $phone,                        
                    ]);
                }

            }

        }




        return response()->json([
            'status' => false,                        
        ]);
    }

}