<!-- Navbar -->
<nav class="fixed w-full z-20 top-0 left-0 bg-teal-600 shadow-md">
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
            <li><a href="/" class="hover:text-yellow-200 transition">Home</a></li>
            <li><a href="/categories" class="hover:text-yellow-200 transition">Categories</a></li>
            @if(Session('user'))
            <li><a href="/user-details" class="hover:text-yellow-200 transition">Welcome, {{ Session('user')->name }}</a></li>
            <li><a href="/user-logout" class="hover:text-yellow-200 transition">Logout</a></li>
            @else
            <li><a href="/user-login" class="hover:text-yellow-200 transition">Login</a></li>
            <li><a href="/user-signup" class="hover:text-yellow-200 transition">Signup</a></li>
            @endif
            <li><a href="/" class="hover:text-yellow-200 transition">Blog</a></li>
        </ul>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu"
        class="lg:hidden max-h-0 overflow-hidden transition-all duration-500 ease-in-out 
        bg-teal-600 text-white px-6 space-y-3">
        
        <a href="/" class="block py-2 hover:text-yellow-200">Home</a>
        <a href="/" class="block py-2 hover:text-yellow-200">Categories</a>
        <a href="/" class="block py-2 hover:text-yellow-200">Login</a>
        <a href="/user-signup" class="block py-2 hover:text-yellow-200">Signup</a>
        <a href="/" class="block py-2 hover:text-yellow-200">Blog</a>
    </div>
</nav>