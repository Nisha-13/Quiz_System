    <!-- Navbar -->
    <nav class="fixed w-full z-20 top-0 left-0 bg-gradient-to-r from-blue-600 to-cyan-500 shadow-lg">
        <div class="max-w-7xl mx-auto flex items-center justify-between px-6 py-3">
            <!-- Brand -->
            <a href="#" class="flex items-center gap-2 text-white text-lg font-semibold">
                <i class="bi bi-mortarboard-fill text-2xl"></i> Quiz System
            </a>

            <!-- Toggle Button -->
            <button id="menu-toggle"
                class="lg:hidden text-white text-3xl focus:outline-none transition-transform duration-300 hover:scale-110">
                <i class="bi bi-list"></i>
            </button>

            <!-- Desktop Menu -->
            <ul class="hidden lg:flex items-center space-x-6 text-white font-medium">
                <li><a href="/dashboard" class="hover:text-yellow-300 transition">Dashboard</a></li>
                <li><a href="/admin-categories" class="hover:text-yellow-300 transition">Categories</a></li>
                <li><a href="/add-quiz" class="hover:text-yellow-300 transition">Quiz</a></li>
                <li><a href="#" class="hover:text-yellow-300 transition">Welcome, {{ $name }}</a></li>
                <li>
                    <a href="/admin-logout"
                       class="bg-yellow-400 text-gray-800 px-4 py-2 rounded-lg font-semibold hover:bg-yellow-300 transition">
                       Logout
                    </a>
                </li>
            </ul>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu"
             class="lg:hidden max-h-0 overflow-hidden transition-all duration-500 ease-in-out 
             bg-gradient-to-r from-blue-600 to-cyan-500 text-white px-6 space-y-3">
             
            <a href="/dashboard" class="block py-2 hover:text-yellow-300">Dashboard</a>
            <a href="/admin-categories" class="block py-2 hover:text-yellow-300">Categories</a>
            <a href="/add-quiz" class="block py-2 hover:text-yellow-300">Quiz</a>
            <a href="#" class="block py-2 hover:text-yellow-300">Welcome, {{ $name }}</a>
            <a href="/admin-logout"
               class="block bg-yellow-400 text-gray-800 px-4 py-2 rounded-lg font-semibold hover:bg-yellow-300 transition text-center">
               Logout
            </a>
        </div>
    </nav>