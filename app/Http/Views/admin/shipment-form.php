<?php $page = 'shipments'; $isEdit = !is_null($shipment); ob_start(); ?>
<div class="card">
    <div class="card-header">
        <span><?= $isEdit ? 'Edit Shipment' : 'Create New Shipment' ?></span>
        <a href="/admin/shipments" class="btn btn-ghost btn-sm">← Back to Shipments</a>
    </div>

    <?php if ($isEdit): ?>
    <div style="margin-bottom:1.5rem;padding:0.75rem 1rem;background:#fff7ed;border-radius:0.5rem;border:1px solid #fed7aa;">
        <strong>Tracking Number:</strong> <?= htmlspecialchars($shipment['tracking_number']) ?>
        <span class="status-badge status-<?= $shipment['status'] ?>" style="margin-left:0.75rem;"><?= str_replace('_', ' ', ucfirst($shipment['status'])) ?></span>
    </div>
    <?php endif; ?>

    <form method="POST" action="/admin/shipments<?= $isEdit ? '/' . $shipment['id'] : '' ?>">
        <h3 style="font-size:0.95rem;font-weight:600;color:#475569;margin-bottom:1rem;">Sender Information</h3>
        <div class="form-row">
            <div class="form-group">
                <label>Sender Name *</label>
                <input type="text" name="sender_name" required value="<?= htmlspecialchars($shipment['sender_name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Sender Email *</label>
                <input type="email" name="sender_email" required value="<?= htmlspecialchars($shipment['sender_email'] ?? '') ?>">
            </div>
        </div>
        <div class="form-group">
            <label>Sender Phone</label>
            <input type="text" name="sender_phone" value="<?= htmlspecialchars($shipment['sender_phone'] ?? '') ?>">
        </div>

        <h3 style="font-size:0.95rem;font-weight:600;color:#475569;margin:1.5rem 0 1rem;">Recipient Information</h3>
        <div class="form-row">
            <div class="form-group">
                <label>Recipient Name *</label>
                <input type="text" name="recipient_name" required value="<?= htmlspecialchars($shipment['recipient_name'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Recipient Email *</label>
                <input type="email" name="recipient_email" required value="<?= htmlspecialchars($shipment['recipient_email'] ?? '') ?>">
            </div>
        </div>
        <div class="form-group">
            <label>Recipient Phone</label>
            <input type="text" name="recipient_phone" value="<?= htmlspecialchars($shipment['recipient_phone'] ?? '') ?>">
        </div>

        <h3 style="font-size:0.95rem;font-weight:600;color:#475569;margin:1.5rem 0 1rem;">Shipment Details</h3>
        <div class="form-row">
            <div class="form-group">
                <label>Origin *</label>
                <input type="text" name="origin" required value="<?= htmlspecialchars($shipment['origin'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Destination *</label>
                <input type="text" name="destination" required value="<?= htmlspecialchars($shipment['destination'] ?? '') ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Weight (kg)</label>
                <input type="number" step="0.01" name="weight" value="<?= htmlspecialchars($shipment['weight'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="pending" <?= ($shipment['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="in_transit" <?= ($shipment['status'] ?? '') === 'in_transit' ? 'selected' : '' ?>>In Transit</option>
                    <option value="delivered" <?= ($shipment['status'] ?? '') === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                    <option value="cancelled" <?= ($shipment['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="3"><?= htmlspecialchars($shipment['description'] ?? '') ?></textarea>
        </div>

        <div class="form-actions">
            <a href="/admin/shipments" class="btn btn-ghost">Cancel</a>
            <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Update Shipment' : 'Create Shipment' ?></button>
        </div>
    </form>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/admin.php'; ?>
