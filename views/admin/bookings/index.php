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
?>

<!-- Thông báo thành công/lỗi -->
<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Thành công!</strong> Thao tác đã được thực hiện.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Lỗi!</strong> <?= htmlspecialchars($_GET['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Card chứa bảng -->
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <h3 class="card-title">Danh sách Booking</h3>
            </div>
            <div class="col-md-6 text-end">
                <?php if (isAdmin()): ?>
                    <a href="<?= BASE_URL ?>?act=booking-create" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Tạo Booking mới
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Form tìm kiếm và lọc -->
        <form method="GET" action="<?= BASE_URL ?>?act=bookings" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm..." 
                           value="<?= htmlspecialchars($currentSearch ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <select name="tour_id" class="form-select">
                        <option value="">Tất cả tour</option>
                        <?php foreach ($tours as $tour): ?>
                            <option value="<?= $tour['id'] ?>" 
                                    <?= ($currentTourId ?? '') == $tour['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($tour['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="guide_id" class="form-select">
                        <option value="">Tất cả hướng dẫn viên</option>
                        <?php foreach ($guides as $guide): ?>
                            <option value="<?= $guide['id'] ?>" 
                                    <?= ($currentGuideId ?? '') == $guide['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($guide['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        <?php foreach ($statuses as $status): ?>
                            <option value="<?= $status['id'] ?>" 
                                    <?= ($currentStatus ?? '') == $status['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($status['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-info w-100">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="<?= BASE_URL ?>?act=bookings" class="btn btn-secondary w-100">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
            </div>
        </form>

        <!-- Bảng danh sách -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th width="60">ID</th>
                        <th>Mã Booking</th>
                        <th>Tour</th>
                        <th>Khách hàng</th>
                        <th>Số khách</th>
                        <th>Tổng tiền</th>
                        <th>Ngày đi</th>
                        <th>Hướng dẫn viên</th>
                        <th width="120">Trạng thái</th>
                        <th width="150">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bookings)): ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <i class="bi bi-inbox"></i> Chưa có booking nào
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td>#<?= $booking['id'] ?></td>
                                <td>
                                    <strong class="text-primary"><?= htmlspecialchars($booking['booking_code'] ?? '-') ?></strong>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($booking['tour_name'] ?? '-') ?></strong>
                                </td>
                                <td>
                                    <?php if (!empty($booking['customer'])): ?>
                                        <strong><?= htmlspecialchars($booking['customer']['name'] ?? '-') ?></strong><br>
                                        <small class="text-muted"><?= htmlspecialchars($booking['customer']['phone'] ?? '') ?></small>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($booking['adult']) || !empty($booking['child'])): ?>
                                        <?= $booking['adult'] ?? 0 ?> người lớn
                                        <?php if (!empty($booking['child'])): ?>
                                            <br><small><?= $booking['child'] ?> trẻ em</small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?= $booking['total_guests'] ?? 0 ?> khách
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= formatCurrency($booking['total_amount'] ?? 0) ?>
                                    <?php if (!empty($booking['discount_amount']) && $booking['discount_amount'] > 0): ?>
                                        <br><small class="text-success">-<?= formatCurrency($booking['discount_amount']) ?></small>
                                    <?php endif; ?>
                                    <?php if (!empty($booking['payment_status']) && $booking['payment_status'] != 'pending'): ?>
                                        <br><small class="text-info">(<?= htmlspecialchars($booking['payment_status']) ?>)</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= formatDate($booking['start_date']) ?>
                                    <?php if (!empty($booking['end_date'])): ?>
                                        <br><small>→ <?= formatDate($booking['end_date']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($booking['guide_name'] ?? '-') ?></td>
                                <td>
                                    <?php
                                    $statusId = $booking['status'] ?? 1;
                                    $statusClass = [
                                        1 => 'warning',  // Chờ xác nhận
                                        2 => 'info',     // Đã xác nhận
                                        3 => 'primary',  // Đã cọc
                                        4 => 'success',  // Đang diễn ra
                                        5 => 'secondary', // Hoàn tất
                                        6 => 'danger',   // Hủy
                                    ];
                                    $class = $statusClass[$statusId] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $class ?>">
                                        <?= htmlspecialchars($booking['status_name'] ?? '-') ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= BASE_URL ?>?act=booking-show&id=<?= $booking['id'] ?>" 
                                           class="btn btn-sm btn-info" title="Xem chi tiết">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <?php if (isAdmin()): ?>
                                            <?php
                                            $canDelete = in_array($statusId, [1, 6]); // Chỉ có thể xóa nếu status = 1 (Chờ xác nhận) hoặc 6 (Hủy)
                                            ?>
                                            <?php if ($canDelete): ?>
                                                <button type="button" class="btn btn-sm btn-danger" 
                                                        onclick="deleteBooking(<?= $booking['id'] ?>)" title="Xóa">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Form xóa ẩn -->
<form id="deleteForm" method="POST" action="<?= BASE_URL ?>?act=booking-delete" style="display: none;">
    <input type="hidden" name="id" id="deleteId">
</form>

<script>
function deleteBooking(id) {
    if (confirm('Bạn có chắc chắn muốn xóa booking này? Chỉ có thể xóa booking ở trạng thái "Chờ xác nhận" hoặc "Hủy".')) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteForm').submit();
    }
}
</script>

