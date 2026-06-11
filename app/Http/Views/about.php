<?php $thisPage = 'about'; ob_start(); ?>
<div class="hero hero-about">
    <div class="hero-overlay"></div>
    <div class="container">
        <h1>Shipping Made Simple</h1>
        <p>We connect people with the shipments that matter — across town or across the ocean.</p>
    </div>
</div>

<div class="section">
    <div class="container">
        <div class="about-story">
            <div class="about-text">
                <h2>Our Promise</h2>
                <p>Every day, thousands of packages move between businesses, families, and communities. We believe every shipment should be trackable, predictable, and worry-free.</p>
                <p>Whether you're sending a single parcel or managing frequent freight, our platform gives you the visibility you need — without the complexity.</p>
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
        <h2 class="section-title">What We Offer</h2>
        <p class="section-subtitle">Clear, reliable features that make shipping easier</p>
        <div class="about-grid">
            <div class="about-card">
                <div class="about-card-icon">📦</div>
                <h3>Package Tracking</h3>
                <p>Real-time tracking with detailed timeline. Know where your shipment is and when it will arrive.</p>
            </div>
            <div class="about-card">
                <div class="about-card-icon">🚚</div>
                <h3>Multiple Carriers</h3>
                <p>We work with trusted carriers across road, air, and sea to get your cargo where it needs to go.</p>
            </div>
            <div class="about-card">
                <div class="about-card-icon">📬</div>
                <h3>Email Notifications</h3>
                <p>Automatic updates sent to you and your recipient. No manual check-ins required.</p>
            </div>
            <div class="about-card">
                <div class="about-card-icon">🔄</div>
                <h3>Easy Returns</h3>
                <p>Simple reverse logistics for when shipments need to come back. Same tracking, same peace of mind.</p>
            </div>
        </div>
    </div>
</div>

<div class="section">
    <div class="container" style="text-align:center;">
        <h2 class="section-title">Trusted by Businesses Big and Small</h2>
        <p class="section-subtitle" style="max-width:700px;margin-left:auto;margin-right:auto;">From local shops to global enterprises, companies rely on us to keep their shipments moving and their customers informed.</p>
        <div class="stats-row" style="justify-content:center;margin-top:2rem;">
            <div class="stat-item">
                <div class="stat-num">50K+</div>
                <div class="stat-label">Shipments Delivered</div>
            </div>
            <div class="stat-item">
                <div class="stat-num">500+</div>
                <div class="stat-label">Cities Covered</div>
            </div>
            <div class="stat-item">
                <div class="stat-num">98%</div>
                <div class="stat-label">Satisfaction Rate</div>
            </div>
        </div>
    </div>
</div>

<div class="section section-cta" style="background:linear-gradient(135deg, #1e293b, #0f172a);">
    <div class="container" style="text-align:center;">
        <h2 class="section-title" style="color:#fff;">Have a Question?</h2>
        <p class="section-subtitle" style="color:#94a3b8;max-width:500px;margin:0 auto 1.5rem;">We're here to help with your shipments, tracking, or anything you need.</p>
        <a href="/" class="btn btn-primary btn-lg">Get In Touch</a>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/layouts/main.php'; ?>
