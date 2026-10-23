<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
$pageTitle = 'Home';
require 'includes/header.php';
?>

<header class="hero">
  <div class="container">
    <div class="eyebrow">Bayara Hospital · Bauchi State</div>
    <h1>Know your TB risk, privately, before you ever walk into a clinic.</h1>
    <p class="lede">A free online screening tool built on the WHO four-symptom
      TB screen. Answer a short questionnaire, get an instant risk assessment,
      and — if needed — book a confidential appointment for confirmatory testing.</p>
    <div class="hero-actions">
      <?php if (is_logged_in()): ?>
        <a href="diagnose.php" class="btn btn-primary">Start screening</a>
        <a href="dashboard.php" class="btn btn-outline">Go to dashboard</a>
      <?php else: ?>
        <a href="register.php" class="btn btn-primary">Create a free account</a>
        <a href="login.php" class="btn btn-outline">Log in</a>
      <?php endif; ?>
    </div>
    <div class="hero-stats">
      <div><strong>44%</strong><span>of TB cases in Nigeria go undiagnosed</span></div>
      <div><strong>10–15</strong><span>people one untreated case can infect yearly</span></div>
      <div><strong>2 min</strong><span>to complete the screening questionnaire</span></div>
    </div>
  </div>
</header>

<section class="section">
  <div class="container">
    <div class="eyebrow">How it works</div>
    <h2>Three steps to a preliminary answer</h2>
    <div class="pulse-divider"></div>
    <div class="card-grid">
      <div class="card">
        <div class="feature-icon">1</div>
        <h3>Register &amp; answer</h3>
        <p>Create a confidential account and answer questions about your
          symptoms — cough, fever, night sweats, weight loss, and known
          risk factors.</p>
      </div>
      <div class="card">
        <div class="feature-icon">2</div>
        <h3>Get your risk level</h3>
        <p>Our rule-based knowledge system, built from WHO screening
          guidelines, scores your answers instantly and explains what the
          result means.</p>
      </div>
      <div class="card">
        <div class="feature-icon">3</div>
        <h3>Book confirmatory testing</h3>
        <p>If your screen is positive, schedule a visit to Bayara Hospital
          for sputum microscopy, GeneXpert, or a chest X-ray — no waiting
          in a queue to be seen first.</p>
      </div>
    </div>
  </div>
</section>

<section class="section" style="background:var(--paper-dim);">
  <div class="container text-center">
    <h2>Your symptoms stay confidential</h2>
    <p style="max-width:560px;margin:0 auto 24px;">
      Stigma keeps people from seeking care. This platform lets you check
      your risk from anywhere, at any time, without being seen at a clinic.
    </p>
    <?php if (!is_logged_in()): ?>
      <a href="register.php" class="btn btn-teal">Get started</a>
    <?php endif; ?>
  </div>
</section>

<?php require 'includes/footer.php'; ?>
