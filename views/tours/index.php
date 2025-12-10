<?php ob_start(); ?>

<!--begin::Row-->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-airplane-engines me-2"></i>
                    Danh sách Tour
                </h3>
                <div class="card-tools">
                    <a href="<?= BASE_URL ?>?act=tours/create" class="btn btn-success btn-sm">
                        <i class="bi bi-plus-circle me-1"></i> Thêm Tour mới
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
                
                <!-- Filter Form -->
                <form method="GET" action="" class="row g-3 mb-4">
                    <input type="hidden" name="act" value="tours">
                    
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" name="search" placeholder="Tìm kiếm tour..." 
                                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <select class="form-select" name="category">
                            <option value="">Tất cả danh mục</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" 
                                    <?= (isset($_GET['category']) && $_GET['category'] == $category['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <select class="form-select" name="status">
                            <option value="">Tất cả trạng thái</option>
                            <option value="1" <?= (isset($_GET['status']) && $_GET['status'] == '1') ? 'selected' : '' ?>>Chờ xác nhận</option>
                            <option value="2" <?= (isset($_GET['status']) && $_GET['status'] == '2') ? 'selected' : '' ?>>Đã xác nhận</option>
                            <option value="3" <?= (isset($_GET['status']) && $_GET['status'] == '3') ? 'selected' : '' ?>>Đang diễn ra</option>
                            <option value="4" <?= (isset($_GET['status']) && $_GET['status'] == '4') ? 'selected' : '' ?>>Đã hoàn thành</option>
                            <option value="5" <?= (isset($_GET['status']) && $_GET['status'] == '5') ? 'selected' : '' ?>>Đã hủy</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-funnel me-1"></i> Lọc
                        </button>
                    </div>
                    
                    <div class="col-md-2">
                        <a href="<?= BASE_URL ?>?act=tours" class="btn btn-secondary w-100">
                            <i class="bi bi-arrow-clockwise me-1"></i> Reset
                        </a>
                    </div>
                </form>
                
                <!-- Tours Table -->
                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="toursTable">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Tên Tour</th>
                                <th>Danh mục</th>
                                <th>Địa điểm</th>
                                <th>Ngày đi</th>
                                <th>Giá</th>
                                <th>HDV</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($tours)): ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox me-2"></i>
                                        Không có tour nào
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($tours as $tour): ?>
                                <tr>
                                    <td><?= $tour['id'] ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($tour['name']) ?></strong>
                                        <?php if ($tour['max_participants'] > 0): ?>
                                            <br>
                                            <small class="text-muted">
                                                <i class="bi bi-people"></i> Tối đa: <?= $tour['max_participants'] ?> người
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($tour['category_name'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($tour['location']) ?></td>
                                    <td>
                                        <?= date('d/m/Y', strtotime($tour['start_date'])) ?><br>
                                        <small class="text-muted"><?= date('d/m/Y', strtotime($tour['end_date'])) ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-success fs-6">
                                            <?= number_format($tour['price'], 0, ',', '.') ?> ₫
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($tour['guide_name'] ?? 'Chưa phân công') ?></td>
                                    <td>
                                        <?php
                                        $status_classes = [
                                            '1' => 'warning',    // pending
                                            '2' => 'info',       // confirmed
                                            '3' => 'primary',    // active
                                            '4' => 'success',    // completed
                                            '5' => 'danger',     // cancelled
                                        ];
                                        $status_text = [
                                            '1' => 'Chờ xác nhận',
                                            '2' => 'Đã xác nhận',
                                            '3' => 'Đang diễn ra',
                                            '4' => 'Đã hoàn thành',
                                            '5' => 'Đã hủy',
                                        ];
                                        $status_id = $tour['status_id'] ?? 1;
                                        ?>
                                        <span class="badge bg-<?= $status_classes[$status_id] ?? 'secondary' ?>">
                                            <?= $status_text[$status_id] ?? 'Unknown' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="<?= BASE_URL ?>?act=tours/show&id=<?= $tour['id'] ?>" 
                                               class="btn btn-info" title="Xem chi tiết">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="<?= BASE_URL ?>?act=tours/edit&id=<?= $tour['id'] ?>" 
                                               class="btn btn-warning" title="Sửa">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger" 
                                                    title="Xóa" 
                                                    onclick="confirmDelete(<?= $tour['id'] ?>, '<?= htmlspecialchars(addslashes($tour['name'])) ?>')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Tổng cộng: <strong><?= count($tours) ?></strong> tour
                    </div>
                    <div>
                        <a href="<?= BASE_URL ?>?act=tours/create" class="btn btn-success">
                            <i class="bi bi-plus-circle me-1"></i> Thêm Tour mới
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
                <p>Bạn có chắc chắn muốn xóa tour <strong id="tourName"></strong>?</p>
                <p class="text-danger"><small>Hành động này không thể hoàn tác!</small></p>
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
    document.getElementById('tourName').textContent = name;
    document.getElementById('deleteForm').action = '<?= BASE_URL ?>?act=tours/delete';
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Initialize DataTable
document.addEventListener('DOMContentLoaded', function() {
    $('#toursTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json'
        },
        pageLength: 10,
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: [8] } // Disable sorting on action column
        ]
    });
});
</script>

<?php
$content = ob_get_clean();
?>