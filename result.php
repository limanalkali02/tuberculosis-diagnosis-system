<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
require_login();

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM diagnoses WHERE diagnosis_id = ? AND user_id = ?');
$stmt->execute([$id, $_SESSION['user_id']]);
$diagnosis = $stmt->fetch();

if (!$diagnosis) {
    redirect('dashboard.php');
}

$stmt = $pdo->prepare(
    'SELECT s.question_text, s.category, a.answer
     FROM diagnosis_answers a JOIN symptoms s ON s.symptom_id = a.symptom_id
     WHERE a.diagnosis_id = ?
     ORDER BY FIELD(s.category, "core", "additional", "risk_factor"), s.display_order'
);
$stmt->execute([$id]);
$answers = $stmt->fetchAll();

$pageTitle = 'Your result';
require 'includes/header.php';
?>

<section class="section">
  <div class="container" style="max-width:680px;">
    <div class="result-banner <?= strtolower($diagnosis['risk_level']) ?>">
      <div class="risk-level"><?= h($diagnosis['tb_status']) ?></div>
      <h2><?= h($diagnosis['risk_level']) ?> risk</h2>
      <p style="opacity:0.95; margin-bottom:14px;"><?= h($diagnosis['advice']) ?></p>
      <span class="score-pill">Symptom score: <?= (int) $diagnosis['total_score'] ?></span>
    </div>

    <?php if ($diagnosis['tb_status'] === 'Presumptive Positive'): ?>
      <div class="card" style="margin-bottom:24px;">
        <h3 class="mt-0">Next step</h3>
        <p>Book a confirmatory test at Bayara Hospital — sputum microscopy,
          GeneXpert, or chest radiograph — as advised above.</p>
        <a href="appointment.php?diagnosis_id=<?= $id ?>" class="btn btn-primary">Book an appointment</a>
      </div>
    <?php endif; ?>

    <div class="card">
      <h3 class="mt-0">Your answers</h3>
      <table>
        <tbody>
        <?php foreach ($answers as $a): ?>
          <tr>
            <td><?= h($a['question_text']) ?></td>
            <td style="width:70px; text-align:right;">
              <span class="badge <?= $a['answer'] ? 'badge-high' : 'badge-negative' ?>">
                <?= $a['answer'] ? 'Yes' : 'No' ?>
              </span>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <p style="margin-top:20px;">
      <a href="dashboard.php">&larr; Back to dashboard</a> &nbsp;·&nbsp;
      <a href="history.php">View all past results</a>
    </p>
  </div>
</section>

<?php require 'includes/footer.php'; ?>
