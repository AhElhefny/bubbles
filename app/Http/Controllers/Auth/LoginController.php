<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;
   
    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    
    public function authenticated()
    {
        
        if(auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'staff')
        {

            return redirect()->route('admin-dashboard');
        } 

         elseif(auth()->user()->user_type == 'seller' && auth()->user()->seller_type == 'super_manager')
         {

            return redirect()->route('dashboard');
         }else{
             auth()->logout();
         }
    }
    

    public function logout(Request $request)
    {
        if(auth()->user() != null && (auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'staff')){

            $redirect_route = 'login';
        }

        else{
            
            $redirect_route = 'home';
        }
        
        $this->guard()->logout();
        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect()->route($redirect_route);
    }

}
