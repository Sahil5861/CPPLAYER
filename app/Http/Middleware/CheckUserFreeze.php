<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class CheckUserFreeze
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->freeze_status == 1) {

            abort(403, 'Your account has been frozen. Please contact admin.');
        }

        // new code here start

        if ($user->role == 3) {
            
            // check is admin is freeze or not
            $created_by = $user->created_by;

            $admin = User::where('id', $created_by)->first();

            if ($admin && $admin->freeze_status == 1) {
                abort(403, 'Your account has been frozen. Please contact admin.');
            }
        
        }
        if ($user->role == 4) {
            
            // check is reseller is freeze or not
            $created_by = $user->created_by;
            $reseller = User::where('id', $created_by)->first();

            if ($reseller && $reseller->freeze_status == 1) {
                abort(403, 'Your account has been frozen. Please contact admin.');
            }
            else{
                // Cehck for resellers admin id freeze or not

                $reseller_created_by = $reseller->created_by;

                $admin = User::where('id', $reseller_created_by)->first();

                if ($admin && $admin->freeze_status == 1) {
                    abort(403, 'Your account has been frozen. Please contact admin.');
                }
            }   
        }

        return $next($request);
    }
}
