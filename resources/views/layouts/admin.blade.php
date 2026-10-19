<!DOCTYPE html>
<html lang="vi" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Quản Trị Hệ Thống' }} - MinhTrading Forex Academy</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=be-vietnam-pro:300,400,500,600,700|jetbrains-mono:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts & Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js fallback if not bundled -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Be Vietnam Pro', sans-serif;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #0f172a;
        }
        ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }
    </style>

    @yield('styles')
</head>
<body class="bg-slate-900 text-slate-100 antialiased min-h-screen" 
      x-data="{ 
          sidebarOpen: false,
          sidebarCollapsed: localStorage.getItem('sidebar_collapsed') === 'true',
          toggleSidebar() {
              this.sidebarCollapsed = !this.sidebarCollapsed;
              localStorage.setItem('sidebar_collapsed', this.sidebarCollapsed);
          }
      }"
      @keydown.window.ctrl.b.prevent="toggleSidebar()">
    <div class="flex min-h-screen">
        <!-- Mobile Sidebar Backdrop -->
        <div 
            x-show="sidebarOpen" 
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-40 bg-slate-950/80 backdrop-blur-sm lg:hidden"
            @click="sidebarOpen = false"
            style="display: none;"
        ></div>

        <!-- Sidebar -->
        <aside 
            :class="[
                sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
                sidebarCollapsed ? 'w-64 lg:w-20' : 'w-64 lg:w-64'
            ]"
            class="fixed inset-y-0 left-0 z-50 bg-slate-900 border-r border-slate-800 flex flex-col transition-all duration-300 ease-in-out"
        >
            <!-- Logo Header -->
            <div class="h-16 flex items-center justify-between px-4 border-b border-slate-800 bg-slate-900/60 backdrop-blur">
                <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : url('/admin/dashboard') }}" 
                   class="flex items-center gap-3 group overflow-hidden"
                   :class="sidebarCollapsed ? 'lg:justify-center lg:w-full' : ''">
                    <div class="w-10 h-10 shrink-0 rounded-xl bg-gradient-to-tr from-emerald-500 to-sky-500 flex items-center justify-center text-slate-950 font-black shadow-lg shadow-emerald-500/20 group-hover:scale-105 transition-transform"
                         :title="sidebarCollapsed ? 'MinhTrading PRO' : ''">
                        <svg class="w-6 h-6 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div :class="sidebarCollapsed ? 'lg:hidden' : 'block'" class="transition-opacity duration-200">
                        <div class="text-base font-bold tracking-tight text-white flex items-center gap-1.5 whitespace-nowrap">
                            MinhTrading
                            <!-- <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">PRO</span> -->
                        </div>
                        <div class="text-[11px] font-medium text-slate-400 uppercase tracking-wider whitespace-nowrap">Admin Portal</div>
                    </div>
                </a>

                <!-- Desktop Collapse Button inside Sidebar Header (visible when expanded) -->
                <!-- <button @click="toggleSidebar()" 
                        type="button" 
                        :class="sidebarCollapsed ? 'lg:hidden' : 'hidden lg:flex'"
                        class="items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors"
                        title="Thu gọn menu (Ctrl+B)">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                    </svg>
                </button> -->

                <!-- Mobile Close Button -->
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
                <div :class="sidebarCollapsed ? 'lg:hidden' : 'block'" class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Tổng quan</div>
                <div :class="sidebarCollapsed ? 'hidden lg:block' : 'hidden'" class="my-2 border-t border-slate-800 mx-1"></div>
                
                <!-- Dashboard -->
                <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : url('/admin/dashboard') }}" 
                   :title="sidebarCollapsed ? 'Dashboard' : ''"
                   :class="sidebarCollapsed ? 'lg:justify-center lg:px-2' : ''"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 shadow-sm' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-emerald-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span :class="sidebarCollapsed ? 'lg:hidden' : 'block'" class="truncate">📊 Dashboard</span>
                </a>

                <div :class="sidebarCollapsed ? 'lg:hidden' : 'block'" class="pt-4 px-3 pb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Đào tạo & Quản lý</div>
                <div :class="sidebarCollapsed ? 'hidden lg:block' : 'hidden'" class="my-2 border-t border-slate-800 mx-1"></div>

                <!-- Students -->
                <a href="{{ Route::has('admin.students.index') ? route('admin.students.index') : url('/admin/students') }}" 
                   :title="sidebarCollapsed ? 'Quản lý học viên' : ''"
                   :class="sidebarCollapsed ? 'lg:justify-center lg:px-2' : ''"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.students.*') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.students.*') ? 'text-emerald-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span :class="sidebarCollapsed ? 'lg:hidden' : 'block'" class="truncate">👥 Quản lý học viên</span>
                </a>

                <!-- Courses -->
                <a href="{{ Route::has('admin.courses.index') ? route('admin.courses.index') : url('/admin/courses') }}" 
                   :title="sidebarCollapsed ? 'Khóa học' : ''"
                   :class="sidebarCollapsed ? 'lg:justify-center lg:px-2' : ''"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.courses.*') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.courses.*') ? 'text-emerald-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <span :class="sidebarCollapsed ? 'lg:hidden' : 'block'" class="truncate">📚 Khóa học</span>
                </a>

                <!-- Assignments & Submissions -->
                <a href="{{ Route::has('admin.submissions.index') ? route('admin.submissions.index') : url('/admin/submissions') }}" 
                   :title="sidebarCollapsed ? 'Bài tập & Bài nộp' : ''"
                   :class="sidebarCollapsed ? 'lg:justify-center lg:px-2' : ''"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.assignments.*') || request()->routeIs('admin.submissions.*') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.assignments.*') || request()->routeIs('admin.submissions.*') ? 'text-emerald-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    <span :class="sidebarCollapsed ? 'lg:hidden' : 'block'" class="truncate">📝 Bài tập & Bài nộp</span>
                </a>

                <!-- Questions Q&A -->
                <a href="{{ route('admin.questions.index') }}" 
                   :title="sidebarCollapsed ? 'Hỏi đáp học viên' : ''"
                   :class="sidebarCollapsed ? 'lg:justify-center lg:px-2' : ''"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.questions.*') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.questions.*') ? 'text-emerald-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                    </svg>
                    <span :class="sidebarCollapsed ? 'lg:hidden' : 'block'" class="truncate">💬 Hỏi đáp học viên</span>
                </a>

                <!-- Featured Showcase -->
                <a href="{{ route('admin.featured.index') }}" 
                   :title="sidebarCollapsed ? 'Tiêu biểu (Showcase)' : ''"
                   :class="sidebarCollapsed ? 'lg:justify-center lg:px-2' : ''"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.featured.*') ? 'bg-amber-500/10 text-amber-400 border border-amber-500/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.featured.*') ? 'text-amber-400' : 'text-amber-500/80' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                    <span :class="sidebarCollapsed ? 'lg:hidden' : 'block'" class="truncate">⭐ Tiêu biểu (Showcase)</span>
                </a>

                <div :class="sidebarCollapsed ? 'lg:hidden' : 'block'" class="pt-4 px-3 pb-2 text-[11px] font-semibold uppercase tracking-wider text-slate-500">Giao dịch & Phân tích</div>
                <div :class="sidebarCollapsed ? 'hidden lg:block' : 'hidden'" class="my-2 border-t border-slate-800 mx-1"></div>

                <!-- Model Trades -->
                <a href="{{ Route::has('admin.model-trades.index') ? route('admin.model-trades.index') : url('/admin/model-trades') }}" 
                   :title="sidebarCollapsed ? 'Mô hình mẫu' : ''"
                   :class="sidebarCollapsed ? 'lg:justify-center lg:px-2' : ''"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.model-trades.*') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.model-trades.*') ? 'text-emerald-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                    </svg>
                    <span :class="sidebarCollapsed ? 'lg:hidden' : 'block'" class="truncate">🏆 Mô hình mẫu</span>
                </a>

                <!-- Trade Stats -->
                <a href="{{ Route::has('admin.trade-stats.index') ? route('admin.trade-stats.index') : url('/admin/trade-stats') }}" 
                   :title="sidebarCollapsed ? 'Thống kê Trading' : ''"
                   :class="sidebarCollapsed ? 'lg:justify-center lg:px-2' : ''"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.trade-stats.*') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.trade-stats.*') ? 'text-emerald-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span :class="sidebarCollapsed ? 'lg:hidden' : 'block'" class="truncate">📊 Thống kê Trading</span>
                </a>

                <!-- Notifications -->
                <a href="{{ Route::has('admin.notifications.index') ? route('admin.notifications.index') : url('/admin/notifications') }}" 
                   :title="sidebarCollapsed ? 'Thông báo' : ''"
                   :class="sidebarCollapsed ? 'lg:justify-center lg:px-2' : ''"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.notifications.*') ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.notifications.*') ? 'text-emerald-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <span :class="sidebarCollapsed ? 'lg:hidden' : 'block'" class="truncate">🔔 Thông báo</span>
                </a>
            </nav>

            <!-- Bottom User Card -->
            <div class="p-3 border-t border-slate-800 bg-slate-900/80">
                <div class="flex items-center gap-3 px-3 py-2 rounded-lg bg-slate-800/50 border border-slate-700/50"
                     :class="sidebarCollapsed ? 'lg:justify-center lg:px-1.5' : ''"
                     :title="sidebarCollapsed ? ('{{ auth()->user()->name ?? 'Quản Trị Viên' }}' + ' ({{ auth()->user()->email ?? 'admin@minhtrading.com' }})') : ''">
                    <div class="w-8 h-8 shrink-0 rounded-full bg-emerald-500/20 text-emerald-400 font-bold flex items-center justify-center border border-emerald-500/30 text-xs">
                        {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
                    </div>
                    <div :class="sidebarCollapsed ? 'lg:hidden' : 'block'" class="flex-1 min-w-0 transition-opacity">
                        <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name ?? 'Quản Trị Viên' }}</p>
                        <p class="text-[10px] text-slate-400 truncate">{{ auth()->user()->email ?? 'admin@minhtrading.com' }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 transition-all duration-300 ease-in-out"
             :class="sidebarCollapsed ? 'lg:pl-20' : 'lg:pl-64'">
            <!-- Top Navbar -->
            <header class="h-16 sticky top-0 z-30 bg-slate-900/90 backdrop-blur border-b border-slate-800 px-4 sm:px-6 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <!-- Mobile Hamburger -->
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>

                    <!-- Desktop Sidebar Toggle Button (Mở ra / Thu vào) -->
                    <button @click="toggleSidebar()" 
                            type="button" 
                            class="hidden lg:inline-flex items-center justify-center p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors border border-slate-800 hover:border-slate-700" 
                            :title="sidebarCollapsed ? 'Mở rộng thanh menu (Ctrl+B)' : 'Thu gọn thanh menu (Ctrl+B)'">
                        <svg class="w-5 h-5 transition-transform duration-300" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                        </svg>
                    </button>

                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            Thị trường Forex: LIVE
                        </span>
                    </div>
                </div>

                <!-- Right Actions / Profile -->
                <div class="flex items-center gap-3">
                    <!-- Quick notification bell -->
                    <a href="{{ Route::has('admin.notifications.index') ? route('admin.notifications.index') : url('/admin/notifications') }}" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 relative">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                    </a>

                    <!-- User Dropdown with Alpine.js -->
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-slate-800 transition-colors">
                            <div class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-xs font-semibold text-emerald-400">
                                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                            </div>
                            <span class="text-sm font-medium text-slate-200 hidden sm:inline-block">{{ auth()->user()->name ?? 'Admin' }}</span>
                            <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">PRO</span>
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <div 
                            x-show="open" 
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-700"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-48 rounded-xl bg-slate-800 border border-slate-700 shadow-xl py-1 z-50 divide-y divide-slate-700/50"
                            style="display: none;"
                        >
                            <div class="px-4 py-2.5">
                                <p class="text-xs text-slate-400">Đang đăng nhập với</p>
                                <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->email ?? 'admin@minhtrading.com' }}</p>
                            </div>
                            <div class="py-1">
                                <a href="{{ Route::has('profile.edit') ? route('profile.edit') : url('/profile') }}" class="flex items-center gap-2 px-4 py-2 text-xs text-slate-300 hover:bg-slate-700 hover:text-white">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    Hồ sơ cá nhân
                                </a>
                            </div>
                            <div class="py-1">
                                <form method="POST" action="{{ Route::has('logout') ? route('logout') : url('/logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-xs text-rose-400 hover:bg-slate-700">
                                        <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                        Đăng xuất
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Flash Alerts -->
            <div class="px-4 sm:px-6 pt-4">
                @if (session('success'))
                    <div class="mb-4 flex items-center gap-3 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 flex items-center gap-3 p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if (session('warning'))
                    <div class="mb-4 flex items-center gap-3 p-4 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-400 text-sm">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <span>{{ session('warning') }}</span>
                    </div>
                @endif
            </div>

            <!-- Main Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="border-t border-slate-800 py-4 px-6 text-center text-xs text-slate-500">
                &copy; {{ date('Y') }} MinhTrading Academy. Hệ thống quản lý học viên Forex chuyên nghiệp.
            </footer>
        </div>
    </div>

    <!-- Scripts slot for page-specific JS (e.g. Chart.js) -->
    @yield('scripts')
</body>
</html>
