<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Categories &mdash; QuizSphere</title>
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
    <!-- Header -->
    <section class="pt-28 pb-10 text-center px-4" style="background: radial-gradient(ellipse 80% 60% at 50% -10%, rgba(99,102,241,0.13), transparent), #f8fafc;">
        <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-slate-900">All <span class="bg-gradient-to-r from-teal-600 to-cyan-500 bg-clip-text text-transparent">Categories</span></h1>
        <p class="text-slate-500 mt-3 max-w-lg mx-auto">Every topic in one place &mdash; find the perfect quiz for you.</p>
    </section>

    @if(session('message-success'))
    <div class="flex justify-center px-4 mb-6">
        <div class="flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-3 rounded-xl text-sm font-medium max-w-lg w-full">
            <i class="bi bi-check-circle-fill text-emerald-500"></i>
            <span>{{ session('message-success') }}</span>
        </div>
    </div>
    @endif
    @if(session('category_delete'))
    <div class="flex justify-center px-4 mb-6">
        <div class="flex items-center gap-2 bg-rose-50 border border-rose-200 text-rose-700 px-5 py-3 rounded-xl text-sm font-medium max-w-lg w-full">
            <i class="bi bi-trash-fill text-rose-500"></i>
            <span>{{ session('category_delete') }}</span>
        </div>
    </div>
    @endif

    <!-- Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-6">
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse($categories as $category)
                <a href="/user-quiz-list/{{ $category->id }}/{{ $category->name }}"
                   class="group relative bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-teal-500/5 hover:border-teal-200 hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                    <div class="absolute -right-8 -top-8 w-28 h-28 rounded-full bg-gradient-to-br from-teal-500/10 to-cyan-500/10 group-hover:scale-150 transition-transform duration-500"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-teal-600 to-emerald-600 grid place-items-center shadow-lg shadow-teal-500/25">
                                <i class="bi bi-collection-fill text-white text-xl"></i>
                            </div>
                            <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-sky-50 text-sky-600">{{ $category->quizzes_count }} quizzes</span>
                        </div>
                        <h3 class="font-bold text-lg text-slate-900 group-hover:text-teal-600 transition">{{ $category->name }}</h3>
                        <p class="mt-4 text-sm font-semibold text-teal-600 inline-flex items-center gap-1.5">
                            Explore quizzes <i class="bi bi-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </p>
                    </div>
                </a>
            @empty
                <div class="col-span-full bg-white rounded-2xl p-12 text-center border border-dashed border-slate-200">
                    <i class="bi bi-inbox text-4xl text-slate-300 block mb-3"></i>
                    <p class="text-slate-500 font-medium">No categories found.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($categories->lastPage() > 1)
        <div class="flex justify-center mt-10">
            <div class="flex items-center gap-2 bg-white px-4 py-3 rounded-2xl shadow-sm border border-slate-100">
                @if ($categories->onFirstPage())
                    <span class="px-4 py-2 text-sm text-slate-300 rounded-lg cursor-not-allowed">&laquo; Prev</span>
                @else
                    <a href="{{ $categories->previousPageUrl() }}" class="px-4 py-2 text-sm font-semibold text-slate-700 bg-slate-100 rounded-lg hover:bg-teal-100 hover:text-teal-600 transition">&laquo; Prev</a>
                @endif

                @foreach ($categories->getUrlRange(1, $categories->lastPage()) as $page => $url)
                    @if ($page == $categories->currentPage())
                        <span class="px-4 py-2 rounded-lg bg-gradient-to-r from-teal-600 to-emerald-600 text-white font-semibold shadow-md shadow-teal-500/25">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="px-4 py-2 rounded-lg text-slate-600 font-medium hover:bg-slate-100 transition">{{ $page }}</a>
                    @endif
                @endforeach

                @if ($categories->hasMorePages())
                    <a href="{{ $categories->nextPageUrl() }}" class="px-4 py-2 text-sm font-semibold text-slate-700 bg-slate-100 rounded-lg hover:bg-teal-100 hover:text-teal-600 transition">Next &raquo;</a>
                @else
                    <span class="px-4 py-2 text-sm text-slate-300 rounded-lg cursor-not-allowed">Next &raquo;</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</main>

<x-user-footer></x-user-footer>
</body>
</html>

