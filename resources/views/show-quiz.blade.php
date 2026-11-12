<x-admin-layout :name="$name" :stats="$stats" title="Manage Quiz" heading="{{ $quizName }}" subheading="Category: {{ $quiz->category->name ?? 'Uncategorized' }} &bull; {{ $mcqs->count() }} questions">

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">

    <!-- Left: Question Bank -->
    <div class="xl:col-span-2 space-y-4">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 flex-wrap gap-3">
                <div class="flex items-center gap-3">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Question Bank</h2>
                        <p class="text-sm text-slate-500">Review or remove individual questions</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="user-quiz-list/{{ $quiz->category_id ?? '' }}/{{ $quiz->category->name ?? '' }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-bold text-teal-700 bg-teal-50 hover:bg-teal-100 transition" title="Preview as user">
                        <i class="bi bi-eye"></i> Player View
                    </a>
                    <span class="text-xs font-bold px-3 py-1.5 rounded-full bg-slate-100 text-slate-600">{{ $mcqs->count() }} items</span>
                </div>
            </div>

            <div class="divide-y divide-slate-100">
                @foreach($mcqs as $key => $mcq)
                    <div class="p-5 sm:px-6 hover:bg-slate-50/60 transition-colors">
                        <div class="flex items-start gap-4">
                            <span class="shrink-0 w-8 h-8 rounded-lg bg-gradient-to-br from-teal-600 to-emerald-600 grid place-items-center text-white text-xs font-bold">{{ $key + 1 }}</span>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-slate-900">{{ $mcq->question }}</p>
                                <div class="mt-3 grid sm:grid-cols-2 gap-2 text-sm">
                                    <div class="flex items-center gap-2 px-3 py-2 rounded-lg {{ $mcq->correct_ans == 'a' ? 'bg-emerald-50 border border-emerald-200' : 'bg-slate-50 border border-slate-100' }}">
                                        <span class="w-5 h-5 rounded-full bg-teal-100 text-teal-600 text-[10px] font-bold grid place-items-center">A</span>
                                        <span class="text-slate-700">{{ $mcq->option_a }}</span>
                                        @if($mcq->correct_ans == 'a')<i class="bi bi-check-circle-fill text-emerald-500 ml-auto" title="Correct answer"></i>@endif
                                    </div>
                                    <div class="flex items-center gap-2 px-3 py-2 rounded-lg {{ $mcq->correct_ans == 'b' ? 'bg-emerald-50 border border-emerald-200' : 'bg-slate-50 border border-slate-100' }}">
                                        <span class="w-5 h-5 rounded-full bg-teal-100 text-teal-600 text-[10px] font-bold grid place-items-center">B</span>
                                        <span class="text-slate-700">{{ $mcq->option_b }}</span>
                                        @if($mcq->correct_ans == 'b')<i class="bi bi-check-circle-fill text-emerald-500 ml-auto" title="Correct answer"></i>@endif
                                    </div>
                                    <div class="flex items-center gap-2 px-3 py-2 rounded-lg {{ $mcq->correct_ans == 'c' ? 'bg-emerald-50 border border-emerald-200' : 'bg-slate-50 border border-slate-100' }}">
                                        <span class="w-5 h-5 rounded-full bg-teal-100 text-teal-600 text-[10px] font-bold grid place-items-center">C</span>
                                        <span class="text-slate-700">{{ $mcq->option_c }}</span>
                                        @if($mcq->correct_ans == 'c')<i class="bi bi-check-circle-fill text-emerald-500 ml-auto" title="Correct answer"></i>@endif
                                    </div>
                                    <div class="flex items-center gap-2 px-3 py-2 rounded-lg {{ $mcq->correct_ans == 'd' ? 'bg-emerald-50 border border-emerald-200' : 'bg-slate-50 border border-slate-100' }}">
                                        <span class="w-5 h-5 rounded-full bg-teal-100 text-teal-600 text-[10px] font-bold grid place-items-center">D</span>
                                        <span class="text-slate-700">{{ $mcq->option_d }}</span>
                                        @if($mcq->correct_ans == 'd')<i class="bi bi-check-circle-fill text-emerald-500 ml-auto" title="Correct answer"></i>@endif
                                    </div>
                                </div>
                            </div>
                            <a href="/mcq/delete/{{ $mcq->id }}"
                               onclick="return confirm('Delete this question?')"
                               title="Delete question"
                               class="shrink-0 inline-flex w-9 h-9 items-center justify-center rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition">
                               <i class="bi bi-trash-fill"></i>
                            </a>
                        </div>
                    </div>
                @endforeach

                @if($mcqs->isEmpty())
                    <div class="text-center py-14 text-slate-400">
                        <i class="bi bi-inbox text-4xl block mb-3"></i>
                        <p class="font-medium">No questions in this quiz yet.</p>
                        <p class="text-sm mt-1">Add the first question using the panel on the right.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right: Integrated add-question panel -->
    <div class="xl:sticky xl:top-24">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden">
            <div class="bg-gradient-to-r from-teal-600 to-emerald-600 px-6 py-4 text-white">
                <h3 class="font-bold flex items-center gap-2"><i class="bi bi-plus-square"></i> Add Question</h3>
                <p class="text-xs text-teal-100/90 mt-0.5">Adds directly to "{{ str_replace('-', ' ', $quiz->name) }}" &mdash; no page switch</p>
            </div>

            <form action="/add-mcq" method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="quiz_id" value="{{ $quiz->id }}">

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Question</label>
                    <textarea name="question" rows="3" required minlength="5" placeholder="Type your question here&hellip;"
                              class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 focus:border-transparent outline-none transition resize-none"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-1 gap-3">
                    @foreach(['a','b','c','d'] as $opt)
                    <div class="flex items-center gap-2 rounded-xl border border-slate-200 pl-2 pr-3 py-1.5 focus-within:ring-2 focus-within:ring-teal-500 transition">
                        <label class="cursor-pointer shrink-0">
                            <input type="radio" name="correct_ans" value="{{ $opt }}" required class="peer sr-only">
                            <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-500 text-[11px] font-bold grid place-items-center uppercase peer-checked:bg-emerald-500 peer-checked:text-white transition">{{ strtoupper($opt) }}</span>
                        </label>
                        <input type="text" name="option_{{ $opt }}" required placeholder="Option {{ strtoupper($opt) }}"
                               class="flex-1 min-w-0 bg-transparent text-sm py-1.5 outline-none placeholder-slate-400">
                    </div>
                    @endforeach
                </div>
                <p class="text-[11px] text-slate-400"><i class="bi bi-check2-circle mr-1"></i>Click a letter badge to mark the correct answer</p>

                <button type="submit" name="submit" value="add-more"
                        class="w-full bg-gradient-to-r from-teal-600 to-emerald-600 text-white font-bold py-3 rounded-xl shadow-lg shadow-teal-500/30 hover:shadow-teal-500/50 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                    <i class="bi bi-plus-circle"></i> Add Question
                </button>
                <p class="text-center text-[11px] text-slate-400">The question appears instantly in the Question Bank on the left</p>
            </form>
        </div>
    </div>
</div>

</x-admin-layout>
