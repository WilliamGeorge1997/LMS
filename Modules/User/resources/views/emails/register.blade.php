@extends('user::emails.layout', ['title' => 'Account Verification'])

@section('content')
    <h2 style="margin: 0 0 14px 0; color: #0f172a; font-size: 20px; font-weight: 700; line-height: 1.3;">
        Account Verification
    </h2>

    <p style="margin: 0 0 10px 0; font-size: 15px; color: #475569; line-height: 1.6;">
        Hello,
    </p>

    <p style="margin: 0 0 24px 0; font-size: 15px; color: #475569; line-height: 1.6;">
        Thank you for registering. Please use the verification code below to verify your email address and activate your account:
    </p>

    <!-- Large Bold Verification Code Box -->
    <div style="margin: 30px 0; text-align: center;">
        <div style="display: inline-block; background-color: #eff6ff; border: 2px dashed #93c5fd; border-radius: 12px; padding: 18px 36px; text-align: center;">
            <div style="font-size: 11px; font-weight: 700; color: #3b82f6; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 6px;">
                Verification Code
            </div>
            <div style="font-size: 40px; font-weight: 800; letter-spacing: 12px; color: #1d4ed8; font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, 'Courier New', monospace; line-height: 1.1; padding-left: 12px;">
                {{ $verifyCode }}
            </div>
        </div>
    </div>

    <!-- Security Alert Box -->
    <div style="background-color: #f8fafc; border-left: 4px solid #3b82f6; border-radius: 4px; padding: 12px 16px; margin: 26px 0 18px 0;">
        <p style="margin: 0; font-size: 13px; color: #475569; line-height: 1.5;">
            <strong style="color: #1e293b;">Security Notice:</strong> This code is single-use and will expire shortly. Never share this code with anyone.
        </p>
    </div>

    <p style="margin: 0; font-size: 13px; color: #94a3b8; line-height: 1.5;">
        If you did not attempt to register an account, you can safely disregard this email.
    </p>
@endsection
