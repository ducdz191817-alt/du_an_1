<?php

// Nạp cấu hình chung của ứng dụng
$config = require __DIR__ . '/config/config.php';

// Nạp các file chứa hàm trợ giúp
require_once __DIR__ . '/src/helpers/helpers.php'; // Helper chứa các hàm trợ giúp (hàm xử lý view, block, asset, session, ...)
require_once __DIR__ . '/src/helpers/database.php'; // Helper kết nối database(kết nối với cơ sở dữ liệu)

// Nạp các file chứa model
require_once __DIR__ . '/src/models/User.php';
require_once __DIR__ . '/src/models/Tour.php';
require_once __DIR__ . '/src/models/Booking.php';
require_once __DIR__ . '/src/models/Customer.php';
require_once __DIR__ . '/src/models/GuideProfile.php';
require_once __DIR__ . '/src/models/Category.php';

// Nạp các file chứa controller
require_once __DIR__ . '/src/controllers/HomeController.php';
require_once __DIR__ . '/src/controllers/AuthController.php';
require_once __DIR__ . '/src/controllers/DashboardController.php';
require_once __DIR__ . '/src/controllers/TourController.php';
require_once __DIR__ . '/src/controllers/BookingController.php';
require_once __DIR__ . '/src/controllers/CustomerController.php';
require_once __DIR__ . '/src/controllers/GuideProfileController.php';
require_once __DIR__ . '/src/controllers/CategoryController.php';
require_once __DIR__ . '/src/controllers/UserController.php';

// Khởi tạo các controller
$homeController = new HomeController();
$authController = new AuthController();
$dashboardController = new DashboardController();
$tourController = new TourController();
$bookingController = new BookingController();
$customerController = new CustomerController();
$guideProfileController = new GuideProfileController();
$categoryController = new CategoryController();
$userController = new UserController();

// Xác định route dựa trên tham số act (mặc định là trang chủ '/')
$act = $_GET['act'] ?? '/';

// Match đảm bảo chỉ một action tương ứng được gọi
match ($act) {
    // Trang welcome (cho người chưa đăng nhập) - mặc định khi truy cập '/'
    '/', 'welcome' => $homeController->welcome(),

    // Trang home (cho người đã đăng nhập)
    'home' => $homeController->home(),

    // Đường dẫn đăng nhập, đăng xuất
    'login' => $authController->login(),
    'check-login' => $authController->checkLogin(),
    'logout' => $authController->logout(),

    // Dashboard admin
    'dashboard' => $dashboardController->index(),
    'dashboard-revenue-data' => $dashboardController->revenueData(),

    // Quản lý Danh mục Tour
    'categories' => $categoryController->index(),
    'category-create' => $categoryController->create(),
    'category-store' => $categoryController->store(),
    'category-edit' => $categoryController->edit(),
    'category-update' => $categoryController->update(),
    'category-delete' => $categoryController->delete(),

    // Quản lý Tour
    'tours' => $tourController->index(),
    'tour-create' => $tourController->create(),
    'tour-store' => $tourController->store(),
    'tour-edit' => $tourController->edit(),
    'tour-update' => $tourController->update(),
    'tour-delete' => $tourController->delete(),

    // Quản lý Booking
    'bookings' => $bookingController->index(),
    'booking-create' => $bookingController->create(),
    'booking-store' => $bookingController->store(),
    'booking-show' => $bookingController->show(),
    'booking-update-status' => $bookingController->updateStatus(),
    'booking-delete' => $bookingController->delete(),

    // Quản lý Khách hàng
    'customers' => $customerController->index(),
    'customer-create' => $customerController->create(),
    'customer-store' => $customerController->store(),
    'customer-edit' => $customerController->edit(),
    'customer-update' => $customerController->update(),
    'customer-show' => $customerController->show(),
    'customer-delete' => $customerController->delete(),

    // Quản lý Hồ sơ HDV
    'guide-profiles' => $guideProfileController->index(),
    'guide-profile-create' => $guideProfileController->create(),
    'guide-profile-store' => $guideProfileController->store(),
    'guide-profile-edit' => $guideProfileController->edit(),
    'guide-profile-update' => $guideProfileController->update(),
    'guide-profile-show' => $guideProfileController->show(),
    'guide-profile-delete' => $guideProfileController->delete(),

    // Trang dành cho hướng dẫn viên
    'guide-schedule' => $bookingController->guideSchedule(),
    'guide-history' => $bookingController->guideCustomers(),

    // Quản lý Người dùng (Hướng dẫn viên)
    'users' => $userController->index(),
    'user-create' => $userController->create(),
    'user-store' => $userController->store(),
    'user-edit' => $userController->edit(),
    'user-update' => $userController->update(),
    'user-delete' => $userController->delete(),
    'user-assign-tour' => $userController->assignTour(),
    'user-assign-tour-store' => $userController->assignTourStore(),

    // Đường dẫn không tồn tại
    default => $homeController->notFound(),
};
