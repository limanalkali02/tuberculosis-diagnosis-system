<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_admin();

$totalUsers = $pdo->query("SELECT COUNT(*) c FROM users WHERE role='patient'")->fetch()['c'];
$totalScreenings = $pdo->query("SELECT COUNT(*) c FROM diagnoses")->fetch()['c'];
$positiveScreenings = $pdo->query("SELECT COUNT(*) c FROM diagnoses WHERE tb_status='Presumptive Positive'")->fetch()['c'];
$pendingAppts = $pdo->query("SELECT COUNT(*) c FROM appointments WHERE status='Pending'")->fetch()['c'];

$recentDiagnoses = $pdo->query(
    "SELECT d.*, u.full_name FROM diagnoses d JOIN users u ON u.user_id = d.user_id
     ORDER BY d.created_at DESC LIMIT 10"
)->fetchAll();

$upcomingAppts = $pdo->query(
    "SELECT a.*, u.full_name FROM appointments a JOIN users u ON u.user_id = a.user_id
     WHERE a.status IN ('Pending','Confirmed')
     ORDER BY a.appointment_date ASC LIMIT 10"
)->fetchAll();

$pageTitle = 'Admin';
require '../includes/header.php';
?>

<section class="section">
  <div class="container">
    <div class="eyebrow">Bayara Hospital · Staff view</div>
    <h1>Admin dashboard</h1>

    <div class="card-grid" style="margin-bottom:30px;">
      <div class="card"><div class="eyebrow">Patients</div><h2 class="mb-0"><?= (int) $totalUsers ?></h2></div>
      <div class="card"><div class="eyebrow">Screenings</div><h2 class="mb-0"><?= (int) $totalScreenings ?></h2></div>
      <div class="card"><div class="eyebrow">Presumptive positive</div><h2 class="mb-0"><?= (int) $positiveScreenings ?></h2></div>
    </div>

    <div class="dash-grid">
      <div>
        <div class="card">
          <h3 class="mt-0">Recent screenings</h3>
          <table>
            <thead><tr><th>Patient</th><th>Date</th><th>Risk</th><th>Score</th></tr></thead>
            <tbody>
            <?php foreach ($recentDiagnoses as $d): ?>
              <tr>
                <td><?= h($d['full_name']) ?></td>
                <td><?= date('d M Y', strtotime($d['created_at'])) ?></td>
                <td><span class="badge badge-<?= strtolower($d['risk_level']) ?>"><?= h($d['risk_level']) ?></span></td>
                <td><?= (int) $d['total_score'] ?></td>
              </tr>
            <?php endforeach; ?>
            <?php if (!$recentDiagnoses): ?><tr><td colspan="4">No screenings yet.</td></tr><?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
      <div>
        <div class="card">
          <h3 class="mt-0">Upcoming appointments (<?= (int) $pendingAppts ?> pending)</h3>
          <?php foreach ($upcomingAppts as $a): ?>
            <div style="padding:10px 0; border-bottom:1px solid var(--line);">
              <strong><?= h($a['full_name']) ?></strong><br>
              <?= date('d M Y', strtotime($a['appointment_date'])) ?> ·
              <?= date('g:ia', strtotime($a['appointment_time'])) ?>
              &nbsp;<a href="update_appointment.php?id=<?= (int) $a['appointment_id'] ?>&status=Confirmed" class="btn btn-sm btn-teal">Confirm</a>
              <a href="update_appointment.php?id=<?= (int) $a['appointment_id'] ?>&status=Cancelled" class="btn btn-sm btn-danger">Cancel</a>
            </div>
          <?php endforeach; ?>
          <?php if (!$upcomingAppts): ?><p>No pending appointments.</p><?php endif; ?>
          <a href="enquiries.php" class="btn btn-outline btn-sm" style="border-color:var(--line); color:var(--teal-700); margin-top:14px;">View enquiries</a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require '../includes/footer.php'; ?>
