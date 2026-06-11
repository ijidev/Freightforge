<?php $page = 'shipments'; ob_start(); ?>
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:0.75rem;">
    <h2 style="font-size:1.25rem;font-weight:600;"><?= htmlspecialchars($shipment['tracking_number']) ?></h2>
    <div style="display:flex;gap:0.5rem;">
        <a href="/admin/shipments/<?= $shipment['id'] ?>/edit" class="btn btn-primary btn-sm">Edit Shipment</a>
        <a href="/admin/shipments" class="btn btn-ghost btn-sm">← Back</a>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-header">Shipment Information</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
            <div>
                <div style="font-size:0.75rem;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;">Tracking Number</div>
                <div style="font-weight:600;font-size:0.95rem;"><?= htmlspecialchars($shipment['tracking_number']) ?></div>
            </div>
            <div>
                <div style="font-size:0.75rem;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;">Status</div>
                <span class="status-badge status-<?= $shipment['status'] ?>"><?= str_replace('_', ' ', ucfirst($shipment['status'])) ?></span>
            </div>
            <div>
                <div style="font-size:0.75rem;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;">Origin</div>
                <div style="font-weight:600;"><?= htmlspecialchars($shipment['origin']) ?></div>
            </div>
            <div>
                <div style="font-size:0.75rem;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;">Destination</div>
                <div style="font-weight:600;"><?= htmlspecialchars($shipment['destination']) ?></div>
            </div>
            <div>
                <div style="font-size:0.75rem;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;">Weight</div>
                <div><?= $shipment['weight'] ? htmlspecialchars($shipment['weight']) . ' kg' : '—' ?></div>
            </div>
            <div>
                <div style="font-size:0.75rem;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;">Created</div>
                <div><?= date('M j, Y', strtotime($shipment['created_at'])) ?></div>
            </div>
        </div>
        <?php if (!empty($shipment['description'])): ?>
        <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid #f1f5f9;">
            <div style="font-size:0.75rem;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.25rem;">Description</div>
            <div style="font-size:0.9rem;color:#475569;"><?= htmlspecialchars($shipment['description']) ?></div>
        </div>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-header">Sender & Recipient</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
            <div>
                <div style="font-size:0.75rem;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">Sender</div>
                <div style="font-weight:600;"><?= htmlspecialchars($shipment['sender_name']) ?></div>
                <div style="font-size:0.85rem;color:#64748b;"><?= htmlspecialchars($shipment['sender_email']) ?></div>
                <?php if (!empty($shipment['sender_phone'])): ?>
                <div style="font-size:0.85rem;color:#64748b;"><?= htmlspecialchars($shipment['sender_phone']) ?></div>
                <?php endif; ?>
            </div>
            <div>
                <div style="font-size:0.75rem;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">Recipient</div>
                <div style="font-weight:600;"><?= htmlspecialchars($shipment['recipient_name']) ?></div>
                <div style="font-size:0.85rem;color:#64748b;"><?= htmlspecialchars($shipment['recipient_email']) ?></div>
                <?php if (!empty($shipment['recipient_phone'])): ?>
                <div style="font-size:0.85rem;color:#64748b;"><?= htmlspecialchars($shipment['recipient_phone']) ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span>Add Status Update</span>
    </div>
    <form method="POST" action="/admin/shipments/<?= $shipment['id'] ?>/status" style="display:flex;gap:0.75rem;flex-wrap:wrap;align-items:flex-end;">
        <div class="form-group" style="flex:1;min-width:150px;margin-bottom:0;">
            <label>Status</label>
            <select name="status" required>
                <?php foreach ($availableStatuses as $st): ?>
                <option value="<?= htmlspecialchars($st['slug']) ?>" <?= $st['slug'] === $shipment['status'] ? 'selected' : '' ?>><?= htmlspecialchars($st['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group" style="flex:2;min-width:200px;margin-bottom:0;">
            <label>Remark</label>
            <input type="text" name="remark" placeholder="Optional note about this status update">
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Add Update</button>
    </form>
</div>

<div class="card">
    <div class="card-header">Status History</div>
    <?php if (!empty($statusHistory)): ?>
    <div class="tracking-timeline">
        <?php foreach ($statusHistory as $i => $sh): 
            $isLast = $i === count($statusHistory) - 1;
            $stepClass = $isLast ? 'active' : 'completed';
        ?>
        <div class="tracking-step <?= $stepClass ?>">
            <div class="tracking-dot"><?= $isLast ? '●' : '✓' ?></div>
            <div class="tracking-content">
                <h4><?= str_replace('_', ' ', ucfirst($sh['status'])) ?></h4>
                <p><?= date('M j, Y g:i A', strtotime($sh['created_at'])) ?></p>
                <?php if (!empty($sh['remark'])): ?>
                <p style="margin-top:0.25rem;font-style:italic;color:#64748b;">"<?= htmlspecialchars($sh['remark']) ?>"</p>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <div class="icon">📋</div>
        <h3>No status history</h3>
    </div>
    <?php endif; ?>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/admin.php'; ?>
