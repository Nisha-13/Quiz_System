<x-admin-layout :name="$name" :stats="$stats ?? []" title="Attempts" heading="Quiz Attempts" subheading="Every attempt made by your users">

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
        <div>
            <h2 class="text-lg font-bold text-slate-900">All Attempts</h2>
            <p class="text-sm text-slate-500">Newest first</p>
        </div>
        <span class="text-xs font-bold px-3 py-1.5 rounded-full bg-teal-50 text-teal-600">{{ $records->total() }} total</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-4">#</th>
                    <th class="px-6 py-4">User</th>
                    <th class="px-6 py-4">Quiz</th>
                    <th class="px-6 py-4 text-center">Score</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4">Started</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($records as $key => $record)
                    @php
                        $score = $scores[$record->id] ?? null;
                        $total = $score->total ?? 0;
                        $correct = $score->correct ?? 0;
                        $pct = $total > 0 ? round($correct / $total * 100) : 0;
                        $passed = $record->status == 2 && $pct >= 70;
                    @endphp
                    <tr class="hover:bg-teal-50/40 transition-colors">
                        <td class="px-6 py-4 font-semibold text-slate-400">{{ ($records->currentPage() - 1) * $records->perPage() + $key + 1 }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <span class="w-9 h-9 rounded-full bg-gradient-to-br from-teal-500 to-emerald-500 grid place-items-center text-white text-xs font-bold shrink-0">{{ strtoupper(substr($record->user->name ?? '?', 0, 1)) }}</span>
                                <span class="font-semibold text-slate-800">{{ $record->user->name ?? 'Deleted user' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ $record->quiz->name ?? 'Deleted quiz' }}</td>
                        <td class="px-6 py-4 text-center">
                            @if ($total > 0)
                                <span class="text-xs font-bold px-3 py-1 rounded-full {{ $pct >= 70 ? 'bg-emerald-50 text-emerald-600' : ($pct >= 40 ? 'bg-amber-50 text-amber-600' : 'bg-rose-50 text-rose-600') }}">
                                    {{ $correct }}/{{ $total }} &middot; {{ $pct }}%
                                </span>
                            @else
                                <span class="text-xs text-slate-400">&mdash;</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if ($record->status == 2)
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full {{ $passed ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    <i class="bi bi-{{ $passed ? 'award-fill' : 'check-circle-fill' }}"></i> {{ $passed ? 'Passed' : 'Completed' }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full bg-amber-50 text-amber-700"><i class="bi bi-hourglass-split"></i> In Progress</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-500">{{ optional($record->created_at)->format('d M Y') }}</td>
                    </tr>
                @endforeach

                @if ($records->isEmpty())
                    <tr><td colspan="6" class="text-center py-10 text-slate-400"><i class="bi bi-inbox text-3xl block mb-2"></i>No attempts yet</td></tr>
                @endif
            </tbody>
        </table>
    </div>

    <x-pagination :paginator="$records" />
</div>

</x-admin-layout>
