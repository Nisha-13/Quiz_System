<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ str_replace('-', ' ', $category) }} Quizzes &mdash; QuizSphere</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>

<body class="bg-slate-50 font-sans min-h-screen flex flex-col">
<x-user-navbar></x-user-navbar>

<main class="flex-grow">
    <section class="pt-28 pb-8 px-4 text-center" style="background: radial-gradient(ellipse 80% 60% at 50% -10%, rgba(99,102,241,0.13), transparent), #f8fafc;">
        <div class="inline-flex items-center gap-2 text-sm text-slate-500 mb-3">
            <a href="/categories" class="hover:text-teal-600 transition flex items-center gap-1"><i class="bi bi-arrow-left"></i> Categories</a>
            <i class="bi bi-chevron-right text-xs"></i>
            <span class="font-semibold text-slate-700">{{ str_replace('-', ' ', $category) }}</span>
        </div>
        <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-slate-900">{{ str_replace('-', ' ', $category) }}</h1>
        <p class="text-slate-500 mt-3">{{ $quizData->total() }} {{ Str::plural('quiz', $quizData->count()) }} in this category</p>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-6">
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse($quizData as $item)
                <div class="group bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:shadow-xl hover:border-teal-200 hover:-translate-y-1 transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-teal-500 to-emerald-500 grid place-items-center shadow-md shadow-teal-500/25">
                            <i class="bi bi-journal-richtext text-white text-lg"></i>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-slate-100 text-slate-500">{{ $item->mcqs_count }} Qs</span>
                    </div>
                    <h3 class="font-bold text-lg text-slate-900 group-hover:text-teal-600 transition truncate" title="{{ $item->name }}">{{ $item->name }}</h3>
                    <p class="text-sm text-slate-500 mt-1">Certificate on 70%+</p>
                    <a href="/start-quiz/{{ $item->id }}/{{ $item->name }}"
                       class="mt-5 w-full inline-flex items-center justify-center gap-2 bg-slate-900 text-white text-sm font-semibold px-4 py-2.5 rounded-xl hover:bg-teal-600 transition-all">
                        <i class="bi bi-play-fill"></i> Attempt Quiz
                    </a>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-2xl p-12 text-center border border-dashed border-slate-200">
                    <i class="bi bi-journal-x text-4xl text-slate-300 block mb-3"></i>
                    <p class="text-slate-500 font-medium">No quizzes in this category yet.</p>
                </div>
            @endforelse
        </div>

        @if($quizData->lastPage() > 1)
        <div class="flex justify-center gap-2 mt-10">
            @if(!$quizData->onFirstPage())
                <a href="{{ $quizData->previousPageUrl() }}" class="px-4 py-2 text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-teal-50 hover:text-teal-600 transition">&laquo; Prev</a>
            @endif
            @if($quizData->hasMorePages())
                <a href="{{ $quizData->nextPageUrl() }}" class="px-4 py-2 text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-teal-50 hover:text-teal-600 transition">Next &raquo;</a>
            @endif
        </div>
        @endif
    </div>

    @if(session('no_mcq'))
    <div id="popup" class="fixed inset-0 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm z-50 px-4">
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-sm w-full text-center relative animate-bounce">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-rose-100 text-rose-600 grid place-items-center mb-4">
                <i class="bi bi-exclamation-triangle-fill text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-2">Empty Quiz</h3>
            <p class="text-slate-500 mb-6">{{ session('no_mcq') }}</p>
            <button id="closePopup" class="w-full bg-gradient-to-r from-teal-600 to-emerald-600 text-white font-semibold px-4 py-2.5 rounded-xl hover:shadow-lg transition">
                Got it
            </button>
        </div>
    </div>

    <script>
        const popup = document.getElementById('popup');
        const closeBtn = document.getElementById('closePopup');
        closeBtn.addEventListener('click', () => popup.remove());
        setTimeout(() => { if (popup) popup.remove(); }, 5000);
    </script>
    @endif
</main>

<x-user-footer></x-user-footer>
</body>
</html>

