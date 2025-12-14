<?php
$category = $category ?? null;
$old = $old ?? [];
$errors = $errors ?? [];
$parentCategories = $parentCategories ?? [];

// Kiểm tra quyền admin
if (!isAdmin()) {
    echo '<div class="alert alert-danger">Bạn không có quyền truy cập trang này. Chỉ Admin mới có quyền sửa danh mục.</div>';
    echo '<a href="' . BASE_URL . '?act=categories" class="btn btn-secondary">Quay lại danh sách</a>';
    return;
}

if (!$category) {
    echo '<div class="alert alert-danger">Không tìm thấy danh mục.</div>';
    echo '<a href="' . BASE_URL . '?act=categories" class="btn btn-secondary">Quay lại danh sách</a>';
    return;
}
?>

<div class="alert alert-info">
    <i class="bi bi-shield-check"></i> <strong>Chỉ dành cho Admin:</strong> Bạn đang có quyền sửa thông tin danh mục.
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

<!-- Form sửa danh mục -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Sửa Danh mục</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= BASE_URL ?>?act=category-update">
            <input type="hidden" name="id" value="<?= $category->id ?>">
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên Danh mục <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= htmlspecialchars((string)($old['name'] ?? $category->name ?? '')) ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug (URL thân thiện)</label>
                        <input type="text" class="form-control" id="slug" name="slug" 
                               value="<?= htmlspecialchars((string)($old['slug'] ?? $category->slug ?? '')) ?>" 
                               placeholder="Tự động tạo từ tên nếu để trống">
                        <small class="form-text text-muted">Ví dụ: tour-trong-nuoc</small>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Mô tả</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars((string)($old['description'] ?? $category->description ?? '')) ?></textarea>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="parent_id" class="form-label">Danh mục cha</label>
                        <select class="form-select" id="parent_id" name="parent_id">
                            <option value="">-- Không có (Danh mục gốc) --</option>
                            <?php foreach ($parentCategories as $parent): ?>
                                <?php if ($parent['id'] != $category->id): ?>
                                    <option value="<?= $parent['id'] ?>" 
                                            <?= (($old['parent_id'] ?? $category->parent_id ?? '') == $parent['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($parent['name']) ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">Không thể chọn chính danh mục này làm danh mục cha</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="sort_order" class="form-label">Thứ tự sắp xếp</label>
                        <input type="number" class="form-control" id="sort_order" name="sort_order" 
                               value="<?= htmlspecialchars((string)($old['sort_order'] ?? $category->sort_order ?? '0')) ?>" min="0">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select class="form-select" id="status" name="status">
                            <option value="1" <?= (($old['status'] ?? $category->status ?? 1)) == 1 ? 'selected' : '' ?>>Hoạt động</option>
                            <option value="0" <?= (($old['status'] ?? $category->status ?? 1)) == 0 ? 'selected' : '' ?>>Không hoạt động</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Hình ảnh (URL)</label>
                <input type="text" class="form-control" id="image" name="image" 
                       value="<?= htmlspecialchars((string)($old['image'] ?? $category->image ?? '')) ?>" 
                       placeholder="https://example.com/image.jpg">
            </div>

            <div class="d-flex justify-content-between">
                <a href="<?= BASE_URL ?>?act=categories" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Cập nhật Danh mục
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Tự động tạo slug từ tên (chỉ khi slug trống)
document.getElementById('name').addEventListener('input', function() {
    const slugInput = document.getElementById('slug');
    if (!slugInput.value || slugInput.dataset.autoGenerated === 'true') {
        const name = this.value;
        const slug = name.toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/đ/g, 'd').replace(/Đ/g, 'D')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        slugInput.value = slug;
        slugInput.dataset.autoGenerated = 'true';
    }
});

// Cho phép chỉnh sửa slug thủ công
document.getElementById('slug').addEventListener('input', function() {
    this.dataset.autoGenerated = 'false';
});
</script>

