<?php
class Customer
{
    private $db;
    
    public function __construct($database)
    {
        $this->db = $database;
    }
    
    // Lấy tất cả khách hàng
    public function getAll($filters = [])
    {
        $sql = "SELECT * FROM customers WHERE 1=1";
        $params = [];
        
        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
            $params[':status'] = $filters['status'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (name LIKE :search OR email LIKE :search OR phone LIKE :search OR company LIKE :search)";
            $params[':search'] = "%{$filters['search']}%";
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Lấy khách hàng theo ID
    public function find($id)
    {
        $sql = "SELECT * FROM customers WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Tạo khách hàng mới
    public function create($data)
    {
        $sql = "INSERT INTO customers (name, phone, email, address, company, bus_code, notes, status) 
                VALUES (:name, :phone, :email, :address, :company, :bus_code, :notes, :status)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':name' => $data['name'],
            ':phone' => $data['phone'] ?? '',
            ':email' => $data['email'] ?? '',
            ':address' => $data['address'] ?? '',
            ':company' => $data['company'] ?? '',
            ':bus_code' => $data['bus_code'] ?? '',
            ':notes' => $data['notes'] ?? '',
            ':status' => $data['status'] ?? 'active',
        ]);
    }
    
    // Cập nhật khách hàng
    public function update($id, $data)
    {
        $sql = "UPDATE customers 
                SET name = :name,
                    phone = :phone,
                    email = :email,
                    address = :address,
                    company = :company,
                    bus_code = :bus_code,
                    notes = :notes,
                    status = :status
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':name' => $data['name'],
            ':phone' => $data['phone'] ?? '',
            ':email' => $data['email'] ?? '',
            ':address' => $data['address'] ?? '',
            ':company' => $data['company'] ?? '',
            ':bus_code' => $data['bus_code'] ?? '',
            ':notes' => $data['notes'] ?? '',
            ':status' => $data['status'] ?? 'active',
            ':id' => $id,
        ]);
    }
    
    // Xóa khách hàng
    public function delete($id)
    {
        // Kiểm tra xem khách hàng có booking nào không
        if ($this->hasBookings($id)) {
            return false;
        }
        
        $sql = "DELETE FROM customers WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
    
    // Kiểm tra khách hàng có booking không
    public function hasBookings($customer_id)
    {
        $sql = "SELECT COUNT(*) as count FROM bookings WHERE customer_id = :customer_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':customer_id' => $customer_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
    
    // Lấy booking của khách hàng
    public function getBookings($customer_id)
    {
        $sql = "SELECT b.*, t.name as tour_name 
                FROM bookings b 
                LEFT JOIN tours t ON b.tour_id = t.id 
                WHERE b.customer_id = :customer_id 
                ORDER BY b.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':customer_id' => $customer_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Đếm số khách hàng
    public function count($filters = [])
    {
        $sql = "SELECT COUNT(*) as total FROM customers WHERE 1=1";
        $params = [];
        
        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
            $params[':status'] = $filters['status'];
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    
    // Tìm khách hàng bằng email hoặc phone
    public function findByEmailOrPhone($email, $phone)
    {
        $sql = "SELECT * FROM customers WHERE email = :email OR phone = :phone LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':email' => $email,
            ':phone' => $phone,
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>