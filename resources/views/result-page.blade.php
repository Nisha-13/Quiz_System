<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quiz Result &mdash; QuizSphere</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    @php
        $total = count($resultData);
        $pct = $total > 0 ? round(($correctAnswers / $total) * 100) : 0;
        $passed = $pct >= ($passMark ?? 70);
        // SVG ring
        $radius = 56;
        $circumference = 2 * pi() * $radius;
    @endphp
</head>

<body class="bg-slate-50 font-sans min-h-screen flex flex-col">
<x-user-navbar></x-user-navbar>

<main class="flex-grow pt-28 pb-8 px-4">
    <div class="max-w-5xl mx-auto space-y-8">

        <!-- &hellip; Score Hero -->
        <div class="relative overflow-hidden rounded-3xl border border-teal-100 bg-white shadow-xl shadow-teal-500/5 p-8 sm:p-10">
            <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full bg-gradient-to-br from-teal-500/10 to-cyan-500/10"></div>

            <div class="relative flex flex-col sm:flex-row items-center gap-8 justify-between">
                <div class="flex items-center gap-6">
                    <!-- Score Ring -->
                    <div class="relative w-[136px] h-[136px] shrink-0">
                        <svg class="w-full h-full -rotate-90" viewBox="0 0 128 128">
                            <circle cx="64" cy="64" r="{{ $radius }}" fill="none" stroke="#e2e8f0" stroke-width="10"/>
                            <circle cx="64" cy="64" r="{{ $radius }}" fill="none"
                                    stroke="{{ $passed ? '#10b981' : ($pct >= 40 ? '#f59e0b' : '#ef4444') }}"
                                    stroke-width="10" stroke-linecap="round"
                                    stroke-dasharray="{{ $circumference }}"
                                    stroke-dashoffset="{{ $circumference - ($circumference * $pct / 100) }}"/>
                        </svg>
                        <div class="absolute inset-0 grid place-items-center">
                            <div class="text-center">
                                <p class="text-3xl font-extrabold text-slate-900">{{ $pct }}%</p>
                                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Score</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-teal-600 mb-1">{{ $quizName ?? 'Quiz' }}</p>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                            {{ $passed ? 'Congratulations! 🎉' : ($pct >= 40 ? 'Almost there!' : 'Keep practicing!') }}
                        </h1>
                        <p class="text-slate-500 mt-1.5 text-sm sm:text-base">You answered <span class="font-bold text-slate-700">{{ $correctAnswers }}</span> out of <span class="font-bold text-slate-700">{{ $total }}</span> questions correctly.</p>
                    </div>
                </div>

                @if($passed)
                    <a href="/certificate/{{ Session('currentQuiz')['recordId'] }}"
                       class="inline-flex items-center gap-2.5 bg-gradient-to-r from-emerald-600 to-teal-500 text-white font-bold px-6 py-3.5 rounded-2xl shadow-xl shadow-emerald-600/25 hover:shadow-emerald-500/40 hover:-translate-y-0.5 transition-all whitespace-nowrap">
                        <i class="bi bi-award-fill text-lg"></i> View Certificate
                    </a>
                @endif
            </div>
        </div>

        <!-- &hellip; Review -->
        <div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
                <h2 class="text-lg font-bold text-slate-900">Answer Review</h2>
                <span class="text-xs font-bold px-3 py-1.5 rounded-full bg-slate-100 text-slate-500">{{ $total }} questions</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">#</th>
                            <th class="px-6 py-4">Question</th>
                            <th class="px-6 py-4">Your Answer</th>
                            <th class="px-6 py-4">Correct Answer</th>
                            <th class="px-6 py-4 text-center">Result</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($resultData as $key => $item)
                            @php
                                $options = [
                                    'a' => $item->mcq->option_a ?? 'N/A',
                                    'b' => $item->mcq->option_b ?? 'N/A',
                                    'c' => $item->mcq->option_c ?? 'N/A',
                                    'd' => $item->mcq->option_d ?? 'N/A',
                                ];
                                $userAnswerText = $options[strtolower($item->select_answer)] ?? 'No answer';
                                $correctAnswerKey = strtolower($item->mcq->correct_ans ?? '');
                                $correctAnswerText = $options[$correctAnswerKey] ?? 'N/A';
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-6 py-4 font-bold text-slate-400">{{ $key + 1 }}</td>
                                <td class="px-6 py-4 text-slate-800 font-medium min-w-[220px]">{{ $item->mcq->question ?? 'N/A' }}</td>
                                @if($item->is_correct)
                                    <td class="px-6 py-4 text-emerald-700 font-medium">{{ $userAnswerText }}</td>
                                    <td class="px-6 py-4 text-slate-600">{{ $correctAnswerText }}</td>
                                @else
                                    <td class="px-6 py-4 text-rose-600 font-medium line-through decoration-rose-300">{{ $userAnswerText }}</td>
                                    <td class="px-6 py-4 text-emerald-700 font-semibold">{{ $correctAnswerText }}</td>
                                @endif
                                <td class="px-6 py-4 text-center">
                                    @if($item->is_correct)
                                        <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-600"><i class="bi bi-check-lg"></i> Correct</span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-full bg-rose-50 text-rose-600"><i class="bi bi-x-lg"></i> Wrong</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-wrap justify-center gap-3">
            <a href="/user-details"
               class="inline-flex items-center gap-2 bg-slate-900 text-white font-semibold px-6 py-3 rounded-xl hover:bg-slate-800 transition">
                <i class="bi bi-journal-check"></i> My Attempts
            </a>
            <a href="/categories"
               class="inline-flex items-center gap-2 bg-white border border-slate-200 text-slate-700 font-semibold px-6 py-3 rounded-xl hover:bg-slate-50 hover:border-teal-300 hover:text-teal-600 transition">
                <i class="bi bi-collection"></i> Browse More Quizzes
            </a>
        </div>
    </div>
</main>

<x-user-footer></x-user-footer>
</body>
</html>

