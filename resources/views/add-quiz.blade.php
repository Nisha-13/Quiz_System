<x-admin-layout :name="$name" :stats="$stats" title="Create Quiz" heading="Create Quiz" subheading="Build quizzes and add multiple-choice questions">

<div class="grid gap-6 lg:grid-cols-3">

    <!-- &hellip; Left Column: Quiz / MCQ Form -->
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sm:p-8">

        @if(session('quiz_info'))
        <div class="mb-5 flex items-center gap-2 bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 rounded-xl text-sm">
            <i class="bi bi-info-circle-fill"></i>
            <span class="font-medium">{{ session('quiz_info') }}</span>
        </div>
        @endif

        @if(!session('quizDetails'))
        <!-- Step 1: Create Quiz -->
        <div class="flex items-center gap-3 mb-6">
            <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-teal-600 to-emerald-600 grid place-items-center text-white font-bold">1</span>
            <div>
                <h2 class="text-lg font-bold text-slate-900 leading-tight">Step 1 &mdash; Quiz Details</h2>
                <p class="text-xs text-slate-500">Name your quiz and pick a category</p>
            </div>
        </div>

        <form action="/add-quiz" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Quiz Name</label>
                <input type="text" name="quiz_name" placeholder="e.g. JavaScript Fundamentals" required
                       class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition"
                       value="{{ old('quiz_name') }}">
                @error('quiz_name')
                <p class="text-rose-600 text-xs font-medium mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Category</label>
                <select name="category_id" required
                        class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm bg-white focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition">
                    <option value="" disabled selected>Choose a category&hellip;</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id')
                <p class="text-rose-600 text-xs font-medium mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full bg-gradient-to-r from-teal-600 to-emerald-600 text-white font-semibold py-3 rounded-xl shadow-lg shadow-teal-500/25 hover:shadow-teal-500/40 hover:-translate-y-px transition-all">
                <i class="bi bi-arrow-right mr-1"></i> Create &amp; Add Questions
            </button>
        </form>
        @else
        <!-- Step 2: Add MCQs -->
        <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-teal-600 to-emerald-600 grid place-items-center text-white font-bold">2</span>
                <div>
                    <h2 class="text-lg font-bold text-slate-900 leading-tight">Step 2 &mdash; Add Questions</h2>
                    <p class="text-xs text-slate-500">To: <span class="font-semibold text-teal-600">{{ session('quizDetails')->name }}</span></p>
                </div>
            </div>
            <a href="/show-quiz/{{ session('quizDetails')->id }}/{{ session('quizDetails')->name }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-teal-600 bg-teal-50 hover:bg-teal-100 transition">
                <i class="bi bi-collection"></i> Preview ({{ $totalMCQs }})
            </a>
        </div>

        <form action="/add-mcq" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Question</label>
                <textarea name="question" rows="3" placeholder="Type your question here&hellip;"
                    class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition">{{ old('question') }}</textarea>
                @error('question')
                <p class="text-rose-600 text-xs font-medium mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5"><span class="inline-flex w-5 h-5 rounded-full bg-teal-100 text-teal-600 text-xs font-bold items-center justify-center mr-1">A</span>Option A</label>
                    <input type="text" name="option_a" placeholder="Enter option A" value="{{ old('option_a') }}"
                        class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition">
                    @error('option_a')<p class="text-rose-600 text-xs font-medium mt-1.5">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5"><span class="inline-flex w-5 h-5 rounded-full bg-teal-100 text-teal-600 text-xs font-bold items-center justify-center mr-1">B</span>Option B</label>
                    <input type="text" name="option_b" placeholder="Enter option B" value="{{ old('option_b') }}"
                        class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition">
                    @error('option_b')<p class="text-rose-600 text-xs font-medium mt-1.5">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5"><span class="inline-flex w-5 h-5 rounded-full bg-teal-100 text-teal-600 text-xs font-bold items-center justify-center mr-1">C</span>Option C</label>
                    <input type="text" name="option_c" placeholder="Enter option C" value="{{ old('option_c') }}"
                        class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition">
                    @error('option_c')<p class="text-rose-600 text-xs font-medium mt-1.5">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5"><span class="inline-flex w-5 h-5 rounded-full bg-teal-100 text-teal-600 text-xs font-bold items-center justify-center mr-1">D</span>Option D</label>
                    <input type="text" name="option_d" placeholder="Enter option D" value="{{ old('option_d') }}"
                        class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition">
                    @error('option_d')<p class="text-rose-600 text-xs font-medium mt-1.5">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Correct Answer</label>
                <div class="grid grid-cols-4 gap-3">
                    @foreach(['a', 'b', 'c', 'd'] as $opt)
                    <label class="relative">
                        <input type="radio" name="correct_ans" value="{{ $opt }}" class="peer sr-only">
                        <div class="cursor-pointer text-center py-2.5 rounded-xl border-2 border-slate-200 text-sm font-bold text-slate-500 peer-checked:border-teal-600 peer-checked:bg-teal-600 peer-checked:text-white hover:border-teal-300 transition-all uppercase">{{ $opt }}</div>
                    </label>
                    @endforeach
                </div>
                @error('correct_ans')
                <p class="text-rose-600 text-xs font-medium mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                <button type="submit" name="submit" value="add-more"
                    class="flex-1 bg-gradient-to-r from-teal-600 to-emerald-600 text-white font-semibold py-3 rounded-xl shadow-lg shadow-teal-500/25 hover:shadow-teal-500/40 hover:-translate-y-px transition-all">
                    <i class="bi bi-plus-lg mr-1"></i> Add &amp; Continue
                </button>
                <button type="submit" name="submit" value="done"
                    class="flex-1 bg-gradient-to-r from-emerald-600 to-teal-500 text-white font-semibold py-3 rounded-xl shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 hover:-translate-y-px transition-all">
                    <i class="bi bi-check2-circle mr-1"></i> Publish Quiz
                </button>
                <a href="/end-quiz"
                    class="flex-1 text-center bg-rose-50 text-rose-600 border border-rose-200 font-semibold py-3 rounded-xl hover:bg-rose-100 transition">
                    <i class="bi bi-x-lg mr-1"></i> Discard
                </a>
            </div>
        </form>
        @endif
    </div>

    <!-- &hellip; Right Column: Live progress -->
    <div class="space-y-6">
        <div class="bg-gradient-to-br from-slate-900 to-teal-950 rounded-2xl p-6 text-white h-fit">
            <h3 class="text-sm font-bold uppercase tracking-wider text-teal-300 mb-4">Live Progress</h3>
            <div class="flex items-baseline gap-2">
                <span class="text-5xl font-extrabold">{{ $totalMCQs }}</span>
                <span class="text-slate-400 font-medium">questions added</span>
            </div>
            @if(session('quizDetails'))
            <div class="mt-5 space-y-2 text-sm">
                <div class="flex justify-between border-b border-white/10 pb-2"><span class="text-slate-400">Quiz</span><span class="font-semibold truncate max-w-[160px]" title="{{ session('quizDetails')->name }}">{{ session('quizDetails')->name }}</span></div>
                @php $cat = $categories->firstWhere('id', session('quizDetails')->category_id); @endphp
                <div class="flex justify-between pb-1"><span class="text-slate-400">Category</span><span class="font-semibold">{{ $cat?->name ?? '&mdash;' }}</span></div>
            </div>
            <div class="h-2.5 bg-white/10 rounded-full overflow-hidden mt-4">
                <div class="h-full bg-gradient-to-r from-teal-500 to-cyan-500 rounded-full transition-all duration-500" style="width: {{ min($totalMCQs * 10, 100) }}%"></div>
            </div>
            <p class="text-xs text-slate-500 mt-2">{{ min($totalMCQs * 10, 100) }}% toward a 10-question quiz</p>
            @else
            <div class="mt-5 text-sm text-slate-400 flex gap-2 items-start">
                <i class="bi bi-lightbulb mt-0.5"></i>
                <span>Create a quiz in Step 1, then questions you add will appear here in real time.</span>
            </div>
            @endif
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 h-fit">
            <h3 class="font-bold text-slate-900 mb-3 flex items-center gap-2"><i class="bi bi-stars text-amber-500"></i> Pro Tips</h3>
            <ul class="text-sm text-slate-500 space-y-2.5">
                <li class="flex gap-2"><span class="text-teal-500 font-bold">&bull;</span> Keep questions short and clear.</li>
                <li class="flex gap-2"><span class="text-teal-500 font-bold">&bull;</span> Aim for 10+ questions per quiz.</li>
                <li class="flex gap-2"><span class="text-teal-500 font-bold">&bull;</span> Users get a certificate for 70%+.</li>
            </ul>
        </div>
    </div>
</div>

</x-admin-layout>

