<x-admin-layout :name="$name" :stats="$stats ?? []" title="Questions" heading="Questions" subheading="Every multiple-choice question across all quizzes">

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
        <div>
            <h2 class="text-lg font-bold text-slate-900">All Questions</h2>
            <p class="text-sm text-slate-500">Newest first</p>
        </div>
        <span class="text-xs font-bold px-3 py-1.5 rounded-full bg-teal-50 text-teal-600">{{ $questions->total() }} total</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-4">#</th>
                    <th class="px-6 py-4">Question</th>
                    <th class="px-6 py-4">Quiz</th>
                    <th class="px-6 py-4 text-center">Answer</th>
                    <th class="px-6 py-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($questions as $key => $question)
                    <tr class="hover:bg-teal-50/40 transition-colors align-top">
                        <td class="px-6 py-4 font-semibold text-slate-400">{{ ($questions->currentPage() - 1) * $questions->perPage() + $key + 1 }}</td>
                        <td class="px-6 py-4">
                            <p class="font-semibold text-slate-800 max-w-md">{{ $question->question }}</p>
                            <div class="mt-2 flex flex-wrap gap-1.5 text-[11px]">
                                @foreach (['a' => $question->option_a, 'b' => $question->option_b, 'c' => $question->option_c, 'd' => $question->option_d] as $key => $option)
                                    <span class="px-2 py-1 rounded-lg {{ $key === $question->correct_ans ? 'bg-emerald-50 text-emerald-700 font-bold ring-1 ring-emerald-200' : 'bg-slate-50 text-slate-500' }}">
                                        {{ strtoupper($key) }}. {{ \Illuminate\Support\Str::limit($option, 28) }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ $question->quiz->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 font-bold uppercase">{{ $question->correct_ans }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if ($question->quiz)
                                <a href="/show-quiz/{{ $question->quiz_id }}/{{ $question->quiz->name }}" title="Open quiz"
                                   class="inline-flex w-9 h-9 rounded-lg bg-teal-50 text-teal-600 hover:bg-teal-600 hover:text-white transition items-center justify-center mr-1">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                            @endif
                            <a href="/mcq/delete/{{ $question->id }}" title="Delete"
                               onclick="return confirm('Delete this question?')"
                               class="inline-flex w-9 h-9 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition items-center justify-center">
                                <i class="bi bi-trash-fill"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach

                @if ($questions->isEmpty())
                    <tr><td colspan="5" class="text-center py-10 text-slate-400"><i class="bi bi-inbox text-3xl block mb-2"></i>No questions yet</td></tr>
                @endif
            </tbody>
        </table>
    </div>

    <x-pagination :paginator="$questions" />
</div>

</x-admin-layout>
