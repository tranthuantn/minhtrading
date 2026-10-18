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

*(Tùy chọn: Chạy kiểm thử tự động với lệnh `php artisan test` - hiện có 65/65 test case đạt chuẩn với 235 assertions).*

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
   - **Tự động tạo bài tập tương ứng với bài học:** Số lượng bài tập tương ứng chính xác với số bài học trong khóa học. Khi thêm bài học mới, hệ thống tự động tạo 1 bài tập thực hành tương ứng (Giảng viên có thể tùy chỉnh tiêu đề, loại bài tập phân tích/backtest/vào lệnh, yêu cầu và hạn nộp ngay trong form bài học).
   - **Sắp xếp thứ tự bài học (Lesson Reordering):** Cho phép Giảng viên điều chỉnh số thứ tự bài học (#1, #2, #3...) và lưu thứ tự để học viên học theo đúng lộ trình đào tạo chuẩn.

4. **Quản Lý Gộp Bài Tập & Bài Nộp (`/admin/submissions` & `/admin/assignments`):**
   - **Chia theo khóa học & Lọc đa năng:**
     - Tab **"📋 Bài nộp của học viên":** Lọc theo Khóa học (`course_id`), trạng thái chấm, học viên, bài tập; chấm điểm thang 10 kèm nhận xét.
     - Tab **"📝 Danh sách đề bài tập":** Danh sách bài tập được **chia theo từng khóa học** với tiêu đề bài học và bài tập tương ứng; bộ lọc nhanh theo Khóa học, Loại bài tập (Analysis / Backtest / Trade Entry), và Trạng thái nộp bài (Có bài chờ chấm / Đã chấm / Chưa có bài nộp).
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
   - Bài nộp tiêu biểu sẽ được đưa vào thư viện Showcase của lớp để các học viên khác cùng tham khảo và học hỏi.

8. **Quản Lý & Giải Đáp Câu Hỏi Của Học Viên (`/admin/questions`):**
   - Theo dõi toàn bộ các thắc mắc của học viên được đặt theo từng bài học cụ thể.
   - **Bộ lọc thông minh:** Lọc theo Khóa học, Trạng thái giải đáp (`Chưa trả lời` / `Đã trả lời`), Trạng thái tiêu biểu, và Tìm kiếm theo từ khóa hoặc tên học viên.
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

10. **Báo Cáo & Thống Kê Toàn Lớp (Trade Analytics):**
    - Tổng hợp toàn bộ dữ liệu giao dịch của học viên trong lớp.
    - Thống kê tỷ lệ Win/Loss, các cặp tiền được giao dịch hiệu quả nhất, phân tích tâm lý và kỷ luật lệnh.

11. **Gửi Thông Báo (Notifications):**
    - Gửi thông báo đến toàn thể lớp học hoặc đích danh một học viên cụ thể (thông báo lịch học, nhắc hạn nộp bài tập, chia sẻ góc nhìn thị trường).

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

3. **Gộp Quản Lý Bài Tập & Bài Đã Nộp (`/student/assignments`):**
   - **Mỗi khóa học có số lượng bài tập tương ứng:** Số bài tập bằng đúng số bài học trong khóa học. Học viên học đến đâu làm bài tập đến đó.
   - **Danh sách bài tập chia theo từng khóa học:** Mỗi khóa học được hiển thị trong một khung riêng biệt, có tiêu đề khóa học, badge số lượng bài tập = bài học, và thanh tiến độ hoàn thành trực quan.
   - **Bộ lọc khóa học & Bộ lọc trạng thái đồng bộ:**
     - **Lọc Khóa Học:** Cho phép chọn xem bài tập của một khóa học cụ thể hoặc tất cả các khóa học được cấp quyền.
     - **Lọc Trạng Thái:** Lọc nhanh tức thời: `Tất cả` | `Chưa nộp` | `Chờ chấm` | `Đã chấm` | `Cần sửa lại`.
   - **Bảng danh sách hợp nhất đa năng:**
     - Xem trực quan Tên bài tập, Bài học tương ứng, Loại bài (Phân tích / Backtest / Vào lệnh), Hạn nộp (Deadline), Trạng thái nộp bài, Điểm số đạt được và Nhận xét của Giảng viên.
     - Hành động thông minh theo trạng thái: Nếu chưa nộp -> nút **"Làm bài ngay"**; Nếu đã nộp -> nút **"Xem bài & nhận xét"**; Nếu cần sửa lại -> nút **"Sửa & Nộp lại"**.
   - **Trang làm bài & xem kết quả tích hợp (`show.blade.php`):** Xem yêu cầu đề bài, nộp bài/sửa bài, và đọc toàn bộ nhận xét, điểm số, hướng dẫn khắc phục của Giảng viên trên cùng một trang.

4. **Thư Viện Bài Tập & Câu Hỏi Tiêu Biểu - Showcase (`/student/featured`):**
   - **Không gian học tập từ bài mẫu xuất sắc:** Nơi học viên có thể vào đọc và tham khảo các bài nộp đạt điểm tuyệt đối và những câu hỏi hay có tính đúc kết thực chiến cao.
   - **Phân quyền chặt chẽ:** Học viên **chỉ thấy các bài tập và câu hỏi tiêu biểu thuộc các khóa học mà mình đã được cấp quyền học**.
   - **2 Tab chuyên biệt:**
     - **Tab 1 - 📝 Bài tập tiêu biểu:** Hiển thị card bài làm mẫu, điểm số, phân tích của bạn học, ảnh biểu đồ phóng to được, và đặc biệt là nhận xét lý do vinh danh của Giảng viên.
     - **Tab 2 - ❓ Câu hỏi tiêu biểu:** Xem các thắc mắc hay về kỹ thuật giao dịch, ảnh biểu đồ đính kèm, lời giải đáp chuẩn mực của Giảng viên và lý do Giảng viên bình chọn.
   - Bộ lọc theo từng khóa học của học viên.

5. **Quản Lý Nhật Ký Backtest (`/student/backtests`):**
   - Ghi chép dữ liệu các lệnh luyện tập trong quá khứ (Replay mode).
   - Ghi nhận: Cặp tiền, Khung thời gian, Vị thế Mua/Bán, Giá vào, Cắt lỗ, Chốt lời, Kết quả (Win/Loss), Lý do vào lệnh, Ảnh biểu đồ trước và sau khi chạy.
   - Tự động thống kê số lệnh, tỷ lệ Winrate và tỷ lệ Risk:Reward trung bình của phương pháp.

6. **Quản Lý Lệnh Thực Chiến / Demo (`/student/trades`):**
   - Sổ tay giao dịch hàng ngày giúp rèn luyện kỷ luật quản lý vốn.
   - Theo dõi trạng thái lệnh: Đang chạy (Pending), Thắng (Win), Thua (Loss), Hòa vốn (Breakeven).
   - Thống kê số pips và số tiền lời/lỗ thực tế.

7. **Thư Viện Mô Hình Mẫu (`/student/model-trades`):**
   - **Giao diện bảng Table trực quan:** Hiển thị danh sách các case study chọn lọc với ảnh nhỏ, cặp tiền, BUY/SELL, khung thời gian, tên mô hình & lý do vào lệnh.
   - **Bộ lọc nhanh:** Lọc theo cặp tiền tệ quan tâm hoặc lọc theo vị thế BUY / SELL.
   - **Xem ảnh popup modal:** Click vào ảnh biểu đồ để phóng to ngay trên màn hình, xem nhanh tóm tắt lý do setup.
   - **Nút "Xem chi tiết":** Mở trang nghiên cứu chuyên sâu, xem toàn bộ thông số giá Entry, SL, TP, tỷ lệ R:R, checklist điều kiện vào lệnh và lời chú thích giải nghĩa của Giảng viên.

8. **Trung Tâm Thông Báo (Notifications):**
   - Nhận thông báo tự động khi Giảng viên chấm bài tập, khi bài nộp được vinh danh Tiêu Biểu, hoặc khi Giảng viên giải đáp câu hỏi của mình.
   - Đánh dấu đã đọc hoặc xem lại các thông báo cũ.

---

## 4. BẢNG TỔNG HỢP CÁC ĐƯỜNG DẪN CHÍNH (URL ROUTES)

| Nhóm | Chức năng | Đường dẫn (URL) |
| :--- | :--- | :--- |
| **Chung** | Trang chủ / Đăng nhập | `/` hoặc `/login` |
| **Chung** | Chuyển hướng sau đăng nhập | `/dashboard` (tự động nhận diện Admin hoặc Student) |
| **Admin** | Bảng điều khiển quản trị | `/admin/dashboard` |
| **Admin** | Quản lý học viên | `/admin/students` |
| **Admin** | Quản lý khóa học & bài học | `/admin/courses` |
| **Admin** | **Gộp Quản lý Bài tập & Bài nộp** | `/admin/submissions` hoặc `/admin/assignments` |
| **Admin** | **Hỏi đáp học viên (Q&A)** | `/admin/questions` |
| **Admin** | **Quản lý Bài tập & Câu hỏi tiêu biểu (Showcase)** | `/admin/featured` |
| **Admin** | **Thư viện mô hình mẫu (Dạng Bảng Table)** | `/admin/model-trades` |
| **Admin** | Thống kê trading toàn học viện | `/admin/trade-stats` |
| **Admin** | Gửi thông báo | `/admin/notifications` |
| **Student** | Bảng điều khiển học viên | `/student/dashboard` |
| **Student** | Danh sách khóa học được cấp quyền | `/student/courses` |
| **Student** | **Gộp Bài tập & Bài đã nộp** | `/student/assignments` |
| **Student** | **Bài tập & Câu hỏi tiêu biểu (Showcase)** | `/student/featured` |
| **Student** | Quản lý nhật ký Backtest | `/student/backtests` |
| **Student** | Quản lý nhật ký Vào lệnh (Live/Demo) | `/student/trades` |
| **Student** | **Thư viện mô hình mẫu (Dạng Bảng Table)** | `/student/model-trades` |
| **Student** | Thông báo cá nhân | `/student/notifications` |

