<?php if (!isAdmin()): ?>
    <div class="alert alert-danger">
        <strong>Lỗi!</strong> Bạn không có quyền truy cập trang này.
    </div>
<?php else: ?>
    <div class="alert alert-info">
        <strong>Chỉ dành cho Admin:</strong> Bạn đang có quyền thêm danh mục mới vào hệ thống
    </div>

    <!-- Thông báo lỗi -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Form thêm danh mục -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Thêm Danh mục mới</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= BASE_URL ?>?act=category-store">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên Danh mục <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?= htmlspecialchars($old['name'] ?? '') ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug (URL thân thiện)</label>
                            <input type="text" class="form-control" id="slug" name="slug" 
                                   value="<?= htmlspecialchars($old['slug'] ?? '') ?>" 
                                   placeholder="Tự động tạo từ tên nếu để trống">
                            <small class="form-text text-muted">Ví dụ: tour-trong-nuoc</small>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Mô tả</label>
                    <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="parent_id" class="form-label">Danh mục cha</label>
                            <select class="form-select" id="parent_id" name="parent_id">
                                <option value="">-- Không có (Danh mục gốc) --</option>
                                <?php foreach ($parentCategories as $parent): ?>
                                    <option value="<?= $parent['id'] ?>" 
                                            <?= ($old['parent_id'] ?? '') == $parent['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($parent['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="sort_order" class="form-label">Thứ tự sắp xếp</label>
                            <input type="number" class="form-control" id="sort_order" name="sort_order" 
                                   value="<?= htmlspecialchars($old['sort_order'] ?? '0') ?>" min="0">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select class="form-select" id="status" name="status">
                                <option value="1" <?= ($old['status'] ?? '1') == '1' ? 'selected' : '' ?>>Hoạt động</option>
                                <option value="0" <?= ($old['status'] ?? '') == '0' ? 'selected' : '' ?>>Không hoạt động</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Hình ảnh (URL)</label>
                    <input type="text" class="form-control" id="image" name="image" 
                           value="<?= htmlspecialchars($old['image'] ?? '') ?>" 
                           placeholder="https://example.com/image.jpg">
                </div>

                <div class="d-flex justify-content-between">
                    <a href="<?= BASE_URL ?>?act=categories" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Lưu Danh mục
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Tự động tạo slug từ tên
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
<?php endif; ?>


