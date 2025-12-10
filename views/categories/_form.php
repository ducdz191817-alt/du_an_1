<?php
// Partial form for reuse (optional)
// This file can be included in both create.php and edit.php
?>

<div class="mb-3">
    <label for="name" class="form-label required">Tên Danh mục</label>
    <input type="text" class="form-control" id="name" name="name" 
           value="<?= htmlspecialchars($category['name'] ?? ($_POST['name'] ?? '')) ?>" 
           required minlength="3" maxlength="100">
    <div class="form-text">Tên danh mục phải có ít nhất 3 ký tự</div>
</div>

<div class="mb-3">
    <label for="description" class="form-label">Mô tả</label>
    <textarea class="form-control" id="description" name="description" 
              rows="3"><?= htmlspecialchars($category['description'] ?? ($_POST['description'] ?? '')) ?></textarea>
</div>

<div class="mb-4">
    <label for="status" class="form-label">Trạng thái</label>
    <div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="status" 
                   id="status_active" value="active" 
                   <?= (!isset($category['status']) && !isset($_POST['status']) || ($category['status'] ?? ($_POST['status'] ?? 'active')) === 'active') ? 'checked' : '' ?>>
            <label class="form-check-label" for="status_active">
                <span class="badge bg-success">
                    <i class="bi bi-check-circle me-1"></i> Hoạt động
                </span>
            </label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="status" 
                   id="status_inactive" value="inactive"
                   <?= (($category['status'] ?? ($_POST['status'] ?? '')) === 'inactive') ? 'checked' : '' ?>>
            <label class="form-check-label" for="status_inactive">
                <span class="badge bg-danger">
                    <i class="bi bi-x-circle me-1"></i> Ngừng hoạt động
                </span>
            </label>
        </div>
    </div>
</div>