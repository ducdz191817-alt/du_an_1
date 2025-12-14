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
                <h3 class="card-title">Danh sách Khách hàng</h3>
            </div>
            <div class="col-md-6 text-end">
                <?php if (isAdmin()): ?>
                    <a href="<?= BASE_URL ?>?act=customer-create" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Thêm Khách hàng mới
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Form tìm kiếm và lọc -->
        <form method="GET" action="<?= BASE_URL ?>" class="mb-3">
            <input type="hidden" name="act" value="customers">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm tên, SĐT, email..." 
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
                    <a href="<?= BASE_URL ?>?act=customers" class="btn btn-secondary w-100">
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
                        <th>Số điện thoại</th>
                        <th>Email</th>
                        <th>Địa chỉ</th>
                        <th width="100">Số Booking</th>
                        <th width="100">Trạng thái</th>
                        <th width="150">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($customers)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox"></i> Chưa có khách hàng nào
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td>#<?= $customer['id'] ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($customer['name']) ?></strong>
                                    <?php if (!empty($customer['company'])): ?>
                                        <br><small class="text-muted"><?= htmlspecialchars($customer['company']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="tel:<?= htmlspecialchars($customer['phone']) ?>">
                                        <?= htmlspecialchars($customer['phone']) ?>
                                    </a>
                                </td>
                                <td>
                                    <?php if (!empty($customer['email'])): ?>
                                        <a href="mailto:<?= htmlspecialchars($customer['email']) ?>">
                                            <?= htmlspecialchars($customer['email']) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($customer['address'])): ?>
                                        <?= htmlspecialchars(mb_substr($customer['address'], 0, 50)) ?>
                                        <?= mb_strlen($customer['address']) > 50 ? '...' : '' ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary"><?= $customer['booking_count'] ?></span>
                                </td>
                                <td>
                                    <?php if ($customer['status'] == 1): ?>
                                        <span class="badge bg-success">Hoạt động</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Không hoạt động</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (isAdmin()): ?>
                                        <div class="btn-group" role="group">
                                            <a href="<?= BASE_URL ?>?act=customer-show&id=<?= $customer['id'] ?>" 
                                               class="btn btn-sm btn-info" title="Xem chi tiết">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="<?= BASE_URL ?>?act=customer-edit&id=<?= $customer['id'] ?>" 
                                               class="btn btn-sm btn-warning" title="Sửa">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="deleteCustomer(<?= $customer['id'] ?>)" title="Xóa">
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
<form id="deleteForm" method="POST" action="<?= BASE_URL ?>?act=customer-delete" style="display: none;">
    <input type="hidden" name="id" id="deleteId">
</form>

<script>
function deleteCustomer(id) {
    if (confirm('Bạn có chắc chắn muốn xóa khách hàng này? Khách hàng đang có booking sẽ không thể xóa.')) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteForm').submit();
    }
}
</script>

