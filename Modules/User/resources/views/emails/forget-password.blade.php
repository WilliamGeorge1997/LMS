@extends('user::emails.layout', ['title' => 'Password Reset'])

@section('content')
    <h2 style="margin: 0 0 14px 0; color: #0f172a; font-size: 20px; font-weight: 700; line-height: 1.3;">
        Password Reset Request
    </h2>

    <p style="margin: 0 0 10px 0; font-size: 15px; color: #475569; line-height: 1.6;">
        Hello,
    </p>

    <p style="margin: 0 0 24px 0; font-size: 15px; color: #475569; line-height: 1.6;">
        We received a request to reset the password for your account. Please use the verification code below to proceed with setting a new password:
    </p>

    <!-- Large Bold Verification Code Box -->
    <div style="margin: 30px 0; text-align: center;">
        <div style="display: inline-block; background-color: #fef2f2; border: 2px dashed #fca5a5; border-radius: 12px; padding: 18px 36px; text-align: center;">
            <div style="font-size: 11px; font-weight: 700; color: #ef4444; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 6px;">
                Reset Code
            </div>
            <div style="font-size: 40px; font-weight: 800; letter-spacing: 12px; color: #dc2626; font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, 'Courier New', monospace; line-height: 1.1; padding-left: 12px;">
                {{ $verifyCode }}
            </div>
        </div>
    </div>

    <!-- Security Alert Box -->
    <div style="background-color: #f8fafc; border-left: 4px solid #ef4444; border-radius: 4px; padding: 12px 16px; margin: 26px 0 18px 0;">
        <p style="margin: 0; font-size: 13px; color: #475569; line-height: 1.5;">
            <strong style="color: #1e293b;">Security Notice:</strong> This code is single-use and will expire shortly. If you did not request a password reset, your account is safe and you can safely ignore this email.
        </p>
    </div>

    <p style="margin: 0; font-size: 13px; color: #94a3b8; line-height: 1.5;">
        Never share this verification code with anyone, including our support team.
    </p>
@endsection
