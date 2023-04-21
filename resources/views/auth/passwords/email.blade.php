@extends('auth.main')
@section('title')
    Forgot Password | {{ config('app.name', 'Calltronix Kenya Limited') }}
@endsection
@section('content')
    <div class="card-body">
        <div class="divider">
            <div class="divider-text text-uppercase text-muted">
                <small> Password Reset</small>
            </div>
        </div>
        <div class="text-muted text-center mb-2"><small>Enter the email or phone number you
                used
                when you joined
                and we will send you temporary password</small></div>
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
            <div class="form-group mb-2">
                <label class="text-bold-600" for="emailaddress">Email </label>
                <input type="text" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}"
                       id="emailaddress"
                       placeholder="Enter your email" name="email" value="{{ old('email') }}">
                @if ($errors->has('email'))
                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                @endif
            </div>
            <button style="color: #fff;background-color: #0072bc; border-color: #0072bc;" type="submit"
                    class="btn glow position-relative w-100">SEND
                PASSWORD<i id="icon-arrow" class="bx bx-right-arrow-alt"></i>
            </button>
        </form>
        <hr/>
        <div
            class="form-group d-flex flex-md-row flex-column justify-content-between align-items-center ">
            <div class="text-center">
                <small class="text-muted ">
                    I remembered my password
                </small>

                <a href="{{ route('login') }}"
                   class="card-link load-page"><small>login</small>
                </a>
            </div>
        </div>

        <div class="text-center text-sm-center  mb-2" style="font-size: 0.8rem;">

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
