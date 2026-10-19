# HỆ THỐNG QUẢN LÝ LỚP HỌC TRADING FOREX - MINHTRADING

> **Công nghệ xây dựng:** Laravel 13.4.0 | PHP 8.3+ | MySQL 8.4+ | Tailwind CSS | Alpine.js | Chart.js  
> **Môi trường hoạt động:** Laragon / Localhost (`c:\laragon\www\minhtrading`)

---

## 1. THÔNG TIN TÀI KHOẢN THỬ NGHIỆM

Tất cả tài khoản thử nghiệm bên dưới đều dùng chung mật khẩu: **`password`**

| Vai trò | Họ tên | Email đăng nhập | Mật khẩu | Chức năng chính |
| :--- | :--- | :--- | :--- | :--- |
| **Giảng viên (Admin)** | Quản Trị Viên MinhTrading | `admin@minhtrading.com` | `password` | Toàn quyền quản trị học viên, giáo án, chấm bài, duyệt mô hình mẫu, xem thống kê |
| **Học viên 1** | Nguyễn Văn An | `nguyen.van.an@example.com` | `password` | Học viên tiêu biểu, nhiều lệnh thực chiến, đã nộp bài tập |
| **Học viên 2** | Trần Thị Bích | `tran.thi.bich@example.com` | `password` | Học bài, nộp bài tập, theo dõi nhật ký backtest |
| **Học viên 3** | Lê Hoàng Long | `le.hoang.long@example.com` | `password` | Học viên mới, xem thông báo và thực hành |
| **Học viên 4** | Phạm Minh Đức | `pham.minh.duc@example.com` | `password` | Thực hành ghi nhật ký vào lệnh Live & Demo |
| **Học viên 5** | Hoàng Thị Mai | `hoang.thi.mai@example.com` | `password` | Luyện tập nghiên cứu các mô hình lệnh mẫu |

---

## 2. HƯỚNG DẪN KHỞI CHẠY HỆ THỐNG

### Cách 1: Chạy trực tiếp qua PHP Artisan
1. Mở PowerShell hoặc Terminal tại thư mục: `c:\laragon\www\minhtrading`
2. Chạy lệnh:
   ```bash
   php artisan serve --port=8000
   ```
3. Mở trình duyệt web truy cập: [http://127.0.0.1:8000](http://127.0.0.1:8000)

### Cách 2: Sử dụng máy chủ ảo Laragon
1. Đảm bảo Laragon đã bật **Apache/Nginx** và **MySQL**.
2. Truy cập qua tên miền ảo nội bộ: [http://minhtrading.test](http://minhtrading.test)

*(Tùy chọn: Chạy kiểm thử tự động với lệnh `php artisan test` - hiện có 80/80 test case đạt chuẩn với 323 assertions).*

---

## 3. TÓM TẮT TÍNH NĂNG HỆ THỐNG

### A. Phân Hệ Dành Cho Giảng Viên (Admin)

1. **Bảng Điều Khiển Quản Trị (Dashboard):**
   - Thống kê tổng số học viên đang hoạt động, số khóa học, số bài tập chờ duyệt chấm, số lệnh trading trong tháng.
   - Bảng danh sách bài tập vừa nộp cần chấm điểm ngay.
   - Bảng xếp hạng Top học viên có tỷ lệ thắng (Win Rate) và P&L cao nhất.

2. **Quản Lý Học Viên (Student Management):**
   - Danh sách học viên, tìm kiếm theo tên/email, kích hoạt hoặc tạm khóa tài khoản.
   - Xem hồ sơ chi tiết từng học viên: Số lệnh đã vào, tỷ lệ Winrate, điểm trung bình bài tập.
   - **Thống kê chuyên sâu từng học viên (`/admin/students/{id}/stats`):** Biểu đồ biến động số dư, đường cong P&L, tỷ lệ thắng theo từng cặp tiền tệ (XAU/USD, EUR/USD, GBP/USD...).

3. **Quản Lý Khóa Học & Soạn Bài Giảng (Courses & Lessons Management):**
   - **Tạo & quản lý khóa học:** Đặt tiêu đề, mô tả, ảnh thumbnail, thứ tự hiển thị, bật/tắt xuất bản.
   - **Cấp quyền học viên (Access Control):** Giảng viên có thể chọn cấp quyền hoặc thu hồi quyền truy cập khóa học cho từng học viên cụ thể ngay khi tạo khóa học hoặc trong trang chi tiết khóa học.
   - **Soạn bài học với trình soạn thảo như Word (Rich Text / WYSIWYG Editor):**
     - Hỗ trợ định dạng tiêu đề (H1, H2, H3), in đậm, in nghiêng, gạch chân, trích dẫn, khối mã, danh sách số/chấm.
     - **Chèn hình ảnh minh họa trực tiếp vào bài viết** (tải ảnh từ máy tính hoặc dán ảnh clipboard).
     - **Nhúng Link Video bài giảng:** Hỗ trợ link YouTube, Vimeo, Google Drive...
     - **Tải lên File đính kèm:** Đính kèm file PDF, Word, Excel, Slide giáo trình hoặc Cheat-sheet trading cho học viên tải về.
   - **Quản lý Bài học Độc lập:** Soạn thảo văn bản rich text, định dạng heading/list, nhúng ảnh trực tiếp vào bài viết, video bài giảng (YouTube, Vimeo, Google Drive hỗ trợ nhúng xem trực tiếp) và file tài liệu đính kèm.
   - **Tách biệt Logic Bài Học & Bài Tập:** Giảng viên chủ động tự tạo từng bài tập thực hành theo nhu cầu giảng dạy (`/admin/assignments/create`) thay vì tự động ép cứng mỗi bài học 1 bài tập. Form tạo/sửa bài học được tối ưu tinh gọn, loại bỏ các trường bài tập thừa.
   - **Sắp xếp thứ tự bài học (Lesson Reordering):** Cho phép Giảng viên điều chỉnh số thứ tự bài học (#1, #2, #3...) và lưu thứ tự để học viên học theo đúng lộ trình đào tạo chuẩn.

4. **Quản Lý Gộp Bài Tập & Bài Nộp (`/admin/submissions` & `/admin/assignments`):**
   - **Chủ động tạo đề bài tập linh hoạt:** Giảng viên tạo đề bài tập độc lập tại `/admin/assignments/create`, chọn bài học và khóa học áp dụng, thiết lập hình thức nộp (Phân tích / Backtest / Vào lệnh thực tế) và deadline.
   - **⚡ Tái Sử Dụng & Nhân Bản Đề Bài Tập 1-Click (Assignment 1-Click Clone):**
     - **Nhân bản 1-Click từ danh sách & chi tiết:** Tại mỗi dòng bài tập trong `/admin/assignments` và trang chi tiết `/admin/assignments/{id}`, Giảng viên chỉ cần bấm nút **📋 Nhân bản** -> Popup Modal hiện lên -> Chọn Khóa học đích & Bài học đích cần giao đề -> Bấm **"Xác nhận Nhân bản"**. Hệ thống lập tức sao chép nguyên vẹn: Tiêu đề, Loại bài, Mô tả và Yêu cầu thực hành chi tiết sang khóa mới.
     - **Điền nhanh từ đề bài cũ (Template Pre-fill):** Trong form tạo mới `/admin/assignments/create`, bổ sung thanh chọn *"⚡ Tái sử dụng nhanh từ đề bài cũ"*. Khi chọn 1 đề có sẵn, form tự động điền ngay tiêu đề, loại bài, mô tả và yêu cầu để Giảng viên tinh chỉnh nhanh mà không cần gõ lại.
   - **Chia theo khóa học & Lọc đa năng:**
     - Tab **"📋 Bài nộp của học viên":** Lọc theo Khóa học (`course_id`), trạng thái chấm, học viên, bài tập; chấm điểm thang 10 kèm nhận xét và đánh giá tiêu biểu.
     - Tab **"📝 Danh sách đề bài tập":** Danh sách bài tập được chia theo từng khóa học; bộ lọc nhanh theo Khóa học, Loại bài tập, và Trạng thái nộp bài.
     - Tab **"📈 Thống kê hoàn thành":** Báo cáo trực quan tỷ lệ nộp bài, điểm số trung bình, tỷ lệ hoàn thành theo từng lớp.
   - Giảng viên quản lý toàn bộ quy trình giao đề và chấm bài tại một nơi duy nhất.

5. **Chọn Lệnh Của Học Viên / Lệnh Mẫu (Convert to Model Trade):**
   - Giảng viên có thể duyệt nhật ký lệnh của học viên hoặc lệnh của chính mình, sau đó bấm **"Tạo mô hình mẫu từ lệnh này"** để đưa vào thư viện chung làm bài giảng thực chiến.

6. **Thư Viện Mô Hình Mẫu (`/admin/model-trades`):**
   - **Định dạng bảng Table:** Tối ưu không gian hiển thị, ẩn các chi tiết rườm rà (không hiển thị Entry, SL, TP trên danh sách bảng).
   - **Các cột thông tin hiển thị:** Hình ảnh nhỏ (Thumbnail), Cặp tiền, Vị thế (BUY/SELL), Khung thời gian (Timeframe), Mô hình & Lý do vào lệnh, Nguồn gốc lệnh, Trạng thái xuất bản.
   - **Popup Modal phóng to ảnh:** Bấm trực tiếp vào ảnh biểu đồ để xem popup phóng to nhanh kèm tóm tắt lý do setup mà không cần chuyển trang.
   - **Trang xem chi tiết (`show.blade.php`):** Hiển thị toàn bộ thông số kỹ thuật (Entry, SL, TP, tỷ lệ R:R, số pips kiếm được, checklist điều kiện vào lệnh, phân tích bối cảnh và toàn bộ chú thích của Giảng viên).

7. **Đánh Giá Bài Tập Tiêu Biểu & Vinh Danh Học Viên:**
   - Trong quá trình chấm bài nộp (`/admin/submissions/{id}/review`), Giảng viên có thể tích chọn **"⭐ Đánh giá là BÀI TẬP TIÊU BIỂU"** và nhập ghi chú lý do tiêu biểu (ví dụ: "Phân tích SMC chuẩn chỉ, quản lý vốn chặt chẽ").
   - Giảng viên cũng có thể bật/tắt (toggle) trạng thái tiêu biểu trực tiếp từ trang chi tiết bài nộp (`/admin/submissions/{id}`).
   - Khi được đánh giá tiêu biểu, học viên sẽ nhận được thông báo chúc mừng vinh danh tự động từ hệ thống.
   - **Chia sẻ mở toàn hệ thống (Cross-course Sharing):** Bài nộp tiêu biểu sẽ được đưa vào thư viện Showcase chung để **toàn bộ học viên ở mọi khóa học (K15, K16, K17...) đều có thể tham khảo, nghiên cứu và học hỏi kinh nghiệm**, phá vỡ rào cản phân cách giữa các khóa.

8. **Quản Lý & Giải Đáp Câu Hỏi Của Học Viên (`/admin/questions`):**
   - Theo dõi toàn bộ thắc mắc của học viên được đặt **trong từng bài học** hoặc **ngoài bài học (hỏi đáp chung)**.
   - **Bộ lọc đa chiều thông minh:**
     - **Lọc theo Khóa học / Ngoài bài học:** Cho phép lọc xem câu hỏi của từng khóa học cụ thể hoặc lọc riêng mục `🌐 Ngoài bài học (Hỏi đáp chung)`.
     - **Lọc theo Trạng thái giải đáp:** `Tất cả` | `⏳ Chưa có câu trả lời` (`unanswered`) | `✅ Đã có câu trả lời` (`answered`).
     - **Lọc Tiêu biểu:** Xem các câu hỏi đã được đánh giá tiêu biểu.
     - **Tìm kiếm:** Tìm theo từ khóa trong tiêu đề, nội dung hoặc tên học viên.
   - **Soạn lời giải đáp chuyên nghiệp (`/admin/questions/{id}`):**
     - Giảng viên soạn nội dung giải đáp cặn kẽ cho học viên.
     - **Hỗ trợ tải lên hình ảnh biểu đồ minh họa:** Đính kèm ảnh chụp màn hình phân tích chuẩn (Chart sửa lỗi, xác nhận entry, chỉ ra FVG/OB chuẩn...).
     - Tích chọn **"⭐ Đánh giá câu hỏi này là CÂU HỎI TIÊU BIỂU"** kèm ghi chú đánh giá để đưa vào thư viện chung.
     - Tự động gửi thông báo trực tiếp đến học viên khi Giảng viên giải đáp.

9. **Quản Lý Thư Viện Showcase Tiêu Biểu (`/admin/featured`):**
   - Không gian tập trung quản lý toàn bộ nội dung xuất sắc nhất của học viện với 2 Tab:
     - **Tab 1 - 📝 Bài tập tiêu biểu:** Hiển thị học viên, điểm số, đề bài, nội dung nộp, ảnh biểu đồ, lời nhận xét vinh danh của Giảng viên. Có nút hủy tiêu biểu nhanh hoặc chấm lại.
     - **Tab 2 - ❓ Câu hỏi tiêu biểu:** Hiển thị câu hỏi của học viên, ảnh đính kèm, lời giải đáp chuẩn của Giảng viên, ghi chú lý do tiêu biểu.
   - Bộ lọc theo từng khóa học cụ thể.

10. **Quản Lý Học Phí & Cảnh Báo Nợ Thông Minh (`/admin/tuition`):**
    - **Mô hình quản lý linh hoạt đa kỳ hạn:**
      - Đóng theo từng tháng (`monthly`), đóng trước nhiều tháng (`multi_month` - 3 tháng theo quý, 6 tháng...), đóng trọn gói cả khóa (`full_course` - học trọn đời, miễn phí duy trì), hoặc miễn phí học bổng (`free`).
    - **Thuật toán tự động phát hiện & cảnh báo nợ:**
      - Hệ thống tự động so sánh ngày hiện tại với mốc `paid_until` của học viên để phân loại tức thời:
        - 🟢 **Đã đóng đủ:** Còn hạn > 5 ngày.
        - 🟡 **Sắp hết hạn:** Còn $\le$ 5 ngày nữa là đến hạn đóng tiếp theo.
        - 🔴 **Chưa nộp tháng này:** Quá hạn từ 1 đến 30 ngày (ghi rõ quá hạn bao nhiêu ngày).
        - ⛔ **Nợ nhiều tháng:** Quá hạn > 30 ngày (tính rõ nợ 2 tháng, 3 tháng...).
        - 💎 **Trọn gói cả khóa:** Không bao giờ báo nợ.
    - **Giao diện nhập liệu thông minh 1-Click (Chống nhầm lẫn 100%):**
      - Khi chọn Học viên, hệ thống lập tức hiển thị **Thẻ tóm tắt học phí hiện tại** (hạn nộp cũ, số tiền/tháng, tình trạng nợ).
      - **Các nút Preset tự động:** Bấm `[+ 1 Tháng]`, `[+ 3 Tháng (Quý)]`, `[+ 6 Tháng]`, `[Trọn gói]` $\rightarrow$ Hệ thống tự động cộng dồn từ ngày hết hạn cũ, tự nhảy ngày kết thúc (End Date) chính xác, tự nhân số tiền và tự điền nội dung ghi chú. Admin không phải tự tính nhẩm ngày kết thúc!
      - Đính kèm ảnh chụp bill chuyển khoản, mã giao dịch ngân hàng và tự động gửi **Thông báo biên lai** xác nhận vào tài khoản học viên.
    - **Hành động nhanh & Nhắc phí:** Nút **"Nhắc phí 1-Click"** gửi thông báo kèm hướng dẫn chuyển khoản trực tiếp tới app của học viên; xem sổ cái lịch sử giao dịch toàn viện hoặc chi tiết từng học viên.

11. **Báo Cáo & Thống Kê Toàn Lớp (Trade Analytics):**
    - Tổng hợp toàn bộ dữ liệu giao dịch của học viên trong lớp.
    - Thống kê tỷ lệ Win/Loss, các cặp tiền được giao dịch hiệu quả nhất, phân tích tâm lý và kỷ luật lệnh.

12. **Gửi Thông Báo (Notifications):**
    - Gửi thông báo đến toàn thể lớp học hoặc đích danh một học viên cụ thể (thông báo lịch học, nhắc hạn nộp bài tập, chia sẻ góc nhìn thị trường).

13. **Thanh Menu Điều Hướng Thông Minh (Collapsible Sidebar):**
    - Trang bị nút thu gọn / mở rộng menu (`«` / `»`) linh hoạt trên thanh Top Navbar và đầu thanh Sidebar.
    - Chế độ thu gọn (Mini Sidebar - 80px) giúp mở rộng không gian xem biểu đồ nến TradingView, bảng dữ liệu lớn và tài liệu bài học.
    - Tự động lưu trạng thái thu gọn vào trình duyệt (`localStorage`) để duy trì khi chuyển trang.
    - Hỗ trợ phím tắt tiện lợi: **`Ctrl + B`** để đóng/mở nhanh tức thì.

---

### B. Phân Hệ Dành Cho Học Viên (Student)

1. **Dashboard Học Viên Cá Nhân:**
   - Xem nhanh thông báo mới nhất từ Giảng viên.
   - Thống kê tổng số lệnh cá nhân, tỷ lệ thắng hiện tại, tổng lợi nhuận P&L, số bài tập chưa nộp.
   - Danh sách bài tập sắp đến hạn và các lệnh giao dịch gần nhất.

2. **Khóa Học Được Cấp Quyền & Học Bài Tương Tác:**
   - **Giới hạn quyền truy cập nghiêm ngặt:** Học viên chỉ có thể xem danh sách các khóa học và bài học mà mình đã được Giảng viên cấp quyền.
   - **Chặn truy cập trái phép:** Nếu học viên chưa được cấp quyền mà cố tình truy cập qua đường link trực tiếp, hệ thống sẽ chặn với thông báo lỗi 403 rõ ràng ("Bạn chưa được cấp quyền truy cập khóa học này").
   - **Giao diện học bài tối ưu:** Xem nội dung bài viết định dạng chuẩn rich text kèm hình ảnh minh họa sinh động, xem Video bài giảng nhúng sẵn, tải tài liệu đính kèm và làm bài tập thực hành liên quan.
   - Nút chuyển tiếp bài học trước / bài học sau theo đúng thứ tự lộ trình.
   - **Mục Hỏi đáp & Thắc mắc trực tiếp dưới mỗi bài học:**
     - Học viên có thể đặt câu hỏi thắc mắc riêng cho bài học đó với tiêu đề, nội dung và **tải kèm tối đa 5 hình ảnh biểu đồ chart**.
     - Xem các câu hỏi đã có của bài học, nhận biết câu hỏi tiêu biểu có badge `⭐ Tiêu biểu` và đọc câu trả lời giải đáp từ Giảng viên.
     - Trang thảo luận chi tiết (`/student/questions/{id}`) cho phép học viên và Giảng viên trao đổi, phản hồi nhiều lượt.

3. **Gộp Quản Lý Bài Tập & Hỏi Đáp (`/student/assignments` - Menu "📝 Bài tập & Hỏi đáp"):**
   - **Tích hợp 2 Tab nghiệp vụ song hành:**
     - **Tab 1 - 📝 Bài Tập & Bài Nộp:**
       - Mỗi bài tập được tạo tương ứng với nội dung kiến thức trọng tâm cần rèn luyện.
       - Danh sách bài tập chia theo từng khóa học được cấp quyền, có thanh tiến độ hoàn thành trực quan.
       - Bộ lọc nhanh theo Khóa học và Trạng thái nộp: `Tất cả` | `Chưa nộp` | `Chờ chấm` | `Đã chấm` | `Cần sửa lại`.
       - Hành động thông minh theo trạng thái: Nếu chưa nộp -> nút **"Làm bài ngay"**; Nếu đã nộp -> nút **"Xem bài & nhận xét"**; Nếu cần sửa lại -> nút **"Sửa & Nộp lại"**.
     - **Tab 2 - 💬 Hỏi Đáp & Thảo Luận:**
       - **Đặt câu hỏi linh hoạt qua Popup Modal:**
         - **Trong bài học cụ thể:** Học viên chọn Khóa học -> Chọn Bài học cụ thể để đặt câu hỏi.
         - **Ngoài bài học (Hỏi đáp chung):** Học viên đặt các câu hỏi mở về tâm lý, quản lý vốn, setup thực tế, chọn sàn... có thể gắn với một khóa học cụ thể hoặc để hỏi chung toàn hệ thống.
         - Đính kèm tối đa 5 hình ảnh biểu đồ phân tích (JPG, PNG, WebP).
       - **Bộ lọc tìm kiếm toàn diện:**
         - **Lọc theo Khóa học / Ngoài bài học:** `Tất cả phạm vi` | `🌐 Ngoài bài học (Hỏi chung)` | Từng khóa học được cấp quyền.
         - **Lọc theo Trạng thái:** `Tất cả` | `⏳ Chưa có câu trả lời` | `✅ Đã có câu trả lời`.
         - **Lọc câu hỏi cá nhân:** Tùy chọn checkbox `"Chỉ câu hỏi của tôi"`.
         - **Tìm kiếm từ khóa:** Tìm nhanh theo tiêu đề và nội dung thắc mắc.
       - Thẻ hiển thị trực quan thông tin người hỏi, badge phạm vi bài học, badge trạng thái đã/chưa giải đáp, số lượt phản hồi, ảnh thumbnail và link vào trang thảo luận chi tiết `/student/questions/{id}`.

4. **Theo Dõi Học Phí & Biên Lai Thanh Toán (`/student/tuition` - Menu "💳 Học phí & Biên lai"):**
   - **Thẻ trạng thái học phí cá nhân:**
     - Hiển thị ngày hết hạn học phí (`paid_until`), số ngày còn lại hoặc cảnh báo quá hạn.
     - Mức học phí hàng tháng và tổng số tiền đã đóng trong quá trình học.
   - **Thông tin hướng dẫn chuyển khoản:**
     - Thông tin tài khoản ngân hàng thụ hưởng (Techcombank/MB Bank) kèm cú pháp chuyển khoản chuẩn tự động tạo theo tên và SĐT của học viên.
   - **Sổ cái biên lai thanh toán:**
     - Danh sách toàn bộ các lần đã nộp tiền, số tiền, kỳ hạn học phí (từ ngày đến ngày), phương thức và link xem ảnh biên lai xác nhận.

5. **Thư Viện Bài Tập & Câu Hỏi Tiêu Biểu - Showcase Mở Rộng (`/student/featured`):**
   - **Chia sẻ mở toàn diện:** Học viên có thể xem **tất cả bài tập tiêu biểu và câu hỏi tiêu biểu của tất cả các khóa học** trong học viện để học hỏi kinh nghiệm, phương pháp phân tích biểu đồ và cách xử lý tình huống thực chiến xuất sắc từ các bạn học khác.
   - **2 Tab chuyên biệt:**
     - **Tab 1 - 📝 Bài tập tiêu biểu:** Hiển thị card bài làm mẫu, điểm số, phân tích của bạn học, ảnh biểu đồ phóng to được, và đặc biệt là nhận xét lý do vinh danh của Giảng viên.
     - **Tab 2 - ❓ Câu hỏi tiêu biểu:** Xem các thắc mắc hay về kỹ thuật giao dịch, ảnh biểu đồ đính kèm, lời giải đáp chuẩn mực của Giảng viên và lý do Giảng viên bình chọn.
   - **Bộ lọc linh hoạt:** Lọc theo từng khóa học hoặc xem tất cả khóa học trên toàn hệ thống.

6. **Quản Lý Nhật Ký Backtest (`/student/backtests`):**
   - Ghi chép dữ liệu các lệnh luyện tập trong quá khứ (Replay mode).
   - Ghi nhận: Cặp tiền, Khung thời gian, Vị thế Mua/Bán, Giá vào, Cắt lỗ, Chốt lời, Kết quả (Win/Loss), Lý do vào lệnh, Ảnh biểu đồ trước và sau khi chạy.
   - Tự động thống kê số lệnh, tỷ lệ Winrate và tỷ lệ Risk:Reward trung bình của phương pháp.

7. **Quản Lý Lệnh Thực Chiến / Demo (`/student/trades`):**
   - Sổ tay giao dịch hàng ngày giúp rèn luyện kỷ luật quản lý vốn.
   - Theo dõi trạng thái lệnh: Đang chạy (Pending), Thắng (Win), Thua (Loss), Hòa vốn (Breakeven).
   - Thống kê số pips và số tiền lời/lỗ thực tế.

8. **Thư Viện Mô Hình Mẫu (`/student/model-trades`):**
   - **Giao diện bảng Table trực quan:** Hiển thị danh sách các case study chọn lọc với ảnh nhỏ, cặp tiền, BUY/SELL, khung thời gian, tên mô hình & lý do vào lệnh.
   - **Bộ lọc nhanh:** Lọc theo cặp tiền tệ quan tâm hoặc lọc theo vị thế BUY / SELL.
   - **Xem ảnh popup modal:** Click vào ảnh biểu đồ để phóng to ngay trên màn hình, xem nhanh tóm tắt lý do setup.
   - **Nút "Xem chi tiết":** Mở trang nghiên cứu chuyên sâu, xem toàn bộ thông số giá Entry, SL, TP, tỷ lệ R:R, checklist điều kiện vào lệnh và lời chú thích giải nghĩa của Giảng viên.

9. **Trung Tâm Thông Báo (Notifications):**
   - Nhận thông báo tự động khi Giảng viên xác nhận thu học phí, khi chấm bài tập, khi bài nộp được vinh danh Tiêu Biểu, hoặc khi Giảng viên giải đáp câu hỏi của mình.
   - Đánh dấu đã đọc hoặc xem lại các thông báo cũ.

---

## 4. BẢNG TỔNG HỢP CÁC ĐƯỜNG DẪN CHÍNH (URL ROUTES)

| Nhóm | Chức năng | Đường dẫn (URL) |
| :--- | :--- | :--- |
| **Chung** | Trang chủ / Đăng nhập | `/` hoặc `/login` |
| **Chung** | Chuyển hướng sau đăng nhập | `/dashboard` (tự động nhận diện Admin hoặc Student) |
| **Admin** | Bảng điều khiển quản trị | `/admin/dashboard` |
| **Admin** | Quản lý học viên | `/admin/students` |
| **Admin** | **Quản lý học phí & Cảnh báo nợ** | `/admin/tuition` |
| **Admin** | Ghi nhận thu học phí (1-Click Presets) | `/admin/tuition/create` |
| **Admin** | Sổ cái lịch sử thu tiền toàn viện | `/admin/tuition/history` |
| **Admin** | Lịch sử đóng tiền của 1 học viên | `/admin/tuition/students/{id}` |
| **Admin** | Quản lý khóa học & bài học | `/admin/courses` |
| **Admin** | **Gộp Quản lý Bài tập & Bài nộp** | `/admin/submissions` hoặc `/admin/assignments` |
| **Admin** | **Hỏi đáp học viên (Q&A)** | `/admin/questions` |
| **Admin** | **Quản lý Bài tập & Câu hỏi tiêu biểu (Showcase)** | `/admin/featured` |
| **Admin** | **Thư viện mô hình mẫu (Dạng Bảng Table)** | `/admin/model-trades` |
| **Admin** | Thống kê Trading của học viên | `/admin/trade-stats` |
| **Admin** | Gửi thông báo đến học viên | `/admin/notifications` |
| **Student** | Dashboard học viên | `/student/dashboard` |
| **Student** | Khóa học & Bài giảng được cấp quyền | `/student/courses` |
| **Student** | **Bài tập & Hỏi đáp (2 Tab)** | `/student/assignments` |
| **Student** | **Học phí & Biên lai thanh toán** | `/student/tuition` |
| **Student** | Thư viện Tiêu Biểu (Showcase mở rộng) | `/student/featured` |
| **Student** | Nhật ký Backtest | `/student/backtests` |
| **Student** | Nhật ký Vào lệnh (Live & Demo) | `/student/trades` |
| **Student** | Thư viện Mô hình mẫu | `/student/model-trades` |
| **Student** | Hộp thư thông báo | `/student/notifications` |

