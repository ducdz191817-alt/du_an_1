<?php
$old = $old ?? [];
$errors = $errors ?? [];

// Kiểm tra quyền admin
if (!isAdmin()) {
    echo '<div class="alert alert-danger">Bạn không có quyền truy cập trang này. Chỉ Admin mới có quyền tạo hướng dẫn viên.</div>';
    echo '<a href="' . BASE_URL . '?act=users" class="btn btn-secondary">Quay lại danh sách</a>';
    return;
}
?>

<!-- Thông báo quyền admin -->
<div class="alert alert-info">
    <i class="bi bi-shield-check"></i> <strong>Chỉ dành cho Admin:</strong> Bạn đang có quyền tạo hướng dẫn viên mới vào hệ thống.
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

<form method="POST" action="<?= BASE_URL ?>?act=user-store">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Thông tin Hướng dẫn viên</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Họ tên <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" 
                           value="<?= htmlspecialchars($old['name'] ?? '') ?>" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password" name="password" 
                           minlength="6" required>
                    <small class="form-text text-muted">Tối thiểu 6 ký tự</small>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="password_confirm" class="form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password_confirm" name="password_confirm" 
                           minlength="6" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">Số điện thoại</label>
                    <input type="text" class="form-control" id="phone" name="phone" 
                           value="<?= htmlspecialchars($old['phone'] ?? '') ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="role" class="form-label">Vai trò</label>
                    <select class="form-select" id="role" name="role">
                        <option value="guide" <?= ($old['role'] ?? 'guide') == 'guide' ? 'selected' : '' ?>>Hướng dẫn viên</option>
                        <option value="staff" <?= ($old['role'] ?? '') == 'staff' ? 'selected' : '' ?>>Nhân viên</option>
                    </select>
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
        <a href="<?= BASE_URL ?>?act=users" class="btn btn-secondary">
            <i class="bi bi-x-circle"></i> Hủy
        </a>
    </div>
</form>

