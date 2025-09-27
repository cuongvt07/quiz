# PROMPT FOR COPILOT — Thiết kế & triển khai hệ thống thi trắc nghiệm (Laravel)

## 1. Mục tiêu
Thiết kế và tích hợp hệ thống **thi trắc nghiệm** vào source Laravel hiện có, bao gồm 2 phần chính:
1. **Trang quản trị hệ thống trắc nghiệm (Admin)**: tối giản, dễ dùng, hỗ trợ import câu hỏi + đáp án từ Excel.
2. **Trang thi trắc nghiệm cho người dùng (Frontend)**: giao diện tươi mới, mượt mà, dùng jQuery + AJAX hạn chế reload.

Công nghệ: **Laravel MVC + Blade + jQuery/JS**.

## 2. Chức năng chính
### 2.1 Trang Quản Trị
- Quản lý danh mục, môn học, bài thi, câu hỏi, đáp án.
- Import câu hỏi từ file Excel.
- Tạo bài thi, cấu hình số lượng câu hỏi, thời gian thi.
- Báo cáo kết quả thi của người dùng.

### 2.2 Trang Thi Trắc Nghiệm
- Người dùng đăng ký mặc định có **2 lượt thi miễn phí**.
- Khi hết lượt thi, không thể tham gia cho đến khi mua gói.
- Mua nhiều gói → **cộng dồn lượt thi và thời gian hiệu lực**.
- Bắt đầu thi → kiểm tra số lượt, tạo attempt mới, trừ lượt.
- Thi bằng giao diện AJAX (không reload), có autosave và đồng hồ đếm ngược.
- Khi hết giờ hoặc người dùng submit → lưu kết quả, chấm điểm, hiển thị kết quả.

## 3. Database & Migration
Kết hợp **file `quiz1.sql`** với DB hiện tại trong project.
- Giữ nguyên các bảng hiện có, chỉ thêm mới hoặc mở rộng bảng cần thiết.

### Lưu ý
- Khi user bắt đầu thi:
  - Nếu còn `free_slots` > 0 → giảm 1.
  - Nếu hết, kiểm tra gói đang hoạt động trong `user_subscriptions`.
  - Nếu không có → không cho thi, hiển thị thông báo và link mua gói.
- Khi mua gói:
  - Cộng thêm lượt thi + cộng dồn thời gian hiệu lực.

## 4. Import Câu Hỏi từ Excel
- Sử dụng package **maatwebsite/excel**.
- File Excel gồm các cột:
  - `category`, `subject`, `question_type`, `question_text`, `choice_1`...`choice_4`, `correct_choice`
- Quy trình:
  1. Upload file.
  2. Preview dữ liệu.
  3. Xác nhận import.
  4. Validate và insert vào DB.

## 5. Giao diện
### 5.1 Admin
- Giao diện tối giản, phân module rõ ràng: Quản lý bài thi, câu hỏi, danh mục, người dùng, gói.
- Có trang import Excel (mẫu file, xem trước dữ liệu).
- Sử dụng Blade template, form đơn giản, thông báo kết quả import.

### 5.2 Frontend
- Giao diện hiện đại, responsive, dễ thao tác.
- Hiển thị danh sách bài thi.
- Khi nhấn “Bắt đầu thi” → kiểm tra lượt thi (AJAX).
- Khi thi:
  - Hiển thị câu hỏi, lựa chọn đáp án.
  - Autosave định kỳ qua AJAX.
  - Đồng hồ đếm ngược.
- Khi hết giờ hoặc submit → lưu kết quả, chấm điểm, hiển thị kết quả.

## 6. Logic chính
- Khi user đăng ký: `free_slots = 2`.
- Khi bắt đầu thi: kiểm tra số lượt, tạo `exam_attempt`, giảm lượt tương ứng.
- Mua gói: cộng thêm lượt, gia hạn thời gian nếu có.
- Một gói có thể có:
  - `attempt_count`
  - `duration_days`
- Khi hết lượt và hết hạn gói → ẩn hoặc disable nút tham gia.

## 7. API / Routes (gợi ý)
- Admin:
  - `GET /admin/exams`
  - `POST /admin/exams/import-questions`
- Frontend:
  - `GET /exams`
  - `POST /exams/{exam}/start`
  - `POST /attempts/{id}/save`
  - `POST /attempts/{id}/submit`

## 8. Testing
- Test khi user mới có thể thi 2 lần free.
- Test import Excel.
- Test logic cộng dồn khi mua gói.
- Test countdown, autosave, submit.

## 9. Ghi chú triển khai
- Không xóa bảng hiện có.
- Kết hợp file `quiz1.sql` để viết migration phù hợp.
- Chạy migration và seed dữ liệu mẫu.
- Đảm bảo transaction khi trừ lượt / tạo attempt.
- AJAX trả JSON, xử lý lỗi UI mượt mà.
