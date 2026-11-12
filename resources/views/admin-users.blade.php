<x-admin-layout :name="$name" :stats="$stats ?? []" title="Users" heading="Registered Users" subheading="Every member of your platform">

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
        <div>
            <h2 class="text-lg font-bold text-slate-900">All Users</h2>
            <p class="text-sm text-slate-500">Newest members first</p>
        </div>
        <span class="text-xs font-bold px-3 py-1.5 rounded-full bg-teal-50 text-teal-600">{{ $users->total() }} total</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-4">#</th>
                    <th class="px-6 py-4">Member</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4 text-center">Attempts</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4">Joined</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($users as $key => $user)
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
                            <span class="text-xs font-bold px-3 py-1 rounded-full bg-sky-50 text-sky-600">{{ $user->records_count }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if ($user->active == 2)
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700"><i class="bi bi-check-circle-fill"></i> Verified</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full bg-amber-50 text-amber-700"><i class="bi bi-hourglass-split"></i> Pending</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-500">{{ optional($user->created_at)->format('d M Y') }}</td>
                    </tr>
                @endforeach

                @if ($users->isEmpty())
                    <tr><td colspan="6" class="text-center py-10 text-slate-400"><i class="bi bi-inbox text-3xl block mb-2"></i>No users found</td></tr>
                @endif
            </tbody>
        </table>
    </div>

    <x-pagination :paginator="$users" />
</div>

</x-admin-layout>
