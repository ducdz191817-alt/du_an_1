<?php
// Format số tiền
function formatCurrency($amount) {
    if (empty($amount)) return '-';
    return number_format((float)$amount, 0, ',', '.') . ' đ';
}

// Format ngày tháng
function formatDate($date) {
    if (empty($date)) return '-';
    return date('d/m/Y', strtotime($date));
}

// Format datetime
function formatDateTime($datetime) {
    if (empty($datetime)) return '-';
    return date('d/m/Y H:i', strtotime($datetime));
}
?>

<!-- Thông báo thành công/lỗi -->
<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Thành công!</strong> Trạng thái đã được cập nhật.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Lỗi!</strong> <?= htmlspecialchars($_GET['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <!-- Thông tin chính -->
    <div class="col-md-8">
        <!-- Thông tin Booking -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Thông tin Booking #<?= $booking->id ?></h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="200">Mã Booking</th>
                        <td><strong class="text-primary"><?= htmlspecialchars($booking->booking_code ?? '-') ?></strong></td>
                    </tr>
                    <tr>
                        <th width="200">Tour</th>
                        <td><strong><?= htmlspecialchars($bookingData['tour_name'] ?? '-') ?></strong></td>
                    </tr>
                    <tr>
                        <th>Ngày bắt đầu</th>
                        <td><?= formatDate($booking->start_date) ?></td>
                    </tr>
                    <tr>
                        <th>Ngày kết thúc</th>
                        <td><?= formatDate($booking->end_date) ?></td>
                    </tr>
                    <tr>
                        <th>Hướng dẫn viên</th>
                        <td><?= htmlspecialchars($bookingData['guide_name'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>Trạng thái</th>
                        <td>
                            <?php
                            $statusId = $booking->status ?? 1;
                            $statusClass = [
                                1 => 'warning',
                                2 => 'info',
                                3 => 'success',
                                4 => 'danger',
                            ];
                            $class = $statusClass[$statusId] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?= $class ?> fs-6">
                                <?= htmlspecialchars($bookingData['status_name'] ?? '-') ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Người tạo</th>
                        <td><?= htmlspecialchars($bookingData['created_by_name'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>Ngày tạo</th>
                        <td><?= formatDateTime($booking->created_at) ?></td>
                    </tr>
                    <?php if (!empty($booking->notes)): ?>
                        <tr>
                            <th>Ghi chú</th>
                            <td><?= nl2br(htmlspecialchars($booking->notes)) ?></td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <!-- Thông tin Khách hàng -->
        <?php if (!empty($serviceDetail['customer'])): ?>
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Thông tin Khách hàng</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="200">Tên</th>
                            <td><strong><?= htmlspecialchars($serviceDetail['customer']['name'] ?? '-') ?></strong></td>
                        </tr>
                        <tr>
                            <th>Số điện thoại</th>
                            <td><?= htmlspecialchars($serviceDetail['customer']['phone'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td><?= htmlspecialchars($serviceDetail['customer']['email'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <th>Địa chỉ</th>
                            <td><?= htmlspecialchars($serviceDetail['customer']['address'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <th>Loại booking</th>
                            <td>
                                <?php
                                $bookingType = $serviceDetail['booking_type'] ?? 'individual';
                                echo $bookingType == 'group' ? 'Đoàn' : 'Cá nhân';
                                ?>
                            </td>
                        </tr>
                        <?php if (!empty($serviceDetail['special_requirements'])): ?>
                            <tr>
                                <th>Yêu cầu đặc biệt</th>
                                <td><?= htmlspecialchars($serviceDetail['special_requirements']) ?></td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        <?php endif; ?>

        <!-- Thông tin thanh toán -->
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Thông tin Thanh toán</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="200">Số người lớn</th>
                        <td><?= $serviceDetail['adult'] ?? 0 ?></td>
                    </tr>
                    <tr>
                        <th>Số trẻ em</th>
                        <td><?= $serviceDetail['child'] ?? 0 ?></td>
                    </tr>
                    <tr>
                        <th>Tổng số khách</th>
                        <td><strong><?= $serviceDetail['total_guests'] ?? 0 ?></strong></td>
                    </tr>
                    <tr>
                        <th>Giá người lớn</th>
                        <td><?= formatCurrency($serviceDetail['adult_price'] ?? 0) ?></td>
                    </tr>
                    <tr>
                        <th>Giá trẻ em</th>
                        <td><?= formatCurrency($serviceDetail['child_price'] ?? 0) ?></td>
                    </tr>
                    <tr>
                        <th><strong>Tổng tiền</strong></th>
                        <td><strong class="text-success fs-5"><?= formatCurrency($booking->total_amount ?? $serviceDetail['total_amount'] ?? 0) ?></strong></td>
                    </tr>
                    <?php if (!empty($booking->discount_amount) && $booking->discount_amount > 0): ?>
                        <tr>
                            <th>Giảm giá</th>
                            <td class="text-success">
                                -<?= formatCurrency($booking->discount_amount) ?>
                                <?php if (!empty($booking->discount_code)): ?>
                                    <br><small>(Mã: <?= htmlspecialchars($booking->discount_code) ?>)</small>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <th>Đã thanh toán</th>
                        <td class="text-info"><?= formatCurrency($booking->paid_amount ?? 0) ?></td>
                    </tr>
                    <tr>
                        <th>Còn lại</th>
                        <td class="text-warning"><strong><?= formatCurrency($booking->remaining_amount ?? 0) ?></strong></td>
                    </tr>
                    <tr>
                        <th>Trạng thái thanh toán</th>
                        <td>
                            <?php
                            $paymentStatusClass = [
                                'pending' => 'warning',
                                'partial' => 'info',
                                'paid' => 'success',
                                'refunded' => 'danger',
                            ];
                            $paymentClass = $paymentStatusClass[$booking->payment_status ?? 'pending'] ?? 'secondary';
                            $paymentStatusText = [
                                'pending' => 'Chờ thanh toán',
                                'partial' => 'Thanh toán một phần',
                                'paid' => 'Đã thanh toán',
                                'refunded' => 'Đã hoàn tiền',
                            ];
                            ?>
                            <span class="badge bg-<?= $paymentClass ?>">
                                <?= $paymentStatusText[$booking->payment_status ?? 'pending'] ?? 'Chờ thanh toán' ?>
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Danh sách khách -->
        <?php if (!empty($guests)): ?>
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Danh sách Khách</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Họ tên</th>
                                    <th>Ngày sinh</th>
                                    <th>Giới tính</th>
                                    <th>Số hộ chiếu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($guests as $index => $guest): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($guest['fullname']) ?></td>
                                        <td><?= formatDate($guest['dob']) ?></td>
                                        <td><?= htmlspecialchars($guest['gender'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($guest['passport_number'] ?? '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Lịch sử thay đổi trạng thái -->
        <?php if (!empty($statusLogs)): ?>
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">Lịch sử Thay đổi Trạng thái</h3>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <?php foreach ($statusLogs as $log): ?>
                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>
                                            <?= htmlspecialchars($log['old_status_name'] ?? 'N/A') ?> 
                                            → 
                                            <?= htmlspecialchars($log['new_status_name'] ?? 'N/A') ?>
                                        </strong>
                                        <?php if (!empty($log['note'])): ?>
                                            <br><small class="text-muted"><?= htmlspecialchars($log['note']) ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">
                                            <?= htmlspecialchars($log['changed_by_name'] ?? 'System') ?><br>
                                            <?= formatDateTime($log['changed_at']) ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <div class="col-md-4">
        <!-- Cập nhật trạng thái -->
        <?php if (isAdmin()): ?>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Cập nhật Trạng thái</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= BASE_URL ?>?act=booking-update-status">
                        <input type="hidden" name="id" value="<?= $booking->id ?>">
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Trạng thái mới</label>
                            <select class="form-select" id="status" name="status" required>
                                <?php
                                $pdo = getDB();
                                $statusesStmt = $pdo->query('SELECT * FROM tour_statuses ORDER BY id');
                                $statuses = $statusesStmt->fetchAll();
                                ?>
                                <?php foreach ($statuses as $status): ?>
                                    <option value="<?= $status['id'] ?>" 
                                            <?= $booking->status == $status['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($status['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="note" class="form-label">Ghi chú (tùy chọn)</label>
                            <textarea class="form-control" id="note" name="note" rows="3"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-circle"></i> Cập nhật Trạng thái
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- Thao tác -->
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Thao tác</h3>
            </div>
            <div class="card-body">
                <a href="<?= BASE_URL ?>?act=bookings" class="btn btn-secondary w-100 mb-2">
                    <i class="bi bi-arrow-left"></i> Quay lại danh sách
                </a>
                <?php if (isAdmin() && $booking->canDelete()): ?>
                    <form method="POST" action="<?= BASE_URL ?>?act=booking-delete" onsubmit="return confirm('Bạn có chắc chắn muốn xóa booking này?');">
                        <input type="hidden" name="id" value="<?= $booking->id ?>">
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="bi bi-trash"></i> Xóa Booking
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

