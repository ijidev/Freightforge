<?php $page = 'shipments'; ob_start(); ?>
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:0.75rem;">
    <form method="GET" action="/admin/shipments" style="display:flex;gap:0.5rem;flex-wrap:wrap;">
        <input type="text" name="search" placeholder="Search shipments..." value="<?= htmlspecialchars($searchQuery ?? '') ?>" style="padding:0.5rem 0.75rem;border:2px solid #e2e8f0;border-radius:0.5rem;font-size:0.85rem;outline:none;font-family:inherit;min-width:220px;">
        <select name="status" style="padding:0.5rem 0.75rem;border:2px solid #e2e8f0;border-radius:0.5rem;font-size:0.85rem;outline:none;font-family:inherit;">
            <option value="">All Statuses</option>
            <option value="pending" <?= ($statusFilter ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="in_transit" <?= ($statusFilter ?? '') === 'in_transit' ? 'selected' : '' ?>>In Transit</option>
            <option value="delivered" <?= ($statusFilter ?? '') === 'delivered' ? 'selected' : '' ?>>Delivered</option>
            <option value="cancelled" <?= ($statusFilter ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
        </select>
        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
        <a href="/admin/shipments" class="btn btn-ghost btn-sm">Clear</a>
    </form>
    <a href="/admin/shipments/create" class="btn btn-primary">+ New Shipment</a>
</div>

<div class="card">
    <?php if (!empty($shipments)): ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Tracking #</th>
                    <th>Origin</th>
                    <th>Destination</th>
                    <th>Sender</th>
                    <th>Recipient</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($shipments as $s): ?>
                <tr>
                    <td><a href="/admin/shipments/<?= $s['id'] ?>/edit" style="font-weight:600;font-size:0.85rem;"><?= htmlspecialchars($s['tracking_number']) ?></a></td>
                    <td><?= htmlspecialchars($s['origin']) ?></td>
                    <td><?= htmlspecialchars($s['destination']) ?></td>
                    <td style="font-size:0.85rem;"><?= htmlspecialchars($s['sender_name']) ?></td>
                    <td style="font-size:0.85rem;"><?= htmlspecialchars($s['recipient_name']) ?></td>
                    <td><span class="status-badge status-<?= $s['status'] ?>"><?= str_replace('_', ' ', ucfirst($s['status'])) ?></span></td>
                    <td style="font-size:0.85rem;color:#94a3b8;"><?= date('M j, Y', strtotime($s['created_at'])) ?></td>
                    <td>
                        <div class="table-actions">
                            <a href="/admin/shipments/<?= $s['id'] ?>/edit" class="btn btn-ghost btn-sm">Edit</a>
                            <form method="POST" action="/admin/shipments/<?= $s['id'] ?>/delete" style="display:inline;" onsubmit="return confirm('Delete this shipment?')">
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <div class="icon">📦</div>
        <h3>No shipments found</h3>
        <p><?= !empty($searchQuery) || !empty($statusFilter) ? 'Try adjusting your search or filter criteria.' : 'Create your first shipment to get started.' ?></p>
        <a href="/admin/shipments/create" class="btn btn-primary" style="margin-top:1rem;">Create Shipment</a>
    </div>
    <?php endif; ?>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/admin.php'; ?>
