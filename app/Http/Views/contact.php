<?php $thisPage = 'contact'; ob_start(); ?>
<div class="hero hero-contact">
    <div class="hero-overlay"></div>
    <div class="container">
        <h1>Get in Touch</h1>
        <p>Have a question about your shipment? We're here to help.</p>
    </div>
</div>

<div class="section">
    <div class="container">
        <div class="contact-layout">
            <div class="contact-info">
                <h2>Contact Information</h2>
                <p style="color:#475569;margin-bottom:2rem;">Reach out to us — we typically respond within 24 hours.</p>

                <div class="contact-detail">
                    <div class="contact-icon">📧</div>
                    <div>
                        <div style="font-weight:600;">Email</div>
                        <div style="color:#64748b;font-size:0.9rem;"><?= htmlspecialchars($siteEmail ?? 'contact@freightforge.test') ?></div>
                    </div>
                </div>

                <div class="contact-detail">
                    <div class="contact-icon">📞</div>
                    <div>
                        <div style="font-weight:600;">Phone</div>
                        <div style="color:#64748b;font-size:0.9rem;"><?= htmlspecialchars($sitePhone ?? '+1 (555) 123-4567') ?></div>
                    </div>
                </div>

                <div class="contact-detail">
                    <div class="contact-icon">📍</div>
                    <div>
                        <div style="font-weight:600;">Address</div>
                        <div style="color:#64748b;font-size:0.9rem;"><?= nl2br(htmlspecialchars($siteAddress ?? "123 Logistics Ave, Suite 100\nPort City, PC 10001")) ?></div>
                    </div>
                </div>

                <div class="contact-detail">
                    <div class="contact-icon">🕐</div>
                    <div>
                        <div style="font-weight:600;">Business Hours</div>
                        <div style="color:#64748b;font-size:0.9rem;"><?= nl2br(htmlspecialchars($siteHours ?? "Mon — Fri: 8:00 AM — 6:00 PM\nSat: 9:00 AM — 2:00 PM")) ?></div>
                    </div>
                </div>
            </div>

            <div class="contact-form-wrap">
                <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>
                <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST" action="/contact" class="contact-form">
                    <h3 style="font-size:1.1rem;font-weight:600;margin-bottom:1.5rem;">Send Us a Message</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Your Name</label>
                            <input type="text" name="name" required placeholder="John Doe">
                        </div>
                        <div class="form-group">
                            <label>Your Email</label>
                            <input type="email" name="email" required placeholder="john@example.com">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Subject</label>
                        <input type="text" name="subject" required placeholder="How can we help?">
                    </div>
                    <div class="form-group">
                        <label>Message</label>
                        <textarea name="message" rows="5" required placeholder="Describe your question or concern..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .hero-contact { background: linear-gradient(135deg, rgba(15,23,42,0.85), rgba(15,23,42,0.85)), url('https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=1400&q=80') center/cover no-repeat; }
    .contact-layout { display: grid; grid-template-columns: 1fr 1.5fr; gap: 3rem; max-width: 1000px; margin: 0 auto; }
    .contact-detail { display: flex; gap: 1rem; align-items: flex-start; margin-bottom: 1.5rem; }
    .contact-icon { width: 2.5rem; height: 2.5rem; background: #fff7ed; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0; }
    .contact-form-wrap { background: #fff; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 2rem; }
    .contact-form .btn { width: 100%; justify-content: center; }
    @media (max-width: 768px) { .contact-layout { grid-template-columns: 1fr; } }
</style>
<?php $content = ob_get_clean(); require __DIR__ . '/layouts/main.php'; ?>
