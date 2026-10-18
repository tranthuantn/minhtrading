<div class="flex items-center gap-2 border-b border-slate-800 pb-3 mb-6 overflow-x-auto">
    <a href="{{ Route::has('admin.submissions.index') ? route('admin.submissions.index') : url('/admin/submissions') }}" 
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all whitespace-nowrap {{ request()->routeIs('admin.submissions.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'bg-slate-800/80 hover:bg-slate-800 text-slate-300 hover:text-white border border-slate-700/60' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
        <span>📋 Bài nộp của học viên</span>
    </a>

    <a href="{{ Route::has('admin.assignments.index') ? route('admin.assignments.index') : url('/admin/assignments') }}" 
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all whitespace-nowrap {{ request()->routeIs('admin.assignments.index') || request()->routeIs('admin.assignments.create') || request()->routeIs('admin.assignments.edit') || request()->routeIs('admin.assignments.show') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'bg-slate-800/80 hover:bg-slate-800 text-slate-300 hover:text-white border border-slate-700/60' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        <span>📝 Danh sách đề bài tập</span>
    </a>

    <a href="{{ Route::has('admin.assignments.stats') ? route('admin.assignments.stats') : url('/admin/assignments/stats') }}" 
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all whitespace-nowrap {{ request()->routeIs('admin.assignments.stats') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'bg-slate-800/80 hover:bg-slate-800 text-slate-300 hover:text-white border border-slate-700/60' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
        <span>📈 Thống kê hoàn thành</span>
    </a>
</div>

