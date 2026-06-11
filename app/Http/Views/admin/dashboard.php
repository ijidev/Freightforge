<?php $page = 'dashboard'; ob_start(); ?>
<div class="stats-grid">
    <div class="stat-card">
        <div class="num"><?= $stats['total'] ?? 0 ?></div>
        <div class="label">Total Shipments</div>
    </div>
    <div class="stat-card">
        <div class="num" style="color:#92400e;"><?= $stats['pending'] ?? 0 ?></div>
        <div class="label">Pending</div>
    </div>
    <div class="stat-card">
        <div class="num" style="color:#1e40af;"><?= $stats['inTransit'] ?? 0 ?></div>
        <div class="label">In Transit</div>
    </div>
    <div class="stat-card">
        <div class="num" style="color:#065f46;"><?= $stats['delivered'] ?? 0 ?></div>
        <div class="label">Delivered</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span>Recent Shipments</span>
        <a href="/admin/shipments" class="btn btn-primary btn-sm">View All</a>
    </div>
    <?php if (!empty($recentShipments)): ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Tracking #</th>
                    <th>Origin</th>
                    <th>Destination</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentShipments as $s): ?>
                <tr>
                    <td><a href="/admin/shipments/<?= $s['id'] ?>/edit" style="font-weight:600;"><?= htmlspecialchars($s['tracking_number']) ?></a></td>
                    <td><?= htmlspecialchars($s['origin']) ?></td>
                    <td><?= htmlspecialchars($s['destination']) ?></td>
                    <td><span class="status-badge status-<?= $s['status'] ?>"><?= str_replace('_', ' ', ucfirst($s['status'])) ?></span></td>
                    <td style="font-size:0.85rem;color:#94a3b8;"><?= date('M j, Y', strtotime($s['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <div class="icon">📦</div>
        <h3>No shipments yet</h3>
        <p>Create your first shipment to get started.</p>
        <a href="/admin/shipments/create" class="btn btn-primary" style="margin-top:1rem;">Create Shipment</a>
    </div>
    <?php endif; ?>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/admin.php'; ?>
