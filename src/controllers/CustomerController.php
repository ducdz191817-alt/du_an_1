<?php

class CustomerController
{
    // Hiển thị danh sách khách hàng
    public function index(): void
    {
        requireAdmin();

        $pdo = getDB();
        if ($pdo === null) {
            throw new RuntimeException('Không thể kết nối cơ sở dữ liệu');
        }

        // Lấy tham số filter
        $status = $_GET['status'] ?? null;
        $search = $_GET['search'] ?? '';

        // Xây dựng query
        $where = [];
        $params = [];

        if ($status !== null && $status !== '') {
            $where[] = 'c.status = :status';
            $params[':status'] = $status;
        }

        if (!empty($search)) {
            $where[] = '(c.name LIKE :search1 OR c.phone LIKE :search2 OR c.email LIKE :search3)';
            $params[':search1'] = '%' . $search . '%';
            $params[':search2'] = '%' . $search . '%';
            $params[':search3'] = '%' . $search . '%';
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "
            SELECT c.*,
                   (SELECT COUNT(*) FROM bookings WHERE customer_id = c.id) as booking_count
            FROM customers c
            {$whereClause}
            ORDER BY c.created_at DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $customers = $stmt->fetchAll();

        ob_start();
        include view_path('admin.customers.index');
        $content = ob_get_clean();

        view('layouts.AdminLayout', [
            'title' => 'Danh sách Khách hàng',
            'pageTitle' => 'Danh sách Khách hàng',
            'content' => $content,
            'customers' => $customers,
            'currentStatus' => $status,
            'currentSearch' => $search,
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => BASE_URL . '?act=dashboard'],
                ['label' => 'Danh sách Khách hàng', 'url' => BASE_URL . '?act=customers', 'active' => true],
            ],
        ]);
    }

    // Hiển thị form thêm khách hàng
    public function create(): void
    {
        requireAdmin();

        ob_start();
        include view_path('admin.customers.create');
        $content = ob_get_clean();

        view('layouts.AdminLayout', [
            'title' => 'Thêm Khách hàng mới',
            'pageTitle' => 'Thêm Khách hàng mới',
            'content' => $content,
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => BASE_URL . '?act=dashboard'],
                ['label' => 'Danh sách Khách hàng', 'url' => BASE_URL . '?act=customers'],
                ['label' => 'Thêm Khách hàng mới', 'url' => BASE_URL . '?act=customer-create', 'active' => true],
            ],
        ]);
    }

    // Xử lý lưu khách hàng mới
    public function store(): void
    {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?act=customers');
            exit;
        }

        $errors = [];

        // Validate
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $company = trim($_POST['company'] ?? '');
        $taxCode = trim($_POST['tax_code'] ?? '');
        $dateOfBirth = $_POST['date_of_birth'] ?? null;
        $gender = $_POST['gender'] ?? null;
        $nationality = trim($_POST['nationality'] ?? '');
        $passportNumber = trim($_POST['passport_number'] ?? '');
        $passportExpiry = $_POST['passport_expiry'] ?? null;
        $notes = trim($_POST['notes'] ?? '');
        $status = $_POST['status'] ?? 1;

        if (empty($name)) {
            $errors[] = 'Vui lòng nhập tên khách hàng';
        }

        if (empty($phone)) {
            $errors[] = 'Vui lòng nhập số điện thoại';
        }

        // Kiểm tra số điện thoại trùng lặp
        if (!empty($phone)) {
            $pdo = getDB();
            $checkStmt = $pdo->prepare('SELECT id FROM customers WHERE phone = :phone');
            $checkStmt->execute([':phone' => $phone]);
            if ($checkStmt->fetch()) {
                $errors[] = 'Số điện thoại này đã được sử dụng';
            }
        }

        // Kiểm tra email trùng lặp (nếu có)
        if (!empty($email)) {
            $pdo = getDB();
            $checkStmt = $pdo->prepare('SELECT id FROM customers WHERE email = :email');
            $checkStmt->execute([':email' => $email]);
            if ($checkStmt->fetch()) {
                $errors[] = 'Email này đã được sử dụng';
            }
        }

        if (!empty($errors)) {
            ob_start();
            include view_path('admin.customers.create');
            $content = ob_get_clean();

            view('layouts.AdminLayout', [
                'title' => 'Thêm Khách hàng mới',
                'pageTitle' => 'Thêm Khách hàng mới',
                'content' => $content,
                'errors' => $errors,
                'old' => $_POST,
                'breadcrumb' => [
                    ['label' => 'Dashboard', 'url' => BASE_URL . '?act=dashboard'],
                    ['label' => 'Danh sách Khách hàng', 'url' => BASE_URL . '?act=customers'],
                    ['label' => 'Thêm Khách hàng mới', 'url' => BASE_URL . '?act=customer-create', 'active' => true],
                ],
            ]);
            return;
        }

        // Tạo customer mới
        $customer = new Customer([
            'name' => $name,
            'phone' => $phone,
            'email' => $email ?: null,
            'address' => $address ?: null,
            'company' => $company ?: null,
            'tax_code' => $taxCode ?: null,
            'date_of_birth' => $dateOfBirth ?: null,
            'gender' => $gender ?: null,
            'nationality' => $nationality ?: null,
            'passport_number' => $passportNumber ?: null,
            'passport_expiry' => $passportExpiry ?: null,
            'notes' => $notes ?: null,
            'status' => (int)$status,
        ]);

        try {
            if ($customer->save()) {
                header('Location: ' . BASE_URL . '?act=customers&success=1');
            } else {
                header('Location: ' . BASE_URL . '?act=customer-create&error=' . urlencode('Không thể lưu khách hàng'));
            }
        } catch (Exception $e) {
            header('Location: ' . BASE_URL . '?act=customer-create&error=' . urlencode($e->getMessage()));
        }
        exit;
    }

    // Hiển thị form sửa khách hàng
    public function edit(): void
    {
        requireAdmin();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . '?act=customers');
            exit;
        }

        $customer = Customer::find($id);
        if (!$customer) {
            header('Location: ' . BASE_URL . '?act=customers&error=' . urlencode('Không tìm thấy khách hàng'));
            exit;
        }

        ob_start();
        include view_path('admin.customers.edit');
        $content = ob_get_clean();

        view('layouts.AdminLayout', [
            'title' => 'Sửa Khách hàng',
            'pageTitle' => 'Sửa Khách hàng',
            'content' => $content,
            'customer' => $customer,
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => BASE_URL . '?act=dashboard'],
                ['label' => 'Danh sách Khách hàng', 'url' => BASE_URL . '?act=customers'],
                ['label' => 'Sửa Khách hàng', 'url' => BASE_URL . '?act=customer-edit&id=' . $id, 'active' => true],
            ],
        ]);
    }

    // Xử lý cập nhật khách hàng
    public function update(): void
    {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?act=customers');
            exit;
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . '?act=customers');
            exit;
        }

        $customer = Customer::find($id);
        if (!$customer) {
            header('Location: ' . BASE_URL . '?act=customers&error=' . urlencode('Không tìm thấy khách hàng'));
            exit;
        }

        $errors = [];

        // Validate
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $company = trim($_POST['company'] ?? '');
        $taxCode = trim($_POST['tax_code'] ?? '');
        $dateOfBirth = $_POST['date_of_birth'] ?? null;
        $gender = $_POST['gender'] ?? null;
        $nationality = trim($_POST['nationality'] ?? '');
        $passportNumber = trim($_POST['passport_number'] ?? '');
        $passportExpiry = $_POST['passport_expiry'] ?? null;
        $notes = trim($_POST['notes'] ?? '');
        $status = $_POST['status'] ?? 1;

        if (empty($name)) {
            $errors[] = 'Vui lòng nhập tên khách hàng';
        }

        if (empty($phone)) {
            $errors[] = 'Vui lòng nhập số điện thoại';
        }

        // Kiểm tra số điện thoại trùng lặp (trừ chính nó)
        if (!empty($phone)) {
            $pdo = getDB();
            $checkStmt = $pdo->prepare('SELECT id FROM customers WHERE phone = :phone AND id != :id');
            $checkStmt->execute([':phone' => $phone, ':id' => $id]);
            if ($checkStmt->fetch()) {
                $errors[] = 'Số điện thoại này đã được sử dụng bởi khách hàng khác';
            }
        }

        // Kiểm tra email trùng lặp (nếu có, trừ chính nó)
        if (!empty($email)) {
            $pdo = getDB();
            $checkStmt = $pdo->prepare('SELECT id FROM customers WHERE email = :email AND id != :id');
            $checkStmt->execute([':email' => $email, ':id' => $id]);
            if ($checkStmt->fetch()) {
                $errors[] = 'Email này đã được sử dụng bởi khách hàng khác';
            }
        }

        if (!empty($errors)) {
            ob_start();
            include view_path('admin.customers.edit');
            $content = ob_get_clean();

            view('layouts.AdminLayout', [
                'title' => 'Sửa Khách hàng',
                'pageTitle' => 'Sửa Khách hàng',
                'content' => $content,
                'customer' => $customer,
                'errors' => $errors,
                'old' => $_POST,
                'breadcrumb' => [
                    ['label' => 'Dashboard', 'url' => BASE_URL . '?act=dashboard'],
                    ['label' => 'Danh sách Khách hàng', 'url' => BASE_URL . '?act=customers'],
                    ['label' => 'Sửa Khách hàng', 'url' => BASE_URL . '?act=customer-edit&id=' . $id, 'active' => true],
                ],
            ]);
            return;
        }

        // Cập nhật customer
        $customer->name = $name;
        $customer->phone = $phone;
        $customer->email = $email ?: null;
        $customer->address = $address ?: null;
        $customer->company = $company ?: null;
        $customer->tax_code = $taxCode ?: null;
        $customer->date_of_birth = $dateOfBirth ?: null;
        $customer->gender = $gender ?: null;
        $customer->nationality = $nationality ?: null;
        $customer->passport_number = $passportNumber ?: null;
        $customer->passport_expiry = $passportExpiry ?: null;
        $customer->notes = $notes ?: null;
        $customer->status = (int)$status;

        try {
            if ($customer->save()) {
                header('Location: ' . BASE_URL . '?act=customers&success=1');
            } else {
                header('Location: ' . BASE_URL . '?act=customer-edit&id=' . $id . '&error=' . urlencode('Không thể cập nhật khách hàng'));
            }
        } catch (Exception $e) {
            header('Location: ' . BASE_URL . '?act=customer-edit&id=' . $id . '&error=' . urlencode($e->getMessage()));
        }
        exit;
    }

    // Hiển thị chi tiết khách hàng
    public function show(): void
    {
        requireAdmin();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . '?act=customers');
            exit;
        }

        $customer = Customer::find($id);
        if (!$customer) {
            header('Location: ' . BASE_URL . '?act=customers&error=' . urlencode('Không tìm thấy khách hàng'));
            exit;
        }

        // Lấy danh sách bookings
        $bookings = $customer->getBookings();

        ob_start();
        include view_path('admin.customers.show');
        $content = ob_get_clean();

        view('layouts.AdminLayout', [
            'title' => 'Chi tiết Khách hàng',
            'pageTitle' => 'Chi tiết Khách hàng',
            'content' => $content,
            'customer' => $customer,
            'bookings' => $bookings,
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => BASE_URL . '?act=dashboard'],
                ['label' => 'Danh sách Khách hàng', 'url' => BASE_URL . '?act=customers'],
                ['label' => 'Chi tiết Khách hàng', 'url' => BASE_URL . '?act=customer-show&id=' . $id, 'active' => true],
            ],
        ]);
    }

    // Xóa khách hàng
    public function delete(): void
    {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?act=customers');
            exit;
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . '?act=customers');
            exit;
        }

        $customer = Customer::find($id);
        if (!$customer) {
            header('Location: ' . BASE_URL . '?act=customers&error=' . urlencode('Không tìm thấy khách hàng'));
            exit;
        }

        try {
            if ($customer->delete()) {
                header('Location: ' . BASE_URL . '?act=customers&success=1');
            } else {
                header('Location: ' . BASE_URL . '?act=customers&error=' . urlencode('Không thể xóa khách hàng'));
            }
        } catch (Exception $e) {
            header('Location: ' . BASE_URL . '?act=customers&error=' . urlencode($e->getMessage()));
        }
        exit;
    }
}

