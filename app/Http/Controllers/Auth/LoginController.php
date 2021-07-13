<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Hashing\BcryptHasher;

use Auth;

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

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //session(['url.intended' => url()->previous()]);
        //$this->redirectTo = session()->get('url.intended');
        $this->middleware('guest')->except('logout');
    }

    public function username(){
        return 'username';
    }

    protected function authenticated(Request $request)
    {
        //dd($request->all());
        // Ambil Username Session
        $username = $request->input('username');
        $id = Auth::id();
        
        // Set Session Username & ID
        Session::put('userid', $id);
        //$request->session()->put('username', $username);

        $user = DB::table('users')
                    ->join('roles','users.role_user','roles.role_code')
                    ->join('site_mstrs','site_mstrs.site_code','users.site')
                    ->where('users.id','=',$id)
                    ->first();

                    
        if(!is_null($user)){
            Session::put('username',$user->username);
            Session::put('menu_access', $user->menu_access);
            Session::put('name', $user->name);
            Session::put('site',$user->site);
            Session::put('pusat_cabang',$user->pusat_cabang);
	    Session::put('salesman',$user->salesman);        
	}

    }

    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $previous_session = Auth::User()->session_id;
        if ($previous_session) {
            \Session::getHandler()->destroy($previous_session);
        }

        Auth::user()->session_id = \Session::getId();
        
        Auth::user()->save();
        $this->clearLoginAttempts($request);

        return $this->authenticated($request, $this->guard()->user())
                ?: redirect()->intended($this->redirectPath());
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $data = DB::table('users')
                    ->where('username','=',$request->username)
                    ->get();
     
        if(count($data) == 0){
            return redirect()->back()->with(['error'=>'Username salah / tidak terdaftar']);
        }

        $hasher = app('hash');

        $users = DB::table("users")
                    ->select('id','password')
                    ->where("users.username",$request->username)
                    ->first();

        if(!$hasher->check($request->password,$users->password))
        {   
            return redirect()->back()->with(['error'=>'Password salah']);
        }
    }

}
