<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Attempted Quiz</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  @vite('resources/css/app.css')
</head>
<body class="bg-gradient-to-b from-teal-50 to-white min-h-screen flex flex-col">

  <!-- Navbar -->
  <x-user-navbar></x-user-navbar>

  <main class="flex-grow container mx-auto px-4 py-24">
    <div class="max-w-5xl mx-auto">
      <header class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-extrabold text-teal-700">Attempted Quiz</h1>
        <p class="text-sm text-gray-600">Your quiz attempts and status</p>
      </header>

      <div class="bg-white rounded-2xl shadow p-6 border border-teal-200">
        <div class="overflow-x-auto">
          <table class="w-full table-auto text-sm text-left text-gray-700">
            <thead class="bg-teal-600 text-white">
              <tr>
                <th class="px-4 py-3 w-16">S.No</th>
                <th class="px-4 py-3">Quiz Name</th>
                <th class="px-4 py-3 w-40">Status</th>
              </tr>
            </thead>

            <tbody>
              @foreach($quizRecord as $key => $record)
              <tr class="border-b even:bg-teal-50">
                <td class="px-4 py-3 font-medium">{{ $key + 1 }}</td>
                <td class="px-4 py-3">{{ $record->name ?? 'N/A' }}</td>
                <td class="px-4 py-3">
                  @if($record->status == 2)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                      ✔ Completed
                    </span>
                  @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                      ✖ Incomplete
                    </span>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>

      <!-- Back button -->
      <div class="mt-6 text-center">
        <a href="/" class="inline-block bg-teal-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-teal-700 transition">
          ← Back to Home
        </a>
      </div>
    </div>
  </main>

  <x-user-footer></x-user-footer>
</body>
</html>
