<div class="card">
    <div class="card-body">
        <h5>Lịch trình của tôi</h5>
        <?php if (empty($bookings)): ?>
            <p>Không có lịch trình nào được gán.</p>
        <?php else: ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Mã</th>
                        <th>Tour</th>
                        <th>Khách</th>
                        <th>Ngày khởi hành</th>
                        <th>Trạng thái</th>
                        <th>Ghi chú</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($bookings as $b): ?>
                    <tr>
                        <td><?= htmlspecialchars($b['booking_code']) ?></td>
                        <td><?= htmlspecialchars($b['tour_name'] ?? '') ?></td>
                        <td><?= htmlspecialchars($b['customer']['name'] ?? ($b['customer_id'] ?? '')) ?></td>
                        <td><?= htmlspecialchars($b['departure_date'] ?? '') ?></td>
                        <td><?= htmlspecialchars($b['status_name'] ?? '') ?></td>
                        <td><?= htmlspecialchars($b['notes'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
