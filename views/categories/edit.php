<?php ob_start(); ?>

<!--begin::Row-->
<div class="row">
    <div class="col-12 col-md-8 col-lg-6 mx-auto">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-pencil me-2"></i>
                    Sửa Danh mục: <?= htmlspecialchars($category['name']) ?>
                </h3>
            </div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h5><i class="bi bi-exclamation-triangle-fill me-2"></i> Có lỗi xảy ra:</h5>
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Stats -->
                <div class="alert alert-info mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Thông tin danh mục:</strong>
                        </div>
                        <div>
                            <span class="badge bg-primary">
                                <?= $category['tour_count'] ?> tour
                            </span>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small>
                            Ngày tạo: <?= date('d/m/Y H:i', strtotime($category['created_at'])) ?>
                            <?php if ($category['updated_at'] !== $category['created_at']): ?>
                                • Cập nhật: <?= date('d/m/Y H:i', strtotime($category['updated_at'])) ?>
                            <?php endif; ?>
                        </small>
                    </div>
                </div>
                
                <form method="POST" action="<?= BASE_URL ?>?act=categories/update" novalidate>
                    <input type="hidden" name="id" value="<?= $category['id'] ?>">
                    
                    <!-- Category Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label required">Tên Danh mục</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= htmlspecialchars($category['name']) ?>" 
                               required minlength="3" maxlength="100">
                        <div class="form-text">Tên danh mục phải có ít nhất 3 ký tự</div>
                    </div>
                    
                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="description" name="description" 
                                  rows="3"><?= htmlspecialchars($category['description']) ?></textarea>
                    </div>
                    
                    <!-- Status -->
                    <div class="mb-4">
                        <label for="status" class="form-label">Trạng thái</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" 
                                       id="status_active" value="active" 
                                       <?= ($category['status'] === 'active' || $category['status'] == 1) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="status_active">
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i> Hoạt động
                                    </span>
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" 
                                       id="status_inactive" value="inactive"
                                       <?= ($category['status'] === 'inactive' || $category['status'] == 0) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="status_inactive">
                                    <span class="badge bg-danger">
                                        <i class="bi bi-x-circle me-1"></i> Ngừng hoạt động
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="form-text">
                            <?php if (($category['status'] === 'active' || $category['status'] == 1) && $category['tour_count'] > 0): ?>
                                <i class="bi bi-exclamation-triangle text-warning me-1"></i>
                                Có <?= $category['tour_count'] ?> tour đang sử dụng danh mục này.
                                Nếu chuyển sang "Ngừng hoạt động", danh mục sẽ không hiển thị khi tạo tour mới.
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="d-flex justify-content-between">
                        <a href="<?= BASE_URL ?>?act=categories" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Quay lại
                        </a>
                        <div class="btn-group">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-1"></i> Lưu thay đổi
                            </button>
                            <?php if ($category['tour_count'] === 0): ?>
                                <button type="button" class="btn btn-danger" 
                                        onclick="confirmDelete(<?= $category['id'] ?>, '<?= htmlspecialchars(addslashes($category['name'])) ?>')">
                                    <i class="bi bi-trash me-1"></i> Xóa
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--end::Row-->

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa danh mục <strong id="categoryName"></strong>?</p>
                <p class="text-danger">
                    <small>
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        Hành động này không thể hoàn tác!
                    </small>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <input type="hidden" name="id" id="deleteId">
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    document.getElementById('deleteId').value = id;
    document.getElementById('categoryName').textContent = name;
    document.getElementById('deleteForm').action = '<?= BASE_URL ?>?act=categories/delete';
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});
</script>

<?php
$content = ob_get_clean();
?>