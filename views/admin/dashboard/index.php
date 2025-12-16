<?php
// Dashboard view - Hiển thị thống kê và thông tin tổng quan

// Format số tiền
function formatCurrency($amount) {
    return number_format($amount, 0, ',', '.') . ' đ';
}

// Format ngày tháng
function formatDate($date) {
    if (empty($date)) return '-';
    return date('d/m/Y', strtotime($date));
}
?>

<div class="row">
    <!-- Thống kê tổng quan -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?= $stats['total_tours'] ?? 0 ?></h3>
                <p>Tổng số Tour</p>
            </div>
            <div class="icon">
                <i class="bi bi-map"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?= $stats['total_bookings'] ?? 0 ?></h3>
                <p>Tổng số Booking</p>
            </div>
            <div class="icon">
                <i class="bi bi-calendar-check"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>3</h3>
                <p>Hướng dẫn viên</p>
            </div>
            <div class="icon">
                <i class="bi bi-people"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3><?= formatCurrency($stats['revenue_this_month'] ?? 0) ?></h3>
                <p>Doanh thu tháng này</p>
            </div>
            <div class="icon">
                <i class="bi bi-cash-coin"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Thống kê booking -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Thống kê Booking</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="description-block border-right">
                            <span class="description-percentage text-warning">
                                <i class="bi bi-clock-history"></i>
                            </span>
                            <h5 class="description-header"><?= $stats['pending_bookings'] ?? 0 ?></h5>
                            <span class="description-text">Chờ xác nhận</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="description-block">
                            <span class="description-percentage text-info">
                                <i class="bi bi-check-circle"></i>
                            </span>
                            <h5 class="description-header"><?= $stats['active_bookings'] ?? 0 ?></h5>
                            <span class="description-text">Đang hoạt động</span>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-6">
                        <div class="description-block border-right">
                            <span class="description-percentage text-success">
                                <i class="bi bi-check2-all"></i>
                            </span>
                            <h5 class="description-header"><?= $stats['completed_bookings'] ?? 0 ?></h5>
                            <span class="description-text">Hoàn tất</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="description-block">
                            <span class="description-percentage text-primary">
                                <i class="bi bi-calendar-day"></i>
                            </span>
                            <h5 class="description-header"><?= $stats['new_bookings_today'] ?? 0 ?></h5>
                            <span class="description-text">Mới hôm nay</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Thống kê doanh thu -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Thống kê Doanh thu</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="description-block">
                            <h5 class="description-header text-success"><?= formatCurrency($stats['revenue_today'] ?? 0) ?></h5>
                            <span class="description-text">Hôm nay</span>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-6">
                        <div class="description-block border-right">
                            <h5 class="description-header text-info"><?= formatCurrency($stats['revenue_this_month'] ?? 0) ?></h5>
                            <span class="description-text">Tháng này</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="description-block">
                            <h5 class="description-header"><?= formatCurrency($stats['revenue_last_month'] ?? 0) ?></h5>
                            <span class="description-text">Tháng trước</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Booking gần đây -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Booking gần đây</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tour</th>
                            <th>Trạng thái</th>
                            <th>Số khách</th>
                            <th>Tổng tiền</th>
                            <th>Ngày tạo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentBookings)): ?>
                            <tr>
                                <td colspan="6" class="text-center">Chưa có booking nào</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentBookings as $booking): ?>
                                <tr>
                                    <td>#<?= $booking['id'] ?></td>
                                    <td><?= htmlspecialchars($booking['tour_name'] ?? '-') ?></td>
                                    <td>
                                        <span class="badge bg-<?= 
                                            $booking['status'] == 1 ? 'warning' : 
                                            ($booking['status'] == 2 ? 'info' : 
                                            ($booking['status'] == 3 ? 'success' : 'danger')) 
                                        ?>">
                                            <?= htmlspecialchars($booking['status_name'] ?? '-') ?>
                                        </span>
                                    </td>
                                    <td><?= $booking['total_guests'] ?? 0 ?></td>
                                    <td><?= formatCurrency($booking['total_amount'] ?? 0) ?></td>
                                    <td><?= formatDate($booking['created_at'] ?? '') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Thống kê theo trạng thái -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Booking theo trạng thái</h3>
            </div>
            <div class="card-body">
                <?php if (empty($bookingStatusStats)): ?>
                    <p class="text-muted">Chưa có dữ liệu</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($bookingStatusStats as $stat): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?= htmlspecialchars($stat['name']) ?>
                                <span class="badge bg-primary rounded-pill"><?= $stat['count'] ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

