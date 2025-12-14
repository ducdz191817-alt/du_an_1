# PHÂN TÍCH DỰ ÁN - WEBSITE QUẢN LÝ TOUR

## 📋 TỔNG QUAN DỰ ÁN

**Tên dự án:** Website Quản Lý Tour  
**Ngôn ngữ:** PHP (Native, không dùng framework)  
**Kiến trúc:** MVC tối giản (Mô phỏng)  
**Mục đích:** Dự án học tập, quản lý tour du lịch

---

## 🏗️ CẤU TRÚC DỰ ÁN

### 1. Cấu trúc thư mục
```
du_an_1n/
├── config/              # Cấu hình
│   └── config.php       # Cấu hình DB, BASE_URL, BASE_PATH
├── src/
│   ├── controllers/     # Xử lý logic nghiệp vụ
│   │   ├── AuthController.php
│   │   ├── HomeController.php
│   │   └── DashboardController.php
│   ├── models/          # Đại diện dữ liệu
│   │   └── User.php
│   └── helpers/         # Hàm tiện ích
│       ├── helpers.php  # View, session, auth helpers
│       └── database.php # Kết nối DB (PDO singleton)
├── views/               # Giao diện
│   ├── layouts/         # Layout chung
│   │   ├── AdminLayout.php
│   │   ├── AuthLayout.php
│   │   └── blocks/      # Header, Footer, Aside
│   ├── auth/            # Trang đăng nhập
│   ├── home.php
│   ├── welcome.php
│   └── not_found.php
├── public/              # Tài nguyên tĩnh
│   ├── css/
│   ├── js/
│   └── dist/            # AdminLTE assets
├── index.php            # Entry point, routing
├── tour.sql             # Database schema
└── README.md
```

### 2. Kiến trúc MVC

**Model (src/models/):**
- `User.php`: Model đại diện cho người dùng
  - CRUD operations (save, delete, find)
  - Kiểm tra role (isAdmin, isGuide)
  - Validation (existsByEmail)

**View (views/):**
- Layout system với Output Buffering
- Tách biệt layout (AdminLayout, AuthLayout)
- Block system (header, footer, aside)
- Sử dụng dot notation: `view('auth.login')` → `views/auth/login.php`

**Controller (src/controllers/):**
- `HomeController`: Trang chủ, welcome, 404
- `AuthController`: Đăng nhập, đăng xuất
- `DashboardController`: Dashboard admin (thống kê)

**Routing:**
- Sử dụng `match` expression trong `index.php`
- Query parameter `?act=` để định tuyến
- Ví dụ: `?act=login`, `?act=home`, `?act=dashboard`

---

## 🛠️ CÔNG NGHỆ SỬ DỤNG

### Backend
- **PHP 8+** (sử dụng match expression)
- **PDO** (Prepared Statements, bảo mật SQL injection)
- **Session** (quản lý authentication)
- **Password hashing** (bcrypt)

### Frontend
- **AdminLTE 3** (Admin dashboard template)
- **Bootstrap 5.3.3**
- **Bootstrap Icons**
- **OverlayScrollbars**

### Database
- **MySQL/MariaDB**
- Charset: `utf8mb4`
- Foreign keys với constraints

---

## 📊 CẤU TRÚC DATABASE

### Bảng chính:

1. **users** - Người dùng hệ thống
   - Roles: `admin`, `guide`
   - Status: active/inactive

2. **tours** - Tour du lịch
   - Lưu JSON: schedule, images, prices, policies, suppliers
   - Liên kết với categories

3. **bookings** - Đặt tour
   - Status: 1 (Chờ xác nhận), 2 (Đã cọc), 3 (Hoàn tất), 4 (Hủy)
   - Lưu JSON: schedule_detail, service_detail, diary, lists_file
   - Liên kết: tour_id, created_by, assigned_guide_id

4. **customers** - Khách hàng
   - Thông tin liên hệ, công ty, mã số thuế

5. **guide_profiles** - Hồ sơ hướng dẫn viên
   - Thông tin chi tiết: ngày sinh, avatar, chứng chỉ, ngôn ngữ, kinh nghiệm

6. **categories** - Danh mục tour
7. **tour_statuses** - Trạng thái booking
8. **booking_status_logs** - Lịch sử thay đổi trạng thái
9. **tour_guests** - Danh sách khách trong tour

### Quan hệ:
- Foreign keys được thiết lập đầy đủ
- Cascade constraints
- Indexes trên các cột thường query

---

## ✅ ĐIỂM MẠNH

### 1. Bảo mật
- ✅ Sử dụng Prepared Statements (chống SQL injection)
- ✅ Password hashing với bcrypt
- ✅ Session management
- ✅ Role-based access control (admin, guide)
- ✅ Middleware functions (requireLogin, requireAdmin)

### 2. Code Organization
- ✅ Tách biệt rõ ràng: Model, View, Controller
- ✅ Helper functions tái sử dụng
- ✅ Layout system linh hoạt
- ✅ Singleton pattern cho DB connection

### 3. Database Design
- ✅ Normalized structure
- ✅ Foreign keys và constraints
- ✅ Indexes cho performance
- ✅ JSON fields cho dữ liệu linh hoạt

### 4. User Experience
- ✅ Responsive design (AdminLTE)
- ✅ Breadcrumb navigation
- ✅ Error handling (404 page)
- ✅ Redirect sau login theo role

---

## ⚠️ VẤN ĐỀ VÀ LỖI CẦN SỬA

### 🔴 LỖI NGHIÊM TRỌNG (DashboardController.php)

1. **Lỗi cú pháp - Thiếu dấu phẩy (dòng 25)**
   ```php
   'revenue_this_month' => 0
   'revenue_last_month' => 0  // ❌ Thiếu dấu phẩy
   ```

2. **Lỗi đánh máy - `featch()` thay vì `fetch()`** (nhiều chỗ)
   ```php
   $row = $stmt->featch();  // ❌ Sai
   $row = $stmt->fetch();   // ✅ Đúng
   ```
   - Dòng 31, 36, 40, 46, 62, 67, 72

3. **Lỗi đánh máy - `pepare()` thay vì `prepare()`** (dòng 55)
   ```php
   $stmt = $pdo->pepare(...);  // ❌ Sai
   $stmt = $pdo->prepare(...); // ✅ Đúng
   ```

4. **Lỗi đánh máy - `service_deltail` thay vì `service_detail`** (dòng 87, 107, 128)
   ```php
   $service = json_decode($row['service_deltail'], true);  // ❌ Sai
   $service = json_decode($row['service_detail'], true);   // ✅ Đúng
   ```

5. **Lỗi cú pháp - Thiếu dấu chấm phẩy (dòng 122)**
   ```php
   AND DATE(created_at) = :today
   ")  // ❌ Thiếu dấu chấm phẩy
   ```

6. **Lỗi cú pháp - `id()` thay vì `if()`** (dòng 190)
   ```php
   id($pdo === null){  // ❌ Sai
   if($pdo === null){  // ✅ Đúng
   ```

7. **Lỗi cú pháp - Thiếu `$` trước biến** (dòng 200)
   ```php
   for(i =11; $i >= 0; $i--){  // ❌ Thiếu $ trước i
   for($i = 11; $i >= 0; $i--){ // ✅ Đúng
   ```

8. **Lỗi logic - Query sai bảng** (dòng 44)
   ```php
   $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE status = 1");
   // ❌ Đang đếm users thay vì categories
   // ✅ Nên là: SELECT COUNT(*) FROM categories WHERE status = 1
   ```

9. **Lỗi logic - Gán sai biến** (dòng 46)
   ```php
   $stats['total_guides'] = (int)($row['count'] ?? 0);
   // ❌ Đang gán vào total_guides
   // ✅ Nên là: $stats['total_categories'] = ...
   ```

10. **Lỗi đánh máy - `completted` thay vì `completed`** (dòng 23)
    ```php
    'completted_bookings' => 0  // ❌ Sai
    'completed_bookings' => 0   // ✅ Đúng
    ```

11. **Lỗi cú pháp - `json_decode()` thay vì `json_encode()`** (dòng 192, 225)
    ```php
    echo json_decode(['error' => '...']);  // ❌ Sai
    echo json_encode(['error' => '...']);  // ✅ Đúng
    ```

12. **Lỗi logic - Tên cột sai** (dòng 121, 208)
    ```php
    DATE(created_at)  // ❌ Cột là created_at
    DATE(create_at)   // ✅ Đúng theo schema (nhưng nên kiểm tra lại)
    ```

13. **Lỗi logic - Thiếu dấu chấm phẩy** (dòng 122)
    ```php
    AND DATE(created_at) = :today
    ")  // ❌ Thiếu dấu chấm phẩy trước dấu ngoặc
    ```

14. **View path không tồn tại** (dòng 172)
    ```php
    include view_path('admin.dashboard.index');
    // ❌ File không tồn tại: views/admin/dashboard/index.php
    ```

### 🟡 VẤN ĐỀ CẢI THIỆN

1. **Routing**
   - Chưa có `.htaccess` để rewrite URL (theo README có đề cập)
   - Phụ thuộc vào query parameter `?act=`

2. **Error Handling**
   - Chưa có try-catch cho database operations
   - Chưa có error logging system
   - Chưa có custom error pages (500, 403)

3. **Security**
   - Chưa có CSRF protection
   - Chưa có rate limiting cho login
   - Chưa validate input đầy đủ (email format, password strength)

4. **Code Quality**
   - Chưa có autoloader (phải require thủ công)
   - Chưa có namespace
   - Chưa có type hints đầy đủ
   - Magic numbers (status: 1, 2, 3, 4) nên dùng constants

5. **Database**
   - JSON fields khó query và index
   - Chưa có migration system
   - Chưa có seeders

6. **Testing**
   - Chưa có unit tests
   - Chưa có integration tests

7. **Documentation**
   - Chưa có API documentation
   - Chưa có code comments đầy đủ

8. **Performance**
   - Chưa có caching
   - Chưa có query optimization
   - N+1 query problem có thể xảy ra

---

## 🔧 KHUYẾN NGHỊ CẢI THIỆN

### Ưu tiên cao (Sửa lỗi)

1. **Sửa tất cả lỗi trong DashboardController.php**
   - Sửa lỗi đánh máy: `featch()` → `fetch()`
   - Sửa lỗi cú pháp: thiếu dấu phẩy, chấm phẩy
   - Sửa lỗi logic: query sai bảng, gán sai biến

2. **Tạo view cho dashboard**
   - Tạo file `views/admin/dashboard/index.php`
   - Hoặc sửa lại view path

3. **Đăng ký route cho dashboard**
   - Thêm vào `index.php`: `'dashboard' => $dashboardController->index()`

### Ưu tiên trung bình (Cải thiện)

1. **Thêm .htaccess cho URL rewriting**
   ```apache
   RewriteEngine On
   RewriteCond %{REQUEST_FILENAME} !-f
   RewriteCond %{REQUEST_FILENAME} !-d
   RewriteRule ^(.*)$ index.php?act=$1 [L,QSA]
   ```

2. **Thêm constants cho status**
   ```php
   class BookingStatus {
       const PENDING = 1;
       const DEPOSITED = 2;
       const COMPLETED = 3;
       const CANCELLED = 4;
   }
   ```

3. **Thêm error handling**
   - Try-catch cho database operations
   - Error logging
   - User-friendly error messages

4. **Thêm validation**
   - Email format validation
   - Password strength requirements
   - Input sanitization

### Ưu tiên thấp (Tối ưu)

1. **Autoloader (PSR-4)**
2. **Namespace**
3. **Unit tests**
4. **Caching system**
5. **API documentation**

---

## 📈 ĐÁNH GIÁ TỔNG QUAN

### Điểm mạnh: 7/10
- ✅ Cấu trúc rõ ràng, dễ hiểu
- ✅ Bảo mật cơ bản tốt
- ✅ Database design hợp lý
- ✅ Code organization tốt

### Điểm yếu: 4/10
- ❌ Nhiều lỗi syntax trong DashboardController
- ❌ Chưa có error handling đầy đủ
- ❌ Chưa có testing
- ❌ Chưa có documentation đầy đủ

### Tổng kết: 5.5/10

**Dự án phù hợp cho:**
- ✅ Học tập và thực hành PHP
- ✅ Hiểu rõ MVC pattern
- ✅ Làm việc với database và authentication

**Cần cải thiện:**
- 🔧 Sửa lỗi syntax và logic
- 🔧 Thêm error handling
- 🔧 Cải thiện security
- 🔧 Thêm testing

---

## 📝 KẾT LUẬN

Đây là một dự án PHP học tập tốt với cấu trúc MVC rõ ràng. Tuy nhiên, có nhiều lỗi syntax và logic trong `DashboardController.php` cần được sửa ngay. Sau khi sửa các lỗi, dự án sẽ sẵn sàng để phát triển thêm các tính năng.

**Hành động tiếp theo:**
1. Sửa tất cả lỗi trong DashboardController.php
2. Tạo view cho dashboard
3. Đăng ký route cho dashboard
4. Test các chức năng

---

*Phân tích được tạo vào: 2025-12-08*

