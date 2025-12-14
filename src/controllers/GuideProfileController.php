<?php

class GuideProfileController
{
    // Hiển thị danh sách hồ sơ HDV
    public function index(): void
    {
        requireAdmin();

        $pdo = getDB();
        if ($pdo === null) {
            throw new RuntimeException('Không thể kết nối cơ sở dữ liệu');
        }

        // Lấy tham số filter
        $availabilityStatus = $_GET['availability_status'] ?? null;
        $groupType = $_GET['group_type'] ?? null;
        $search = $_GET['search'] ?? '';

        // Xây dựng query
        $where = [];
        $params = [];

        if ($availabilityStatus !== null && $availabilityStatus !== '') {
            $where[] = 'gp.availability_status = :availability_status';
            $params[':availability_status'] = $availabilityStatus;
        }

        if ($groupType !== null && $groupType !== '') {
            $where[] = 'gp.group_type = :group_type';
            $params[':group_type'] = $groupType;
        }

        if (!empty($search)) {
            $where[] = '(u.name LIKE :search1 OR u.email LIKE :search2 OR gp.phone LIKE :search3)';
            $params[':search1'] = '%' . $search . '%';
            $params[':search2'] = '%' . $search . '%';
            $params[':search3'] = '%' . $search . '%';
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "
            SELECT gp.*, u.name as user_name, u.email as user_email, u.status as user_status,
                   (SELECT COUNT(*) FROM bookings WHERE assigned_guide_id = gp.user_id) as booking_count
            FROM guide_profiles gp
            LEFT JOIN users u ON u.id = gp.user_id
            {$whereClause}
            ORDER BY gp.created_at DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $profiles = $stmt->fetchAll();

        ob_start();
        include view_path('admin.guide-profiles.index');
        $content = ob_get_clean();

        view('layouts.AdminLayout', [
            'title' => 'Danh sách Hồ sơ HDV',
            'pageTitle' => 'Danh sách Hồ sơ HDV',
            'content' => $content,
            'profiles' => $profiles,
            'currentAvailabilityStatus' => $availabilityStatus,
            'currentGroupType' => $groupType,
            'currentSearch' => $search,
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => BASE_URL . '?act=dashboard'],
                ['label' => 'Danh sách Hồ sơ HDV', 'url' => BASE_URL . '?act=guide-profiles', 'active' => true],
            ],
        ]);
    }

    // Hiển thị form thêm hồ sơ HDV
    public function create(): void
    {
        requireAdmin();

        $pdo = getDB();
        if ($pdo === null) {
            throw new RuntimeException('Không thể kết nối cơ sở dữ liệu');
        }

        // Lấy danh sách users có role là guide chưa có profile
        $stmt = $pdo->query("
            SELECT u.* FROM users u
            LEFT JOIN guide_profiles gp ON gp.user_id = u.id
            WHERE u.role = 'guide' AND gp.id IS NULL
            ORDER BY u.name
        ");
        $availableGuides = $stmt->fetchAll();

        ob_start();
        include view_path('admin.guide-profiles.create');
        $content = ob_get_clean();

        view('layouts.AdminLayout', [
            'title' => 'Thêm Hồ sơ HDV mới',
            'pageTitle' => 'Thêm Hồ sơ HDV mới',
            'content' => $content,
            'availableGuides' => $availableGuides,
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => BASE_URL . '?act=dashboard'],
                ['label' => 'Danh sách Hồ sơ HDV', 'url' => BASE_URL . '?act=guide-profiles'],
                ['label' => 'Thêm Hồ sơ HDV mới', 'url' => BASE_URL . '?act=guide-profile-create', 'active' => true],
            ],
        ]);
    }

    // Xử lý lưu hồ sơ HDV mới
    public function store(): void
    {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?act=guide-profiles');
            exit;
        }

        $errors = [];

        // Validate
        $userId = $_POST['user_id'] ?? null;
        $birthdate = $_POST['birthdate'] ?? null;
        $avatar = trim($_POST['avatar'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $idCard = trim($_POST['id_card'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $experience = trim($_POST['experience'] ?? '');
        $healthStatus = trim($_POST['health_status'] ?? '');
        $groupType = $_POST['group_type'] ?? null;
        $speciality = trim($_POST['speciality'] ?? '');
        $availabilityStatus = $_POST['availability_status'] ?? 'available';

        // Xử lý JSON fields
        $certificates = [];
        if (!empty($_POST['certificates']) && is_array($_POST['certificates'])) {
            $certificates = array_filter(array_map('trim', $_POST['certificates']));
        }

        $languages = [];
        if (!empty($_POST['languages']) && is_array($_POST['languages'])) {
            $languages = array_filter(array_map('trim', $_POST['languages']));
        }

        if (empty($userId)) {
            $errors[] = 'Vui lòng chọn hướng dẫn viên';
        } else {
            // Kiểm tra user có phải là guide không
            $pdo = getDB();
            $userStmt = $pdo->prepare('SELECT role FROM users WHERE id = :id');
            $userStmt->execute([':id' => $userId]);
            $user = $userStmt->fetch();
            if (!$user || !in_array($user['role'], ['guide', 'huong_dan_vien'])) {
                $errors[] = 'User được chọn không phải là hướng dẫn viên';
            }

            // Kiểm tra đã có profile chưa
            $profileStmt = $pdo->prepare('SELECT id FROM guide_profiles WHERE user_id = :user_id');
            $profileStmt->execute([':user_id' => $userId]);
            if ($profileStmt->fetch()) {
                $errors[] = 'Hướng dẫn viên này đã có hồ sơ';
            }
        }

        if (!empty($errors)) {
            $pdo = getDB();
            $stmt = $pdo->query("
                SELECT u.* FROM users u
                LEFT JOIN guide_profiles gp ON gp.user_id = u.id
                WHERE u.role = 'guide' AND gp.id IS NULL
                ORDER BY u.name
            ");
            $availableGuides = $stmt->fetchAll();

            ob_start();
            include view_path('admin.guide-profiles.create');
            $content = ob_get_clean();

            view('layouts.AdminLayout', [
                'title' => 'Thêm Hồ sơ HDV mới',
                'pageTitle' => 'Thêm Hồ sơ HDV mới',
                'content' => $content,
                'availableGuides' => $availableGuides,
                'errors' => $errors,
                'old' => $_POST,
                'breadcrumb' => [
                    ['label' => 'Dashboard', 'url' => BASE_URL . '?act=dashboard'],
                    ['label' => 'Danh sách Hồ sơ HDV', 'url' => BASE_URL . '?act=guide-profiles'],
                    ['label' => 'Thêm Hồ sơ HDV mới', 'url' => BASE_URL . '?act=guide-profile-create', 'active' => true],
                ],
            ]);
            return;
        }

        // Tạo guide profile mới
        $profile = new GuideProfile([
            'user_id' => $userId,
            'birthdate' => $birthdate ?: null,
            'avatar' => $avatar ?: null,
            'phone' => $phone ?: null,
            'id_card' => $idCard ?: null,
            'address' => $address ?: null,
            'certificate' => $certificates,
            'languages' => $languages,
            'experience' => $experience ?: null,
            'history' => [],
            'rating' => 0.00,
            'review_count' => 0,
            'health_status' => $healthStatus ?: null,
            'group_type' => $groupType ?: null,
            'speciality' => $speciality ?: null,
            'availability_status' => $availabilityStatus,
        ]);

        try {
            if ($profile->save()) {
                header('Location: ' . BASE_URL . '?act=guide-profiles&success=1');
            } else {
                header('Location: ' . BASE_URL . '?act=guide-profile-create&error=' . urlencode('Không thể lưu hồ sơ HDV'));
            }
        } catch (Exception $e) {
            header('Location: ' . BASE_URL . '?act=guide-profile-create&error=' . urlencode($e->getMessage()));
        }
        exit;
    }

    // Hiển thị form sửa hồ sơ HDV
    public function edit(): void
    {
        requireAdmin();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . '?act=guide-profiles');
            exit;
        }

        $profile = GuideProfile::find($id);
        if (!$profile) {
            header('Location: ' . BASE_URL . '?act=guide-profiles&error=' . urlencode('Không tìm thấy hồ sơ HDV'));
            exit;
        }

        ob_start();
        include view_path('admin.guide-profiles.edit');
        $content = ob_get_clean();

        view('layouts.AdminLayout', [
            'title' => 'Sửa Hồ sơ HDV',
            'pageTitle' => 'Sửa Hồ sơ HDV',
            'content' => $content,
            'profile' => $profile,
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => BASE_URL . '?act=dashboard'],
                ['label' => 'Danh sách Hồ sơ HDV', 'url' => BASE_URL . '?act=guide-profiles'],
                ['label' => 'Sửa Hồ sơ HDV', 'url' => BASE_URL . '?act=guide-profile-edit&id=' . $id, 'active' => true],
            ],
        ]);
    }

    // Xử lý cập nhật hồ sơ HDV
    public function update(): void
    {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?act=guide-profiles');
            exit;
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . '?act=guide-profiles');
            exit;
        }

        $profile = GuideProfile::find($id);
        if (!$profile) {
            header('Location: ' . BASE_URL . '?act=guide-profiles&error=' . urlencode('Không tìm thấy hồ sơ HDV'));
            exit;
        }

        $errors = [];

        // Validate
        $birthdate = $_POST['birthdate'] ?? null;
        $avatar = trim($_POST['avatar'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $idCard = trim($_POST['id_card'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $experience = trim($_POST['experience'] ?? '');
        $healthStatus = trim($_POST['health_status'] ?? '');
        $groupType = $_POST['group_type'] ?? null;
        $speciality = trim($_POST['speciality'] ?? '');
        $availabilityStatus = $_POST['availability_status'] ?? 'available';

        // Xử lý JSON fields
        $certificates = [];
        if (!empty($_POST['certificates']) && is_array($_POST['certificates'])) {
            $certificates = array_filter(array_map('trim', $_POST['certificates']));
        }

        $languages = [];
        if (!empty($_POST['languages']) && is_array($_POST['languages'])) {
            $languages = array_filter(array_map('trim', $_POST['languages']));
        }

        if (!empty($errors)) {
            ob_start();
            include view_path('admin.guide-profiles.edit');
            $content = ob_get_clean();

            view('layouts.AdminLayout', [
                'title' => 'Sửa Hồ sơ HDV',
                'pageTitle' => 'Sửa Hồ sơ HDV',
                'content' => $content,
                'profile' => $profile,
                'errors' => $errors,
                'old' => $_POST,
                'breadcrumb' => [
                    ['label' => 'Dashboard', 'url' => BASE_URL . '?act=dashboard'],
                    ['label' => 'Danh sách Hồ sơ HDV', 'url' => BASE_URL . '?act=guide-profiles'],
                    ['label' => 'Sửa Hồ sơ HDV', 'url' => BASE_URL . '?act=guide-profile-edit&id=' . $id, 'active' => true],
                ],
            ]);
            return;
        }

        // Cập nhật profile
        $profile->birthdate = $birthdate ?: null;
        $profile->avatar = $avatar ?: null;
        $profile->phone = $phone ?: null;
        $profile->id_card = $idCard ?: null;
        $profile->address = $address ?: null;
        $profile->certificate = $certificates;
        $profile->languages = $languages;
        $profile->experience = $experience ?: null;
        $profile->health_status = $healthStatus ?: null;
        $profile->group_type = $groupType ?: null;
        $profile->speciality = $speciality ?: null;
        $profile->availability_status = $availabilityStatus;

        try {
            if ($profile->save()) {
                header('Location: ' . BASE_URL . '?act=guide-profiles&success=1');
            } else {
                header('Location: ' . BASE_URL . '?act=guide-profile-edit&id=' . $id . '&error=' . urlencode('Không thể cập nhật hồ sơ HDV'));
            }
        } catch (Exception $e) {
            header('Location: ' . BASE_URL . '?act=guide-profile-edit&id=' . $id . '&error=' . urlencode($e->getMessage()));
        }
        exit;
    }

    // Hiển thị chi tiết hồ sơ HDV
    public function show(): void
    {
        requireAdmin();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . '?act=guide-profiles');
            exit;
        }

        $profile = GuideProfile::find($id);
        if (!$profile) {
            header('Location: ' . BASE_URL . '?act=guide-profiles&error=' . urlencode('Không tìm thấy hồ sơ HDV'));
            exit;
        }

        // Lấy thông tin user
        $user = $profile->getUser();

        // Lấy danh sách bookings
        $bookings = $profile->getBookings();

        ob_start();
        include view_path('admin.guide-profiles.show');
        $content = ob_get_clean();

        view('layouts.AdminLayout', [
            'title' => 'Chi tiết Hồ sơ HDV',
            'pageTitle' => 'Chi tiết Hồ sơ HDV',
            'content' => $content,
            'profile' => $profile,
            'user' => $user,
            'bookings' => $bookings,
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => BASE_URL . '?act=dashboard'],
                ['label' => 'Danh sách Hồ sơ HDV', 'url' => BASE_URL . '?act=guide-profiles'],
                ['label' => 'Chi tiết Hồ sơ HDV', 'url' => BASE_URL . '?act=guide-profile-show&id=' . $id, 'active' => true],
            ],
        ]);
    }

    // Xóa hồ sơ HDV
    public function delete(): void
    {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?act=guide-profiles');
            exit;
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . '?act=guide-profiles');
            exit;
        }

        $profile = GuideProfile::find($id);
        if (!$profile) {
            header('Location: ' . BASE_URL . '?act=guide-profiles&error=' . urlencode('Không tìm thấy hồ sơ HDV'));
            exit;
        }

        try {
            if ($profile->delete()) {
                header('Location: ' . BASE_URL . '?act=guide-profiles&success=1');
            } else {
                header('Location: ' . BASE_URL . '?act=guide-profiles&error=' . urlencode('Không thể xóa hồ sơ HDV'));
            }
        } catch (Exception $e) {
            header('Location: ' . BASE_URL . '?act=guide-profiles&error=' . urlencode($e->getMessage()));
        }
        exit;
    }
//abc4

