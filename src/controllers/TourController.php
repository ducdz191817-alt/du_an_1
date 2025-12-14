<?php

class TourController
{
    // Hiển thị danh sách tour
    public function index(): void
    {
        requireAdmin();

        $pdo = getDB();
        if ($pdo === null) {
            throw new RuntimeException('Không thể kết nối cơ sở dữ liệu');
        }

        // Lấy tham số filter
        $status = $_GET['status'] ?? null;
        $categoryId = $_GET['category_id'] ?? null;
        $search = $_GET['search'] ?? '';

        // Xây dựng query
        $where = [];
        $params = [];

        if ($status !== null && $status !== '') {
            $where[] = 't.status = :status';
            $params[':status'] = $status;
        }

        if ($categoryId !== null && $categoryId !== '') {
            $where[] = 't.category_id = :category_id';
            $params[':category_id'] = $categoryId;
        }

        if (!empty($search)) {
            $where[] = '(t.name LIKE :search1 OR t.description LIKE :search2)';
            $params[':search1'] = '%' . $search . '%';
            $params[':search2'] = '%' . $search . '%';
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "
            SELECT t.*, c.name as category_name
            FROM tours t
            LEFT JOIN categories c ON c.id = t.category_id
            {$whereClause}
            ORDER BY t.created_at DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $tours = $stmt->fetchAll();

        // Lấy danh sách categories để filter
        $categoriesStmt = $pdo->query('SELECT * FROM categories WHERE status = 1 ORDER BY name');
        $categories = $categoriesStmt->fetchAll();

        ob_start();
        include view_path('admin.tours.index');
        $content = ob_get_clean();

        view('layouts.AdminLayout', [
            'title' => 'Danh sách Tour',
            'pageTitle' => 'Danh sách Tour',
            'content' => $content,
            'tours' => $tours,
            'categories' => $categories,
            'currentStatus' => $status,
            'currentCategoryId' => $categoryId,
            'currentSearch' => $search,
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => BASE_URL . '?act=dashboard'],
                ['label' => 'Danh sách Tour', 'url' => BASE_URL . '?act=tours', 'active' => true],
            ],
        ]);
    }

    // Hiển thị form thêm tour
    public function create(): void
    {
        requireAdmin();

        $pdo = getDB();
        if ($pdo === null) {
            throw new RuntimeException('Không thể kết nối cơ sở dữ liệu');
        }

        // Lấy danh sách categories
        $stmt = $pdo->query('SELECT * FROM categories WHERE status = 1 ORDER BY name');
        $categories = $stmt->fetchAll();

        ob_start();
        include view_path('admin.tours.create');
        $content = ob_get_clean();

        view('layouts.AdminLayout', [
            'title' => 'Thêm Tour mới',
            'pageTitle' => 'Thêm Tour mới',
            'content' => $content,
            'categories' => $categories,
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => BASE_URL . '?act=dashboard'],
                ['label' => 'Danh sách Tour', 'url' => BASE_URL . '?act=tours'],
                ['label' => 'Thêm Tour mới', 'url' => BASE_URL . '?act=tour-create', 'active' => true],
            ],
        ]);
    }

    // Xử lý lưu tour mới
    public function store(): void
    {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?act=tours');
            exit;
        }

        $errors = [];

        // Validate
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $categoryId = $_POST['category_id'] ?? null;
        $price = $_POST['price'] ?? null;
        $status = $_POST['status'] ?? 1;
        $duration = trim($_POST['duration'] ?? '');
        $maxGuests = $_POST['max_guests'] ?? null;

        if (empty($name)) {
            $errors[] = 'Vui lòng nhập tên tour';
        }

        if (empty($description)) {
            $errors[] = 'Vui lòng nhập mô tả tour';
        }

        if ($categoryId === null || $categoryId === '') {
            $errors[] = 'Vui lòng chọn danh mục';
        }

        if ($price === null || $price === '' || $price < 0) {
            $errors[] = 'Vui lòng nhập giá tour hợp lệ';
        }

        // Xử lý JSON fields
        $schedule = null;
        $images = [];
        $prices = null;
        $policies = null;
        $suppliers = null;

        // Schedule
        if (!empty($_POST['schedule'])) {
            $schedule = json_decode($_POST['schedule'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $schedule = null;
            }
        }

        // Images
        if (!empty($_POST['images'])) {
            $imagesInput = is_array($_POST['images']) ? $_POST['images'] : explode(',', $_POST['images']);
            $images = array_filter(array_map('trim', $imagesInput));
        }

        // Prices
        $adultPrice = $_POST['adult_price'] ?? null;
        $childPrice = $_POST['child_price'] ?? null;
        if ($adultPrice !== null || $childPrice !== null) {
            $prices = [
                'adult' => $adultPrice ? (float)$adultPrice : null,
                'child' => $childPrice ? (float)$childPrice : null,
            ];
        }

        // Policies
        if (!empty($_POST['policies'])) {
            $policies = ['text' => $_POST['policies']];
        }

        // Suppliers
        if (!empty($_POST['suppliers'])) {
            $suppliersInput = is_array($_POST['suppliers']) ? $_POST['suppliers'] : explode(',', $_POST['suppliers']);
            $suppliers = ['text' => json_encode(array_filter(array_map('trim', $suppliersInput)))];
        }

        if (!empty($errors)) {
            $pdo = getDB();
            $stmt = $pdo->query('SELECT * FROM categories WHERE status = 1 ORDER BY name');
            $categories = $stmt->fetchAll();

            ob_start();
            include view_path('admin.tours.create');
            $content = ob_get_clean();

            view('layouts.AdminLayout', [
                'title' => 'Thêm Tour mới',
                'pageTitle' => 'Thêm Tour mới',
                'content' => $content,
                'categories' => $categories,
                'errors' => $errors,
                'old' => $_POST,
                'breadcrumb' => [
                    ['label' => 'Dashboard', 'url' => BASE_URL . '?act=dashboard'],
                    ['label' => 'Danh sách Tour', 'url' => BASE_URL . '?act=tours'],
                    ['label' => 'Thêm Tour mới', 'url' => BASE_URL . '?act=tour-create', 'active' => true],
                ],
            ]);
            return;
        }

        // Tạo tour mới
        $tour = new Tour([
            'name' => $name,
            'description' => $description,
            'category_id' => $categoryId,
            'schedule' => $schedule,
            'images' => $images,
            'prices' => $prices,
            'policies' => $policies,
            'suppliers' => $suppliers,
            'price' => $price,
            'status' => $status,
            'duration' => $duration,
            'max_guests' => $maxGuests,
        ]);

        if ($tour->save()) {
            header('Location: ' . BASE_URL . '?act=tours&success=1');
            exit;
        } else {
            $errors[] = 'Có lỗi xảy ra khi lưu tour. Vui lòng thử lại.';
            
            $pdo = getDB();
            $stmt = $pdo->query('SELECT * FROM categories WHERE status = 1 ORDER BY name');
            $categories = $stmt->fetchAll();

            ob_start();
            include view_path('admin.tours.create');
            $content = ob_get_clean();

            view('layouts.AdminLayout', [
                'title' => 'Thêm Tour mới',
                'pageTitle' => 'Thêm Tour mới',
                'content' => $content,
                'categories' => $categories,
                'errors' => $errors,
                'old' => $_POST,
                'breadcrumb' => [
                    ['label' => 'Dashboard', 'url' => BASE_URL . '?act=dashboard'],
                    ['label' => 'Danh sách Tour', 'url' => BASE_URL . '?act=tours'],
                    ['label' => 'Thêm Tour mới', 'url' => BASE_URL . '?act=tour-create', 'active' => true],
                ],
            ]);
        }
    }

    // Hiển thị form sửa tour
    public function edit(): void
    {
        requireAdmin();

        $id = $_GET['id'] ?? null;
        if ($id === null) {
            header('Location: ' . BASE_URL . '?act=tours');
            exit;
        }

        $tour = Tour::find((int)$id);
        if ($tour === null) {
            header('Location: ' . BASE_URL . '?act=tours');
            exit;
        }

        $pdo = getDB();
        $stmt = $pdo->query('SELECT * FROM categories WHERE status = 1 ORDER BY name');
        $categories = $stmt->fetchAll();

        ob_start();
        include view_path('admin.tours.edit');
        $content = ob_get_clean();

        view('layouts.AdminLayout', [
            'title' => 'Sửa Tour',
            'pageTitle' => 'Sửa Tour',
            'content' => $content,
            'tour' => $tour,
            'categories' => $categories,
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => BASE_URL . '?act=dashboard'],
                ['label' => 'Danh sách Tour', 'url' => BASE_URL . '?act=tours'],
                ['label' => 'Sửa Tour', 'url' => BASE_URL . '?act=tour-edit&id=' . $id, 'active' => true],
            ],
        ]);
    }

    // Xử lý cập nhật tour
    public function update(): void
    {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?act=tours');
            exit;
        }

        $id = $_POST['id'] ?? null;
        if ($id === null) {
            header('Location: ' . BASE_URL . '?act=tours');
            exit;
        }

        $tour = Tour::find((int)$id);
        if ($tour === null) {
            header('Location: ' . BASE_URL . '?act=tours');
            exit;
        }

        $errors = [];

        // Validate (tương tự store)
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $categoryId = $_POST['category_id'] ?? null;
        $price = $_POST['price'] ?? null;
        $status = $_POST['status'] ?? 1;
        $duration = trim($_POST['duration'] ?? '');
        $maxGuests = $_POST['max_guests'] ?? null;

        if (empty($name)) {
            $errors[] = 'Vui lòng nhập tên tour';
        }

        if (empty($description)) {
            $errors[] = 'Vui lòng nhập mô tả tour';
        }

        if ($categoryId === null || $categoryId === '') {
            $errors[] = 'Vui lòng chọn danh mục';
        }

        if ($price === null || $price === '' || $price < 0) {
            $errors[] = 'Vui lòng nhập giá tour hợp lệ';
        }

        // Xử lý JSON fields (tương tự store)
        $schedule = null;
        $images = [];
        $prices = null;
        $policies = null;
        $suppliers = null;

        if (!empty($_POST['schedule'])) {
            $schedule = json_decode($_POST['schedule'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $schedule = null;
            }
        }

        if (!empty($_POST['images'])) {
            $imagesInput = is_array($_POST['images']) ? $_POST['images'] : explode(',', $_POST['images']);
            $images = array_filter(array_map('trim', $imagesInput));
        }

        $adultPrice = $_POST['adult_price'] ?? null;
        $childPrice = $_POST['child_price'] ?? null;
        if ($adultPrice !== null || $childPrice !== null) {
            $prices = [
                'adult' => $adultPrice ? (float)$adultPrice : null,
                'child' => $childPrice ? (float)$childPrice : null,
            ];
        }

        if (!empty($_POST['policies'])) {
            $policies = ['text' => $_POST['policies']];
        }

        if (!empty($_POST['suppliers'])) {
            $suppliersInput = is_array($_POST['suppliers']) ? $_POST['suppliers'] : explode(',', $_POST['suppliers']);
            $suppliers = ['text' => json_encode(array_filter(array_map('trim', $suppliersInput)))];
        }

        if (!empty($errors)) {
            $pdo = getDB();
            $stmt = $pdo->query('SELECT * FROM categories WHERE status = 1 ORDER BY name');
            $categories = $stmt->fetchAll();

            ob_start();
            include view_path('admin.tours.edit');
            $content = ob_get_clean();

            view('layouts.AdminLayout', [
                'title' => 'Sửa Tour',
                'pageTitle' => 'Sửa Tour',
                'content' => $content,
                'tour' => $tour,
                'categories' => $categories,
                'errors' => $errors,
                'old' => $_POST,
                'breadcrumb' => [
                    ['label' => 'Dashboard', 'url' => BASE_URL . '?act=dashboard'],
                    ['label' => 'Danh sách Tour', 'url' => BASE_URL . '?act=tours'],
                    ['label' => 'Sửa Tour', 'url' => BASE_URL . '?act=tour-edit&id=' . $id, 'active' => true],
                ],
            ]);
            return;
        }

        // Cập nhật tour
        $tour->name = $name;
        $tour->description = $description;
        $tour->category_id = $categoryId;
        $tour->schedule = $schedule;
        $tour->images = $images;
        $tour->prices = $prices;
        $tour->policies = $policies;
        $tour->suppliers = $suppliers;
        $tour->price = $price;
        $tour->status = $status;
        $tour->duration = $duration;
        $tour->max_guests = $maxGuests;

        if ($tour->save()) {
            header('Location: ' . BASE_URL . '?act=tours&success=1');
            exit;
        } else {
            $errors[] = 'Có lỗi xảy ra khi cập nhật tour. Vui lòng thử lại.';
            
            $pdo = getDB();
            $stmt = $pdo->query('SELECT * FROM categories WHERE status = 1 ORDER BY name');
            $categories = $stmt->fetchAll();

            ob_start();
            include view_path('admin.tours.edit');
            $content = ob_get_clean();

            view('layouts.AdminLayout', [
                'title' => 'Sửa Tour',
                'pageTitle' => 'Sửa Tour',
                'content' => $content,
                'tour' => $tour,
                'categories' => $categories,
                'errors' => $errors,
                'old' => $_POST,
                'breadcrumb' => [
                    ['label' => 'Dashboard', 'url' => BASE_URL . '?act=dashboard'],
                    ['label' => 'Danh sách Tour', 'url' => BASE_URL . '?act=tours'],
                    ['label' => 'Sửa Tour', 'url' => BASE_URL . '?act=tour-edit&id=' . $id, 'active' => true],
                ],
            ]);
        }
    }

    // Xóa tour
    public function delete(): void
    {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?act=tours');
            exit;
        }

        $id = $_POST['id'] ?? null;
        if ($id === null) {
            header('Location: ' . BASE_URL . '?act=tours');
            exit;
        }

        $tour = Tour::find((int)$id);
        if ($tour === null) {
            header('Location: ' . BASE_URL . '?act=tours');
            exit;
        }

        if ($tour->delete()) {
            header('Location: ' . BASE_URL . '?act=tours&success=1');
        } else {
            header('Location: ' . BASE_URL . '?act=tours&error=Không thể xóa tour vì đang có booking liên quan');
        }
        exit;
    }
}

