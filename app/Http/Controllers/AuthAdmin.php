<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Cookie;
use Carbon\Carbon;
use Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPassword;
use App\Models\User;


class AuthAdmin extends Controller
{
    public function showForgetPasswordForm()
    {
        return view('auth.forgot_password');
    }


    public function submitForgetPasswordForm(Request $request)
    {
    $request->validate([
      'email' => 'required|email|exists:users',
    ]);

    $token = Str::random(64);

    DB::table('password_resets')->insert([
      'email' => $request->email,
      'token' => $token,
      'created_at' => Carbon::now()
    ]);

    $email = $request->email;
    $subject = "RESET PASSWORD || GIGABIT IPTV";
    $from = "noreply@gigabitiptv.com";

    $data = array('body' => 'Reset Passwod','email' => $email ,'token' => $token);

    // $sent = Mail::to($request->email)->send(new ForgotPassword($token));

    $sent = Mail::send(['html' => 'emails.forgetPassword'], $data, function($message) use ($email,$subject, $from) {
        $message->to($email, "Forgot Password")
        ->subject($subject);
        $message->from($from , 'noreply@gigabitiptv.com');
    });

    // check for failures
    if ($sent) {
        return back()->withErrors('message', 'Error in sending e-mail!');
    }

    return back()->with('success', 'We have e-mailed your password reset link!');

    }

    public function showResetPasswordForm($token) {
     return view('auth.reset_password', ['token' => $token]);
    }


    public function submitResetPasswordForm(Request $request)
    {
      $request->validate([
          'email' => 'required|email|exists:users',
          'password' => 'required|string|min:6|confirmed',
          'password_confirmation' => 'required'
      ]);

      $updatePassword = DB::table('password_resets')
                          ->where([
                            'email' => $request->email,
                            'token' => $request->token
                          ])
                          ->first();

      if(!$updatePassword){
          return back()->withErrors('message', 'Invalid token!');
      }

      $user = User::where('email', $request->email)
                  ->update(['password' => $request->password,'real_password' =>$request->password]);

      DB::table('password_resets')->where(['email'=> $request->email])->delete();

      return redirect('/')->with('success', 'Your password has been changed!');
    }
}
