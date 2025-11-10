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

<!-- Skills Section -->
<section class="bg-gray-100 py-12 text-center mt-16">
@if(session('message-success'))
<div class="flex justify-center my-3">
    <div class="p-3 rounded-lg bg-green-100 border border-green-300 text-green-700 text-sm font-medium flex items-center gap-2 shadow-md max-w-md w-full text-center">
        <i class="bi bi-check-circle-fill text-green-600 text-lg"></i>
        <span class="font-bold">{{ session('message-success') }}</span>
    </div>
</div>
@endif

    <h1 class="text-4xl font-extrabold fs-2 text-teal-700 mb-6">Check Your Skills</h1>

    <div class="flex justify-center">
        <div class="relative w-full max-w-md">
            <form action="/search-quiz" method="get">
                <input type="text" name="search"
                   placeholder="Search a quiz topic..."
                   class="w-full border border-teal-500 rounded-full py-3 px-5 focus:outline-none focus:ring-2 focus:ring-teal-500 text-gray-700 shadow-sm">
                
                <button class="absolute right-2 top-1/2 -translate-y-1/2 cursor-pointer bg-teal-600 text-white px-4 py-2 rounded-full hover:bg-teal-700 transition">
                    <i class="bi bi-search"></i>
                </button>
            </form>
        </div>
    </div>
</section>

   <!-- ✅ Category List -->
<div class="flex flex-col items-center px-4 mb-10">
    <h1 class="text-3xl font-bold text-teal-700 mb-6 tracking-wide text-center">
       Top Categories
    </h1>

    @if(session('category_delete'))
        <div class="mb-4 bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2 shadow-sm w-full max-w-md">
            <i class="bi bi-trash-fill text-red-600 text-lg"></i>
            <span class="font-medium text-sm sm:text-base">{{ session('category_delete') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-md overflow-hidden w-full max-w-5xl">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left text-gray-700 border-collapse">
                <thead class="bg-teal-600 text-white text-xs sm:text-sm">
                    <tr>
                        <th class="px-4 sm:px-6 py-3">ID</th>
                        <th class="px-4 sm:px-6 py-3">Name</th>
                        <th class="px-4 sm:px-6 py-3">Total Quiz</th>
                        <th class="px-4 sm:px-6 py-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $key => $category)
                    <tr class="{{ $loop->odd ? 'bg-teal-50' : 'bg-white' }} border-b hover:bg-teal-100 transition-colors duration-200">
                        <td class="px-4 sm:px-6 py-3 font-medium text-gray-900">{{ $key+1 }}</td>
                        <td class="px-4 sm:px-6 py-3">{{ $category->name }}</td>
                        <td class="px-4 sm:px-6 py-3">{{ $category->quizzes_count }}</td>
                        <td class="px-4 sm:px-6 py-3 text-center">
                            <a href="/user-quiz-list/{{$category->id}}/{{str_replace(' ', '-', $category->name)}}" title="View" class="inline-block">
                                <i class="bi bi-eye-fill text-lg text-teal-600 hover:text-teal-800 transition-colors"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach

                    @if($categories->isEmpty())
                    <tr>
                        <td colspan="4" class="text-center py-6 text-gray-500 text-sm sm:text-base">No categories found</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

 <!-- ✅ Quiz List -->
<h1 class="text-3xl font-bold text-teal-700 mb-6 tracking-wide text-center">
       Top Quiz
    </h1>
<div class="flex flex-col items-center mt-12 px-4 mb-10">
    <div class="bg-white rounded-2xl shadow-md overflow-hidden w-full max-w-5xl">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left text-gray-700 border-collapse">
                <thead class="bg-teal-600 text-white text-xs sm:text-sm">
                    <tr>
                        <th class="px-4 sm:px-6 py-3">Name</th>
                        <th class="px-4 sm:px-6 py-3 text-center">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($quizData as $item)
                        <tr class="{{ $loop->odd ? 'bg-teal-50' : 'bg-white' }} border-b hover:bg-teal-100 transition-colors duration-200">
                            <td class="px-4 sm:px-6 py-3">{{ $item->name }}</td>
                            <td class="px-4 sm:px-6 py-3 text-center">
                                <a href="/start-quiz/{{$item->id}}/{{str_replace(' ', '-', $item->name)}}" 
                                   title="Attempt Quiz" 
                                   class="inline-block bg-teal-600 text-white px-4 py-2 rounded-lg text-xs sm:text-sm font-medium
                                          hover:bg-teal-700 hover:shadow-md transition-all duration-300 ease-in-out transform hover:-translate-y-0.5">
                                   <i class="bi bi-pencil-square mr-1"></i> Attempt Quiz
                                </a>
                            </td>
                        </tr>
                    @endforeach

                    @if($quizData->isEmpty())
                        <tr>
                            <td colspan="3" class="text-center py-6 text-gray-500 text-sm sm:text-base">
                                No quizzes found
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>


<x-user-footer></x-user-footer>
</body>