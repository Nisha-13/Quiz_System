<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Quiz</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- ✅ Tailwind + Vite --}}
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>

<body class="bg-gray-100 font-sans">

    <!-- ✅ Navbar -->
    <x-navbar name="{{ $name }}"></x-navbar>


      <div class="flex justify-center mt-24">
        <div class="bg-white shadow-lg rounded-2xl p-8 w-full max-w-md">
            @if(!session('quizDetails'))
            <h2 class="text-2xl font-semibold text-blue-600 mb-4 text-center">Add Quiz</h2>
            <form action="/add-quiz" method="get" class="space-y-4">
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Quiz Name</label>
                    <input type="text" name="quiz_name" placeholder="Enter Quiz name" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 outline-none">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Category Name</label>
                    <select type="text" name="category_id"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 outline-none">
                           @foreach($categories as $category)
                           <option value="{{$category->id}}">{{$category->name}}</option>
                           @endforeach
                    </select>
                </div>
                <button type="submit"
                        class="w-full bg-gradient-to-r from-blue-600 to-cyan-500 text-white font-semibold py-2 rounded-lg hover:opacity-90 transition">
                    Add Quiz
                </button>
            </form>
            @else


            <span class="block text-gray-700 font-medium text-lg mb-2">
                Quiz Name: <span class="text-blue-600 font-semibold">{{ session('quizDetails')->name }}</span>
            </span>
            <p class="block text-gray-700 font-medium text-lg mb-2">Total MCQs: {{$totalMCQs}}
                @if($totalMCQs>0)
                <a href="/show-quiz/{{session('quizDetails')->id}}/{{ session('quizDetails')->name }}" class="text-sm text-green-500">Show MCQs</a>
                @endif
            </p>
            <h2 class="text-2xl font-semibold text-blue-600 mb-4 text-center">Add MCQs</h2>
            <form action="add-mcq" method="post" class="space-y-4">
                @csrf
                <div>
                    <textarea name="question" 
                            placeholder="Enter your Question name"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 outline-none"></textarea>
                
                @error('question')
                <div class="text-red-500">{{$message}}</div>
                @enderror
                </div>
                <div>
                    <input type="text" name="option_a" placeholder="Enter First Option"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 outline-none">
                
                @error('option_a')
                <div class="text-red-500">{{$message}}</div>
                @enderror
                </div>
                <div>
                    <input type="text" name="option_b" placeholder="Enter Second Option"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 outline-none">
                @error('option_b')
                <div class="text-red-500">{{$message}}</div>
                @enderror
                </div>
                <div>
                    <input type="text" name="option_c" placeholder="Enter Third Option"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 outline-none">
                @error('option_c')
                <div class="text-red-500">{{$message}}</div>
                @enderror
                </div>
                <div>
                    <input type="text" name="option_d" placeholder="Enter Forth Option"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 outline-none">
                @error('option_d')
                <div class="text-red-500">{{$message}}</div>
                @enderror
                </div>
                <div>
                    <select type="text" name="correct_ans" placeholder="Enter First Option"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 outline-none">
                        <option value="">Select Right Answer</option>
                        <option value="a">A</option>
                        <option value="b">B</option>
                        <option value="c">C</option>
                        <option value="d">D</option>
                    </select>
                @error('correct_ans')
                <div class="text-red-500">{{$message}}</div>
                @enderror
                </div>
                <button type="submit" value="add-more" name="submit"
                        class="w-full bg-gradient-to-r from-blue-600 to-cyan-500 text-white font-semibold py-2 rounded-lg hover:opacity-90 transition">
                    Add More
                </button>
                <button type="submit" value="done" name="submit"
                        class="w-full bg-gradient-to-r from-green-600 to-green-500 text-white font-semibold py-2 rounded-lg hover:opacity-90 transition">
                    Add and Submit
                </button>
                <a href="/end-quiz" class="w-full bg-gradient-to-r block text-center from-red-600 to-red-500 text-white font-semibold py-2 rounded-lg hover:opacity-90 transition">
                    Finish Quiz
                </a>
            </form>
            @endif
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