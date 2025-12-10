<?php
class Booking
{
    private $db;
    
    public function __construct($database)
    {
        $this->db = $database;
    }
    
    // Lấy tất cả booking
    public function getAll($filters = [])
    {
        $sql = "SELECT b.*, 
                       t.name as tour_name, 
                       c.name as customer_name, 
                       u1.name as created_by_name,
                       u2.name as guide_name
                FROM bookings b 
                LEFT JOIN tours t ON b.tour_id = t.id 
                LEFT JOIN customers c ON b.customer_id = c.id 
                LEFT JOIN users u1 ON b.created_by = u1.id 
                LEFT JOIN users u2 ON b.assigned_guide_id = u2.id 
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['status'])) {
            $sql .= " AND b.status = :status";
            $params[':status'] = $filters['status'];
        }
        
        if (!empty($filters['tour_id'])) {
            $sql .= " AND b.tour_id = :tour_id";
            $params[':tour_id'] = $filters['tour_id'];
        }
        
        if (!empty($filters['customer_id'])) {
            $sql .= " AND b.customer_id = :customer_id";
            $params[':customer_id'] = $filters['customer_id'];
        }
        
        if (!empty($filters['guide_id'])) {
            $sql .= " AND b.assigned_guide_id = :guide_id";
            $params[':guide_id'] = $filters['guide_id'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (t.name LIKE :search OR c.name LIKE :search OR c.phone LIKE :search)";
            $params[':search'] = "%{$filters['search']}%";
        }
        
        if (!empty($filters['start_date'])) {
            $sql .= " AND b.start_date >= :start_date";
            $params[':start_date'] = $filters['start_date'];
        }
        
        if (!empty($filters['end_date'])) {
            $sql .= " AND b.end_date <= :end_date";
            $params[':end_date'] = $filters['end_date'];
        }
        
        $sql .= " ORDER BY b.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Lấy booking theo ID
    public function find($id)
    {
        $sql = "SELECT b.*, 
                       t.name as tour_name, 
                       t.price as tour_price,
                       c.name as customer_name,
                       c.phone as customer_phone,
                       c.email as customer_email,
                       u1.name as created_by_name,
                       u2.name as guide_name
                FROM bookings b 
                LEFT JOIN tours t ON b.tour_id = t.id 
                LEFT JOIN customers c ON b.customer_id = c.id 
                LEFT JOIN users u1 ON b.created_by = u1.id 
                LEFT JOIN users u2 ON b.assigned_guide_id = u2.id 
                WHERE b.id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Tạo booking mới
    public function create($data)
    {
        $sql = "INSERT INTO bookings (tour_id, customer_id, created_by, assigned_guide_id, status, start_date, end_date, schedule_detail, service_detail, notes) 
                VALUES (:tour_id, :customer_id, :created_by, :assigned_guide_id, :status, :start_date, :end_date, :schedule_detail, :service_detail, :notes)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':tour_id' => $data['tour_id'],
            ':customer_id' => $data['customer_id'],
            ':created_by' => $data['created_by'] ?? null,
            ':assigned_guide_id' => $data['assigned_guide_id'] ?? null,
            ':status' => $data['status'] ?? 'pending',
            ':start_date' => $data['start_date'] ?? null,
            ':end_date' => $data['end_date'] ?? null,
            ':schedule_detail' => $data['schedule_detail'] ?? '',
            ':service_detail' => $data['service_detail'] ?? '',
            ':notes' => $data['notes'] ?? '',
        ]);
    }
    
    // Cập nhật booking
    public function update($id, $data)
    {
        $sql = "UPDATE bookings 
                SET tour_id = :tour_id,
                    customer_id = :customer_id,
                    assigned_guide_id = :assigned_guide_id,
                    status = :status,
                    start_date = :start_date,
                    end_date = :end_date,
                    schedule_detail = :schedule_detail,
                    service_detail = :service_detail,
                    notes = :notes,
                    updated_at = NOW()
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':tour_id' => $data['tour_id'],
            ':customer_id' => $data['customer_id'],
            ':assigned_guide_id' => $data['assigned_guide_id'] ?? null,
            ':status' => $data['status'] ?? 'pending',
            ':start_date' => $data['start_date'] ?? null,
            ':end_date' => $data['end_date'] ?? null,
            ':schedule_detail' => $data['schedule_detail'] ?? '',
            ':service_detail' => $data['service_detail'] ?? '',
            ':notes' => $data['notes'] ?? '',
            ':id' => $id,
        ]);
    }
    
    // Xóa booking
    public function delete($id)
    {
        $sql = "DELETE FROM bookings WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
    
    // Cập nhật trạng thái booking
    public function updateStatus($id, $status, $changed_by = null, $note = '')
    {
        // Lấy trạng thái cũ
        $old_status = $this->getStatus($id);
        
        // Cập nhật trạng thái mới
        $sql = "UPDATE bookings SET status = :status, updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            ':status' => $status,
            ':id' => $id,
        ]);
        
        if ($result) {
            // Ghi log thay đổi trạng thái
            $this->logStatusChange($id, $old_status, $status, $changed_by, $note);
        }
        
        return $result;
    }
    
    // Lấy trạng thái hiện tại
    public function getStatus($id)
    {
        $sql = "SELECT status FROM bookings WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['status'] ?? null;
    }
    
    // Ghi log thay đổi trạng thái
    public function logStatusChange($booking_id, $old_status, $new_status, $changed_by = null, $note = '')
    {
        $sql = "INSERT INTO booking_status_logs (booking_id, old_status, new_status, changed_by, note) 
                VALUES (:booking_id, :old_status, :new_status, :changed_by, :note)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':booking_id' => $booking_id,
            ':old_status' => $old_status,
            ':new_status' => $new_status,
            ':changed_by' => $changed_by,
            ':note' => $note,
        ]);
    }
    
    // Lấy lịch sử trạng thái
    public function getStatusHistory($booking_id)
    {
        $sql = "SELECT l.*, u.name as changed_by_name 
                FROM booking_status_logs l 
                LEFT JOIN users u ON l.changed_by = u.id 
                WHERE l.booking_id = :booking_id 
                ORDER BY l.changed_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':booking_id' => $booking_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Lấy khách tham gia tour
    public function getGuests($booking_id)
    {
        $sql = "SELECT * FROM tour_guests WHERE booking_id = :booking_id ORDER BY created_at ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':booking_id' => $booking_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Đếm số booking
    public function count($filters = [])
    {
        $sql = "SELECT COUNT(*) as total FROM bookings WHERE 1=1";
        $params = [];
        
        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
            $params[':status'] = $filters['status'];
        }
        
        if (!empty($filters['tour_id'])) {
            $sql .= " AND tour_id = :tour_id";
            $params[':tour_id'] = $filters['tour_id'];
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    
    // Lấy booking của guide
    public function getGuideBookings($guide_id, $status = null)
    {
        $sql = "SELECT b.*, t.name as tour_name, c.name as customer_name 
                FROM bookings b 
                LEFT JOIN tours t ON b.tour_id = t.id 
                LEFT JOIN customers c ON b.customer_id = c.id 
                WHERE b.assigned_guide_id = :guide_id";
        
        $params = [':guide_id' => $guide_id];
        
        if ($status !== null) {
            $sql .= " AND b.status = :status";
            $params[':status'] = $status;
        }
        
        $sql .= " ORDER BY b.start_date ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>