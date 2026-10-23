<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_admin();

$enquiries = $pdo->query('SELECT * FROM enquiries ORDER BY created_at DESC')->fetchAll();

$pageTitle = 'Enquiries';
require '../includes/header.php';
?>

<section class="section">
  <div class="container">
    <div class="eyebrow">Admin</div>
    <h1>Contact enquiries</h1>
    <div class="card" style="padding:0;">
      <table>
        <thead><tr><th>Name</th><th>Email</th><th>Message</th><th>Date</th></tr></thead>
        <tbody>
        <?php foreach ($enquiries as $e): ?>
          <tr>
            <td><?= h($e['name']) ?></td>
            <td><?= h($e['email']) ?></td>
            <td><?= h($e['message']) ?></td>
            <td><?= date('d M Y', strtotime($e['created_at'])) ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$enquiries): ?><tr><td colspan="4">No enquiries yet.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
    <p style="margin-top:16px;"><a href="index.php">&larr; Back to admin dashboard</a></p>
  </div>
</section>

<?php require '../includes/footer.php'; ?>
