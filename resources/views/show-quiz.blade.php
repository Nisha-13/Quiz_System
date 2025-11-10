<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Categories</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- ✅ Tailwind + Vite --}}
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>

<body class="bg-gray-100 font-sans">

    <!-- ✅ Navbar -->
    <x-navbar name="{{ $name }}"></x-navbar>

    <!-- ✅ Add Category Form -->
    <div class="flex justify-center mt-24">
            <h2 class="text-2xl font-semibold text-blue-600 mb-4 text-center">Quiz Name: {{$quizName}}
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
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($mcqs as $mcq)
                            <tr class="{{ $loop->odd ? 'bg-blue-50' : 'bg-white' }} border-b">
                                <td class="px-6 py-3 font-medium text-gray-900">{{ $mcq->id }}</td>
                                <td class="px-6 py-3">{{ $mcq->question }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>



    <!-- ✅ Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- ✅ Toggle Animation Script -->
    <script>
        const toggleBtn = document.getElementById('menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        const toggleIcon = toggleBtn?.querySelector('i');
        let isOpen = false;

        toggleBtn?.addEventListener('click', () => {
            isOpen = !isOpen;
            if (isOpen) {
                mobileMenu.classList.remove('max-h-0');
                mobileMenu.classList.add('max-h-96', 'py-4');
                toggleIcon.classList.replace('bi-list', 'bi-x');
            } else {
                mobileMenu.classList.add('max-h-0');
                mobileMenu.classList.remove('max-h-96', 'py-4');
                toggleIcon.classList.replace('bi-x', 'bi-list');
            }
        });
    </script>

</body>
</html>
