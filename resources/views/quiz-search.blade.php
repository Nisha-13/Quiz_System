<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Results &mdash; QuizSphere</title>
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
    <section class="pt-28 pb-10 text-center px-4" style="background: radial-gradient(ellipse 80% 60% at 50% -10%, rgba(99,102,241,0.13), transparent), #f8fafc;">
        <p class="text-sm text-slate-500 mb-2">Search results for</p>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900">
            &ldquo;<span class="bg-gradient-to-r from-teal-600 to-cyan-500 bg-clip-text text-transparent">{{ $quiz }}</span>&rdquo;
        </h1>
        <div class="mt-3 inline-flex items-center gap-4 text-sm text-slate-500">
            <span><span class="font-bold text-teal-600">{{ $categories->count() }}</span> {{ Str::plural('category', $categories->count()) }}</span>
            <span class="text-slate-300">|</span>
            <span><span class="font-bold text-teal-600">{{ $quizzes->count() }}</span> {{ Str::plural('quiz', $quizzes->count()) }}</span>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-6 space-y-10">

        @if($quizzes->count())
        <section>
            <h2 class="text-xl font-bold text-slate-900 mb-5 flex items-center gap-2"><i class="bi bi-journal-text text-teal-600"></i> Matching Quizzes</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($quizzes as $item)
                    <div class="group bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:shadow-xl hover:border-teal-200 hover:-translate-y-1 transition-all duration-300">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ $item->category->name ?? 'General' }}</span>
                        <h3 class="font-bold text-lg text-slate-900 mt-2 truncate" title="{{ $item->name }}">{{ $item->name }}</h3>
                        <p class="text-sm text-slate-500 mt-1">{{ $item->mcqs_count }} {{ Str::plural('question', $item->mcqs_count) }}</p>
                        <a href="/start-quiz/{{ $item->id }}/{{ $item->name }}"
                           class="mt-5 w-full inline-flex items-center justify-center gap-2 bg-slate-900 text-white text-sm font-semibold px-4 py-2.5 rounded-xl hover:bg-teal-600 transition-all">
                            <i class="bi bi-play-fill"></i> Attempt Quiz
                        </a>
                    </div>
                @endforeach
            </div>
        </section>
        @endif

        @if($categories->count())
        <section>
            <h2 class="text-xl font-bold text-slate-900 mb-5 flex items-center gap-2"><i class="bi bi-collection text-teal-600"></i> Matching Categories</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($categories as $category)
                    <a href="/user-quiz-list/{{ $category->id }}/{{ $category->name }}"
                       class="group bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:shadow-xl hover:border-teal-200 hover:-translate-y-1 transition-all duration-300">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-teal-600 to-emerald-600 grid place-items-center shadow-lg shadow-teal-500/25">
                                <i class="bi bi-collection-fill text-white"></i>
                            </div>
                            <div class="min-w-0">
                                <h3 class="font-bold text-slate-900 group-hover:text-teal-600 transition truncate">{{ $category->name }}</h3>
                                <p class="text-xs text-slate-500">{{ $category->quizzes_count }} {{ Str::plural('quiz', $category->quizzes_count) }}</p>
                            </div>
                            <i class="bi bi-arrow-right ml-auto text-slate-400 group-hover:text-teal-600 group-hover:translate-x-1 transition-all"></i>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
        @endif

        @if($categories->isEmpty() && $quizzes->isEmpty())
            <div class="bg-white rounded-2xl p-14 text-center border border-dashed border-slate-200">
                <i class="bi bi-search text-4xl text-slate-300 block mb-4"></i>
                <h3 class="text-lg font-bold text-slate-900">Nothing found for &ldquo;{{ $quiz }}&rdquo;</h3>
                <p class="text-slate-500 mt-2 mb-6">Try a different keyword, or browse all topics.</p>
                <a href="/categories" class="inline-flex items-center gap-2 bg-gradient-to-r from-teal-600 to-emerald-600 text-white font-semibold px-6 py-2.5 rounded-xl shadow-lg shadow-teal-500/25 hover:shadow-teal-500/40 transition-all">
                    Browse Categories
                </a>
            </div>
        @endif
    </div>
</main>

<x-user-footer></x-user-footer>
</body>
</html>

