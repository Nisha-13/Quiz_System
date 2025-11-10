<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password — Quiz System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9fafb; padding: 30px;">
    <div style="max-width: 500px; margin: auto; background: #ffffff; border-radius: 10px; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
        
        <!-- Header -->
        <div style="text-align: center; margin-bottom: 20px;">
            <i style="font-size: 50px; color: #0d9488;" class="bi bi-shield-lock"></i>
            <h2 style="color: #0d9488; margin-top: 10px;">Reset Your Password</h2>
        </div>

        <!-- Message -->
        <p style="font-size: 16px; color: #333;">
            Hi there 👋,<br><br>
            We received a request to reset your password for your <strong>Quiz System</strong> account.
            Click the button below to set a new password.
        </p>

        <!-- Reset Button -->
        <p style="text-align: center; margin: 25px 0;">
            <a href="{{ $link }}" 
               style="display:inline-block; background-color:#0d9488; color:white; padding:12px 24px; border-radius:6px; text-decoration:none; font-weight:bold;">
                Reset Password
            </a>
        </p>

        <!-- Link in case button doesn't work -->
        <p style="font-size: 14px; color: #555;">
            If the button doesn’t work, copy and paste this link into your browser:
            <br>
            <a href="{{ $link }}" style="color:#0d9488;">{{ $link }}</a>
        </p>

        <!-- Footer -->
        <p style="font-size: 13px; color: #999; margin-top: 30px;">
            If you didn’t request a password reset, you can safely ignore this email.  
            Your password will remain unchanged.
        </p>

        <hr style="border:none; border-top:1px solid #eee; margin-top:25px;">
        <p style="text-align:center; font-size:13px; color:#888;">© {{ date('Y') }} Quiz System — All Rights Reserved</p>
    </div>
</body>
</html>