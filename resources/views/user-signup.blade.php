<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'User Signup')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    {{-- Tailwind + Vite --}}
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>

<body class="bg-gray-100 font-sans min-h-screen flex flex-col">

    <!-- Navbar -->
    <x-user-navbar></x-user-navbar>

    <!-- Signup Section -->
    <section class="flex-grow flex justify-center items-center px-4 py-12 mt-10">
        <div class="bg-white shadow-xl rounded-2xl w-full max-w-md px-8 py-8">
            
            <!-- Header -->
            <div class="text-center mb-6">
                <i class="bi bi-person-plus-fill text-teal-600 text-5xl"></i>
                <h2 class="text-2xl font-bold text-teal-700 mt-2">Create Account</h2>
                <p class="text-gray-500 text-sm mt-1">Join the Quiz System today!</p>
            </div>

            <!-- Signup Form -->
            <form action="/user-signup" method="POST" class="space-y-5">
                @csrf

                <!-- Username -->
                <div>
                    <label class="block text-gray-700 font-medium mb-1" for="name">Username</label>
                    <input type="text" id="username" name="name"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition">
                    @error('username')
                    <div class="text-red-400">{{$message}}</div>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-gray-700 font-medium mb-1" for="email">Email</label>
                    <input type="email" id="email" name="email"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition">
                        @error('email')
                        <div class="text-red-400">{{$message}}</div>
                        @enderror
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-gray-700 font-medium mb-1" for="password">Password</label>
                    <input type="password" id="password" name="password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition">
                        @error('password')
                        <div class="text-red-400">{{$message}}</div>
                        @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="block text-gray-700 font-medium mb-1" for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="password_confirmation"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition">
                </div>

                <!-- Signup Button -->
                <button type="submit"
                    class="w-full bg-teal-600 text-white py-2 rounded-lg font-semibold hover:bg-teal-700 transition transform hover:-translate-y-0.5">
                    Sign Up
                </button>
            </form>

            <!-- Login Link -->
            <p class="text-center text-gray-600 text-sm mt-5">
                Already have an account? 
                <a href="/user-login" class="text-teal-600 hover:text-teal-800 font-medium">Login</a>
            </p>
        </div>
    </section>

    <!-- Footer -->
    <x-user-footer></x-user-footer>
</body>
</html>
