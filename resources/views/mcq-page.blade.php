<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Question {{ Session('currentQuiz')['currentMcq'] }} &mdash; QuizSphere</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  @vite('resources/css/app.css')
  @vite('resources/js/app.js')
  @php
    $qz = Session('currentQuiz');
    $progress = $qz ? ($qz['currentMcq'] / max($qz['totalMcq'], 1)) * 100 : 0;
  @endphp
</head>

<body class="bg-slate-950 min-h-screen font-sans relative overflow-x-hidden">
<x-user-navbar></x-user-navbar>

<div class="fixed inset-0 pointer-events-none">
    <div class="absolute -top-32 left-1/4 w-[500px] h-[500px] bg-teal-600/25 rounded-full blur-[120px]"></div>
    <div class="absolute bottom-0 right-0 w-[400px] h-[400px] bg-cyan-600/20 rounded-full blur-[120px]"></div>
</div>

<main class="relative z-10 min-h-screen px-4 pb-16 pt-24">
  <div class="max-w-3xl mx-auto">

    <!-- Progress Header -->
    <div class="flex items-center justify-between mb-3 text-sm font-semibold">
        <span class="text-slate-300">{{ $qz['quizName'] ? str_replace('-', ' ', $qz['quizName']) : '' }}</span>
        <span class="text-teal-300">{{ $qz['currentMcq'] }} / {{ $qz['totalMcq'] }}</span>
    </div>
    <div class="h-2.5 bg-white/10 rounded-full overflow-hidden">
        <div class="h-full bg-gradient-to-r from-teal-500 via-emerald-500 to-cyan-500 rounded-full transition-all duration-700" style="width: {{ $progress }}%"></div>
    </div>

    <!-- Question Card -->
    <div class="mt-6 bg-white/[0.07] backdrop-blur-2xl border border-white/10 rounded-3xl p-6 sm:p-10 shadow-2xl">

        <div class="flex items-center justify-between mb-8">
            <span class="inline-flex items-center gap-2 text-xs font-bold tracking-widest uppercase text-teal-400">
                <i class="bi bi-question-circle-fill"></i> Question {{ $qz['currentMcq'] }}
            </span>
            <span class="text-xs font-semibold text-slate-400">{{ round(((($qz['totalMcq'] - $qz['currentMcq']) + 1)) / $qz['totalMcq'] * 100) }}% remaining</span>
        </div>

        <h1 class="text-xl sm:text-2xl font-bold text-white leading-relaxed mb-8">
            {{ $mcqData->question }}
        </h1>

        <form action="/submit-next/{{ $mcqData->id }}" method="post" id="mcq-form">
            @csrf
            <input type="hidden" name="id" value="{{ $mcqData->id }}">

            <div class="space-y-3.5">
                @foreach(['a' => $mcqData->option_a, 'b' => $mcqData->option_b, 'c' => $mcqData->option_c, 'd' => $mcqData->option_d] as $key => $option)
                <label class="flex items-center gap-4 cursor-pointer rounded-2xl border border-white/10 bg-white/5 p-4 hover:border-teal-400/60 hover:bg-teal-500/10 has-[:checked]:border-teal-500 has-[:checked]:bg-teal-500/15 has-[:checked]:shadow-lg has-[:checked]:shadow-teal-600/20 transition-all duration-200">
                    <input type="radio" name="option" value="{{ $key }}" required class="sr-only">
                    <span class="shrink-0 w-9 h-9 rounded-xl bg-white/10 text-slate-400 grid place-items-center font-bold uppercase has-[:checked]:bg-teal-600 has-[:checked]:text-white transition">{{ $key }}</span>
                    <span class="text-slate-200 font-medium text-[15px] leading-relaxed">{{ $option }}</span>
                    <i class="bi bi-check-circle-fill text-teal-400 ml-auto opacity-0 has-[:checked]:opacity-100 transition"></i>
                </label>
                @endforeach
            </div>

            <button type="submit"
                    class="mt-9 w-full bg-gradient-to-r from-teal-600 to-emerald-600 text-white font-bold py-4 rounded-2xl shadow-xl shadow-teal-600/40 hover:shadow-teal-500/60 hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center gap-2">
            @if($qz['currentMcq'] == $qz['totalMcq'])
                <i class="bi bi-flag-fill"></i> Finish &amp; See Results
            @else
                <i class="bi bi-arrow-right"></i> Submit &amp; Next
            @endif
            </button>
        </form>
    </div>

    <p class="text-center text-xs text-slate-500 mt-5"><i class="bi bi-cursor"></i> Select an option to continue</p>
  </div>
</main>
</body>
</html>

