<?php

class UserController
{
    // Hiển thị danh sách hướng dẫn viên
    public function index(): void
    {
        requireAdmin();

        $pdo = getDB();
        if ($pdo === null) {
            throw new RuntimeException('Không thể kết nối cơ sở dữ liệu');
        }

        // Lấy tham số filter
        $role = $_GET['role'] ?? 'guide'; // Mặc định chỉ hiển thị guides
        $status = $_GET['status'] ?? null;
        $search = $_GET['search'] ?? '';

        // Xây dựng query
        $where = ['u.role = :role'];
        $params = [':role' => $role];

        if ($status !== null && $status !== '') {
            $where[] = 'u.status = :status';
            $params[':status'] = $status;
        }

        if (!empty($search)) {
            $where[] = '(u.name LIKE :search1 OR u.email LIKE :search2 OR u.phone LIKE :search3)';
            $params[':search1'] = '%' . $search . '%';
            $params[':search2'] = '%' . $search . '%';
            $params[':search3'] = '%' . $search . '%';
        }

        $whereClause = 'WHERE ' . implode(' AND ', $where);

        $sql = "
            SELECT u.*,
                   (SELECT COUNT(*) FROM bookings WHERE assigned_guide_id = u.id) as booking_count,
                   (SELECT COUNT(*) FROM guide_profiles WHERE user_id = u.id) as has_profile
            FROM users u
            {$whereClause}
            ORDER BY u.created_at DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $users = $stmt->fetchAll();

        ob_start();
        include view_path('admin.users.index');
        $content = ob_get_clean();

        view('layouts.AdminLayout', [
            'title' => 'Quản lý Hướng dẫn viên',
            'pageTitle' => 'Quản lý Hướng dẫn viên',
            'content' => $content,
            'users' => $users,
            'currentRole' => $role,
            'currentStatus' => $status,
            'currentSearch' => $search,
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => BASE_URL . '?act=dashboard'],
                ['label' => 'Quản lý Hướng dẫn viên', 'url' => BASE_URL . '?act=users', 'active' => true],
            ],
        ]);
    }

    // Hiển thị form thêm hướng dẫn viên
    public function create(): void
    {
        requireAdmin();

        ob_start();
        include view_path('admin.users.create');
        $content = ob_get_clean();

        view('layouts.AdminLayout', [
            'title' => 'Thêm Hướng dẫn viên mới',
            'pageTitle' => 'Thêm Hướng dẫn viên mới',
            'content' => $content,
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => BASE_URL . '?act=dashboard'],
                ['label' => 'Quản lý Hướng dẫn viên', 'url' => BASE_URL . '?act=users'],
                ['label' => 'Thêm Hướng dẫn viên mới', 'url' => BASE_URL . '?act=user-create', 'active' => true],
            ],
        ]);
    }

    // Xử lý lưu hướng dẫn viên mới
    public function store(): void
    {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?act=users');
            exit;
        }

        $errors = [];

        // Validate
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        $phone = trim($_POST['phone'] ?? '');
        $role = $_POST['role'] ?? 'guide';
        $status = $_POST['status'] ?? 1;

        if (empty($name)) {
            $errors[] = 'Vui lòng nhập tên';
        }

        if (empty($email)) {
            $errors[] = 'Vui lòng nhập email';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ';
        } elseif (User::existsByEmail($email)) {
            $errors[] = 'Email này đã được sử dụng';
        }

        if (empty($password)) {
            $errors[] = 'Vui lòng nhập mật khẩu';
        } elseif (strlen($password) < 6) {
            $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự';
        } elseif ($password !== $passwordConfirm) {
            $errors[] = 'Mật khẩu xác nhận không khớp';
        }

        if (!empty($errors)) {
            ob_start();
            include view_path('admin.users.create');
            $content = ob_get_clean();

            view('layouts.AdminLayout', [
                'title' => 'Thêm Hướng dẫn viên mới',
                'pageTitle' => 'Thêm Hướng dẫn viên mới',
                'content' => $content,
                'errors' => $errors,
                'old' => $_POST,
                'breadcrumb' => [
                    ['label' => 'Dashboard', 'url' => BASE_URL . '?act=dashboard'],
                    ['label' => 'Quản lý Hướng dẫn viên', 'url' => BASE_URL . '?act=users'],
                    ['label' => 'Thêm Hướng dẫn viên mới', 'url' => BASE_URL . '?act=user-create', 'active' => true],
                ],
            ]);
            return;
        }

        // Tạo user mới
        $user = new User([
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'status' => (int)$status,
            'phone' => $phone ?: null,
        ]);

        if ($user->save($password)) {
            header('Location: ' . BASE_URL . '?act=users&success=1');
        } else {
            header('Location: ' . BASE_URL . '?act=user-create&error=' . urlencode('Không thể lưu hướng dẫn viên'));
        }
        exit;
    }

    // Hiển thị form sửa hướng dẫn viên
    public function edit(): void
    {
        requireAdmin();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . '?act=users');
            exit;
        }

        $user = User::find($id);
        if (!$user) {
            header('Location: ' . BASE_URL . '?act=users&error=' . urlencode('Không tìm thấy người dùng'));
            exit;
        }

        ob_start();
        include view_path('admin.users.edit');
        $content = ob_get_clean();

        view('layouts.AdminLayout', [
            'title' => 'Sửa Hướng dẫn viên',
            'pageTitle' => 'Sửa Hướng dẫn viên',
            'content' => $content,
            'user' => $user,
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => BASE_URL . '?act=dashboard'],
                ['label' => 'Quản lý Hướng dẫn viên', 'url' => BASE_URL . '?act=users'],
                ['label' => 'Sửa Hướng dẫn viên', 'url' => BASE_URL . '?act=user-edit&id=' . $id, 'active' => true],
            ],
        ]);
    }

    // Xử lý cập nhật hướng dẫn viên
    public function update(): void
    {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?act=users');
            exit;
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . '?act=users');
            exit;
        }

        $user = User::find($id);
        if (!$user) {
            header('Location: ' . BASE_URL . '?act=users&error=' . urlencode('Không tìm thấy người dùng'));
            exit;
        }

        $errors = [];

        // Validate
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        $phone = trim($_POST['phone'] ?? '');
        $role = $_POST['role'] ?? 'guide';
        $status = $_POST['status'] ?? 1;

        if (empty($name)) {
            $errors[] = 'Vui lòng nhập tên';
        }

        if (empty($email)) {
            $errors[] = 'Vui lòng nhập email';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ';
        } elseif (User::existsByEmail($email, $id)) {
            $errors[] = 'Email này đã được sử dụng bởi người dùng khác';
        }

        if (!empty($password)) {
            if (strlen($password) < 6) {
                $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự';
            } elseif ($password !== $passwordConfirm) {
                $errors[] = 'Mật khẩu xác nhận không khớp';
            }
        }

        if (!empty($errors)) {
            ob_start();
            include view_path('admin.users.edit');
            $content = ob_get_clean();

            view('layouts.AdminLayout', [
                'title' => 'Sửa Hướng dẫn viên',
                'pageTitle' => 'Sửa Hướng dẫn viên',
                'content' => $content,
                'user' => $user,
                'errors' => $errors,
                'old' => $_POST,
                'breadcrumb' => [
                    ['label' => 'Dashboard', 'url' => BASE_URL . '?act=dashboard'],
                    ['label' => 'Quản lý Hướng dẫn viên', 'url' => BASE_URL . '?act=users'],
                    ['label' => 'Sửa Hướng dẫn viên', 'url' => BASE_URL . '?act=user-edit&id=' . $id, 'active' => true],
                ],
            ]);
            return;
        }

        // Cập nhật user
        $user->name = $name;
        $user->email = $email;
        $user->role = $role;
        $user->status = (int)$status;
        $user->phone = $phone ?: null;

        $passwordToUpdate = !empty($password) ? $password : null;
        if ($user->save($passwordToUpdate)) {
            header('Location: ' . BASE_URL . '?act=users&success=1');
        } else {
            header('Location: ' . BASE_URL . '?act=user-edit&id=' . $id . '&error=' . urlencode('Không thể cập nhật hướng dẫn viên'));
        }
        exit;
    }

    // Xóa hướng dẫn viên
    public function delete(): void
    {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?act=users');
            exit;
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . '?act=users');
            exit;
        }

        $user = User::find($id);
        if (!$user) {
            header('Location: ' . BASE_URL . '?act=users&error=' . urlencode('Không tìm thấy người dùng'));
            exit;
        }

        // Kiểm tra xem có booking nào đang sử dụng guide này không
        $pdo = getDB();
        $checkStmt = $pdo->prepare('SELECT COUNT(*) as count FROM bookings WHERE assigned_guide_id = :id');
        $checkStmt->execute([':id' => $id]);
        $result = $checkStmt->fetch();
        if ($result && $result['count'] > 0) {
            header('Location: ' . BASE_URL . '?act=users&error=' . urlencode('Không thể xóa hướng dẫn viên này vì đang có booking được gán'));
            exit;
        }

        if ($user->delete()) {
            header('Location: ' . BASE_URL . '?act=users&success=1');
        } else {
            header('Location: ' . BASE_URL . '?act=users&error=' . urlencode('Không thể xóa hướng dẫn viên'));
        }
        exit;
    }

    // Phân công tour cho HDV
    public function assignTour(): void
    {
        requireAdmin();

        $pdo = getDB();
        if ($pdo === null) {
            throw new RuntimeException('Không thể kết nối cơ sở dữ liệu');
        }

        // Lấy danh sách bookings chưa có guide hoặc có thể thay đổi guide
        $status = $_GET['status'] ?? null;
        $guideId = $_GET['guide_id'] ?? null;
        $search = $_GET['search'] ?? '';

        $where = [];
        $params = [];

        if ($status !== null && $status !== '') {
            $where[] = 'b.status = :status';
            $params[':status'] = $status;
        }

        if ($guideId !== null && $guideId !== '') {
            $where[] = 'b.assigned_guide_id = :guide_id';
            $params[':guide_id'] = $guideId;
        } else {
            // Hiển thị cả booking chưa có guide
            // $where[] = '(b.assigned_guide_id IS NULL OR b.assigned_guide_id = :guide_id)';
        }

        if (!empty($search)) {
            $where[] = '(t.name LIKE :search1 OR b.booking_code LIKE :search2)';
            $params[':search1'] = '%' . $search . '%';
            $params[':search2'] = '%' . $search . '%';
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "
            SELECT b.*, t.name as tour_name, ts.name as status_name,
                   g.name as guide_name, g.id as guide_id
            FROM bookings b
            LEFT JOIN tours t ON t.id = b.tour_id
            LEFT JOIN tour_statuses ts ON ts.id = b.status
            LEFT JOIN users g ON g.id = b.assigned_guide_id
            {$whereClause}
            ORDER BY b.start_date DESC, b.created_at DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $bookings = $stmt->fetchAll();

        // Lấy danh sách guides
        $guidesStmt = $pdo->query("SELECT * FROM users WHERE role = 'guide' AND status = 1 ORDER BY name");
        $guides = $guidesStmt->fetchAll();

        // Lấy danh sách trạng thái
        $statusesStmt = $pdo->query('SELECT * FROM tour_statuses ORDER BY id');
        $statuses = $statusesStmt->fetchAll();

        ob_start();
        include view_path('admin.users.assign-tour');
        $content = ob_get_clean();

        view('layouts.AdminLayout', [
            'title' => 'Phân công Tour cho HDV',
            'pageTitle' => 'Phân công Tour cho HDV',
            'content' => $content,
            'bookings' => $bookings,
            'guides' => $guides,
            'statuses' => $statuses,
            'currentStatus' => $status,
            'currentGuideId' => $guideId,
            'currentSearch' => $search,
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => BASE_URL . '?act=dashboard'],
                ['label' => 'Quản lý Hướng dẫn viên', 'url' => BASE_URL . '?act=users'],
                ['label' => 'Phân công Tour', 'url' => BASE_URL . '?act=user-assign-tour', 'active' => true],
            ],
        ]);
    }

    // Xử lý phân công tour cho HDV
    public function assignTourStore(): void
    {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?act=user-assign-tour');
            exit;
        }

        $bookingId = $_POST['booking_id'] ?? null;
        $guideId = $_POST['guide_id'] ?? null;

        if (!$bookingId) {
            header('Location: ' . BASE_URL . '?act=user-assign-tour&error=' . urlencode('Không tìm thấy booking'));
            exit;
        }

        $pdo = getDB();
        if ($pdo === null) {
            throw new RuntimeException('Không thể kết nối cơ sở dữ liệu');
        }

        // Kiểm tra booking tồn tại
        $bookingStmt = $pdo->prepare('SELECT * FROM bookings WHERE id = :id');
        $bookingStmt->execute([':id' => $bookingId]);
        $booking = $bookingStmt->fetch();

        if (!$booking) {
            header('Location: ' . BASE_URL . '?act=user-assign-tour&error=' . urlencode('Không tìm thấy booking'));
            exit;
        }

        // Nếu guide_id rỗng, bỏ phân công
        if (empty($guideId)) {
            $guideId = null;
        } else {
            // Kiểm tra guide tồn tại và là guide
            $guideStmt = $pdo->prepare("SELECT * FROM users WHERE id = :id AND role = 'guide' AND status = 1");
            $guideStmt->execute([':id' => $guideId]);
            $guide = $guideStmt->fetch();

            if (!$guide) {
                header('Location: ' . BASE_URL . '?act=user-assign-tour&error=' . urlencode('Không tìm thấy hướng dẫn viên hoặc hướng dẫn viên không hợp lệ'));
                exit;
            }
        }

        // Cập nhật assigned_guide_id
        $updateStmt = $pdo->prepare('UPDATE bookings SET assigned_guide_id = :guide_id WHERE id = :booking_id');
        if ($updateStmt->execute([':guide_id' => $guideId, ':booking_id' => $bookingId])) {
            header('Location: ' . BASE_URL . '?act=user-assign-tour&success=1');
        } else {
            header('Location: ' . BASE_URL . '?act=user-assign-tour&error=' . urlencode('Không thể phân công tour'));
        }
        exit;
    }
}

