<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Dashboard') ?> — <?= htmlspecialchars($siteName ?? 'FreightForge') ?> Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; color: #1e293b; background: #f8fafc; }
        a { color: #f97316; text-decoration: none; }
        a:hover { color: #ea580c; }

        .sidebar {
            position: fixed; top: 0; left: 0;
            width: 260px; height: 100vh;
            background: #1e293b; color: #e2e8f0;
            padding: 1.5rem 0;
            overflow-y: auto; z-index: 40;
        }
        .sidebar-brand {
            padding: 0 1.5rem 1.5rem;
            border-bottom: 1px solid #334155;
            margin-bottom: 1rem;
            font-size: 1.15rem; font-weight: 700;
            color: #f1f5f9;
            display: flex; align-items: center; gap: 0.5rem;
        }
        .sidebar-nav { padding: 0 0.75rem; }
        .sidebar-nav a {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.65rem 0.75rem;
            border-radius: 0.5rem;
            color: #94a3b8;
            font-size: 0.9rem; font-weight: 500;
            transition: all 0.15s;
            margin-bottom: 0.15rem;
        }
        .sidebar-nav a:hover { background: #334155; color: #e2e8f0; }
        .sidebar-nav a.active { background: #f97316; color: #fff; }
        .sidebar-nav .section-label {
            padding: 1rem 0.75rem 0.35rem;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #475569;
            font-weight: 600;
        }

        .main-content { margin-left: 260px; min-height: 100vh; background: #f8fafc; }
        .topbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0 2rem;
            height: 4rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky; top: 0; z-index: 30;
        }
        .topbar h2 { font-size: 1.15rem; font-weight: 600; }
        .topbar-right { display: flex; align-items: center; gap: 1rem; }
        .topbar-right .avatar {
            width: 2.25rem; height: 2.25rem;
            border-radius: 50%;
            background: #fff7ed; color: #f97316;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.85rem;
        }
        .topbar a { color: #64748b; font-size: 0.9rem; }
        .topbar a:hover { color: #f97316; }
        .page-content { padding: 2rem; }

        .btn {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.65rem 1.5rem;
            border-radius: 0.5rem;
            font-size: 0.9rem; font-weight: 600;
            border: none; cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            font-family: inherit;
        }
        .btn-primary { background: #f97316; color: #fff; }
        .btn-primary:hover { background: #ea580c; color: #fff; }
        .btn-success { background: #10b981; color: #fff; }
        .btn-success:hover { background: #059669; color: #fff; }
        .btn-danger { background: #ef4444; color: #fff; }
        .btn-danger:hover { background: #dc2626; color: #fff; }
        .btn-ghost { background: transparent; color: #64748b; border: 1px solid #e2e8f0; }
        .btn-ghost:hover { background: #f1f5f9; border-color: #cbd5e1; }
        .btn-sm { padding: 0.4rem 0.85rem; font-size: 0.8rem; }

        .card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .card-header {
            font-size: 1.05rem; font-weight: 600;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 1rem;
            display: flex; align-items: center; justify-content: space-between;
        }

        .form-group { margin-bottom: 1.25rem; }
        .form-group label { display: block; font-size: 0.85rem; font-weight: 500; color: #475569; margin-bottom: 0.35rem; }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 0.65rem 0.85rem;
            border: 2px solid #e2e8f0;
            border-radius: 0.5rem;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.2s;
            font-family: inherit;
            background: #fff;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: #f97316; }
        .form-row { display: flex; gap: 1rem; }
        .form-row .form-group { flex: 1; }
        .form-actions { display: flex; gap: 0.75rem; justify-content: flex-end; padding-top: 1rem; border-top: 1px solid #f1f5f9; }

        .alert {
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        .alert-success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        .alert-warning { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }

        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
        th { text-align: left; padding: 0.75rem 1rem; color: #64748b; font-weight: 600; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid #e2e8f0; }
        td { padding: 0.75rem 1rem; border-bottom: 1px solid #f1f5f9; color: #475569; }
        .table-actions { display: flex; gap: 0.5rem; }

        .status-badge {
            display: inline-block;
            padding: 0.2rem 0.65rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-pending { background: #fffbeb; color: #92400e; }
        .status-in_transit { background: #eff6ff; color: #1e40af; }
        .status-delivered { background: #ecfdf5; color: #065f46; }
        .status-cancelled { background: #fef2f2; color: #991b1b; }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
        .stat-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 1.25rem;
        }
        .stat-card .num { font-size: 1.75rem; font-weight: 800; color: #f97316; }
        .stat-card .label { font-size: 0.8rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 0.25rem; }

        .empty-state { text-align: center; padding: 3rem 1rem; color: #94a3b8; }
        .empty-state .icon { font-size: 3rem; margin-bottom: 1rem; }
        .empty-state h3 { font-size: 1.1rem; color: #475569; margin-bottom: 0.5rem; }

        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }

        .upload-area {
            border: 2px dashed #e2e8f0;
            border-radius: 0.75rem;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        .upload-area:hover { border-color: #f97316; background: #fff7ed; }

        .toast {
            position: fixed; top: 1rem; right: 1rem;
            padding: 0.75rem 1.25rem;
            border-radius: 0.5rem;
            font-size: 0.9rem;
            font-weight: 500;
            z-index: 100;
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s;
            pointer-events: none;
        }
        .toast.show { opacity: 1; transform: translateY(0); }
        .toast-success { background: #065f46; color: #a7f3d0; }
        .toast-error { background: #991b1b; color: #fecaca; }

        .modal-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.5);
            display: none;
            align-items: center; justify-content: center;
            z-index: 200;
        }
        .modal-overlay.open { display: flex; }
        .modal {
            background: #fff;
            border-radius: 0.75rem;
            padding: 2rem;
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }
        .modal h3 { font-size: 1.1rem; font-weight: 600; margin-bottom: 1rem; }
        .modal-actions { display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #f1f5f9; }

        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            color: #475569;
            padding: 0.25rem;
        }
        .sidebar-toggle svg { width: 1.5rem; height: 1.5rem; }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                display: none;
            }
            .sidebar.open { display: block; }
            .main-content { margin-left: 0; }
            .sidebar-toggle { display: block; }
            .topbar { padding: 0 1rem; }
            .page-content { padding: 1rem; }
            .form-row { flex-direction: column; gap: 0; }
            .grid-2 { grid-template-columns: 1fr; }
            .table-wrap table { font-size: 0.8rem; }
            .table-wrap th, .table-wrap td { padding: 0.5rem; }
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-brand">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#f97316" stroke-width="2"><path d="M3 3h18v18H3z"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
            <?= htmlspecialchars($siteName ?? 'FreightForge') ?>
        </div>
        <nav class="sidebar-nav">
            <div class="section-label">Management</div>
            <a href="/admin" class="<?= ($page ?? '') === 'dashboard' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Dashboard
            </a>
            <a href="/admin/shipments" class="<?= ($page ?? '') === 'shipments' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3h18v18H3z"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                Shipments
            </a>
            <a href="/admin/shipment-settings" class="<?= ($page ?? '') === 'shipment-settings' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
                Shipment Settings
            </a>
            <div class="section-label">Site</div>
            <a href="/admin/settings" class="<?= ($page ?? '') === 'settings' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
                Site Settings
            </a>
            <a href="/admin/sections" class="<?= ($page ?? '') === 'sections' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                Content Sections
            </a>
            <div class="section-label">Account</div>
            <a href="/admin/profile" class="<?= ($page ?? '') === 'profile' ? 'active' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Profile
            </a>
            <a href="/admin/logout" style="margin-top:1rem;color:#475569;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                Sign Out
            </a>
            <a href="/" style="color:#475569;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                Back to Site
            </a>
        </nav>
    </aside>

    <div class="main-content">
        <div class="topbar">
            <div style="display:flex;align-items:center;gap:0.75rem;">
                <button class="sidebar-toggle" onclick="document.querySelector('.sidebar').classList.toggle('open')" aria-label="Toggle sidebar">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12h18"/><path d="M3 6h18"/><path d="M3 18h18"/></svg>
                </button>
                <h2><?= $title ?? 'Dashboard' ?></h2>
            </div>
            <div class="topbar-right">
                <a href="/admin/profile"><?= htmlspecialchars($adminUser['name'] ?? 'Profile') ?></a>
                <div class="avatar"><?= strtoupper(substr($adminUser['name'] ?? 'A', 0, 1)) ?></div>
            </div>
        </div>
        <div class="page-content">
            <?php if (!empty($flash)): ?>
            <div class="alert alert-<?= $flash['type'] ?>"><?= htmlspecialchars($flash['message']) ?></div>
            <?php endif; ?>
            <?= $content ?? '' ?>
        </div>
    </div>

    <div id="toast" class="toast"></div>

    <script>
        function showToast(message, type) {
            var toast = document.getElementById('toast');
            toast.textContent = message;
            toast.className = 'toast toast-' + (type || 'success') + ' show';
            setTimeout(function() { toast.classList.remove('show'); }, 3000);
        }
        function openModal(id) { document.getElementById(id).classList.add('open'); }
        function closeModal(id) { document.getElementById(id).classList.remove('open'); }
        document.querySelectorAll('.modal-overlay').forEach(function(m) {
            m.addEventListener('click', function(e) {
                if (e.target === this) this.classList.remove('open');
            });
        });
    </script>
</body>
</html>
