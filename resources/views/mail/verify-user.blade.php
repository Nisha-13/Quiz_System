<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">


    {{-- Tailwind + Vite --}}
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 30px;">
    <div style="max-width: 500px; margin: auto; background: #ffffff; border-radius: 8px; padding: 25px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
        <h2 style="color: #2563eb;">Welcome to Our Platform!</h2>
        <p style="font-size: 16px; color: #333;">
            Thank you for signing up. Please verify your email address to activate your account.
        </p>
        <p style="margin-top: 20px;">
            <a href="{{ $link }}" 
               style="display:inline-block; background-color:#2563eb; color:white; padding:10px 18px; border-radius:6px; text-decoration:none; font-weight:bold;">
                Verify My Account
            </a>
        </p>
        <p style="font-size: 14px; color: #666; margin-top: 25px;">
            Or copy and paste this link into your browser:<br>
            <a href="{{ $link }}">{{ $link }}</a>
        </p>
        <p style="font-size: 13px; color: #999; margin-top: 20px;">If you didn’t request this email, please ignore it.</p>
    </div>
</body>
</html>
