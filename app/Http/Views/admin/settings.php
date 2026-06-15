<?php $page = 'settings'; ob_start(); ?>
<div class="card">
    <div class="card-header">General Settings</div>
    <form method="POST" action="/admin/settings" enctype="multipart/form-data">
        <div class="form-group">
            <label>Site Name</label>
            <input type="text" name="site_name" value="<?= htmlspecialchars($settings['site_name'] ?? 'FreightForge') ?>">
        </div>
        <div class="form-group">
            <label>Site Email</label>
            <input type="email" name="site_email" value="<?= htmlspecialchars($settings['site_email'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Site Description</label>
            <textarea name="site_description" rows="3"><?= htmlspecialchars($settings['site_description'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
            <label>Site Logo</label>
            <?php if (!empty($settings['site_logo'])): ?>
            <div style="margin-bottom:0.5rem;">
                <img src="<?= htmlspecialchars($settings['site_logo']) ?>" alt="Logo" style="max-height:60px;border-radius:0.5rem;border:1px solid #e2e8f0;">
            </div>
            <?php endif; ?>
            <div class="upload-area" onclick="document.getElementById('logo-input').click()">
                <div class="icon">🖼</div>
                <p>Click to upload a new logo</p>
                <input type="file" name="site_logo" id="logo-input" accept="image/*">
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </div>
    </form>
</div>

<div class="card" style="margin-top:1.5rem;">
    <div class="card-header">Contact Information</div>
    <form method="POST" action="/admin/settings">
        <div class="form-group">
            <label>Phone Number</label>
            <input type="text" name="site_phone" value="<?= htmlspecialchars($settings['site_phone'] ?? '+1 (555) 123-4567') ?>">
        </div>
        <div class="form-group">
            <label>Address</label>
            <textarea name="site_address" rows="3"><?= htmlspecialchars($settings['site_address'] ?? "123 Logistics Ave, Suite 100\nPort City, PC 10001") ?></textarea>
        </div>
        <div class="form-group">
            <label>Business Hours</label>
            <textarea name="site_hours" rows="3"><?= htmlspecialchars($settings['site_hours'] ?? "Mon — Fri: 8:00 AM — 6:00 PM\nSat: 9:00 AM — 2:00 PM") ?></textarea>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Contact Info</button>
        </div>
    </form>
</div>

<div class="card" style="margin-top:1.5rem;">
    <div class="card-header">Email Configuration</div>
    <form method="POST" action="/admin/settings">
        <div class="form-row">
            <div class="form-group">
                <label>Mail Driver</label>
                <select name="mail_driver">
                    <option value="smtp" <?= ($settings['mail_driver'] ?? 'smtp') === 'smtp' ? 'selected' : '' ?>>SMTP</option>
                    <option value="mail" <?= ($settings['mail_driver'] ?? '') === 'mail' ? 'selected' : '' ?>>PHP Mail</option>
                </select>
            </div>
            <div class="form-group">
                <label>Encryption</label>
                <select name="mail_encryption">
                    <option value="">None</option>
                    <option value="tls" <?= ($settings['mail_encryption'] ?? '') === 'tls' ? 'selected' : '' ?>>TLS</option>
                    <option value="ssl" <?= ($settings['mail_encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>SMTP Host</label>
                <input type="text" name="mail_host" value="<?= htmlspecialchars($settings['mail_host'] ?? '') ?>" placeholder="smtp.example.com">
            </div>
            <div class="form-group">
                <label>SMTP Port</label>
                <input type="text" name="mail_port" value="<?= htmlspecialchars($settings['mail_port'] ?? '587') ?>" placeholder="587">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>SMTP Username</label>
                <input type="text" name="mail_username" value="<?= htmlspecialchars($settings['mail_username'] ?? '') ?>" placeholder="user@example.com">
            </div>
            <div class="form-group">
                <label>SMTP Password</label>
                <input type="password" name="mail_password" value="<?= htmlspecialchars($settings['mail_password'] ?? '') ?>" placeholder="Enter password">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>From Address</label>
                <input type="email" name="mail_from_address" value="<?= htmlspecialchars($settings['mail_from_address'] ?? '') ?>" placeholder="noreply@example.com">
            </div>
            <div class="form-group">
                <label>From Name</label>
                <input type="text" name="mail_from_name" value="<?= htmlspecialchars($settings['mail_from_name'] ?? '') ?>" placeholder="FreightForge">
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </div>
    </form>
</div>
<div class="card" style="margin-top:1.5rem;">
    <div class="card-header">Database Tools</div>
    <div style="padding:1.5rem;">
        <p style="margin-bottom:1rem;color:#64748b;">Seed missing tables and initial data without reinstalling. This will add any missing tables and default content (sections, settings, statuses).</p>
        <form method="POST" action="/admin/seed-database" onsubmit="return confirm('Are you sure you want to seed the database? This will add missing tables and default data.');">
            <button type="submit" class="btn btn-primary" style="background:#059669;">Run Database Seed</button>
        </form>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/admin.php'; ?>
