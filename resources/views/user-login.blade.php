<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Login — Quiz System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>

<body class="bg-gray-100 font-sans">
<x-user-navbar></x-user-navbar>

<!-- ✅ Login Card -->
<div class="flex justify-center mt-24 mb-20 px-4">
  <div class="bg-white shadow-xl rounded-2xl w-full max-w-md px-8 py-10">
      @if(session('message-error'))
<div class="flex justify-center my-3">
    <div class="p-3 rounded-lg bg-red-100 border border-red-300 text-red-700 text-sm font-medium flex items-center gap-2 shadow-md max-w-md w-full text-center">
        <i class="bi bi-check-circle-fill text-red-600 text-lg"></i>
        <span class="font-bold">{{ session('message-error') }}</span>
    </div>
</div>
@endif
@if(session('message-success'))
<div class="flex justify-center my-3">
    <div class="p-3 rounded-lg bg-green-100 border border-green-300 text-green-700 text-sm font-medium flex items-center gap-2 shadow-md max-w-md w-full text-center">
        <i class="bi bi-check-circle-fill text-green-600 text-lg"></i>
        <span class="font-bold">{{ session('message-success') }}</span>
    </div>
</div>
@endif
    <div class="text-center mb-6">
      <i class="bi bi-box-arrow-in-right text-teal-600 text-5xl"></i>
      <h2 class="text-2xl font-bold text-teal-700 mt-2">Welcome Back</h2>
      <p class="text-gray-500 text-sm mt-1">Login to continue your quiz journey</p>
    </div>

    <!-- ✅ Login Form -->
    <form action="/user-login" method="POST" class="space-y-5">
      @csrf

      <!-- Email -->
      <div>
        <label for="email" class="block text-gray-700 font-medium mb-1">Email</label>
        <input type="email" id="email" name="email" required
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition">
      </div>

      <!-- Password + Forgot Link -->
      <div>
        <div class="flex justify-between items-center mb-1">
          <label for="password" class="text-gray-700 font-medium">Password</label>
          <a href="/forgot-password" class="text-sm text-teal-600 hover:text-teal-800 font-medium">
            Forgot Password?
          </a>
        </div>
        <input type="password" id="password" name="password" required
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition">
      </div>

      <!-- Login Button -->
      <button type="submit"
              class="w-full bg-gradient-to-r from-teal-600 to-cyan-500 text-white py-2 rounded-lg font-semibold hover:from-teal-700 hover:to-cyan-600 transform hover:-translate-y-0.5 transition">
        Login
      </button>
    </form>

    <!-- Signup Link -->
    <p class="text-center text-gray-600 text-sm mt-5">
      Don’t have an account? 
      <a href="/user-signup" class="text-teal-600 hover:text-teal-800 font-medium">Sign Up</a>
    </p>
  </div>
</div>

<x-user-footer></x-user-footer>
</body>
</html>
