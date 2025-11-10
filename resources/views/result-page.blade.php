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
<body class="bg-gradient-to-b from-teal-50 to-white min-h-screen flex flex-col">

    <!-- ✅ Navbar -->
    <x-user-navbar></x-user-navbar>

    <div class="flex flex-col items-center flex-grow pt-24 px-4 pb-10">
        <h1 class="text-4xl font-extrabold text-teal-700 mb-6 tracking-wide">
            🎯 Quiz Result
        </h1>

        <div class="bg-white shadow-xl rounded-2xl w-full max-w-5xl p-6 border border-teal-300">
            <!-- ✅ Certificate link if score > 70% -->
            @if($correctAnswers * 100 / count($resultData) > 70)
                <div class="text-center mb-4">
                    <a href="/certificate"
                       class="inline-block bg-teal-600 text-white font-semibold px-6 py-2 rounded-lg 
                              hover:bg-teal-700 hover:text-yellow-200 transition-all duration-200 shadow">
                        🎓 View & Download Certificate
                    </a>
                </div>
            @endif

            <!-- ✅ Score Summary -->
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-teal-700">
                    {{$correctAnswers}} out of {{count($resultData)}} Correct
                </h2>
                <p class="text-gray-600 mt-2 text-sm">
                    You answered {{ round(($correctAnswers/count($resultData))*100) }}% of questions correctly.
                </p>
            </div>

            <!-- ✅ Result Table -->
            <div class="overflow-x-auto">
                <table class="w-full border border-teal-200 rounded-xl overflow-hidden">
                    <thead class="bg-teal-600 text-white text-left">
                        <tr>
                            <th class="px-4 py-3">S.No</th>
                            <th class="px-4 py-3">Question</th>
                            <th class="px-4 py-3">Your Answer</th>
                            <th class="px-4 py-3">Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($resultData as $key=>$item)
                            <tr class="even:bg-teal-50 hover:bg-teal-100 transition-colors duration-200">
                                <td class="px-4 py-2 font-semibold text-gray-700">{{ $key + 1 }}</td>
                                <td class="px-4 py-2 text-gray-800">{{ $item->question }}</td>
                                <td class="px-4 py-2 text-gray-600">{{ strtoupper($item->select_answer) }}</td>
                                @if($item->is_correct)
                                    <td class="px-4 py-2 text-green-600 font-bold">✔ Correct</td>
                                @else
                                    <td class="px-4 py-2 text-red-500 font-bold">✘ Incorrect</td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ✅ Back Button -->
        <div class="mt-8">
            <a href="/"
               class="bg-teal-600 text-white px-6 py-2 rounded-lg font-semibold shadow 
                      hover:bg-teal-700 hover:text-yellow-200 transition">
                ⬅ Back to Dashboard
            </a>
        </div>
    </div>

    <!-- ✅ Footer -->
    <x-user-footer></x-user-footer>
</body>
</html>
