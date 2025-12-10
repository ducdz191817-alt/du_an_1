<?php
require_once BASE_PATH . '/src/models/Tour.php';
require_once BASE_PATH . '/src/models/Category.php';
require_once BASE_PATH . '/src/models/User.php';
require_once BASE_PATH . '/src/models/Booking.php';

class TourController
{
    private $tourModel;
    private $categoryModel;
    private $userModel;
    private $bookingModel;
    
    public function __construct()
    {
        $this->tourModel = new Tour(getDB());
        $this->categoryModel = new Category(getDB());
        $this->userModel = new User(getDB());
        $this->bookingModel = new Booking(getDB());
    }
    
    // Danh sách tour
    public function index()
    {
        requireAdmin();
        
        $filters = [
            'category_id' => $_GET['category'] ?? null,
            'status' => $_GET['status'] ?? null,
            'search' => $_GET['search'] ?? null,
        ];
        
        $tours = $this->tourModel->getAll($filters);
        $categories = $this->categoryModel->getAllActive();
        
        ob_start();
        include view_path('tours.index');
        $content = ob_get_clean();
        
        view('layouts.AdminLayout', [
            'title' => 'Quản lý Tour',
            'pageTitle' => 'Danh sách Tour',
            'content' => $content,
            'breadcrumb' => [
                ['label' => 'Tour', 'url' => BASE_URL . '?act=tours', 'active' => true],
            ],
            'extraCss' => ['css/dataTables.bootstrap5.min.css'],
            'extraJs' => [
                'js/jquery.dataTables.min.js',
                'js/dataTables.bootstrap5.min.js',
                'js/tours.js'
            ],
        ]);
    }
    
    // Form tạo tour mới
    public function create()
    {
        requireAdmin();
        
        $categories = $this->categoryModel->getAllActive();
        $guides = $this->userModel->getGuides();
        
        ob_start();
        include view_path('tours.create');
        $content = ob_get_clean();
        
        view('layouts.AdminLayout', [
            'title' => 'Thêm Tour mới',
            'pageTitle' => 'Thêm Tour mới',
            'content' => $content,
            'breadcrumb' => [
                ['label' => 'Tour', 'url' => BASE_URL . '?act=tours'],
                ['label' => 'Thêm mới', 'active' => true],
            ],
            'extraJs' => ['js/tour-form.js'],
        ]);
    }
    
    // Xử lý tạo tour
    public function store()
    {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?act=tours');
            exit;
        }
        
        $errors = [];
        
        // Validation
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'category_id' => (int)($_POST['category_id'] ?? 0),
            'description' => trim($_POST['description'] ?? ''),
            'price' => (float)($_POST['price'] ?? 0),
            'status' => (int)($_POST['status'] ?? 1),
            'duration' => trim($_POST['duration'] ?? ''),
            'max_guests' => (int)($_POST['max_guests'] ?? null),
        ];
        
        // Parse JSON fields if provided
        if (!empty($_POST['schedule'])) {
            $data['schedule'] = is_string($_POST['schedule']) ? json_decode($_POST['schedule'], true) : $_POST['schedule'];
        }
        if (!empty($_POST['images'])) {
            $data['images'] = is_string($_POST['images']) ? json_decode($_POST['images'], true) : $_POST['images'];
        }
        if (!empty($_POST['prices'])) {
            $data['prices'] = is_string($_POST['prices']) ? json_decode($_POST['prices'], true) : $_POST['prices'];
        }
        if (!empty($_POST['policies'])) {
            $data['policies'] = is_string($_POST['policies']) ? json_decode($_POST['policies'], true) : $_POST['policies'];
        }
        if (!empty($_POST['suppliers'])) {
            $data['suppliers'] = is_string($_POST['suppliers']) ? json_decode($_POST['suppliers'], true) : $_POST['suppliers'];
        }
        
        // Validate
        if (empty($data['name'])) {
            $errors[] = 'Tên tour là bắt buộc';
        }
        
        if (strlen($data['name']) < 5) {
            $errors[] = 'Tên tour phải có ít nhất 5 ký tự';
        }
        
        if ($data['category_id'] <= 0) {
            $errors[] = 'Vui lòng chọn danh mục';
        }
        
        if ($data['price'] <= 0) {
            $errors[] = 'Giá tour phải lớn hơn 0';
        }
        
        if (empty($errors)) {
            $success = $this->tourModel->create($data);
            if ($success) {
                $_SESSION['success'] = 'Thêm tour thành công!';
                header('Location: ' . BASE_URL . '?act=tours');
                exit;
            } else {
                $errors[] = 'Có lỗi xảy ra khi thêm tour. Vui lòng thử lại.';
            }
        }
        
        // Nếu có lỗi, hiển thị lại form
        $categories = $this->categoryModel->getAllActive();
        $guides = $this->userModel->getGuides();
        
        ob_start();
        include view_path('tours.create');
        $content = ob_get_clean();
        
        view('layouts.AdminLayout', [
            'title' => 'Thêm Tour mới',
            'pageTitle' => 'Thêm Tour mới',
            'content' => $content,
            'breadcrumb' => [
                ['label' => 'Tour', 'url' => BASE_URL . '?act=tours'],
                ['label' => 'Thêm mới', 'active' => true],
            ],
            'extraJs' => ['js/tour-form.js'],
        ]);
    }
    
    // Form sửa tour
    public function edit()
    {
        requireAdmin();
        
        $id = (int)($_GET['id'] ?? 0);
        $tour = $this->tourModel->find($id);
        
        if (!$tour) {
            $_SESSION['error'] = 'Tour không tồn tại!';
            header('Location: ' . BASE_URL . '?act=tours');
            exit;
        }
        
        $categories = $this->categoryModel->getAllActive();
        $guides = $this->userModel->getGuides();
        
        ob_start();
        include view_path('tours.edit');
        $content = ob_get_clean();
        
        view('layouts.AdminLayout', [
            'title' => 'Sửa Tour',
            'pageTitle' => 'Sửa Tour: ' . htmlspecialchars($tour['name']),
            'content' => $content,
            'breadcrumb' => [
                ['label' => 'Tour', 'url' => BASE_URL . '?act=tours'],
                ['label' => 'Sửa tour', 'active' => true],
            ],
            'extraJs' => ['js/tour-form.js'],
        ]);
    }
    
    // Xử lý cập nhật
    public function update()
    {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?act=tours');
            exit;
        }
        
        $id = (int)($_POST['id'] ?? 0);
        $errors = [];
        
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'category_id' => (int)($_POST['category_id'] ?? 0),
            'description' => trim($_POST['description'] ?? ''),
            'price' => (float)($_POST['price'] ?? 0),
            'status' => (int)($_POST['status'] ?? 1),
            'duration' => trim($_POST['duration'] ?? ''),
            'max_guests' => (int)($_POST['max_guests'] ?? null),
        ];
        
        // Parse JSON fields if provided
        if (!empty($_POST['schedule'])) {
            $data['schedule'] = is_string($_POST['schedule']) ? json_decode($_POST['schedule'], true) : $_POST['schedule'];
        }
        if (!empty($_POST['images'])) {
            $data['images'] = is_string($_POST['images']) ? json_decode($_POST['images'], true) : $_POST['images'];
        }
        if (!empty($_POST['prices'])) {
            $data['prices'] = is_string($_POST['prices']) ? json_decode($_POST['prices'], true) : $_POST['prices'];
        }
        if (!empty($_POST['policies'])) {
            $data['policies'] = is_string($_POST['policies']) ? json_decode($_POST['policies'], true) : $_POST['policies'];
        }
        if (!empty($_POST['suppliers'])) {
            $data['suppliers'] = is_string($_POST['suppliers']) ? json_decode($_POST['suppliers'], true) : $_POST['suppliers'];
        }
        
        // Validation
        if (empty($data['name'])) {
            $errors[] = 'Tên tour là bắt buộc';
        }
        
        if (strlen($data['name']) < 5) {
            $errors[] = 'Tên tour phải có ít nhất 5 ký tự';
        }
        
        if ($data['category_id'] <= 0) {
            $errors[] = 'Vui lòng chọn danh mục';
        }
        
        if ($data['price'] <= 0) {
            $errors[] = 'Giá tour phải lớn hơn 0';
        }
        
        if (empty($errors)) {
            $success = $this->tourModel->update($id, $data);
            if ($success) {
                $_SESSION['success'] = 'Cập nhật tour thành công!';
                header('Location: ' . BASE_URL . '?act=tours');
                exit;
            } else {
                $errors[] = 'Có lỗi xảy ra khi cập nhật. Vui lòng thử lại.';
            }
        }
        
        // Nếu có lỗi, hiển thị lại form
        $tour = $this->tourModel->find($id);
        $categories = $this->categoryModel->getAllActive();
        $guides = $this->userModel->getGuides();
        
        ob_start();
        include view_path('tours.edit');
        $content = ob_get_clean();
        
        view('layouts.AdminLayout', [
            'title' => 'Sửa Tour',
            'pageTitle' => 'Sửa Tour: ' . htmlspecialchars($tour['name']),
            'content' => $content,
            'breadcrumb' => [
                ['label' => 'Tour', 'url' => BASE_URL . '?act=tours'],
                ['label' => 'Sửa tour', 'active' => true],
            ],
            'extraJs' => ['js/tour-form.js'],
        ]);
    }
    
    // Xóa tour
    public function delete()
    {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?act=tours');
            exit;
        }
        
        $id = (int)($_POST['id'] ?? 0);
        
        // Kiểm tra xem tour có booking nào không
        if ($this->tourModel->hasBookings($id)) {
            $_SESSION['error'] = 'Không thể xóa tour vì đã có booking!';
            header('Location: ' . BASE_URL . '?act=tours');
            exit;
        }
        
        $success = $this->tourModel->delete($id);
        
        if ($success) {
            $_SESSION['success'] = 'Xóa tour thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa tour';
        }
        
        header('Location: ' . BASE_URL . '?act=tours');
        exit;
    }
    
    // Chi tiết tour
    public function show()
    {
        requireAdmin();
        
        $id = (int)($_GET['id'] ?? 0);
        $tour = $this->tourModel->find($id);
        
        if (!$tour) {
            $_SESSION['error'] = 'Tour không tồn tại!';
            header('Location: ' . BASE_URL . '?act=tours');
            exit;
        }
        
        // Lấy danh sách booking của tour này
        $bookings = $this->tourModel->getBookings($id);
        
        ob_start();
        include view_path('tours.show');
        $content = ob_get_clean();
        
        view('layouts.AdminLayout', [
            'title' => 'Chi tiết Tour',
            'pageTitle' => 'Chi tiết Tour: ' . htmlspecialchars($tour['name']),
            'content' => $content,
            'breadcrumb' => [
                ['label' => 'Tour', 'url' => BASE_URL . '?act=tours'],
                ['label' => 'Chi tiết', 'active' => true],
            ],
        ]);
    }
    
    // API: Lấy danh sách tour cho select
    public function apiList()
    {
        requireAdmin();
        
        $tours = $this->tourModel->getAll(['status' => 1]); // Chỉ lấy tour active
        
        header('Content-Type: application/json');
        echo json_encode($tours);
        exit;
    }
}
?>