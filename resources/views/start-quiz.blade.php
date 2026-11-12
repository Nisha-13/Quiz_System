<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ str_replace('-', ' ', $quizName) }} &mdash; QuizSphere</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>

<body class="bg-slate-950 min-h-screen font-sans relative overflow-x-hidden">
<x-user-navbar></x-user-navbar>

<!-- Ambient glows -->
<div class="fixed inset-0 pointer-events-none">
    <div class="absolute -top-32 left-1/4 w-[500px] h-[500px] bg-teal-600/25 rounded-full blur-[120px]"></div>
    <div class="absolute bottom-0 right-0 w-[400px] h-[400px] bg-cyan-600/20 rounded-full blur-[120px]"></div>
</div>

@if(session('message-success'))
<div class="fixed top-20 inset-x-0 z-50 flex justify-center px-4">
    <div class="flex items-center gap-2 bg-white text-emerald-700 px-5 py-3 rounded-xl shadow-xl text-sm font-medium">
        <i class="bi bi-check-circle-fill text-emerald-500"></i>
        <span>{{ session('message-success') }}</span>
    </div>
</div>
@endif

<main class="relative z-10 flex flex-col items-center justify-center min-h-screen px-4 py-28">
    <div class="w-full max-w-2xl bg-white/[0.06] backdrop-blur-2xl border border-white/10 rounded-3xl p-8 sm:p-12 text-center shadow-2xl">

        <div class="w-20 h-20 mx-auto rounded-3xl bg-gradient-to-br from-teal-600 via-emerald-600 to-cyan-500 grid place-items-center shadow-2xl shadow-teal-600/40 mb-7">
            <i class="bi bi-lightning-charge-fill text-white text-3xl"></i>
        </div>

        <p class="text-xs font-bold tracking-[0.25em] text-teal-400 uppercase mb-2">Ready to begin</p>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight break-words">{{ str_replace('-', ' ', $quizName) }}</h1>

        <div class="grid grid-cols-2 gap-3 max-w-md mx-auto mt-8">
            <div class="rounded-2xl bg-white/5 border border-white/10 p-4">
                <p class="text-2xl font-extrabold text-white">{{ $quizCount }}</p>
                <p class="text-xs text-slate-400 font-medium mt-0.5">Questions</p>
            </div>
            <div class="rounded-2xl bg-white/5 border border-white/10 p-4">
                <p class="text-2xl font-extrabold text-white">&hellip;</p>
                <p class="text-xs text-slate-400 font-medium mt-0.5">Time Limit</p>
            </div>
        </div>

        <div class="mt-7 rounded-2xl bg-teal-500/10 border border-teal-500/20 px-5 py-3.5 text-sm text-teal-300 flex items-center gap-2.5 justify-center">
            <i class="bi bi-award-fill"></i>
            Score {{ $passMark ?? 70 }}% or higher to earn a certificate
        </div>

        @if($attempt)
            <div class="mt-7 rounded-2xl bg-amber-500/10 border border-amber-500/20 px-5 py-3.5 text-sm text-amber-300 flex items-center gap-2.5 justify-center">
                <i class="bi bi-hourglass-split"></i>
                You have an unfinished attempt &mdash; pick up where you left off
            </div>

            <a href="/resume-quiz/{{ $attempt->id }}"
               class="mt-9 w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-r from-amber-500 to-orange-500 text-white font-bold text-lg px-10 py-4 rounded-2xl shadow-2xl shadow-amber-500/30 hover:shadow-amber-500/50 hover:-translate-y-1 transition-all duration-300">
                <i class="bi bi-play-fill"></i> Continue Quiz
            </a>
        @elseif(Session('user'))
            <a href="/mcq/{{ Session('firstMCQ')->id }}/{{ $quizName }}"
               class="mt-9 w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-r from-teal-600 to-emerald-600 text-white font-bold text-lg px-10 py-4 rounded-2xl shadow-2xl shadow-teal-600/40 hover:shadow-teal-500/60 hover:-translate-y-1 transition-all duration-300">
                <i class="bi bi-rocket-takeoff-fill"></i> Start Quiz
            </a>
        @else
            <div class="mt-9 flex flex-col sm:flex-row gap-3 justify-center">
                <a href="/user-login-quiz"
                   class="inline-flex items-center justify-center gap-2 bg-white text-slate-900 font-bold px-8 py-4 rounded-2xl hover:bg-slate-200 transition">
                    <i class="bi bi-box-arrow-in-right"></i> Login to Start
                </a>
                <a href="/user-signup-quiz"
                   class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-teal-600 to-emerald-600 text-white font-bold px-8 py-4 rounded-2xl shadow-xl shadow-teal-600/40 hover:-translate-y-0.5 transition-all">
                    <i class="bi bi-person-plus-fill"></i> Create Free Account
                </a>
            </div>
        @endif

        <p class="mt-6 text-sm text-slate-500 flex items-center justify-center gap-2">
            <i class="bi bi-shield-check"></i> Good luck &mdash; you can review every answer afterwards!
        </p>
    </div>
</main>
</body>
</html>

