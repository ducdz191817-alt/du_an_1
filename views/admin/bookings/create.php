<?php
$old = $old ?? [];
$errors = $errors ?? [];

// Kiểm tra quyền admin
if (!isAdmin()) {
    echo '<div class="alert alert-danger">Bạn không có quyền truy cập trang này. Chỉ Admin mới có quyền tạo booking.</div>';
    echo '<a href="' . BASE_URL . '?act=bookings" class="btn btn-secondary">Quay lại danh sách</a>';
    return;
}
?>

<!-- Thông báo quyền admin -->
<div class="alert alert-info">
    <i class="bi bi-shield-check"></i> <strong>Chỉ dành cho Admin:</strong> Bạn đang có quyền tạo booking mới vào hệ thống.
</div>

<!-- Hiển thị lỗi -->
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" action="<?= BASE_URL ?>?act=booking-store" id="bookingForm">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Thông tin Tour</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="tour_id" class="form-label">Tour <span class="text-danger">*</span></label>
                    <select class="form-select" id="tour_id" name="tour_id" required>
                        <option value="">-- Chọn tour --</option>
                        <?php foreach ($tours as $tour): ?>
                            <option value="<?= $tour['id'] ?>" 
                                    data-price="<?= $tour['price'] ?>"
                                    <?= ($old['tour_id'] ?? '') == $tour['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($tour['name']) ?> - <?= number_format($tour['price'], 0, ',', '.') ?> đ
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="start_date" class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="start_date" name="start_date" 
                           value="<?= htmlspecialchars($old['start_date'] ?? '') ?>" required>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="end_date" class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="end_date" name="end_date" 
                           value="<?= htmlspecialchars($old['end_date'] ?? '') ?>" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="assigned_guide_id" class="form-label">Hướng dẫn viên</label>
                    <select class="form-select" id="assigned_guide_id" name="assigned_guide_id">
                        <option value="">-- Chọn hướng dẫn viên --</option>
                        <?php foreach ($guides as $guide): ?>
                            <option value="<?= $guide['id'] ?>" 
                                    <?= ($old['assigned_guide_id'] ?? '') == $guide['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($guide['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select class="form-select" id="status" name="status">
                        <?php foreach ($statuses as $status): ?>
                            <option value="<?= $status['id'] ?>" 
                                    <?= ($old['status'] ?? '1') == $status['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($status['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h3 class="card-title">Thông tin Khách hàng</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="customer_id" class="form-label">Khách hàng có sẵn (hoặc tạo mới bên dưới)</label>
                    <select class="form-select" id="customer_id" name="customer_id">
                        <option value="">-- Tạo khách hàng mới --</option>
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?= $customer['id'] ?>" 
                                    data-name="<?= htmlspecialchars($customer['name']) ?>"
                                    data-phone="<?= htmlspecialchars($customer['phone']) ?>"
                                    data-email="<?= htmlspecialchars($customer['email'] ?? '') ?>"
                                    data-address="<?= htmlspecialchars($customer['address'] ?? '') ?>">
                                <?= htmlspecialchars($customer['name']) ?> - <?= htmlspecialchars($customer['phone']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="booking_type" class="form-label">Loại booking</label>
                    <select class="form-select" id="booking_type" name="booking_type">
                        <option value="individual" <?= ($old['booking_type'] ?? 'individual') == 'individual' ? 'selected' : '' ?>>Cá nhân</option>
                        <option value="group" <?= ($old['booking_type'] ?? '') == 'group' ? 'selected' : '' ?>>Đoàn</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="customer_name" class="form-label">Tên khách hàng <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="customer_name" name="customer_name" 
                           value="<?= htmlspecialchars($old['customer_name'] ?? '') ?>" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="customer_phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="customer_phone" name="customer_phone" 
                           value="<?= htmlspecialchars($old['customer_phone'] ?? '') ?>" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="customer_email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="customer_email" name="customer_email" 
                           value="<?= htmlspecialchars($old['customer_email'] ?? '') ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="customer_address" class="form-label">Địa chỉ</label>
                    <input type="text" class="form-control" id="customer_address" name="customer_address" 
                           value="<?= htmlspecialchars($old['customer_address'] ?? '') ?>">
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h3 class="card-title">Thông tin Booking</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="adult" class="form-label">Số người lớn <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="adult" name="adult" 
                           value="<?= htmlspecialchars($old['adult'] ?? '1') ?>" min="0" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="child" class="form-label">Số trẻ em</label>
                    <input type="number" class="form-control" id="child" name="child" 
                           value="<?= htmlspecialchars($old['child'] ?? '0') ?>" min="0">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="special_requirements" class="form-label">Yêu cầu đặc biệt</label>
                    <input type="text" class="form-control" id="special_requirements" name="special_requirements" 
                           value="<?= htmlspecialchars($old['special_requirements'] ?? '') ?>" 
                           placeholder="VD: Bệnh lý, dị ứng...">
                </div>

                <div class="col-md-12 mb-3">
                    <label for="notes" class="form-label">Ghi chú</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"><?= htmlspecialchars($old['notes'] ?? '') ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save"></i> Tạo Booking
        </button>
        <a href="<?= BASE_URL ?>?act=bookings" class="btn btn-secondary">
            <i class="bi bi-x-circle"></i> Hủy
        </a>
    </div>
</form>

<script>
// Tự động điền thông tin khách hàng khi chọn từ dropdown
document.getElementById('customer_id').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    if (option.value && option.dataset.name) {
        document.getElementById('customer_name').value = option.dataset.name || '';
        document.getElementById('customer_phone').value = option.dataset.phone || '';
        document.getElementById('customer_email').value = option.dataset.email || '';
        document.getElementById('customer_address').value = option.dataset.address || '';
    }
});
</script>

