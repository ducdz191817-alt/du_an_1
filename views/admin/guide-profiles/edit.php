<?php
$profile = $profile ?? null;
$old = $old ?? [];
$errors = $errors ?? [];

// Kiểm tra quyền admin
if (!isAdmin()) {
    echo '<div class="alert alert-danger">Bạn không có quyền truy cập trang này. Chỉ Admin mới có quyền sửa hồ sơ HDV.</div>';
    echo '<a href="' . BASE_URL . '?act=guide-profiles" class="btn btn-secondary">Quay lại danh sách</a>';
    return;
}

if (!$profile) {
    echo '<div class="alert alert-danger">Không tìm thấy hồ sơ HDV.</div>';
    echo '<a href="' . BASE_URL . '?act=guide-profiles" class="btn btn-secondary">Quay lại danh sách</a>';
    return;
}

$certificates = !empty($old['certificates']) ? $old['certificates'] : $profile->getCertificateArray();
$languages = !empty($old['languages']) ? $old['languages'] : $profile->getLanguagesArray();
if (empty($certificates)) $certificates = [''];
if (empty($languages)) $languages = [''];
?>

<!-- Thông báo quyền admin -->
<div class="alert alert-info">
    <i class="bi bi-shield-check"></i> <strong>Chỉ dành cho Admin:</strong> Bạn đang có quyền sửa thông tin hồ sơ HDV.
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

<form method="POST" action="<?= BASE_URL ?>?act=guide-profile-update" id="guideProfileForm">
    <input type="hidden" name="id" value="<?= $profile->id ?>">
    
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Thông tin cơ bản</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Hướng dẫn viên</label>
                    <?php $user = $profile->getUser(); ?>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($user ? $user->name : '-') ?>" disabled>
                    <small class="text-muted">Không thể thay đổi hướng dẫn viên sau khi tạo hồ sơ</small>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="birthdate" class="form-label">Ngày sinh</label>
                    <input type="date" class="form-control" id="birthdate" name="birthdate" 
                           value="<?= htmlspecialchars((string)($old['birthdate'] ?? $profile->birthdate ?? '')) ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">Số điện thoại</label>
                    <input type="text" class="form-control" id="phone" name="phone" 
                           value="<?= htmlspecialchars((string)($old['phone'] ?? $profile->phone ?? '')) ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="id_card" class="form-label">CMND/CCCD</label>
                    <input type="text" class="form-control" id="id_card" name="id_card" 
                           value="<?= htmlspecialchars((string)($old['id_card'] ?? $profile->id_card ?? '')) ?>">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="avatar" class="form-label">Avatar (URL)</label>
                    <input type="text" class="form-control" id="avatar" name="avatar" 
                           value="<?= htmlspecialchars((string)($old['avatar'] ?? $profile->avatar ?? '')) ?>" placeholder="https://...">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="availability_status" class="form-label">Trạng thái</label>
                    <select class="form-select" id="availability_status" name="availability_status">
                        <option value="available" <?= (($old['availability_status'] ?? $profile->availability_status ?? 'available')) == 'available' ? 'selected' : '' ?>>Có sẵn</option>
                        <option value="busy" <?= (($old['availability_status'] ?? $profile->availability_status ?? 'available')) == 'busy' ? 'selected' : '' ?>>Bận</option>
                        <option value="unavailable" <?= (($old['availability_status'] ?? $profile->availability_status ?? 'available')) == 'unavailable' ? 'selected' : '' ?>>Không có sẵn</option>
                    </select>
                </div>

                <div class="col-md-12 mb-3">
                    <label for="address" class="form-label">Địa chỉ</label>
                    <textarea class="form-control" id="address" name="address" rows="2"><?= htmlspecialchars((string)($old['address'] ?? $profile->address ?? '')) ?></textarea>
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
                <?php foreach ($certificates as $index => $cert): ?>
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
                <?php foreach ($languages as $index => $lang): ?>
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
                        <option value="nội địa" <?= (($old['group_type'] ?? $profile->group_type ?? '')) == 'nội địa' ? 'selected' : '' ?>>Nội địa</option>
                        <option value="quốc tế" <?= (($old['group_type'] ?? $profile->group_type ?? '')) == 'quốc tế' ? 'selected' : '' ?>>Quốc tế</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="speciality" class="form-label">Chuyên môn</label>
                    <input type="text" class="form-control" id="speciality" name="speciality" 
                           value="<?= htmlspecialchars((string)($old['speciality'] ?? $profile->speciality ?? '')) ?>" placeholder="VD: chuyên tuyến miền Bắc">
                </div>

                <div class="col-md-12 mb-3">
                    <label for="experience" class="form-label">Kinh nghiệm</label>
                    <textarea class="form-control" id="experience" name="experience" rows="3"><?= htmlspecialchars((string)($old['experience'] ?? $profile->experience ?? '')) ?></textarea>
                </div>

                <div class="col-md-12 mb-3">
                    <label for="health_status" class="form-label">Tình trạng sức khỏe</label>
                    <textarea class="form-control" id="health_status" name="health_status" rows="2"><?= htmlspecialchars((string)($old['health_status'] ?? $profile->health_status ?? '')) ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save"></i> Cập nhật
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

