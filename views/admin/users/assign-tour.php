<?php
// Format ngày tháng
function formatDate($date) {
    if (empty($date)) return '-';
    return date('d/m/Y', strtotime($date));
}

// Format số tiền
function formatCurrency($amount) {
    if (empty($amount)) return '-';
    return number_format((float)$amount, 0, ',', '.') . ' đ';
}
?>

<!-- Thông báo thành công/lỗi -->
<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Thành công!</strong> Phân công tour đã được cập nhật.
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
                <h3 class="card-title">Phân công Tour cho HDV</h3>
            </div>
            <div class="col-md-6 text-end">
                <a href="<?= BASE_URL ?>?act=users" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Form tìm kiếm và lọc -->
        <form method="GET" action="<?= BASE_URL ?>" class="mb-3">
            <input type="hidden" name="act" value="user-assign-tour">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm tour, mã booking..." 
                           value="<?= htmlspecialchars($currentSearch ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <select name="guide_id" class="form-select">
                        <option value="">Tất cả HDV</option>
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
                <div class="col-md-2">
                    <button type="submit" class="btn btn-info w-100">
                        <i class="bi bi-search"></i> Tìm kiếm
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="<?= BASE_URL ?>?act=user-assign-tour" class="btn btn-secondary w-100">
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
                        <th width="50">ID</th>
                        <th>Mã Booking</th>
                        <th>Tour</th>
                        <th>Ngày bắt đầu</th>
                        <th>Ngày kết thúc</th>
                        <th>HDV hiện tại</th>
                        <th>Trạng thái</th>
                        <th width="200">Phân công HDV</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bookings)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
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
                                <td><?= htmlspecialchars($booking['tour_name'] ?? '-') ?></td>
                                <td><?= formatDate($booking['start_date'] ?? '') ?></td>
                                <td><?= formatDate($booking['end_date'] ?? '') ?></td>
                                <td>
                                    <?php if (!empty($booking['guide_name'])): ?>
                                        <span class="badge bg-info"><?= htmlspecialchars($booking['guide_name']) ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Chưa phân công</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $statusId = $booking['status'] ?? 1;
                                    $statusClass = [
                                        1 => 'warning',
                                        2 => 'info',
                                        3 => 'success',
                                        4 => 'danger',
                                        5 => 'secondary',
                                        6 => 'dark',
                                    ];
                                    $class = $statusClass[$statusId] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $class ?>">
                                        <?= htmlspecialchars($booking['status_name'] ?? '-') ?>
                                    </span>
                                </td>
                                <td>
                                    <form method="POST" action="<?= BASE_URL ?>?act=user-assign-tour-store" class="d-flex gap-2">
                                        <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                                        <select name="guide_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <option value="">-- Bỏ phân công --</option>
                                            <?php foreach ($guides as $guide): ?>
                                                <option value="<?= $guide['id'] ?>" 
                                                        <?= ($booking['guide_id'] ?? '') == $guide['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($guide['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

