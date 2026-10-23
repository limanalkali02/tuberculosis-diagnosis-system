<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
require_login();

$symptoms = $pdo->query(
    'SELECT * FROM symptoms ORDER BY FIELD(category, "core", "additional", "risk_factor"), display_order'
)->fetchAll();

$categoryLabels = [
    'core'        => 'WHO four-symptom screen',
    'additional'  => 'Additional symptoms',
    'risk_factor' => 'Risk factors',
];

$pageTitle = 'TB Screening';
require 'includes/header.php';
?>

<section class="section">
  <div class="container form-narrow" style="max-width:640px;">
    <div class="eyebrow">Confidential screening</div>
    <h1>Answer honestly — it takes about 2 minutes</h1>
    <p>For each question, select Yes or No. This is a preliminary screen, not
      a diagnosis; a positive result means you should visit Bayara Hospital
      for confirmatory testing.</p>

    <?php if ($msg = flash('flash_error')): ?>
      <div class="alert alert-error"><?= h($msg) ?></div>
    <?php endif; ?>

    <form method="post" action="process_diagnosis.php">
      <?php $lastCategory = null; foreach ($symptoms as $s): ?>
        <?php if ($s['category'] !== $lastCategory): $lastCategory = $s['category']; ?>
          <div class="q-category-label"><?= h($categoryLabels[$s['category']]) ?></div>
        <?php endif; ?>
        <div class="q-item">
          <div class="q-text"><?= h($s['question_text']) ?></div>
          <div class="q-options">
            <label><input type="radio" name="symptom_<?= $s['symptom_id'] ?>" value="1" required> Yes</label>
            <label><input type="radio" name="symptom_<?= $s['symptom_id'] ?>" value="0"> No</label>
          </div>
        </div>
      <?php endforeach; ?>

      <button type="submit" class="btn btn-primary btn-block" style="margin-top:20px;">Get my result</button>
    </form>
  </div>
</section>

<?php require 'includes/footer.php'; ?>
