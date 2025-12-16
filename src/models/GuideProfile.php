<?php

class GuideProfile
{
    public $id;
    public $user_id;
    public $birthdate;
    public $avatar;
    public $phone;
    public $id_card;
    public $address;
    public $certificate;
    public $languages;
    public $experience;
    public $history;
    public $rating;
    public $review_count;
    public $health_status;
    public $group_type;
    public $speciality;
    public $availability_status;
    public $created_at;
    public $updated_at;

    // Constructor để khởi tạo thực thể GuideProfile
    public function __construct($data = [])
    {
        if (is_array($data)) {
            $this->id = $data['id'] ?? null;
            $this->user_id = $data['user_id'] ?? null;
            $this->birthdate = $data['birthdate'] ?? null;
            $this->avatar = $data['avatar'] ?? null;
            $this->phone = $data['phone'] ?? null;
            $this->id_card = $data['id_card'] ?? null;
            $this->address = $data['address'] ?? null;
            $this->certificate = $data['certificate'] ?? null;
            $this->languages = $data['languages'] ?? null;
            $this->experience = $data['experience'] ?? null;
            $this->history = $data['history'] ?? null;
            $this->rating = $data['rating'] ?? 0.00;
            $this->review_count = $data['review_count'] ?? 0;
            $this->health_status = $data['health_status'] ?? null;
            $this->group_type = $data['group_type'] ?? null;
            $this->speciality = $data['speciality'] ?? null;
            $this->availability_status = $data['availability_status'] ?? 'available';
            $this->created_at = $data['created_at'] ?? null;
            $this->updated_at = $data['updated_at'] ?? null;
        }
    }

    // Lưu guide profile vào database (INSERT hoặc UPDATE)
    public function save(): bool
    {
        $pdo = getDB();
        if ($pdo === null) {
            throw new RuntimeException('Không thể kết nối cơ sở dữ liệu');
        }

        // Xử lý JSON fields
        $certificateJson = null;
        if (!empty($this->certificate)) {
            if (is_array($this->certificate)) {
                $certificateJson = json_encode($this->certificate, JSON_UNESCAPED_UNICODE);
            } else {
                $certificateJson = $this->certificate;
            }
        }

        $languagesJson = null;
        if (!empty($this->languages)) {
            if (is_array($this->languages)) {
                $languagesJson = json_encode($this->languages, JSON_UNESCAPED_UNICODE);
            } else {
                $languagesJson = $this->languages;
            }
        }

        $historyJson = null;
        if (!empty($this->history)) {
            if (is_array($this->history)) {
                $historyJson = json_encode($this->history, JSON_UNESCAPED_UNICODE);
            } else {
                $historyJson = $this->history;
            }
        }

        if ($this->id) {
            // UPDATE
            $sql = "UPDATE guide_profiles SET 
                    user_id = :user_id,
                    birthdate = :birthdate,
                    avatar = :avatar,
                    phone = :phone,
                    id_card = :id_card,
                    address = :address,
                    certificate = :certificate,
                    languages = :languages,
                    experience = :experience,
                    history = :history,
                    rating = :rating,
                    review_count = :review_count,
                    health_status = :health_status,
                    group_type = :group_type,
                    speciality = :speciality,
                    availability_status = :availability_status,
                    updated_at = NOW()
                    WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                ':id' => $this->id,
                ':user_id' => $this->user_id,
                ':birthdate' => $this->birthdate ?: null,
                ':avatar' => $this->avatar ?: null,
                ':phone' => $this->phone ?: null,
                ':id_card' => $this->id_card ?: null,
                ':address' => $this->address ?: null,
                ':certificate' => $certificateJson,
                ':languages' => $languagesJson,
                ':experience' => $this->experience ?: null,
                ':history' => $historyJson,
                ':rating' => $this->rating,
                ':review_count' => $this->review_count,
                ':health_status' => $this->health_status ?: null,
                ':group_type' => $this->group_type ?: null,
                ':speciality' => $this->speciality ?: null,
                ':availability_status' => $this->availability_status,
            ]);
        } else {
            // INSERT
            $sql = "INSERT INTO guide_profiles (user_id, birthdate, avatar, phone, id_card, address, certificate, languages, experience, history, rating, review_count, health_status, group_type, speciality, availability_status, created_at, updated_at)
                    VALUES (:user_id, :birthdate, :avatar, :phone, :id_card, :address, :certificate, :languages, :experience, :history, :rating, :review_count, :health_status, :group_type, :speciality, :availability_status, NOW(), NOW())";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                ':user_id' => $this->user_id,
                ':birthdate' => $this->birthdate ?: null,
                ':avatar' => $this->avatar ?: null,
                ':phone' => $this->phone ?: null,
                ':id_card' => $this->id_card ?: null,
                ':address' => $this->address ?: null,
                ':certificate' => $certificateJson,
                ':languages' => $languagesJson,
                ':experience' => $this->experience ?: null,
                ':history' => $historyJson,
                ':rating' => $this->rating,
                ':review_count' => $this->review_count,
                ':health_status' => $this->health_status ?: null,
                ':group_type' => $this->group_type ?: null,
                ':speciality' => $this->speciality ?: null,
                ':availability_status' => $this->availability_status,
            ]);
            if ($result) {
                $this->id = $pdo->lastInsertId();
            }
            return $result;
        }
    }

    // Xóa guide profile khỏi database
    public function delete(): bool
    {
        if (!$this->id) {
            return false;
        }

        $pdo = getDB();
        if ($pdo === null) {
            throw new RuntimeException('Không thể kết nối cơ sở dữ liệu');
        }

        // Kiểm tra xem có booking nào đang sử dụng guide này không
        $checkStmt = $pdo->prepare('SELECT COUNT(*) as count FROM bookings WHERE assigned_guide_id = :user_id');
        $checkStmt->execute([':user_id' => $this->user_id]);
        $result = $checkStmt->fetch();
        if ($result && $result['count'] > 0) {
            throw new RuntimeException('Không thể xóa hồ sơ này vì đang có booking được gán cho hướng dẫn viên này.');
        }

        $stmt = $pdo->prepare('DELETE FROM guide_profiles WHERE id = :id');
        return $stmt->execute([':id' => $this->id]);
    }

    // Tìm guide profile theo ID
    public static function find($id): ?GuideProfile
    {
        $pdo = getDB();
        if ($pdo === null) {
            throw new RuntimeException('Không thể kết nối cơ sở dữ liệu');
        }

        $stmt = $pdo->prepare('SELECT * FROM guide_profiles WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();

        if ($data) {
            return new GuideProfile($data);
        }

        return null;
    }

    // Tìm guide profile theo user_id
    public static function findByUserId($userId): ?GuideProfile
    {
        $pdo = getDB();
        if ($pdo === null) {
            throw new RuntimeException('Không thể kết nối cơ sở dữ liệu');
        }

        $stmt = $pdo->prepare('SELECT * FROM guide_profiles WHERE user_id = :user_id LIMIT 1');
        $stmt->execute([':user_id' => $userId]);
        $data = $stmt->fetch();

        if ($data) {
            return new GuideProfile($data);
        }

        return null;
    }

    // Lấy tất cả guide profiles
    public static function all(): array
    {
        $pdo = getDB();
        if ($pdo === null) {
            throw new RuntimeException('Không thể kết nối cơ sở dữ liệu');
        }

        $stmt = $pdo->query('SELECT * FROM guide_profiles ORDER BY created_at DESC');
        $profiles = [];
        while ($row = $stmt->fetch()) {
            $profiles[] = new GuideProfile($row);
        }

        return $profiles;
    }

    // Lấy thông tin user
    public function getUser(): ?User
    {
        if (!$this->user_id) {
            return null;
        }

        return User::find($this->user_id);
    }

    // Lấy danh sách bookings của guide
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
            WHERE b.assigned_guide_id = :user_id
            ORDER BY b.created_at DESC
        ');
        $stmt->execute([':user_id' => $this->user_id]);

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

        $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM bookings WHERE assigned_guide_id = :user_id');
        $stmt->execute([':user_id' => $this->user_id]);
        $result = $stmt->fetch();

        return $result ? (int)$result['count'] : 0;
    }

    // Lấy mảng certificate
    public function getCertificateArray(): array
    {
        if (empty($this->certificate)) {
            return [];
        }

        if (is_array($this->certificate)) {
            return $this->certificate;
        }

        $decoded = json_decode($this->certificate, true);
        return is_array($decoded) ? $decoded : [];
    }

    // Lấy mảng languages
    public function getLanguagesArray(): array
    {
        if (empty($this->languages)) {
            return [];
        }

        if (is_array($this->languages)) {
            return $this->languages;
        }

        $decoded = json_decode($this->languages, true);
        return is_array($decoded) ? $decoded : [];
    }

    // Lấy mảng history
    public function getHistoryArray(): array
    {
        if (empty($this->history)) {
            return [];
        }

        if (is_array($this->history)) {
            return $this->history;
        }

        $decoded = json_decode($this->history, true);
        return is_array($decoded) ? $decoded : [];
    }

    // Format ngày sinh
    public function getBirthdateFormatted(): string
    {
        if (empty($this->birthdate)) {
            return '-';
        }
        return date('d/m/Y', strtotime($this->birthdate));
    }

    // Format trạng thái availability
    public function getAvailabilityStatusText(): string
    {
        switch ($this->availability_status) {
            case 'available':
                return 'Có sẵn';
            case 'busy':
                return 'Bận';
            case 'unavailable':
                return 'Không có sẵn';
            default:
                return '-';
        }
    }

    // Format group type
    public function getGroupTypeText(): string
    {
        switch ($this->group_type) {
            case 'nội địa':
            case 'noi-dia':
                return 'Nội địa';
            case 'quốc tế':
            case 'quoc-te':
                return 'Quốc tế';
            default:
                return $this->group_type ?: '-';
        }
    }
}

