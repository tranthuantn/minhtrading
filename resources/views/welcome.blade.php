<!DOCTYPE html>
<html lang="vi" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MinhTrading - Hệ Thống Quản Lý Lớp Học & Đào Tạo Forex</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
</head>
<body class="bg-slate-950 text-slate-100 font-sans antialiased min-h-screen flex flex-col selection:bg-emerald-500 selection:text-slate-950">

    <!-- Top Navigation -->
    <header class="border-b border-slate-800/80 bg-slate-900/60 backdrop-blur sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <!-- <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-500 to-sky-500 flex items-center justify-center font-bold text-slate-950 shadow-lg shadow-emerald-500/20">
                    MT
                </div> -->
                <!-- // cho image dạng circle vào đây -->
                <div class="w-10 h-10 shrink-0 rounded-full bg-gradient-to-tr from-emerald-500 to-sky-500 flex items-center justify-center text-slate
-950 font-black shadow-lg shadow-emerald-500/20 group-hover:scale-105 transition-transform">
                    <img src="{{ asset('icon.jfif') }}" alt="MinhTrading" class="w-full h-full object-cover rounded-full">
                </div>
                
                    <div>
                    <span class="text-xl font-bold bg-gradient-to-r from-white via-slate-200 to-slate-400 bg-clip-text text-transparent">MinhTrading</span>
                    <span class="hidden sm:inline-block text-xs px-2 py-0.5 ml-2 bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 rounded-full font-medium">Forex Academy</span>
                </div>
            </div>

            <nav class="flex items-center space-x-3">
                @auth
                    <a href="{{ url('/dashboard') }}" class="px-4 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-semibold text-sm transition shadow-lg shadow-emerald-500/20">
                        Vào Dashboard &rarr;
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 text-sm font-medium transition">
                        Đăng nhập
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-4 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-semibold text-sm transition shadow-lg shadow-emerald-500/20">
                            Đăng ký học viên
                        </a>
                    @endif
                @endauth
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <main class="flex-1">
        <section class="relative overflow-hidden py-20 sm:py-28 border-b border-slate-800/80">
            <!-- Background Glow -->
            <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[800px] h-[500px] bg-gradient-to-tr from-emerald-500/15 via-sky-500/10 to-transparent blur-3xl pointer-events-none rounded-full"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative text-center">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-900 border border-slate-700/80 text-xs text-slate-300 mb-6">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Hệ thống đào tạo TMS</span>
                </div>

                <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tight max-w-4xl mx-auto leading-tight sm:leading-none">
                    Quản Lý Lớp Học & <span class="bg-gradient-to-r from-emerald-400 to-sky-400 bg-clip-text text-transparent">Nhật Ký Lệnh Forex</span> Toàn Diện
                </h1>

                <p class="mt-6 text-lg sm:text-xl text-slate-400 max-w-2xl mx-auto font-normal leading-relaxed">
                    Nền tảng dành riêng cho giảng viên và học viên: Quản lý bài tập, chữa bài chi tiết, thống kê tỷ lệ thắng (Win Rate), nhật ký backtest và thư viện mô hình lệnh chuẩn.
                </p>

                <div class="mt-10 flex flex-wrap justify-center gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-8 py-3.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-base transition shadow-xl shadow-emerald-500/25">
                            Truy cập Bảng điều khiển
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-8 py-3.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-base transition shadow-xl shadow-emerald-500/25">
                            Đăng nhập ngay
                        </a>
                        <a href="#quick-login" class="px-8 py-3.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-200 border border-slate-700 font-semibold text-base transition">
                            Tài khoản mẫu thử nghiệm
                        </a>
                    @endauth
                </div>

                <!-- Market Ticker Demo -->
                <div class="mt-14 max-w-4xl mx-auto grid grid-cols-2 sm:grid-cols-4 gap-3 text-left">
                    <div class="p-4 rounded-xl bg-slate-900/80 border border-slate-800">
                        <div class="text-xs text-slate-400">EUR/USD</div>
                        <div class="text-lg font-bold text-emerald-400 mt-1">1.08542 <span class="text-xs font-normal">+0.42%</span></div>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-900/80 border border-slate-800">
                        <div class="text-xs text-slate-400">XAU/USD (Gold)</div>
                        <div class="text-lg font-bold text-emerald-400 mt-1">2,385.60 <span class="text-xs font-normal">+1.18%</span></div>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-900/80 border border-slate-800">
                        <div class="text-xs text-slate-400">GBP/USD</div>
                        <div class="text-lg font-bold text-rose-400 mt-1">1.26410 <span class="text-xs font-normal">-0.15%</span></div>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-900/80 border border-slate-800">
                        <div class="text-xs text-slate-400">USD/JPY</div>
                        <div class="text-lg font-bold text-emerald-400 mt-1">154.230 <span class="text-xs font-normal">+0.22%</span></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Feature Columns: Admin & Student -->
        <section class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold tracking-tight">Hệ Thống Hai Phân Hệ Chuyên Biệt</h2>
                <p class="text-slate-400 mt-2">Được thiết kế tối ưu hóa cho cả Giảng viên quản lý và Học viên thực chiến</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Admin Card -->
                <div class="rounded-2xl bg-slate-900/90 border border-slate-800 p-8 relative overflow-hidden flex flex-col justify-between hover:border-emerald-500/40 transition">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/5 rounded-full blur-2xl pointer-events-none"></div>
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 text-xs font-semibold mb-4">
                            Dành Cho Giảng Viên / Admin
                        </div>
                        <h3 class="text-2xl font-bold mb-4">Quản Trị Lớp Học & Chấm Bài</h3>
                        <ul class="space-y-3 text-slate-300 text-sm">
                            <li class="flex items-start gap-3">
                                <span class="text-emerald-400 font-bold">&#10003;</span>
                                <span><strong>Soạn giáo án & khóa học:</strong> Tổ chức bài giảng lý thuyết, đính kèm video và tài liệu.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="text-emerald-400 font-bold">&#10003;</span>
                                <span><strong>Giao bài tập & Chữa bài:</strong> Chấm điểm (0-10), nhận xét chi tiết, chỉ ra lỗi sai trên chart.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="text-emerald-400 font-bold">&#10003;</span>
                                <span><strong>Mô hình lệnh mẫu:</strong> Chọn lọc lệnh backtest hoặc lệnh thật xuất sắc của học viên làm case study mẫu cho cả lớp.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="text-emerald-400 font-bold">&#10003;</span>
                                <span><strong>Thống kê chuyên sâu:</strong> Theo dõi Win Rate, P&L, Drawdown, thống kê theo từng cặp tiền cho từng học viên.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="text-emerald-400 font-bold">&#10003;</span>
                                <span><strong>Thông báo:</strong> Gửi thông báo nhắc deadline, lịch live stream tới toàn bộ hoặc học viên cụ thể.</span>
                            </li>
                        </ul>
                    </div>
                    <div class="mt-8 pt-6 border-t border-slate-800">
                        <span class="text-xs text-slate-400">Quyền truy cập cao nhất, quản lý toàn diện chất lượng đào tạo</span>
                    </div>
                </div>

                <!-- Student Card -->
                <div class="rounded-2xl bg-slate-900/90 border border-slate-800 p-8 relative overflow-hidden flex flex-col justify-between hover:border-sky-500/40 transition">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-sky-500/5 rounded-full blur-2xl pointer-events-none"></div>
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-500/10 text-sky-400 border border-sky-500/30 text-xs font-semibold mb-4">
                            Dành Cho Học Viên
                        </div>
                        <h3 class="text-2xl font-bold mb-4">Học Tập & Rèn Luyện Kỷ Luật</h3>
                        <ul class="space-y-3 text-slate-300 text-sm">
                            <li class="flex items-start gap-3">
                                <span class="text-sky-400 font-bold">&#10003;</span>
                                <span><strong>Học lý thuyết & Xem bài giảng:</strong> Theo dõi lộ trình bài học từ cơ bản đến SMC chuyên sâu.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="text-sky-400 font-bold">&#10003;</span>
                                <span><strong>Nộp bài tập:</strong> Nộp nhận định thị trường, đính kèm ảnh chart và thông số lệnh vào.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="text-sky-400 font-bold">&#10003;</span>
                                <span><strong>Nhật ký Backtest:</strong> Lưu trữ dữ liệu kiểm tra chiến lược, tự động tính win rate và R:R.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="text-sky-400 font-bold">&#10003;</span>
                                <span><strong>Nhật ký Vào Lệnh (Live / Demo):</strong> Quản lý kỷ luật tâm lý, ghi chép entry/SL/TP và kết quả.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <span class="text-sky-400 font-bold">&#10003;</span>
                                <span><strong>Học từ Mô hình mẫu:</strong> Xem và phân tích các lệnh chuẩn do giảng viên xuất bản.</span>
                            </li>
                        </ul>
                    </div>
                    <div class="mt-8 pt-6 border-t border-slate-800">
                        <span class="text-xs text-slate-400">Giúp hình thành tư duy giao dịch có hệ thống và nhất quán</span>
                    </div>
                </div>
            </div>
        </section>

       
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800/80 bg-slate-950 py-8 text-center text-xs text-slate-500">
        <p>&copy; {{ date('Y') }} MinhTrading Academy. Hệ thống quản lý đào tạo & giao dịch Forex chuyên nghiệp.</p>
    </footer>

</body>
</html>
