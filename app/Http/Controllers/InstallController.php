<?php

namespace App\Http\Controllers;

use Helix\Http\JsonResponse;
use Helix\Http\Request;
use Helix\Http\Response;
use Helix\Installer\WebInstaller;
use Helix\Routing\Attributes\Route;

class InstallController
{
    private WebInstaller $installer;

    public function __construct()
    {
        $this->installer = new WebInstaller(realpath(__DIR__ . '/../../..'));
    }

    #[Route('/install', method: 'GET')]
    public function index(): Response
    {
        if ($this->installer->isInstalled()) {
            return Response::redirect('/');
        }

        $envExists = file_exists(realpath(__DIR__ . '/../../..') . '/.env');
        $envExample = file_exists(realpath(__DIR__ . '/../../..') . '/.env.example');

        $defaults = [];
        if ($envExample) {
            $defaults = parse_ini_file(realpath(__DIR__ . '/../../..') . '/.env.example') ?: [];
        }

        $html = $this->renderPage($envExists, $defaults);
        return Response::html($html);
    }

    #[Route('/install/check', method: 'POST')]
    public function runChecks(): JsonResponse
    {
        $results = $this->installer->runChecks();
        $failed = count(array_filter($results, fn($r) => $r['status'] === 'fail'));
        $passed = count(array_filter($results, fn($r) => $r['status'] === 'pass'));

        return new JsonResponse([
            'success' => $failed === 0,
            'results' => $results,
            'passed' => $passed,
            'failed' => $failed,
        ]);
    }

    #[Route('/install/setup', method: 'POST')]
    public function setup(Request $request): JsonResponse
    {
        $config = $request->all();
        $result = $this->installer->runSetup($config);

        return new JsonResponse($result);
    }

    private function renderPage(bool $envExists, array $defaults): string
    {
        $appUrl = $defaults['APP_URL'] ?? 'http://localhost:8080';
        $dbDriver = $defaults['DB_DRIVER'] ?? 'mysql';
        $dbPath = $defaults['DB_PATH'] ?? 'storage/database.sqlite';

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FreightForge — Installer</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container { max-width: 680px; width: 100%; padding: 2rem; }
        .card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 1.5rem;
        }
        h1 {
            font-size: 1.75rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 0.25rem;
            color: #f1f5f9;
        }
        .subtitle {
            text-align: center;
            color: #64748b;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }
        .step { display: none; }
        .step.active { display: block; }
        .step-header {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #cbd5e1;
        }
        .badge {
            display: inline-block;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.15rem 0.5rem;
            border-radius: 9999px;
            margin-left: 0.5rem;
        }
        .badge.pass { background: #065f46; color: #6ee7b7; }
        .badge.fail { background: #7f1d1d; color: #fca5a5; }
        .badge.warn { background: #78350f; color: #fcd34d; }

        .check-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.6rem 0;
            border-bottom: 1px solid #334155;
        }
        .check-item:last-child { border-bottom: none; }
        .check-label { font-size: 0.9rem; color: #94a3b8; }
        .check-status { font-size: 0.85rem; }
        .check-status.pass { color: #6ee7b7; }
        .check-status.fail { color: #fca5a5; }
        .check-status.warn { color: #fcd34d; }

        .summary-row {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin: 1rem 0;
        }
        .summary-stat {
            text-align: center;
            padding: 0.75rem 1.5rem;
            background: #0f172a;
            border-radius: 0.5rem;
        }
        .summary-stat .num { font-size: 1.5rem; font-weight: 700; }
        .summary-stat .label { font-size: 0.75rem; color: #64748b; text-transform: uppercase; }
        .summary-stat.pass .num { color: #6ee7b7; }
        .summary-stat.fail .num { color: #fca5a5; }

        .form-group { margin-bottom: 1rem; }
        .form-group label {
            display: block;
            font-size: 0.85rem;
            color: #94a3b8;
            margin-bottom: 0.3rem;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 0.6rem 0.75rem;
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 0.5rem;
            color: #e2e8f0;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.15s;
        }
        .form-group input:focus, .form-group select:focus {
            border-color: #818cf8;
        }
        .form-row { display: flex; gap: 1rem; }
        .form-row .form-group { flex: 1; }

        .btn {
            display: inline-block;
            padding: 0.65rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s, opacity 0.15s;
        }
        .btn:disabled { opacity: 0.5; cursor: not-allowed; }
        .btn-primary { background: #818cf8; color: #fff; }
        .btn-primary:hover:not(:disabled) { background: #6366f1; }
        .btn-success { background: #059669; color: #fff; }
        .btn-success:hover:not(:disabled) { background: #047857; }
        .btn-center { display: flex; justify-content: center; margin-top: 1rem; }

        .spinner {
            display: inline-block;
            width: 1rem; height: 1rem;
            border: 2px solid #334155;
            border-top-color: #818cf8;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
            margin-right: 0.5rem;
            vertical-align: middle;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        .success-icon {
            text-align: center;
            font-size: 3rem;
            margin-bottom: 0.5rem;
        }
        .success-title {
            text-align: center;
            font-size: 1.25rem;
            font-weight: 700;
            color: #6ee7b7;
            margin-bottom: 0.5rem;
        }
        .success-desc {
            text-align: center;
            color: #64748b;
            margin-bottom: 1.5rem;
        }
        .success-links {
            text-align: center;
        }
        .success-links a {
            color: #818cf8;
            text-decoration: none;
            margin: 0 0.75rem;
        }
        .success-links a:hover { text-decoration: underline; }

        .error-box {
            background: #7f1d1d;
            border: 1px solid #991b1b;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            margin: 1rem 0;
            color: #fca5a5;
            font-size: 0.85rem;
        }

        .hidden { display: none; }

        .db-config { display: none; }
        .db-config.visible { display: block; }
    </style>
</head>
<body>
    <div class="container">
        <h1>⚙ FreightForge</h1>
        <p class="subtitle">First-Time Setup</p>

        <!-- Step 1: Environment Check -->
        <div class="card step active" id="step-1">
            <div class="step-header">Step 1 of 3 — System Requirements</div>
            <p style="color:#64748b;font-size:0.85rem;margin-bottom:1rem;">
                Checking PHP version, required extensions, and directory permissions.
            </p>
            <div id="check-results"></div>
            <div id="check-summary" class="hidden">
                <div class="summary-row" id="summary-stats"></div>
                <div id="check-error" class="error-box hidden"></div>
                <div class="btn-center">
                    <button class="btn btn-primary" id="btn-step1-next" disabled onclick="goToStep(2)">Continue →</button>
                </div>
            </div>
        </div>

        <!-- Step 2: Configuration -->
        <div class="card step" id="step-2">
            <div class="step-header">Step 2 of 3 — Application Configuration</div>
            <div id="config-error" class="error-box hidden"></div>
            <form id="config-form">
                <div class="form-group">
                    <label>Application URL</label>
                    <input type="text" name="APP_URL" value="{$appUrl}" placeholder="http://localhost:8080">
                </div>
                <div class="form-group">
                    <label>Database Driver</label>
                    <select name="DB_DRIVER" id="db-driver" onchange="toggleDbConfig()">
                        <option value="sqlite"<?= $dbDriver === 'sqlite' ? ' selected' : '' ?>>SQLite</option>
                        <option value="mysql"<?= $dbDriver === 'mysql' ? ' selected' : '' ?>>MySQL</option>
                        <option value="pgsql"<?= $dbDriver === 'pgsql' ? ' selected' : '' ?>>PostgreSQL</option>
                    </select>
                </div>
                <div id="sqlite-config">
                    <div class="form-group">
                        <label>Database Path</label>
                        <input type="text" name="DB_PATH" value="{$dbPath}" placeholder="storage/database.sqlite">
                    </div>
                </div>
                <div id="server-db-config" class="db-config">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Host</label>
                            <input type="text" name="DB_HOST" value="127.0.0.1">
                        </div>
                        <div class="form-group">
                            <label>Port</label>
                            <input type="text" name="DB_PORT" value="3306">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Database Name</label>
                            <input type="text" name="DB_NAME" value="freightforge">
                        </div>
                        <div class="form-group">
                            <label>User</label>
                            <input type="text" name="DB_USER" value="root">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="DB_PASS" value="">
                    </div>
                </div>
                <div style="margin-top:1.5rem;padding-top:1.5rem;border-top:1px solid #334155;">
                    <div class="step-header" style="margin-bottom:1rem;">Admin Account</div>
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="ADMIN_NAME" value="Admin" placeholder="Admin" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="ADMIN_EMAIL" value="admin@freightforge.test" placeholder="admin@example.com" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="ADMIN_PASSWORD" id="admin-password" placeholder="min 6 characters" required>
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" name="ADMIN_PASSWORD_CONFIRM" id="admin-password-confirm" placeholder="repeat password" required>
                        </div>
                    </div>
                    <div id="password-error" class="error-box hidden" style="margin-top:0.5rem;">Passwords do not match.</div>
                </div>
                <div class="btn-center" style="display:flex;gap:0.75rem;">
                    <button type="button" class="btn" onclick="goToStep(1)" style="background:#334155;color:#e2e8f0;">← Back</button>
                    <button type="button" class="btn btn-primary" id="btn-install" onclick="runInstall()">Install →</button>
                </div>
            </form>
        </div>

        <!-- Step 3: Complete -->
        <div class="card step" id="step-3">
            <div id="install-progress"></div>
        </div>
    </div>

    <script>
        let checksPassed = false;

        // Run checks on page load
        document.addEventListener('DOMContentLoaded', async () => {
            toggleDbConfig();

            const resultsDiv = document.getElementById('check-results');
            resultsDiv.innerHTML = '<div style="text-align:center;padding:1rem;"><div class="spinner"></div> Running checks...</div>';

            try {
                const res = await fetch('/install/check', { method: 'POST' });
                const data = await res.json();
                renderChecks(data);
            } catch (e) {
                resultsDiv.innerHTML = '<div class="error-box">Failed to run checks: ' + e.message + '</div>';
            }
        });

        function renderChecks(data) {
            const resultsDiv = document.getElementById('check-results');
            const summaryDiv = document.getElementById('check-summary');
            const statsDiv = document.getElementById('summary-stats');
            const errorDiv = document.getElementById('check-error');
            const nextBtn = document.getElementById('btn-step1-next');

            let html = '';
            data.results.forEach(r => {
                const icon = r.status === 'pass' ? '✓' : r.status === 'fail' ? '✗' : '⚠';
                html += '<div class="check-item">' +
                    '<span class="check-label">' + escapeHtml(r.check) + '</span>' +
                    '<span class="check-status ' + r.status + '">' + icon + ' ' + escapeHtml(r.message) + '</span>' +
                    '</div>';
            });
            resultsDiv.innerHTML = html;

            statsDiv.innerHTML =
                '<div class="summary-stat pass"><div class="num">' + data.passed + '</div><div class="label">Passed</div></div>' +
                '<div class="summary-stat fail"><div class="num">' + data.failed + '</div><div class="label">Failed</div></div>';

            checksPassed = data.success;
            summaryDiv.classList.remove('hidden');

            if (data.success) {
                nextBtn.disabled = false;
                nextBtn.textContent = 'Continue →';
            } else {
                nextBtn.disabled = true;
                errorDiv.classList.remove('hidden');
                errorDiv.textContent = 'Please fix the failed checks before continuing.';
            }
        }

        function toggleDbConfig() {
            const driver = document.getElementById('db-driver').value;
            document.getElementById('sqlite-config').style.display = driver === 'sqlite' ? 'block' : 'none';
            const serverConfig = document.getElementById('server-db-config');
            serverConfig.classList.toggle('visible', driver !== 'sqlite');
            if (driver === 'mysql') {
                document.querySelector('#server-db-config input[name="DB_PORT"]').value = '3306';
            } else if (driver === 'pgsql') {
                document.querySelector('#server-db-config input[name="DB_PORT"]').value = '5432';
            }
        }

        async function runInstall() {
            var pw = document.getElementById('admin-password').value;
            var confirm = document.getElementById('admin-password-confirm').value;
            var pwError = document.getElementById('password-error');

            if (pw !== confirm) {
                pwError.classList.remove('hidden');
                pwError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }
            pwError.classList.add('hidden');

            if (pw.length < 6) {
                pwError.textContent = 'Password must be at least 6 characters.';
                pwError.classList.remove('hidden');
                return;
            }
            pwError.classList.add('hidden');

            const form = document.getElementById('config-form');
            const formData = new FormData(form);

            document.getElementById('step-2').classList.remove('active');
            document.getElementById('step-3').classList.add('active');

            showInstallProgress('spinner');

            try {
                const res = await fetch('/install/setup', { method: 'POST', body: formData });
                const data = await res.json();
                const container = document.getElementById('install-progress');

                if (data.success) {
                    var hasWarnings = data.results && data.results.some(function(r) { return r.status === 'warn'; });
                    var warnNote = hasWarnings
                        ? '<div class="error-box" style="background:#78350f;border-color:#92400e;color:#fcd34d;margin-bottom:1rem;">⚠ Some items need attention. You can fix them later from settings.</div>'
                        : '';

                    var resultHtml = '';
                    if (data.results) {
                        data.results.forEach(function(r) {
                            var icon = r.status === 'pass' ? '✓' : r.status === 'warn' ? '⚠' : '✗';
                            resultHtml += '<div class="check-item">' +
                                '<span class="check-label">' + escapeHtml(r.check) + '</span>' +
                                '<span class="check-status ' + r.status + '">' + icon + ' ' + escapeHtml(r.message) + '</span>' +
                                '</div>';
                        });
                    }

                    container.innerHTML =
                        '<div class="success-icon">🎉</div>' +
                        '<div class="success-title">Installation Complete!</div>' +
                        '<div class="success-desc">FreightForge is ready to use.</div>' +
                        warnNote +
                        (resultHtml ? '<div style="margin:1rem 0;">' + resultHtml + '</div>' : '') +
                        '<div class="btn-center">' +
                        '<button class="btn btn-success" onclick="window.location.href=\'/\'" style="font-size:1rem;padding:0.75rem 2rem;">Load Application</button>' +
                        '</div>';
                } else {
                    goToStep(2);
                    showConfigError(data.error || 'Installation failed', data.results);
                }
            } catch (e) {
                var container = document.getElementById('install-progress');
                container.innerHTML =
                    '<div class="error-box">Connection error: ' + escapeHtml(e.message) + '</div>' +
                    '<div class="btn-center">' +
                    '<button class="btn" onclick="goToStep(2)" style="background:#334155;color:#e2e8f0;padding:0.65rem 2rem;">← Try Again</button>' +
                    '</div>';
            }
        }

        function showConfigError(errorMsg, results) {
            var errorDiv = document.getElementById('config-error');
            var html = '<strong>' + escapeHtml(errorMsg) + '</strong>';

            if (results) {
                var failedItems = results.filter(function(r) { return r.status === 'fail' || r.status === 'warn'; });
                if (failedItems.length) {
                    html += '<ul style="margin:0.5rem 0 0 1rem;font-size:0.85rem;">';
                    failedItems.forEach(function(r) {
                        var icon = r.status === 'fail' ? '✗' : '⚠';
                        html += '<li style="margin:0.25rem 0;">' + icon + ' ' + escapeHtml(r.check) + ': ' + escapeHtml(r.message) + '</li>';
                    });
                    html += '</ul>';
                }
            }

            errorDiv.innerHTML = html;
            errorDiv.classList.remove('hidden');
            errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });

            document.getElementById('btn-install').disabled = false;
            document.getElementById('btn-install').textContent = 'Retry Install';
        }

        function showInstallProgress(state, results, errorMsg) {
            var container = document.getElementById('install-progress');

            if (state === 'spinner') {
                container.innerHTML =
                    '<div style="text-align:center;padding:2rem 0;">' +
                    '<div class="spinner" style="width:2rem;height:2rem;border-width:3px;"></div>' +
                    '<p style="margin-top:1rem;color:#64748b;">Running installation...</p></div>';
            }
        }

        function goToStep(n) {
            document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));
            document.getElementById('step-' + n).classList.add('active');
            document.getElementById('config-error').classList.add('hidden');
            var btn = document.getElementById('btn-install');
            btn.disabled = false;
            btn.textContent = 'Install →';
        }

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }
    </script>
</body>
</html>
HTML;
    }
}
