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
                <h3 class="card-title">Quản lý Hướng dẫn viên</h3>
            </div>
            <div class="col-md-6 text-end">
                <?php if (isAdmin()): ?>
                    <a href="<?= BASE_URL ?>?act=user-assign-tour" class="btn btn-info me-2">
                        <i class="bi bi-person-check"></i> Phân công Tour
                    </a>
                    <a href="<?= BASE_URL ?>?act=user-create" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Thêm HDV mới
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Form tìm kiếm và lọc -->
        <form method="GET" action="<?= BASE_URL ?>" class="mb-3">
            <input type="hidden" name="act" value="users">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm tên, email, SĐT..." 
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
                    <a href="<?= BASE_URL ?>?act=users" class="btn btn-secondary w-100">
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
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th width="100">Số Booking</th>
                        <th width="100">Hồ sơ</th>
                        <th width="100">Trạng thái</th>
                        <th width="150">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox"></i> Chưa có hướng dẫn viên nào
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td>#<?= $user['id'] ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($user['name']) ?></strong>
                                </td>
                                <td>
                                    <a href="mailto:<?= htmlspecialchars($user['email']) ?>">
                                        <?= htmlspecialchars($user['email']) ?>
                                    </a>
                                </td>
                                <td>
                                    <?php if (!empty($user['phone'])): ?>
                                        <a href="tel:<?= htmlspecialchars($user['phone']) ?>">
                                            <?= htmlspecialchars($user['phone']) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary"><?= $user['booking_count'] ?></span>
                                </td>
                                <td class="text-center">
                                    <?php if ($user['has_profile'] > 0): ?>
                                        <a href="<?= BASE_URL ?>?act=guide-profile-show&id=<?= $user['id'] ?>" class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> Có
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= BASE_URL ?>?act=guide-profile-create&user_id=<?= $user['id'] ?>" class="badge bg-warning">
                                            <i class="bi bi-plus-circle"></i> Tạo
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($user['status'] == 1): ?>
                                        <span class="badge bg-success">Hoạt động</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Không hoạt động</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (isAdmin()): ?>
                                        <div class="btn-group" role="group">
                                            <a href="<?= BASE_URL ?>?act=user-edit&id=<?= $user['id'] ?>" 
                                               class="btn btn-sm btn-warning" title="Sửa">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="deleteUser(<?= $user['id'] ?>)" title="Xóa">
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
<form id="deleteForm" method="POST" action="<?= BASE_URL ?>?act=user-delete" style="display: none;">
    <input type="hidden" name="id" id="deleteId">
</form>

<script>
function deleteUser(id) {
    if (confirm('Bạn có chắc chắn muốn xóa hướng dẫn viên này? Hướng dẫn viên đang có booking sẽ không thể xóa.')) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteForm').submit();
    }
}
</script>

