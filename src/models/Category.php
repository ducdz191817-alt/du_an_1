<?php

// Model Category đại diện cho thực thể danh mục trong hệ thống
class Category
{
    // Các thuộc tính của Category
    public $id;
    public $name;
    public $slug;
    public $description;
    public $image;
    public $parent_id;
    public $sort_order;
    public $status;
    public $created_at;
    public $updated_at;

    // Constructor để khởi tạo thực thể Category
    public function __construct($data = [])
    {
        if (is_array($data)) {
            $this->id = $data['id'] ?? null;
            $this->name = $data['name'] ?? '';
            $this->slug = $data['slug'] ?? '';
            $this->description = $data['description'] ?? '';
            $this->image = $data['image'] ?? null;
            $this->parent_id = $data['parent_id'] ?? null;
            $this->sort_order = $data['sort_order'] ?? 0;
            $this->status = $data['status'] ?? 1;
            $this->created_at = $data['created_at'] ?? null;
            $this->updated_at = $data['updated_at'] ?? null;
        }
    }

    // Lưu category vào database (INSERT hoặc UPDATE)
    public function save(): bool
    {
        $pdo = getDB();
        if ($pdo === null) {
            throw new RuntimeException('Không thể kết nối cơ sở dữ liệu');
        }

        // Tạo slug từ name nếu chưa có
        if (empty($this->slug) && !empty($this->name)) {
            $this->slug = $this->generateSlug($this->name);
        }

        // Kiểm tra slug trùng lặp (trừ chính nó)
        if ($this->id) {
            $checkStmt = $pdo->prepare('SELECT id FROM categories WHERE slug = :slug AND id != :id');
            $checkStmt->execute([':slug' => $this->slug, ':id' => $this->id]);
        } else {
            $checkStmt = $pdo->prepare('SELECT id FROM categories WHERE slug = :slug');
            $checkStmt->execute([':slug' => $this->slug]);
        }
        if ($checkStmt->fetch()) {
            // Nếu slug trùng, thêm số vào cuối
            $baseSlug = $this->slug;
            $counter = 1;
            do {
                $this->slug = $baseSlug . '-' . $counter;
                if ($this->id) {
                    $checkStmt = $pdo->prepare('SELECT id FROM categories WHERE slug = :slug AND id != :id');
                    $checkStmt->execute([':slug' => $this->slug, ':id' => $this->id]);
                } else {
                    $checkStmt = $pdo->prepare('SELECT id FROM categories WHERE slug = :slug');
                    $checkStmt->execute([':slug' => $this->slug]);
                }
                $counter++;
            } while ($checkStmt->fetch());
        }

        if ($this->id) {
            // UPDATE
            $sql = "UPDATE categories SET 
                    name = :name,
                    slug = :slug,
                    description = :description,
                    image = :image,
                    parent_id = :parent_id,
                    sort_order = :sort_order,
                    status = :status,
                    updated_at = NOW()
                    WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                ':id' => $this->id,
                ':name' => $this->name,
                ':slug' => $this->slug,
                ':description' => $this->description,
                ':image' => $this->image,
                ':parent_id' => $this->parent_id ?: null,
                ':sort_order' => $this->sort_order,
                ':status' => $this->status,
            ]);
        } else {
            // INSERT
            $sql = "INSERT INTO categories (name, slug, description, image, parent_id, sort_order, status, created_at, updated_at)
                    VALUES (:name, :slug, :description, :image, :parent_id, :sort_order, :status, NOW(), NOW())";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                ':name' => $this->name,
                ':slug' => $this->slug,
                ':description' => $this->description,
                ':image' => $this->image,
                ':parent_id' => $this->parent_id ?: null,
                ':sort_order' => $this->sort_order,
                ':status' => $this->status,
            ]);
            if ($result) {
                $this->id = $pdo->lastInsertId();
            }
            return $result;
        }
    }

    // Xóa category khỏi database
    public function delete(): bool
    {
        if (!$this->id) {
            return false;
        }

        $pdo = getDB();
        if ($pdo === null) {
            throw new RuntimeException('Không thể kết nối cơ sở dữ liệu');
        }

        // Kiểm tra xem có tour nào đang sử dụng category này không
        $checkStmt = $pdo->prepare('SELECT COUNT(*) as count FROM tours WHERE category_id = :id');
        $checkStmt->execute([':id' => $this->id]);
        $result = $checkStmt->fetch();
        if ($result && $result['count'] > 0) {
            throw new RuntimeException('Không thể xóa danh mục này vì đang có tour sử dụng.');
        }

        // Kiểm tra xem có danh mục con nào không
        $checkStmt = $pdo->prepare('SELECT COUNT(*) as count FROM categories WHERE parent_id = :id');
        $checkStmt->execute([':id' => $this->id]);
        $result = $checkStmt->fetch();
        if ($result && $result['count'] > 0) {
            throw new RuntimeException('Không thể xóa danh mục này vì đang có danh mục con.');
        }

        $stmt = $pdo->prepare('DELETE FROM categories WHERE id = :id');
        return $stmt->execute([':id' => $this->id]);
    }

    // Tìm category theo ID
    public static function find($id): ?Category
    {
        $pdo = getDB();
        if ($pdo === null) {
            throw new RuntimeException('Không thể kết nối cơ sở dữ liệu');
        }

        $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();

        if ($data) {
            return new Category($data);
        }

        return null;
    }

    // Lấy tất cả categories
    public static function all($status = null): array
    {
        $pdo = getDB();
        if ($pdo === null) {
            throw new RuntimeException('Không thể kết nối cơ sở dữ liệu');
        }

        if ($status !== null) {
            $stmt = $pdo->prepare('SELECT * FROM categories WHERE status = :status ORDER BY sort_order ASC, name ASC');
            $stmt->execute([':status' => $status]);
        } else {
            $stmt = $pdo->query('SELECT * FROM categories ORDER BY sort_order ASC, name ASC');
        }

        $categories = [];
        while ($row = $stmt->fetch()) {
            $categories[] = new Category($row);
        }

        return $categories;
    }

    // Lấy danh mục cha
    public function getParent(): ?Category
    {
        if (!$this->parent_id) {
            return null;
        }

        return self::find($this->parent_id);
    }

    // Lấy danh sách danh mục con
    public function getChildren(): array
    {
        $pdo = getDB();
        if ($pdo === null) {
            throw new RuntimeException('Không thể kết nối cơ sở dữ liệu');
        }

        $stmt = $pdo->prepare('SELECT * FROM categories WHERE parent_id = :id ORDER BY sort_order ASC, name ASC');
        $stmt->execute([':id' => $this->id]);

        $categories = [];
        while ($row = $stmt->fetch()) {
            $categories[] = new Category($row);
        }

        return $categories;
    }

    // Tạo slug từ tên
    private function generateSlug($string): string
    {
        // Chuyển đổi sang chữ thường
        $string = mb_strtolower($string, 'UTF-8');

        // Loại bỏ dấu tiếng Việt
        $string = $this->removeVietnameseAccents($string);

        // Thay thế khoảng trắng và ký tự đặc biệt bằng dấu gạch ngang
        $string = preg_replace('/[^a-z0-9]+/', '-', $string);

        // Loại bỏ dấu gạch ngang ở đầu và cuối
        $string = trim($string, '-');

        return $string;
    }

    // Loại bỏ dấu tiếng Việt
    private function removeVietnameseAccents($string): string
    {
        $accents = [
            'à' => 'a', 'á' => 'a', 'ạ' => 'a', 'ả' => 'a', 'ã' => 'a',
            'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ậ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a',
            'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ặ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a',
            'è' => 'e', 'é' => 'e', 'ẹ' => 'e', 'ẻ' => 'e', 'ẽ' => 'e',
            'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ệ' => 'e', 'ể' => 'e', 'ễ' => 'e',
            'ì' => 'i', 'í' => 'i', 'ị' => 'i', 'ỉ' => 'i', 'ĩ' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ọ' => 'o', 'ỏ' => 'o', 'õ' => 'o',
            'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ộ' => 'o', 'ổ' => 'o', 'ỗ' => 'o',
            'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ợ' => 'o', 'ở' => 'o', 'ỡ' => 'o',
            'ù' => 'u', 'ú' => 'u', 'ụ' => 'u', 'ủ' => 'u', 'ũ' => 'u',
            'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ự' => 'u', 'ử' => 'u', 'ữ' => 'u',
            'ỳ' => 'y', 'ý' => 'y', 'ỵ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y',
            'đ' => 'd',
            'À' => 'A', 'Á' => 'A', 'Ạ' => 'A', 'Ả' => 'A', 'Ã' => 'A',
            'Â' => 'A', 'Ầ' => 'A', 'Ấ' => 'A', 'Ậ' => 'A', 'Ẩ' => 'A', 'Ẫ' => 'A',
            'Ă' => 'A', 'Ằ' => 'A', 'Ắ' => 'A', 'Ặ' => 'A', 'Ẳ' => 'A', 'Ẵ' => 'A',
            'È' => 'E', 'É' => 'E', 'Ẹ' => 'E', 'Ẻ' => 'E', 'Ẽ' => 'E',
            'Ê' => 'E', 'Ề' => 'E', 'Ế' => 'E', 'Ệ' => 'E', 'Ể' => 'E', 'Ễ' => 'E',
            'Ì' => 'I', 'Í' => 'I', 'Ị' => 'I', 'Ỉ' => 'I', 'Ĩ' => 'I',
            'Ò' => 'O', 'Ó' => 'O', 'Ọ' => 'O', 'Ỏ' => 'O', 'Õ' => 'O',
            'Ô' => 'O', 'Ồ' => 'O', 'Ố' => 'O', 'Ộ' => 'O', 'Ổ' => 'O', 'Ỗ' => 'O',
            'Ơ' => 'O', 'Ờ' => 'O', 'Ớ' => 'O', 'Ợ' => 'O', 'Ở' => 'O', 'Ỡ' => 'O',
            'Ù' => 'U', 'Ú' => 'U', 'Ụ' => 'U', 'Ủ' => 'U', 'Ũ' => 'U',
            'Ư' => 'U', 'Ừ' => 'U', 'Ứ' => 'U', 'Ự' => 'U', 'Ử' => 'U', 'Ữ' => 'U',
            'Ỳ' => 'Y', 'Ý' => 'Y', 'Ỵ' => 'Y', 'Ỷ' => 'Y', 'Ỹ' => 'Y',
            'Đ' => 'D',
        ];

        return strtr($string, $accents);
    }
}


