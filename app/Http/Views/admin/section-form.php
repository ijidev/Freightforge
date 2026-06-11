<?php $page = 'sections'; $isCreate = !empty($isCreate); ob_start(); ?>
<div class="card">
    <div class="card-header">
        <span><?= $isCreate ? 'New Section' : 'Edit Section: <code style="background:#f1f5f9;padding:0.15rem 0.5rem;border-radius:0.25rem;">' . htmlspecialchars($sectionKey) . '</code>' ?></span>
        <a href="/admin/sections" class="btn btn-ghost btn-sm">← Back to Sections</a>
    </div>
    <form method="POST" action="<?= $isCreate ? '/admin/sections/create' : '/admin/sections/' . htmlspecialchars($sectionPage) . '/' . htmlspecialchars($sectionKey) ?>" enctype="multipart/form-data">
        <?php if ($isCreate): ?>
        <div class="form-row">
            <div class="form-group">
                <label>Page</label>
                <select name="page" required>
                    <option value="">— Select Page —</option>
                    <option value="home">Home</option>
                    <option value="about">About</option>
                    <option value="contact">Contact</option>
                </select>
            </div>
            <div class="form-group">
                <label>Section Key</label>
                <input type="text" name="section_key" placeholder="e.g. hero, features, mission" required>
            </div>
        </div>
        <?php endif; ?>
        <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" value="<?= htmlspecialchars($section['title'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Subtitle</label>
            <input type="text" name="subtitle" value="<?= htmlspecialchars($section['subtitle'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Content</label>
            <textarea name="content" rows="6"><?= htmlspecialchars($section['content'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
            <label>Image</label>
            <?php if (!empty($section['image_path'])): ?>
            <div style="margin-bottom:0.5rem;">
                <img src="<?= htmlspecialchars($section['image_path']) ?>" alt="Section image" style="max-height:100px;border-radius:0.5rem;border:1px solid #e2e8f0;">
            </div>
            <?php endif; ?>
            <div class="upload-area" onclick="document.getElementById('section-image').click()">
                <div class="icon">🖼</div>
                <p>Click to upload an image</p>
                <input type="file" name="image" id="section-image" accept="image/*">
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?= $isCreate ? 'Create Section' : 'Save Section' ?></button>
        </div>
    </form>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/admin.php'; ?>
