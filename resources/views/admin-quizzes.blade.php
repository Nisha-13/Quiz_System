<x-admin-layout :name="$name" :stats="$stats ?? []" title="Quizzes" heading="Quizzes" subheading="Every quiz on your platform">

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
        <div>
            <h2 class="text-lg font-bold text-slate-900">All Quizzes</h2>
            <p class="text-sm text-slate-500">Newest first</p>
        </div>
        <a href="/add-quiz" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-teal-600 to-emerald-600 text-white text-sm font-semibold shadow-lg shadow-teal-500/25 hover:shadow-teal-500/40 hover:-translate-y-px transition-all">
            <i class="bi bi-plus-lg"></i> New Quiz
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-4">#</th>
                    <th class="px-6 py-4">Quiz</th>
                    <th class="px-6 py-4">Category</th>
                    <th class="px-6 py-4 text-center">Questions</th>
                    <th class="px-6 py-4 text-center">Attempts</th>
                    <th class="px-6 py-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($quizzes as $key => $quiz)
                    <tr class="hover:bg-teal-50/40 transition-colors">
                        <td class="px-6 py-4 font-semibold text-slate-400">{{ ($quizzes->currentPage() - 1) * $quizzes->perPage() + $key + 1 }}</td>
                        <td class="px-6 py-4 font-semibold text-slate-800">{{ $quiz->name }}</td>
                        <td class="px-6 py-4">
                            @if ($quiz->category)
                                <a href="/quiz-list/{{ $quiz->category_id }}/{{ $quiz->category->name }}" class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full bg-sky-50 text-sky-700 hover:bg-sky-100 transition">
                                    <i class="bi bi-collection-fill"></i> {{ $quiz->category->name }}
                                </a>
                            @else
                                <span class="text-slate-400">&mdash;</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs font-bold px-3 py-1 rounded-full bg-amber-50 text-amber-600">{{ $quiz->mcqs_count }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-xs font-bold px-3 py-1 rounded-full bg-rose-50 text-rose-600">{{ $quiz->records_count }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="/show-quiz/{{ $quiz->id }}/{{ $quiz->name }}" title="Manage"
                               class="inline-flex w-9 h-9 rounded-lg bg-teal-50 text-teal-600 hover:bg-teal-600 hover:text-white transition items-center justify-center mr-1">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <a href="/quiz/delete/{{ $quiz->id }}" title="Delete"
                               onclick="return confirm('Delete this quiz? Its questions and attempts will be removed too.')"
                               class="inline-flex w-9 h-9 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition items-center justify-center">
                                <i class="bi bi-trash-fill"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach

                @if ($quizzes->isEmpty())
                    <tr><td colspan="6" class="text-center py-10 text-slate-400"><i class="bi bi-inbox text-3xl block mb-2"></i>No quizzes yet &mdash; create your first one!</td></tr>
                @endif
            </tbody>
        </table>
    </div>

    <x-pagination :paginator="$quizzes" />
</div>

</x-admin-layout>
