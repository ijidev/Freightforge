<?php $page = 'profile'; ob_start(); ?>
<div class="card">
    <div class="card-header">Profile Information</div>
    <form method="POST" action="/admin/profile">
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>">
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Profile</button>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header">Change Password</div>
    <form method="POST" action="/admin/profile">
        <div class="form-row">
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="password" placeholder="Min 6 characters" minlength="6">
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="password_confirm" placeholder="Repeat password">
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Change Password</button>
        </div>
    </form>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/admin.php'; ?>
