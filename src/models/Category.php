<?php
class Category
{
    private $db;
    
    public function __construct($database)
    {
        $this->db = $database;
    }
    
    // Lấy tất cả danh mục
    public function getAll()
    {
        $sql = "SELECT * FROM categories ORDER BY name ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Lấy danh mục active
    public function getAllActive()
    {
        $sql = "SELECT * FROM categories WHERE status = 1 ORDER BY name ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Lấy danh mục theo ID
    public function find($id)
    {
        $sql = "SELECT * FROM categories WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Tạo danh mục mới
    public function create($data)
    {
        // Chuyển đổi status từ string sang int: 'active' -> 1, 'inactive' -> 0
        $status = 1; // default active
        if (isset($data['status'])) {
            $status = ($data['status'] === 'active' || $data['status'] === 1) ? 1 : 0;
        }
        
        $sql = "INSERT INTO categories (name, description, status) 
                VALUES (:name, :description, :status)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':name' => $data['name'],
            ':description' => $data['description'] ?? '',
            ':status' => $status,
        ]);
    }
    
    // Cập nhật danh mục
    public function update($id, $data)
    {
        // Chuyển đổi status từ string sang int: 'active' -> 1, 'inactive' -> 0
        $status = 1; // default active
        if (isset($data['status'])) {
            $status = ($data['status'] === 'active' || $data['status'] === 1) ? 1 : 0;
        }
        
        $sql = "UPDATE categories 
                SET name = :name,
                    description = :description,
                    status = :status,
                    updated_at = NOW()
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':name' => $data['name'],
            ':description' => $data['description'] ?? '',
            ':status' => $status,
            ':id' => $id,
        ]);
    }
    
    // Xóa danh mục
    public function delete($id)
    {
        // Kiểm tra xem danh mục có tour nào không
        if ($this->hasTours($id)) {
            return false;
        }
        
        $sql = "DELETE FROM categories WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
    
    // Kiểm tra danh mục có tour không
    public function hasTours($category_id)
    {
        $sql = "SELECT COUNT(*) as count FROM tours WHERE category_id = :category_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':category_id' => $category_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
    
    // Đếm số tour trong danh mục
    public function countTours($category_id)
    {
        $sql = "SELECT COUNT(*) as count FROM tours WHERE category_id = :category_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':category_id' => $category_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }
}
?>