<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Users List</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Tailwind + Vite --}}
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>

<body class="bg-gray-100 font-sans">

    <!-- ✅ Navbar -->
    <x-navbar name="{{ $name }}"></x-navbar>

    <!-- ✅ Users List -->
    <div class="flex flex-col items-center mt-24 px-4 mb-10">
        <h1 class="text-3xl font-bold text-blue-700 mb-6 tracking-wide">
            Users List
        </h1>

        @if(session('user_deleted'))
            <div class="mb-4 bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2 shadow-sm">
                <i class="bi bi-trash-fill text-red-600 text-lg"></i>
                <span class="font-medium">{{ session('user_deleted') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-md overflow-hidden w-full max-w-5xl">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700 border-collapse">
                    <thead class="bg-gradient-to-r from-blue-600 to-cyan-500 text-white">
                        <tr>
                            <th class="px-6 py-3">ID</th>
                            <th class="px-6 py-3">Name</th>
                            <th class="px-6 py-3">Email</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($users as $key => $user)
                            <tr class="{{ $loop->odd ? 'bg-blue-50' : 'bg-white' }} border-b">
                                <td class="px-6 py-3 font-medium text-gray-900">{{ $key+1 }}</td>
                                <td class="px-6 py-3">{{ $user->name }}</td>
                                <td class="px-6 py-3">{{ $user->email }}</td>
                            </tr>
                        @endforeach

                        @if($users->isEmpty())
                            <tr>
                                <td colspan="5" class="text-center py-6 text-gray-500">
                                    No users found
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="flex justify-center mt-10 mb-8">
    <div class="flex items-center gap-2 bg-white px-6 py-4 rounded-2xl shadow-lg border border-blue-200">
        
        {{-- Previous Button --}}
        @if ($users->onFirstPage())
            <span class="px-4 py-2 text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">← Prev</span>
        @else
            <a href="{{ $users->previousPageUrl() }}" 
               class="px-4 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-cyan-500 text-white font-medium shadow-md hover:from-blue-700 hover:to-cyan-600 transition-all duration-200">
               ← Prev
            </a>
        @endif

        {{-- Page Numbers --}}
        @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
            @if ($page == $users->currentPage())
                <span class="px-4 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-cyan-500 text-white font-semibold shadow-md">
                    {{ $page }}
                </span>
            @else
                <a href="{{ $url }}" 
                   class="px-4 py-2 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 transition-all duration-200">
                   {{ $page }}
                </a>
            @endif
        @endforeach

        {{-- Next Button --}}
        @if ($users->hasMorePages())
            <a href="{{ $users->nextPageUrl() }}" 
               class="px-4 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-cyan-500 text-white font-medium shadow-md hover:from-blue-700 hover:to-cyan-600 transition-all duration-200">
               Next →
            </a>
        @else
            <span class="px-4 py-2 text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">Next →</span>
        @endif
    </div>
</div>

    </div>

    <!-- ✅ Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- ✅ Navbar Toggle Script -->
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
