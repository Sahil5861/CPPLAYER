<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

class LoginController extends Controller
{
    /**
     * Display login page.
     * 
     * @return Renderable
     */
    public function show()
    {
        if(Auth::check()) 
        {
            return redirect('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Handle account login request
     * 
     * @param LoginRequest $request
     * 
     * @return \Illuminate\Http\Response
     */
    // public function login(LoginRequest $request)
    // {   
    //     $request->merge(['status' => 1]);
    //     $credentials = $request->getCredentials();


    //     // print_r($credentials); exit;
    //     // $valid = Auth::validate($credentials);

        
        
    //     $valid = User::where(['email'=>$request->email, 'real_password' => $request->password])->first();
        
    //     // print_r(json_encode($valid)); exit;
    //     if ($valid) {
    //         // $user = Auth::getProvider()->retrieveByCredentials($credentials); 
    //         $user = User::where(['email'=>$request->email, 'real_password' => $request->password])->first();  
            
    //         // print_r(json_encode($user)); exit;
        
    //         Auth::login($user);

    //         return $this->authenticated($request, $user);
    //     }
    //     else{
    //         $check_status = DB::table('users')->where(['email'=>$request->email, 'real_password' => $request->password, 'status' => 0])->first();

    //         if(isset($check_status->id)){
    //             $master_admin = DB::table('users')->where(['id'=>$check_status->created_by, 'status' => 1])->first();
    //             return redirect()->back()
    //             ->with('blocked', 'Your service is suspended, please contact your provider '.$master_admin->mobile);
    //         }else{
    //             return redirect()->to('login')
    //             ->withErrors(trans('auth.failed'));
    //         }

    //     }

    //     // if(!Auth::validate($credentials)):

        
    //     //     $check_status = DB::table('users')->where(['email'=>$request->email, 'real_password' => $request->password, 'status' => 0])->first();

            

            
    //     //     if(isset($check_status->id)){
    //     //         $master_admin = DB::table('users')->where(['id'=>$check_status->created_by, 'status' => 1])->first();
    //     //         return redirect()->back()
    //     //         ->with('blocked', 'Your service is suspended, please contact your provider '.$master_admin->mobile);
    //     //     }else{
    //     //         return redirect()->to('login')
    //     //         ->withErrors(trans('auth.failed'));
    //     //     }
    //     // endif;

        
    //     // $user = Auth::getProvider()->retrieveByCredentials($credentials);        
        
    //     // Auth::login($user);

    //     // return $this->authenticated($request, $user);
    // }



    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        // 🔍 Find user by email
        $user = User::where('email', $request->email)->whereNull('deleted_at')->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User not found']);
        }

        // 🚫 Check if user is inactive
        if ($user->status == 0) {
            $master_admin = User::where('id', $user->created_by)
                ->where('status', 1)
                ->first();

            return back()->with('blocked',
                'Your service is suspended. Contact: ' . ($master_admin->mobile ?? 'Admin')
            );
        }


        // print_r($user); exit;

        // 🔐 Secure password check
        // if (!Hash::check($request->password, $user->password)) {
        //     return back()->withErrors(['password' => 'Invalid password']);
        // }

        if ($request->password !== $user->real_password) {
            return back()->withErrors(['password' => 'Invalid password']);
        }

        // ✅ Login user
        Auth::login($user);

        return $this->authenticated($request, $user);
    }
    /**
     * Handle response after user authenticated
     * 
     * @param Request $request
     * @param Auth $user
     * 
     * @return \Illuminate\Http\Response
     */
    protected function authenticated(Request $request, $user) 
    {
        return redirect()->intended();
    }
}