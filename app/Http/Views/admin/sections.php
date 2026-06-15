<?php $page = 'sections'; ob_start(); ?>
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
    <h2 style="font-size:1.25rem;font-weight:600;">Content Sections</h2>
    <a href="/admin/sections/create" class="btn btn-primary btn-sm">+ New Section</a>
</div>

<?php if (!empty($grouped)): ?>
    <?php foreach ($grouped as $pageName => $sections): ?>
    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-header">
            <span><?= ucfirst(htmlspecialchars($pageName)) ?> Sections</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Section Key</th>
                        <th>Layout</th>
                        <th>Title</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sections as $section): ?>
                    <tr>
                        <td><code style="background:#f1f5f9;padding:0.15rem 0.5rem;border-radius:0.25rem;font-size:0.85rem;"><?= htmlspecialchars($section['section_key']) ?></code></td>
                        <td><span class="layout-badge layout-<?= htmlspecialchars($section['layout'] ?? 'default') ?>"><?= htmlspecialchars($section['layout'] ?? 'default') ?></span></td>
                        <td><?= htmlspecialchars($section['title'] ?? '—') ?></td>
                        <td><?= !empty($section['image_path']) ? '✓' : '—' ?></td>
                        <td>
                            <a href="/admin/sections/<?= htmlspecialchars($section['page']) ?>/<?= htmlspecialchars($section['section_key']) ?>" class="btn btn-ghost btn-sm">Edit</a>
                            <form method="POST" action="/admin/sections/<?= htmlspecialchars($section['page']) ?>/<?= htmlspecialchars($section['section_key']) ?>/delete" style="display:inline;" onsubmit="return confirm('Delete this section?')">
                                <button type="submit" class="btn btn-ghost btn-sm" style="color:#ef4444;">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endforeach; ?>
<?php else: ?>
<div class="card">
    <div class="empty-state">
        <div class="icon">📄</div>
        <h3>No content sections yet</h3>
        <p>Create your first content section to manage front-end content from the admin panel.</p>
        <a href="/admin/sections/create" class="btn btn-primary" style="margin-top:1rem;">Create Section</a>
    </div>
</div>
<?php endif; ?>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/admin.php'; ?>
