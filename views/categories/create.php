<?php ob_start(); ?>

<!--begin::Row-->
<div class="row">
    <div class="col-12 col-md-8 col-lg-6 mx-auto">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-plus-circle me-2"></i>
                    Thêm Danh mục mới
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
                
                <form method="POST" action="<?= BASE_URL ?>?act=categories/store" novalidate>
                    <!-- Category Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label required">Tên Danh mục</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" 
                               required minlength="3" maxlength="100" 
                               placeholder="VD: Tour Du Lịch Biển Đảo">
                        <div class="form-text">Tên danh mục phải có ít nhất 3 ký tự</div>
                    </div>
                    
                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="description" name="description" 
                                  rows="3" placeholder="Mô tả chi tiết về danh mục này..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    </div>
                    
                    <!-- Status -->
                    <div class="mb-4">
                        <label for="status" class="form-label">Trạng thái</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" 
                                       id="status_active" value="active" 
                                       <?= (!isset($_POST['status']) || $_POST['status'] === 'active') ? 'checked' : '' ?>>
                                <label class="form-check-label" for="status_active">
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i> Hoạt động
                                    </span>
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" 
                                       id="status_inactive" value="inactive"
                                       <?= (isset($_POST['status']) && $_POST['status'] === 'inactive') ? 'checked' : '' ?>>
                                <label class="form-check-label" for="status_inactive">
                                    <span class="badge bg-danger">
                                        <i class="bi bi-x-circle me-1"></i> Ngừng hoạt động
                                    </span>
                                </label>
                            </div>
                        </div>
                        <div class="form-text">Danh mục ngừng hoạt động sẽ không hiển thị khi tạo tour mới</div>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="d-flex justify-content-between">
                        <a href="<?= BASE_URL ?>?act=categories" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Quay lại
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i> Thêm Danh mục
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--end::Row-->

<script>
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