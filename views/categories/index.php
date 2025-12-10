<?php ob_start(); ?>

<!--begin::Row-->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-tags me-2"></i>
                    Danh sách Danh mục Tour
                </h3>
                <div class="card-tools">
                    <a href="<?= BASE_URL ?>?act=categories/create" class="btn btn-success btn-sm">
                        <i class="bi bi-plus-circle me-1"></i> Thêm Danh mục
                    </a>
                </div>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <?= htmlspecialchars($_SESSION['success']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle-fill me-2"></i>
                        <?= htmlspecialchars($_SESSION['error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
                
                <?php if (empty($categories)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Chưa có danh mục nào. 
                        <a href="<?= BASE_URL ?>?act=categories/create" class="alert-link">Thêm danh mục đầu tiên</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Tên Danh mục</th>
                                    <th>Mô tả</th>
                                    <th>Số Tour</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tạo</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?= $category['id'] ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($category['name']) ?></strong>
                                    </td>
                                    <td>
                                        <?php if (!empty($category['description'])): ?>
                                            <?= htmlspecialchars(substr($category['description'], 0, 100)) ?>
                                            <?php if (strlen($category['description']) > 100): ?>...<?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted">Không có mô tả</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary rounded-pill">
                                            <?= $category['tour_count'] ?> tour
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($category['status'] === 'active' || $category['status'] == 1): ?>
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i> Hoạt động
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">
                                                <i class="bi bi-x-circle me-1"></i> Ngừng hoạt động
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= date('d/m/Y', strtotime($category['created_at'])) ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="<?= BASE_URL ?>?act=categories/edit&id=<?= $category['id'] ?>" 
                                               class="btn btn-warning" title="Sửa">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger" 
                                                    title="Xóa" 
                                                    onclick="confirmDelete(<?= $category['id'] ?>, '<?= htmlspecialchars(addslashes($category['name'])) ?>')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Tổng cộng: <strong><?= count($categories) ?></strong> danh mục
                    </div>
                    <div>
                        <a href="<?= BASE_URL ?>?act=categories/create" class="btn btn-success">
                            <i class="bi bi-plus-circle me-1"></i> Thêm Danh mục
                        </a>
                    </div>
                </div>
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
                        Cảnh báo: Không thể xóa danh mục nếu đã có tour thuộc danh mục này!
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

// Auto-hide alerts after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);
</script>

<?php
$content = ob_get_clean();
?>