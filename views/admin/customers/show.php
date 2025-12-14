<?php
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

// Format số tiền
function formatCurrency($amount) {
    if (empty($amount)) return '-';
    return number_format((float)$amount, 0, ',', '.') . ' đ';
}

$customer = $customer ?? null;
if (!$customer) {
    echo '<div class="alert alert-danger">Không tìm thấy khách hàng.</div>';
    echo '<a href="' . BASE_URL . '?act=customers" class="btn btn-secondary">Quay lại danh sách</a>';
    return;
}
?>

<div class="row">
    <!-- Thông tin chính -->
    <div class="col-md-8">
        <!-- Thông tin Khách hàng -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Thông tin Khách hàng #<?= $customer->id ?></h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="200">Họ tên</th>
                        <td><strong><?= htmlspecialchars($customer->name) ?></strong></td>
                    </tr>
                    <tr>
                        <th>Số điện thoại</th>
                        <td>
                            <a href="tel:<?= htmlspecialchars($customer->phone) ?>">
                                <?= htmlspecialchars($customer->phone) ?>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>
                            <?php if (!empty($customer->email)): ?>
                                <a href="mailto:<?= htmlspecialchars($customer->email) ?>">
                                    <?= htmlspecialchars($customer->email) ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Giới tính</th>
                        <td><?= $customer->getGenderText() ?></td>
                    </tr>
                    <tr>
                        <th>Ngày sinh</th>
                        <td><?= $customer->getDateOfBirthFormatted() ?></td>
                    </tr>
                    <tr>
                        <th>Quốc tịch</th>
                        <td><?= htmlspecialchars($customer->nationality ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>Địa chỉ</th>
                        <td><?= htmlspecialchars($customer->address ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>Trạng thái</th>
                        <td>
                            <?php if ($customer->status == 1): ?>
                                <span class="badge bg-success">Hoạt động</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Không hoạt động</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Ngày tạo</th>
                        <td><?= formatDateTime($customer->created_at) ?></td>
                    </tr>
                    <tr>
                        <th>Cập nhật lần cuối</th>
                        <td><?= formatDateTime($customer->updated_at) ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Thông tin công ty -->
        <?php if (!empty($customer->company) || !empty($customer->tax_code)): ?>
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Thông tin Công ty</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <?php if (!empty($customer->company)): ?>
                    <tr>
                        <th width="200">Tên công ty</th>
                        <td><?= htmlspecialchars($customer->company) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($customer->tax_code)): ?>
                    <tr>
                        <th>Mã số thuế</th>
                        <td><?= htmlspecialchars($customer->tax_code) ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Thông tin hộ chiếu -->
        <?php if (!empty($customer->passport_number) || !empty($customer->passport_expiry)): ?>
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Thông tin Hộ chiếu</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <?php if (!empty($customer->passport_number)): ?>
                    <tr>
                        <th width="200">Số hộ chiếu</th>
                        <td><?= htmlspecialchars($customer->passport_number) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($customer->passport_expiry)): ?>
                    <tr>
                        <th>Ngày hết hạn</th>
                        <td><?= $customer->getPassportExpiryFormatted() ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Ghi chú -->
        <?php if (!empty($customer->notes)): ?>
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Ghi chú</h3>
            </div>
            <div class="card-body">
                <p><?= nl2br(htmlspecialchars($customer->notes)) ?></p>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <div class="col-md-4">
        <!-- Thao tác -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Thao tác</h3>
            </div>
            <div class="card-body">
                <?php if (isAdmin()): ?>
                    <a href="<?= BASE_URL ?>?act=customer-edit&id=<?= $customer->id ?>" class="btn btn-warning w-100 mb-2">
                        <i class="bi bi-pencil"></i> Sửa thông tin
                    </a>
                    <button type="button" class="btn btn-danger w-100" onclick="deleteCustomer(<?= $customer->id ?>)">
                        <i class="bi bi-trash"></i> Xóa khách hàng
                    </button>
                <?php endif; ?>
                <a href="<?= BASE_URL ?>?act=customers" class="btn btn-secondary w-100 mt-2">
                    <i class="bi bi-arrow-left"></i> Quay lại danh sách
                </a>
            </div>
        </div>

        <!-- Thống kê -->
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Thống kê</h3>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    <strong>Số booking:</strong> 
                    <span class="badge bg-primary"><?= $customer->getBookingCount() ?></span>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Danh sách Bookings -->
<?php if (!empty($bookings)): ?>
<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title">Danh sách Booking (<?= count($bookings) ?>)</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="50">ID</th>
                        <th>Mã Booking</th>
                        <th>Tour</th>
                        <th>Ngày bắt đầu</th>
                        <th>Ngày kết thúc</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th width="100">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td>#<?= $booking['id'] ?></td>
                            <td>
                                <strong class="text-primary"><?= htmlspecialchars($booking['booking_code'] ?? '-') ?></strong>
                            </td>
                            <td><?= htmlspecialchars($booking['tour_name'] ?? '-') ?></td>
                            <td><?= formatDate($booking['start_date'] ?? '') ?></td>
                            <td><?= formatDate($booking['end_date'] ?? '') ?></td>
                            <td><?= formatCurrency($booking['total_amount'] ?? 0) ?></td>
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
                                <a href="<?= BASE_URL ?>?act=booking-show&id=<?= $booking['id'] ?>" 
                                   class="btn btn-sm btn-info" title="Xem chi tiết">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php else: ?>
<div class="card mt-3">
    <div class="card-body text-center text-muted">
        <i class="bi bi-inbox"></i> Khách hàng này chưa có booking nào
    </div>
</div>
<?php endif; ?>

<!-- Form xóa ẩn -->
<form id="deleteForm" method="POST" action="<?= BASE_URL ?>?act=customer-delete" style="display: none;">
    <input type="hidden" name="id" id="deleteId">
</form>

<script>
function deleteCustomer(id) {
    if (confirm('Bạn có chắc chắn muốn xóa khách hàng này? Khách hàng đang có booking sẽ không thể xóa.')) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteForm').submit();
    }
}
</script>

