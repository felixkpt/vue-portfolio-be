<?php

namespace App\Http\Controllers\Auth;

use App\Providers\RouteServiceProvider;
use App\Rules\StrongPassword;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Support\Facades\Auth;

class CustomAuthController extends Controller
{
    protected $maxAttempts = 20;
    protected $decayMinutes = 1;

    /**
     * This trait has all the login throttling functionality.
     */
    use ThrottlesLogins;

    /**
     * Login user
     */

    public function login(Request $request)
    {
        \request()->validate([
            'username' => 'required',
            'password' => [
                'required'
            ]
        ]);
        $username = \request()->username;
        if (strpos($username, '@') !== false)
            \request()->validate([
                'username' => 'required|email'
            ]);
        $user_name_field = filter_var(request()->username, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'phone';

        request()->merge([
            $user_name_field => request()->username
        ]);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (
            method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)
        ) {
            $this->fireLockoutEvent($request);

            $seconds = $this->limiter()->availableIn(
                $this->throttleKey($request)
            );

            return redirect()->back()->withErrors(['password' => ['Too many login attempts. Please try again in ' . $seconds . ' seconds']])->withInput();

            //            return response(['errors'=>['password'=>['Too many login attempts. Please try again in '.$seconds.' seconds']]],422);
        }



        if (Auth::attempt(request()->only($user_name_field, 'password'))) {
            $this->clearLoginAttempts($request);

            if (session('url.intended'))
                return redirect(session('url.intended')); //return ['force_redirect' => session('url.intended')];
            return redirect('home');
            //return ['force_redirect' => 'home'];
        }
        //keep track of login attempts from the user.
        $this->incrementLoginAttempts(\request());

        return redirect()->back()->withErrors(['username' => ['Invalid Username / Password.']])->withInput();

        //        return response(['errors' => ['username' => ['Invalid Username / Password.']]], 422);

    }

    /**
     * Register New user with
     * master company
     */
    public function registerUser()
    {
        $this->validateData();

        $user_data = [
            'firstname' => request()->first_name,
            'lastname' => request()->last_name,
            'middlename' => request()->middle_name,
            'name' => request()->first_name . ' ' . request()->last_name,
            'email' => request()->email,
            'phone' => request()->phone,
            'password' => request()->password,
            'calltronix_department_id' => request()->department_id,
            'role' => 'admin',
            'user_id' => \auth()->id(),
            'form_model' => User::class,
            'email_verified_at' => date("Y-m-d H:i:s")
        ];
        $user = $this->autoSaveModel($user_data);
        Auth::login($user);
        return ['force_redirect' => $user->role];
    }

    protected function formatPhone($phone)
    {
        $len = strlen($phone);
        if ($len == 10) {
            $phone = "repl" . $phone;
            $phone = str_replace('repl07', '+2547', $phone);
        }
        if ($len == 12) {
            $phone = '+' . $phone;
        }

        return $phone;
    }

    public function validateData()
    {
        request()->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['required', 'string', 'max:255'],
            'department_id' => ['required'],
            'email' => 'required|email|max:255|unique:users|regex:/^[A-Za-z0-9\.]*@(calltronix)[.](co.ke)$/',
            //            'email' => ['required','string','email','max:255','unique:users'],//
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'phone' => ['unique:users,phone', 'regex:/^((\+?254|0){1}[7]{1}[0-9]{8})$/'],

        ]);
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return filter_var(request()->username, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'phone';
    }
}
