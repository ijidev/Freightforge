<?php $thisPage = 'about'; ob_start(); ?>
<div class="hero hero-about">
    <div class="hero-overlay"></div>
    <div class="container">
        <h1><?= htmlspecialchars($S['hero']['title'] ?? 'Shipping Made Simple') ?></h1>
        <p><?= htmlspecialchars($S['hero']['subtitle'] ?? 'We connect people with the shipments that matter — across town or across the ocean.') ?></p>
    </div>
</div>

<div class="section">
    <div class="container">
        <div class="about-story">
            <div class="about-text">
                <h2><?= htmlspecialchars($S['story']['title'] ?? 'Our Promise') ?></h2>
                <?= nl2br(htmlspecialchars($S['story']['content'] ?? '')) ?>
            </div>
            <div class="about-image">
                <div class="about-image-frame">
                    <div class="about-img-placeholder about-img-logistics"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="section section-alt">
    <div class="container">
        <h2 class="section-title"><?= htmlspecialchars($S['offerings']['title'] ?? 'What We Offer') ?></h2>
        <p class="section-subtitle"><?= htmlspecialchars($S['offerings']['subtitle'] ?? 'Clear, reliable features that make shipping easier') ?></p>
        <div class="about-grid">
            <?php
            $offerings = [];
            if (!empty($S['offerings']['content'])) {
                $offerings = json_decode($S['offerings']['content'], true) ?: [];
            }
            if (empty($offerings)) {
                $offerings = [
                    ['icon' => '📦', 'title' => 'Package Tracking', 'desc' => 'Real-time tracking with detailed timeline. Know where your shipment is and when it will arrive.'],
                    ['icon' => '🚚', 'title' => 'Multiple Carriers', 'desc' => 'We work with trusted carriers across road, air, and sea to get your cargo where it needs to go.'],
                    ['icon' => '📬', 'title' => 'Email Notifications', 'desc' => 'Automatic updates sent to you and your recipient. No manual check-ins required.'],
                    ['icon' => '🔄', 'title' => 'Easy Returns', 'desc' => 'Simple reverse logistics for when shipments need to come back. Same tracking, same peace of mind.'],
                ];
            }
            foreach ($offerings as $offering):
            ?>
            <div class="about-card">
                <div class="about-card-icon"><?= htmlspecialchars($offering['icon'] ?? '') ?></div>
                <h3><?= htmlspecialchars($offering['title'] ?? '') ?></h3>
                <p><?= htmlspecialchars($offering['desc'] ?? '') ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="section">
    <div class="container" style="text-align:center;">
        <h2 class="section-title"><?= htmlspecialchars($S['trust']['title'] ?? 'Trusted by Businesses Big and Small') ?></h2>
        <p class="section-subtitle" style="max-width:700px;margin-left:auto;margin-right:auto;"><?= htmlspecialchars($S['trust']['subtitle'] ?? 'From local shops to global enterprises, companies rely on us to keep their shipments moving and their customers informed.') ?></p>
        <div class="stats-row" style="justify-content:center;margin-top:2rem;">
            <?php
            $trustContent = $S['trust']['content'] ?? '';
            $trustItems = $trustContent ? explode(',', $trustContent) : ['50K+','Shipments Delivered','500+','Cities Covered','98%','Satisfaction Rate'];
            ?>
            <?php if (count($trustItems) >= 2): ?>
                <?php for ($i = 0; $i < count($trustItems); $i += 2): ?>
                <div class="stat-item">
                    <div class="stat-num"><?= htmlspecialchars(trim($trustItems[$i])) ?></div>
                    <div class="stat-label"><?= htmlspecialchars(trim($trustItems[$i+1] ?? '')) ?></div>
                </div>
                <?php endfor; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="section section-cta" style="background:linear-gradient(135deg, #1e293b, #0f172a);">
    <div class="container" style="text-align:center;">
        <h2 class="section-title" style="color:#fff;"><?= htmlspecialchars($S['cta']['title'] ?? 'Have a Question?') ?></h2>
        <p class="section-subtitle" style="color:#94a3b8;max-width:500px;margin:0 auto 1.5rem;"><?= htmlspecialchars($S['cta']['subtitle'] ?? 'We\'re here to help with your shipments, tracking, or anything you need.') ?></p>
        <a href="/" class="btn btn-primary btn-lg">Get In Touch</a>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/layouts/main.php'; ?>
