<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', $title ?? 'Dashboard') &mdash; QuizSphere Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>

<body class="bg-slate-100 font-sans min-h-screen">

<!-- &hellip; Sidebar -->
<aside id="qs-sidebar" class="fixed inset-y-0 left-0 z-40 w-72 -translate-x-full lg:translate-x-0 bg-ink-900 text-slate-300 flex flex-col transition-transform duration-300 shadow-2xl">
    <!-- Brand -->
    <div class="flex items-center gap-3 px-6 h-20 border-b border-white/10">
        <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-teal-600 via-emerald-600 to-cyan-500 grid place-items-center shadow-lg shadow-teal-500/30">
            <i class="bi bi-mortarboard-fill text-white text-xl"></i>
        </span>
        <span class="text-lg font-extrabold tracking-tight text-white">Quiz<span class="text-teal-400">Sphere</span></span>
        <button id="qs-sidebar-close" class="lg:hidden ml-auto text-slate-400 hover:text-white text-2xl"><i class="bi bi-x-lg"></i></button>
    </div>

    <!-- Nav Links -->
    <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1.5">
        <p class="px-3 pb-2 text-[11px] font-bold tracking-widest text-slate-500 uppercase">Overview</p>
        <a href="/dashboard" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition {{ request()->is('dashboard') ? 'bg-gradient-to-r from-teal-600 to-emerald-600 text-white shadow-lg shadow-teal-900/40' : 'hover:bg-white/5 hover:text-white' }}">
            <i class="bi bi-grid-1x2 text-lg"></i> Dashboard
        </a>

        <p class="px-3 pt-5 pb-2 text-[11px] font-bold tracking-widest text-slate-500 uppercase">Manage</p>
        <a href="/admin-categories" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition {{ request()->is('admin-categories') || request()->is('quiz-list*') ? 'bg-gradient-to-r from-teal-600 to-emerald-600 text-white shadow-lg shadow-teal-900/40' : 'hover:bg-white/5 hover:text-white' }}">
            <i class="bi bi-collection text-lg"></i> Categories
            <span class="ml-auto text-[11px] font-bold px-2 py-0.5 rounded-full bg-teal-500/20 text-teal-300">{{ $stats['categories'] ?? '' }}</span>
        </a>
        <a href="/admin-quizzes" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition {{ request()->is('admin-quizzes') ? 'bg-gradient-to-r from-teal-600 to-emerald-600 text-white shadow-lg shadow-teal-900/40' : 'hover:bg-white/5 hover:text-white' }}">
            <i class="bi bi-journal-text text-lg"></i> Quizzes
            <span class="ml-auto text-[11px] font-bold px-2 py-0.5 rounded-full bg-teal-500/20 text-teal-300">{{ $stats['quizzes'] ?? '' }}</span>
        </a>
        <a href="/admin-questions" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition {{ request()->is('admin-questions') ? 'bg-gradient-to-r from-teal-600 to-emerald-600 text-white shadow-lg shadow-teal-900/40' : 'hover:bg-white/5 hover:text-white' }}">
            <i class="bi bi-question-circle text-lg"></i> Questions
            <span class="ml-auto text-[11px] font-bold px-2 py-0.5 rounded-full bg-teal-500/20 text-teal-300">{{ $stats['mcqs'] ?? '' }}</span>
        </a>
        <a href="/add-quiz" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition {{ request()->is('add-quiz') || request()->is('show-quiz*') ? 'bg-gradient-to-r from-teal-600 to-emerald-600 text-white shadow-lg shadow-teal-900/40' : 'hover:bg-white/5 hover:text-white' }}">
            <i class="bi bi-plus-square text-lg"></i> Create Quiz
        </a>

        <p class="px-3 pt-5 pb-2 text-[11px] font-bold tracking-widest text-slate-500 uppercase">Activity</p>
        <a href="/admin-users" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition {{ request()->is('admin-users') ? 'bg-gradient-to-r from-teal-600 to-emerald-600 text-white shadow-lg shadow-teal-900/40' : 'hover:bg-white/5 hover:text-white' }}">
            <i class="bi bi-people text-lg"></i> Users
            <span class="ml-auto text-[11px] font-bold px-2 py-0.5 rounded-full bg-teal-500/20 text-teal-300">{{ $stats['users'] ?? '' }}</span>
        </a>
        <a href="/admin-attempts" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition {{ request()->is('admin-attempts') ? 'bg-gradient-to-r from-teal-600 to-emerald-600 text-white shadow-lg shadow-teal-900/40' : 'hover:bg-white/5 hover:text-white' }}">
            <i class="bi bi-clipboard-check text-lg"></i> Quiz Attempts
            <span class="ml-auto text-[11px] font-bold px-2 py-0.5 rounded-full bg-teal-500/20 text-teal-300">{{ $stats['attempts'] ?? '' }}</span>
        </a>

        <p class="px-3 pt-5 pb-2 text-[11px] font-bold tracking-widest text-slate-500 uppercase">Account</p>
        <a href="/logout" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-rose-400 hover:bg-rose-500/10 hover:text-rose-300 transition">
            <i class="bi bi-box-arrow-left text-lg"></i> Logout
        </a>
    </nav>

    <!-- Admin Card -->
    <div class="p-4 border-t border-white/10">
        <div class="flex items-center gap-3 rounded-2xl bg-white/5 p-3">
            <span class="w-10 h-10 rounded-full bg-gradient-to-br from-teal-500 to-emerald-500 grid place-items-center text-white font-bold">{{ strtoupper(substr($name, 0, 1)) }}</span>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-white truncate">{{ $name }}</p>
                <p class="text-xs text-slate-500">Administrator</p>
            </div>
            <span class="ml-auto w-2.5 h-2.5 rounded-full bg-emerald-400 shadow-[0_0_8px_2px_rgba(52,211,153,0.5)]"></span>
        </div>
    </div>
</aside>

<!-- &hellip; Backdrop for mobile -->
<div id="qs-backdrop" class="fixed inset-0 z-30 bg-slate-900/60 backdrop-blur-sm hidden lg:hidden"></div>

<!-- &hellip; Main Column -->
<div class="lg:pl-72 min-h-screen flex flex-col">

    <!-- Topbar -->
    <header class="sticky top-0 z-20 h-20 bg-white/85 backdrop-blur-xl border-b border-slate-200 flex items-center gap-4 px-4 sm:px-8">
        <button id="qs-sidebar-open" class="lg:hidden w-10 h-10 rounded-lg grid place-items-center text-slate-700 hover:bg-slate-100 transition">
            <i class="bi bi-list text-2xl"></i>
        </button>
        <div>
            <h1 class="text-lg sm:text-xl font-bold text-slate-900 leading-tight">{{ $heading ?? 'Dashboard' }}</h1>
            <p class="text-xs sm:text-sm text-slate-500 hidden sm:block">{{ $subheading ?? 'Manage your quiz platform' }}</p>
        </div>
        <div class="ml-auto flex items-center gap-3">
            @if(request()->is('add-quiz'))
            <a href="/admin-categories" class="hidden sm:inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition">
                <i class="bi bi-arrow-left"></i> Categories
            </a>
            @endif
            <a href="/add-quiz" class="hidden sm:inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-teal-600 to-emerald-600 text-white text-sm font-semibold shadow-lg shadow-teal-500/30 hover:shadow-teal-500/50 hover:-translate-y-px transition-all">
                <i class="bi bi-plus-lg"></i> New Quiz
            </a>
            <a href="/" class="w-10 h-10 rounded-xl bg-slate-100 grid place-items-center text-slate-600 hover:bg-teal-50 hover:text-teal-600 transition" title="View Public Site">
                <i class="bi bi-globe2"></i>
            </a>
        </div>
    </header>

    <!-- Flash Messages -->
    <div class="px-4 sm:px-8 pt-6 space-y-3">
        @if(session('success'))
        <div class="flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm font-medium shadow-sm">
            <i class="bi bi-check-circle-fill text-emerald-500 mt-0.5"></i>
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="ml-auto text-emerald-500 hover:text-emerald-700"><i class="bi bi-x-lg"></i></button>
        </div>
        @endif
        @if(session('error'))
        <div class="flex items-start gap-3 bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-sm font-medium shadow-sm">
            <i class="bi bi-x-circle-fill text-rose-500 mt-0.5"></i>
            <span>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="ml-auto text-rose-500 hover:text-rose-700"><i class="bi bi-x-lg"></i></button>
        </div>
        @endif
        @if($errors->any())
        <div class="bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-xl text-sm shadow-sm">
            <p class="font-bold mb-1 flex items-center gap-2"><i class="bi bi-exclamation-triangle-fill"></i> Please fix the following:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    <main class="flex-1 px-4 sm:px-8 py-6">
        {{ $slot }}
    </main>

    <x-footer></x-footer>
</div>

<script>
(function () {
    var sidebar = document.getElementById('qs-sidebar');
    var backdrop = document.getElementById('qs-backdrop');
    var openBtn = document.getElementById('qs-sidebar-open');
    var closeBtn = document.getElementById('qs-sidebar-close');

    function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        backdrop.classList.remove('hidden');
    }
    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        backdrop.classList.add('hidden');
    }
    openBtn && openBtn.addEventListener('click', openSidebar);
    closeBtn && closeBtn.addEventListener('click', closeSidebar);
    backdrop && backdrop.addEventListener('click', closeSidebar);
})();
</script>

</body>
</html>

