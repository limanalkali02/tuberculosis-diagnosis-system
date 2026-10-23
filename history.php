<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
require_login();

$stmt = $pdo->prepare('SELECT * FROM diagnoses WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$_SESSION['user_id']]);
$records = $stmt->fetchAll();

$pageTitle = 'History';
require 'includes/header.php';
?>

<section class="section">
  <div class="container">
    <div class="eyebrow">Your records</div>
    <h1>Screening history</h1>

    <?php if (!$records): ?>
      <div class="card text-center">
        <p>You have no screening records yet.</p>
        <a href="diagnose.php" class="btn btn-primary">Start your first screening</a>
      </div>
    <?php else: ?>
      <div class="card" style="padding:0; overflow:hidden;">
        <table>
          <thead>
            <tr><th>Date</th><th>Score</th><th>Risk level</th><th>Status</th><th></th></tr>
          </thead>
          <tbody>
          <?php foreach ($records as $r): ?>
            <tr>
              <td><?= date('d M Y, g:ia', strtotime($r['created_at'])) ?></td>
              <td><?= (int) $r['total_score'] ?></td>
              <td><span class="badge badge-<?= strtolower($r['risk_level']) ?>"><?= h($r['risk_level']) ?></span></td>
              <td><?= h($r['tb_status']) ?></td>
              <td><a href="result.php?id=<?= (int) $r['diagnosis_id'] ?>" class="btn btn-outline btn-sm" style="border-color:var(--line); color:var(--teal-700);">View</a></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php require 'includes/footer.php'; ?>
