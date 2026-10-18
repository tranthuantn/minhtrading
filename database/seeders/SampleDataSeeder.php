<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonQuestion;
use App\Models\ModelTrade;
use App\Models\QuestionAnswer;
use App\Models\Submission;
use App\Models\SubmissionReview;
use App\Models\TradeEntry;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Get or create Admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@minhtrading.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '0901234567',
                'avatar' => 'avatars/admin.png',
                'bio' => 'Giảng viên trưởng & Nhà sáng lập MinhTrading.',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // 2. Create 5 sample students
        $studentsData = [
            [
                'name' => 'Nguyễn Văn An',
                'email' => 'an.nguyen@minhtrading.test',
                'phone' => '0912345671',
                'bio' => 'Học viên K15 - Tập trung giao dịch Price Action theo xu hướng chính các cặp Major.',
                'avatar' => 'avatars/student1.png',
            ],
            [
                'name' => 'Trần Thị Bình',
                'email' => 'binh.tran@minhtrading.test',
                'phone' => '0912345672',
                'bio' => 'Học viên K15 - Nghiên cứu sâu về Smart Money Concepts và quản lý vốn R:R tối ưu.',
                'avatar' => 'avatars/student2.png',
            ],
            [
                'name' => 'Lê Hoàng Cường',
                'email' => 'cuong.le@minhtrading.test',
                'phone' => '0912345673',
                'bio' => 'Học viên K16 - Chuyên giao dịch Vàng (XAU/USD) theo phiên London và New York.',
                'avatar' => 'avatars/student3.png',
            ],
            [
                'name' => 'Phạm Minh Đức',
                'email' => 'duc.pham@minhtrading.test',
                'phone' => '0912345674',
                'bio' => 'Học viên K16 - Đang thực hiện mục tiêu backtest 500 lệnh theo hệ thống SMC.',
                'avatar' => 'avatars/student4.png',
            ],
            [
                'name' => 'Hoàng Mai Linh',
                'email' => 'linh.hoang@minhtrading.test',
                'phone' => '0912345675',
                'bio' => 'Học viên K16 - Mục tiêu trở thành Full-time Trader với phong cách kiên nhẫn và kỷ luật.',
                'avatar' => 'avatars/student5.png',
            ],
        ];

        $students = [];
        foreach ($studentsData as $data) {
            $students[] = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'role' => 'student',
                    'phone' => $data['phone'],
                    'bio' => $data['bio'],
                    'avatar' => $data['avatar'],
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );
        }

        // 3. Create 2 Courses with 3-4 Lessons each
        // Course 1
        $course1 = Course::updateOrCreate(
            ['title' => 'Price Action & Cấu trúc Thị trường Forex Cơ bản'],
            [
                'description' => 'Khóa học cung cấp nền tảng vững chắc về phương pháp giao dịch theo Hành động giá (Price Action), nhận diện cấu trúc đỉnh đáy và quy tắc quản lý vốn chuyên nghiệp trong Forex.',
                'thumbnail' => 'courses/price-action-course.jpg',
                'is_published' => true,
                'sort_order' => 1,
            ]
        );

        $c1Lessons = [
            [
                'title' => 'Bài 1: Tổng quan thị trường Forex và cấu trúc sóng cơ bản',
                'content' => 'Nội dung bài học bao gồm cách vận hành của thị trường ngoại hối, các phiên giao dịch chính (Á, Âu, Mỹ), đặc điểm của các cặp tiền chính và cách xác định cấu trúc sóng cơ bản (Higher High, Higher Low, Lower High, Lower Low).',
                'video_url' => 'https://www.youtube.com/watch?v=sample_video_1',
                'attachments' => [
                    ['name' => 'Tong-quan-forex-k15.pdf', 'url' => 'materials/lesson1-overview.pdf'],
                ],
                'sort_order' => 1,
                'is_published' => true,
            ],
            [
                'title' => 'Bài 2: Xác định các vùng Kháng cự, Hỗ trợ và Cung Cầu (Supply/Demand)',
                'content' => 'Hướng dẫn chi tiết phương pháp vẽ các vùng Kháng cự/Hỗ trợ có độ tin cậy cao, phân biệt giữa vùng cản tĩnh và cản động, xác định các vùng Cung Cầu mạnh dựa trên phản ứng giá và sự mất cân bằng.',
                'video_url' => 'https://www.youtube.com/watch?v=sample_video_2',
                'attachments' => [
                    ['name' => 'Huong-dan-ve-vung-cung-cau.pdf', 'url' => 'materials/lesson2-support-resistance.pdf'],
                ],
                'sort_order' => 2,
                'is_published' => true,
            ],
            [
                'title' => 'Bài 3: Các mô hình nến Price Action xác nhận vào lệnh (Pinbar, Engulfing)',
                'content' => 'Phân tích tâm lý đằng sau các mô hình nến đảo chiều mạnh mẽ nhất: Pinbar, Bullish/Bearish Engulfing, Fakey. Cách kết hợp mô hình nến với vùng cản để nâng cao xác suất thắng.',
                'video_url' => 'https://www.youtube.com/watch?v=sample_video_3',
                'attachments' => [
                    ['name' => 'Mo-hinh-nen-price-action.pdf', 'url' => 'materials/lesson3-candlestick-patterns.pdf'],
                ],
                'sort_order' => 3,
                'is_published' => true,
            ],
            [
                'title' => 'Bài 4: Quản lý vốn, tính toán Lot size và tỷ lệ Risk:Reward (R:R)',
                'content' => 'Chiến lược quản trị rủi ro thực tế: quy tắc rủi ro tối đa 1-2% trên mỗi giao dịch, công thức tính Lot size chính xác theo số pip Stop-loss, và cách xây dựng hệ thống có kỳ vọng dương với R:R tối thiểu 1:2.',
                'video_url' => 'https://www.youtube.com/watch?v=sample_video_4',
                'attachments' => [
                    ['name' => 'Bang-tinh-lot-size-quan-ly-von.xlsx', 'url' => 'materials/lesson4-risk-management.xlsx'],
                ],
                'sort_order' => 4,
                'is_published' => true,
            ],
        ];

        $lessonsC1 = [];
        foreach ($c1Lessons as $lData) {
            $lessonsC1[] = Lesson::updateOrCreate(
                [
                    'course_id' => $course1->id,
                    'title' => $lData['title'],
                ],
                $lData
            );
        }

        // Course 2
        $course2 = Course::updateOrCreate(
            ['title' => 'Phương pháp Smart Money Concepts (SMC) & Dòng tiền Lớn'],
            [
                'description' => 'Khóa học nâng cao phân tích dấu chân dòng tiền tổ chức: Cấu trúc đa khung thời gian, Liquidity Sweep, Order Block (OB), Fair Value Gap (FVG) và chiến lược tối ưu R:R cao từ 1:3 trở lên.',
                'thumbnail' => 'courses/smc-advanced-course.jpg',
                'is_published' => true,
                'sort_order' => 2,
            ]
        );

        $c2Lessons = [
            [
                'title' => 'Bài 1: Cấu trúc thị trường đa khung thời gian: BOS, CHoCH và Swing High/Low',
                'content' => 'Nắm vững cách đọc cấu trúc thị trường SMC: Break of Structure (BOS), Change of Character (CHoCH), phân biệt Strong/Weak Highs và Lows, đồng bộ khung thời gian từ H4, H1 xuống M15.',
                'video_url' => 'https://www.youtube.com/watch?v=sample_video_5',
                'attachments' => [
                    ['name' => 'Tai-lieu-cau-truc-smc.pdf', 'url' => 'materials/smc-structure.pdf'],
                ],
                'sort_order' => 1,
                'is_published' => true,
            ],
            [
                'title' => 'Bài 2: Nhận diện Order Block (OB), Fair Value Gap (FVG) và Vùng Discount/Premium',
                'content' => 'Cách nhận diện Order Block hợp lệ chưa bị chạm (Unmitigated OB), đo lường khoảng trống thanh khoản FVG, sử dụng công cụ Fibonacci để phân chia vùng Discount (ưu tiên Mua) và Premium (ưu tiên Bán).',
                'video_url' => 'https://www.youtube.com/watch?v=sample_video_6',
                'attachments' => [
                    ['name' => 'Nhan-dien-order-block-fvg.pdf', 'url' => 'materials/smc-ob-fvg.pdf'],
                ],
                'sort_order' => 2,
                'is_published' => true,
            ],
            [
                'title' => 'Bài 3: Khai thác thanh khoản (Liquidity Inducement) và Bẫy săn Stop-loss',
                'content' => 'Bản chất của thanh khoản trong thị trường tài chính: Buy-side Liquidity (BSL), Sell-side Liquidity (SSL), Inducement (IDM). Cách tránh bị dính bẫy thanh khoản và kỹ thuật vào lệnh theo sau các đợt quét thanh khoản lớn.',
                'video_url' => 'https://www.youtube.com/watch?v=sample_video_7',
                'attachments' => [
                    ['name' => 'Chien-luoc-san-thanh-khoan.pdf', 'url' => 'materials/smc-liquidity.pdf'],
                ],
                'sort_order' => 3,
                'is_published' => true,
            ],
        ];

        $lessonsC2 = [];
        foreach ($c2Lessons as $lData) {
            $lessonsC2[] = Lesson::updateOrCreate(
                [
                    'course_id' => $course2->id,
                    'title' => $lData['title'],
                ],
                $lData
            );
        }

        // Cấp quyền học viên vào khóa học
        $course1->students()->syncWithoutDetaching([
            $students[0]->id => ['is_active' => true, 'enrolled_at' => now()],
            $students[1]->id => ['is_active' => true, 'enrolled_at' => now()],
            $students[3]->id => ['is_active' => true, 'enrolled_at' => now()],
            $students[4]->id => ['is_active' => true, 'enrolled_at' => now()],
        ]);

        $course2->students()->syncWithoutDetaching([
            $students[0]->id => ['is_active' => true, 'enrolled_at' => now()],
            $students[2]->id => ['is_active' => true, 'enrolled_at' => now()],
            $students[4]->id => ['is_active' => true, 'enrolled_at' => now()],
        ]);

        // 4. Create Assignments corresponding to every lesson
        // Course 1 - Lesson 1
        $assignment1_1 = Assignment::updateOrCreate(
            [
                'lesson_id' => $lessonsC1[0]->id, // Lesson 1 Course 1
                'title' => 'Bài tập 1: Phân tích cấu trúc sóng đỉnh/đáy trên cặp EUR/USD',
            ],
            [
                'description' => 'Học viên mở biểu đồ EUR/USD trên TradingView, xác định các đỉnh cao hơn (HH), đáy cao hơn (HL) hoặc đỉnh thấp hơn (LH), đáy thấp hơn (LL) trên khung thời gian H1/H4.',
                'requirements' => "1. Chụp màn hình biểu đồ rõ nét kèm chú thích.\n2. Đánh dấu tối thiểu 3 con sóng đẩy (Impulse) và sóng hồi (Retracement).\n3. Kết luận nhận định xu hướng chính hiện tại của thị trường.",
                'due_date' => now()->addDays(5),
                'type' => 'analysis',
            ]
        );

        // Course 1 - Lesson 2
        $assignment1_2 = Assignment::updateOrCreate(
            [
                'lesson_id' => $lessonsC1[1]->id, // Lesson 2 Course 1
                'title' => 'Bài tập 2: Phân tích vùng Supply/Demand và cấu trúc xu hướng EUR/USD trên khung H4',
            ],
            [
                'description' => 'Học viên mở biểu đồ EUR/USD trên TradingView, xác định cấu trúc xu hướng hiện tại và vẽ tối thiểu 2 vùng Supply và 2 vùng Demand quan trọng nhất.',
                'requirements' => "1. Chụp màn hình biểu đồ khung H4 rõ nét.\n2. Đánh dấu các đỉnh/đáy chính xác định xu hướng.\n3. Nêu rõ lý do lựa chọn từng vùng Cung/Cầu và dự báo kịch bản giá tiếp theo.",
                'due_date' => now()->addDays(7),
                'type' => 'analysis',
            ]
        );
        $assignment1 = $assignment1_2;

        // Course 1 - Lesson 3
        $assignment1_3 = Assignment::updateOrCreate(
            [
                'lesson_id' => $lessonsC1[2]->id, // Lesson 3 Course 1
                'title' => 'Bài tập 3: Thực hành Backtest 20 lệnh mô hình nến Engulfing trên cặp GBP/USD',
            ],
            [
                'description' => 'Sử dụng công cụ Replay của TradingView để kiểm tra hiệu quả của mô hình nến Engulfing tại các vùng cản khung H1 trên cặp GBP/USD.',
                'requirements' => "1. Ghi lại dữ liệu 20 lệnh vào bảng tính hoặc nhật ký giao dịch.\n2. Mỗi lệnh cần ghi rõ: Ngày vào, Giá Entry, SL, TP, Kết quả Win/Loss, Số R kiếm được.\n3. Tổng kết tỷ lệ Winrate và Win/Loss Ratio.",
                'due_date' => now()->addDays(14),
                'type' => 'backtest',
            ]
        );
        $assignment2 = $assignment1_3;

        // Course 1 - Lesson 4
        $assignment1_4 = Assignment::updateOrCreate(
            [
                'lesson_id' => $lessonsC1[3]->id, // Lesson 4 Course 1
                'title' => 'Bài tập 4: Lập bảng tính toán Lot size & Quản trị rủi ro 1% tài khoản',
            ],
            [
                'description' => 'Thực hành tính toán khối lượng lệnh (Lot size) cho tài khoản $10,000 với mức rủi ro cố định 1% (tương đương $100) trên các cặp tiền XAU/USD, EUR/USD và GBP/USD.',
                'requirements' => "1. Điền bảng tính toán số lot theo số pip Stop-loss cụ thể.\n2. Xác định điểm Chốt lời (TP) đảm bảo tỷ lệ R:R tối thiểu 1:2.\n3. Trình bày quy tắc kỷ luật cắt lỗ khi thị trường đi ngược phân tích.",
                'due_date' => now()->addDays(12),
                'type' => 'trade_entry',
            ]
        );

        // Course 2 - Lesson 1
        $assignment2_1 = Assignment::updateOrCreate(
            [
                'lesson_id' => $lessonsC2[0]->id, // Lesson 1 Course 2
                'title' => 'Bài tập 1: Nhận diện cấu trúc thị trường SMC, đánh dấu BOS và CHoCH',
            ],
            [
                'description' => 'Áp dụng phương pháp SMC để nhận diện sự phá vỡ cấu trúc (Break of Structure - BOS) và thay đổi thuộc tính xu hướng (Change of Character - CHoCH) trên khung H1.',
                'requirements' => "1. Chụp biểu đồ khung H1 trên cặp tiền yêu thích.\n2. Đánh dấu chính xác vị trí xảy ra BOS và CHoCH.\n3. Giải thích ý nghĩa của tín hiệu đối với hướng đi của dòng tiền lớn.",
                'due_date' => now()->addDays(6),
                'type' => 'analysis',
            ]
        );

        // Course 2 - Lesson 2
        $assignment2_2 = Assignment::updateOrCreate(
            [
                'lesson_id' => $lessonsC2[1]->id, // Lesson 2 Course 2
                'title' => 'Bài tập 2: Tìm Order Block và FVG hợp lệ để lên kế hoạch vào lệnh XAU/USD (Vàng)',
            ],
            [
                'description' => 'Lập kế hoạch giao dịch (Trade Plan) chi tiết cho cặp Vàng dựa trên vùng Order Block chưa bị khai thác (Unmitigated OB) tại vùng Discount/Premium.',
                'requirements' => "1. Xác định xu hướng khung lớn H4/H1.\n2. Tìm vùng Order Block và Fair Value Gap (FVG) hợp lệ trên khung M15.\n3. Tính toán thông số: Entry, SL, TP (tỷ lệ R:R tối thiểu 1:3), Lot size với rủi ro 1% tài khoản.",
                'due_date' => now()->addDays(10),
                'type' => 'trade_entry',
            ]
        );
        $assignment3 = $assignment2_2;

        // Course 2 - Lesson 3
        $assignment2_3 = Assignment::updateOrCreate(
            [
                'lesson_id' => $lessonsC2[2]->id, // Lesson 3 Course 2
                'title' => 'Bài tập 3: Ghi nhận 10 trường hợp bẫy săn thanh khoản (Liquidity Sweep) và vào lệnh',
            ],
            [
                'description' => 'Tìm kiếm và phân tích các trường hợp giá quét sạch thanh khoản của phe Mua (BSL) hoặc phe Bán (SSL) rồi đảo chiều mạnh mẽ.',
                'requirements' => "1. Tìm 10 ví dụ thực tế trên biểu đồ TradingView.\n2. Phân tích bẫy thanh khoản (Inducement / Stop hunt).\n3. Ghi lại điểm vào lệnh tối ưu theo sau đợt quét thanh khoản.",
                'due_date' => now()->addDays(15),
                'type' => 'backtest',
            ]
        );

        // 5. Create Submissions & Reviews
        // Student 1 submits Assignment 1 (Reviewed with feedback)
        $sub1 = Submission::updateOrCreate(
            [
                'assignment_id' => $assignment1->id,
                'student_id' => $students[0]->id,
            ],
            [
                'content' => 'Em xin nộp bài phân tích cấu trúc EUR/USD khung H4. Hiện tại giá đang hình thành chuỗi Higher High và Higher Low thể hiện xu hướng tăng rõ rệt. Em đã xác định được 2 vùng Demand quan trọng tại 1.08200 và 1.07850.',
                'attachments' => [
                    ['title' => 'EURUSD_H4_Analysis.png', 'url' => 'submissions/student1_eurusd_h4.png'],
                ],
                'trade_data' => [
                    'currency_pair' => 'EUR/USD',
                    'timeframe' => 'H4',
                    'trend' => 'Bullish',
                    'demand_zones' => ['1.08200 - 1.08350', '1.07850 - 1.08000'],
                    'supply_zones' => ['1.09400 - 1.09600'],
                ],
                'status' => 'approved',
                'is_featured' => true,
                'featured_note' => 'Bài làm rất chuẩn xác! Cấu trúc đỉnh đáy được xác định rất bài bản. Vùng Demand có nến Imbalance mạnh đạt chuẩn mô hình SMC.',
                'featured_at' => now()->subDays(2),
            ]
        );

        SubmissionReview::updateOrCreate(
            [
                'submission_id' => $sub1->id,
                'reviewer_id' => $admin->id,
            ],
            [
                'feedback' => 'Bài làm rất chuẩn xác! Cấu trúc đỉnh đáy được xác định rất bài bản. Vùng Demand 1.08200 có nến mất cân bằng (Imbalance) mạnh nên xác suất phản ứng là rất cao. Phát huy tốt nhé!',
                'score' => 9.50,
                'corrections' => [
                    'Lưu ý thêm tin tức Non-Farm Payrolls vào thứ Sáu có thể gây biến động mạnh tại vùng Demand thứ hai.',
                ],
            ]
        );

        // Student 2 submits Assignment 1 (Reviewed)
        $sub2 = Submission::updateOrCreate(
            [
                'assignment_id' => $assignment1->id,
                'student_id' => $students[1]->id,
            ],
            [
                'content' => 'Bài tập phân tích EUR/USD của em. Em nhận thấy giá đang tiếp cận vùng Supply mạnh 1.09500 và có tín hiệu chậm lại của lực mua.',
                'attachments' => [
                    ['title' => 'BinhTran_EURUSD_Chart.png', 'url' => 'submissions/student2_eurusd_chart.png'],
                ],
                'trade_data' => [
                    'currency_pair' => 'EUR/USD',
                    'timeframe' => 'H4',
                    'primary_bias' => 'Bullish retracement to Supply',
                ],
                'status' => 'reviewed',
            ]
        );

        SubmissionReview::updateOrCreate(
            [
                'submission_id' => $sub2->id,
                'reviewer_id' => $admin->id,
            ],
            [
                'feedback' => 'Phân tích tốt, nhìn nhận vùng cản đúng thời điểm. Cần quan sát thêm khung M15 để tìm tín hiệu xác nhận đảo chiều trước khi có ý định Bán.',
                'score' => 8.50,
                'corrections' => [
                    'Không nên vội vã Sell sớm khi chưa có nến xác nhận CHoCH hoặc Pinbar từ chối vùng 1.09500.',
                ],
            ]
        );

        // Student 3 submits Assignment 2 (Pending)
        Submission::updateOrCreate(
            [
                'assignment_id' => $assignment2->id,
                'student_id' => $students[2]->id,
            ],
            [
                'content' => 'Em gửi thầy bảng thống kê 20 lệnh backtest mô hình Engulfing trên GBP/USD khung H1 từ tháng 1 đến tháng 3/2024. Tổng kết: 13 Win - 7 Loss, tỷ lệ R:R trung bình 1:2.2.',
                'attachments' => [
                    ['title' => 'GBPUSD_Engulfing_Backtest.xlsx', 'url' => 'submissions/student3_backtest.xlsx'],
                ],
                'trade_data' => [
                    'total_trades' => 20,
                    'wins' => 13,
                    'losses' => 7,
                    'winrate' => '65%',
                    'net_r' => 21.6,
                ],
                'status' => 'pending',
            ]
        );

        // Student 4 submits Assignment 3 (Needs Revision)
        $sub4 = Submission::updateOrCreate(
            [
                'assignment_id' => $assignment3->id,
                'student_id' => $students[3]->id,
            ],
            [
                'content' => 'Em đã chọn được Order Block M15 trên Vàng tại mức giá 2335.00 và dự kiến đặt lệnh Limit Buy.',
                'attachments' => [
                    ['title' => 'XAUUSD_M15_Plan.png', 'url' => 'submissions/student4_xauusd_plan.png'],
                ],
                'trade_data' => [
                    'currency_pair' => 'XAU/USD',
                    'entry' => 2335.00,
                    'sl' => 2328.00,
                    'tp' => 2360.00,
                ],
                'status' => 'revision_needed',
            ]
        );

        SubmissionReview::updateOrCreate(
            [
                'submission_id' => $sub4->id,
                'reviewer_id' => $admin->id,
            ],
            [
                'feedback' => 'Vùng OB 2335.00 em chọn đã bị nến trước đó hồi về test (Mitigated) rồi, không còn là Fresh Order Block. Vùng an toàn hơn là chân sóng tại 2325.00.',
                'score' => 6.00,
                'corrections' => [
                    'Chọn lại vùng Unmitigated Order Block ở vùng Discount sâu hơn.',
                    'Kiểm tra xem vùng OB có đi kèm FVG chưa bị lấp hay không.',
                ],
            ]
        );

        // Student 1 submits Assignment 1_1 (Approved)
        $sub1_1 = Submission::updateOrCreate(
            [
                'assignment_id' => $assignment1_1->id,
                'student_id' => $students[0]->id,
            ],
            [
                'content' => 'Em nộp bài tập 1: Phân tích các con sóng đẩy và sóng hồi trên EUR/USD khung H1. Xu hướng tăng duy trì rất rõ với các đáy sau cao hơn đáy trước.',
                'attachments' => [
                    ['title' => 'EURUSD_H1_Waves.png', 'url' => 'submissions/student1_waves.png'],
                ],
                'status' => 'approved',
                'is_featured' => true,
                'featured_note' => 'Xác định cấu trúc sóng rất chuẩn xác! Các mốc đỉnh đáy được vẽ rõ ràng, logic tốt, rất đáng để học hỏi.',
                'featured_at' => now()->subDay(),
            ]
        );

        SubmissionReview::updateOrCreate(
            [
                'submission_id' => $sub1_1->id,
                'reviewer_id' => $admin->id,
            ],
            [
                'feedback' => 'Xác định cấu trúc sóng rất chuẩn xác! Các mốc đỉnh đáy được vẽ rõ ràng, logic tốt.',
                'score' => 9.00,
            ]
        );

        // Student 1 submits Assignment 2_1 (Pending)
        Submission::updateOrCreate(
            [
                'assignment_id' => $assignment2_1->id,
                'student_id' => $students[0]->id,
            ],
            [
                'content' => 'Thưa thầy em đã xác định được điểm CHoCH trên khung H1 của GBP/JPY và đánh dấu các vùng phá vỡ cấu trúc BOS.',
                'attachments' => [
                    ['title' => 'GBPJPY_CHoCH_Analysis.png', 'url' => 'submissions/student1_choch.png'],
                ],
                'status' => 'pending',
            ]
        );

        // 6. Create Trade Entries
        // Trade 1: Student 1 Live Win EUR/USD
        $trade1 = TradeEntry::updateOrCreate(
            [
                'user_id' => $students[0]->id,
                'currency_pair' => 'EUR/USD',
                'entry_time' => now()->subDays(5)->setTime(14, 30),
            ],
            [
                'type' => 'live',
                'direction' => 'buy',
                'entry_price' => 1.08500,
                'stop_loss' => 1.08200,
                'take_profit' => 1.09400,
                'lot_size' => 0.50,
                'analysis_notes' => 'Vào lệnh Buy tại vùng Demand H1 sau khi giá xuất hiện nến Bullish Pinbar xác nhận từ chối giảm. Quản lý rủi ro 1% tài khoản.',
                'chart_screenshots' => [
                    'screenshots/trade1_entry.png',
                    'screenshots/trade1_exit.png',
                ],
                'result' => 'win',
                'pnl_pips' => 90.00,
                'pnl_amount' => 450.00,
                'exit_time' => now()->subDays(4)->setTime(19, 00),
                'timeframe' => 'H1',
                'setup_criteria' => [
                    'Demand Zone H1',
                    'Bullish Pinbar Confirmation',
                    'Risk:Reward 1:3',
                ],
            ]
        );

        // Trade 2: Student 2 Demo Win GBP/USD
        $trade2 = TradeEntry::updateOrCreate(
            [
                'user_id' => $students[1]->id,
                'currency_pair' => 'GBP/USD',
                'entry_time' => now()->subDays(3)->setTime(9, 15),
            ],
            [
                'type' => 'demo',
                'direction' => 'sell',
                'entry_price' => 1.29800,
                'stop_loss' => 1.30200,
                'take_profit' => 1.28600,
                'lot_size' => 0.30,
                'analysis_notes' => 'Bán theo cấu trúc giảm H4 khi giá hồi phục về Bearish Order Block phiên London.',
                'chart_screenshots' => [
                    'screenshots/trade2_gbpusd.png',
                ],
                'result' => 'win',
                'pnl_pips' => 120.00,
                'pnl_amount' => 360.00,
                'exit_time' => now()->subDays(2)->setTime(16, 45),
                'timeframe' => 'H4',
                'setup_criteria' => [
                    'H4 Downtrend Structure',
                    'Bearish OB Mitigation',
                    'Inducement Cleared',
                ],
            ]
        );

        // Trade 3: Student 3 Backtest Win XAU/USD
        $trade3 = TradeEntry::updateOrCreate(
            [
                'user_id' => $students[2]->id,
                'currency_pair' => 'XAU/USD',
                'entry_time' => now()->subDays(10)->setTime(15, 00),
            ],
            [
                'type' => 'backtest',
                'direction' => 'buy',
                'entry_price' => 2320.50000,
                'stop_loss' => 2312.00000,
                'take_profit' => 2345.00000,
                'lot_size' => 1.00,
                'analysis_notes' => 'Backtest lệnh quét đáy phiên Á (Liquidity Sweep) và tạo CHoCH khung M5 lúc mở phiên London.',
                'chart_screenshots' => [
                    'screenshots/trade3_backtest_gold.png',
                ],
                'result' => 'win',
                'pnl_pips' => 245.00,
                'pnl_amount' => 2450.00,
                'exit_time' => now()->subDays(10)->setTime(18, 30),
                'timeframe' => 'M15',
                'setup_criteria' => [
                    'Asian Low Sweep',
                    'M5 CHoCH with FVG',
                    'Target Previous Day High',
                ],
            ]
        );

        // Trade 4: Student 4 Live Loss USD/JPY
        $trade4 = TradeEntry::updateOrCreate(
            [
                'user_id' => $students[3]->id,
                'currency_pair' => 'USD/JPY',
                'entry_time' => now()->subDays(2)->setTime(13, 00),
            ],
            [
                'type' => 'live',
                'direction' => 'buy',
                'entry_price' => 155.20000,
                'stop_loss' => 154.80000,
                'take_profit' => 156.40000,
                'lot_size' => 0.20,
                'analysis_notes' => 'Lệnh Buy theo đà tăng H1 nhưng dính đợt phát biểu bất ngờ của đại diện Ngân hàng Trung ương Nhật Bản (BOJ), giá lao dốc quét Stop-loss.',
                'chart_screenshots' => [
                    'screenshots/trade4_usdjpy_loss.png',
                ],
                'result' => 'loss',
                'pnl_pips' => -40.00,
                'pnl_amount' => -80.00,
                'exit_time' => now()->subDays(2)->setTime(13, 45),
                'timeframe' => 'H1',
                'setup_criteria' => [
                    'H1 Trend Continuation',
                    'Stop-loss 40 pips',
                ],
            ]
        );

        // Trade 5: Student 5 Demo Pending EUR/USD
        $trade5 = TradeEntry::updateOrCreate(
            [
                'user_id' => $students[4]->id,
                'currency_pair' => 'EUR/USD',
                'entry_time' => now()->subHours(6),
            ],
            [
                'type' => 'demo',
                'direction' => 'sell',
                'entry_price' => 1.09100,
                'stop_loss' => 1.09350,
                'take_profit' => 1.08350,
                'lot_size' => 0.40,
                'analysis_notes' => 'Lệnh đang chạy theo tín hiệu FVG M15 sau khi giá từ chối vùng kháng cự H4.',
                'chart_screenshots' => [
                    'screenshots/trade5_running.png',
                ],
                'result' => 'pending',
                'pnl_pips' => null,
                'pnl_amount' => null,
                'exit_time' => null,
                'timeframe' => 'M15',
                'setup_criteria' => [
                    'M15 FVG Fill',
                    'Target H1 Swing Low',
                ],
            ]
        );

        // Trade 6: Admin Live Win XAU/USD (Base for Model Trade)
        $adminTrade = TradeEntry::updateOrCreate(
            [
                'user_id' => $admin->id,
                'currency_pair' => 'XAU/USD',
                'entry_time' => now()->subDays(1)->setTime(14, 00),
            ],
            [
                'type' => 'live',
                'direction' => 'buy',
                'entry_price' => 2350.00000,
                'stop_loss' => 2342.00000,
                'take_profit' => 2375.00000,
                'lot_size' => 2.00,
                'analysis_notes' => 'Lệnh mẫu chuẩn phương pháp SMC: Giá vàng quét sạch Sell-side Liquidity tại 2345 phiên Á, tạo cấu trúc CHoCH M15 kèm khối lượng lớn. Vào lệnh tại 50% FVG Discount.',
                'chart_screenshots' => [
                    'screenshots/admin_xauusd_entry.png',
                    'screenshots/admin_xauusd_tp.png',
                ],
                'result' => 'win',
                'pnl_pips' => 250.00,
                'pnl_amount' => 5000.00,
                'exit_time' => now()->subDays(1)->setTime(21, 15),
                'timeframe' => 'H4',
                'setup_criteria' => [
                    'Sell-side Liquidity Sweep',
                    'M15 CHoCH Confirmation',
                    '50% Discount FVG Entry',
                    'Target Daily High',
                ],
            ]
        );

        // 7. Create Model Trades (By Admin)
        ModelTrade::updateOrCreate(
            [
                'admin_id' => $admin->id,
                'title' => 'Lệnh Mẫu SMC: Mua Vàng (XAU/USD) sau khi Quét Thanh Khoản Sell-side',
            ],
            [
                'trade_entry_id' => $adminTrade->id,
                'description' => 'Phân tích chi tiết thiết lập giao dịch mua Vàng đạt tỷ lệ R:R 1:3.1 theo phương pháp Smart Money Concepts.',
                'currency_pair' => 'XAU/USD',
                'pair' => 'XAU/USD',
                'direction' => 'buy',
                'entry_price' => 2350.00000,
                'stop_loss' => 2342.00000,
                'take_profit' => 2375.00000,
                'rr_ratio' => 3.1,
                'setup_name' => 'Quét thanh khoản đáy Á (Liquidity Sweep) & M15 CHoCH',
                'chart_image' => 'model_trades/gold_setup_overview.svg',
                'analysis' => "1. Bối cảnh thị trường:\nKhung D1 và H4 của Vàng đang duy trì xu hướng tăng chủ đạo. Trong phiên Á, giá thoái lui và tích luỹ hẹp phía trên mức đáy ngày hôm trước.\n\n2. Bẫy thanh khoản & Tín hiệu xác nhận:\nNgay đầu phiên London (14:00 giờ VN), một cây nến xung lượng mạnh lao xuống mức 2345 để kích hoạt toàn bộ lệnh cắt lỗ của phe Mua (Sell-side Liquidity Sweep). Sau đó giá lập tức rút chân mạnh mẽ và đóng nến tạo CHoCH trên khung M15.\n\n3. Điểm vào lệnh (Entry):\nChờ đợi giá hồi quy về mức 50% của khối Fair Value Gap (FVG) vừa được hình thành tại 2350.00. Điểm dừng lỗ đặt an toàn phía dưới râu nến quét thanh khoản tại 2342.00 (-80 pips). Mục tiêu chốt lời tại đỉnh cũ phiên New York ngày hôm trước 2375.00 (+250 pips).\n\n4. Bài học rút ra:\nKhông bao giờ đặt lệnh đuổi theo nến phá đáy ban đầu; hãy kiên nhẫn chờ đợi xác nhận rút chân và hồi về vùng giá chiết khấu (Discount).",
                'chart_screenshots' => [
                    'model_trades/gold_setup_overview.svg',
                ],
                'timeframe' => 'H4',
                'setup_criteria' => [
                    'Quét sạch Sell-side Liquidity phiên Á',
                    'M15 CHoCH kèm xung lượng lớn',
                    'Entry tại vùng Discount FVG (50%)',
                    'Tỷ lệ Risk:Reward tối thiểu 1:3',
                ],
                'is_published' => true,
            ]
        );

        ModelTrade::updateOrCreate(
            [
                'admin_id' => $admin->id,
                'title' => 'Lệnh Mẫu Price Action: Bán EUR/USD tại Vùng Bearish Order Block',
            ],
            [
                'trade_entry_id' => null,
                'description' => 'Mô hình thiết lập lệnh Bán kinh điển theo dòng tiền giảm khung H1 kết hợp mô hình Bearish Engulfing.',
                'currency_pair' => 'EUR/USD',
                'pair' => 'EUR/USD',
                'direction' => 'sell',
                'entry_price' => 1.09200,
                'stop_loss' => 1.09500,
                'take_profit' => 1.08300,
                'rr_ratio' => 3.0,
                'setup_name' => 'Bearish Order Block H1 & Phá vỡ cấu trúc giảm',
                'chart_image' => 'model_trades/eurusd_bearish_ob.svg',
                'analysis' => "Phân tích bối cảnh: Cặp EUR/USD chạm vùng kháng cự mạnh khung Daily. Trên H1, giá tạo cấu trúc đỉnh thấp hơn (Lower High) và tạo ra một Bearish Order Block sắc nét. Lệnh Sell Limit kích hoạt chính xác tại 1.09200, SL 30 pips, TP 90 pips (R:R 1:3).",
                'chart_screenshots' => [
                    'model_trades/eurusd_bearish_ob.svg',
                ],
                'timeframe' => 'H1',
                'setup_criteria' => [
                    'Bearish Trend Structure H1',
                    'Unmitigated Bearish Order Block',
                    'Bearish Engulfing Confirmation',
                ],
                'is_published' => true,
            ]
        );

        ModelTrade::updateOrCreate(
            [
                'admin_id' => $admin->id,
                'title' => 'Lệnh Mẫu Backtest: Mua GBP/USD tại Vùng Demand sau Breaker Block',
            ],
            [
                'trade_entry_id' => $trade2->id ?? null,
                'description' => 'Setup mua đảo chiều GBP/USD tại chân sóng M15 sau khi giá hình thành cấu trúc Breaker Block.',
                'currency_pair' => 'GBP/USD',
                'pair' => 'GBP/USD',
                'direction' => 'buy',
                'entry_price' => 1.28500,
                'stop_loss' => 1.28200,
                'take_profit' => 1.29700,
                'rr_ratio' => 4.0,
                'setup_name' => 'Breaker Block M15 hợp lưu Demand Zone',
                'chart_image' => 'model_trades/gold_setup_overview.svg',
                'analysis' => "Giá GBP/USD phá vỡ đỉnh cũ xác nhận cấu trúc tăng tiếp diễn, quay về kiểm tra lại đỉnh vừa phá (Breaker Block) trùng khớp với đường xu hướng H1. Entry BUY tại 1.28500 với mức RR lý tưởng 1:4.",
                'chart_screenshots' => [
                    'model_trades/gold_setup_overview.svg',
                ],
                'timeframe' => 'M15',
                'setup_criteria' => [
                    'H1 Bullish Continuation',
                    'Breaker Block retest',
                    'Risk Reward 1:4',
                ],
                'is_published' => true,
            ]
        );

        ModelTrade::updateOrCreate(
            [
                'admin_id' => $admin->id,
                'title' => 'Lệnh Mẫu: Bán USD/JPY tại Kháng cự D1 & Phân kỳ RSI',
            ],
            [
                'trade_entry_id' => null,
                'description' => 'Mô hình đánh chặn đỉnh khi thị trường quá mua kết hợp tín hiệu nến đảo chiều Evening Star.',
                'currency_pair' => 'USD/JPY',
                'pair' => 'USD/JPY',
                'direction' => 'sell',
                'entry_price' => 156.40000,
                'stop_loss' => 156.90000,
                'take_profit' => 154.40000,
                'rr_ratio' => 4.0,
                'setup_name' => 'Phân kỳ đỉnh RSI D1 + Evening Star',
                'chart_image' => 'model_trades/eurusd_bearish_ob.svg',
                'analysis' => "USD/JPY tiếp cận mốc tâm lý 156.50 nhưng đà tăng suy yếu rõ rệt. Khung H4 xuất hiện phân kỳ âm RSI kết hợp tổ hợp nến Evening Star. Thực hiện lệnh SELL tại 156.40, TP hướng về vùng hỗ trợ 154.40.",
                'chart_screenshots' => [
                    'model_trades/eurusd_bearish_ob.svg',
                ],
                'timeframe' => 'D1',
                'setup_criteria' => [
                    'Daily Major Resistance',
                    'RSI Bearish Divergence',
                    'Evening Star Pattern',
                ],
                'is_published' => true,
            ]
        );

        // 8. Create Sample User Notifications
        $notificationsData = [
            [
                'user_id' => $students[0]->id,
                'title' => 'Bài tập của bạn đã được duyệt!',
                'message' => 'Giảng viên đã chấm bài tập "Phân tích vùng Supply/Demand EUR/USD" của bạn với điểm số 9.5/10.',
                'type' => 'review',
                'is_read' => true,
                'data' => [
                    'assignment_id' => $assignment1->id,
                    'score' => 9.50,
                ],
            ],
            [
                'user_id' => $students[1]->id,
                'title' => 'Nhận xét mới cho bài tập phân tích EUR/USD',
                'message' => 'Giảng viên đã để lại phản hồi chi tiết về cách xác định vùng cản cho bài nộp của bạn.',
                'type' => 'review',
                'is_read' => false,
                'data' => [
                    'assignment_id' => $assignment1->id,
                    'score' => 8.50,
                ],
            ],
            [
                'user_id' => $students[2]->id,
                'title' => 'Nhắc nhở: Sắp đến hạn nộp bài tập Backtest',
                'message' => 'Bài tập "Thực hành Backtest 20 lệnh mô hình Engulfing trên GBP/USD" còn 7 ngày nữa là đến hạn.',
                'type' => 'assignment',
                'is_read' => false,
                'data' => [
                    'assignment_id' => $assignment2->id,
                    'due_date' => now()->addDays(7)->toIso8601String(),
                ],
            ],
            [
                'user_id' => $students[3]->id,
                'title' => 'Yêu cầu sửa đổi bài tập Vàng (XAU/USD)',
                'message' => 'Vùng Order Block bạn chọn đã bị Mitigated. Vui lòng xem nhận xét của Giảng viên và nộp lại bài.',
                'type' => 'review',
                'is_read' => false,
                'data' => [
                    'assignment_id' => $assignment3->id,
                    'status' => 'revision_needed',
                ],
            ],
            [
                'user_id' => $students[0]->id,
                'title' => 'Lệnh Mẫu Mới: Phân tích Kỹ thuật Mua Vàng XAU/USD',
                'message' => 'Giảng viên Minh đã xuất bản một Lệnh Mẫu mới kèm phân tích chi tiết thiết lập SMC trên Vàng.',
                'type' => 'announcement',
                'is_read' => false,
                'data' => [
                    'currency_pair' => 'XAU/USD',
                    'action' => 'buy',
                ],
            ],
            [
                'user_id' => $admin->id,
                'title' => 'Có bài tập mới cần chấm điểm',
                'message' => 'Học viên Lê Hoàng Cường vừa gửi bài nộp cho bài tập "Backtest 20 lệnh Engulfing GBP/USD".',
                'type' => 'assignment',
                'is_read' => false,
                'data' => [
                    'student_name' => 'Lê Hoàng Cường',
                    'assignment_id' => $assignment2->id,
                ],
            ],
            [
                'user_id' => $students[4]->id,
                'title' => 'Chào mừng bạn đến với MinhTrading!',
                'message' => 'Chào mừng bạn gia nhập cộng đồng giao dịch ngoại hối MinhTrading. Hãy bắt đầu từ khóa học Price Action cơ bản nhé!',
                'type' => 'info',
                'is_read' => true,
                'data' => [
                    'welcome' => true,
                ],
            ],
        ];

        foreach ($notificationsData as $nData) {
            UserNotification::create($nData);
        }

        // 8. Create Sample Lesson Questions & Answers
        if (! empty($lessonsC1)) {
            // Question 1 (Featured)
            $q1 = LessonQuestion::updateOrCreate(
                [
                    'user_id' => $students[0]->id,
                    'lesson_id' => $lessonsC1[0]->id,
                    'title' => 'Làm sao để tránh bẫy Fakeout khi giá quét đỉnh phiên Á?',
                ],
                [
                    'content' => "Thưa thầy, khi giao dịch phiên London, em hay gặp trường hợp giá vừa vượt qua đỉnh phiên Á (Asian High) được vài pip thì rút râu đảo chiều rất mạnh khiến lệnh Buy của em dính Stop Loss ngay lập tức. Có dấu hiệu cụ thể nào để nhận biết đâu là Breakout thật và đâu là nhịp quét thanh khoản (Liquidity Sweep) không ạ?",
                    'images' => [
                        'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?auto=format&fit=crop&w=800&q=80',
                    ],
                    'is_featured' => true,
                    'featured_note' => 'Câu hỏi cực kỳ hay và mang tính thực chiến cao về nhận diện bẫy thanh khoản phiên Á (Asian Range Liquidity Trap) theo phương pháp SMC.',
                    'featured_at' => now()->subDays(3),
                ]
            );

            // Instructor Answer for Q1
            QuestionAnswer::updateOrCreate(
                [
                    'question_id' => $q1->id,
                    'user_id' => $admin->id,
                ],
                [
                    'content' => "Chào An, đây là một trong những câu hỏi hay nhất mà bất cứ Trader giao dịch theo phiên (Session Trading) nào cũng phải trải qua.\n\nĐể phân biệt Breakout thật và Liquidity Sweep, em hãy ghi nhớ 3 nguyên tắc sau:\n1. Nến đóng cửa (Candle Close): Đột phá thật đòi hỏi thân nến M15/H1 phải đóng cửa dứt khoát trên đỉnh Á. Nếu chỉ có râu nến (Wick) thò lên rồi đóng nến rút chân thành Pinbar hoặc Doji, 90% đó là bẫy thanh khoản.\n2. Sự xuất hiện của FVG: Breakout thật luôn đi kèm nến động lượng lớn tạo ra khoảng trống giá Fair Value Gap. Nếu không có FVG, lực mua đó là giả tạo.\n3. Thời điểm (Timing): Quét đỉnh đáy Á thường diễn ra vào 30 phút đầu phiên London (14h00 - 14h30 VN). Em không nên Buy breakout ngay mà hãy kiên nhẫn chờ nến đảo chiều CHoCH khung M5 để vào lệnh Sell theo dòng tiền lớn (Smart Money).",
                    'images' => [
                        'https://images.unsplash.com/photo-1642543492481-44e81e3914a7?auto=format&fit=crop&w=800&q=80',
                    ],
                ]
            );

            // Question 2 (Featured) on Lesson 2
            if (isset($lessonsC1[1])) {
                $q2 = LessonQuestion::updateOrCreate(
                    [
                        'user_id' => $students[1]->id,
                        'lesson_id' => $lessonsC1[1]->id,
                        'title' => 'Mức Consequent Encroachment (50%) của FVG và cách đặt lệnh Limit',
                    ],
                    [
                        'content' => "Em chào thầy Minh! Khi giá hồi về một vùng FVG lớn trên khung H4, em nên đặt Limit tại mép đầu của FVG hay đặt tại mức cân bằng 50% (Consequent Encroachment) để có R:R tốt nhất ạ? Nếu đặt ở 50% thì có sợ bị lỡ sóng không thầy?",
                        'images' => [
                            'https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?auto=format&fit=crop&w=800&q=80',
                        ],
                        'is_featured' => true,
                        'featured_note' => 'Giải đáp trọng tâm về mức cân bằng 50% Consequent Encroachment (C.E) của FVG và chiến lược vào lệnh tối ưu R:R.',
                        'featured_at' => now()->subDays(2),
                    ]
                );

                QuestionAnswer::updateOrCreate(
                    [
                        'question_id' => $q2->id,
                        'user_id' => $admin->id,
                    ],
                    [
                        'content' => "Chào Bình! Quy tắc vàng là:\n- Nếu FVG đi kèm một vùng Order Block rõ ràng bên dưới, giá thường có xu hướng chạm sâu vào mức 50% FVG (hoặc chạm hẳn vào mép trên của Order Block) rồi mới bứt phá. Khi đó đặt Limit tại 50% sẽ giúp em có Stop Loss ngắn hơn và tỷ lệ R:R cao hơn gấp đôi.\n- Trong thị trường có Momentum cực mạnh (ví dụ sau tin tức CPI hoặc lãi suất), giá chỉ chạm mép ngoài của FVG là bay tiếp. Lúc này giải pháp thông minh là chia 2 lệnh nhỏ (50% volume tại mép và 50% volume tại CE 50%) để vừa không lỡ sóng vừa trung bình giá tối ưu.",
                    ]
                );
            }

            // Question 3 (Normal / Unfeatured) on Lesson 3
            if (isset($lessonsC1[2])) {
                $q3 = LessonQuestion::updateOrCreate(
                    [
                        'user_id' => $students[2]->id,
                        'lesson_id' => $lessonsC1[2]->id,
                        'title' => 'Nến Pinbar có cần phải có râu dài gấp 3 lần thân nến không?',
                    ],
                    [
                        'content' => "Thưa thầy, trong sách thường nói râu nến Pinbar phải dài tối thiểu gấp 2 đến 3 lần thân. Nếu nến Pinbar xuất hiện ngay tại vùng cản cứng nhưng râu chỉ dài gấp 1.5 lần thân thì có được coi là tín hiệu tin cậy không ạ?",
                        'is_featured' => false,
                    ]
                );

                QuestionAnswer::updateOrCreate(
                    [
                        'question_id' => $q3->id,
                        'user_id' => $admin->id,
                    ],
                    [
                        'content' => "Chào Cường! Tỷ lệ râu nến là tiêu chuẩn sách giáo khoa, nhưng bối cảnh thị trường (Context) quan trọng hơn hình thái nến rất nhiều. Nếu một nến Pinbar râu 1.5 lần thân nhưng xuất hiện tại Key Level H4 và nến trước đó vừa quét sạch thanh khoản của một chuỗi Equal Lows thì tín hiệu đó vẫn cực kỳ mạnh mẽ. Hãy ưu tiên bối cảnh trước rồi mới xét đến hình dáng nến nhé!",
                    ]
                );
            }
        }
    }
}

