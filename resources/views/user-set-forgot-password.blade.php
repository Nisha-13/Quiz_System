<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Set New Password — Quiz System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>

<body class="bg-gray-100 font-sans">
<x-user-navbar></x-user-navbar>

<!-- ✅ Reset Password Card -->
<div class="flex justify-center mt-24 mb-20 px-4">
  <div class="bg-white shadow-xl rounded-2xl w-full max-w-md px-8 py-10">
    <div class="text-center mb-6">
      <i class="bi bi-key text-teal-600 text-5xl"></i>
      <h2 class="text-2xl font-bold text-teal-700 mt-2">Set a New Password</h2>
      <p class="text-gray-500 text-sm mt-1">Enter and confirm your new password</p>
    </div>

    <!-- ✅ Reset Password Form -->
    <form action="/user-set-forgot-password" method="POST" class="space-y-5">
      @csrf

      <!-- Hidden Email Field -->
      <input type="hidden" name="email" value="{{ $email }}">

      <!-- New Password -->
      <div>
        <label for="password" class="block text-gray-700 font-medium mb-1">New Password</label>
        <input type="password" id="password" name="password" required
               placeholder="Enter new password"
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition">
      </div>
      @error('password')
      <div class="text-red-500">{{$message}}</div>
      @enderror

      <!-- Confirm Password -->
      <div>
        <label for="password_confirmation" class="block text-gray-700 font-medium mb-1">Confirm Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required
               placeholder="Re-enter new password"
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition">
      </div>

      <!-- Submit Button -->
      <button type="submit"
              class="w-full bg-gradient-to-r from-teal-600 to-cyan-500 text-white py-2 rounded-lg font-semibold hover:from-teal-700 hover:to-cyan-600 transform hover:-translate-y-0.5 transition">
        Update Password
      </button>
    </form>

    <!-- Back to Login -->
    <p class="text-center text-gray-600 text-sm mt-5">
      Remembered your password?
      <a href="/user-login" class="text-teal-600 hover:text-teal-800 font-medium">Login</a>
    </p>
  </div>
</div>

<x-user-footer></x-user-footer>
</body>
</html>
