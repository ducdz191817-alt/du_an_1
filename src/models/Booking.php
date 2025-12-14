<?php

// Model Booking đại diện cho thực thể booking trong hệ thống
class Booking
{
    // Các thuộc tính của Booking
    public $id;
    public $booking_code;
    public $tour_id;
    public $created_by;
    public $customer_id;
    public $assigned_guide_id;
    public $status;
    public $start_date;
    public $end_date;
    public $departure_date;
    public $return_date;
    public $schedule_detail;
    public $service_detail;
    public $diary;
    public $lists_file;
    public $notes;
    public $internal_notes;
    public $total_amount;
    public $paid_amount;
    public $remaining_amount;
    public $discount_amount;
    public $discount_type;
    public $discount_code;
    public $payment_status;
    public $cancellation_reason;
    public $cancelled_at;
    public $cancelled_by;
    public $created_at;
    public $updated_at;

    // Constructor để khởi tạo thực thể Booking
    public function __construct($data = [])
    {
        if (is_array($data)) {
            $this->id = $data['id'] ?? null;
            $this->booking_code = $data['booking_code'] ?? null;
            $this->tour_id = $data['tour_id'] ?? null;
            $this->created_by = $data['created_by'] ?? null;
            $this->customer_id = $data['customer_id'] ?? null;
            $this->assigned_guide_id = $data['assigned_guide_id'] ?? null;
            $this->status = $data['status'] ?? 1; // Mặc định: Chờ xác nhận
            $this->start_date = $data['start_date'] ?? null;
            $this->end_date = $data['end_date'] ?? null;
            $this->departure_date = $data['departure_date'] ?? null;
            $this->return_date = $data['return_date'] ?? null;
            $this->schedule_detail = $data['schedule_detail'] ?? null;
            $this->service_detail = $data['service_detail'] ?? null;
            $this->diary = $data['diary'] ?? null;
            $this->lists_file = $data['lists_file'] ?? null;
            $this->notes = $data['notes'] ?? null;
            $this->internal_notes = $data['internal_notes'] ?? null;
            $this->total_amount = $data['total_amount'] ?? 0.00;
            $this->paid_amount = $data['paid_amount'] ?? 0.00;
            $this->remaining_amount = $data['remaining_amount'] ?? 0.00;
            $this->discount_amount = $data['discount_amount'] ?? 0.00;
            $this->discount_type = $data['discount_type'] ?? null;
            $this->discount_code = $data['discount_code'] ?? null;
            $this->payment_status = $data['payment_status'] ?? 'pending';
            $this->cancellation_reason = $data['cancellation_reason'] ?? null;
            $this->cancelled_at = $data['cancelled_at'] ?? null;
            $this->cancelled_by = $data['cancelled_by'] ?? null;
            $this->created_at = $data['created_at'] ?? null;
            $this->updated_at = $data['updated_at'] ?? null;
        }
    }

    // Lấy booking theo ID
    public static function find(int $id): ?Booking
    {
        $pdo = getDB();
        if ($pdo === null) {
            return null;
        }

        $stmt = $pdo->prepare('SELECT * FROM bookings WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        return $row ? new Booking($row) : null;
    }

    // Lấy tất cả bookings
    public static function all($status = null, $guideId = null): array
    {
        $pdo = getDB();
        if ($pdo === null) {
            return [];
        }

        $where = [];
        $params = [];

        if ($status !== null) {
            $where[] = 'b.status = :status';
            $params[':status'] = $status;
        }

        if ($guideId !== null) {
            $where[] = 'b.assigned_guide_id = :guide_id';
            $params[':guide_id'] = $guideId;
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "
            SELECT b.*, t.name as tour_name, ts.name as status_name,
                   u.name as created_by_name, g.name as guide_name
            FROM bookings b
            LEFT JOIN tours t ON t.id = b.tour_id
            LEFT JOIN tour_statuses ts ON ts.id = b.status
            LEFT JOIN users u ON u.id = b.created_by
            LEFT JOIN users g ON g.id = b.assigned_guide_id
            {$whereClause}
            ORDER BY b.created_at DESC
        ";

        if (!empty($params)) {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
        } else {
            $stmt = $pdo->query($sql);
        }

        $bookings = [];
        while ($row = $stmt->fetch()) {
            $bookings[] = $row;
        }

        return $bookings;
    }

    // Lưu booking (tạo mới hoặc cập nhật)
    public function save(): bool
    {
        $pdo = getDB();
        if ($pdo === null) {
            return false;
        }

        // Chuyển đổi mảng thành JSON nếu cần
        $scheduleJson = is_array($this->schedule_detail) ? json_encode($this->schedule_detail) : $this->schedule_detail;
        $serviceJson = is_array($this->service_detail) ? json_encode($this->service_detail) : $this->service_detail;
        $diaryJson = is_array($this->diary) ? json_encode($this->diary) : $this->diary;
        $listsFileJson = is_array($this->lists_file) ? json_encode($this->lists_file) : $this->lists_file;

        // Tạo booking_code nếu chưa có
        if (empty($this->booking_code)) {
            $this->booking_code = $this->generateBookingCode();
        }

        // Tính remaining_amount
        $this->remaining_amount = $this->total_amount - $this->paid_amount - $this->discount_amount;

        if ($this->id === null) {
            // Tạo mới
            $stmt = $pdo->prepare(
                'INSERT INTO bookings (booking_code, tour_id, created_by, customer_id, assigned_guide_id, status, start_date, end_date, departure_date, return_date,
                 schedule_detail, service_detail, diary, lists_file, notes, internal_notes, total_amount, paid_amount, remaining_amount, discount_amount, discount_type, discount_code, payment_status)
                 VALUES (:booking_code, :tour_id, :created_by, :customer_id, :assigned_guide_id, :status, :start_date, :end_date, :departure_date, :return_date,
                 :schedule_detail, :service_detail, :diary, :lists_file, :notes, :internal_notes, :total_amount, :paid_amount, :remaining_amount, :discount_amount, :discount_type, :discount_code, :payment_status)'
            );
            $ok = $stmt->execute([
                ':booking_code' => $this->booking_code,
                ':tour_id' => $this->tour_id,
                ':created_by' => $this->created_by,
                ':customer_id' => $this->customer_id,
                ':assigned_guide_id' => $this->assigned_guide_id,
                ':status' => $this->status,
                ':start_date' => $this->start_date,
                ':end_date' => $this->end_date,
                ':departure_date' => $this->departure_date,
                ':return_date' => $this->return_date,
                ':schedule_detail' => $scheduleJson,
                ':service_detail' => $serviceJson,
                ':diary' => $diaryJson,
                ':lists_file' => $listsFileJson,
                ':notes' => $this->notes,
                ':internal_notes' => $this->internal_notes,
                ':total_amount' => $this->total_amount,
                ':paid_amount' => $this->paid_amount,
                ':remaining_amount' => $this->remaining_amount,
                ':discount_amount' => $this->discount_amount,
                ':discount_type' => $this->discount_type,
                ':discount_code' => $this->discount_code,
                ':payment_status' => $this->payment_status,
            ]);

            if ($ok) {
                $this->id = (int)$pdo->lastInsertId();
                // Ghi log trạng thái
                $this->logStatusChange(null, $this->status, 'Tạo booking mới');
            }
            return $ok;
        }

        // Cập nhật - lấy trạng thái cũ trước
        $oldBooking = self::find($this->id);
        $oldStatus = $oldBooking ? $oldBooking->status : null;

        $stmt = $pdo->prepare(
            'UPDATE bookings
             SET booking_code = :booking_code,
                 tour_id = :tour_id,
                 created_by = :created_by,
                 customer_id = :customer_id,
                 assigned_guide_id = :assigned_guide_id,
                 status = :status,
                 start_date = :start_date,
                 end_date = :end_date,
                 departure_date = :departure_date,
                 return_date = :return_date,
                 schedule_detail = :schedule_detail,
                 service_detail = :service_detail,
                 diary = :diary,
                 lists_file = :lists_file,
                 notes = :notes,
                 internal_notes = :internal_notes,
                 total_amount = :total_amount,
                 paid_amount = :paid_amount,
                 remaining_amount = :remaining_amount,
                 discount_amount = :discount_amount,
                 discount_type = :discount_type,
                 discount_code = :discount_code,
                 payment_status = :payment_status
             WHERE id = :id'
        );
        $ok = $stmt->execute([
            ':booking_code' => $this->booking_code,
            ':tour_id' => $this->tour_id,
            ':created_by' => $this->created_by,
            ':customer_id' => $this->customer_id,
            ':assigned_guide_id' => $this->assigned_guide_id,
            ':status' => $this->status,
            ':start_date' => $this->start_date,
            ':end_date' => $this->end_date,
            ':departure_date' => $this->departure_date,
            ':return_date' => $this->return_date,
            ':schedule_detail' => $scheduleJson,
            ':service_detail' => $serviceJson,
            ':diary' => $diaryJson,
            ':lists_file' => $listsFileJson,
            ':notes' => $this->notes,
            ':internal_notes' => $this->internal_notes,
            ':total_amount' => $this->total_amount,
            ':paid_amount' => $this->paid_amount,
            ':remaining_amount' => $this->remaining_amount,
            ':discount_amount' => $this->discount_amount,
            ':discount_type' => $this->discount_type,
            ':discount_code' => $this->discount_code,
            ':payment_status' => $this->payment_status,
            ':id' => $this->id,
        ]);

        // Ghi log nếu trạng thái thay đổi
        if ($ok && $oldStatus !== null && $oldStatus != $this->status) {
            $this->logStatusChange($oldStatus, $this->status);
        }

        return $ok;
    }

    // Xóa booking
    public function delete(): bool
    {
        if ($this->id === null) {
            return false;
        }

        $pdo = getDB();
        if ($pdo === null) {
            return false;
        }

        // Xóa danh sách khách trước
        $stmt = $pdo->prepare('DELETE FROM tour_guests WHERE booking_id = :id');
        $stmt->execute([':id' => $this->id]);

        // Xóa log trạng thái
        $stmt = $pdo->prepare('DELETE FROM booking_status_logs WHERE booking_id = :id');
        $stmt->execute([':id' => $this->id]);

        // Xóa booking
        $stmt = $pdo->prepare('DELETE FROM bookings WHERE id = :id');
        return $stmt->execute([':id' => $this->id]);
    }

    // Ghi log thay đổi trạng thái
    public function logStatusChange($oldStatus, $newStatus, $note = '')
    {
        $pdo = getDB();
        if ($pdo === null || $this->id === null) {
            return false;
        }

        $currentUser = getCurrentUser();
        $changedBy = $currentUser ? $currentUser->id : null;

        $stmt = $pdo->prepare(
            'INSERT INTO booking_status_logs (booking_id, old_status, new_status, changed_by, note)
             VALUES (:booking_id, :old_status, :new_status, :changed_by, :note)'
        );
        return $stmt->execute([
            ':booking_id' => $this->id,
            ':old_status' => $oldStatus,
            ':new_status' => $newStatus,
            ':changed_by' => $changedBy,
            ':note' => $note,
        ]);
    }

    // Lấy tour của booking
    public function getTour()
    {
        if ($this->tour_id === null) {
            return null;
        }

        return Tour::find($this->tour_id);
    }

    // Lấy trạng thái của booking
    public function getStatus()
    {
        $pdo = getDB();
        if ($pdo === null || $this->status === null) {
            return null;
        }

        $stmt = $pdo->prepare('SELECT * FROM tour_statuses WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $this->status]);
        return $stmt->fetch();
    }

    // Lấy danh sách khách
    public function getGuests()
    {
        $pdo = getDB();
        if ($pdo === null || $this->id === null) {
            return [];
        }

        $stmt = $pdo->prepare('SELECT * FROM tour_guests WHERE booking_id = :id ORDER BY id');
        $stmt->execute([':id' => $this->id]);
        return $stmt->fetchAll();
    }

    // Lấy lịch sử thay đổi trạng thái
    public function getStatusLogs()
    {
        $pdo = getDB();
        if ($pdo === null || $this->id === null) {
            return [];
        }

        $stmt = $pdo->prepare(
            'SELECT bsl.*, u.name as changed_by_name,
                    os.name as old_status_name, ns.name as new_status_name
             FROM booking_status_logs bsl
             LEFT JOIN users u ON u.id = bsl.changed_by
             LEFT JOIN tour_statuses os ON os.id = bsl.old_status
             LEFT JOIN tour_statuses ns ON ns.id = bsl.new_status
             WHERE bsl.booking_id = :id
             ORDER BY bsl.changed_at DESC'
        );
        $stmt->execute([':id' => $this->id]);
        return $stmt->fetchAll();
    }

    // Lấy giá trị JSON đã decode
    public function getServiceDetailArray()
    {
        if (empty($this->service_detail)) {
            return null;
        }
        $decoded = json_decode($this->service_detail, true);
        return is_array($decoded) ? $decoded : null;
    }

    public function getScheduleDetailArray()
    {
        if (empty($this->schedule_detail)) {
            return null;
        }
        $decoded = json_decode($this->schedule_detail, true);
        return is_array($decoded) ? $decoded : null;
    }

    public function getDiaryArray()
    {
        if (empty($this->diary)) {
            return null;
        }
        $decoded = json_decode($this->diary, true);
        return is_array($decoded) ? $decoded : null;
    }

    // Kiểm tra booking có thể xóa không
    public function canDelete(): bool
    {
        // Có thể xóa nếu status là "Chờ xác nhận" (1) hoặc "Hủy" (6)
        return in_array($this->status, [1, 6]);
    }

    // Tạo mã booking tự động
    private function generateBookingCode()
    {
        $pdo = getDB();
        if ($pdo === null) {
            return 'BK' . date('Ymd') . '001';
        }

        // Lấy prefix từ settings hoặc dùng mặc định
        $prefix = 'BK';
        $stmt = $pdo->prepare('SELECT value FROM settings WHERE `key` = ?');
        $stmt->execute(['booking_prefix']);
        $row = $stmt->fetch();
        if ($row) {
            $prefix = $row['value'] ?? 'BK';
        }

        $date = date('Ymd');
        $code = $prefix . $date . '001';

        // Kiểm tra và tăng số nếu trùng
        $stmt = $pdo->prepare('SELECT booking_code FROM bookings WHERE booking_code LIKE ? ORDER BY booking_code DESC LIMIT 1');
        $stmt->execute([$prefix . $date . '%']);
        $row = $stmt->fetch();
        
        if ($row) {
            $lastCode = $row['booking_code'];
            $lastNumber = (int)substr($lastCode, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
            $code = $prefix . $date . $newNumber;
        }

        return $code;
    }

    // Lấy khách hàng
    public function getCustomer()
    {
        if ($this->customer_id === null) {
            return null;
        }

        $pdo = getDB();
        if ($pdo === null) {
            return null;
        }

        $stmt = $pdo->prepare('SELECT * FROM customers WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $this->customer_id]);
        return $stmt->fetch();
    }
}

