<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login &mdash; QuizSphere</title>
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
      @if(session('message-success'))
      <div class="mb-5 flex items-center gap-2 bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 px-4 py-2.5 rounded-xl text-sm">
          <i class="bi bi-check-circle-fill"></i>
          <span class="font-medium">{{ session('message-success') }}</span>
      </div>
      @endif

      <div class="text-center mb-8">
          <div class="w-14 h-14 mx-auto rounded-2xl bg-gradient-to-br from-teal-600 to-emerald-600 grid place-items-center shadow-xl shadow-teal-600/40 mb-4">
              <i class="bi bi-box-arrow-in-right text-white text-2xl"></i>
          </div>
          <h2 class="text-2xl font-extrabold text-white">Welcome back</h2>
          <p class="text-slate-400 text-sm mt-1">One account for everything &mdash; users and admins log in here</p>
      </div>

      <form action="/login" method="POST" class="space-y-5">
        @csrf
        <div>
          <label for="account" class="block text-sm font-semibold text-slate-300 mb-1.5">Email or username</label>
          <div class="relative">
            <i class="bi bi-person absolute left-4 top-1/2 -translate-y-1/2 text-slate-500"></i>
            <input type="text" id="account" name="account" required autocomplete="username" placeholder="you@example.com or admin"
                   value="{{ old('account') }}"
                   class="w-full bg-white/5 border {{ $errors->has('account') ? 'border-rose-500/70' : 'border-white/10' }} rounded-xl pl-11 pr-4 py-2.5 text-sm text-white placeholder-slate-500 focus:ring-2 focus:ring-teal-500 focus:border-transparent outline-none transition">
          </div>
          @error('account')
          <p class="mt-1.5 flex items-center gap-1 text-xs font-medium text-rose-400">
            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
          </p>
          @enderror
        </div>

        <div>
          <div class="flex justify-between items-center mb-1.5">
            <label for="password" class="text-sm font-semibold text-slate-300">Password</label>
            <a href="/forgot-password" class="text-xs font-semibold text-teal-400 hover:text-teal-300 transition">Forgot?</a>
          </div>
          <div class="relative">
            <i class="bi bi-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-500"></i>
            <input type="password" id="password" name="password" required autocomplete="current-password" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                   class="w-full bg-white/5 border {{ $errors->has('password') ? 'border-rose-500/70' : 'border-white/10' }} rounded-xl pl-11 pr-4 py-2.5 text-sm text-white placeholder-slate-500 focus:ring-2 focus:ring-teal-500 focus:border-transparent outline-none transition">
          </div>
          @error('password')
          <p class="mt-1.5 flex items-center gap-1 text-xs font-medium text-rose-400">
            <i class="bi bi-exclamation-circle-fill"></i> {{ $message }}
          </p>
          @enderror
        </div>

        <button type="submit"
                class="w-full bg-gradient-to-r from-teal-600 to-emerald-600 text-white font-bold py-3 rounded-xl shadow-xl shadow-teal-600/30 hover:shadow-teal-500/50 hover:-translate-y-0.5 transition-all">
          Login
        </button>
      </form>

      @if(Session('pending_verification_email'))
      <div class="mt-5 rounded-xl bg-amber-500/10 border border-amber-500/30 px-4 py-3 text-xs text-amber-200 flex items-start gap-2">
          <i class="bi bi-envelope-exclamation mt-0.5"></i>
          <span>
              Still waiting to verify <strong>{{ Session('pending_verification_email') }}</strong>?
              <a href="/verify-email" class="font-bold underline underline-offset-2 hover:text-amber-100">Resend the link</a>
          </span>
      </div>
      @endif

      <p class="text-center text-slate-400 text-sm mt-7">
          New to QuizSphere?
          <a href="/user-signup" class="text-teal-400 hover:text-teal-300 font-semibold">Create a free account</a>
      </p>
  </div>
</main>
</body>
</html>
