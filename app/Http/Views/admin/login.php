<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 50%, #fef3c7 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1e293b;
        }
        .login-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            padding: 2.5rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        }
        .login-card h1 {
            font-size: 1.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 0.25rem;
        }
        .login-card .subtitle {
            text-align: center;
            color: #64748b;
            font-size: 0.9rem;
            margin-bottom: 2rem;
        }
        .form-group { margin-bottom: 1.25rem; }
        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 500;
            color: #475569;
            margin-bottom: 0.35rem;
        }
        .form-group input {
            width: 100%;
            padding: 0.65rem 0.85rem;
            border: 2px solid #e2e8f0;
            border-radius: 0.5rem;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.2s;
            font-family: inherit;
        }
        .form-group input:focus { border-color: #f97316; }
        .btn {
            display: block;
            width: 100%;
            padding: 0.75rem;
            border: none;
            border-radius: 0.5rem;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            font-family: inherit;
            background: #f97316;
            color: #fff;
        }
        .btn:hover { background: #ea580c; }
        .error-box {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.85rem;
            margin-bottom: 1rem;
            text-align: center;
        }
        .brand {
            text-align: center;
            font-size: 1.15rem;
            font-weight: 700;
            color: #f97316;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand">FreightForge</div>
        <h1>Admin Login</h1>
        <p class="subtitle">Sign in to manage your logistics</p>

        <?php if (!empty($error)): ?>
        <div class="error-box"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="/admin/login">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="admin@example.com" required autofocus>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="btn">Sign In</button>
        </form>
    </div>
</body>
</html>
