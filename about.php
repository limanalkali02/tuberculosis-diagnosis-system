<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
$pageTitle = 'About';
require 'includes/header.php';
?>

<section class="section">
  <div class="container" style="max-width:720px;">
    <div class="eyebrow">About this project</div>
    <h1>Why an online TB diagnosis system</h1>
    <div class="pulse-divider"></div>
    <p>Tuberculosis remains one of Nigeria's leading infectious-disease killers,
      and an estimated 44% of cases go undiagnosed — driven by limited access
      to health facilities, inadequate laboratory infrastructure, and
      persistent stigma.</p>
    <p>This system, developed for Bayara Hospital in Bauchi, gives people a
      discreet, accessible first step: a knowledge-based screening tool that
      applies rule-based reasoning — built on the WHO four-symptom TB screen
      — to produce a preliminary risk assessment from any device with
      internet access.</p>
    <p>Users who screen positive are directed to the hospital for
      confirmatory testing via sputum microscopy, GeneXpert, or chest
      radiography. The platform complements, rather than replaces, existing
      facility-based diagnostic services.</p>
    <h3>Built with</h3>
    <p>HTML, Bootstrap-style CSS, JavaScript, PHP, and MySQL, following an
      iterative waterfall development methodology.</p>
  </div>
</section>

<?php require 'includes/footer.php'; ?>
