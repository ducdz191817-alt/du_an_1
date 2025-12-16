<?php
// Lấy giá trị cũ hoặc giá trị mặc định
$old = $old ?? [];
$errors = $errors ?? [];

// Kiểm tra quyền admin (controller đã xử lý requireAdmin(), nhưng kiểm tra thêm ở view để đảm bảo)
if (!isAdmin()) {
    echo '<div class="alert alert-danger">Bạn không có quyền truy cập trang này. Chỉ Admin mới có quyền thêm tour mới.</div>';
    echo '<a href="' . BASE_URL . '?act=tours" class="btn btn-secondary">Quay lại danh sách</a>';
    return;
}
?>

<!-- Thông báo quyền admin -->
<div class="alert alert-info">
    <i class="bi bi-shield-check"></i> <strong>Chỉ dành cho Admin:</strong> Bạn đang có quyền thêm tour mới vào hệ thống.
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

<form method="POST" action="<?= BASE_URL ?>?act=tour-store">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Thông tin cơ bản</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="name" class="form-label">Tên Tour <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" 
                           value="<?= htmlspecialchars($old['name'] ?? '') ?>" required>
                </div>

                <div class="col-md-12 mb-3">
                    <label for="description" class="form-label">Mô tả <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="description" name="description" rows="4" required><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="category_id" class="form-label">Danh mục <span class="text-danger">*</span></label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        <option value="">-- Chọn danh mục --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" 
                                    <?= ($old['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="price" class="form-label">Giá tour (VNĐ) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="price" name="price" 
                           value="<?= htmlspecialchars($old['price'] ?? '') ?>" min="0" step="1000" required>
                </div>

                

                <div class="col-md-4 mb-3">
                    <label for="duration" class="form-label">Thời lượng</label>
                    <input type="text" class="form-control" id="duration" name="duration" 
                           value="<?= htmlspecialchars($old['duration'] ?? '') ?>" 
                           placeholder="VD: 3 ngày 2 đêm">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="max_guests" class="form-label">Số khách tối đa</label>
                    <input type="number" class="form-control" id="max_guests" name="max_guests" 
                           value="<?= htmlspecialchars($old['max_guests'] ?? '') ?>" min="1">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select class="form-select" id="status" name="status">
                        <option value="1" <?= ($old['status'] ?? '1') == '1' ? 'selected' : '' ?>>Hoạt động</option>
                        <option value="0" <?= ($old['status'] ?? '1') == '0' ? 'selected' : '' ?>>Không hoạt động</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h3 class="card-title">Thông tin bổ sung</h3>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label for="images" class="form-label">Hình ảnh (mỗi ảnh một dòng hoặc cách nhau bằng dấu phẩy)</label>
                <textarea class="form-control" id="images" name="images" rows="3" 
                          placeholder="VD: image1.jpg, image2.jpg"><?= htmlspecialchars($old['images'] ?? '') ?></textarea>
                <small class="text-muted">Nhập tên file ảnh, mỗi ảnh một dòng hoặc cách nhau bằng dấu phẩy</small>
            </div>


            <div class="mb-3">
                <label for="schedule" class="form-label">Lịch trình (JSON)</label>
                <textarea class="form-control" id="schedule" name="schedule" rows="5" 
                          placeholder='{"days": [{"date": "2024-01-01", "activities": ["Hoạt động 1", "Hoạt động 2"]}]}'><?= htmlspecialchars($old['schedule'] ?? '') ?></textarea>
                <small class="text-muted">Nhập dữ liệu JSON cho lịch trình tour</small>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save"></i> Lưu Tour
        </button>
        <a href="<?= BASE_URL ?>?act=tours" class="btn btn-secondary">
            <i class="bi bi-x-circle"></i> Hủy
        </a>
    </div>
</form>

