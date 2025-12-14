<?php
$old = $old ?? [];
$errors = $errors ?? [];

// Kiểm tra quyền admin
if (!isAdmin()) {
    echo '<div class="alert alert-danger">Bạn không có quyền truy cập trang này. Chỉ Admin mới có quyền tạo khách hàng.</div>';
    echo '<a href="' . BASE_URL . '?act=customers" class="btn btn-secondary">Quay lại danh sách</a>';
    return;
}
?>

<!-- Thông báo quyền admin -->
<div class="alert alert-info">
    <i class="bi bi-shield-check"></i> <strong>Chỉ dành cho Admin:</strong> Bạn đang có quyền tạo khách hàng mới vào hệ thống.
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

<form method="POST" action="<?= BASE_URL ?>?act=customer-store">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Thông tin cơ bản</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Họ tên <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" 
                           value="<?= htmlspecialchars($old['name'] ?? '') ?>" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="phone" name="phone" 
                           value="<?= htmlspecialchars($old['phone'] ?? '') ?>" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?= htmlspecialchars($old['email'] ?? '') ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="gender" class="form-label">Giới tính</label>
                    <select class="form-select" id="gender" name="gender">
                        <option value="">-- Chọn giới tính --</option>
                        <option value="male" <?= ($old['gender'] ?? '') == 'male' ? 'selected' : '' ?>>Nam</option>
                        <option value="female" <?= ($old['gender'] ?? '') == 'female' ? 'selected' : '' ?>>Nữ</option>
                        <option value="other" <?= ($old['gender'] ?? '') == 'other' ? 'selected' : '' ?>>Khác</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="date_of_birth" class="form-label">Ngày sinh</label>
                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                           value="<?= htmlspecialchars($old['date_of_birth'] ?? '') ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="nationality" class="form-label">Quốc tịch</label>
                    <input type="text" class="form-control" id="nationality" name="nationality" 
                           value="<?= htmlspecialchars($old['nationality'] ?? '') ?>" placeholder="VD: Việt Nam">
                </div>

                <div class="col-md-12 mb-3">
                    <label for="address" class="form-label">Địa chỉ</label>
                    <textarea class="form-control" id="address" name="address" rows="2"><?= htmlspecialchars($old['address'] ?? '') ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h3 class="card-title">Thông tin công ty (nếu có)</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="company" class="form-label">Tên công ty</label>
                    <input type="text" class="form-control" id="company" name="company" 
                           value="<?= htmlspecialchars($old['company'] ?? '') ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="tax_code" class="form-label">Mã số thuế</label>
                    <input type="text" class="form-control" id="tax_code" name="tax_code" 
                           value="<?= htmlspecialchars($old['tax_code'] ?? '') ?>">
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h3 class="card-title">Thông tin hộ chiếu (nếu có)</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="passport_number" class="form-label">Số hộ chiếu</label>
                    <input type="text" class="form-control" id="passport_number" name="passport_number" 
                           value="<?= htmlspecialchars($old['passport_number'] ?? '') ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="passport_expiry" class="form-label">Ngày hết hạn</label>
                    <input type="date" class="form-control" id="passport_expiry" name="passport_expiry" 
                           value="<?= htmlspecialchars($old['passport_expiry'] ?? '') ?>">
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h3 class="card-title">Thông tin khác</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="notes" class="form-label">Ghi chú</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"><?= htmlspecialchars($old['notes'] ?? '') ?></textarea>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select class="form-select" id="status" name="status">
                        <option value="1" <?= ($old['status'] ?? '1') == '1' ? 'selected' : '' ?>>Hoạt động</option>
                        <option value="0" <?= ($old['status'] ?? '') == '0' ? 'selected' : '' ?>>Không hoạt động</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save"></i> Lưu
        </button>
        <a href="<?= BASE_URL ?>?act=customers" class="btn btn-secondary">
            <i class="bi bi-x-circle"></i> Hủy
        </a>
    </div>
</form>

