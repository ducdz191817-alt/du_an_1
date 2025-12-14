<?php

class DashboardController{
    public function index(): void
    {
      requireAdmin();

      $pdo = getDB();
      if($pdo === null){
        throw new RuntimeException('Không thể kết nối cơ sở dữ liệu');
      }
      
      // Thống kê dữ liệu
      $stats = [
        'total_tours' => 0,
        'total_bookings' => 0,
        'total_guides' => 0,
        'total_categories' => 0,
        'total_customers' => 0,
        'new_bookings_today' => 0,
        'pending_bookings' => 0,
        'active_bookings' => 0,
        'completed_bookings' => 0,
        'revenue_this_month' => 0,
        'revenue_last_month' => 0,
        'revenue_today' => 0
      ];

      // Đếm tour
      $stmt = $pdo->query('SELECT COUNT(*) as count FROM tours');
      $row = $stmt->fetch();
      $stats['total_tours'] = (int)($row['count'] ?? 0);
      
      // bookings
      $stmt = $pdo->query('SELECT COUNT(*) as count FROM bookings');
      $row = $stmt->fetch();
      $stats['total_bookings'] = (int)($row['count'] ?? 0);
      
      //guides
      $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'guide' AND status = 1");
      $row = $stmt->fetch();
      $stats['total_guides'] = (int)($row['count'] ?? 0);
      
      //categories
      $stmt = $pdo->query("SELECT COUNT(*) as count FROM categories WHERE status = 1");
      $row = $stmt->fetch();
      $stats['total_categories'] = (int)($row['count'] ?? 0);

      // customers
      $stmt = $pdo->query('SELECT COUNT(*) as count FROM customers WHERE status = 1');
      $row = $stmt->fetch();
      $stats['total_customers'] = (int)($row['count'] ?? 0);

      // số booking mới trong ngày
      $today = date('Y-m-d');
      $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM bookings WHERE DATE(created_at) = :today');
      $stmt->execute([':today' => $today]);
      $row = $stmt->fetch();
      $stats['new_bookings_today'] = (int)($row['count'] ?? 0);
      
      //booking dang cho xac nhan (status = 1)
      $stmt = $pdo->query('SELECT COUNT(*) as count FROM bookings WHERE status = 1');
      $row = $stmt->fetch();
      $stats['pending_bookings'] = (int)($row['count'] ?? 0);

      //booking dang hoat dong (status = 4: Đang diễn ra)
      $stmt = $pdo->query('SELECT COUNT(*) as count FROM bookings WHERE status = 4');
      $row = $stmt->fetch();
      $stats['active_bookings'] = (int)($row['count'] ?? 0);

      //booking da hoan thanh (status = 5: Hoàn tất)
      $stmt = $pdo->query('SELECT COUNT(*) as count FROM bookings WHERE status = 5');
      $row = $stmt->fetch();
      $stats['completed_bookings'] = (int)($row['count'] ?? 0);

      //Doanh thu thang hien tai - Sử dụng total_amount từ database
      $currentMonth = date('Y-m');
      $stmt = $pdo->prepare("
         SELECT COALESCE(SUM(total_amount), 0) as revenue
         FROM bookings
         WHERE status IN (2, 3, 4, 5)
         AND DATE_FORMAT(created_at, '%Y-%m') = :month
      ");
      $stmt->execute([':month' => $currentMonth]);
      $row = $stmt->fetch();
      $stats['revenue_this_month'] = (float)($row['revenue'] ?? 0);

      // Doanh thu thang truoc
      $lastMonth = date('Y-m', strtotime('-1 month'));
      $stmt = $pdo->prepare("
         SELECT COALESCE(SUM(total_amount), 0) as revenue
         FROM bookings
         WHERE status IN (2, 3, 4, 5)
         AND DATE_FORMAT(created_at, '%Y-%m') = :month
      ");
      $stmt->execute([':month' => $lastMonth]);
      $row = $stmt->fetch();
      $stats['revenue_last_month'] = (float)($row['revenue'] ?? 0);

      // doanh thu hom nay
      $stmt = $pdo->prepare("
         SELECT COALESCE(SUM(total_amount), 0) as revenue
         FROM bookings
         WHERE status IN (2, 3, 4, 5)
         AND DATE(created_at) = :today
      ");
      $stmt->execute([':today' => $today]);
      $row = $stmt->fetch();
      $stats['revenue_today'] = (float)($row['revenue'] ?? 0);

      //booking gan day
      $stmt = $pdo->query("
        SELECT b.*, t.name AS tour_name, ts.name AS status_name,
        u.name AS created_by_name
        FROM bookings b
        LEFT JOIN tours t ON t.id = b.tour_id
        LEFT JOIN tour_statuses ts ON ts.id = b.status
        LEFT JOIN users u ON u.id = b.created_by
        ORDER BY b.created_at DESC
        LIMIT 10
      ");
      $recentBookings = $stmt->fetchAll();

      // Xử lý service_detail để lấy số khách (total_amount đã có sẵn trong database)
      foreach($recentBookings as &$b){
        // Ưu tiên sử dụng total_amount từ database
        if (empty($b['total_amount']) && !empty($b['service_detail'])) {
          $service = json_decode($b['service_detail'], true);
          if (is_array($service)){
            $b['total_amount'] = $service['total_amount'] ?? 0;
          }
        }
        
        // Lấy số khách từ service_detail
        if (!empty($b['service_detail'])) {
          $service = json_decode($b['service_detail'], true);
          if (is_array($service)){
            $b['total_guests'] = $service['total_guests'] ?? 0;
          }
        }
      }
      unset($b);
      
      // thong ke booking theo trang thai
      $stmt = $pdo->query("
        SELECT ts.id, ts.name, COUNT(b.id) as count
        FROM tour_statuses ts
        LEFT JOIN bookings b ON b.status = ts.id
        GROUP BY ts.id, ts.name
        ORDER BY ts.id
      ");
      $bookingStatusStats = $stmt->fetchAll();

      ob_start();
      include view_path('admin.dashboard.index');
      $content = ob_get_clean();

      view('layouts.AdminLayout', [
        'title' => 'Dashboard',
        'pageTitle' => 'Dashboard',
        'content' => $content,
        'stats' => $stats,
        'recentBookings' => $recentBookings,
        'bookingStatusStats' => $bookingStatusStats,
        'breadcrumb' => [
          ['label' => "Dashboard", 'url' => BASE_URL . '?act=dashboard', 'active' => true],
        ],
      ]);
}

    // API: lay du lieu doanh thu cua 12 thang
    public function revenueData(): void
    {
      requireAdmin();
      $pdo = getDB();
      if($pdo === null){
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Database connection failed']);
        exit;
      }

      // lay 12 thang gan nhat - Sử dụng total_amount từ database
      $months = [];
      $revenues = [];
      for($i = 11; $i >= 0; $i--){
        $date = date('Y-m', strtotime("-$i months"));
        $months[] = date('m/Y', strtotime("-$i months"));

        $stmt = $pdo->prepare("
          SELECT COALESCE(SUM(total_amount), 0) as revenue
          FROM bookings
          WHERE status IN (2, 3, 4, 5)
          AND DATE_FORMAT(created_at, '%Y-%m') = :month
        ");

        $stmt->execute([':month' => $date]);
        $row = $stmt->fetch();
        $revenues[] = (float)($row['revenue'] ?? 0);
      }
      header('Content-Type: application/json');
      echo json_encode([
        'months' => $months,
        'revenues' => $revenues,
      ]);
      exit;
    }
}