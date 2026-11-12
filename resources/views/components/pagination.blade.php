@if ($paginator->hasPages())
    <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between">
        <p class="text-sm text-slate-500">
            Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}
            <span class="text-slate-400">&middot; {{ $paginator->total() }} total</span>
        </p>
        <div class="flex items-center gap-2">
            @if ($paginator->onFirstPage())
                <span class="px-4 py-2 text-sm text-slate-400 bg-slate-50 rounded-lg cursor-not-allowed">&laquo; Prev</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-4 py-2 text-sm font-semibold text-teal-600 bg-teal-50 rounded-lg hover:bg-teal-100 transition">&laquo; Prev</a>
            @endif
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="px-4 py-2 text-sm font-semibold text-teal-600 bg-teal-50 rounded-lg hover:bg-teal-100 transition">Next &raquo;</a>
            @else
                <span class="px-4 py-2 text-sm text-slate-400 bg-slate-50 rounded-lg cursor-not-allowed">Next &raquo;</span>
            @endif
        </div>
    </div>
@endif
