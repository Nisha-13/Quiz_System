<!-- Modern User Navbar -->
<nav id="qs-navbar" class="fixed top-0 inset-x-0 z-50 transition-all duration-300 bg-white/90 backdrop-blur-xl border-b border-slate-200/80 shadow-[0_2px_20px_-10px_rgba(15,23,42,0.15)]">
    <div class="max-w-7xl mx-auto flex items-center justify-between px-4 sm:px-6 lg:px-8 h-16">
        <!-- Brand -->
        <a href="/" class="flex items-center gap-2.5 group">
            <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-teal-600 via-emerald-600 to-cyan-500 grid place-items-center shadow-lg shadow-teal-500/30 group-hover:scale-105 transition-transform">
                <i class="bi bi-mortarboard-fill text-white text-xl"></i>
            </span>
            <span class="text-lg font-extrabold tracking-tight text-slate-900">Quiz<span class="bg-gradient-to-r from-teal-600 to-emerald-500 bg-clip-text text-transparent">Sphere</span></span>
        </a>

        <!-- Desktop Links -->
        <div class="hidden md:flex items-center gap-1 font-medium text-sm">
            <a href="/" class="px-4 py-2 rounded-lg transition {{ request()->routeIs('/') || request()->is('/') ? 'text-teal-600 bg-teal-50' : 'text-slate-600 hover:text-teal-600 hover:bg-slate-50' }}">Home</a>
            <a href="/categories" class="px-4 py-2 rounded-lg transition {{ request()->is('categories') ? 'text-teal-600 bg-teal-50' : 'text-slate-600 hover:text-teal-600 hover:bg-slate-50' }}">Categories</a>
            <a href="/search-quiz?search=" class="px-4 py-2 rounded-lg transition {{ request()->is('search-quiz') ? 'text-teal-600 bg-teal-50' : 'text-slate-600 hover:text-teal-600 hover:bg-slate-50' }}">Find Quiz</a>
            @if(Session('user'))
            <div class="relative ml-2" id="qs-avatar-wrap">
                <button id="qs-avatar-toggle" class="flex items-center gap-2 pl-2 pr-3 py-1.5 rounded-full border border-slate-200 hover:border-teal-300 bg-white shadow-sm transition">
                    <span class="w-7 h-7 rounded-full bg-gradient-to-br from-teal-500 to-emerald-500 grid place-items-center text-white text-xs font-bold">{{ strtoupper(substr(Session('user')->name, 0, 1)) }}</span>
                    <span class="text-slate-700 text-sm font-semibold max-w-[120px] truncate">{{ Session('user')->name }}</span>
                    <i class="bi bi-caret-down-fill text-[10px] text-slate-400"></i>
                </button>
                <div id="qs-avatar-menu" class="hidden absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-xl border border-slate-100 py-2 z-50">
                    <div class="px-4 py-2 border-b border-slate-100 mb-1">
                        <p class="text-xs text-slate-500 truncate">{{ Session('user')->email }}</p>
                    </div>
                    <a href="/user-details" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-teal-50 hover:text-teal-600 transition"><i class="bi bi-journal-check"></i> My Attempts</a>
                    <a href="/logout" class="flex items-center gap-2 px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 transition"><i class="bi bi-box-arrow-right"></i> Logout</a>
                </div>
            </div>
            @else
            <a href="/login" class="ml-2 px-4 py-2 rounded-lg text-slate-600 hover:text-teal-600 hover:bg-slate-50 transition">Login</a>
            <a href="/user-signup" class="ml-1 px-5 py-2.5 rounded-xl bg-gradient-to-r from-teal-600 to-emerald-600 text-white font-semibold shadow-lg shadow-teal-500/25 hover:shadow-teal-500/40 hover:-translate-y-0.5 transition-all">Get Started</a>
            @endif
        </div>

        <!-- Mobile Toggle -->
        <button id="qs-menu-toggle" class="md:hidden w-10 h-10 rounded-lg grid place-items-center text-slate-700 hover:bg-slate-100 transition">
            <i id="qs-menu-icon" class="bi bi-list text-2xl"></i>
        </button>
    </div>

    <!-- Mobile Menu -->
    <div id="qs-mobile-menu" class="md:hidden hidden border-t border-slate-100 bg-white/95 backdrop-blur-xl px-4 pb-4 pt-2 space-y-1">
        <a href="/" class="block px-4 py-2.5 rounded-lg text-slate-700 font-medium hover:bg-teal-50 hover:text-teal-600 transition">Home</a>
        <a href="/categories" class="block px-4 py-2.5 rounded-lg text-slate-700 font-medium hover:bg-teal-50 hover:text-teal-600 transition">Categories</a>
        <a href="/search-quiz?search=" class="block px-4 py-2.5 rounded-lg text-slate-700 font-medium hover:bg-teal-50 hover:text-teal-600 transition">Find Quiz</a>
        @if(Session('user'))
        <a href="/user-details" class="block px-4 py-2.5 rounded-lg text-slate-700 font-medium hover:bg-teal-50 hover:text-teal-600 transition">My Attempts</a>
        <a href="/logout" class="block px-4 py-2.5 rounded-lg text-rose-600 font-medium hover:bg-rose-50 transition">Logout ({{ Session('user')->name }})</a>
        @else
        <a href="/login" class="block px-4 py-2.5 rounded-lg text-slate-700 font-medium hover:bg-teal-50 hover:text-teal-600 transition">Login</a>
        <a href="/user-signup" class="block px-4 py-2.5 rounded-lg bg-teal-600 text-white font-semibold text-center mt-2">Get Started</a>
        @endif
    </div>
</nav>

<script>
(function () {
    var toggle = document.getElementById('qs-menu-toggle');
    var menu = document.getElementById('qs-mobile-menu');
    var icon = document.getElementById('qs-menu-icon');
    if (!toggle) return;
    toggle.addEventListener('click', function () {
        var hidden = menu.classList.contains('hidden');
        if (hidden) {
            menu.classList.remove('hidden');
            if (icon) icon.classList.replace('bi-list', 'bi-x');
        } else {
            menu.classList.add('hidden');
            if (icon) icon.classList.replace('bi-x', 'bi-list');
        }
    });

    var avatarToggle = document.getElementById('qs-avatar-toggle');
    var avatarMenu = document.getElementById('qs-avatar-menu');
    if (avatarToggle && avatarMenu) {
        avatarToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            avatarMenu.classList.toggle('hidden');
        });
        document.addEventListener('click', function (e) {
            if (!document.getElementById('qs-avatar-wrap').contains(e.target)) {
                avatarMenu.classList.add('hidden');
            }
        });
    }
})();
</script>

