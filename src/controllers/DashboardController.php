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
      $stmt = $pdo->query('SELECT COUNT(*) as count FROM tours ');
      $row = $stmt->fetch();
      $stats['total_tours'] = (int)($row['count'] ?? 0);
      
      // bookings
      $stmt = $pdo->query('SELECT COUNT(*) as count FROM bookings ');
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
      $stmt->execute([':today'=>$today]);
      $row = $stmt->fetch();
      $stats['new_bookings_today']= (int)($row['count'] ?? 0);
      
      //booking dang cho xac nhan
      $stmt = $pdo->query('SELECT COUNT(*) as count FROM bookings WHERE status = 1 ');
      $row = $stmt->fetch();
      $stats['pending_bookings'] = (int)($row['count'] ?? 0);

      //booking dang hoat dong
      $stmt = $pdo->query('SELECT COUNT(*) as count FROM bookings WHERE status = 2 ');
      $row = $stmt->fetch();
      $stats['active_bookings'] = (int)($row['count'] ?? 0);

      //booking da hoan thanh
      $stmt = $pdo->query('SELECT COUNT(*) as count FROM bookings WHERE status = 3 ');
      $row = $stmt->fetch();
      $stats['completed_bookings'] = (int)($row['count'] ?? 0);

      //Doanh thu thang hien tai
      $currentMonth = date('Y-m');
      $stmt = $pdo->prepare("
         SELECT service_detail
         FROM bookings
         WHERE status IN (2,3)
         AND DATE_FORMAT(created_at, '%Y-%m') = :month
      ");
      $stmt->execute([':month' => $currentMonth]);
      $revenue = 0 ;
      while($row = $stmt->fetch()){
        if(!empty($row['service_detail'])){
          $service = json_decode($row['service_detail'], true);
          if (is_array($service) && isset($service['total_amount'])) {
            $revenue += (float)$service['total_amount'];
          }
        }
      }
      $stats['revenue_this_month'] = $revenue;

      // Doanh thu thang truoc
      $lastMonth = date('Y-m', strtotime('-1 month'));
      $stmt = $pdo->prepare("
         SELECT service_detail
         FROM bookings
         WHERE status IN (2,3)
         AND DATE_FORMAT(created_at, '%Y-%m') = :month
      ");
      $stmt->execute([':month' => $lastMonth]);
      $revenueLast = 0;
      while($row = $stmt->fetch()){
        if(!empty($row['service_detail'])){
          $service = json_decode($row['service_detail'], true);
          if (is_array($service) && isset($service['total_amount'])) {
            $revenueLast += (float)$service['total_amount'];
          }
        }
      }
      $stats['revenue_last_month'] = $revenueLast;


  // doanh thu hom nay
    $stmt = $pdo->prepare("
    SELECT service_detail
    FROM bookings
    WHERE status IN (2,3)
      AND DATE(created_at) = :today
");

if (!$stmt->execute([':today' => $today])) {
    // in lỗi SQL/ PDO nếu cần debug
    $err = $stmt->errorInfo();
    throw new Exception("DB execute failed: " . ($err[2] ?? 'unknown error'));
}

$revenueToday = 0.0;

// đảm bảo fetch associative
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // debug nhanh (bỏ comment khi không cần)
    // file_put_contents('/tmp/debug_service_detail.txt', $row['service_detail'] . PHP_EOL, FILE_APPEND);

    if (empty($row['service_detail'])) {
        continue;
    }

    $service = json_decode($row['service_detail'], true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        // ghi log để debug nội dung JSON bị lỗi
        $msg = 'JSON decode error: ' . json_last_error_msg() . ' -- value: ' . substr($row['service_detail'], 0, 200);
        error_log($msg);
        continue; // bỏ qua bản ghi lỗi
    }

    // Nếu service là mảng danh sách item: [{...}, {...}]
    if (is_array($service)) {
        // phát hiện mảng list: keys là số nguyên
        $isList = array_keys($service) !== array_keys($service, null) && isset($service[0]);

        if ($isList) {
            foreach ($service as $item) {
                if (is_array($item) && isset($item['total_amount'])) {
                    $revenueToday += (float)$item['total_amount'];
                } elseif (is_numeric($item)) {
                    // nếu item chỉ là số
                    $revenueToday += (float)$item;
                }
            }
        } else {
            // service là associative array: có thể chứa 'total_amount' ở gốc
            if (isset($service['total_amount'])) {
                $revenueToday += (float)$service['total_amount'];
            } else {
                // trường hợp khác: có thể là ['items' => [...]]
                if (isset($service['items']) && is_array($service['items'])) {
                    foreach ($service['items'] as $it) {
                        if (isset($it['total_amount'])) $revenueToday += (float)$it['total_amount'];
                    }
                }
            }
        }
    } else {
        // không phải array sau decode — bỏ qua hoặc log
        error_log('service_detail not an array after json_decode at booking row');
    }
}

$stats['revenue_today'] = $revenueToday;

   //booking gan day

   $stmt = $pdo->query("
   SELECT b.*, t.name AS tour_name, ts.name AS status_name,
   u.name AS created_by_name
   FROM bookings b
   LEFT JOIN tours t ON t.id = b.tour_id
   LEFT JOIN tour_statuses ts ON ts.id = b.status
   LEFT JOIN users u ON u.id = b.created_by
   ORDER BY b.created_at DESC
   LIMIT 10");
  $recentBookings = $stmt->fetchAll();

  // service_detail

  foreach($recentBookings as &$b){
    if (!empty($b['service_detail'])) {
      $service = json_decode($b['service_detail'], true);
      if (is_array($service)){
        $b['total_guests'] = $service['total_guests'] ?? 0;
        $b['total_amount'] = $service['total_amount'] ?? 0;
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
 include view_path('dashbroad.index');
 $content = ob_get_clean();

 view('layouts.AdminLayout',[
  'title' => 'Dashboard',
  'pageTitle' => 'Dashboard',
  'content' => $content,
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

    // lay 12 thang gan nhat

    $months = [];
    $revenues = [];
    for($i =11; $i >= 0; $i--){
      $date = date('Y-m', strtotime("-$i months"));
      $months[] = date('m/Y' , strtotime("-$i months"));

      $stmt = $pdo->prepare("
      SELECT service_detail
      FROM bookings
      WHERE status IN (2, 3)
      AND DATE_FORMAT(created_at, '%Y-%m') = :month
      ");

      $stmt->execute([':month' => $date]);

      $revenue = 0 ;
       while ($row = $stmt->fetch()) {
       if (!empty($row['service_detail'])) {
       $service = json_decode($row['service_detail'], true);
      if (is_array($service) && isset($service['total_amount'])) {
      $revenue += (float)$service['total_amount'];
                    }
                }
            }
            $revenues[] = $revenue;
    }
    header('Content-Type: application/json');
    echo json_encode([
      'months' => $months,
      'revenues' => $revenues,
    ]);
    exit;
}
}