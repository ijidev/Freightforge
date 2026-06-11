<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Home') ?> — <?= htmlspecialchars($siteName ?? 'FreightForge') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f8fafc;
            color: #1e293b;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        a { color: #f97316; text-decoration: none; transition: color 0.2s; }
        a:hover { color: #ea580c; }
        img { max-width: 100%; height: auto; }

        .container { max-width: 1200px; margin: 0 auto; padding: 0 1.5rem; }

        .navbar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 50;
            padding: 0 1.5rem;
        }
        .navbar .container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 4rem;
        }
        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.35rem;
            font-weight: 800;
            color: #1e293b;
            text-decoration: none;
        }
        .navbar-brand:hover { color: #f97316; }
        .navbar-brand svg { width: 2rem; height: 2rem; }
        .nav-toggle {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.5rem;
            color: #1e293b;
        }
        .nav-toggle svg { width: 1.5rem; height: 1.5rem; }
        .nav-links { display: flex; align-items: center; gap: 0.25rem; }
        .nav-links a {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            color: #475569;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        .nav-links a:hover {
            background: #fff7ed;
            color: #f97316;
        }
        .nav-links a.active {
            background: #f97316;
            color: #ffffff;
        }
        .nav-links a.active:hover { background: #ea580c; color: #fff; }

        .hero {
            padding: 6rem 0 5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .hero-home { background: linear-gradient(135deg, rgba(15,23,42,0.85), rgba(15,23,42,0.85)), url('https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=1400&q=80') center/cover no-repeat; }
        .hero-about { background: linear-gradient(135deg, rgba(15,23,42,0.85), rgba(15,23,42,0.85)), url('https://images.unsplash.com/photo-1494412574643-ff11b0a5c1c3?w=1400&q=80') center/cover no-repeat; }
        .hero-track { background: linear-gradient(135deg, rgba(15,23,42,0.85), rgba(15,23,42,0.85)), url('https://images.unsplash.com/photo-1578575437130-527eed3abbec?w=1400&q=80') center/cover no-repeat; }
        .hero-overlay {
            position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, rgba(15,23,42,0.88) 0%, rgba(15,23,42,0.75) 100%);
            z-index: 1;
        }
        .hero .container { position: relative; z-index: 2; }
        .hero h1 {
            font-size: 3rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 1rem;
            line-height: 1.15;
        }
        .hero h1 span { color: #f97316; }
        .hero p {
            font-size: 1.15rem;
            color: #cbd5e1;
            max-width: 600px;
            margin: 0 auto 2rem;
        }
        .hero-actions { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
        .hero-actions .btn-outline { border-color: #475569; color: #e2e8f0; }
        .hero-actions .btn-outline:hover { border-color: #f97316; color: #fff; }
        .btn {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.75rem 1.75rem;
            border-radius: 0.5rem;
            font-size: 0.95rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }
        .btn-primary { background: #f97316; color: #fff; }
        .btn-primary:hover { background: #ea580c; color: #fff; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(249,115,22,0.3); }
        .btn-outline { background: transparent; color: #475569; border: 2px solid #e2e8f0; }
        .btn-outline:hover { border-color: #f97316; color: #f97316; transform: translateY(-1px); }
        .btn-sm { padding: 0.5rem 1rem; font-size: 0.85rem; }
        .btn-lg { padding: 0.9rem 2.25rem; font-size: 1.05rem; }
        .btn-success { background: #10b981; color: #fff; }
        .btn-success:hover { background: #059669; color: #fff; }
        .btn-danger { background: #ef4444; color: #fff; }
        .btn-danger:hover { background: #dc2626; color: #fff; }
        .btn-ghost { background: transparent; color: #64748b; padding: 0.5rem 1rem; }
        .btn-ghost:hover { background: #f1f5f9; color: #1e293b; }
        .btn-white { background: #fff; color: #1e293b; }
        .btn-white:hover { background: #f1f5f9; color: #1e293b; }

        .section { padding: 4rem 0; }
        .section-alt { background: #ffffff; border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; }
        .section-title {
            font-size: 1.75rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 0.75rem;
            color: #1e293b;
        }
        .section-subtitle {
            text-align: center;
            color: #64748b;
            margin-bottom: 3rem;
            font-size: 1.05rem;
        }

        .features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; }
        .feature-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            padding: 2rem;
            transition: all 0.3s;
        }
        .feature-card:hover { border-color: #f97316; box-shadow: 0 8px 24px rgba(249,115,22,0.08); transform: translateY(-2px); }
        .feature-icon {
            width: 3rem; height: 3rem;
            background: #fff7ed;
            border-radius: 0.75rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        .feature-card h3 { font-size: 1.1rem; font-weight: 600; margin-bottom: 0.5rem; }
        .feature-card p { color: #64748b; font-size: 0.9rem; line-height: 1.6; }

        .steps-row { display: flex; align-items: flex-start; justify-content: center; gap: 0; flex-wrap: wrap; }
        .step-card {
            flex: 1; min-width: 220px; max-width: 300px;
            text-align: center;
            padding: 2rem 1.5rem;
            position: relative;
        }
        .step-number {
            width: 2rem; height: 2rem;
            background: #fff7ed; color: #f97316;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.8rem;
            margin: 0 auto 1rem;
        }
        .step-icon { font-size: 2.5rem; margin-bottom: 1rem; }
        .step-card h3 { font-size: 1.1rem; font-weight: 600; margin-bottom: 0.5rem; }
        .step-card p { color: #64748b; font-size: 0.9rem; line-height: 1.6; }
        .step-connector { display: flex; align-items: center; padding-top: 3rem; flex-shrink: 0; }
        @media (max-width: 768px) { .step-connector { display: none; } }

        .section-image { position: relative; overflow: hidden; padding: 5rem 0; background-size: cover; background-position: center; }
        .section-image-truck { background-image: url('https://images.unsplash.com/photo-1578575437130-527eed3abbec?w=1400&q=80'); background-attachment: fixed; }
        .section-image-overlay {
            position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, rgba(15,23,42,0.85) 0%, rgba(15,23,42,0.7) 100%);
            z-index: 1;
        }
        .section-image .container { position: relative; z-index: 2; }

        .testimonial-strip { max-width: 700px; margin: 0 auto; }
        .testimonial-text h2 { font-size: 2rem; font-weight: 700; color: #fff; margin-bottom: 1rem; }
        .testimonial-text p { color: #cbd5e1; font-size: 1.05rem; line-height: 1.7; margin-bottom: 2rem; }

        .stats-row { display: flex; gap: 2.5rem; flex-wrap: wrap; }
        .stat-item { text-align: center; }
        .stat-num { font-size: 2.25rem; font-weight: 800; color: #f97316; line-height: 1; }
        .stat-label { font-size: 0.8rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 0.25rem; }
        .section-image .stat-label { color: #94a3b8; }

        .section-cta { background: linear-gradient(135deg, #1e293b, #0f172a); padding: 5rem 0; }

        .about-story { display: flex; align-items: center; gap: 3rem; max-width: 1000px; margin: 0 auto; }
        .about-text { flex: 1; }
        .about-text h2 { font-size: 1.75rem; font-weight: 700; margin-bottom: 1rem; color: #1e293b; }
        .about-text p { color: #475569; line-height: 1.7; margin-bottom: 1rem; }
        .about-image { flex: 1; }
        .about-image-frame {
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        }
        .about-img-placeholder {
            width: 100%; height: 300px;
            background-size: cover; background-position: center;
        }
        .about-img-logistics { background-image: url('https://images.unsplash.com/photo-1494412574643-ff11b0a5c1c3?w=800&q=80'); }
        .about-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; max-width: 900px; margin: 0 auto; }
        .about-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s;
        }
        .about-card:hover { border-color: #f97316; box-shadow: 0 8px 24px rgba(249,115,22,0.08); transform: translateY(-2px); }
        .about-card-icon { font-size: 2rem; margin-bottom: 0.75rem; }
        .about-card h3 { font-size: 1rem; font-weight: 600; margin-bottom: 0.5rem; }
        .about-card p { color: #64748b; font-size: 0.85rem; line-height: 1.6; }

        .track-card-icon { font-size: 2.5rem; margin-bottom: 0.75rem; }
        .result-card { margin-top: 2rem; }
        .tracking-number-display {
            text-align: center;
            padding: 1rem;
            background: #fff7ed;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
        }
        .tracking-label { display: block; font-size: 0.75rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem; }
        .tracking-value { font-size: 1.25rem; font-weight: 700; color: #f97316; letter-spacing: 0.05em; }
        .route-display { display: flex; align-items: center; justify-content: center; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
        .route-endpoint { text-align: center; }
        .route-city { font-size: 1.1rem; font-weight: 600; color: #1e293b; }
        .route-name { font-size: 0.85rem; color: #64748b; }
        .route-line { color: #f97316; display: flex; align-items: center; }
        .shipment-note {
            font-size: 0.85rem; color: #64748b;
            padding: 0.75rem 1rem;
            background: #f8fafc;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }
        .timeline-header { font-size: 0.9rem; font-weight: 600; margin-bottom: 0.5rem; color: #1e293b; }

        @media (max-width: 768px) {
            .about-story { flex-direction: column; }
            .about-text { order: 1; }
            .about-image { order: 0; }
            .stats-row { justify-content: center; gap: 1.5rem; }
            .step-card { max-width: 100%; }
        }

        .nav-links.open { display: flex; }

        .track-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            padding: 2.5rem;
            max-width: 600px;
            margin: 0 auto;
            text-align: center;
        }
        .track-card h2 { font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem; }
        .track-card p { color: #64748b; font-size: 0.9rem; margin-bottom: 1.5rem; }
        .track-form { display: flex; gap: 0.75rem; }
        .track-form input {
            flex: 1;
            padding: 0.75rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 0.5rem;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s;
            font-family: inherit;
        }
        .track-form input:focus { border-color: #f97316; }
        .track-form button { padding: 0.75rem 1.5rem; }

        .footer {
            background: #1e293b;
            color: #94a3b8;
            padding: 3rem 0;
            margin-top: auto;
        }
        .footer .container { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
        .footer-brand { font-size: 1.1rem; font-weight: 700; color: #f1f5f9; }
        .footer-links { display: flex; gap: 1.5rem; }
        .footer-links a { color: #94a3b8; font-size: 0.85rem; }
        .footer-links a:hover { color: #f97316; }

        .page-header {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 2.5rem 0;
        }
        .page-header h1 { font-size: 1.75rem; font-weight: 700; margin-bottom: 0.5rem; }
        .page-header p { color: #64748b; }

        .card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 1.5rem;
        }
        .card-header { font-size: 1.1rem; font-weight: 600; margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid #e2e8f0; }

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

        .alert {
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        .alert-success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        .alert-warning { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
        .alert-info { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }

        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
        th { text-align: left; padding: 0.75rem 1rem; color: #64748b; font-weight: 600; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid #e2e8f0; }
        td { padding: 0.75rem 1rem; border-bottom: 1px solid #f1f5f9; color: #475569; }

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

        .tracking-timeline { position: relative; padding: 1rem 0; }
        .tracking-step { display: flex; gap: 1rem; padding: 0.75rem 0; position: relative; }
        .tracking-step:not(:last-child)::before {
            content: '';
            position: absolute;
            left: 0.95rem;
            top: 2.5rem;
            bottom: -0.25rem;
            width: 2px;
            background: #e2e8f0;
        }
        .tracking-step.active:not(:last-child)::before { background: #f97316; }
        .tracking-dot {
            width: 2rem; height: 2rem;
            border-radius: 50%;
            background: #e2e8f0;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.75rem;
            flex-shrink: 0;
            color: #94a3b8;
        }
        .tracking-step.active .tracking-dot { background: #f97316; color: #fff; }
        .tracking-step.completed .tracking-dot { background: #10b981; color: #fff; }
        .tracking-content h4 { font-size: 0.95rem; font-weight: 600; margin-bottom: 0.15rem; }
        .tracking-content p { font-size: 0.85rem; color: #64748b; }
        .tracking-step.active .tracking-content h4 { color: #f97316; }
        .tracking-step.completed .tracking-content h4 { color: #10b981; }

        .empty-state { text-align: center; padding: 3rem 1rem; color: #94a3b8; }
        .empty-state .icon { font-size: 3rem; margin-bottom: 1rem; }
        .empty-state h3 { font-size: 1.1rem; color: #475569; margin-bottom: 0.5rem; }
        .empty-state p { font-size: 0.9rem; }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .stat-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 1.25rem;
            text-align: center;
        }
        .stat-card .num { font-size: 2rem; font-weight: 800; color: #f97316; }
        .stat-card .label { font-size: 0.8rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 0.25rem; }

        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: 260px;
            height: 100vh;
            background: #1e293b;
            color: #e2e8f0;
            padding: 1.5rem 0;
            overflow-y: auto;
            z-index: 40;
        }
        .sidebar-brand {
            padding: 0 1.5rem 1.5rem;
            border-bottom: 1px solid #334155;
            margin-bottom: 1rem;
            font-size: 1.15rem;
            font-weight: 700;
            color: #f1f5f9;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .sidebar-nav { padding: 0 0.75rem; }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem 0.75rem;
            border-radius: 0.5rem;
            color: #94a3b8;
            font-size: 0.9rem;
            font-weight: 500;
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
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0 2rem;
            height: 4rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 30;
        }
        .topbar h2 { font-size: 1.15rem; font-weight: 600; }
        .topbar-right { display: flex; align-items: center; gap: 1rem; }
        .topbar-right .avatar {
            width: 2.25rem; height: 2.25rem;
            border-radius: 50%;
            background: #fff7ed;
            color: #f97316;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
        }
        .page-content { padding: 2rem; }

        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; }

        .upload-area {
            border: 2px dashed #e2e8f0;
            border-radius: 0.75rem;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        .upload-area:hover { border-color: #f97316; background: #fff7ed; }
        .upload-area .icon { font-size: 2rem; margin-bottom: 0.5rem; }
        .upload-area p { font-size: 0.85rem; color: #64748b; }
        .upload-area input { display: none; }

        @media (max-width: 768px) {
            .hero h1 { font-size: 2rem; }
            .hero { padding: 4rem 0 3rem; }
            .track-form { flex-direction: column; }
            .form-row { flex-direction: column; gap: 0; }
            .grid-2, .grid-3 { grid-template-columns: 1fr; }
            .sidebar { width: 100%; height: auto; position: relative; }
            .main-content { margin-left: 0; }
            .nav-toggle { display: block; }
            .nav-links {
                display: none;
                position: absolute;
                top: 4rem;
                left: 0;
                right: 0;
                background: #fff;
                border-bottom: 1px solid #e2e8f0;
                flex-direction: column;
                padding: 0.5rem 1.5rem 1rem;
                box-shadow: 0 4px 12px rgba(0,0,0,0.06);
                z-index: 40;
            }
            .nav-links a { width: 100%; }
            .steps-row { flex-direction: column; align-items: center; }
            .step-card { max-width: 100%; }
            .stats-row { justify-content: center; gap: 1.5rem; }
            .about-story { flex-direction: column; }
            .about-text { order: 1; }
            .about-image { order: 0; }
            .route-display { flex-direction: column; gap: 0.5rem; }
            .route-line { transform: rotate(90deg); }
            .tracking-number-display { padding: 0.75rem; }
            .tracking-value { font-size: 1rem; word-break: break-all; }
            .page-content { padding: 1rem; }
            .table-wrap table { font-size: 0.8rem; }
            .table-wrap th, .table-wrap td { padding: 0.5rem; }
            .footer .container { flex-direction: column; text-align: center; }
            .footer-links { flex-wrap: wrap; justify-content: center; }
            .container { padding: 0 1rem; }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="/" class="navbar-brand">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7h13a2 2 0 012 2v8a2 2 0 01-2 2H3"/><path d="M7 19a2 2 0 100-4 2 2 0 000 4z"/><path d="M14 19a2 2 0 100-4 2 2 0 000 4z"/><path d="M16 9h3l3 3v3h-2"/><path d="M5 17v-2a2 2 0 012-2h4"/></svg>
                <?= htmlspecialchars($siteName ?? 'FreightForge') ?>
            </a>
            <button class="nav-toggle" onclick="document.querySelector('.nav-links').classList.toggle('open')" aria-label="Toggle navigation">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12h18"/><path d="M3 6h18"/><path d="M3 18h18"/></svg>
            </button>
            <div class="nav-links">
                <a href="/" class="<?= ($page ?? '') === 'home' ? 'active' : '' ?>">Home</a>
                <a href="/track" class="<?= ($page ?? '') === 'track' ? 'active' : '' ?>">Track</a>
                <a href="/about" class="<?= ($page ?? '') === 'about' ? 'active' : '' ?>">About</a>
                <a href="/contact" class="<?= ($page ?? '') === 'contact' ? 'active' : '' ?>">Contact</a>
            </div>
        </div>
    </nav>

    <?= $content ?? '' ?>

    <footer class="footer">
        <div class="container">
            <div>
                <div class="footer-brand"><?= htmlspecialchars($siteName ?? 'FreightForge') ?></div>
                <div style="font-size:0.8rem;margin-top:0.25rem;">Shipment Tracking Platform</div>
            </div>
            <div class="footer-links">
                <a href="/">Home</a>
                <a href="/track">Track Shipment</a>
                <a href="/about">About</a>
                <a href="/contact">Contact</a>
            </div>
        </div>
    </footer>
</body>
</html>
