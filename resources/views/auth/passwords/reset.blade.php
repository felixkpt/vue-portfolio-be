@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Reset Password') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Reset Password') }}
                                </button>
                            </div>
                        </div>
                    </form>


                    <div class="password-hint-wrapper d-none">
                        <br>
                        <h6>Password requirements</h6>
                        <div class="hint is-long-enough hint-valid">
                          <svg class="malibu-icon malibu-icon-dark-gray">
                            <use xlink:href="#confirm-16"></use>
                          </svg>
                          Must be a minimum of 8 characters.
                        </div>
              
                        <div class="hint has-all-characters hint-valid">
                          <svg class="malibu-icon malibu-icon-dark-gray">
                            <use xlink:href="#confirm-16"></use>
                          </svg>
                          Must contain letters, numbers, and symbols.
                        </div>
              
                        <div class="hint passwords-match hint-valid">
                          <svg class="malibu-icon malibu-icon-dark-gray">
                            <use xlink:href="#confirm-16"></use>
                          </svg>
                          Passwords must match.
                        </div>
                      </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
