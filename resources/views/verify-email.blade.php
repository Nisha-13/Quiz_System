<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Your Email &mdash; QuizSphere</title>
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

      @if(session('message-success'))
      <div class="mb-5 flex items-center gap-2 bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 px-4 py-2.5 rounded-xl text-sm">
          <i class="bi bi-check-circle-fill"></i>
          <span class="font-medium">{{ session('message-success') }}</span>
      </div>
      @endif
      @if(session('message-error'))
      <div class="mb-5 flex items-center gap-2 bg-rose-500/10 border border-rose-500/30 text-rose-300 px-4 py-2.5 rounded-xl text-sm">
          <i class="bi bi-x-circle-fill"></i>
          <span class="font-medium">{{ session('message-error') }}</span>
      </div>
      @endif

      <div class="text-center mb-8">
          <div class="w-14 h-14 mx-auto rounded-2xl bg-gradient-to-br from-amber-500 to-orange-500 grid place-items-center shadow-xl shadow-amber-500/30 mb-4">
              <i class="bi bi-envelope-check-fill text-white text-2xl"></i>
          </div>
          <h2 class="text-2xl font-extrabold text-white">Check your inbox</h2>
          <p class="text-slate-400 text-sm mt-1">You need to verify your account before you can sign in.</p>
      </div>

      @if($email)
      <div class="rounded-2xl bg-white/5 border border-white/10 px-5 py-4 text-sm text-slate-300 flex items-center gap-3">
          <i class="bi bi-envelope-at text-teal-400 text-lg"></i>
          <span class="break-all">We sent a verification link to <strong class="text-white font-semibold">{{ $email }}</strong>.</span>
      </div>
      @else
      <div class="rounded-2xl bg-white/5 border border-white/10 px-5 py-4 text-sm text-slate-300">
          Enter the email address you signed up with and we will send you a new verification link.
      </div>
      @endif

      <form action="/resend-verification" method="POST" class="mt-6 space-y-4">
          @csrf
          <div>
              <label for="email" class="block text-sm font-semibold text-slate-300 mb-1.5">Email Address</label>
              <div class="relative">
                  <i class="bi bi-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-500"></i>
                  <input type="email" id="email" name="email" required placeholder="you@example.com" value="{{ old('email', $email) }}"
                         class="w-full bg-white/5 border {{ $errors->has('email') ? 'border-rose-500/70' : 'border-white/10' }} rounded-xl pl-11 pr-4 py-2.5 text-sm text-white placeholder-slate-500 focus:ring-2 focus:ring-teal-500 focus:border-transparent outline-none transition">
              </div>
              @error('email')<p class="mt-1.5 flex items-center gap-1 text-xs font-medium text-rose-400"><i class="bi bi-exclamation-circle-fill"></i> {{ $message }}</p>@enderror
          </div>

          <button type="submit"
                  class="w-full bg-gradient-to-r from-teal-600 to-emerald-600 text-white font-bold py-3 rounded-xl shadow-xl shadow-teal-600/30 hover:shadow-teal-500/50 hover:-translate-y-0.5 transition-all">
              <i class="bi bi-send mr-1"></i> Resend Verification Email
          </button>
      </form>

      <p class="text-center text-slate-400 text-sm mt-7">
          Already verified?
          <a href="/login" class="text-teal-400 hover:text-teal-300 font-semibold">Sign in</a>
      </p>
  </div>
</main>
</body>
</html>
