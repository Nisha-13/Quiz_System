<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">


    {{-- Tailwind + Vite --}}
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>

<body class="bg-gray-100 font-sans">
<x-user-navbar></x-user-navbar>

<section class="flex flex-col items-center justify-center mt-24 px-4 mb-12">
  @if(session('message-success'))
<div class="flex justify-center my-3">
    <div class="p-3 rounded-lg bg-green-100 border border-green-300 text-green-700 text-sm font-medium flex items-center gap-2 shadow-md max-w-md w-full text-center">
        <i class="bi bi-check-circle-fill text-green-600 text-lg"></i>
        <span class="font-bold">{{ session('message-success') }}</span>
    </div>
</div>
@endif
  <div class="bg-white shadow-xl rounded-2xl w-full max-w-3xl px-8 py-10 text-center">
    
    <!-- Quiz Title -->
    <h1 class="text-3xl font-bold text-teal-700 mb-3">
      Quiz Name — {{str_replace('-', ' ', $quizName)}}
    </h1>

    <!-- Quiz Info -->
    <h2 class="text-lg text-gray-700 mb-6">
      This quiz contains <span class="font-semibold text-teal-600">{{ $quizCount }}</span> questions,
      and there’s <span class="font-semibold text-teal-600">no time limit</span> to attempt this quiz.
    </h2>

    <!-- Good Luck -->
    <h1 class="text-2xl font-semibold text-teal-700 mb-8">Good Luck! 🍀</h1>

    <!-- Button -->
     @if(Session('user'))
    <a href="/mcq/{{Session('firstMCQ')->id .'/'. $quizName}}"
       class="inline-block bg-gradient-to-r from-teal-600 to-cyan-500 text-white font-semibold px-6 py-3 rounded-lg hover:from-teal-700 hover:to-cyan-600 transform hover:-translate-y-0.5 transition">
       Start Quiz
    </a>
    @else
    <a href="/user-login-quiz"
       class="inline-block bg-gradient-to-r from-teal-600 to-cyan-500 text-white font-semibold px-6 py-3 rounded-lg hover:from-teal-700 hover:to-cyan-600 transform hover:-translate-y-0.5 transition">
       Login to Start Quiz
    </a>
    <a href="/user-signup-quiz"
       class="inline-block bg-gradient-to-r from-teal-600 to-cyan-500 text-white font-semibold px-6 py-3 rounded-lg hover:from-teal-700 hover:to-cyan-600 transform hover:-translate-y-0.5 transition">
       Signup to Start Quiz
    </a>
    @endif
  </div>
</section>




<x-user-footer></x-user-footer>
</body>