<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>MCQs Page</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  @vite('resources/css/app.css')
  @vite('resources/js/app.js')
</head>

<body class="bg-gray-100 font-sans">
  <x-user-navbar></x-user-navbar>

  <!-- ✅ Quiz Container -->
  <section class="flex flex-col items-center mt-28 px-4 mb-16">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl p-8">

      <!-- Header -->
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-teal-700 mb-2">
          {{$quizName}}
        </h1>
        <p class="text-gray-600 text-sm">
          Answer all questions carefully and click Submit when done.
        </p>
        <h1 class="text-3xl font-bold text-teal-700 mb-2">
          Total Question: {{Session('currentQuiz')['totalMcq']}}
        </h1>
        <h1 class="text-3xl font-bold text-teal-700 mb-2">
          {{Session('currentQuiz')['currentMcq']}} of {{Session('currentQuiz')['totalMcq']}}
        </h1>
      </div>

      <!-- ✅ Static MCQ Form -->
      <form class="space-y-8" action="/submit-next/{{$mcqData->id}}" method="post">
        @csrf
        <input type="hidden" name="id" value="{{$mcqData->id}}">
        <!-- Question 1 -->
        <div class="border border-gray-200 rounded-xl p-5 bg-gray-50 hover:shadow-md transition">
          <h2 class="font-semibold text-gray-800 mb-4">
            {{$mcqData->question}}
          </h2>

          <div class="space-y-3">
            <label class="flex items-center gap-3 cursor-pointer bg-white p-3 rounded-lg border border-gray-200 hover:bg-teal-50 transition">
              <input type="radio" name="option" value="a" class="text-teal-600 focus:ring-teal-500">
              <span class="text-gray-700">{{$mcqData->option_a}}</span>
            </label>

            <label class="flex items-center gap-3 cursor-pointer bg-white p-3 rounded-lg border border-gray-200 hover:bg-teal-50 transition">
              <input type="radio" name="option" value="b" class="text-teal-600 focus:ring-teal-500">
              <span class="text-gray-700">{{$mcqData->option_b}}</span>
            </label>

            <label class="flex items-center gap-3 cursor-pointer bg-white p-3 rounded-lg border border-gray-200 hover:bg-teal-50 transition">
              <input type="radio" name="option" value="c" class="text-teal-600 focus:ring-teal-500">
              <span class="text-gray-700">{{$mcqData->option_c}}</span>
            </label>

            <label class="flex items-center gap-3 cursor-pointer bg-white p-3 rounded-lg border border-gray-200 hover:bg-teal-50 transition">
              <input type="radio" name="option" value="d" class="text-teal-600 focus:ring-teal-500">
              <span class="text-gray-700">{{$mcqData->option_d}}</span>
            </label>
          </div>

        <!-- ✅ Submit Button -->
        <div class="text-center pt-6">
          <button type="submit"
                  class="bg-gradient-to-r from-teal-600 to-cyan-500 cursor-pointer text-white font-semibold px-8 py-3 rounded-lg hover:from-teal-700 hover:to-cyan-600 transform hover:-translate-y-0.5 transition">
            Submit Quiz
          </button>
        </div>
      </form>
    </div>
  </section>

  <x-user-footer></x-user-footer>
</body>
</html>
