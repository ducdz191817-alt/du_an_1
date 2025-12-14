<?php

class CategoryController
{
    // Hiển thị danh sách danh mục
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
            $where[] = '(c.name LIKE :search1 OR c.description LIKE :search2)';
            $params[':search1'] = '%' . $search . '%';
            $params[':search2'] = '%' . $search . '%';
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "
            SELECT c.*, 
                   p.name as parent_name,
                   (SELECT COUNT(*) FROM tours WHERE category_id = c.id) as tour_count,
                   (SELECT COUNT(*) FROM categories WHERE parent_id = c.id) as children_count
            FROM categories c
            LEFT JOIN categories p ON p.id = c.parent_id
            {$whereClause}
            ORDER BY c.sort_order ASC, c.name ASC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $categories = $stmt->fetchAll();

        ob_start();
        include view_path('admin.categories.index');
        $content = ob_get_clean();

        view('layouts.AdminLayout', [
            'title' => 'Danh sách Danh mục',
            'pageTitle' => 'Danh sách Danh mục',
            'content' => $content,
            'categories' => $categories,
            'currentStatus' => $status,
            'currentSearch' => $search,
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => BASE_URL . '?act=dashboard'],
                ['label' => 'Danh sách Danh mục', 'url' => BASE_URL . '?act=categories', 'active' => true],
            ],
        ]);
    }

    // Hiển thị form thêm danh mục
    public function create(): void
    {
        requireAdmin();

        $pdo = getDB();
        if ($pdo === null) {
            throw new RuntimeException('Không thể kết nối cơ sở dữ liệu');
        }

        // Lấy danh sách categories để chọn parent
        $stmt = $pdo->query('SELECT * FROM categories WHERE status = 1 ORDER BY name');
        $parentCategories = $stmt->fetchAll();

        ob_start();
        include view_path('admin.categories.create');
        $content = ob_get_clean();

        view('layouts.AdminLayout', [
            'title' => 'Thêm Danh mục mới',
            'pageTitle' => 'Thêm Danh mục mới',
            'content' => $content,
            'parentCategories' => $parentCategories,
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => BASE_URL . '?act=dashboard'],
                ['label' => 'Danh sách Danh mục', 'url' => BASE_URL . '?act=categories'],
                ['label' => 'Thêm Danh mục mới', 'url' => BASE_URL . '?act=category-create', 'active' => true],
            ],
        ]);
    }

    // Xử lý lưu danh mục mới
    public function store(): void
    {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?act=categories');
            exit;
        }

        $errors = [];

        // Validate
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $image = trim($_POST['image'] ?? '');
        $parentId = $_POST['parent_id'] ?? null;
        $sortOrder = $_POST['sort_order'] ?? 0;
        $status = $_POST['status'] ?? 1;

        if (empty($name)) {
            $errors[] = 'Vui lòng nhập tên danh mục';
        }

        if (!empty($errors)) {
            $pdo = getDB();
            $stmt = $pdo->query('SELECT * FROM categories WHERE status = 1 ORDER BY name');
            $parentCategories = $stmt->fetchAll();

            ob_start();
            include view_path('admin.categories.create');
            $content = ob_get_clean();

            view('layouts.AdminLayout', [
                'title' => 'Thêm Danh mục mới',
                'pageTitle' => 'Thêm Danh mục mới',
                'content' => $content,
                'parentCategories' => $parentCategories,
                'errors' => $errors,
                'old' => $_POST,
                'breadcrumb' => [
                    ['label' => 'Dashboard', 'url' => BASE_URL . '?act=dashboard'],
                    ['label' => 'Danh sách Danh mục', 'url' => BASE_URL . '?act=categories'],
                    ['label' => 'Thêm Danh mục mới', 'url' => BASE_URL . '?act=category-create', 'active' => true],
                ],
            ]);
            return;
        }

        // Tạo category mới
        $category = new Category([
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'image' => $image ?: null,
            'parent_id' => $parentId ?: null,
            'sort_order' => (int)$sortOrder,
            'status' => (int)$status,
        ]);

        try {
            if ($category->save()) {
                header('Location: ' . BASE_URL . '?act=categories&success=1');
            } else {
                header('Location: ' . BASE_URL . '?act=category-create&error=' . urlencode('Không thể lưu danh mục'));
            }
        } catch (Exception $e) {
            header('Location: ' . BASE_URL . '?act=category-create&error=' . urlencode($e->getMessage()));
        }
        exit;
    }

    // Hiển thị form sửa danh mục
    public function edit(): void
    {
        requireAdmin();

        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . '?act=categories');
            exit;
        }

        $category = Category::find($id);
        if (!$category) {
            header('Location: ' . BASE_URL . '?act=categories&error=' . urlencode('Không tìm thấy danh mục'));
            exit;
        }

        $pdo = getDB();
        if ($pdo === null) {
            throw new RuntimeException('Không thể kết nối cơ sở dữ liệu');
        }

        // Lấy danh sách categories để chọn parent (loại trừ chính nó và các danh mục con)
        $stmt = $pdo->prepare('SELECT * FROM categories WHERE status = 1 AND id != :id ORDER BY name');
        $stmt->execute([':id' => $id]);
        $parentCategories = $stmt->fetchAll();

        ob_start();
        include view_path('admin.categories.edit');
        $content = ob_get_clean();

        view('layouts.AdminLayout', [
            'title' => 'Sửa Danh mục',
            'pageTitle' => 'Sửa Danh mục',
            'content' => $content,
            'category' => $category,
            'parentCategories' => $parentCategories,
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => BASE_URL . '?act=dashboard'],
                ['label' => 'Danh sách Danh mục', 'url' => BASE_URL . '?act=categories'],
                ['label' => 'Sửa Danh mục', 'url' => BASE_URL . '?act=category-edit&id=' . $id, 'active' => true],
            ],
        ]);
    }

    // Xử lý cập nhật danh mục
    public function update(): void
    {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?act=categories');
            exit;
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . '?act=categories');
            exit;
        }

        $category = Category::find($id);
        if (!$category) {
            header('Location: ' . BASE_URL . '?act=categories&error=' . urlencode('Không tìm thấy danh mục'));
            exit;
        }

        $errors = [];

        // Validate
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $image = trim($_POST['image'] ?? '');
        $parentId = $_POST['parent_id'] ?? null;
        $sortOrder = $_POST['sort_order'] ?? 0;
        $status = $_POST['status'] ?? 1;

        if (empty($name)) {
            $errors[] = 'Vui lòng nhập tên danh mục';
        }

        // Kiểm tra không được chọn chính nó làm parent
        if ($parentId == $id) {
            $errors[] = 'Không thể chọn chính danh mục này làm danh mục cha';
        }

        if (!empty($errors)) {
            $pdo = getDB();
            $stmt = $pdo->prepare('SELECT * FROM categories WHERE status = 1 AND id != :id ORDER BY name');
            $stmt->execute([':id' => $id]);
            $parentCategories = $stmt->fetchAll();

            ob_start();
            include view_path('admin.categories.edit');
            $content = ob_get_clean();

            view('layouts.AdminLayout', [
                'title' => 'Sửa Danh mục',
                'pageTitle' => 'Sửa Danh mục',
                'content' => $content,
                'category' => $category,
                'parentCategories' => $parentCategories,
                'errors' => $errors,
                'old' => $_POST,
                'breadcrumb' => [
                    ['label' => 'Dashboard', 'url' => BASE_URL . '?act=dashboard'],
                    ['label' => 'Danh sách Danh mục', 'url' => BASE_URL . '?act=categories'],
                    ['label' => 'Sửa Danh mục', 'url' => BASE_URL . '?act=category-edit&id=' . $id, 'active' => true],
                ],
            ]);
            return;
        }

        // Cập nhật category
        $category->name = $name;
        $category->slug = $slug;
        $category->description = $description;
        $category->image = $image ?: null;
        $category->parent_id = $parentId ?: null;
        $category->sort_order = (int)$sortOrder;
        $category->status = (int)$status;

        try {
            if ($category->save()) {
                header('Location: ' . BASE_URL . '?act=categories&success=1');
            } else {
                header('Location: ' . BASE_URL . '?act=category-edit&id=' . $id . '&error=' . urlencode('Không thể cập nhật danh mục'));
            }
        } catch (Exception $e) {
            header('Location: ' . BASE_URL . '?act=category-edit&id=' . $id . '&error=' . urlencode($e->getMessage()));
        }
        exit;
    }

    // Xóa danh mục
    public function delete(): void
    {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?act=categories');
            exit;
        }

        $id = $_POST['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . '?act=categories');
            exit;
        }

        $category = Category::find($id);
        if (!$category) {
            header('Location: ' . BASE_URL . '?act=categories&error=' . urlencode('Không tìm thấy danh mục'));
            exit;
        }

        try {
            if ($category->delete()) {
                header('Location: ' . BASE_URL . '?act=categories&success=1');
            } else {
                header('Location: ' . BASE_URL . '?act=categories&error=' . urlencode('Không thể xóa danh mục'));
            }
        } catch (Exception $e) {
            header('Location: ' . BASE_URL . '?act=categories&error=' . urlencode($e->getMessage()));
        }
        exit;
    }
}


