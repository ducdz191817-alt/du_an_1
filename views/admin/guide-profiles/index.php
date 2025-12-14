<?php
// Format ngày tháng
function formatDate($date) {
    if (empty($date)) return '-';
    return date('d/m/Y', strtotime($date));
}

// Format rating
function formatRating($rating) {
    if (empty($rating)) return '0.00';
    return number_format((float)$rating, 2, '.', '');
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
                <h3 class="card-title">Danh sách Hồ sơ HDV</h3>
            </div>
            <div class="col-md-6 text-end">
                <?php if (isAdmin()): ?>
                    <a href="<?= BASE_URL ?>?act=guide-profile-create" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Thêm Hồ sơ HDV mới
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Form tìm kiếm và lọc -->
        <form method="GET" action="<?= BASE_URL ?>" class="mb-3">
            <input type="hidden" name="act" value="guide-profiles">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm tên, email, SĐT..." 
                           value="<?= htmlspecialchars($currentSearch ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <select name="availability_status" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="available" <?= ($currentAvailabilityStatus ?? '') == 'available' ? 'selected' : '' ?>>Có sẵn</option>
                        <option value="busy" <?= ($currentAvailabilityStatus ?? '') == 'busy' ? 'selected' : '' ?>>Bận</option>
                        <option value="unavailable" <?= ($currentAvailabilityStatus ?? '') == 'unavailable' ? 'selected' : '' ?>>Không có sẵn</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="group_type" class="form-select">
                        <option value="">Tất cả loại đoàn</option>
                        <option value="nội địa" <?= ($currentGroupType ?? '') == 'nội địa' ? 'selected' : '' ?>>Nội địa</option>
                        <option value="quốc tế" <?= ($currentGroupType ?? '') == 'quốc tế' ? 'selected' : '' ?>>Quốc tế</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-info w-100">
                        <i class="bi bi-search"></i> Tìm kiếm
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="<?= BASE_URL ?>?act=guide-profiles" class="btn btn-secondary w-100">
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
                        <th>Hướng dẫn viên</th>
                        <th>Thông tin liên hệ</th>
                        <th width="100">Đánh giá</th>
                        <th width="100">Số Booking</th>
                        <th width="120">Loại đoàn</th>
                        <th width="120">Trạng thái</th>
                        <th width="150">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($profiles)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox"></i> Chưa có hồ sơ HDV nào
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($profiles as $profile): ?>
                            <tr>
                                <td>#<?= $profile['id'] ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($profile['user_name'] ?? '-') ?></strong>
                                    <?php if (!empty($profile['speciality'])): ?>
                                        <br><small class="text-muted"><?= htmlspecialchars($profile['speciality']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($profile['phone'])): ?>
                                        <div><i class="bi bi-telephone"></i> <?= htmlspecialchars($profile['phone']) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($profile['user_email'])): ?>
                                        <div><i class="bi bi-envelope"></i> <?= htmlspecialchars($profile['user_email']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div>
                                        <strong><?= formatRating($profile['rating']) ?></strong>
                                        <br><small class="text-muted">(<?= $profile['review_count'] ?> đánh giá)</small>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary"><?= $profile['booking_count'] ?></span>
                                </td>
                                <td>
                                    <?php if (!empty($profile['group_type'])): ?>
                                        <span class="badge bg-info"><?= htmlspecialchars($profile['group_type']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $statusClass = [
                                        'available' => 'success',
                                        'busy' => 'warning',
                                        'unavailable' => 'secondary',
                                    ];
                                    $statusText = [
                                        'available' => 'Có sẵn',
                                        'busy' => 'Bận',
                                        'unavailable' => 'Không có sẵn',
                                    ];
                                    $status = $profile['availability_status'] ?? 'available';
                                    $class = $statusClass[$status] ?? 'secondary';
                                    $text = $statusText[$status] ?? '-';
                                    ?>
                                    <span class="badge bg-<?= $class ?>"><?= $text ?></span>
                                </td>
                                <td>
                                    <?php if (isAdmin()): ?>
                                        <div class="btn-group" role="group">
                                            <a href="<?= BASE_URL ?>?act=guide-profile-show&id=<?= $profile['id'] ?>" 
                                               class="btn btn-sm btn-info" title="Xem chi tiết">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="<?= BASE_URL ?>?act=guide-profile-edit&id=<?= $profile['id'] ?>" 
                                               class="btn btn-sm btn-warning" title="Sửa">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="deleteProfile(<?= $profile['id'] ?>)" title="Xóa">
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
<form id="deleteForm" method="POST" action="<?= BASE_URL ?>?act=guide-profile-delete" style="display: none;">
    <input type="hidden" name="id" id="deleteId">
</form>

<script>
function deleteProfile(id) {
    if (confirm('Bạn có chắc chắn muốn xóa hồ sơ HDV này? Hồ sơ đang có booking sẽ không thể xóa.')) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteForm').submit();
    }
}
</script>

