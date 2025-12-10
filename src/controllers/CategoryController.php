<?php
require_once BASE_PATH . '/src/models/Category.php';
require_once BASE_PATH . '/src/models/Tour.php';

class CategoryController
{
    private $categoryModel;
    private $tourModel;
    
    public function __construct()
    {
        $this->categoryModel = new Category(getDB());
        $this->tourModel = new Tour(getDB());
    }
    
    // Danh sách danh mục
    public function index()
    {
        requireAdmin();
        
        $categories = $this->categoryModel->getAll();
        
        // Đếm số tour cho mỗi danh mục và chuyển đổi status
        foreach ($categories as &$category) {
            $category['tour_count'] = $this->categoryModel->countTours($category['id']);
            // Chuyển đổi status từ int sang string cho view
            $category['status'] = ($category['status'] == 1) ? 'active' : 'inactive';
        }
        unset($category);
        
        ob_start();
        include view_path('categories.index');
        $content = ob_get_clean();
        
        view('layouts.AdminLayout', [
            'title' => 'Quản lý Danh mục Tour',
            'pageTitle' => 'Danh sách Danh mục',
            'content' => $content,
            'breadcrumb' => [
                ['label' => 'Danh mục', 'url' => BASE_URL . '?act=categories', 'active' => true],
            ],
        ]);
    }
    
    // Form tạo danh mục mới
    public function create()
    {
        requireAdmin();
        
        $errors = [];
        
        ob_start();
        include view_path('categories.create');
        $content = ob_get_clean();
        
        view('layouts.AdminLayout', [
            'title' => 'Thêm Danh mục mới',
            'pageTitle' => 'Thêm Danh mục mới',
            'content' => $content,
            'breadcrumb' => [
                ['label' => 'Danh mục', 'url' => BASE_URL . '?act=categories'],
                ['label' => 'Thêm mới', 'active' => true],
            ],
        ]);
    }
    
    // Xử lý tạo danh mục
    public function store()
    {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?act=categories');
            exit;
        }
        
        $errors = [];
        
        // Validation
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'status' => $_POST['status'] ?? 'active',
        ];
        
        // Validate
        if (empty($data['name'])) {
            $errors[] = 'Tên danh mục là bắt buộc';
        }
        
        if (strlen($data['name']) < 3) {
            $errors[] = 'Tên danh mục phải có ít nhất 3 ký tự';
        }
        
        if (empty($errors)) {
            $success = $this->categoryModel->create($data);
            if ($success) {
                $_SESSION['success'] = 'Thêm danh mục thành công!';
                header('Location: ' . BASE_URL . '?act=categories');
                exit;
            } else {
                $errors[] = 'Có lỗi xảy ra khi thêm danh mục. Vui lòng thử lại.';
            }
        }
        
        // Nếu có lỗi, hiển thị lại form
        ob_start();
        include view_path('categories.create');
        $content = ob_get_clean();
        
        view('layouts.AdminLayout', [
            'title' => 'Thêm Danh mục mới',
            'pageTitle' => 'Thêm Danh mục mới',
            'content' => $content,
            'breadcrumb' => [
                ['label' => 'Danh mục', 'url' => BASE_URL . '?act=categories'],
                ['label' => 'Thêm mới', 'active' => true],
            ],
        ]);
    }
    
    // Form sửa danh mục
    public function edit()
    {
        requireAdmin();
        
        $id = (int)($_GET['id'] ?? 0);
        $category = $this->categoryModel->find($id);
        
        if (!$category) {
            $_SESSION['error'] = 'Danh mục không tồn tại!';
            header('Location: ' . BASE_URL . '?act=categories');
            exit;
        }
        
        // Đếm số tour trong danh mục
        $category['tour_count'] = $this->categoryModel->countTours($id);
        // Chuyển đổi status từ int sang string cho view
        $category['status'] = ($category['status'] == 1) ? 'active' : 'inactive';
        $errors = [];
        
        ob_start();
        include view_path('categories.edit');
        $content = ob_get_clean();
        
        view('layouts.AdminLayout', [
            'title' => 'Sửa Danh mục',
            'pageTitle' => 'Sửa Danh mục: ' . htmlspecialchars($category['name']),
            'content' => $content,
            'breadcrumb' => [
                ['label' => 'Danh mục', 'url' => BASE_URL . '?act=categories'],
                ['label' => 'Sửa danh mục', 'active' => true],
            ],
        ]);
    }
    
    // Xử lý cập nhật
    public function update()
    {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?act=categories');
            exit;
        }
        
        $id = (int)($_POST['id'] ?? 0);
        $errors = [];
        
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'status' => $_POST['status'] ?? 'active',
        ];
        
        // Validation
        if (empty($data['name'])) {
            $errors[] = 'Tên danh mục là bắt buộc';
        }
        
        if (strlen($data['name']) < 3) {
            $errors[] = 'Tên danh mục phải có ít nhất 3 ký tự';
        }
        
        if (empty($errors)) {
            $success = $this->categoryModel->update($id, $data);
            if ($success) {
                $_SESSION['success'] = 'Cập nhật danh mục thành công!';
                header('Location: ' . BASE_URL . '?act=categories');
                exit;
            } else {
                $errors[] = 'Có lỗi xảy ra khi cập nhật. Vui lòng thử lại.';
            }
        }
        
        // Nếu có lỗi, hiển thị lại form
        $category = $this->categoryModel->find($id);
        if (!$category) {
            $_SESSION['error'] = 'Danh mục không tồn tại!';
            header('Location: ' . BASE_URL . '?act=categories');
            exit;
        }
        // Giữ lại giá trị POST khi có lỗi validation
        $category['name'] = $data['name'];
        $category['description'] = $data['description'];
        $category['status'] = $data['status'];
        $category['tour_count'] = $this->categoryModel->countTours($id);
        
        ob_start();
        include view_path('categories.edit');
        $content = ob_get_clean();
        
        view('layouts.AdminLayout', [
            'title' => 'Sửa Danh mục',
            'pageTitle' => 'Sửa Danh mục: ' . htmlspecialchars($category['name']),
            'content' => $content,
            'breadcrumb' => [
                ['label' => 'Danh mục', 'url' => BASE_URL . '?act=categories'],
                ['label' => 'Sửa danh mục', 'active' => true],
            ],
        ]);
    }
    
    // Xóa danh mục
    public function delete()
    {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?act=categories');
            exit;
        }
        
        $id = (int)($_POST['id'] ?? 0);
        
        // Kiểm tra xem danh mục có tour nào không
        if ($this->categoryModel->hasTours($id)) {
            $_SESSION['error'] = 'Không thể xóa danh mục vì đã có tour thuộc danh mục này!';
            header('Location: ' . BASE_URL . '?act=categories');
            exit;
        }
        
        $success = $this->categoryModel->delete($id);
        
        if ($success) {
            $_SESSION['success'] = 'Xóa danh mục thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa danh mục';
        }
        
        header('Location: ' . BASE_URL . '?act=categories');
        exit;
    }
}
?>