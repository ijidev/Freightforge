<?php $page = 'shipment-settings'; $tab = $_GET['tab'] ?? 'statuses'; ob_start(); ?>
<div style="margin-bottom:1.5rem;">
    <h2 style="font-size:1.25rem;font-weight:600;margin-bottom:0.25rem;">Shipment Settings</h2>
    <p style="color:#64748b;font-size:0.9rem;">Configure statuses and other shipment preferences.</p>
</div>

<div style="display:flex;gap:0;border-bottom:2px solid #e2e8f0;margin-bottom:1.5rem;">
    <a href="/admin/shipment-settings?tab=statuses" style="padding:0.65rem 1.25rem;font-size:0.9rem;font-weight:600;color:<?= $tab === 'statuses' ? '#f97316' : '#64748b' ?>;border-bottom:2px solid <?= $tab === 'statuses' ? '#f97316' : 'transparent' ?>;margin-bottom:-2px;text-decoration:none;transition:all 0.2s;">
        Custom Statuses
    </a>
    <a href="/admin/shipment-settings?tab=general" style="padding:0.65rem 1.25rem;font-size:0.9rem;font-weight:600;color:<?= $tab === 'general' ? '#f97316' : '#64748b' ?>;border-bottom:2px solid <?= $tab === 'general' ? '#f97316' : 'transparent' ?>;margin-bottom:-2px;text-decoration:none;transition:all 0.2s;opacity:0.5;cursor:not-allowed;" onclick="return false;">
        General
    </a>
</div>

<?php if ($tab === 'statuses'): ?>
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:0.75rem;">
    <h3 style="font-size:1.1rem;font-weight:600;">Custom Statuses</h3>
    <button class="btn btn-primary btn-sm" onclick="openModal('create-status-modal')">+ New Status</button>
</div>

<div id="create-status-modal" class="modal-overlay">
    <div class="modal">
        <h3>Create Status</h3>
        <form method="POST" action="/admin/statuses">
            <div class="form-group">
                <label>Name *</label>
                <input type="text" name="name" placeholder="e.g. Out for Delivery" required>
                <p style="font-size:0.8rem;color:#94a3b8;margin-top:0.25rem;">Slug will be auto-generated from the name.</p>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Color</label>
                    <select name="color">
                        <option value="blue">Blue</option>
                        <option value="green">Green</option>
                        <option value="yellow">Yellow</option>
                        <option value="red">Red</option>
                        <option value="purple">Purple</option>
                        <option value="gray">Gray</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Sort Order</label>
                    <input type="number" name="sort_order" value="0" min="0">
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-ghost" onclick="closeModal('create-status-modal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
        </form>
    </div>
</div>

<div id="edit-status-modal" class="modal-overlay">
    <div class="modal">
        <h3>Edit Status</h3>
        <form method="POST" action="" id="edit-status-form">
            <div class="form-group">
                <label>Name *</label>
                <input type="text" name="name" id="edit-status-name" required>
                <p style="font-size:0.8rem;color:#94a3b8;margin-top:0.25rem;">Slug will be auto-generated from the name.</p>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Color</label>
                    <select name="color" id="edit-status-color">
                        <option value="blue">Blue</option>
                        <option value="green">Green</option>
                        <option value="yellow">Yellow</option>
                        <option value="red">Red</option>
                        <option value="purple">Purple</option>
                        <option value="gray">Gray</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Sort Order</label>
                    <input type="number" name="sort_order" id="edit-status-order" min="0">
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-ghost" onclick="closeModal('edit-status-modal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <?php if (!empty($statuses)): ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Color</th>
                    <th>Order</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($statuses as $st):
                    $jsName = str_replace(["\\", "'"], ["\\\\", "\\'"], htmlspecialchars($st['name'], ENT_NOQUOTES));
                ?>
                <tr>
                    <td style="font-weight:600;"><?= htmlspecialchars($st['name']) ?></td>
                    <td><code style="background:#f1f5f9;padding:0.15rem 0.5rem;border-radius:0.25rem;font-size:0.85rem;"><?= htmlspecialchars($st['slug']) ?></code></td>
                    <td><span class="status-badge" style="background:#f1f5f9;color:#475569;"><?= htmlspecialchars($st['color']) ?></span></td>
                    <td><?= (int) $st['sort_order'] ?></td>
                    <td>
                        <div class="table-actions">
                            <button class="btn btn-ghost btn-sm" onclick="editStatus(<?= $st['id'] ?>, '<?= $jsName ?>', '<?= htmlspecialchars($st['color']) ?>', <?= (int) $st['sort_order'] ?>)">Edit</button>
                            <form method="POST" action="/admin/statuses/<?= $st['id'] ?>/delete" style="display:inline;" onsubmit="return confirm('Delete status \'<?= htmlspecialchars($st['name']) ?>\'?')">
                                <button type="submit" class="btn btn-ghost btn-sm" style="color:#ef4444;">Delete</button>
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
        <div class="icon">🏷️</div>
        <h3>No custom statuses</h3>
        <p>Create statuses to track shipment progress beyond the defaults.</p>
        <button class="btn btn-primary" style="margin-top:1rem;" onclick="openModal('create-status-modal')">Create Status</button>
    </div>
    <?php endif; ?>

    <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid #f1f5f9;">
        <p style="font-size:0.85rem;color:#94a3b8;">Default statuses (Pending, In Transit, Delivered, Cancelled) are always available.</p>
    </div>
</div>

<script>
function editStatus(id, name, color, sortOrder) {
    document.getElementById('edit-status-form').action = '/admin/statuses/' + id;
    document.getElementById('edit-status-name').value = name;
    document.getElementById('edit-status-color').value = color;
    document.getElementById('edit-status-order').value = sortOrder;
    openModal('edit-status-modal');
}
</script>
<?php elseif ($tab === 'general'): ?>
<div class="card">
    <div class="empty-state">
        <div class="icon">⚙️</div>
        <h3>General Settings</h3>
        <p>Shipment-wide preferences will be available here in a future update.</p>
    </div>
</div>
<?php endif; ?>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/admin.php'; ?>
