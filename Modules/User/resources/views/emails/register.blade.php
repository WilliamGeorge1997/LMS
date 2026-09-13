@extends('user::emails.layout')

@section('content')
    <h5 class="card-title text-dark fw-bold mb-3">Account Verification</h5>

    <p class="mb-2">Hello,</p>

    <p class="mb-4">
        Thank you for registering. Please use the verification code below to verify your email address and activate your account.
    </p>

    <div class="card bg-light border-secondary-subtle text-center mb-4 py-3">
        <div class="card-body py-2">
            <span class="display-6 fw-bold text-primary font-monospace tracking-wide">{{ $verifyCode }}</span>
        </div>
    </div>

    <div class="alert alert-primary py-2 px-3 small mb-4" role="alert">
        <strong>Security Notice:</strong> This code is single-use and will expire shortly. Never share this code with anyone.
    </div>

    <p class="text-muted small mb-0">
        If you did not attempt to register an account, please disregard this email.
    </p>
@endsection
