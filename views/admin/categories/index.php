<?php
// Format ngày tháng
function formatDate($date) {
    if (empty($date)) return '-';
    return date('d/m/Y H:i', strtotime($date));
}
?>

<!-- Thông báo thành công/lỗi -->
<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Thành công!</strong> Thao tác đã được thực hiện.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Lỗi!</strong> <?= htmlspecialchars($_GET['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Card chứa bảng -->
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <h3 class="card-title">Danh sách Danh mục</h3>
            </div>
            <div class="col-md-6 text-end">
                <?php if (isAdmin()): ?>
                    <a href="<?= BASE_URL ?>?act=category-create" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Thêm Danh mục mới
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Form tìm kiếm và lọc -->
        <form method="GET" action="<?= BASE_URL ?>" class="mb-3">
            <input type="hidden" name="act" value="categories">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm..." 
                           value="<?= htmlspecialchars($currentSearch ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="1" <?= ($currentStatus ?? '') == '1' ? 'selected' : '' ?>>Hoạt động</option>
                        <option value="0" <?= ($currentStatus ?? '') == '0' ? 'selected' : '' ?>>Không hoạt động</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-info w-100">
                        <i class="bi bi-search"></i> Tìm kiếm
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="<?= BASE_URL ?>?act=categories" class="btn btn-secondary w-100">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
            </div>
        </form>

        <!-- Bảng danh sách -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th width="50">ID</th>
                        <th>Tên Danh mục</th>
                        <th>Slug</th>
                        <th>Danh mục cha</th>
                        <th width="80">Thứ tự</th>
                        <th width="100">Số Tour</th>
                        <th width="100">Danh mục con</th>
                        <th width="100">Trạng thái</th>
                        <th width="150">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-inbox"></i> Chưa có danh mục nào
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td>#<?= $category['id'] ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($category['name']) ?></strong>
                                    <?php if (!empty($category['description'])): ?>
                                        <br><small class="text-muted"><?= htmlspecialchars(mb_substr($category['description'], 0, 100)) ?>...</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <code><?= htmlspecialchars($category['slug']) ?></code>
                                </td>
                                <td>
                                    <?php if ($category['parent_name']): ?>
                                        <span class="badge bg-info"><?= htmlspecialchars($category['parent_name']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center"><?= $category['sort_order'] ?></td>
                                <td class="text-center">
                                    <span class="badge bg-primary"><?= $category['tour_count'] ?></span>
                                </td>
                                <td class="text-center">
                                    <?php if ($category['children_count'] > 0): ?>
                                        <span class="badge bg-success"><?= $category['children_count'] ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($category['status'] == 1): ?>
                                        <span class="badge bg-success">Hoạt động</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Không hoạt động</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (isAdmin()): ?>
                                        <div class="btn-group" role="group">
                                            <a href="<?= BASE_URL ?>?act=category-edit&id=<?= $category['id'] ?>" 
                                               class="btn btn-sm btn-warning" title="Sửa">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="deleteCategory(<?= $category['id'] ?>)" title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">Không có quyền</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Form xóa ẩn -->
<form id="deleteForm" method="POST" action="<?= BASE_URL ?>?act=category-delete" style="display: none;">
    <input type="hidden" name="id" id="deleteId">
</form>

<script>
function deleteCategory(id) {
    if (confirm('Bạn có chắc chắn muốn xóa danh mục này? Danh mục đang có tour hoặc danh mục con sẽ không thể xóa.')) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteForm').submit();
    }
}
</script>


