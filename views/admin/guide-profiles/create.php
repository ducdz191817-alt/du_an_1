<?php
$old = $old ?? [];
$errors = $errors ?? [];
$availableGuides = $availableGuides ?? [];

// Kiểm tra quyền admin
if (!isAdmin()) {
    echo '<div class="alert alert-danger">Bạn không có quyền truy cập trang này. Chỉ Admin mới có quyền tạo hồ sơ HDV.</div>';
    echo '<a href="' . BASE_URL . '?act=guide-profiles" class="btn btn-secondary">Quay lại danh sách</a>';
    return;
}
?>

<!-- Thông báo quyền admin -->
<div class="alert alert-info">
    <i class="bi bi-shield-check"></i> <strong>Chỉ dành cho Admin:</strong> Bạn đang có quyền tạo hồ sơ HDV mới vào hệ thống.
</div>

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

<form method="POST" action="<?= BASE_URL ?>?act=guide-profile-store" id="guideProfileForm">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Thông tin cơ bản</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="user_id" class="form-label">Hướng dẫn viên <span class="text-danger">*</span></label>
                    <select class="form-select" id="user_id" name="user_id" required>
                        <option value="">-- Chọn hướng dẫn viên --</option>
                        <?php foreach ($availableGuides as $guide): ?>
                            <option value="<?= $guide['id'] ?>" 
                                    <?= ($old['user_id'] ?? '') == $guide['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($guide['name']) ?> (<?= htmlspecialchars($guide['email']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (empty($availableGuides)): ?>
                        <small class="text-danger">Không có hướng dẫn viên nào chưa có hồ sơ. Vui lòng tạo user với role "guide" trước.</small>
                    <?php endif; ?>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="birthdate" class="form-label">Ngày sinh</label>
                    <input type="date" class="form-control" id="birthdate" name="birthdate" 
                           value="<?= htmlspecialchars($old['birthdate'] ?? '') ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">Số điện thoại</label>
                    <input type="text" class="form-control" id="phone" name="phone" 
                           value="<?= htmlspecialchars($old['phone'] ?? '') ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="id_card" class="form-label">CMND/CCCD</label>
                    <input type="text" class="form-control" id="id_card" name="id_card" 
                           value="<?= htmlspecialchars($old['id_card'] ?? '') ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="avatar" class="form-label">Avatar (URL)</label>
                    <input type="text" class="form-control" id="avatar" name="avatar" 
                           value="<?= htmlspecialchars($old['avatar'] ?? '') ?>" placeholder="https://...">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="availability_status" class="form-label">Trạng thái</label>
                    <select class="form-select" id="availability_status" name="availability_status">
                        <option value="available" <?= ($old['availability_status'] ?? 'available') == 'available' ? 'selected' : '' ?>>Có sẵn</option>
                        <option value="busy" <?= ($old['availability_status'] ?? '') == 'busy' ? 'selected' : '' ?>>Bận</option>
                        <option value="unavailable" <?= ($old['availability_status'] ?? '') == 'unavailable' ? 'selected' : '' ?>>Không có sẵn</option>
                    </select>
                </div>

                <div class="col-md-12 mb-3">
                    <label for="address" class="form-label">Địa chỉ</label>
                    <textarea class="form-control" id="address" name="address" rows="2"><?= htmlspecialchars($old['address'] ?? '') ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h3 class="card-title">Chứng chỉ</h3>
        </div>
        <div class="card-body">
            <div id="certificatesContainer">
                <?php
                $certificates = $old['certificates'] ?? [];
                if (empty($certificates)) {
                    $certificates = [''];
                }
                foreach ($certificates as $index => $cert): ?>
                    <div class="input-group mb-2 certificate-item">
                        <input type="text" class="form-control" name="certificates[]" 
                               value="<?= htmlspecialchars($cert) ?>" placeholder="VD: HDV quốc tế">
                        <button type="button" class="btn btn-danger remove-certificate" onclick="removeCertificate(this)">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="btn btn-sm btn-secondary" onclick="addCertificate()">
                <i class="bi bi-plus"></i> Thêm chứng chỉ
            </button>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h3 class="card-title">Ngôn ngữ</h3>
        </div>
        <div class="card-body">
            <div id="languagesContainer">
                <?php
                $languages = $old['languages'] ?? [];
                if (empty($languages)) {
                    $languages = [''];
                }
                foreach ($languages as $index => $lang): ?>
                    <div class="input-group mb-2 language-item">
                        <input type="text" class="form-control" name="languages[]" 
                               value="<?= htmlspecialchars($lang) ?>" placeholder="VD: Tiếng Anh">
                        <button type="button" class="btn btn-danger remove-language" onclick="removeLanguage(this)">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="btn btn-sm btn-secondary" onclick="addLanguage()">
                <i class="bi bi-plus"></i> Thêm ngôn ngữ
            </button>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h3 class="card-title">Thông tin khác</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="group_type" class="form-label">Loại đoàn</label>
                    <select class="form-select" id="group_type" name="group_type">
                        <option value="">-- Chọn loại đoàn --</option>
                        <option value="nội địa" <?= ($old['group_type'] ?? '') == 'nội địa' ? 'selected' : '' ?>>Nội địa</option>
                        <option value="quốc tế" <?= ($old['group_type'] ?? '') == 'quốc tế' ? 'selected' : '' ?>>Quốc tế</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="speciality" class="form-label">Chuyên môn</label>
                    <input type="text" class="form-control" id="speciality" name="speciality" 
                           value="<?= htmlspecialchars($old['speciality'] ?? '') ?>" placeholder="VD: chuyên tuyến miền Bắc">
                </div>

                <div class="col-md-12 mb-3">
                    <label for="experience" class="form-label">Kinh nghiệm</label>
                    <textarea class="form-control" id="experience" name="experience" rows="3"><?= htmlspecialchars($old['experience'] ?? '') ?></textarea>
                </div>

                <div class="col-md-12 mb-3">
                    <label for="health_status" class="form-label">Tình trạng sức khỏe</label>
                    <textarea class="form-control" id="health_status" name="health_status" rows="2"><?= htmlspecialchars($old['health_status'] ?? '') ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save"></i> Lưu
        </button>
        <a href="<?= BASE_URL ?>?act=guide-profiles" class="btn btn-secondary">
            <i class="bi bi-x-circle"></i> Hủy
        </a>
    </div>
</form>

<script>
function addCertificate() {
    const container = document.getElementById('certificatesContainer');
    const div = document.createElement('div');
    div.className = 'input-group mb-2 certificate-item';
    div.innerHTML = `
        <input type="text" class="form-control" name="certificates[]" placeholder="VD: HDV quốc tế">
        <button type="button" class="btn btn-danger remove-certificate" onclick="removeCertificate(this)">
            <i class="bi bi-trash"></i>
        </button>
    `;
    container.appendChild(div);
}

function removeCertificate(btn) {
    btn.closest('.certificate-item').remove();
}

function addLanguage() {
    const container = document.getElementById('languagesContainer');
    const div = document.createElement('div');
    div.className = 'input-group mb-2 language-item';
    div.innerHTML = `
        <input type="text" class="form-control" name="languages[]" placeholder="VD: Tiếng Anh">
        <button type="button" class="btn btn-danger remove-language" onclick="removeLanguage(this)">
            <i class="bi bi-trash"></i>
        </button>
    `;
    container.appendChild(div);
}

function removeLanguage(btn) {
    btn.closest('.language-item').remove();
}
</script>

