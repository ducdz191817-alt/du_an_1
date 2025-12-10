<?php ob_start(); ?>

<!--begin::Row-->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-plus-circle me-2"></i>
                    Thêm Tour mới
                </h3>
            </div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h5><i class="bi bi-exclamation-triangle-fill me-2"></i> Có lỗi xảy ra:</h5>
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="<?= BASE_URL ?>?act=tours/store" novalidate>
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <!-- Tour Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label required">Tên Tour</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" 
                                       required minlength="5" maxlength="200">
                                <div class="form-text">Tên tour phải có ít nhất 5 ký tự</div>
                            </div>
                            
                            <!-- Category -->
                            <div class="mb-3">
                                <label for="category_id" class="form-label required">Danh mục</label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">-- Chọn danh mục --</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>" 
                                            <?= (isset($_POST['category_id']) && $_POST['category_id'] == $category['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($category['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <!-- Location -->
                            <div class="mb-3">
                                <label for="location" class="form-label required">Địa điểm</label>
                                <input type="text" class="form-control" id="location" name="location" 
                                       value="<?= htmlspecialchars($_POST['location'] ?? '') ?>" 
                                       required maxlength="200">
                            </div>
                            
                            <!-- Dates -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="start_date" class="form-label required">Ngày bắt đầu</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" 
                                           value="<?= htmlspecialchars($_POST['start_date'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="end_date" class="form-label required">Ngày kết thúc</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" 
                                           value="<?= htmlspecialchars($_POST['end_date'] ?? '') ?>" required>
                                </div>
                            </div>
                            
                            <!-- Price -->
                            <div class="mb-3">
                                <label for="price" class="form-label required">Giá (VNĐ)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="price" name="price" 
                                           value="<?= htmlspecialchars($_POST['price'] ?? '0') ?>" 
                                           min="0" step="1000" required>
                                    <span class="input-group-text">₫</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Column -->
                        <div class="col-md-6">
                            <!-- Guide -->
                            <div class="mb-3">
                                <label for="guide_id" class="form-label">Hướng dẫn viên</label>
                                <select class="form-select" id="guide_id" name="guide_id">
                                    <option value="">-- Chọn HDV --</option>
                                    <?php foreach ($guides as $guide): ?>
                                        <option value="<?= $guide['id'] ?>" 
                                            <?= (isset($_POST['guide_id']) && $_POST['guide_id'] == $guide['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($guide['name']) ?> (<?= $guide['email'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <!-- Max Participants -->
                            <div class="mb-3">
                                <label for="max_participants" class="form-label required">Số lượng tối đa</label>
                                <input type="number" class="form-control" id="max_participants" name="max_participants" 
                                       value="<?= htmlspecialchars($_POST['max_participants'] ?? '20') ?>" 
                                       min="1" max="100" required>
                                <div class="form-text">Số lượng người tham gia tối đa cho tour này</div>
                            </div>
                            
                            <!-- Status -->
                            <div class="mb-3">
                                <label for="status_id" class="form-label">Trạng thái</label>
                                <select class="form-select" id="status_id" name="status_id">
                                    <option value="1" <?= (isset($_POST['status_id']) && $_POST['status_id'] == '1') ? 'selected' : 'selected' ?>>Chờ xác nhận</option>
                                    <option value="2" <?= (isset($_POST['status_id']) && $_POST['status_id'] == '2') ? 'selected' : '' ?>>Đã xác nhận</option>
                                    <option value="5" <?= (isset($_POST['status_id']) && $_POST['status_id'] == '5') ? 'selected' : '' ?>>Đã hủy</option>
                                </select>
                            </div>
                            
                            <!-- Schedule Detail -->
                            <div class="mb-3">
                                <label for="schedule_detail" class="form-label">Lịch trình chi tiết</label>
                                <textarea class="form-control" id="schedule_detail" name="schedule_detail" 
                                          rows="3"><?= htmlspecialchars($_POST['schedule_detail'] ?? '') ?></textarea>
                                <div class="form-text">Nhập lịch trình theo định dạng JSON hoặc văn bản</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="description" name="description" 
                                  rows="4"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="d-flex justify-content-between">
                        <a href="<?= BASE_URL ?>?act=tours" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Quay lại
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i> Thêm Tour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--end::Row-->

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Date validation
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    
    if (startDate && endDate) {
        startDate.addEventListener('change', function() {
            endDate.min = this.value;
            if (endDate.value && endDate.value < this.value) {
                endDate.value = this.value;
            }
        });
        
        // Set minimum dates to today
        const today = new Date().toISOString().split('T')[0];
        startDate.min = today;
        endDate.min = today;
        
        if (!startDate.value) startDate.value = today;
        if (!endDate.value) endDate.value = today;
    }
    
    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});
</script>

<?php
$content = ob_get_clean();<?php ob_start(); ?>

<!--begin::Row-->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="bi bi-plus-circle me-2"></i>
                    Thêm Tour mới
                </h3>
            </div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h5><i class="bi bi-exclamation-triangle-fill me-2"></i> Có lỗi xảy ra:</h5>
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="<?= BASE_URL ?>?act=tours/store" novalidate>
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <!-- Tour Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label required">Tên Tour</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" 
                                       required minlength="5" maxlength="200">
                                <div class="form-text">Tên tour phải có ít nhất 5 ký tự</div>
                            </div>
                            
                            <!-- Category -->
                            <div class="mb-3">
                                <label for="category_id" class="form-label required">Danh mục</label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">-- Chọn danh mục --</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>" 
                                            <?= (isset($_POST['category_id']) && $_POST['category_id'] == $category['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($category['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <!-- Location -->
                            <div class="mb-3">
                                <label for="location" class="form-label required">Địa điểm</label>
                                <input type="text" class="form-control" id="location" name="location" 
                                       value="<?= htmlspecialchars($_POST['location'] ?? '') ?>" 
                                       required maxlength="200">
                            </div>
                            
                            <!-- Dates -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="start_date" class="form-label required">Ngày bắt đầu</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" 
                                           value="<?= htmlspecialchars($_POST['start_date'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="end_date" class="form-label required">Ngày kết thúc</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" 
                                           value="<?= htmlspecialchars($_POST['end_date'] ?? '') ?>" required>
                                </div>
                            </div>
                            
                            <!-- Price -->
                            <div class="mb-3">
                                <label for="price" class="form-label required">Giá (VNĐ)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="price" name="price" 
                                           value="<?= htmlspecialchars($_POST['price'] ?? '0') ?>" 
                                           min="0" step="1000" required>
                                    <span class="input-group-text">₫</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Column -->
                        <div class="col-md-6">
                            <!-- Guide -->
                            <div class="mb-3">
                                <label for="guide_id" class="form-label">Hướng dẫn viên</label>
                                <select class="form-select" id="guide_id" name="guide_id">
                                    <option value="">-- Chọn HDV --</option>
                                    <?php foreach ($guides as $guide): ?>
                                        <option value="<?= $guide['id'] ?>" 
                                            <?= (isset($_POST['guide_id']) && $_POST['guide_id'] == $guide['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($guide['name']) ?> (<?= $guide['email'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <!-- Max Participants -->
                            <div class="mb-3">
                                <label for="max_participants" class="form-label required">Số lượng tối đa</label>
                                <input type="number" class="form-control" id="max_participants" name="max_participants" 
                                       value="<?= htmlspecialchars($_POST['max_participants'] ?? '20') ?>" 
                                       min="1" max="100" required>
                                <div class="form-text">Số lượng người tham gia tối đa cho tour này</div>
                            </div>
                            
                            <!-- Status -->
                            <div class="mb-3">
                                <label for="status_id" class="form-label">Trạng thái</label>
                                <select class="form-select" id="status_id" name="status_id">
                                    <option value="1" <?= (isset($_POST['status_id']) && $_POST['status_id'] == '1') ? 'selected' : 'selected' ?>>Chờ xác nhận</option>
                                    <option value="2" <?= (isset($_POST['status_id']) && $_POST['status_id'] == '2') ? 'selected' : '' ?>>Đã xác nhận</option>
                                    <option value="5" <?= (isset($_POST['status_id']) && $_POST['status_id'] == '5') ? 'selected' : '' ?>>Đã hủy</option>
                                </select>
                            </div>
                            
                            <!-- Schedule Detail -->
                            <div class="mb-3">
                                <label for="schedule_detail" class="form-label">Lịch trình chi tiết</label>
                                <textarea class="form-control" id="schedule_detail" name="schedule_detail" 
                                          rows="3"><?= htmlspecialchars($_POST['schedule_detail'] ?? '') ?></textarea>
                                <div class="form-text">Nhập lịch trình theo định dạng JSON hoặc văn bản</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="description" name="description" 
                                  rows="4"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="d-flex justify-content-between">
                        <a href="<?= BASE_URL ?>?act=tours" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Quay lại
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i> Thêm Tour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--end::Row-->

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Date validation
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    
    if (startDate && endDate) {
        startDate.addEventListener('change', function() {
            endDate.min = this.value;
            if (endDate.value && endDate.value < this.value) {
                endDate.value = this.value;
            }
        });
        
        // Set minimum dates to today
        const today = new Date().toISOString().split('T')[0];
        startDate.min = today;
        endDate.min = today;
        
        if (!startDate.value) startDate.value = today;
        if (!endDate.value) endDate.value = today;
    }
    
    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});
</script>

<?php
$content = ob_get_clean();
?>
?>