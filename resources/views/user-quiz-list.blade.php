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



<!-- ✅ Add Category Form -->
<div class="flex justify-center mt-24 px-4">
    <h2 class="text-2xl font-semibold text-teal-700 mb-4 text-center">
        Category Name: 
        <span class="font-bold text-teal-800">{{str_replace('-', ' ', $category)}}</span>
    </h2>
</div>

<!-- ✅ Category List -->
<div class="flex flex-col items-center mt-12 px-4 mb-10">
    <div class="bg-white rounded-2xl shadow-md overflow-hidden w-full max-w-5xl">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left text-gray-700 border-collapse">
                <thead class="bg-teal-600 text-white text-xs sm:text-sm">
                    <tr>
                        <th class="px-4 sm:px-6 py-3">Quiz ID</th>
                        <th class="px-4 sm:px-6 py-3">Name</th>
                        <th class="px-4 sm:px-6 py-3">Total Mcqs</th>
                        <th class="px-4 sm:px-6 py-3 text-center">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($quizData as $item)
                        <tr class="{{ $loop->odd ? 'bg-teal-50' : 'bg-white' }} border-b hover:bg-teal-100 transition-colors duration-200">
                            <td class="px-4 sm:px-6 py-3 font-medium text-gray-900">{{ $item->id }}</td>
                            <td class="px-4 sm:px-6 py-3">{{ $item->name }}</td>
                            <td class="px-4 sm:px-6 py-3">{{ $item->mcq_count }}</td>
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