<?php
class Tour
{
    private $db;
    
    public function __construct($database)
    {
        $this->db = $database;
    }
    
    // Lấy tất cả tour
    public function getAll($filters = [])
    {
        $sql = "SELECT t.*, c.name as category_name 
                FROM tours t 
                LEFT JOIN categories c ON t.category_id = c.id 
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['category_id'])) {
            $sql .= " AND t.category_id = :category_id";
            $params[':category_id'] = $filters['category_id'];
        }
        
        if (isset($filters['status'])) {
            $sql .= " AND t.status = :status";
            $params[':status'] = $filters['status'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (t.name LIKE :search OR t.description LIKE :search)";
            $params[':search'] = "%{$filters['search']}%";
        }
        
        $sql .= " ORDER BY t.created_at DESC";
        
        if (!empty($filters['limit'])) {
            $sql .= " LIMIT :limit";
            $params[':limit'] = $filters['limit'];
        }
        
        $stmt = $this->db->prepare($sql);
        
        // Bind parameters
        foreach ($params as $key => $value) {
            if ($key === ':limit') {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value);
            }
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Lấy tour theo ID
    public function find($id)
    {
        $sql = "SELECT t.*, c.name as category_name 
                FROM tours t 
                LEFT JOIN categories c ON t.category_id = c.id 
                WHERE t.id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Tạo tour mới
    public function create($data)
    {
        $sql = "INSERT INTO tours (category_id, name, description, price, status, duration, max_guests, schedule, images, prices, policies, suppliers) 
                VALUES (:category_id, :name, :description, :price, :status, :duration, :max_guests, :schedule, :images, :prices, :policies, :suppliers)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':category_id' => $data['category_id'] ?? null,
            ':name' => $data['name'],
            ':description' => $data['description'] ?? '',
            ':price' => $data['price'] ?? 0,
            ':status' => $data['status'] ?? 1,
            ':duration' => $data['duration'] ?? null,
            ':max_guests' => $data['max_guests'] ?? null,
            ':schedule' => isset($data['schedule']) ? json_encode($data['schedule']) : null,
            ':images' => isset($data['images']) ? json_encode($data['images']) : null,
            ':prices' => isset($data['prices']) ? json_encode($data['prices']) : null,
            ':policies' => isset($data['policies']) ? json_encode($data['policies']) : null,
            ':suppliers' => isset($data['suppliers']) ? json_encode($data['suppliers']) : null,
        ]);
    }
    
    // Cập nhật tour
    public function update($id, $data)
    {
        $sql = "UPDATE tours 
                SET category_id = :category_id,
                    name = :name,
                    description = :description,
                    price = :price,
                    status = :status,
                    duration = :duration,
                    max_guests = :max_guests,
                    schedule = :schedule,
                    images = :images,
                    prices = :prices,
                    policies = :policies,
                    suppliers = :suppliers,
                    updated_at = NOW()
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':category_id' => $data['category_id'] ?? null,
            ':name' => $data['name'],
            ':description' => $data['description'] ?? '',
            ':price' => $data['price'] ?? 0,
            ':status' => $data['status'] ?? 1,
            ':duration' => $data['duration'] ?? null,
            ':max_guests' => $data['max_guests'] ?? null,
            ':schedule' => isset($data['schedule']) ? json_encode($data['schedule']) : null,
            ':images' => isset($data['images']) ? json_encode($data['images']) : null,
            ':prices' => isset($data['prices']) ? json_encode($data['prices']) : null,
            ':policies' => isset($data['policies']) ? json_encode($data['policies']) : null,
            ':suppliers' => isset($data['suppliers']) ? json_encode($data['suppliers']) : null,
            ':id' => $id,
        ]);
    }
    
    // Xóa tour
    public function delete($id)
    {
        // Kiểm tra xem tour có booking nào không
        if ($this->hasBookings($id)) {
            return false;
        }
        
        $sql = "DELETE FROM tours WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
    
    // Kiểm tra tour có booking không
    public function hasBookings($tour_id)
    {
        $sql = "SELECT COUNT(*) as count FROM bookings WHERE tour_id = :tour_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':tour_id' => $tour_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
    
    // Lấy booking của tour
    public function getBookings($tour_id)
    {
        $sql = "SELECT b.*, c.name as customer_name, u.name as guide_name 
                FROM bookings b 
                LEFT JOIN customers c ON b.customer_id = c.id 
                LEFT JOIN users u ON b.assigned_guide_id = u.id 
                WHERE b.tour_id = :tour_id 
                ORDER BY b.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':tour_id' => $tour_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Đếm số lượng tour
    public function count($filters = [])
    {
        $sql = "SELECT COUNT(*) as total FROM tours WHERE 1=1";
        $params = [];
        
        if (isset($filters['status'])) {
            $sql .= " AND status = :status";
            $params[':status'] = $filters['status'];
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    
    // Lấy tour active (cho guide) - Lưu ý: tours không có guide_id, cần lấy từ bookings
    public function getActiveToursForGuide($guide_id)
    {
        // Lấy tours từ bookings được assign cho guide này
        $sql = "SELECT DISTINCT t.*, c.name as category_name 
                FROM tours t 
                LEFT JOIN categories c ON t.category_id = c.id 
                INNER JOIN bookings b ON b.tour_id = t.id
                WHERE b.assigned_guide_id = :guide_id 
                AND t.status = 1
                AND b.status IN (1, 2)
                ORDER BY t.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':guide_id' => $guide_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>