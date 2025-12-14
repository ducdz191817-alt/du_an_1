<?php
// Format ngày tháng
function formatDate($date) {
    if (empty($date)) return '-';
    return date('d/m/Y', strtotime($date));
}

// Format datetime
function formatDateTime($datetime) {
    if (empty($datetime)) return '-';
    return date('d/m/Y H:i', strtotime($datetime));
}

// Format số tiền
function formatCurrency($amount) {
    if (empty($amount)) return '-';
    return number_format((float)$amount, 0, ',', '.') . ' đ';
}

// Format rating
function formatRating($rating) {
    if (empty($rating)) return '0.00';
    return number_format((float)$rating, 2, '.', '');
}

$profile = $profile ?? null;
$user = $user ?? null;
if (!$profile) {
    echo '<div class="alert alert-danger">Không tìm thấy hồ sơ HDV.</div>';
    echo '<a href="' . BASE_URL . '?act=guide-profiles" class="btn btn-secondary">Quay lại danh sách</a>';
    return;
}
?>

<div class="row">
    <!-- Thông tin chính -->
    <div class="col-md-8">
        <!-- Thông tin Hồ sơ HDV -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Thông tin Hồ sơ HDV #<?= $profile->id ?></h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="200">Hướng dẫn viên</th>
                        <td><strong><?= htmlspecialchars($user ? $user->name : '-') ?></strong></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>
                            <?php if ($user && !empty($user->email)): ?>
                                <a href="mailto:<?= htmlspecialchars($user->email) ?>">
                                    <?= htmlspecialchars($user->email) ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Số điện thoại</th>
                        <td>
                            <?php if (!empty($profile->phone)): ?>
                                <a href="tel:<?= htmlspecialchars($profile->phone) ?>">
                                    <?= htmlspecialchars($profile->phone) ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Ngày sinh</th>
                        <td><?= $profile->getBirthdateFormatted() ?></td>
                    </tr>
                    <tr>
                        <th>CMND/CCCD</th>
                        <td><?= htmlspecialchars($profile->id_card ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>Địa chỉ</th>
                        <td><?= htmlspecialchars($profile->address ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>Đánh giá</th>
                        <td>
                            <strong><?= formatRating($profile->rating) ?></strong> / 5.00
                            <small class="text-muted">(<?= $profile->review_count ?> đánh giá)</small>
                        </td>
                    </tr>
                    <tr>
                        <th>Loại đoàn</th>
                        <td>
                            <?php if (!empty($profile->group_type)): ?>
                                <span class="badge bg-info"><?= htmlspecialchars($profile->group_type) ?></span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Chuyên môn</th>
                        <td><?= htmlspecialchars($profile->speciality ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>Trạng thái</th>
                        <td>
                            <?php
                            $statusClass = [
                                'available' => 'success',
                                'busy' => 'warning',
                                'unavailable' => 'secondary',
                            ];
                            $status = $profile->availability_status ?? 'available';
                            $class = $statusClass[$status] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?= $class ?>"><?= $profile->getAvailabilityStatusText() ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>Ngày tạo</th>
                        <td><?= formatDateTime($profile->created_at) ?></td>
                    </tr>
                    <tr>
                        <th>Cập nhật lần cuối</th>
                        <td><?= formatDateTime($profile->updated_at) ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Chứng chỉ -->
        <?php $certificates = $profile->getCertificateArray(); ?>
        <?php if (!empty($certificates)): ?>
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Chứng chỉ</h3>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <?php foreach ($certificates as $cert): ?>
                        <li class="list-group-item"><?= htmlspecialchars($cert) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>

        <!-- Ngôn ngữ -->
        <?php $languages = $profile->getLanguagesArray(); ?>
        <?php if (!empty($languages)): ?>
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Ngôn ngữ</h3>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <?php foreach ($languages as $lang): ?>
                        <li class="list-group-item"><?= htmlspecialchars($lang) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>

        <!-- Kinh nghiệm -->
        <?php if (!empty($profile->experience)): ?>
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Kinh nghiệm</h3>
            </div>
            <div class="card-body">
                <p><?= nl2br(htmlspecialchars($profile->experience)) ?></p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Tình trạng sức khỏe -->
        <?php if (!empty($profile->health_status)): ?>
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Tình trạng sức khỏe</h3>
            </div>
            <div class="card-body">
                <p><?= nl2br(htmlspecialchars($profile->health_status)) ?></p>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <div class="col-md-4">
        <!-- Thao tác -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Thao tác</h3>
            </div>
            <div class="card-body">
                <?php if (isAdmin()): ?>
                    <a href="<?= BASE_URL ?>?act=guide-profile-edit&id=<?= $profile->id ?>" class="btn btn-warning w-100 mb-2">
                        <i class="bi bi-pencil"></i> Sửa thông tin
                    </a>
                    <button type="button" class="btn btn-danger w-100" onclick="deleteProfile(<?= $profile->id ?>)">
                        <i class="bi bi-trash"></i> Xóa hồ sơ
                    </button>
                <?php endif; ?>
                <a href="<?= BASE_URL ?>?act=guide-profiles" class="btn btn-secondary w-100 mt-2">
                    <i class="bi bi-arrow-left"></i> Quay lại danh sách
                </a>
            </div>
        </div>

        <!-- Thống kê -->
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Thống kê</h3>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    <strong>Số booking:</strong> 
                    <span class="badge bg-primary"><?= $profile->getBookingCount() ?></span>
                </p>
                <p class="mb-2">
                    <strong>Đánh giá:</strong> 
                    <span class="badge bg-success"><?= formatRating($profile->rating) ?></span>
                </p>
                <p class="mb-0">
                    <strong>Số đánh giá:</strong> 
                    <span class="badge bg-info"><?= $profile->review_count ?></span>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Danh sách Bookings -->
<?php if (!empty($bookings)): ?>
<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title">Danh sách Booking (<?= count($bookings) ?>)</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="50">ID</th>
                        <th>Mã Booking</th>
                        <th>Tour</th>
                        <th>Ngày bắt đầu</th>
                        <th>Ngày kết thúc</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th width="100">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td>#<?= $booking['id'] ?></td>
                            <td>
                                <strong class="text-primary"><?= htmlspecialchars($booking['booking_code'] ?? '-') ?></strong>
                            </td>
                            <td><?= htmlspecialchars($booking['tour_name'] ?? '-') ?></td>
                            <td><?= formatDate($booking['start_date'] ?? '') ?></td>
                            <td><?= formatDate($booking['end_date'] ?? '') ?></td>
                            <td><?= formatCurrency($booking['total_amount'] ?? 0) ?></td>
                            <td>
                                <?php
                                $statusId = $booking['status'] ?? 1;
                                $statusClass = [
                                    1 => 'warning',
                                    2 => 'info',
                                    3 => 'success',
                                    4 => 'danger',
                                    5 => 'secondary',
                                    6 => 'dark',
                                ];
                                $class = $statusClass[$statusId] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $class ?>">
                                    <?= htmlspecialchars($booking['status_name'] ?? '-') ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>?act=booking-show&id=<?= $booking['id'] ?>" 
                                   class="btn btn-sm btn-info" title="Xem chi tiết">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php else: ?>
<div class="card mt-3">
    <div class="card-body text-center text-muted">
        <i class="bi bi-inbox"></i> Hướng dẫn viên này chưa có booking nào
    </div>
</div>
<?php endif; ?>

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

