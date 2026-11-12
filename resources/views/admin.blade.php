<x-admin-layout :name="$name" :stats="$stats" title="Dashboard" heading="Admin Dashboard" subheading="Welcome back, {{ $name }} &mdash; here's what's happening on your platform.">

<!-- &hellip; Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-5">
    @foreach([
        ['label' => 'Registered Users', 'value' => $stats['users'], 'icon' => 'bi-people-fill', 'from' => 'from-teal-500', 'to' => 'to-emerald-500', 'url' => '/admin-users'],
        ['label' => 'Categories', 'value' => $stats['categories'], 'icon' => 'bi-collection-fill', 'from' => 'from-sky-500', 'to' => 'to-cyan-400', 'url' => '/admin-categories'],
        ['label' => 'Quizzes', 'value' => $stats['quizzes'], 'icon' => 'bi-journal-text', 'from' => 'from-amber-500', 'to' => 'to-orange-400', 'url' => '/admin-quizzes'],
        ['label' => 'Questions', 'value' => $stats['mcqs'], 'icon' => 'bi-question-circle-fill', 'from' => 'from-emerald-500', 'to' => 'to-teal-400', 'url' => '/admin-questions'],
        ['label' => 'Quiz Attempts', 'value' => $stats['attempts'], 'icon' => 'bi-clipboard-check-fill', 'from' => 'from-rose-500', 'to' => 'to-pink-400', 'url' => '/admin-attempts'],
    ] as $card)
    <a href="{{ $card['url'] }}"
       class="group bg-white rounded-2xl p-5 shadow-sm border border-slate-100 hover:shadow-lg hover:-translate-y-0.5 hover:border-teal-200 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br {{ $card['from'] }} {{ $card['to'] }} grid place-items-center shadow-md">
                <i class="bi {{ $card['icon'] }} text-white text-xl"></i>
            </div>
            <i class="bi bi-arrow-up-right text-slate-300 text-lg group-hover:text-teal-500 group-hover:-translate-y-0.5 group-hover:translate-x-0.5 transition-all"></i>
        </div>
        <p class="mt-4 text-3xl font-extrabold text-slate-900">{{ $card['value'] }}</p>
        <p class="text-sm text-slate-500 font-medium group-hover:text-teal-600 transition-colors">{{ $card['label'] }}</p>
    </a>
    @endforeach
</div>

<!-- &hellip; Users List -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mt-8">
    <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Registered Users</h2>
            <p class="text-sm text-slate-500">Newest members of your platform</p>
        </div>
        <a href="/admin-users" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-teal-600 bg-teal-50 hover:bg-teal-100 transition">
            View all <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-4">#</th>
                    <th class="px-6 py-4">Member</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($users as $key => $user)
                    <tr class="hover:bg-teal-50/40 transition-colors">
                        <td class="px-6 py-4 font-semibold text-slate-400">{{ ($users->currentPage() - 1) * $users->perPage() + $key + 1 }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <span class="w-9 h-9 rounded-full bg-gradient-to-br from-teal-500 to-emerald-500 grid place-items-center text-white text-xs font-bold shrink-0">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                <span class="font-semibold text-slate-800">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ $user->email }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($user->active == 2)
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700"><i class="bi bi-check-circle-fill"></i> Verified</span>
                            @else
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full bg-amber-50 text-amber-700"><i class="bi bi-hourglass-split"></i> Pending</span>
                            @endif
                        </td>
                    </tr>
                @endforeach

                @if($users->isEmpty())
                    <tr><td colspan="4" class="text-center py-10 text-slate-400"><i class="bi bi-inbox text-3xl block mb-2"></i>No users found</td></tr>
                @endif
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between">
        <p class="text-sm text-slate-500">Page {{ $users->currentPage() }} of {{ $users->lastPage() }}</p>
        <div class="flex items-center gap-2">
            @if ($users->onFirstPage())
                <span class="px-4 py-2 text-sm text-slate-400 bg-slate-50 rounded-lg cursor-not-allowed">&laquo; Prev</span>
            @else
                <a href="{{ $users->previousPageUrl() }}" class="px-4 py-2 text-sm font-semibold text-teal-600 bg-teal-50 rounded-lg hover:bg-teal-100 transition">&laquo; Prev</a>
            @endif
            @if ($users->hasMorePages())
                <a href="{{ $users->nextPageUrl() }}" class="px-4 py-2 text-sm font-semibold text-teal-600 bg-teal-50 rounded-lg hover:bg-teal-100 transition">Next &raquo;</a>
            @else
                <span class="px-4 py-2 text-sm text-slate-400 bg-slate-50 rounded-lg cursor-not-allowed">Next &raquo;</span>
            @endif
        </div>
    </div>
</div>

</x-admin-layout>

