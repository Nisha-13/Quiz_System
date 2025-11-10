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
        <div class="bg-white shadow-lg rounded-2xl p-8 w-full max-w-md">

            <!-- ✅ Success Message -->
            @if(session('category_name'))
                <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2 shadow-sm">
                    <i class="bi bi-check-circle-fill text-green-600 text-lg"></i>
                    <span class="font-medium">{{ session('category_name') }}</span>
                </div>
            @endif

            <h2 class="text-2xl font-semibold text-blue-600 mb-4 text-center">Add New Category</h2>

            <form action="/add-category" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Category Name</label>
                    <input type="text" name="category_name" placeholder="Enter category name"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 outline-none">
                </div>
                @error('category_name')
                <div class="text-red-600 text-sm font-medium mt-1 flex items-center gap-1">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    {{ $message }}
                </div>
                @enderror

                <button type="submit"
                        class="w-full bg-gradient-to-r from-blue-600 to-cyan-500 text-white font-semibold py-2 rounded-lg hover:opacity-90 transition">
                    Add Category
                </button>
            </form>
        </div>
    </div>

    <!-- ✅ Category List -->
    <div class="flex flex-col items-center mt-16 px-4 mb-10">
        <h1 class="text-3xl font-bold text-blue-700 mb-6 tracking-wide">
            Category List
        </h1>
        @if(session('category_delete'))
            <div class="mb-4 bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2 shadow-sm">
                <i class="bi bi-trash-fill text-red-600 text-lg"></i>
                <span class="font-medium">{{ session('category_delete') }}</span>
            </div>
        @endif
        <div class="bg-white rounded-2xl shadow-md overflow-hidden w-full max-w-4xl">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-700 border-collapse">
            <thead class="bg-gradient-to-r from-blue-600 to-cyan-500 text-white">
                <tr>
                    <th class="px-6 py-3">ID</th>
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Creator</th>
                    <th class="px-6 py-3 text-center">Action</th>
                </tr>
            </thead>

            <tbody>
                @foreach($categories as $category)
                    <tr class="{{ $loop->odd ? 'bg-blue-50' : 'bg-white' }} border-b">
                        <td class="px-6 py-3 font-medium text-gray-900">{{ $category->id }}</td>
                        <td class="px-6 py-3">{{ $category->name }}</td>
                        <td class="px-6 py-3">{{ $category->creator }}</td>
                        <td class="px-6 py-3 text-center space-x-4">
                            <a href="/quiz-list/{{$category->id}}/{{$category->name}}" title="View">
                                <i class="bi bi-eye-fill text-lg text-blue-600 hover:text-blue-800"></i>
                            </a>
                            <a href="/category/delete/{{$category->id}}" title="Delete">
                                <i class="bi bi-trash-fill text-lg text-red-600 hover:text-red-800"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach

                @if($categories->isEmpty())
                    <tr>
                        <td colspan="4" class="text-center py-6 text-gray-500">No categories found</td>
                    </tr>
                @endif
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
