<?php

namespace App\Http\Controllers;


use App\Models\Channel;
use App\Models\Genre;
use App\Models\Slider;
use App\Models\Userauth;
use App\Models\ClientUser;
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
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Api-Key, Auth-Key");
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
        // print_r($headers['Auth-Key']); exit;
        if (isset($headers['Auth-Key']) && $headers['Auth-Key'] != '')
        {

            $auth_key = $headers['Auth-Key'];

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

    public function loginAccessUser($userData)
    {
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
        
        $userauth->save();

        
        print_r(json_encode(array(
            "status" => true,
            "msg" => "Login Successfully",
            "result_auth_key" => $auth_key,
            'data' => $userData,
            'imageBaseUrl' => 'https://iptv-gigabitcdn.s3.ap-south-1.amazonaws.com/'
        )));
        exit;
        
    }

    function login_pin(Request $req) {
    
        $post = json_decode(file_get_contents('php://input', 'r'));

        if (isset($post->login_pin) && $post->login_pin != '')
        {

            $login_pin = $post->login_pin;
            // $password = md5($post->password);

            $data_user = ClientUser::where('login_pin','=',$login_pin)->first();
            
            if ($data_user)
            {   

                $data_user->fcm_token = $post->token;
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
                "msg" => "Please enter mobile and password fields."
            )));
            exit;
        }
    }

    public function getChannels(Request $request)
    {
        $user_id = $this->get_user_id();
        $channel = Channel::where('status',1)->get();
        print_r(json_encode(array(
            'status' => true,
            'message' => 'All channels.',
            'data' =>$channel
        )));
        exit;

    }

    public function getSlider(Request $request)
    {
        $user_id = $this->get_user_id();
        $slider = Slider::where('status',1)->get();
        print_r(json_encode(array(
            'status' => true,
            'message' => 'All channels.',
            'data' =>$slider
        )));
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

    public function getChannelsWithGenre(Request $request)
    {
        // code...
        $user_id = $this->get_user_id();
        $channel = Genre::where('status',1)->with('channels')->get();
        print_r(json_encode(array(
            'status' => true,
            'message' => 'All channels with genre.',
            'data' =>$channel
        )));
        exit;
    }

    public function getChannelsWithGenrePopular(Request $request)
    {
        // code...
        $user_id = $this->get_user_id();
        $channel = Genre::where('status',1)->with('channelspopular')->get();
        print_r(json_encode(array(
            'status' => true,
            'message' => 'All channels with genre.',
            'data' =>$channel
        )));
        exit;
    }

    

    public function getGenreChannels(Request $request)
    {
        // code...
        $user_id = $this->get_user_id();
        $post = json_decode(file_get_contents('php://input', 'r'));
        $channel = Genre::where('status',1)->where('id',$post->genre_id)->with('channels')->get();
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
            $success = Storage::disk('s3')->putFileAs('images/' . $imageName, public_path().$destinationPath, ''); // old : $file
            @unlink(public_path().$destinationPath);
            $user->profile_pic = $imageName;
            if($user->save()){
                print_r(json_encode(array(
                    'status' => true,
                    'path' => $success,
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
    
}
