{{--<x-guest-layout>--}}
{{--  <x-auth-card>--}}
{{--    <x-slot name="logo">--}}
{{--      <a href="/">--}}
{{--        <x-application-logo class="w-20 h-20 fill-current text-gray-500"/>--}}
{{--      </a>--}}
{{--    </x-slot>--}}

{{--    <div class="mb-4 text-sm text-gray-600">--}}
{{--      {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}--}}
{{--    </div>--}}

{{--    @if (session('status') == 'verification-link-sent')--}}
{{--      <div class="mb-4 font-medium text-sm text-green-600">--}}
{{--        {{ __('A new verification link has been sent to the email address you provided during registration.') }}--}}
{{--      </div>--}}
{{--    @endif--}}

{{--    <div class="mt-4 flex items-center justify-between">--}}
{{--      <form method="POST" action="{{ route('verification.send') }}">--}}
{{--        @csrf--}}

{{--        <div>--}}
{{--          <x-button>--}}
{{--            {{ __('Resend Verification Email') }}--}}
{{--          </x-button>--}}
{{--        </div>--}}
{{--      </form>--}}

{{--      <form method="POST" action="{{ route('logout') }}">--}}
{{--        @csrf--}}

{{--        <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900">--}}
{{--          {{ __('Logout') }}--}}
{{--        </button>--}}
{{--      </form>--}}
{{--    </div>--}}
{{--  </x-auth-card>--}}
{{--</x-guest-layout>--}}

@extends('layouts.guest')

@section('content')
  <div class="login-box">
    <div class="login-logo">
      <img src="{{ asset('logo.png') }}" alt="Wall Street" class="brand-image img-circle elevation-3" style="opacity: .8; width: 15%">
      <a href="{{ route('welcome') }}"><b>Wall</b><small>Street</small></a>
    </div>

    <div class="card">
      <div class="card-body login-card-body">
        <p class="login-box-msg">Thanks for signing up! Before getting started,</p>
        <p class="login-box-msg">could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email,</p>
        <p class="login-box-msg">we will gladly send you another.</p>

        <form class="mb-2" method="POST" action="{{ route('verification.send') }}">
          @csrf
          <button type="submit" class="btn btn-success btn-block">
            Resend Verification Email
          </button>
        </form>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="btn btn-warning btn-block">
            Logout
          </button>
        </form>
      </div>
    </div>
  </div>
@endsection
