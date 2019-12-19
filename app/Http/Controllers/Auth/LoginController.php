<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use http\Env\Request;
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

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function loginPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|CheckHpAndLogin:email|CheckAccountActivated:email'
        ]);

        if ($validator->fails()) {
            return redirect()->route('LoginGet')->withErrors($validator);
        } else {
            $email = $request->only('email');
            $userId = User::select('user_id')->where('email', '=', $email)->firstOrFail();

            if (Auth::loginUsingId($userId['user_id']) && Log::Log($userId['user_id'])) {
                return redirect()->intended('dashboard');
            } else {
                return redirect()->route('LoginGet')->withErrors($validator);
            }
        }
    }
    public function loginGet()
    {
        return redirect('/login');
    }
    public function Dashboard()
    {
            return view('dashboard');

    }

}
