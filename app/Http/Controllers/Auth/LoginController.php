<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        $login = request()->input('login');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$field => $login]);
        return $field;
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(\Illuminate\Http\Request $request)
    {
        $login = $request->input('login');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
        // If logging in with email, verify user is admin or super_admin
        if ($field === 'email') {
            $user = \App\Models\User::where('email', $login)->first();
            if ($user && !in_array($user->role, ['admin', 'super_admin'])) {
                // Return invalid credentials if employee tries to login with email
                return [
                    $field => $login,
                    'password' => $request->password,
                ];
            }
        }
        
        return [
            $field => $login,
            'password' => $request->password,
        ];
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(\Illuminate\Http\Request $request, $user)
    {
        // Check if employee tried to login with email
        $login = $request->input('login');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
        if ($field === 'email' && $user->role === 'employee') {
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Employees can only login with username, not email.');
        }

        // Check shop status
        if (!$user->isSuperAdmin() && $user->shop && !$user->shop->isActive()) {
            $shop = $user->shop;
            auth()->logout();

            if ($shop->isSuspended()) {
                $msg = "Your shop ('{$shop->name}') has been permanently suspended. Please contact the platform Super Admin.";
            } else {
                $reason = $shop->status_reason ?: 'Payment or policy compliance required';
                $deadlineInfo = '';
                if ($shop->action_deadline) {
                    $daysLeft = (int) now()->startOfDay()->diffInDays($shop->action_deadline->startOfDay(), false);
                    if ($daysLeft > 0) {
                        $deadlineInfo = " You have until {$shop->action_deadline->format('d M, Y')} ({$daysLeft} days left) to clear dues before permanent suspension.";
                    } elseif ($daysLeft === 0) {
                        $deadlineInfo = " Today is the final day to clear dues before permanent suspension.";
                    } else {
                        $deadlineInfo = " The grace period expired on {$shop->action_deadline->format('d M, Y')}.";
                    }
                }
                $msg = "Your shop ('{$shop->name}') is temporarily inactive due to: {$reason}.{$deadlineInfo} Please contact Super Admin or complete pending payment to re-activate.";
            }

            return redirect()->route('login')->with('error', $msg);
        }
        
        if ($user->isSuperAdmin()) {
            return redirect()->route('super_admin.dashboard');
        }

        return redirect()->intended($this->redirectPath());
    }

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
        $this->middleware('auth')->only('logout');
    }
    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(\Illuminate\Http\Request $request)
    {
        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
