<?php ob_start(); ?>

<!--begin::Row-->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-info-circle me-2"></i>
                    Chi tiết Tour: <?= htmlspecialchars($tour['name']) ?>
                </h3>
                <div class="card-tools">
                    <a href="<?= BASE_URL ?>?act=tours/edit&id=<?= $tour['id'] ?>" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil me-1"></i> Sửa
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Tour Info -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <h4>Thông tin cơ bản</h4>
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Tên Tour:</th>
                                <td><?= htmlspecialchars($tour['name']) ?></td>
                            </tr>
                            <tr>
                                <th>Danh mục:</th>
                                <td><?= htmlspecialchars($tour['category_name'] ?? 'N/A') ?></td>
                            </tr>
                            <tr>
                                <th>Địa điểm:</th>
                                <td><?= htmlspecialchars($tour['location']) ?></td>
                            </tr>
                            <tr>
                                <th>Thời gian:</th>
                                <td>
                                    <?= date('d/m/Y', strtotime($tour['start_date'])) ?> 
                                    đến 
                                    <?= date('d/m/Y', strtotime($tour['end_date'])) ?>
                                    (<?= ceil((strtotime($tour['end_date']) - strtotime($tour['start_date'])) / (60 * 60 * 24)) + 1 ?> ngày)
                                </td>
                            </tr>
                            <tr>
                                <th>Giá:</th>
                                <td>
                                    <span class="badge bg-success fs-6">
                                        <?= number_format($tour['price'], 0, ',', '.') ?> ₫
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Hướng dẫn viên:</th>
                                <td><?= htmlspecialchars($tour['guide_name'] ?? 'Chưa phân công') ?></td>
                            </tr>
                            <tr>
                                <th>Số lượng tối đa:</th>
                                <td><?= $tour['max_participants'] ?> người</td>
                            </tr>
                            <tr>
                                <th>Trạng thái:</th>
                                <td>
                                    <?php
                                    $status_classes = [
                                        '1' => 'warning',    // pending
                                        '2' => 'info',       // confirmed
                                        '3' => 'primary',    // active
                                        '4' => 'success',    // completed
                                        '5' => 'danger',     // cancelled
                                    ];
                                    $status_text = [
                                        '1' => 'Chờ xác nhận',
                                        '2' => 'Đã xác nhận',
                                        '3' => 'Đang diễn ra',
                                        '4' => 'Đã hoàn thành',
                                        '5' => 'Đã hủy',
                                    ];
                                    $status_id = $tour['status_id'] ?? 1;
                                    ?>
                                    <span class="badge bg-<?= $status_classes[$status_id] ?? 'secondary' ?> fs-6">
                                        <?= $status_text[$status_id] ?? 'Unknown' ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Ngày tạo:</th>
                                <td><?= date('d/m/Y H:i', strtotime($tour['created_at'])) ?></td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-4">
                        <h4>Thống kê</h4>
                        <div class="list-group">
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                Tổng số booking
                                <span class="badge bg-primary rounded-pill"><?= count($bookings) ?></span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                Booking đã xác nhận
                                <span class="badge bg-success rounded-pill">
                                    <?= count(array_filter($bookings, function($b) { return $b['status'] === 'confirmed'; })) ?>
                                </span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                Booking chờ xác nhận
                                <span class="badge bg-warning rounded-pill">
                                    <?= count(array_filter($bookings, function($b) { return $b['status'] === 'pending'; })) ?>
                                </span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                Booking đã hủy
                                <span class="badge bg-danger rounded-pill">
                                    <?= count(array_filter($bookings, function($b) { return $b['status'] === 'cancelled'; })) ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <h5>Hành động</h5>
                            <div class="d-grid gap-2">
                                <a href="<?= BASE_URL ?>?act=bookings/create&tour_id=<?= $tour['id'] ?>" 
                                   class="btn btn-success">
                                    <i class="bi bi-plus-circle me-1"></i> Tạo booking mới
                                </a>
                                <a href="<?= BASE_URL ?>?act=tours/edit&id=<?= $tour['id'] ?>" 
                                   class="btn btn-warning">
                                    <i class="bi bi-pencil me-1"></i> Sửa thông tin
                                </a>
                                <a href="<?= BASE_URL ?>?act=tours" class="btn btn-secondary">
                                    <i class="bi bi-list me-1"></i> Danh sách tour
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Description -->
                <?php if (!empty($tour['description'])): ?>
                <div class="mb-4">
                    <h4>Mô tả</h4>
                    <div class="card">
                        <div class="card-body">
                            <?= nl2br(htmlspecialchars($tour['description'])) ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Schedule Detail -->
                <?php if (!empty($tour['schedule_detail'])): ?>
                <div class="mb-4">
                    <h4>Lịch trình chi tiết</h4>
                    <div class="card">
                        <div class="card-body">
                            <pre class="mb-0"><?= htmlspecialchars($tour['schedule_detail']) ?></pre>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Bookings -->
                <div class="mb-4">
                    <h4>Danh sách Booking</h4>
                    <?php if (empty($bookings)): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Chưa có booking nào cho tour này.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Khách hàng</th>
                                        <th>HDV phụ trách</th>
                                        <th>Ngày đi</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày tạo</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bookings as $booking): ?>
                                    <tr>
                                        <td>#<?= $booking['id'] ?></td>
                                        <td><?= htmlspecialchars($booking['customer_name'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($booking['guide_name'] ?? 'Chưa phân công') ?></td>
                                        <td><?= date('d/m/Y', strtotime($booking['start_date'])) ?></td>
                                        <td>
                                            <?php
                                            $status_classes = [
                                                'pending' => 'warning',
                                                'confirmed' => 'success',
                                                'cancelled' => 'danger',
                                                'completed' => 'info',
                                            ];
                                            ?>
                                            <span class="badge bg-<?= $status_classes[$booking['status']] ?? 'secondary' ?>">
                                                <?= $booking['status'] ?>
                                            </span>
                                        </td>
                                        <td><?= date('d/m/Y H:i', strtotime($booking['created_at'])) ?></td>
                                        <td>
                                            <a href="<?= BASE_URL ?>?act=bookings/show&id=<?= $booking['id'] ?>" 
                                               class="btn btn-info btn-sm">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-footer">
                <a href="<?= BASE_URL ?>?act=tours" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
                </a>
            </div>
        </div>
    </div>
</div>
<!--end::Row-->

<?php
$content = ob_get_clean();
?>