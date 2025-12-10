<?php
class User
{
    private $db;
    
    // Entity properties
    public $id;
    public $name;
    public $email;
    public $role;
    public $status;
    
    public function __construct($arg = null)
    {
        // Nếu truyền database connection (PDO) -> dùng như Model
        if ($arg instanceof PDO) {
            $this->db = $arg;
        }
        // Nếu truyền array -> dùng như Entity
        elseif (is_array($arg)) {
            $this->id = $arg['id'] ?? null;
            $this->name = $arg['name'] ?? null;
            $this->email = $arg['email'] ?? null;
            $this->role = $arg['role'] ?? null;
            $this->status = $arg['status'] ?? null;
            $this->db = null; // Entity không cần database
        }
        // Nếu truyền null hoặc không truyền gì -> khởi tạo rỗng (entity mode)
        else {
            $this->db = null;
        }
    }
    
    // Lấy tất cả users
    public function getAll($filters = [])
    {
        if (!$this->db) {
            return [];
        }
        $sql = "SELECT * FROM users WHERE 1=1";
        $params = [];
        
        if (!empty($filters['role'])) {
            $sql .= " AND role = :role";
            $params[':role'] = $filters['role'];
        }
        
        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
            $params[':status'] = $filters['status'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (name LIKE :search OR email LIKE :search)";
            $params[':search'] = "%{$filters['search']}%";
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Lấy user theo ID
    public function find($id)
    {
        if (!$this->db) {
            return null;
        }
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Lấy user theo email
    public function findByEmail($email)
    {
        if (!$this->db) {
            return null;
        }
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Lấy tất cả hướng dẫn viên
    public function getGuides($active_only = true)
    {
        if (!$this->db) {
            return [];
        }
        $sql = "SELECT * FROM users WHERE role = 'guide'";
        
        if ($active_only) {
            $sql .= " AND status = 'active'";
        }
        
        $sql .= " ORDER BY name ASC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Đếm số user theo role
    public function countByRole($role)
    {
        if (!$this->db) {
            return 0;
        }
        $sql = "SELECT COUNT(*) as total FROM users WHERE role = :role AND status = 'active'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':role' => $role]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    
    // Entity methods
    // Kiểm tra xem user có phải là admin không
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
    
    // Kiểm tra xem user có phải là hướng dẫn viên không
    public function isGuide()
    {
        return $this->role === 'guide';
    }
}
?>