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
<section class="bg-gray-100 pt-12 text-center mt-16">
@if(session('message-success'))
<div class="flex justify-center my-3">
    <div class="p-3 rounded-lg bg-green-100 border border-green-300 text-green-700 text-sm font-medium flex items-center gap-2 shadow-md max-w-md w-full text-center">
        <i class="bi bi-check-circle-fill text-green-600 text-lg"></i>
        <span class="font-bold">{{ session('message-success') }}</span>
    </div>
</div>
@endif
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
            <div class="flex justify-center my-8">
                <div class="flex items-center gap-2 bg-white px-4 py-3 rounded-xl shadow-md border border-gray-200">
                    @if ($categories->onFirstPage())
                        <span class="px-3 py-1 text-gray-400 cursor-not-allowed">← Prev</span>
                    @else
                        <a href="{{ $categories->previousPageUrl() }}" class="px-3 py-1 rounded-lg bg-teal-600 text-white hover:bg-teal-700 transition">← Prev</a>
                    @endif

                    @foreach ($categories->getUrlRange(1, $categories->lastPage()) as $page => $url)
                        @if ($page == $categories->currentPage())
                            <span class="px-3 py-1 rounded-lg bg-teal-600 text-white font-semibold">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="px-3 py-1 rounded-lg bg-teal-100 text-teal-700 hover:bg-teal-200 transition">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if ($categories->hasMorePages())
                        <a href="{{ $categories->nextPageUrl() }}" class="px-3 py-1 rounded-lg bg-teal-600 text-white hover:bg-teal-700 transition">Next →</a>
                    @else
                        <span class="px-3 py-1 text-gray-400 cursor-not-allowed">Next →</span>
                    @endif
                </div>
            </div>

        </div>


</div>



<x-user-footer></x-user-footer>
</body>