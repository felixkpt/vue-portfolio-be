<?php

namespace App\Cih\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use App\Cih\Repositories\ShRepository;
use App\Models\PermissionGroup;

class AuthController extends Controller
{
    //
    public function login()
    {

        $data = \request()->all();
        $valid = Validator::make($data, [
            'username' => 'required',
            'password' => 'required'
        ]);

        if (count($valid->errors())) {
            return response([
                'status' => 'failed',
                'errors' => $valid->errors()
            ], 422);
        }

        $email = \request('username');
        $password = \request('password');
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $token = request()->user()->createToken('api_token_at_' . now()->toDateTimeString());
            $user = \request()->user();
            ShRepository::storeLog('user_login', "$user->role($user->name) logged in", $user);
            return [
                'status' => 'success',
                'token' => $token->plainTextToken,
                'user' => request()->user()
            ];
        }
        return response([
            'status' => 'failed',
            'errors' => ['email' => ['Invalid email or password']]
        ], 422);
    }

    public function forgotPassword(Request $request)
    {
        $data = \request()->all();
        $valid = Validator::make($data, [
            'email' => 'required|email',
        ]);
        if (count($valid->errors())) {
            return response([
                'status' => 'failed',
                'errors' => $valid->errors()
            ], 422);
        }
        $status = Password::sendResetLink(
            $request->only('email')
        );
        if ($status !== Password::RESET_LINK_SENT) {
            return response([
                'status' => 'failed',
                'errors' => ['email' => [__($status)]]
            ], 422);
        }
        return [
            'status' => 'success',
            'message' => 'Email sent'
        ];
    }
    public function resetPassword(Request $request)
    {
        $data = \request()->all();
        $valid = Validator::make($data, [
            'email' => 'required|email',
            'new_password' => 'required|confirmed'
        ]);
        if (count($valid->errors())) {
            return response([
                'status' => 'failed',
                'errors' => $valid->errors()
            ], 422);
        }
        $credentials =  $request->only('email', 'token');
        if (is_null($user = $this->broker()->getUser($credentials))) {
            return response([
                'status' => 'failed',
                'errors' => ['email' => [trans(Password::INVALID_USER)]]
            ], 422);
        }
        if (!$this->broker()->tokenExists($user, $credentials['token'])) {
            return response([
                'status' => 'failed',
                'errors' => ['email' => [trans(Password::INVALID_TOKEN)]]
            ], 422);
        }
        $user->password = Hash::make(\request('new_password'));
        $user->update();
        return [
            'status' => 'success',
            'message' => 'Password updated'
        ];
    }
    public function broker()
    {
        return Password::broker();
    }
    public function register()
    {
        // User::where([])->delete();
        $rules = [
            'email' => 'required|unique:users',
            'name' => 'required',
            'phone' => 'required',
            'password' => 'required|confirmed',
        ];
        $data = \request()->all();
        $valid = Validator::make($data, $rules);
        if (count($valid->errors())) {
            return response([
                'status' => 'failed',
                'errors' => $valid->errors()
            ], 422);
        }

        $email = \request('email');
        $password = \request('password');
        $name = \request('name');
        $phone = \request('phone');
        $role = 'client';
        $avatar = null;

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'role' => $role,
            'password' => Hash::make($password),
            'avatar' => $avatar
        ]);

        $token = $user->createToken('api_token_at_' . now()->toDateTimeString());

        if (User::count() == 1) {

            $user->role = 'admin';

            $permission_group = PermissionGroup::where([])->first();
            if (!$permission_group) {
                $permission_group = PermissionGroup::create([
                    'name' => 'Admin',
                    'slug' => 'admin',
                    'description' => 'Admin, also known as a web administrator or webmaster, individual or a team responsible for managing and maintaining the website. Admin ensures the smooth operation and functionality of the website, as well as to monitor and manage its content, security, and performance.',
                    'routes' => json_encode(['*']),
                    'slugs' => json_encode(['*']),
                    'is_default' => 1
                ]);
            }

            $user->permission_group_id = $permission_group->id;
            $user->save();
        }

        // dd(4567);

        ShRepository::storeLog('user_registration', "$user->role  <a target='_blank' href='/admin/users/user/$user->id'> $user->name</a> registered", $user);
        return [
            'status' => 'success',
            'user' => $user,
            'token' => $token->plainTextToken
        ];
    }

    public function getUserByToken()
    {

        if (request()->token == 'default') return response([
            'roles' => ['subscriber'],
            'permissions' => [],
            'routes' => [],

        ], 200);

        $user = currentUser();

        if (!$user) return response([], 401);

        // $user = User::with('permissions')->find($user->id)->first();
        $user->roles = [$user->role];

        $user->permissions = PermissionGroup::find($user->permission_group_id);

        if ($user->permissions) {
            $routes = json_decode($user->permissions->routes);
            $user->routes = array_values(array_unique(array_map(fn ($route) => preg_split('#@#', $route, 2)[0], $routes)));
        }

        $user->avatar = asset($user->avatar);

        return $user;
    }

    public function updateProfile()
    {
        $user = \request()->user();
        $data = \request()->all();
        $rules = [
            'phone' => 'required',
            'name' => 'required'
        ];
        $phone = \request('phone');
        $previous_phone = $user->phone;
        if ($previous_phone != $phone) {
            $rules['phone'] = 'required|unique:users';
        }
        $valid = Validator::make($data, $rules);
        if (count($valid->errors())) {
            return response([
                'status' => 'failed',
                'errors' => $valid->errors()
            ], 422);
        }
        $phone = request('phone');
        $phone_arr = explode(':', $phone);
        $previous_phone = $user->phone;
        if (count($phone_arr) == 3) {
            $phone = $phone_arr[2];
            if (!$phone) {
                return response([
                    'status' => 'failed',
                    'errors' => ['phone' => ['phone is required']]
                ], 422);
            }
            $country_code = $phone_arr[0];
            $ext = $phone_arr[1];
            $phone = $ext . $phone;
            $user->phone = $phone;
            $user->country_code = $country_code;
        } else {
            $user->phone = \request('phone');
        }
        if (strlen($user->phone) < 10) {
            return response([
                'status' => 'failed',
                'errors' => ['phone' => ['Invalid phone number provided']]
            ], 422);
        }
        $user->name = \request('name');
        $user->update();
        return [
            'status' => 'success',
            'user' => $user
        ];
    }

    public function updatePassword()
    {
        $data = \request()->all();
        $valid = Validator::make($data, [
            'current_password' => 'required',
            'new_password' => 'required|confirmed',
        ]);
        if (count($valid->errors())) {
            return response([
                'status' => 'failed',
                'errors' => $valid->errors()
            ], 422);
        }
        $user = \request()->user();
        if (!Hash::check(\request('current_password'), $user->password)) {
            return response([
                'status' => 'failed',
                'errors' => ['current_password' => ['Current password incorrect']]
            ], 422);
        }
        $new_password = request('new_password');
        $user->password = Hash::make($new_password);
        $user->update();
        return [
            'status' => 'success',
            'message' => 'password updated successfully'
        ];
    }
    public function logoutUser()
    {
        $user = currentUser();

        if ($user) {
            \Laravel\Sanctum\PersonalAccessToken::findToken(request()->bearerToken() ?? request()->token)->delete();
        }

        return [
            'status' => 'success'
        ];
    }
}
