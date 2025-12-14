<?php

// Model Customer đại diện cho thực thể khách hàng trong hệ thống
class Customer
{
    // Các thuộc tính của Customer
    public $id;
    public $name;
    public $phone;
    public $email;
    public $address;
    public $company;
    public $tax_code;
    public $date_of_birth;
    public $gender;
    public $nationality;
    public $passport_number;
    public $passport_expiry;
    public $notes;
    public $status;
    public $created_at;
    public $updated_at;

    // Constructor để khởi tạo thực thể Customer
    public function __construct($data = [])
    {
        if (is_array($data)) {
            $this->id = $data['id'] ?? null;
            $this->name = $data['name'] ?? '';
            $this->phone = $data['phone'] ?? '';
            $this->email = $data['email'] ?? null;
            $this->address = $data['address'] ?? null;
            $this->company = $data['company'] ?? null;
            $this->tax_code = $data['tax_code'] ?? null;
            $this->date_of_birth = $data['date_of_birth'] ?? null;
            $this->gender = $data['gender'] ?? null;
            $this->nationality = $data['nationality'] ?? null;
            $this->passport_number = $data['passport_number'] ?? null;
            $this->passport_expiry = $data['passport_expiry'] ?? null;
            $this->notes = $data['notes'] ?? null;
            $this->status = $data['status'] ?? 1;
            $this->created_at = $data['created_at'] ?? null;
            $this->updated_at = $data['updated_at'] ?? null;
        }
    }

    // Lưu customer vào database (INSERT hoặc UPDATE)
    public function save(): bool
    {
        $pdo = getDB();
        if ($pdo === null) {
            throw new RuntimeException('Không thể kết nối cơ sở dữ liệu');
        }

        if ($this->id) {
            // UPDATE
            $sql = "UPDATE customers SET 
                    name = :name,
                    phone = :phone,
                    email = :email,
                    address = :address,
                    company = :company,
                    tax_code = :tax_code,
                    date_of_birth = :date_of_birth,
                    gender = :gender,
                    nationality = :nationality,
                    passport_number = :passport_number,
                    passport_expiry = :passport_expiry,
                    notes = :notes,
                    status = :status,
                    updated_at = NOW()
                    WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                ':id' => $this->id,
                ':name' => $this->name,
                ':phone' => $this->phone,
                ':email' => $this->email ?: null,
                ':address' => $this->address ?: null,
                ':company' => $this->company ?: null,
                ':tax_code' => $this->tax_code ?: null,
                ':date_of_birth' => $this->date_of_birth ?: null,
                ':gender' => $this->gender ?: null,
                ':nationality' => $this->nationality ?: null,
                ':passport_number' => $this->passport_number ?: null,
                ':passport_expiry' => $this->passport_expiry ?: null,
                ':notes' => $this->notes ?: null,
                ':status' => $this->status,
            ]);
        } else {
            // INSERT
            $sql = "INSERT INTO customers (name, phone, email, address, company, tax_code, date_of_birth, gender, nationality, passport_number, passport_expiry, notes, status, created_at, updated_at)
                    VALUES (:name, :phone, :email, :address, :company, :tax_code, :date_of_birth, :gender, :nationality, :passport_number, :passport_expiry, :notes, :status, NOW(), NOW())";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                ':name' => $this->name,
                ':phone' => $this->phone,
                ':email' => $this->email ?: null,
                ':address' => $this->address ?: null,
                ':company' => $this->company ?: null,
                ':tax_code' => $this->tax_code ?: null,
                ':date_of_birth' => $this->date_of_birth ?: null,
                ':gender' => $this->gender ?: null,
                ':nationality' => $this->nationality ?: null,
                ':passport_number' => $this->passport_number ?: null,
                ':passport_expiry' => $this->passport_expiry ?: null,
                ':notes' => $this->notes ?: null,
                ':status' => $this->status,
            ]);
            if ($result) {
                $this->id = $pdo->lastInsertId();
            }
            return $result;
        }
    }

    // Xóa customer khỏi database
    public function delete(): bool
    {
        if (!$this->id) {
            return false;
        }

        $pdo = getDB();
        if ($pdo === null) {
            throw new RuntimeException('Không thể kết nối cơ sở dữ liệu');
        }

        // Kiểm tra xem có booking nào đang sử dụng customer này không
        $checkStmt = $pdo->prepare('SELECT COUNT(*) as count FROM bookings WHERE customer_id = :id');
        $checkStmt->execute([':id' => $this->id]);
        $result = $checkStmt->fetch();
        if ($result && $result['count'] > 0) {
            throw new RuntimeException('Không thể xóa khách hàng này vì đang có booking liên quan.');
        }

        $stmt = $pdo->prepare('DELETE FROM customers WHERE id = :id');
        return $stmt->execute([':id' => $this->id]);
    }

    // Tìm customer theo ID
    public static function find($id): ?Customer
    {
        $pdo = getDB();
        if ($pdo === null) {
            throw new RuntimeException('Không thể kết nối cơ sở dữ liệu');
        }

        $stmt = $pdo->prepare('SELECT * FROM customers WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();

        if ($data) {
            return new Customer($data);
        }

        return null;
    }

    // Tìm customer theo số điện thoại
    public static function findByPhone($phone): ?Customer
    {
        $pdo = getDB();
        if ($pdo === null) {
            throw new RuntimeException('Không thể kết nối cơ sở dữ liệu');
        }

        $stmt = $pdo->prepare('SELECT * FROM customers WHERE phone = :phone LIMIT 1');
        $stmt->execute([':phone' => $phone]);
        $data = $stmt->fetch();

        if ($data) {
            return new Customer($data);
        }

        return null;
    }

    // Tìm customer theo email
    public static function findByEmail($email): ?Customer
    {
        $pdo = getDB();
        if ($pdo === null) {
            throw new RuntimeException('Không thể kết nối cơ sở dữ liệu');
        }

        $stmt = $pdo->prepare('SELECT * FROM customers WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $data = $stmt->fetch();

        if ($data) {
            return new Customer($data);
        }

        return null;
    }

    // Lấy tất cả customers
    public static function all($status = null): array
    {
        $pdo = getDB();
        if ($pdo === null) {
            throw new RuntimeException('Không thể kết nối cơ sở dữ liệu');
        }

        if ($status !== null) {
            $stmt = $pdo->prepare('SELECT * FROM customers WHERE status = :status ORDER BY created_at DESC');
            $stmt->execute([':status' => $status]);
        } else {
            $stmt = $pdo->query('SELECT * FROM customers ORDER BY created_at DESC');
        }

        $customers = [];
        while ($row = $stmt->fetch()) {
            $customers[] = new Customer($row);
        }

        return $customers;
    }

    // Lấy danh sách bookings của customer
    public function getBookings(): array
    {
        $pdo = getDB();
        if ($pdo === null) {
            throw new RuntimeException('Không thể kết nối cơ sở dữ liệu');
        }

        $stmt = $pdo->prepare('
            SELECT b.*, t.name as tour_name
            FROM bookings b
            LEFT JOIN tours t ON t.id = b.tour_id
            WHERE b.customer_id = :id
            ORDER BY b.created_at DESC
        ');
        $stmt->execute([':id' => $this->id]);

        $bookings = [];
        while ($row = $stmt->fetch()) {
            $bookings[] = $row;
        }

        return $bookings;
    }

    // Lấy số lượng bookings
    public function getBookingCount(): int
    {
        $pdo = getDB();
        if ($pdo === null) {
            throw new RuntimeException('Không thể kết nối cơ sở dữ liệu');
        }

        $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM bookings WHERE customer_id = :id');
        $stmt->execute([':id' => $this->id]);
        $result = $stmt->fetch();

        return $result ? (int)$result['count'] : 0;
    }

    // Format giới tính
    public function getGenderText(): string
    {
        switch ($this->gender) {
            case 'male':
                return 'Nam';
            case 'female':
                return 'Nữ';
            case 'other':
                return 'Khác';
            default:
                return '-';
        }
    }

    // Format ngày sinh
    public function getDateOfBirthFormatted(): string
    {
        if (empty($this->date_of_birth)) {
            return '-';
        }
        return date('d/m/Y', strtotime($this->date_of_birth));
    }

    // Format ngày hết hạn passport
    public function getPassportExpiryFormatted(): string
    {
        if (empty($this->passport_expiry)) {
            return '-';
        }
        return date('d/m/Y', strtotime($this->passport_expiry));
    }
}

