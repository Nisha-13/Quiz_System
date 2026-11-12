<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>QuizSphere &mdash; Test Your Knowledge</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>

<body class="bg-slate-50 font-sans">

<x-user-navbar></x-user-navbar>

@if(session('message-success'))
<div class="fixed top-20 inset-x-0 z-50 flex justify-center px-4">
    <div class="flex items-center gap-2 bg-white border border-emerald-200 text-emerald-700 px-5 py-3 rounded-xl shadow-xl text-sm font-medium max-w-lg w-full">
        <i class="bi bi-check-circle-fill text-emerald-500 text-lg shrink-0"></i>
        <span>{{ session('message-success') }}</span>
        <button onclick="this.closest('div.flex').remove()" class="ml-auto text-emerald-400 hover:text-emerald-600"><i class="bi bi-x-lg"></i></button>
    </div>
</div>
@endif

@if(session('message-error'))
<div class="fixed top-20 inset-x-0 z-50 flex justify-center px-4">
    <div class="flex items-center gap-2 bg-white border border-rose-200 text-rose-700 px-5 py-3 rounded-xl shadow-xl text-sm font-medium max-w-lg w-full">
        <i class="bi bi-exclamation-triangle-fill text-rose-500 text-lg shrink-0"></i>
        <span>{{ session('message-error') }}</span>
        <button onclick="this.closest('div.flex').remove()" class="ml-auto text-rose-400 hover:text-rose-600"><i class="bi bi-x-lg"></i></button>
    </div>
</div>
@endif

<!-- &hellip; Hero -->
<section class="relative overflow-hidden bg-smoke pt-28 pb-16 sm:pt-32" style="background: radial-gradient(ellipse 80% 60% at 50% -10%, rgba(99,102,241,0.13), transparent), #f8fafc;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold text-teal-600 bg-teal-50 border border-teal-100 mb-6">
            <i class="bi bi-stars"></i> {{ $categories->sum('quizzes_count') }}+ quizzes across {{ $categories->count() }}+ topics
        </span>
        <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tight text-slate-900 max-w-3xl mx-auto leading-tight">
            Check your skills.
            <span class="bg-gradient-to-r from-teal-600 via-emerald-600 to-cyan-500 bg-clip-text text-transparent"> Prove them.</span>
        </h1>
        <p class="mt-5 text-lg text-slate-500 max-w-xl mx-auto">Free quizzes on every topic. Instant results. Earn a shareable certificate when you score 70% or higher.</p>

        <!-- Search -->
        <div class="mt-9 max-w-xl mx-auto">
            <form action="/search-quiz" method="get" class="relative">
                <i class="bi bi-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></i>
                <input type="text" name="search" placeholder="Search any quiz topic &mdash; try &ldquo;javascript&rdquo;&hellip;"
                    class="w-full bg-white border border-slate-200 rounded-2xl py-4 pl-14 pr-36 text-slate-800 shadow-lg shadow-teal-500/5 focus:outline-none focus:ring-4 focus:ring-teal-500/10 focus:border-teal-400 transition">
                <button class="absolute right-2 top-1/2 -translate-y-1/2 bg-gradient-to-r from-teal-600 to-emerald-600 text-white px-6 py-2.5 rounded-xl font-semibold text-sm shadow-lg shadow-teal-500/30 hover:shadow-teal-500/50 hover:-translate-y-px transition-all">
                    Search
                </button>
            </form>
        </div>
    </div>
</section>

<!-- &hellip; Top Categories -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <div class="flex items-end justify-between mb-8">
        <div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Trending Categories</h2>
            <p class="text-slate-500 mt-1">Pick a topic and jump right in</p>
        </div>
        <a href="/categories" class="hidden sm:inline-flex items-center gap-2 text-sm font-semibold text-teal-600 hover:text-teal-700 transition">
            View all <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($categories as $category)
            <a href="/user-quiz-list/{{ $category->id }}/{{ $category->name }}"
               class="group relative bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-teal-500/5 hover:border-teal-200 hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                <div class="absolute -right-8 -top-8 w-28 h-28 rounded-full bg-gradient-to-br from-teal-500/10 to-cyan-500/10 group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-teal-600 to-emerald-600 grid place-items-center shadow-lg shadow-teal-500/25 mb-4">
                        <i class="bi bi-collection-fill text-white text-xl"></i>
                    </div>
                    <h3 class="font-bold text-lg text-slate-900 group-hover:text-teal-600 transition">{{ $category->name }}</h3>
                    <p class="text-sm text-slate-500 mt-1">{{ $category->quizzes_count }} {{ Str::plural('quiz', $category->quizzes_count) }} available</p>
                    <p class="mt-4 text-sm font-semibold text-teal-600 inline-flex items-center gap-1.5">
                        Explore <i class="bi bi-arrow-right group-hover:translate-x-1 transition-transform"></i>
                    </p>
                </div>
            </a>
        @empty
            <div class="col-span-full bg-white rounded-2xl p-12 text-center border border-dashed border-slate-200">
                <i class="bi bi-inbox text-4xl text-slate-300 block mb-3"></i>
                <p class="text-slate-500 font-medium">No categories yet. Administrators can add them from the admin panel.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8 text-center sm:hidden">
        <a href="/categories" class="inline-flex items-center gap-2 text-sm font-semibold text-teal-600">View all categories <i class="bi bi-arrow-right"></i></a>
    </div>
</section>

<!-- &hellip; Latest Quizzes -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-4">
    <div class="flex items-end justify-between mb-8">
        <div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Featured Quizzes</h2>
            <p class="text-slate-500 mt-1">Most attempted by the community</p>
        </div>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($quizData as $item)
            <div class="group bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:shadow-xl hover:border-teal-200 hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-lg bg-sky-50 text-sky-600 grid place-items-center">
                        <i class="bi bi-journal-richtext"></i>
                    </div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider truncate">{{ $item->category->name ?? 'General' }}</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 truncate" title="{{ $item->name }}">{{ $item->name }}</h3>
                <p class="text-sm text-slate-500 mt-1">{{ $item->mcqs_count }} {{ Str::plural('question', $item->mcqs_count) }}  &bull;  instant certificate</p>
                <a href="/start-quiz/{{ $item->id }}/{{ $item->name }}"
                   class="mt-5 w-full inline-flex items-center justify-center gap-2 bg-slate-900 text-white text-sm font-semibold px-4 py-2.5 rounded-xl hover:bg-teal-600 group-hover:bg-teal-600 hover:-translate-y-px transition-all">
                    <i class="bi bi-play-fill"></i> Attempt Quiz
                </a>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl p-12 text-center border border-dashed border-slate-200">
                <i class="bi bi-journal-x text-4xl text-slate-300 block mb-3"></i>
                <p class="text-slate-500 font-medium">No quizzes published yet.</p>
            </div>
        @endforelse
    </div>
</section>

<!-- &hellip; Trust strip -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
        @foreach([
            ['icon' => 'bi-lightning-charge-fill', 'title' => 'Instant Results', 'desc' => 'See your score the second you finish.'],
            ['icon' => 'bi-award-fill', 'title' => 'Free Certificates', 'desc' => 'Score 70%+ and earn a PDF certificate.'],
            ['icon' => 'bi-graph-up-arrow', 'title' => 'Track Progress', 'desc' => 'Review every past attempt.'],
            ['icon' => 'bi-shield-check', 'title' => 'Always Free', 'desc' => 'No fees. No catch. Just learning.'],
        ] as $t)
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm text-center hover:shadow-lg transition-shadow">
            <div class="w-11 h-11 mx-auto rounded-xl bg-teal-50 text-teal-600 grid place-items-center mb-3">
                <i class="bi {{ $t['icon'] }} text-lg"></i>
            </div>
            <h3 class="font-bold text-slate-900 text-sm">{{ $t['title'] }}</h3>
            <p class="text-xs text-slate-500 mt-1.5">{{ $t['desc'] }}</p>
        </div>
        @endforeach
    </div>
</section>

<x-user-footer></x-user-footer>

</body>
</html>

