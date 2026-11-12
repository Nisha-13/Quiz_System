<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>My Attempts " QuizSphere</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  @vite('resources/css/app.css')
  @vite('resources/js/app.js')
</head>
<body class="bg-slate-100 min-h-screen flex flex-col font-sans">

  <x-user-navbar></x-user-navbar>

  <main class="flex-grow pt-24 pb-12 px-4">
    <div class="max-w-5xl mx-auto">

      @if(session('message-success'))
      <div class="mb-6 flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3 rounded-xl text-sm font-medium">
          <i class="bi bi-check-circle-fill text-emerald-500"></i>
          <span>{{ session('message-success') }}</span>
      </div>
      @endif
      @if(session('message-error'))
      <div class="mb-6 flex items-center gap-2 bg-rose-50 border border-rose-200 text-rose-700 px-5 py-3 rounded-xl text-sm font-medium">
          <i class="bi bi-exclamation-circle-fill text-rose-500"></i>
          <span>{{ session('message-error') }}</span>
      </div>
      @endif

      <!-- Profile hero -->
      <div class="mb-8 relative overflow-hidden rounded-3xl bg-gradient-to-r from-teal-700 via-teal-600 to-emerald-600 p-7 sm:p-9 text-white shadow-xl shadow-teal-600/25">
        <div class="absolute -right-14 -top-14 w-56 h-56 rounded-full bg-white/10"></div>
        <div class="absolute right-24 bottom-0 w-24 h-24 rounded-full bg-emerald-400/20"></div>
        <div class="relative flex items-center gap-5 flex-wrap">
          <span class="w-16 h-16 rounded-2xl bg-white/15 backdrop-blur grid place-items-center text-2xl font-extrabold border border-white/20 shadow-inner">{{ strtoupper(substr($userName, 0, 1)) }}</span>
          <div class="flex-grow">
            <p class="text-teal-100 text-xs font-bold uppercase tracking-widest mb-1">Dashboard</p>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">{{ $userName }}</h1>
            <p class="text-teal-100/90 text-sm mt-1"><i class="bi bi-journal-check mr-1"></i>{{ $quizRecord->count() }} {{ Str::plural('attempt', $quizRecord->count()) }} recorded</p>
          </div>
          <a href="/categories" class="inline-flex items-center gap-2 bg-white/15 border border-white/25 backdrop-blur text-white font-semibold text-sm px-4 py-2.5 rounded-xl hover:bg-white/25 transition">
            <i class="bi bi-plus-lg"></i> New Quiz
          </a>
        </div>
      </div>

      <!-- Attempt cards -->
      <div class="flex items-center gap-3 mb-5 mt-10">
        <span class="w-10 h-1.5 rounded-full bg-teal-600"></span>
        <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Attempt History</h2>
        <span class="text-xs font-bold px-3 py-1.5 rounded-full bg-teal-100 text-teal-700">{{ $quizRecord->count() }}</span>
      </div>

      @foreach($quizRecord as $key => $record)
      @php
        $s = $scores[$record->id] ?? null;
        $total = $s['total'] ?? 0;
        $correct = $s['correct'] ?? 0;
        $pct = $s['pct'] ?? 0;
        $done = $record->status == 2;
        $passed = $done && $pct >= $passMark;
      @endphp
      <div class="mb-4 group relative overflow-hidden rounded-2xl bg-white border border-slate-200 shadow-sm hover:shadow-lg hover:border-teal-300 transition-all duration-300">
        <div class="absolute left-0 top-0 bottom-0 w-1.5 {{ $done ? 'bg-gradient-to-b from-teal-500 to-emerald-500' : 'bg-gradient-to-b from-amber-400 to-orange-500' }}"></div>
        <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-6 p-5 pl-6 sm:pl-8">
          <!-- index -->
          <div class="hidden sm:grid w-12 h-12 rounded-xl bg-slate-100 border border-slate-200 place-items-center font-extrabold text-slate-500 shrink-0 group-hover:bg-teal-50 group-hover:text-teal-600 group-hover:border-teal-200 transition">{{ $key + 1 }}</div>

          <!-- title + progress -->
          <div class="flex-grow min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
              <h3 class="font-bold text-slate-900 truncate">{{ $record->quiz_name ?? 'Unknown Quiz' }}</h3>
              @if($done)
                <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200"><i class="bi bi-check-circle-fill"></i> Completed</span>
              @else
                <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-full bg-amber-50 text-amber-600 border border-amber-200"><i class="bi bi-hourglass-split"></i> In Progress</span>
              @endif
              @if($done && ! $passed)
                <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2.5 py-1 rounded-full bg-rose-50 text-rose-600 border border-rose-200"><i class="bi bi-x-circle-fill"></i> {{ $passMark }}% needed</span>
              @endif
            </div>

            @if($total > 0)
              <div class="mt-3 flex items-center gap-3">
                <div class="flex-grow h-2 bg-slate-100 rounded-full overflow-hidden max-w-xs">
                  <div class="h-full rounded-full transition-all duration-700 {{ $done ? ($pct >= $passMark ? 'bg-gradient-to-r from-emerald-500 to-teal-500' : ($pct >= 40 ? 'bg-gradient-to-r from-amber-400 to-orange-400' : 'bg-gradient-to-r from-rose-400 to-rose-500')) : 'bg-gradient-to-r from-amber-400 to-orange-400' }}" style="width: {{ $pct }}%"></div>
                </div>
                <span class="text-xs font-bold {{ $pct >= $passMark ? 'text-emerald-600' : ($pct >= 40 ? 'text-amber-600' : 'text-rose-500') }} whitespace-nowrap">{{ $correct }}/{{ $total }} &middot; {{ $pct }}%</span>
              </div>
            @else
              <p class="mt-3 text-xs font-medium text-slate-400"><i class="bi bi-play-circle mr-1"></i>No questions answered yet</p>
            @endif
          </div>

          <!-- actions -->
          <div class="shrink-0 flex gap-2">
            @if($passed)
              <a href="/certificate/{{ $record->id }}"
                 class="inline-flex items-center gap-1.5 bg-gradient-to-r from-teal-600 to-emerald-600 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-md shadow-teal-600/25 hover:shadow-lg hover:-translate-y-0.5 transition-all">
                <i class="bi bi-award-fill"></i> Certificate
              </a>
              <a href="/download-certificate/{{ $record->id }}/certificate.pdf"
                 class="inline-flex items-center gap-1.5 bg-white border border-slate-200 text-slate-600 text-xs font-bold px-3.5 py-2.5 rounded-xl hover:border-teal-300 hover:text-teal-700 transition">
                <i class="bi bi-download"></i> PDF
              </a>
            @elseif($done)
              <a href="/start-quiz/{{ $record->quiz_id }}/{{ $record->quiz_name }}"
                 class="inline-flex items-center gap-1.5 bg-gradient-to-r from-amber-500 to-orange-500 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-md shadow-amber-500/25 hover:shadow-lg hover:-translate-y-0.5 transition-all">
                <i class="bi bi-arrow-repeat"></i> Retake
              </a>
            @else
              <a href="/resume-quiz/{{ $record->id }}"
                 class="inline-flex items-center gap-1.5 bg-gradient-to-r from-amber-500 to-orange-500 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-md shadow-amber-500/25 hover:shadow-lg hover:-translate-y-0.5 transition-all">
                <i class="bi bi-play-fill"></i> Continue Quiz
              </a>
            @endif
          </div>
        </div>
      </div>
      @endforeach

      @if($quizRecord->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 py-16 text-center text-slate-400">
          <i class="bi bi-journal-x text-4xl block mb-3"></i>
          <p class="font-medium mb-4">You haven't attempted any quizzes yet.</p>
          <a href="/categories" class="inline-flex items-center gap-2 bg-gradient-to-r from-teal-600 to-emerald-600 text-white font-semibold px-6 py-2.5 rounded-xl shadow-lg shadow-teal-600/25 hover:shadow-teal-600/40 transition-all">Start Your First Quiz <i class="bi bi-arrow-right"></i></a>
        </div>
      @endif

      <div class="mt-8 text-center">
        <a href="/" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-teal-600 transition"><i class="bi bi-arrow-left"></i> Back to Home</a>
      </div>
    </div>
  </main>

  <x-user-footer></x-user-footer>
</body>
</html>
