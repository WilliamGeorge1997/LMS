@extends('user::emails.layout')

@section('content')
    <h5 class="card-title text-dark fw-bold mb-3">Password Reset Request</h5>

    <p class="mb-2">Hello,</p>

    <p class="mb-4">
        We received a request to reset the password for your account. Please use the verification code below to proceed with setting a new password.
    </p>

    <div class="card bg-light border-secondary-subtle text-center mb-4 py-3">
        <div class="card-body py-2">
            <span class="display-6 fw-bold text-danger font-monospace tracking-wide">{{ $verifyCode }}</span>
        </div>
    </div>

    <div class="alert alert-warning py-2 px-3 small mb-4" role="alert">
        <strong>Security Notice:</strong> This code is single-use and will expire shortly. If you did not request this password reset, your account is safe and you can safely ignore this email.
    </div>

    <p class="text-muted small mb-0">
        Never share this verification code with anyone, including our support team.
    </p>
@endsection
