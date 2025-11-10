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
    <div class="flex justify-center mt-24">
            <h2 class="text-2xl font-semibold text-blue-600 mb-4 text-center">Category Nanme: {{$category}}
                <a href="/add-quiz" class="text-yellow-400 text-sm">Back</a>
            </h2>
    </div>

    <!-- ✅ Category List -->
    <div class="flex flex-col items-center mt-12 px-4 mb-10">
        <div class="bg-white rounded-2xl shadow-md overflow-hidden w-full max-w-4xl">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700 border-collapse">
                    <thead class="bg-gradient-to-r from-blue-600 to-cyan-500 text-white">
                        <tr>
                            <th class="px-6 py-3">Quiz ID</th>
                            <th class="px-6 py-3">Question</th>
                            <th class="px-6 py-3 text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($quizData as $item)
                            <tr class="{{ $loop->odd ? 'bg-blue-50' : 'bg-white' }} border-b">
                                <td class="px-6 py-3 font-medium text-gray-900">{{ $item->id }}</td>
                                <td class="px-6 py-3">{{ $item->name }}</td>
                                <td class="px-6 py-3 text-center">
                                    <a href="/show-quiz/{{$item->id}}/{{$item->name}}" title="View">
                                        <i class="bi bi-eye-fill text-lg text-blue-600 hover:text-blue-800"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>



<x-user-footer></x-user-footer>
</body>
