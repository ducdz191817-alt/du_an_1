<?php
require_once BASE_PATH . '/src/models/Customer.php';

class CustomerController
{
    private $customerModel;

    public function __construct()
    {
        $this->customerModel = new Customer(getDB());
    }

    // Danh sách khách hàng
    public function index()
    {
        requireAdmin();

        $filters = [
            'status' => $_GET['status'] ?? '',
            'search' => $_GET['search'] ?? '',
        ];

        $customers = $this->customerModel->getAll($filters);

        ob_start();
        include view_path('customers.index');
        $content = ob_get_clean();

        view('layouts.AdminLayout', [
            'title' => 'Quản lý Khách hàng',
            'pageTitle' => 'Danh sách Khách hàng',
            'content' => $content,
            'breadcrumb' => [
                ['label' => 'Khách hàng', 'url' => BASE_URL . '?act=customers', 'active' => true],
            ],
        ]);
    }

    // Form tạo khách hàng mới
    public function create()
    {
        requireAdmin();

        ob_start();
        include view_path('customers.create');
        $content = ob_get_clean();

        view('layouts.AdminLayout', [
            'title' => 'Thêm Khách hàng mới',
            'pageTitle' => 'Thêm Khách hàng mới',
            'content' => $content,
            'breadcrumb' => [
                ['label' => 'Khách hàng', 'url' => BASE_URL . '?act=customers'],
                ['label' => 'Thêm mới', 'active' => true],
            ],
        ]);
    }

    // Xử lý tạo khách hàng
    public function store()
    {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?act=customers');
            exit;
        }

        $errors = [];

        // Validation
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'company' => trim($_POST['company'] ?? ''),
            'bus_code' => trim($_POST['bus_code'] ?? ''),
            'notes' => trim($_POST['notes'] ?? ''),
            'status' => $_POST['status'] ?? 'active',
        ];

        // Validate
        if (empty($data['name'])) {
            $errors[] = 'Tên khách hàng là bắt buộc';
        }

        if (strlen($data['name']) < 2) {
            $errors[] = 'Tên khách hàng phải có ít nhất 2 ký tự';
        }

        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ';
        }

        if (empty($errors)) {
            $success = $this->customerModel->create($data);
            if ($success) {
                $_SESSION['success'] = 'Thêm khách hàng thành công!';
                header('Location: ' . BASE_URL . '?act=customers');
                exit;
            } else {
                $errors[] = 'Có lỗi xảy ra khi thêm khách hàng. Vui lòng thử lại.';
            }
        }

        // Nếu có lỗi, hiển thị lại form
        ob_start();
        include view_path('customers.create');
        $content = ob_get_clean();

        view('layouts.AdminLayout', [
            'title' => 'Thêm Khách hàng mới',
            'pageTitle' => 'Thêm Khách hàng mới',
            'content' => $content,
            'breadcrumb' => [
                ['label' => 'Khách hàng', 'url' => BASE_URL . '?act=customers'],
                ['label' => 'Thêm mới', 'active' => true],
            ],
        ]);
    }

    // Form sửa khách hàng
    public function edit()
    {
        requireAdmin();

        $id = (int)($_GET['id'] ?? 0);
        $customer = $this->customerModel->find($id);

        if (!$customer) {
            $_SESSION['error'] = 'Khách hàng không tồn tại!';
            header('Location: ' . BASE_URL . '?act=customers');
            exit;
        }

        ob_start();
        include view_path('customers.edit');
        $content = ob_get_clean();

        view('layouts.AdminLayout', [
            'title' => 'Sửa Khách hàng',
            'pageTitle' => 'Sửa Khách hàng: ' . htmlspecialchars($customer['name']),
            'content' => $content,
            'breadcrumb' => [
                ['label' => 'Khách hàng', 'url' => BASE_URL . '?act=customers'],
                ['label' => 'Sửa khách hàng', 'active' => true],
            ],
        ]);
    }

    // Xử lý cập nhật
    public function update()
    {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?act=customers');
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        $errors = [];

        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'company' => trim($_POST['company'] ?? ''),
            'bus_code' => trim($_POST['bus_code'] ?? ''),
            'notes' => trim($_POST['notes'] ?? ''),
            'status' => $_POST['status'] ?? 'active',
        ];

        // Validation
        if (empty($data['name'])) {
            $errors[] = 'Tên khách hàng là bắt buộc';
        }

        if (strlen($data['name']) < 2) {
            $errors[] = 'Tên khách hàng phải có ít nhất 2 ký tự';
        }

        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ';
        }

        if (empty($errors)) {
            $success = $this->customerModel->update($id, $data);
            if ($success) {
                $_SESSION['success'] = 'Cập nhật khách hàng thành công!';
                header('Location: ' . BASE_URL . '?act=customers');
                exit;
            } else {
                $errors[] = 'Có lỗi xảy ra khi cập nhật. Vui lòng thử lại.';
            }
        }

        // Nếu có lỗi, hiển thị lại form
        $customer = $this->customerModel->find($id);

        ob_start();
        include view_path('customers.edit');
        $content = ob_get_clean();

        view('layouts.AdminLayout', [
            'title' => 'Sửa Khách hàng',
            'pageTitle' => 'Sửa Khách hàng: ' . htmlspecialchars($customer['name']),
            'content' => $content,
            'breadcrumb' => [
                ['label' => 'Khách hàng', 'url' => BASE_URL . '?act=customers'],
                ['label' => 'Sửa khách hàng', 'active' => true],
            ],
        ]);
    }

    // Xóa khách hàng
    public function delete()
    {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?act=customers');
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);

        // Kiểm tra xem khách hàng có booking nào không
        if ($this->customerModel->hasBookings($id)) {
            $_SESSION['error'] = 'Không thể xóa khách hàng vì đã có booking!';
            header('Location: ' . BASE_URL . '?act=customers');
            exit;
        }

        $success = $this->customerModel->delete($id);

        if ($success) {
            $_SESSION['success'] = 'Xóa khách hàng thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa khách hàng';
        }

        header('Location: ' . BASE_URL . '?act=customers');
        exit;
    }
}
?>
