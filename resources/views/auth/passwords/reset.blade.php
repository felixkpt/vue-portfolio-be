@extends('auth.main')
@section('title')
    Reset Password | {{ config('app.name', 'Calltronix Kenya  Limited') }}
@endsection
@section('content')
    <div class="card-body">
        <div class="divider">
            <div class="divider-text text-uppercase text-muted">
                <small> Password Reset</small>
            </div>
        </div>
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="form-group">
                <label class="text-bold-600" for="emailaddress">Email address</label>
                <input class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                       value="{{ $email ?? old('email') }}" type="email" name="email" id="email"
                       placeholder="Enter your email" required>
                @if ($errors->has('email'))
                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                @endif
            </div>
            <div class="form-group">
                <label class="text-bold-600" for="exampleInputPassword1">New Password</label>
                <input class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                       name="password" required type="password" id="password"
                       placeholder="Enter your password">
                @if ($errors->has('password'))
                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                @endif

            </div>
            <div class="form-group mb-2">
                <label class="text-bold-600" for="exampleInputPassword2">Confirm New
                    Password</label>
                <input class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}"
                       name="password_confirmation" required type="password" id="password_confirmation"
                       placeholder="Enter your password again">
                @if ($errors->has('password_confirmation'))
                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                @endif
            </div>
            <button style="color: #fff;background-color: #0072bc; border-color: #0072bc;" type="submit"
                    class="btn glow position-relative w-100">Reset my
                password<i id="icon-arrow" class="bx bx-right-arrow-alt"></i></button>

        </form>
        <hr/>
        <div class="text-center  mb-2">

            &copy; {{ date('Y') }}
            @if(@$organization->copy_right_text) {{ @$organization->copy_right_text }}
            @else
                Calltronix Kenya Ltd
            @endif
            <span
                style="background-color:#0072bc;padding: 2px 3px;font-weight: 500;line-height: 10px;"
                class="badge badge-info"><small>Version 1.0</small></span>
        </div>
    </div>
@endsection
