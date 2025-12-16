<?php

class BookingController
{
    // Hiển thị danh sách booking
    public function index(): void
    {
        requireAdmin();

        $pdo = getDB();
        if ($pdo === null) {
            throw new RuntimeException('Không thể kết nối cơ sở dữ liệu');
        }

        // Lấy tham số filter
        $status = $_GET['status'] ?? null;
        $tourId = $_GET['tour_id'] ?? null;
        $guideId = $_GET['guide_id'] ?? null;
        $search = $_GET['search'] ?? '';

        // Xây dựng query
        $where = [];
        $params = [];

        if ($status !== null && $status !== '') {
            $where[] = 'b.status = :status';
            $params[':status'] = $status;
        }

        if ($tourId !== null && $tourId !== '') {
            $where[] = 'b.tour_id = :tour_id';
            $params[':tour_id'] = $tourId;
        }

        if ($guideId !== null && $guideId !== '') {
            $where[] = 'b.assigned_guide_id = :guide_id';
            $params[':guide_id'] = $guideId;
        }

        if (!empty($search)) {
            $where[] = '(t.name LIKE :search1 OR b.notes LIKE :search2 OR b.booking_code LIKE :search3)';
            $params[':search1'] = '%' . $search . '%';
            $params[':search2'] = '%' . $search . '%';
            $params[':search3'] = '%' . $search . '%';
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

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $bookings = $stmt->fetchAll();

        // Xử lý service_detail để lấy thông tin khách hàng và số khách
            foreach ($bookings as &$booking) {
            // Ưu tiên sử dụng total_amount từ database
            if (empty($booking['total_amount']) && !empty($booking['service_detail'])) {
                $service = json_decode($booking['service_detail'], true);
                if (is_array($service)) {
                    $booking['total_amount'] = $service['total_amount'] ?? 0;
                }
            }
            
            // Lấy thông tin khách hàng từ service_detail hoặc customer_id
            if (!empty($booking['service_detail'])) {
                $service = json_decode($booking['service_detail'], true);
                if (is_array($service)) {
                    $booking['customer'] = $service['customer'] ?? null;
                    $booking['total_guests'] = $service['total_guests'] ?? 0;
                    $booking['adult'] = $service['adult'] ?? 0;
                    $booking['child'] = $service['child'] ?? 0;
                }
            }
            
            // Nếu có customer_id, lấy thông tin từ bảng customers
            if (!empty($booking['customer_id']) && empty($booking['customer'])) {
                $customerStmt = $pdo->prepare('SELECT * FROM customers WHERE id = ? LIMIT 1');
                $customerStmt->execute([$booking['customer_id']]);
                $customer = $customerStmt->fetch();
                if ($customer) {
                    $booking['customer'] = [
                        'name' => $customer['name'],
                        'phone' => $customer['phone'],
                        'email' => $customer['email'],
                        'address' => $customer['address'],
                    ];
                }
            }
        }
        unset($booking);

        // Lấy danh sách tours để filter
        $toursStmt = $pdo->query('SELECT * FROM tours WHERE status = 1 ORDER BY name');
        $tours = $toursStmt->fetchAll();

        // Lấy danh sách guides để filter
        $guidesStmt = $pdo->query("SELECT * FROM users WHERE role = 'guide' AND status = 1 ORDER BY name");
        $guides = $guidesStmt->fetchAll();

        // Lấy danh sách trạng thái
        $statusesStmt = $pdo->query('SELECT * FROM tour_statuses ORDER BY id');
        $statuses = $statusesStmt->fetchAll();

        ob_start();
        include view_path('admin.bookings.index');
        $content = ob_get_clean();

        view('layouts.AdminLayout', [
            'title' => 'Danh sách Booking',
            'pageTitle' => 'Danh sách Booking',
            'content' => $content,
            'bookings' => $bookings,
            'tours' => $tours,
            'guides' => $guides,
            'statuses' => $statuses,
            'currentStatus' => $status,
            'currentTourId' => $tourId,
            'currentGuideId' => $guideId,
            'currentSearch' => $search,
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => BASE_URL . '?act=dashboard'],
                ['label' => 'Danh sách Booking', 'url' => BASE_URL . '?act=bookings', 'active' => true],
            ],
        ]);
    }

    // Trang lịch trình cho hướng dẫn viên (xem booking được gán)
    public function guideSchedule(): void
    {
        requireGuideOnly();

        $pdo = getDB();
        if ($pdo === null) {
            throw new RuntimeException('Không thể kết nối cơ sở dữ liệu');
        }

        $currentUser = getCurrentUser();
        $guideId = $currentUser->id;

        $sql = "
            SELECT b.*, t.name as tour_name, ts.name as status_name
            FROM bookings b
            LEFT JOIN tours t ON t.id = b.tour_id
            LEFT JOIN tour_statuses ts ON ts.id = b.status
            WHERE b.assigned_guide_id = :guide_id
            ORDER BY b.departure_date ASC, b.created_at DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':guide_id' => $guideId]);
        $bookings = $stmt->fetchAll();

        // Chuẩn hóa thông tin khách hàng cho mỗi booking
        foreach ($bookings as &$booking) {
            if (!empty($booking['service_detail'])) {
                $service = json_decode($booking['service_detail'], true);
                if (is_array($service)) {
                    $booking['customer'] = $service['customer'] ?? null;
                    $booking['total_guests'] = $service['total_guests'] ?? 0;
                }
            }

            if (!empty($booking['customer_id']) && empty($booking['customer'])) {
                $customerStmt = $pdo->prepare('SELECT * FROM customers WHERE id = ? LIMIT 1');
                $customerStmt->execute([$booking['customer_id']]);
                $customer = $customerStmt->fetch();
                if ($customer) {
                    $booking['customer'] = [
                        'name' => $customer['name'],
                        'phone' => $customer['phone'],
                        'email' => $customer['email'],
                        'address' => $customer['address'],
                    ];
                }
            }
        }
        unset($booking);

        ob_start();
        include view_path('guide.schedule');
        $content = ob_get_clean();

        view('layouts.AdminLayout', [
            'title' => 'Lịch trình của tôi',
            'pageTitle' => 'Lịch trình',
            'content' => $content,
            'bookings' => $bookings,
            'breadcrumb' => [
                ['label' => 'Trang chủ', 'url' => BASE_URL . '?act=home'],
                ['label' => 'Lịch trình', 'url' => BASE_URL . '?act=guide-schedule', 'active' => true],
            ],
        ]);
    }

    // Danh sách khách hàng liên quan đến các booking được gán cho hướng dẫn viên
    public function guideCustomers(): void
    {
        requireGuideOnly();

        $pdo = getDB();
        if ($pdo === null) {
            throw new RuntimeException('Không thể kết nối cơ sở dữ liệu');
        }

        $currentUser = getCurrentUser();
        $guideId = $currentUser->id;

        $sql = "SELECT b.* FROM bookings b WHERE b.assigned_guide_id = :guide_id ORDER BY b.created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':guide_id' => $guideId]);
        $bookings = $stmt->fetchAll();

        // Tập hợp khách hàng duy nhất
        $customers = [];
        foreach ($bookings as $b) {
            $cust = null;
            if (!empty($b['service_detail'])) {
                $service = json_decode($b['service_detail'], true);
                if (is_array($service) && !empty($service['customer'])) {
                    $cust = $service['customer'];
                }
            }

            if (!$cust && !empty($b['customer_id'])) {
                $customerStmt = $pdo->prepare('SELECT * FROM customers WHERE id = ? LIMIT 1');
                $customerStmt->execute([$b['customer_id']]);
                $cRow = $customerStmt->fetch();
                if ($cRow) {
                    $cust = [
                        'id' => $cRow['id'],
                        'name' => $cRow['name'],
                        'phone' => $cRow['phone'],
                        'email' => $cRow['email'],
                        'address' => $cRow['address'],
                    ];
                }
            }

            if ($cust) {
                $key = $cust['phone'] ?? ($cust['email'] ?? ($cust['name'] ?? uniqid()));
                if (!isset($customers[$key])) {
                    $customers[$key] = $cust;
                }
            }
        }

        ob_start();
        include view_path('guide.customers');
        $content = ob_get_clean();

        view('layouts.AdminLayout', [
            'title' => 'Khách hàng của tôi',
            'pageTitle' => 'Danh sách Khách hàng',
            'content' => $content,
            'customers' => $customers,
            'breadcrumb' => [
                ['label' => 'Trang chủ', 'url' => BASE_URL . '?act=home'],
                ['label' => 'Khách hàng', 'url' => BASE_URL . '?act=guide-history', 'active' => true],
            ],
        ]);
    }

    // Hiển thị form tạo booking
    public function create(): void
    {
        requireAdmin();

        $pdo = getDB();
        if ($pdo === null) {
            throw new RuntimeException('Không thể kết nối cơ sở dữ liệu');
        }

        // Lấy danh sách tours
        $toursStmt = $pdo->query('SELECT * FROM tours WHERE status = 1 ORDER BY name');
        $tours = $toursStmt->fetchAll();

        // Lấy danh sách guides
        $guidesStmt = $pdo->query("SELECT * FROM users WHERE role = 'guide' AND status = 1 ORDER BY name");
        $guides = $guidesStmt->fetchAll();

        // Lấy danh sách customers
        $customersStmt = $pdo->query('SELECT * FROM customers WHERE status = 1 ORDER BY name');
        $customers = $customersStmt->fetchAll();

        // Lấy danh sách trạng thái
        $statusesStmt = $pdo->query('SELECT * FROM tour_statuses ORDER BY id');
        $statuses = $statusesStmt->fetchAll();

        ob_start();
        include view_path('admin.bookings.create');
        $content = ob_get_clean();

        view('layouts.AdminLayout', [
            'title' => 'Tạo Booking mới',
            'pageTitle' => 'Tạo Booking mới',
            'content' => $content,
            'tours' => $tours,
            'guides' => $guides,
            'customers' => $customers,
            'statuses' => $statuses,
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => BASE_URL . '?act=dashboard'],
                ['label' => 'Danh sách Booking', 'url' => BASE_URL . '?act=bookings'],
                ['label' => 'Tạo Booking mới', 'url' => BASE_URL . '?act=booking-create', 'active' => true],
            ],
        ]);
    }

    // Xử lý lưu booking mới
    public function store(): void
    {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?act=bookings');
            exit;
        }

        $errors = [];

        // Validate
        $tourId = $_POST['tour_id'] ?? null;
        $assignedGuideId = $_POST['assigned_guide_id'] ?? null;
        $status = $_POST['status'] ?? 1;
        $startDate = $_POST['start_date'] ?? null;
        $endDate = $_POST['end_date'] ?? null;
        $notes = trim($_POST['notes'] ?? '');

        // Thông tin khách hàng
        $customerId = $_POST['customer_id'] ?? null;
        $customerName = trim($_POST['customer_name'] ?? '');
        $customerPhone = trim($_POST['customer_phone'] ?? '');
        $customerEmail = trim($_POST['customer_email'] ?? '');
        $customerAddress = trim($_POST['customer_address'] ?? '');

        // Thông tin booking
        $adult = (int)($_POST['adult'] ?? 0);
        $child = (int)($_POST['child'] ?? 0);
        $bookingType = $_POST['booking_type'] ?? 'individual';
        $specialRequirements = trim($_POST['special_requirements'] ?? '');

        if (empty($tourId)) {
            $errors[] = 'Vui lòng chọn tour';
        }

        if (empty($startDate)) {
            $errors[] = 'Vui lòng chọn ngày bắt đầu';
        }

        if (empty($endDate)) {
            $errors[] = 'Vui lòng chọn ngày kết thúc';
        }

        if ($startDate && $endDate && $startDate > $endDate) {
            $errors[] = 'Ngày kết thúc phải sau ngày bắt đầu';
        }

        if ($adult <= 0 && $child <= 0) {
            $errors[] = 'Vui lòng nhập số lượng khách';
        }

        if (empty($customerName)) {
            $errors[] = 'Vui lòng nhập tên khách hàng';
        }

        if (empty($customerPhone)) {
            $errors[] = 'Vui lòng nhập số điện thoại khách hàng';
        }

        if (!empty($errors)) {
            $pdo = getDB();
            $toursStmt = $pdo->query('SELECT * FROM tours WHERE status = 1 ORDER BY name');
            $tours = $toursStmt->fetchAll();
            $guidesStmt = $pdo->query("SELECT * FROM users WHERE role = 'guide' AND status = 1 ORDER BY name");
            $guides = $guidesStmt->fetchAll();
            $customersStmt = $pdo->query('SELECT * FROM customers WHERE status = 1 ORDER BY name');
            $customers = $customersStmt->fetchAll();
            $statusesStmt = $pdo->query('SELECT * FROM tour_statuses ORDER BY id');
            $statuses = $statusesStmt->fetchAll();

            ob_start();
            include view_path('admin.bookings.create');
            $content = ob_get_clean();

            view('layouts.AdminLayout', [
                'title' => 'Tạo Booking mới',
                'pageTitle' => 'Tạo Booking mới',
                'content' => $content,
                'tours' => $tours,
                'guides' => $guides,
                'customers' => $customers,
                'statuses' => $statuses,
                'errors' => $errors,
                'old' => $_POST,
                'breadcrumb' => [
                    ['label' => 'Dashboard', 'url' => BASE_URL . '?act=dashboard'],
                    ['label' => 'Danh sách Booking', 'url' => BASE_URL . '?act=bookings'],
                    ['label' => 'Tạo Booking mới', 'url' => BASE_URL . '?act=booking-create', 'active' => true],
                ],
            ]);
            return;
        }

        // Lấy giá tour
        $pdo = getDB();
        $tour = Tour::find((int)$tourId);
        if (!$tour) {
            $errors[] = 'Tour không tồn tại';
            header('Location: ' . BASE_URL . '?act=booking-create&error=' . urlencode('Tour không tồn tại'));
            exit;
        }

        $prices = $tour->getPricesArray();
        $adultPrice = $prices['adult'] ?? $tour->price ?? 0;
        $childPrice = $prices['child'] ?? ($adultPrice * 0.7);

        $totalAmount = ($adult * $adultPrice) + ($child * $childPrice);
        $totalGuests = $adult + $child;

        // Tạo hoặc lấy customer
        if ($customerId) {
            // Sử dụng customer có sẵn - cập nhật thông tin nếu cần
            $customerStmt = $pdo->prepare(
                'UPDATE customers SET name = :name, phone = :phone, email = :email, address = :address WHERE id = :id'
            );
            $customerStmt->execute([
                ':name' => $customerName,
                ':phone' => $customerPhone,
                ':email' => $customerEmail,
                ':address' => $customerAddress,
                ':id' => $customerId,
            ]);
        } else {
            // Tạo customer mới
            $customerStmt = $pdo->prepare(
                'INSERT INTO customers (name, phone, email, address, status)
                 VALUES (:name, :phone, :email, :address, 1)'
            );
            $customerStmt->execute([
                ':name' => $customerName,
                ':phone' => $customerPhone,
                ':email' => $customerEmail,
                ':address' => $customerAddress,
            ]);
            $customerId = $pdo->lastInsertId();
        }

        // Tạo service_detail
        $serviceDetail = [
            'customer' => [
                'name' => $customerName,
                'phone' => $customerPhone,
                'email' => $customerEmail,
                'address' => $customerAddress,
            ],
            'customer_id' => (int)$customerId,
            'booking_type' => $bookingType,
            'adult' => $adult,
            'child' => $child,
            'total_guests' => $totalGuests,
            'adult_price' => $adultPrice,
            'child_price' => $childPrice,
            'total_amount' => $totalAmount,
        ];

        if (!empty($specialRequirements)) {
            $serviceDetail['special_requirements'] = $specialRequirements;
        }

        // Tạo booking
        $currentUser = getCurrentUser();
        $booking = new Booking([
            'tour_id' => $tourId,
            'created_by' => $currentUser ? $currentUser->id : null,
            'customer_id' => (int)$customerId,
            'assigned_guide_id' => $assignedGuideId ?: null,
            'status' => $status,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'service_detail' => $serviceDetail,
            'notes' => $notes,
            'total_amount' => $totalAmount,
            'paid_amount' => 0.00,
            'remaining_amount' => $totalAmount,
            'payment_status' => 'pending',
        ]);

        if ($booking->save()) {
            // Lưu danh sách khách nếu có
            if (!empty($_POST['guests'])) {
                $guests = json_decode($_POST['guests'], true);
                if (is_array($guests)) {
                    $guestStmt = $pdo->prepare(
                        'INSERT INTO tour_guests (booking_id, fullname, dob, gender, passport_number)
                         VALUES (:booking_id, :fullname, :dob, :gender, :passport_number)'
                    );
                    foreach ($guests as $guest) {
                        $guestStmt->execute([
                            ':booking_id' => $booking->id,
                            ':fullname' => $guest['fullname'] ?? '',
                            ':dob' => !empty($guest['dob']) ? $guest['dob'] : null,
                            ':gender' => $guest['gender'] ?? null,
                            ':passport_number' => $guest['passport_number'] ?? null,
                        ]);
                    }
                }
            }

            header('Location: ' . BASE_URL . '?act=bookings&success=1');
            exit;
        } else {
            header('Location: ' . BASE_URL . '?act=booking-create&error=' . urlencode('Có lỗi xảy ra khi tạo booking'));
            exit;
        }
    }

    // Hiển thị chi tiết booking
    public function show(): void
    {
        requireAdmin();

        $id = $_GET['id'] ?? null;
        if ($id === null) {
            header('Location: ' . BASE_URL . '?act=bookings');
            exit;
        }

        $booking = Booking::find((int)$id);
        if ($booking === null) {
            header('Location: ' . BASE_URL . '?act=bookings');
            exit;
        }

        // Lấy thông tin đầy đủ
        $pdo = getDB();
        $stmt = $pdo->prepare(
            'SELECT b.*, t.name as tour_name, ts.name as status_name,
                    u.name as created_by_name, g.name as guide_name
             FROM bookings b
             LEFT JOIN tours t ON t.id = b.tour_id
             LEFT JOIN tour_statuses ts ON ts.id = b.status
             LEFT JOIN users u ON u.id = b.created_by
             LEFT JOIN users g ON g.id = b.assigned_guide_id
             WHERE b.id = :id'
        );
        $stmt->execute([':id' => $id]);
        $bookingData = $stmt->fetch();

        $serviceDetail = $booking->getServiceDetailArray();
        $guests = $booking->getGuests();
        $statusLogs = $booking->getStatusLogs();

        ob_start();
        include view_path('admin.bookings.show');
        $content = ob_get_clean();

        view('layouts.AdminLayout', [
            'title' => 'Chi tiết Booking',
            'pageTitle' => 'Chi tiết Booking #' . $booking->id,
            'content' => $content,
            'booking' => $booking,
            'bookingData' => $bookingData,
            'serviceDetail' => $serviceDetail,
            'guests' => $guests,
            'statusLogs' => $statusLogs,
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => BASE_URL . '?act=dashboard'],
                ['label' => 'Danh sách Booking', 'url' => BASE_URL . '?act=bookings'],
                ['label' => 'Chi tiết Booking', 'url' => BASE_URL . '?act=booking-show&id=' . $id, 'active' => true],
            ],
        ]);
    }

    // Cập nhật trạng thái booking
    public function updateStatus(): void
    {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?act=bookings');
            exit;
        }

        $id = $_POST['id'] ?? null;
        $newStatus = $_POST['status'] ?? null;
        $note = trim($_POST['note'] ?? '');

        if ($id === null || $newStatus === null) {
            header('Location: ' . BASE_URL . '?act=bookings');
            exit;
        }

        $booking = Booking::find((int)$id);
        if ($booking === null) {
            header('Location: ' . BASE_URL . '?act=bookings');
            exit;
        }

        $oldStatus = $booking->status;
        $booking->status = $newStatus;

        if ($booking->save()) {
            // Log đã được tự động tạo trong save()
            header('Location: ' . BASE_URL . '?act=booking-show&id=' . $id . '&success=1');
            exit;
        } else {
            header('Location: ' . BASE_URL . '?act=booking-show&id=' . $id . '&error=' . urlencode('Có lỗi xảy ra khi cập nhật trạng thái'));
            exit;
        }
    }

    // Xóa booking
    public function delete(): void
    {
        requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?act=bookings');
            exit;
        }

        $id = $_POST['id'] ?? null;
        if ($id === null) {
            header('Location: ' . BASE_URL . '?act=bookings');
            exit;
        }

        $booking = Booking::find((int)$id);
        if ($booking === null) {
            header('Location: ' . BASE_URL . '?act=bookings');
            exit;
        }

        if (!$booking->canDelete()) {
            header('Location: ' . BASE_URL . '?act=bookings&error=' . urlencode('Không thể xóa booking này. Chỉ có thể xóa booking ở trạng thái "Chờ xác nhận" hoặc "Hủy"'));
            exit;
        }

        if ($booking->delete()) {
            header('Location: ' . BASE_URL . '?act=bookings&success=1');
        } else {
            header('Location: ' . BASE_URL . '?act=bookings&error=' . urlencode('Có lỗi xảy ra khi xóa booking'));
        }
        exit;
    }
}

