<?php
// BẮT ĐẦU SESSION TRƯỚC KHI DÙNG
session_start();

// Định nghĩa BASE_PATH - SỬA LẠI CHO ĐÚNG
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__); // Đây sẽ là D:\laragon\www\du_an_1
}

if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/du_an_1/');
}

// Debug: Kiểm tra đường dẫn (tạm thời)
// echo "BASE_PATH: " . BASE_PATH . "<br>";
// exit;

// Nạp cấu hình
$config = require BASE_PATH . '/config/config.php';

// Load helpers - SỬA ĐƯỜNG DẪN Ở ĐÂY
// Dùng BASE_PATH . '/src/helpers/...' thay vì __DIR__
require_once BASE_PATH . '/src/helpers/helpers.php';
require_once BASE_PATH . '/src/helpers/database.php';

// Load models
require_once BASE_PATH . '/src/models/User.php';
require_once BASE_PATH . '/src/models/Tour.php';
require_once BASE_PATH . '/src/models/TourGuest.php';
require_once BASE_PATH . '/src/models/Category.php';
require_once BASE_PATH . '/src/models/Customer.php';
require_once BASE_PATH . '/src/models/GuideProfile.php';
require_once BASE_PATH . '/src/models/Booking.php';
require_once BASE_PATH . '/src/models/BookingStatuslog.php';

// Load controllers
require_once BASE_PATH . '/src/controllers/HomeController.php';
require_once BASE_PATH . '/src/controllers/AuthController.php';
require_once BASE_PATH . '/src/controllers/TourController.php';
require_once BASE_PATH . '/src/controllers/CategoryController.php';
require_once BASE_PATH . '/src/controllers/CustomerController.php';
require_once BASE_PATH . '/src/controllers/GuideController.php';
require_once BASE_PATH . '/src/controllers/BookingController.php';
require_once BASE_PATH . '/src/controllers/DashboardController.php';
require_once BASE_PATH . '/src/controllers/UserController.php';

// Khởi tạo controllers
$homeController = new HomeController();
$authController = new AuthController();
$tourController = new TourController();
$categoryController = new CategoryController();
$dashboardController = new DashboardController();

// Xác định route
$act = $_GET['act'] ?? '/';

match ($act) {
    // Trang cơ bản
    '/', 'welcome' => $homeController->welcome(),
    'home' => $homeController->home(),
    
    // Auth
    'login' => $authController->login(),
    'check-login' => $authController->checkLogin(),
    'logout' => $authController->logout(),
    
    // Quản lý Tour
    'tours' => $tourController->index(),
    'tours/create' => $tourController->create(),
    'tours/store' => $tourController->store(),
    'tours/edit' => $tourController->edit(),
    'tours/update' => $tourController->update(),
    'tours/delete' => $tourController->delete(),
    'tours/show' => $tourController->show(),
    
    // Quản lý Danh mục
    'categories' => $categoryController->index(),
    'categories/create' => $categoryController->create(),
    'categories/store' => $categoryController->store(),
    'categories/edit' => $categoryController->edit(),
    'categories/update' => $categoryController->update(),
    'categories/delete' => $categoryController->delete(),
    
    // Quản lý Khách hàng
    'customers' => $customerController->index(),
    'customers/create' => $customerController->create(),
    'customers/store' => $customerController->store(),
    'customers/edit' => $customerController->edit(),
    'customers/update' => $customerController->update(),
    'customers/delete' => $customerController->delete(),
    
    // Quản lý Hướng dẫn viên
    'guides' => $guideController->index(),
    'guides/create' => $guideController->create(),
    'guides/store' => $guideController->store(),
    'guides/edit' => $guideController->edit(),
    'guides/update' => $guideController->update(),
    'guides/delete' => $guideController->delete(),
    
    // Quản lý Đặt tour
    'bookings' => $bookingController->index(),
    'bookings/create' => $bookingController->create(),
    'bookings/store' => $bookingController->store(),
    'bookings/edit' => $bookingController->edit(),
    'bookings/update' => $bookingController->update(),
    'bookings/delete' => $bookingController->delete(),
    
    // Dashboard
    'dashboard' => $dashboardController->index(),
    
    // Quản lý Users
    'users' => $userController->index(),
    'users/create' => $userController->create(),
    'users/store' => $userController->store(),
    'users/edit' => $userController->edit(),
    'users/update' => $userController->update(),
    'users/delete' => $userController->delete(),
    
    // 404
    default => $homeController->notFound(),
};