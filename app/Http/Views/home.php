<?php $thisPage = 'home'; $S = $sectionMap ?? []; ob_start(); ?>
<div class="hero hero-home">
    <div class="hero-overlay"></div>
    <div class="container">
        <h1><?= nl2br(htmlspecialchars($S['hero']['title'] ?? "Your Shipments,\nAlways in Sight")) ?></h1>
        <p><?= htmlspecialchars($S['hero']['subtitle'] ?? 'Send packages anywhere with confidence. Real-time tracking, instant updates, and delivery notifications — so you never have to wonder where your cargo is.') ?></p>
        <div class="hero-actions">
            <a href="/track" class="btn btn-primary btn-lg">Track a Shipment</a>
            <a href="#how-it-works" class="btn btn-outline btn-lg">How It Works</a>
        </div>
    </div>
</div>

<div class="section" id="how-it-works">
    <div class="container">
        <h2 class="section-title"><?= htmlspecialchars($S['how_it_works']['title'] ?? 'How It Works') ?></h2>
        <p class="section-subtitle"><?= htmlspecialchars($S['how_it_works']['subtitle'] ?? 'Three simple steps to ship with confidence') ?></p>
        <div class="steps-row">
            <?php
            $steps = [];
            if (!empty($S['how_it_works']['content'])) {
                $steps = json_decode($S['how_it_works']['content'], true) ?: [];
            }
            if (empty($steps)) {
                $steps = [
                    ['icon' => '📋', 'title' => 'Book Your Shipment', 'desc' => 'Tell us where it\'s going and what you\'re sending. We handle the rest — from labeling to carrier coordination.'],
                    ['icon' => '📡', 'title' => 'Track in Real Time', 'desc' => 'Follow your shipment every step of the way. Live updates, milestone alerts, and a clear timeline from pickup to delivery.'],
                    ['icon' => '✅', 'title' => 'Delivered With Care', 'desc' => 'Get notified the moment your shipment arrives. Full delivery confirmation and proof every package reaches its destination.'],
                ];
            }
            foreach ($steps as $index => $step):
            ?>
            <div class="step-card">
                <div class="step-number"><?= $index + 1 ?></div>
                <div class="step-icon"><?= htmlspecialchars($step['icon'] ?? '') ?></div>
                <h3><?= htmlspecialchars($step['title'] ?? '') ?></h3>
                <p><?= htmlspecialchars($step['desc'] ?? '') ?></p>
            </div>
            <?php if ($index < count($steps) - 1): ?>
            <div class="step-connector">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#f97316" stroke-width="2"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="section section-image section-image-truck">
    <div class="section-image-overlay"></div>
    <div class="container">
        <div class="testimonial-strip">
            <div class="testimonial-text">
                <h2><?= htmlspecialchars($S['stats']['title'] ?? 'Reliable Shipping, Worldwide') ?></h2>
                <p><?= htmlspecialchars($S['stats']['subtitle'] ?? 'From small parcels to full freight loads — we connect you with trusted carriers across road, sea, and air networks.') ?></p>
                <?php
                $statsContent = $S['stats']['content'] ?? '';
                $statsItems = $statsContent ? explode(',', $statsContent) : ['500+','Routes Covered','99.2%','On-Time Delivery','50K+','Shipments Delivered'];
                ?>
                <?php if (count($statsItems) >= 2): ?>
                <div class="stats-row">
                    <?php for ($i = 0; $i < count($statsItems); $i += 2): ?>
                    <div class="stat-item">
                        <div class="stat-num"><?= htmlspecialchars(trim($statsItems[$i])) ?></div>
                        <div class="stat-label"><?= htmlspecialchars(trim($statsItems[$i+1] ?? '')) ?></div>
                    </div>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="section">
    <div class="container">
        <h2 class="section-title">Why Choose Us</h2>
        <p class="section-subtitle">Built for people who send and receive shipments every day</p>
        <div class="features-grid">
            <?php
            $features = [];
            if (!empty($S['features']['content'])) {
                $features = json_decode($S['features']['content'], true) ?: [];
            }
            if (empty($features)) {
                $features = [
                    ['icon' => '📍', 'title' => 'Live Tracking, Always', 'desc' => 'See exactly where your shipment is at any moment. No more guessing or waiting for phone calls — just real-time visibility.'],
                    ['icon' => '🔔', 'title' => 'Instant Alerts', 'desc' => 'Automatic email updates at every milestone. From pickup to delivery, you and your recipient stay informed without lifting a finger.'],
                    ['icon' => '🌍', 'title' => 'Ship Anywhere', 'desc' => 'Domestic or international, small box or full container — we coordinate with top carriers to get your cargo where it needs to go.'],
                    ['icon' => '🛡️', 'title' => 'Peace of Mind', 'desc' => 'Every shipment is handled with care. Clear tracking history, delivery confirmation, and dedicated support if you ever need us.'],
                    ['icon' => '📱', 'title' => 'Easy to Use', 'desc' => 'Simple tracking by number, no account needed. When you do create shipments, a clean interface makes it effortless.'],
                    ['icon' => '⚡', 'title' => 'Fast & Efficient', 'desc' => 'From booking to delivery, we streamline every step. Less waiting, more moving — because your time matters.'],
                ];
            }
            foreach ($features as $feature):
            ?>
            <div class="feature-card">
                <div class="feature-icon"><?= htmlspecialchars($feature['icon'] ?? '') ?></div>
                <h3><?= htmlspecialchars($feature['title'] ?? '') ?></h3>
                <p><?= htmlspecialchars($feature['desc'] ?? '') ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="section section-alt">
    <div class="container" style="text-align:center;">
        <h2 class="section-title"><?= htmlspecialchars($S['track_cta']['title'] ?? 'Track a Shipment') ?></h2>
        <p class="section-subtitle"><?= htmlspecialchars($S['track_cta']['subtitle'] ?? 'Have a tracking number? Check your shipment status in seconds.') ?></p>
        <div class="track-card">
            <form action="/track" method="GET" class="track-form">
                <input type="text" name="number" placeholder="e.g., FF-1712345678-ABCD" required>
                <button type="submit" class="btn btn-primary">Track Now</button>
            </form>
        </div>
    </div>
</div>

<div class="section section-cta">
    <div class="container" style="text-align:center;">
        <h2 class="section-title" style="color:#fff;"><?= htmlspecialchars($S['final_cta']['title'] ?? 'Ready to Get Started?') ?></h2>
        <p class="section-subtitle" style="color:#fdba74;"><?= htmlspecialchars($S['final_cta']['subtitle'] ?? 'Join thousands of satisfied customers — reliable shipping starts here.') ?></p>
        <div class="hero-actions" style="margin-top:1.5rem;">
            <a href="/about" class="btn btn-primary btn-lg">Learn More</a>
            <a href="/track" class="btn btn-outline btn-lg" style="border-color:#fdba74;color:#fff;">Track Now</a>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/layouts/main.php'; ?>
