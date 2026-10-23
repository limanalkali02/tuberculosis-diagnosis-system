<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
require_login();

$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare(
    'SELECT * FROM diagnoses WHERE user_id = ? ORDER BY created_at DESC LIMIT 1'
);
$stmt->execute([$userId]);
$latest = $stmt->fetch();

$stmt = $pdo->prepare(
    "SELECT * FROM appointments WHERE user_id = ? AND status IN ('Pending','Confirmed')
     ORDER BY appointment_date ASC, appointment_time ASC LIMIT 1"
);
$stmt->execute([$userId]);
$nextAppt = $stmt->fetch();

$pageTitle = 'Dashboard';
require 'includes/header.php';
?>

<section class="section">
  <div class="container">
    <div class="eyebrow">Welcome back</div>
    <h1><?= h($_SESSION['full_name']) ?></h1>

    <?php if ($msg = flash('flash_success')): ?>
      <div class="alert alert-success"><?= h($msg) ?></div>
    <?php endif; ?>

    <div class="dash-grid">
      <div>
        <div class="card" style="margin-bottom:20px;">
          <h3 class="mt-0">Latest screening result</h3>
          <?php if ($latest): ?>
            <p>
              <span class="badge badge-<?= strtolower($latest['risk_level']) ?>"><?= h($latest['risk_level']) ?> risk</span>
              &nbsp; Screened on <?= date('d M Y, g:ia', strtotime($latest['created_at'])) ?>
            </p>
            <p><?= h($latest['advice']) ?></p>
            <a href="result.php?id=<?= (int) $latest['diagnosis_id'] ?>" class="btn btn-teal btn-sm">View full result</a>
          <?php else: ?>
            <p>You haven't taken the TB screening yet.</p>
          <?php endif; ?>
        </div>

        <div class="card">
          <div class="flex-between">
            <h3 class="mt-0 mb-0">Start a new screening</h3>
            <a href="diagnose.php" class="btn btn-primary btn-sm">Begin</a>
          </div>
          <p style="margin-top:10px;">Takes about 2 minutes. Answer honestly for the most accurate risk estimate.</p>
        </div>
      </div>

      <div>
        <div class="card">
          <h3 class="mt-0">Next appointment</h3>
          <?php if ($nextAppt): ?>
            <p><strong><?= date('D, d M Y', strtotime($nextAppt['appointment_date'])) ?></strong><br>
              <?= date('g:ia', strtotime($nextAppt['appointment_time'])) ?> ·
              <span class="badge badge-low"><?= h($nextAppt['status']) ?></span></p>
          <?php else: ?>
            <p>No upcoming appointment.</p>
          <?php endif; ?>
          <a href="appointment.php" class="btn btn-outline btn-sm" style="border-color:var(--line); color:var(--teal-700);">
            <?= $nextAppt ? 'Manage appointments' : 'Book an appointment' ?>
          </a>
        </div>
        <div class="card" style="margin-top:20px;">
          <h3 class="mt-0">Your history</h3>
          <p>Review all past screenings and results.</p>
          <a href="history.php" class="btn btn-outline btn-sm" style="border-color:var(--line); color:var(--teal-700);">View history</a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require 'includes/footer.php'; ?>
