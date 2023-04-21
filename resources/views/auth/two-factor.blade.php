@extends('auth.main')

@section('title')
    Two Factor Verification | {{ config('app.name', 'Calltronix Kenya  Limited') }}
@endsection
@section('content')
    <div class="card-body">
        <div class="divider">
            <div class="divider-text text-uppercase text-muted">
                <small> Two Factor Authentication</small>
            </div>
        </div>

        @if(session()->has('message'))
            <div class="alert text-muted {{session()->get('error')?'alert-danger':'alert-success'}}  m-t-10">
                <small>{!!   session()->get('message') !!}</small>
            </div>
        @endif

        <form method="POST" action="{{ route('verify.store') }}" class="ajax-post">
            {{ csrf_field() }}

            <div class="form-group mb-50">
                <label class="text-bold-600 mb-1" for="username">Verification Code</label>
                <input name="two_factor_code" type="text"
                       class="form-control"
                       placeholder="Verification code" value="{{ old('two_factor_code') ?? request('two_factor_code') }}">

            </div>

            <button style="color: #fff;background-color: #0072bc; border-color: #0072bc;" type="submit"
                    class="btn glow w-100 mt-1 position-relative submit submit-btn">
                <b>Verify</b><i id="icon-arrow" class="bx bx-right-arrow-alt"></i></button>

        </form>
        <hr/>
        <div
            class="form-group d-flex flex-md-row flex-column justify-content-between align-items-center ">
            <div class="text-center">
                <small class="text-muted ">
                    If you haven't received it click
                </small>

                <a href="{{ route('verify.resend') }}"
                   class="card-link load-page "><small>here</small>
                </a>
                <small class="text-muted ">to resend</small>
            </div>
        </div>

        <div class="text-center text-sm-center pt-1" style="font-size: 0.8rem;">
            &copy; {{ date('Y') }}
            @if(@$organization->copy_right_text) {{ @$organization->copy_right_text }}
            @else
                Calltronix Kenya Ltd
            @endif<span
                style="background-color:#0072bc;padding: 2px 3px;font-weight: 500;line-height: 10px;"
                class="badge badge-info"><small>Version 1.0</small></span>
        </div>
    </div>
@endsection
