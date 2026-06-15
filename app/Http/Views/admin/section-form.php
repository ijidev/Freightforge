<?php $page = 'sections'; $isCreate = !empty($isCreate); ob_start(); ?>
<script>
function selectLayout(layout) {
    document.querySelectorAll('.layout-option').forEach(el => el.classList.remove('selected'));
    document.querySelector(`.layout-option[data-layout="${layout}"]`)?.classList.add('selected');
    document.getElementById('layout-input').value = layout;
    document.querySelectorAll('.block-fields').forEach(el => el.style.display = 'none');
    const target = document.getElementById('fields-' + layout);
    if (target) target.style.display = 'block';
}

function addLogoRow() {
    const container = document.getElementById('logos-container');
    const idx = container.children.length;
    const row = document.createElement('div');
    row.className = 'logo-row';
    row.innerHTML = `
        <input type="text" name="logo_names[]" placeholder="Partner name" style="flex:1;">
        <div class="logo-upload-wrap" style="flex:1;">
            <input type="file" name="logo_images[]" accept="image/*" onchange="previewLogo(this)">
            <div class="logo-preview"></div>
        </div>
        <button type="button" class="btn btn-ghost btn-sm" onclick="this.parentElement.remove()" style="color:#ef4444;flex-shrink:0;">✕</button>
    `;
    container.appendChild(row);
}

function previewLogo(input) {
    const wrap = input.closest('.logo-upload-wrap');
    const preview = wrap.querySelector('.logo-preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" style="max-height:30px;border-radius:4px;">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<style>
.layout-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}
.layout-option {
    border: 2px solid #e2e8f0;
    border-radius: 0.75rem;
    padding: 1rem 0.75rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
    background: #fff;
}
.layout-option:hover {
    border-color: #f97316;
    background: #fff7ed;
}
.layout-option.selected {
    border-color: #f97316;
    background: #fff7ed;
    box-shadow: 0 0 0 3px rgba(249,115,22,0.15);
}
.layout-option .layout-icon {
    font-size: 1.75rem;
    margin-bottom: 0.35rem;
}
.layout-option .layout-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #475569;
}
.layout-option.selected .layout-label {
    color: #f97316;
}
.logo-row {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
    padding: 0.75rem;
    background: #f8fafc;
    border-radius: 0.5rem;
    border: 1px solid #e2e8f0;
}
.logo-row input[type="text"] {
    padding: 0.5rem 0.75rem;
    border: 2px solid #e2e8f0;
    border-radius: 0.5rem;
    font-size: 0.85rem;
    outline: none;
    font-family: inherit;
}
.logo-row input[type="text"]:focus {
    border-color: #f97316;
}
.logo-upload-wrap {
    position: relative;
}
.logo-upload-wrap input[type="file"] {
    font-size: 0.8rem;
    color: #64748b;
}
.logo-preview {
    margin-top: 0.25rem;
}
.block-fields {
    animation: fadeIn 0.2s ease;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(4px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

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
            <label style="font-size:0.9rem;font-weight:600;margin-bottom:0.75rem;">Block Style</label>
            <input type="hidden" name="layout" id="layout-input" value="<?= htmlspecialchars($section['layout'] ?? 'default') ?>">
            <div class="layout-grid">
                <?php
                $layouts = [
                    'hero' => ['icon' => '🖼', 'label' => 'Hero Banner'],
                    'features' => ['icon' => '📊', 'label' => 'Features Grid'],
                    'steps' => ['icon' => '📋', 'label' => 'Steps'],
                    'stats' => ['icon' => '📈', 'label' => 'Stats'],
                    'partners' => ['icon' => '🤝', 'label' => 'Partners/Logos'],
                    'cta' => ['icon' => '🔵', 'label' => 'Call to Action'],
                    'content' => ['icon' => '📝', 'label' => 'Text Content'],
                    'default' => ['icon' => '📄', 'label' => 'Default'],
                ];
                $currentLayout = $section['layout'] ?? 'default';
                foreach ($layouts as $key => $info):
                ?>
                <div class="layout-option <?= $currentLayout === $key ? 'selected' : '' ?>" data-layout="<?= $key ?>" onclick="selectLayout('<?= $key ?>')">
                    <div class="layout-icon"><?= $info['icon'] ?></div>
                    <div class="layout-label"><?= $info['label'] ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <?php
        $decodedContent = [];
        if (!empty($section['content'])) {
            $decodedContent = json_decode($section['content'], true) ?: [];
        }
        $logos = [];
        if ($currentLayout === 'partners' && !empty($decodedContent) && is_array($decodedContent)) {
            $logos = $decodedContent;
        }
        ?>

        <!-- Hero Fields -->
        <div class="block-fields" id="fields-hero" style="display:<?= $currentLayout === 'hero' ? 'block' : 'none' ?>;">
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" value="<?= htmlspecialchars($section['title'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Subtitle</label>
                <textarea name="subtitle" rows="3"><?= htmlspecialchars($section['subtitle'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label>Background Image</label>
                <?php if (!empty($section['image_path'])): ?>
                <div style="margin-bottom:0.5rem;">
                    <img src="<?= htmlspecialchars($section['image_path']) ?>" alt="" style="max-height:80px;border-radius:0.5rem;border:1px solid #e2e8f0;">
                </div>
                <?php endif; ?>
                <div class="upload-area" onclick="document.getElementById('section-image').click()">
                    <div class="icon">🖼</div>
                    <p>Click to upload an image</p>
                    <input type="file" name="image" id="section-image" accept="image/*">
                </div>
            </div>
        </div>

        <!-- Features Fields -->
        <div class="block-fields" id="fields-features" style="display:<?= $currentLayout === 'features' ? 'block' : 'none' ?>;">
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" value="<?= htmlspecialchars($section['title'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Subtitle</label>
                <input type="text" name="subtitle" value="<?= htmlspecialchars($section['subtitle'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Content (JSON array of {icon, title, desc})</label>
                <textarea name="content" rows="8"><?= htmlspecialchars($section['content'] ?? '') ?></textarea>
            </div>
        </div>

        <!-- Steps Fields -->
        <div class="block-fields" id="fields-steps" style="display:<?= $currentLayout === 'steps' ? 'block' : 'none' ?>;">
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" value="<?= htmlspecialchars($section['title'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Subtitle</label>
                <input type="text" name="subtitle" value="<?= htmlspecialchars($section['subtitle'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Content (JSON array of {icon, title, desc})</label>
                <textarea name="content" rows="8"><?= htmlspecialchars($section['content'] ?? '') ?></textarea>
            </div>
        </div>

        <!-- Stats Fields -->
        <div class="block-fields" id="fields-stats" style="display:<?= $currentLayout === 'stats' ? 'block' : 'none' ?>;">
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" value="<?= htmlspecialchars($section['title'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Subtitle</label>
                <input type="text" name="subtitle" value="<?= htmlspecialchars($section['subtitle'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Content (comma-separated: value1,label1,value2,label2,...)</label>
                <textarea name="content" rows="4"><?= htmlspecialchars($section['content'] ?? '') ?></textarea>
            </div>
        </div>

        <!-- Partners / Logos Fields -->
        <div class="block-fields" id="fields-partners" style="display:<?= $currentLayout === 'partners' ? 'block' : 'none' ?>;">
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" value="<?= htmlspecialchars($section['title'] ?? 'Our Partners') ?>">
            </div>
            <div class="form-group">
                <label>Subtitle</label>
                <input type="text" name="subtitle" value="<?= htmlspecialchars($section['subtitle'] ?? 'Trusted logistics providers we work with') ?>">
            </div>
            <div class="form-group">
                <label style="display:flex;justify-content:space-between;align-items:center;">
                    <span>Partner Logos</span>
                    <button type="button" class="btn btn-ghost btn-sm" onclick="addLogoRow()" style="font-size:0.8rem;">+ Add Logo</button>
                </label>
                <div id="logos-container">
                    <?php if (!empty($logos)): ?>
                        <?php foreach ($logos as $i => $logo): ?>
                        <div class="logo-row">
                            <input type="text" name="logo_names[]" value="<?= htmlspecialchars($logo['name'] ?? '') ?>" placeholder="Partner name" style="flex:1;">
                            <div class="logo-upload-wrap" style="flex:1;">
                                <?php if (!empty($logo['logo'])): ?>
                                <div style="display:flex;align-items:center;gap:0.5rem;">
                                    <img src="<?= htmlspecialchars($logo['logo']) ?>" alt="" style="max-height:30px;border-radius:4px;">
                                    <span style="font-size:0.75rem;color:#64748b;"><?= htmlspecialchars(basename($logo['logo'])) ?></span>
                                    <input type="hidden" name="existing_logos[]" value="<?= htmlspecialchars($logo['logo']) ?>">
                                </div>
                                <?php else: ?>
                                <input type="file" name="logo_images[]" accept="image/*">
                                <?php endif; ?>
                            </div>
                            <button type="button" class="btn btn-ghost btn-sm" onclick="this.parentElement.remove()" style="color:#ef4444;flex-shrink:0;">✕</button>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <small style="color:#64748b;font-size:0.75rem;display:block;margin-top:0.25rem;">Add partner names and upload their logos.</small>
            </div>
        </div>

        <!-- CTA Fields -->
        <div class="block-fields" id="fields-cta" style="display:<?= $currentLayout === 'cta' ? 'block' : 'none' ?>;">
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" value="<?= htmlspecialchars($section['title'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Subtitle</label>
                <textarea name="subtitle" rows="3"><?= htmlspecialchars($section['subtitle'] ?? '') ?></textarea>
            </div>
        </div>

        <!-- Content Fields -->
        <div class="block-fields" id="fields-content" style="display:<?= $currentLayout === 'content' ? 'block' : 'none' ?>;">
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" value="<?= htmlspecialchars($section['title'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Content</label>
                <textarea name="content" rows="10"><?= htmlspecialchars($section['content'] ?? '') ?></textarea>
            </div>
        </div>

        <!-- Default Fields -->
        <div class="block-fields" id="fields-default" style="display:<?= $currentLayout === 'default' ? 'block' : 'none' ?>;">
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
                    <img src="<?= htmlspecialchars($section['image_path']) ?>" alt="" style="max-height:80px;border-radius:0.5rem;border:1px solid #e2e8f0;">
                </div>
                <?php endif; ?>
                <div class="upload-area" onclick="document.getElementById('section-image-default').click()">
                    <div class="icon">🖼</div>
                    <p>Click to upload an image</p>
                    <input type="file" name="image" id="section-image-default" accept="image/*">
                </div>
            </div>
        </div>

        <!-- Single file input for hero/default (reused) -->
        <?php if ($currentLayout !== 'hero' && $currentLayout !== 'default'): ?>
        <input type="hidden" name="existing_image" value="<?= htmlspecialchars($section['image_path'] ?? '') ?>">
        <?php endif; ?>

        <input type="hidden" name="current_layout" value="<?= htmlspecialchars($currentLayout) ?>">

        <div class="form-actions" style="margin-top:1.5rem;">
            <button type="submit" class="btn btn-primary"><?= $isCreate ? 'Create Section' : 'Save Section' ?></button>
        </div>
    </form>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/admin.php'; ?>
