<x-admin-layout :name="$name" :stats="$stats" title="Quiz List" heading="Quizzes in {{ $category }}" subheading="Manage every quiz in this category">

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <a href="/admin-categories" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                <i class="bi bi-arrow-left"></i> Back
            </a>
            <div>
                <h2 class="text-lg font-bold text-slate-900">{{ $category }}</h2>
                <p class="text-sm text-slate-500">{{ $quizData->count() }} quizzes</p>
            </div>
        </div>
        <a href="/add-quiz" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-teal-600 to-emerald-600 text-white text-sm font-semibold shadow-lg shadow-teal-500/25 hover:shadow-teal-500/40 transition-all">
            <i class="bi bi-plus-lg"></i> New Quiz
        </a>
    </div>

    <div class="p-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach($quizData as $item)
            <div class="group relative rounded-2xl border border-slate-200 hover:border-teal-300 bg-white p-5 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">
                <div class="flex items-start justify-between">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-teal-500 to-emerald-500 grid place-items-center shadow-md shadow-teal-500/25">
                        <i class="bi bi-journal-richtext text-white text-lg"></i>
                    </div>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-slate-100 text-slate-500">#{{ $item->id }}</span>
                </div>
                <h3 class="mt-4 font-bold text-slate-900 group-hover:text-teal-600 transition truncate" title="{{ $item->name }}">{{ $item->name }}</h3>
                <p class="text-sm text-slate-500 mt-1"><i class="bi bi-question-circle mr-1"></i>{{ $item->mcqs_count }} questions</p>
                <div class="mt-4 flex items-center gap-2">
                    <a href="/show-quiz/{{ $item->id }}/{{ $item->name }}"
                       class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold bg-teal-600 text-white hover:bg-teal-700 transition">
                        <i class="bi bi-eye-fill"></i> View
                    </a>
                    <a href="/quiz/delete/{{ $item->id }}"
                       onclick="return confirm('Delete this quiz and all of its questions?')"
                       class="inline-flex w-10 h-10 items-center justify-center rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition">
                        <i class="bi bi-trash-fill"></i>
                    </a>
                </div>
            </div>
        @endforeach

        @if($quizData->isEmpty())
            <div class="col-span-full text-center py-14 text-slate-400">
                <i class="bi bi-inbox text-4xl block mb-3"></i>
                <p class="font-medium">No quizzes in this category yet.</p>
                <a href="/add-quiz" class="text-teal-600 font-semibold hover:underline">Create one now &raquo;</a>
            </div>
        @endif
    </div>
</div>

</x-admin-layout>

