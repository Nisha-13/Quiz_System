<x-admin-layout :name="$name" :stats="$stats" title="Categories" heading="Categories" subheading="Organize your quizzes into topics">

<div class="grid gap-6 lg:grid-cols-3">

    <!-- &hellip; Add Category Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 h-fit lg:sticky lg:top-28">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-teal-600 to-emerald-600 grid place-items-center shadow-lg shadow-teal-500/25">
                <i class="bi bi-plus-lg text-white text-lg"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-900 leading-tight">New Category</h2>
                <p class="text-xs text-slate-500">Create a quiz topic</p>
            </div>
        </div>

        @if(session('category_name'))
        <div class="mb-4 flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-700 px-3 py-2.5 rounded-lg text-sm">
            <i class="bi bi-check-circle-fill"></i>
            <span class="font-medium">{{ session('category_name') }}</span>
        </div>
        @endif
        @if(session('category_delete'))
        <div class="mb-4 flex items-center gap-2 bg-rose-50 border border-rose-200 text-rose-700 px-3 py-2.5 rounded-lg text-sm">
            <i class="bi bi-trash-fill"></i>
            <span class="font-medium">{{ session('category_delete') }}</span>
        </div>
        @endif

        <form action="/add-category" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Category Name</label>
                <input type="text" name="category_name" placeholder="e.g. Web Development"
                       class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 outline-none transition">
                @error('category_name')
                <p class="text-rose-600 text-xs font-medium mt-1.5 flex items-center gap-1"><i class="bi bi-exclamation-circle-fill"></i>{{ $message }}</p>
                @enderror
            </div>
            <button type="submit"
                    class="w-full bg-gradient-to-r from-teal-600 to-emerald-600 text-white font-semibold py-2.5 rounded-xl shadow-lg shadow-teal-500/25 hover:shadow-teal-500/40 hover:-translate-y-px transition-all">
                <i class="bi bi-check-lg mr-1"></i> Add Category
            </button>
        </form>
    </div>

    <!-- &hellip; Category List -->
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
            <h2 class="text-lg font-bold text-slate-900">All Categories</h2>
            <span class="text-xs font-bold px-3 py-1.5 rounded-full bg-teal-50 text-teal-600">{{ $categories->count() }} total</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">#</th>
                        <th class="px-6 py-4">Name</th>
                        <th class="px-6 py-4">Creator</th>
                        <th class="px-6 py-4 text-center">Quizzes</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($categories as $key => $category)
                        <tr class="hover:bg-teal-50/40 transition-colors">
                            <td class="px-6 py-4 font-semibold text-slate-400">{{ $category->id }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-800">{{ $category->name }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $category->creator }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-xs font-bold px-3 py-1 rounded-full bg-sky-50 text-sky-600">{{ $category->quizzes_count }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="/quiz-list/{{ $category->id }}/{{ $category->name }}" title="View Quizzes"
                                   class="inline-flex w-9 h-9 rounded-lg bg-teal-50 text-teal-600 hover:bg-teal-600 hover:text-white transition items-center justify-center mr-1">
                                   <i class="bi bi-eye-fill"></i>
                                </a>
                                <a href="/category/delete/{{ $category->id }}" title="Delete"
                                    onclick="return confirm('Delete this category? All its quizzes and questions will be removed too.')"
                                   class="inline-flex w-9 h-9 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition items-center justify-center">
                                   <i class="bi bi-trash-fill"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach

                    @if($categories->isEmpty())
                        <tr><td colspan="5" class="text-center py-10 text-slate-400"><i class="bi bi-inbox text-3xl block mb-2"></i>No categories yet &mdash; create your first one!</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

</x-admin-layout>

