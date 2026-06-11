<?php $thisPage = 'track'; ob_start(); ?>
<div class="hero hero-track">
    <div class="hero-overlay"></div>
    <div class="container">
        <h1>Track Your Shipment</h1>
        <p>Enter your tracking number to see exactly where your package is right now.</p>
    </div>
</div>

<div class="section">
    <div class="container">
        <div class="track-card" style="max-width:600px;margin:0 auto;">
            <div class="track-card-icon">🔍</div>
            <h2>Search Shipment</h2>
            <p>Enter the tracking number provided by your sender.</p>
            <form action="/track" method="GET" class="track-form">
                <input type="text" name="number" placeholder="Tracking number (e.g., FF-...)" value="<?= htmlspecialchars($trackingNumber ?? '') ?>" required>
                <button type="submit" class="btn btn-primary">Track</button>
            </form>
        </div>

        <?php if (isset($shipment) && $shipment): ?>
        <div class="card result-card" style="max-width:700px;margin:2rem auto 0;">
            <div class="card-header">
                <span>Shipment Details</span>
                <span class="status-badge status-<?= $shipment['status'] ?>"><?= str_replace('_', ' ', ucfirst($shipment['status'])) ?></span>
            </div>
            <div class="tracking-number-display">
                <span class="tracking-label">Tracking Number</span>
                <span class="tracking-value"><?= htmlspecialchars($shipment['tracking_number']) ?></span>
            </div>
            <div class="route-display">
                <div class="route-endpoint">
                    <div class="route-city"><?= htmlspecialchars($shipment['origin']) ?></div>
                    <div class="route-name"><?= htmlspecialchars($shipment['sender_name']) ?></div>
                </div>
                <div class="route-line">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#f97316" stroke-width="2.5"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg>
                </div>
                <div class="route-endpoint">
                    <div class="route-city"><?= htmlspecialchars($shipment['destination']) ?></div>
                    <div class="route-name"><?= htmlspecialchars($shipment['recipient_name']) ?></div>
                </div>
            </div>
            <?php if (!empty($shipment['description'])): ?>
            <div class="shipment-note">
                <?= htmlspecialchars($shipment['description']) ?>
            </div>
            <?php endif; ?>
            <?php if (!empty($statusHistory)): ?>
            <div class="timeline-header">Journey Timeline</div>
            <div class="tracking-timeline">
                <?php $lastIdx = count($statusHistory) - 1; ?>
                <?php foreach ($statusHistory as $i => $sh):
                    $isLast = $i === $lastIdx;
                    $stepClass = $isLast ? 'active' : 'completed';
                ?>
                <div class="tracking-step <?= $stepClass ?>">
                    <div class="tracking-dot"><?= $isLast ? '●' : '✓' ?></div>
                    <div class="tracking-content">
                        <h4><?= str_replace('_', ' ', ucfirst($sh['status'])) ?></h4>
                        <p><?= date('M j, Y g:i A', strtotime($sh['created_at'])) ?></p>
                        <?php if (!empty($sh['remark'])): ?>
                        <p style="margin-top:0.25rem;font-style:italic;color:#64748b;">"<?= htmlspecialchars($sh['remark']) ?>"</p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php elseif (isset($error)): ?>
        <div class="alert alert-error" style="max-width:600px;margin:2rem auto 0;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/layouts/main.php'; ?>
