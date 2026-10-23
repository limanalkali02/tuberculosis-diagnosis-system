<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
require_login();

$userId = $_SESSION['user_id'];
$errors = [];
$diagnosisId = isset($_GET['diagnosis_id']) ? (int) $_GET['diagnosis_id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['appointment_date'] ?? '';
    $time = $_POST['appointment_time'] ?? '';
    $notes = trim($_POST['notes'] ?? '');
    $diagnosisId = $_POST['diagnosis_id'] !== '' ? (int) $_POST['diagnosis_id'] : null;

    $today = date('Y-m-d');
    if ($date < $today) $errors[] = 'Please choose a date from today onward.';
    if ($time === '') $errors[] = 'Please choose a time.';

    if (!$errors) {
        $stmt = $pdo->prepare(
            'INSERT INTO appointments (user_id, diagnosis_id, appointment_date, appointment_time, notes)
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$userId, $diagnosisId, $date, $time, $notes]);
        $_SESSION['flash_success'] = 'Appointment requested for ' . date('d M Y', strtotime($date)) . '. Bayara Hospital will confirm it.';
        redirect('appointment.php');
    }
}

$stmt = $pdo->prepare(
    'SELECT * FROM appointments WHERE user_id = ? ORDER BY appointment_date DESC, appointment_time DESC'
);
$stmt->execute([$userId]);
$appointments = $stmt->fetchAll();

$pageTitle = 'Appointments';
require 'includes/header.php';
?>

<section class="section">
  <div class="container">
    <div class="eyebrow">Bayara Hospital</div>
    <h1>Appointments</h1>

    <?php if ($msg = flash('flash_success')): ?><div class="alert alert-success"><?= h($msg) ?></div><?php endif; ?>
    <?php if ($errors): ?>
      <div class="alert alert-error"><?php foreach ($errors as $e) echo '<div>' . h($e) . '</div>'; ?></div>
    <?php endif; ?>

    <div class="dash-grid">
      <div>
        <div class="card">
          <h3 class="mt-0">Book a new appointment</h3>
          <form method="post" action="appointment.php">
            <input type="hidden" name="diagnosis_id" value="<?= h((string) $diagnosisId) ?>">
            <label for="appointment_date">Date</label>
            <input type="date" id="appointment_date" name="appointment_date" min="<?= date('Y-m-d') ?>" required>

            <label for="appointment_time">Time</label>
            <input type="time" id="appointment_time" name="appointment_time" required>

            <label for="notes">Notes (optional)</label>
            <textarea id="notes" name="notes" rows="3" placeholder="Anything the clinic should know"></textarea>

            <button type="submit" class="btn btn-primary btn-block">Request appointment</button>
          </form>
        </div>
      </div>

      <div>
        <div class="card">
          <h3 class="mt-0">Your appointments</h3>
          <?php if (!$appointments): ?>
            <p>No appointments yet.</p>
          <?php else: ?>
            <?php foreach ($appointments as $a): ?>
              <div style="padding:12px 0; border-bottom:1px solid var(--line);">
                <strong><?= date('D, d M Y', strtotime($a['appointment_date'])) ?></strong> ·
                <?= date('g:ia', strtotime($a['appointment_time'])) ?><br>
                <span class="badge badge-<?= $a['status'] === 'Cancelled' ? 'high' : ($a['status'] === 'Completed' ? 'negative' : 'low') ?>">
                  <?= h($a['status']) ?>
                </span>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require 'includes/footer.php'; ?>
