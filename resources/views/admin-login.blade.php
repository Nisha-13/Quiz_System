<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #007bff, #00b4d8);
            height: 100vh;
        }
        .login-card {
            width: 380px;
            border-radius: 20px;
            background: #fff;
            box-shadow: 0 0 20px rgba(0,0,0,0.15);
        }
        .btn-login {
            border-radius: 30px;
        }
        .title {
            font-weight: 700;
            color: #007bff;
        }
    </style>
</head>
<body>

<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="login-card p-4">
        <h3 class="text-center mb-4 title">Admin Login</h3>
        <form method="POST" action="/admin-login">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">Admin Name</label>
                 @error('user')
                <div class="text-danger">{{$message}}</div>
                @enderror
                <input type="name" name="name" class="form-control" placeholder="Enter name">
                @error('name')
                <div class="text-danger">{{$message}}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter password">
                @error('password')
                <div class="text-danger">{{$message}}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary w-100 btn-login mt-2">Login</button>
        </form>

        <p class="text-center text-muted mt-3 mb-0" style="font-size: 14px;">© 2025 Admin Portal</p>
    </div>
</div>

</body>
</html>
