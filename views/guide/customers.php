<div class="card">
    <div class="card-body">
        <h5>Khách hàng của tôi</h5>
        <?php if (empty($customers)): ?>
            <p>Không tìm thấy khách hàng nào.</p>
        <?php else: ?>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Tên</th>
                        <th>Điện thoại</th>
                        <th>Email</th>
                        <th>Địa chỉ</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($customers as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['name'] ?? '') ?></td>
                        <td><?= htmlspecialchars($c['phone'] ?? '') ?></td>
                        <td><?= htmlspecialchars($c['email'] ?? '') ?></td>
                        <td><?= htmlspecialchars($c['address'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
