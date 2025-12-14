<?php
$errors = $errors ?? [];
$old = $old ?? [];
$tour = $tour ?? null;

// Kiểm tra quyền admin (controller đã xử lý requireAdmin(), nhưng kiểm tra thêm ở view để đảm bảo)
if (!isAdmin()) {
    echo '<div class="alert alert-danger">Bạn không có quyền truy cập trang này. Chỉ Admin mới có quyền sửa tour.</div>';
    echo '<a href="' . BASE_URL . '?act=tours" class="btn btn-secondary">Quay lại danh sách</a>';
    return;
}

// Kiểm tra tour tồn tại
if ($tour === null) {
    echo '<div class="alert alert-danger">Tour không tồn tại.</div>';
    echo '<a href="' . BASE_URL . '?act=tours" class="btn btn-secondary">Quay lại danh sách</a>';
    return;
}

// Lấy giá trị từ tour hoặc từ old (khi có lỗi validation)
$name = $old['name'] ?? ($tour->name ?? '');
$description = $old['description'] ?? ($tour->description ?? '');
$categoryId = $old['category_id'] ?? ($tour->category_id ?? '');
$price = $old['price'] ?? ($tour->price ?? '');
$duration = $old['duration'] ?? ($tour->duration ?? '');
$maxGuests = $old['max_guests'] ?? ($tour->max_guests ?? '');
$status = $old['status'] ?? ($tour->status ?? 1);

// Lấy giá trị từ JSON
$pricesArray = $tour ? $tour->getPricesArray() : null;
$adultPrice = $old['adult_price'] ?? ($pricesArray['adult'] ?? '');
$childPrice = $old['child_price'] ?? ($pricesArray['child'] ?? '');

$imagesArray = $tour ? $tour->getImagesArray() : [];
$images = $old['images'] ?? (!empty($imagesArray) ? implode(', ', $imagesArray) : '');

$scheduleArray = $tour ? $tour->getScheduleArray() : null;
$schedule = $old['schedule'] ?? ($tour->schedule ?? '');

$policiesText = '';
if ($tour && !empty($tour->policies)) {
    $policiesArray = json_decode($tour->policies, true);
    if (is_array($policiesArray) && isset($policiesArray['text'])) {
        $policiesText = is_string($policiesArray['text']) ? $policiesArray['text'] : json_encode($policiesArray['text']);
    } else {
        $policiesText = is_string($tour->policies) ? $tour->policies : json_encode($tour->policies);
    }
}
$policies = $old['policies'] ?? $policiesText;

$suppliersText = '';
if ($tour && !empty($tour->suppliers)) {
    $suppliersArray = json_decode($tour->suppliers, true);
    if (is_array($suppliersArray) && isset($suppliersArray['text'])) {
        $suppliersDecoded = json_decode($suppliersArray['text'], true);
        $suppliersText = is_array($suppliersDecoded) ? implode(', ', $suppliersDecoded) : $suppliersArray['text'];
    } else {
        $suppliersText = is_string($tour->suppliers) ? $tour->suppliers : '';
    }
}
$suppliers = $old['suppliers'] ?? $suppliersText;

// Đảm bảo tất cả biến là string để tránh lỗi htmlspecialchars
$name = (string)($name ?? '');
$description = (string)($description ?? '');
$price = (string)($price ?? '');
$duration = (string)($duration ?? '');
$maxGuests = (string)($maxGuests ?? '');
$adultPrice = (string)($adultPrice ?? '');
$childPrice = (string)($childPrice ?? '');
$images = (string)($images ?? '');
$schedule = (string)($schedule ?? '');
$policies = (string)($policies ?? '');
$suppliers = (string)($suppliers ?? '');
?>

<!-- Hiển thị lỗi -->
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" action="<?= BASE_URL ?>?act=tour-update">
    <input type="hidden" name="id" value="<?= $tour->id ?>">
    
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Thông tin cơ bản</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="name" class="form-label">Tên Tour <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" 
                           value="<?= htmlspecialchars($name ?? '') ?>" required>
                </div>

                <div class="col-md-12 mb-3">
                    <label for="description" class="form-label">Mô tả <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="description" name="description" rows="4" required><?= htmlspecialchars($description ?? '') ?></textarea>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="category_id" class="form-label">Danh mục <span class="text-danger">*</span></label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        <option value="">-- Chọn danh mục --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" 
                                    <?= $categoryId == $cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="price" class="form-label">Giá tour (VNĐ) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="price" name="price" 
                           value="<?= htmlspecialchars($price ?? '') ?>" min="0" step="1000" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="adult_price" class="form-label">Giá người lớn (VNĐ)</label>
                    <input type="number" class="form-control" id="adult_price" name="adult_price" 
                           value="<?= htmlspecialchars($adultPrice ?? '') ?>" min="0" step="1000">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="child_price" class="form-label">Giá trẻ em (VNĐ)</label>
                    <input type="number" class="form-control" id="child_price" name="child_price" 
                           value="<?= htmlspecialchars($childPrice ?? '') ?>" min="0" step="1000">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="duration" class="form-label">Thời lượng</label>
                    <input type="text" class="form-control" id="duration" name="duration" 
                           value="<?= htmlspecialchars($duration ?? '') ?>" 
                           placeholder="VD: 3 ngày 2 đêm">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="max_guests" class="form-label">Số khách tối đa</label>
                    <input type="number" class="form-control" id="max_guests" name="max_guests" 
                           value="<?= htmlspecialchars($maxGuests ?? '') ?>" min="1">
                </div>

                <div class="col-md-4 mb-3">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select class="form-select" id="status" name="status">
                        <option value="1" <?= ($status == 1 || $status == '1') ? 'selected' : '' ?>>Hoạt động</option>
                        <option value="0" <?= ($status == 0 || $status == '0') ? 'selected' : '' ?>>Không hoạt động</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h3 class="card-title">Thông tin bổ sung</h3>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label for="images" class="form-label">Hình ảnh (mỗi ảnh một dòng hoặc cách nhau bằng dấu phẩy)</label>
                <textarea class="form-control" id="images" name="images" rows="3"><?= htmlspecialchars($images ?? '') ?></textarea>
                <small class="text-muted">Nhập tên file ảnh, mỗi ảnh một dòng hoặc cách nhau bằng dấu phẩy</small>
            </div>

            <div class="mb-3">
                <label for="policies" class="form-label">Chính sách</label>
                <textarea class="form-control" id="policies" name="policies" rows="3"><?= htmlspecialchars($policies ?? '') ?></textarea>
            </div>

            <div class="mb-3">
                <label for="suppliers" class="form-label">Nhà cung cấp (mỗi nhà cung cấp một dòng hoặc cách nhau bằng dấu phẩy)</label>
                <textarea class="form-control" id="suppliers" name="suppliers" rows="2"><?= htmlspecialchars($suppliers ?? '') ?></textarea>
            </div>

            <div class="mb-3">
                <label for="schedule" class="form-label">Lịch trình (JSON)</label>
                <textarea class="form-control" id="schedule" name="schedule" rows="5"><?= htmlspecialchars($schedule ?? '') ?></textarea>
                <small class="text-muted">Nhập dữ liệu JSON cho lịch trình tour</small>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save"></i> Cập nhật Tour
        </button>
        <a href="<?= BASE_URL ?>?act=tours" class="btn btn-secondary">
            <i class="bi bi-x-circle"></i> Hủy
        </a>
    </div>
</form>

