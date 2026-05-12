<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use DB;

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
    public function login(LoginRequest $request)
    {   
        $request->merge(['status' => 1]);
        $credentials = $request->getCredentials();

        

        if(!Auth::validate($credentials)):

        
            $check_status = DB::table('users')->where(['email'=>$request->email, 'real_password' => $request->password, 'status' => 0])->first();
            
            if(isset($check_status->id)){
                $master_admin = DB::table('users')->where(['id'=>$check_status->created_by, 'status' => 1])->first();
                return redirect()->back()
                ->with('blocked', 'Your service is suspended, please contact your provider '.$master_admin->mobile);
            }else{
                return redirect()->to('login')
                ->withErrors(trans('auth.failed'));
            }
        endif;

        $user = Auth::getProvider()->retrieveByCredentials($credentials);
        
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