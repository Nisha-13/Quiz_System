<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password &mdash; QuizSphere</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>

<body class="bg-slate-950 min-h-screen font-sans relative overflow-x-hidden">
<x-user-navbar></x-user-navbar>

<div class="fixed inset-0 pointer-events-none">
    <div class="absolute -top-32 left-1/4 w-[500px] h-[500px] bg-teal-600/25 rounded-full blur-[120px]"></div>
    <div class="absolute bottom-0 right-0 w-[400px] h-[400px] bg-cyan-600/20 rounded-full blur-[120px]"></div>
</div>

<main class="relative z-10 min-h-screen flex items-center justify-center px-4 py-28">
  <div class="w-full max-w-md bg-white/[0.06] backdrop-blur-2xl border border-white/10 rounded-3xl p-8 sm:p-10 shadow-2xl">
    @if(session('message-error'))
    <div class="mb-5 flex items-center gap-2 bg-rose-500/10 border border-rose-500/30 text-rose-300 px-4 py-2.5 rounded-xl text-sm">
        <i class="bi bi-x-circle-fill"></i>
        <span class="font-medium">{{ session('message-error') }}</span>
    </div>
    @endif

    <div class="text-center mb-8">
      <div class="w-14 h-14 mx-auto rounded-2xl bg-gradient-to-br from-teal-600 to-emerald-600 grid place-items-center shadow-xl shadow-teal-600/40 mb-4">
          <i class="bi bi-key-fill text-white text-2xl"></i>
      </div>
      <h2 class="text-2xl font-extrabold text-white">Forgot password?</h2>
      <p class="text-slate-400 text-sm mt-1">Enter your email and we'll send you a reset link</p>
    </div>

    <form action="/user-forgot-password" method="POST" class="space-y-5">
      @csrf
      <div>
        <label for="email" class="block text-sm font-semibold text-slate-300 mb-1.5">Email Address</label>
        <div class="relative">
            <i class="bi bi-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-500"></i>
            <input type="email" id="email" name="email" required placeholder="you@example.com" value="{{ old('email') }}"
                   class="w-full bg-white/5 border {{ $errors->has('email') ? 'border-rose-500/70' : 'border-white/10' }} rounded-xl pl-11 pr-4 py-2.5 text-sm text-white placeholder-slate-500 focus:ring-2 focus:ring-teal-500 focus:border-transparent outline-none transition">
        </div>
        @error('email')<p class="mt-1.5 flex items-center gap-1 text-xs font-medium text-rose-400"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}</p>@enderror
      </div>

      <button type="submit"
              class="w-full bg-gradient-to-r from-teal-600 to-emerald-600 text-white font-bold py-3 rounded-xl shadow-xl shadow-teal-600/30 hover:shadow-teal-500/50 hover:-translate-y-0.5 transition-all">
          <i class="bi bi-send mr-1"></i> Send Reset Link
      </button>
    </form>

    <p class="text-center text-slate-400 text-sm mt-7">
      <a href="/login" class="text-teal-400 hover:text-teal-300 font-semibold"><i class="bi bi-arrow-left mr-1"></i>Back to login</a>
    </p>
  </div>
</main>
</body>
</html>

