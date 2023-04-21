@extends('auth.main')

@section('title')
    Login | {{ config('app.name', 'Calltronix Kenya  Limited') }}
@endsection
@section('content')
    <div class="card-body">
        <div class="divider">
            <div class="divider-text text-uppercase text-muted"><small> login with
                    email</small>
            </div>
        </div>
        <form class="ajax-poscct" method="POST" action="{{ url('login-user') }}">
            @csrf
            @if (session('message'))
                <div class="alert alert-danger m-t-10">
                    {{ session('message') }}
                </div>
            @endif

            <div class="form-group mb-50">
                <label class="text-bold-600" for="username">Email Address</label>
                <input name="username" type="text"
                       class="form-control {{ isset($errors) && $errors->has('username') ? ' is-invalid' : '' }}"
                       placeholder="Email" value="{{ old('username') }}">
                @if (isset($errors) && $errors->has('username'))
                    <span class="invalid-feedback">
                                        <strong>{{ isset($errors) && $errors->first('username') }}</strong>
                                </span>
                @endif

            </div>
            <div class="form-group">
                <label class="text-bold-600" for="password">Password</label>
                <input name="password" type="password"
                       class="form-control {{ isset($errors) && $errors->has('password') ? ' is-invalid' : '' }}"
                       placeholder="Password">
                @if (isset($errors) && $errors->has('password'))
                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('password') }}</strong>
                               </span>
                @endif
            </div>
            <div
                class="form-group d-flex flex-md-row flex-column justify-content-between align-items-center">
                <div class="text-left">
                    <div class="checkbox checkbox-sm">
                        <input type="checkbox" class="form-check-input" name="remember"
                               id="checkbox-signin" {{ old('remember') ? 'checked' : '' }} >
                        <label class="checkboxsmall" for="checkbox-signin"><small>Keep me logged
                                in</small></label>
                    </div>
                </div>
                <div class="text-right">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                           class="card-link load-page"><small>Forgot Password?</small></a>
                    @endif
                </div>
            </div>
            <button style="color: #fff;background-color: #0072bc; border-color: #0072bc;" type="submit"
                    class="btn glow w-100 position-relative submit submit-btn">
                <b>Login</b><i id="icon-arrow" class="bx bx-right-arrow-alt"></i></button>
        </form>
        <hr>
        <div class="text-center"><small class="mr-25">Don't have an account?</small> <br>
            <small>Please <a href="mailto:info@calltronix.co.ke">contact</a> Calltronix Administrator to
                get an account</small>
            <div class="text-center text-sm-center pt-1" style="font-size: 0.8rem;">
                &copy; {{ date('Y') }} @if(@$organization->copy_right_text) {{ @$organization->copy_right_text }} @else
                    Calltronix Innovation Hub @endif<span
                    style="background-color:#0072bc;padding: 2px 3px;font-weight: 500;line-height: 10px;"
                    class="badge badge-info"><small>Version 1.0</small></span>
            </div>
        </div>
    </div>
@endsection
