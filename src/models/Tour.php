<?php

// Model Tour đại diện cho thực thể tour trong hệ thống
class Tour
{
    // Các thuộc tính của Tour
    public $id;
    public $name;
    public $slug;
    public $description;
    public $short_description;
    public $category_id;
    public $destination_id;
    public $schedule;
    public $images;
    public $prices;
    public $policies;
    public $suppliers;
    public $inclusions;
    public $exclusions;
    public $price;
    public $sale_price;
    public $status;
    public $duration;
    public $duration_days;
    public $duration_nights;
    public $max_guests;
    public $min_guests;
    public $departure_location;
    public $return_location;
    public $rating;
    public $review_count;
    public $view_count;
    public $booking_count;
    public $is_featured;
    public $is_hot;
    public $meta_title;
    public $meta_description;
    public $meta_keywords;
    public $created_at;
    public $updated_at;

    // Constructor để khởi tạo thực thể Tour
    public function __construct($data = [])
    {
        if (is_array($data)) {
            $this->id = $data['id'] ?? null;
            $this->name = $data['name'] ?? '';
            $this->slug = $data['slug'] ?? '';
            $this->description = $data['description'] ?? '';
            $this->short_description = $data['short_description'] ?? null;
            $this->category_id = $data['category_id'] ?? null;
            $this->destination_id = $data['destination_id'] ?? null;
            $this->schedule = $data['schedule'] ?? null;
            $this->images = $data['images'] ?? null;
            $this->prices = $data['prices'] ?? null;
            $this->policies = $data['policies'] ?? null;
            $this->suppliers = $data['suppliers'] ?? null;
            $this->inclusions = $data['inclusions'] ?? null;
            $this->exclusions = $data['exclusions'] ?? null;
            $this->price = $data['price'] ?? null;
            $this->sale_price = $data['sale_price'] ?? null;
            $this->status = $data['status'] ?? 1;
            $this->duration = $data['duration'] ?? null;
            $this->duration_days = $data['duration_days'] ?? null;
            $this->duration_nights = $data['duration_nights'] ?? null;
            $this->max_guests = $data['max_guests'] ?? null;
            $this->min_guests = $data['min_guests'] ?? 1;
            $this->departure_location = $data['departure_location'] ?? null;
            $this->return_location = $data['return_location'] ?? null;
            $this->rating = $data['rating'] ?? 0.00;
            $this->review_count = $data['review_count'] ?? 0;
            $this->view_count = $data['view_count'] ?? 0;
            $this->booking_count = $data['booking_count'] ?? 0;
            $this->is_featured = $data['is_featured'] ?? 0;
            $this->is_hot = $data['is_hot'] ?? 0;
            $this->meta_title = $data['meta_title'] ?? null;
            $this->meta_description = $data['meta_description'] ?? null;
            $this->meta_keywords = $data['meta_keywords'] ?? null;
            $this->created_at = $data['created_at'] ?? null;
            $this->updated_at = $data['updated_at'] ?? null;
        }
    }

    // Lấy tour theo ID
    public static function find(int $id): ?Tour
    {
        $pdo = getDB();
        if ($pdo === null) {
            return null;
        }

        $stmt = $pdo->prepare('SELECT * FROM tours WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();

        return $row ? new Tour($row) : null;
    }

    // Lấy tất cả tours
    public static function all($status = null): array
    {
        $pdo = getDB();
        if ($pdo === null) {
            return [];
        }

        if ($status !== null) {
            $stmt = $pdo->prepare('SELECT * FROM tours WHERE status = :status ORDER BY created_at DESC');
            $stmt->execute([':status' => $status]);
        } else {
            $stmt = $pdo->query('SELECT * FROM tours ORDER BY created_at DESC');
        }

        $tours = [];
        while ($row = $stmt->fetch()) {
            $tours[] = new Tour($row);
        }

        return $tours;
    }

    // Lưu tour (tạo mới hoặc cập nhật)
    public function save(): bool
    {
        $pdo = getDB();
        if ($pdo === null) {
            return false;
        }

        // Chuyển đổi mảng thành JSON nếu cần
        $scheduleJson = is_array($this->schedule) ? json_encode($this->schedule) : $this->schedule;
        $imagesJson = is_array($this->images) ? json_encode($this->images) : $this->images;
        $pricesJson = is_array($this->prices) ? json_encode($this->prices) : $this->prices;
        $policiesJson = is_array($this->policies) ? json_encode($this->policies) : $this->policies;
        $suppliersJson = is_array($this->suppliers) ? json_encode($this->suppliers) : $this->suppliers;

        // Chuyển đổi thêm các trường JSON mới
        $inclusionsJson = is_array($this->inclusions) ? json_encode($this->inclusions) : $this->inclusions;
        $exclusionsJson = is_array($this->exclusions) ? json_encode($this->exclusions) : $this->exclusions;
        
        // Tạo slug nếu chưa có
        if (empty($this->slug) && !empty($this->name)) {
            $this->slug = $this->generateSlug($this->name);
        }

        if ($this->id === null) {
            // Tạo mới
            $stmt = $pdo->prepare(
                'INSERT INTO tours (name, slug, description, short_description, category_id, destination_id, schedule, images, prices, policies, suppliers, inclusions, exclusions, price, sale_price, status, duration, duration_days, duration_nights, max_guests, min_guests, departure_location, return_location, meta_title, meta_description, meta_keywords)
                 VALUES (:name, :slug, :description, :short_description, :category_id, :destination_id, :schedule, :images, :prices, :policies, :suppliers, :inclusions, :exclusions, :price, :sale_price, :status, :duration, :duration_days, :duration_nights, :max_guests, :min_guests, :departure_location, :return_location, :meta_title, :meta_description, :meta_keywords)'
            );
            $ok = $stmt->execute([
                ':name' => $this->name,
                ':slug' => $this->slug,
                ':description' => $this->description,
                ':short_description' => $this->short_description,
                ':category_id' => $this->category_id,
                ':destination_id' => $this->destination_id,
                ':schedule' => $scheduleJson,
                ':images' => $imagesJson,
                ':prices' => $pricesJson,
                ':policies' => $policiesJson,
                ':suppliers' => $suppliersJson,
                ':inclusions' => $inclusionsJson,
                ':exclusions' => $exclusionsJson,
                ':price' => $this->price,
                ':sale_price' => $this->sale_price,
                ':status' => $this->status,
                ':duration' => $this->duration,
                ':duration_days' => $this->duration_days,
                ':duration_nights' => $this->duration_nights,
                ':max_guests' => $this->max_guests,
                ':min_guests' => $this->min_guests,
                ':departure_location' => $this->departure_location,
                ':return_location' => $this->return_location,
                ':meta_title' => $this->meta_title,
                ':meta_description' => $this->meta_description,
                ':meta_keywords' => $this->meta_keywords,
            ]);

            if ($ok) {
                $this->id = (int)$pdo->lastInsertId();
            }
            return $ok;
        }

        // Cập nhật
        $stmt = $pdo->prepare(
            'UPDATE tours
             SET name = :name,
                 slug = :slug,
                 description = :description,
                 short_description = :short_description,
                 category_id = :category_id,
                 destination_id = :destination_id,
                 schedule = :schedule,
                 images = :images,
                 prices = :prices,
                 policies = :policies,
                 suppliers = :suppliers,
                 inclusions = :inclusions,
                 exclusions = :exclusions,
                 price = :price,
                 sale_price = :sale_price,
                 status = :status,
                 duration = :duration,
                 duration_days = :duration_days,
                 duration_nights = :duration_nights,
                 max_guests = :max_guests,
                 min_guests = :min_guests,
                 departure_location = :departure_location,
                 return_location = :return_location,
                 meta_title = :meta_title,
                 meta_description = :meta_description,
                 meta_keywords = :meta_keywords
             WHERE id = :id'
        );
        return $stmt->execute([
            ':name' => $this->name,
            ':slug' => $this->slug,
            ':description' => $this->description,
            ':short_description' => $this->short_description,
            ':category_id' => $this->category_id,
            ':destination_id' => $this->destination_id,
            ':schedule' => $scheduleJson,
            ':images' => $imagesJson,
            ':prices' => $pricesJson,
            ':policies' => $policiesJson,
            ':suppliers' => $suppliersJson,
            ':inclusions' => $inclusionsJson,
            ':exclusions' => $exclusionsJson,
            ':price' => $this->price,
            ':sale_price' => $this->sale_price,
            ':status' => $this->status,
            ':duration' => $this->duration,
            ':duration_days' => $this->duration_days,
            ':duration_nights' => $this->duration_nights,
            ':max_guests' => $this->max_guests,
            ':min_guests' => $this->min_guests,
            ':departure_location' => $this->departure_location,
            ':return_location' => $this->return_location,
            ':meta_title' => $this->meta_title,
            ':meta_description' => $this->meta_description,
            ':meta_keywords' => $this->meta_keywords,
            ':id' => $this->id,
        ]);
    }

    // Xóa tour
    public function delete(): bool
    {
        if ($this->id === null) {
            return false;
        }

        $pdo = getDB();
        if ($pdo === null) {
            return false;
        }

        // Kiểm tra xem tour có đang được sử dụng trong bookings không
        $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM bookings WHERE tour_id = :id');
        $stmt->execute([':id' => $this->id]);
        $row = $stmt->fetch();
        
        if ((int)($row['count'] ?? 0) > 0) {
            return false; // Không cho phép xóa nếu có booking
        }

        $stmt = $pdo->prepare('DELETE FROM tours WHERE id = :id');
        return $stmt->execute([':id' => $this->id]);
    }

    // Lấy danh mục của tour
    public function getCategory()
    {
        if ($this->category_id === null) {
            return null;
        }

        $pdo = getDB();
        if ($pdo === null) {
            return null;
        }

        $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $this->category_id]);
        return $stmt->fetch();
    }

    // Kiểm tra tour có đang hoạt động không
    public function isActive(): bool
    {
        return $this->status == 1;
    }

    // Lấy giá trị JSON đã decode
    public function getScheduleArray()
    {
        if (empty($this->schedule)) {
            return null;
        }
        $decoded = json_decode($this->schedule, true);
        return is_array($decoded) ? $decoded : null;
    }

    public function getImagesArray()
    {
        if (empty($this->images)) {
            return [];
        }
        $decoded = json_decode($this->images, true);
        return is_array($decoded) ? $decoded : [];
    }

    public function getPricesArray()
    {
        if (empty($this->prices)) {
            return null;
        }
        $decoded = json_decode($this->prices, true);
        return is_array($decoded) ? $decoded : null;
    }

    public function getInclusionsArray()
    {
        if (empty($this->inclusions)) {
            return [];
        }
        $decoded = json_decode($this->inclusions, true);
        return is_array($decoded) ? $decoded : [];
    }

    public function getExclusionsArray()
    {
        if (empty($this->exclusions)) {
            return [];
        }
        $decoded = json_decode($this->exclusions, true);
        return is_array($decoded) ? $decoded : [];
    }

    // Tạo slug từ tên
    private function generateSlug($name)
    {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Kiểm tra slug trùng lặp
        $pdo = getDB();
        if ($pdo !== null) {
            $baseSlug = $slug;
            $counter = 1;
            while (true) {
                $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM tours WHERE slug = :slug AND id != :id');
                $stmt->execute([':slug' => $slug, ':id' => $this->id ?? 0]);
                $row = $stmt->fetch();
                if ((int)($row['count'] ?? 0) == 0) {
                    break;
                }
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
        }
        
        return $slug;
    }

    // Lấy giá hiển thị (ưu tiên sale_price nếu có)
    public function getDisplayPrice()
    {
        return $this->sale_price ?? $this->price ?? 0;
    }

    // Lấy điểm đến
    public function getDestination()
    {
        if ($this->destination_id === null) {
            return null;
        }

        $pdo = getDB();
        if ($pdo === null) {
            return null;
        }

        $stmt = $pdo->prepare('SELECT * FROM destinations WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $this->destination_id]);
        return $stmt->fetch();
    }
}

