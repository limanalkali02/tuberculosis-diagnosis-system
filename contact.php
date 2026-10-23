<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

$errors = [];
$sent = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '') $errors[] = 'Please enter your name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email.';
    if ($message === '') $errors[] = 'Please enter a message.';

    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO enquiries (name, email, message) VALUES (?, ?, ?)');
        $stmt->execute([$name, $email, $message]);
        $sent = true;
    }
}

$pageTitle = 'Contact';
require 'includes/header.php';
?>

<section class="section">
  <div class="container form-narrow">
    <div class="eyebrow">Get in touch</div>
    <h1>Contact us</h1>
    <p>Questions about the system or about TB screening at Bayara Hospital? Send us a message.</p>

    <?php if ($sent): ?>
      <div class="alert alert-success">Thank you — your message has been received. We'll respond by email.</div>
    <?php endif; ?>
    <?php if ($errors): ?>
      <div class="alert alert-error"><?php foreach ($errors as $e) echo '<div>' . h($e) . '</div>'; ?></div>
    <?php endif; ?>

    <form method="post" action="contact.php">
      <label for="name">Name</label>
      <input type="text" id="name" name="name" value="<?= h($_POST['name'] ?? '') ?>" required>

      <label for="email">Email</label>
      <input type="email" id="email" name="email" value="<?= h($_POST['email'] ?? '') ?>" required>

      <label for="message">Message</label>
      <textarea id="message" name="message" rows="5" required><?= h($_POST['message'] ?? '') ?></textarea>

      <button type="submit" class="btn btn-primary btn-block">Send message</button>
    </form>
  </div>
</section>

<?php require 'includes/footer.php'; ?>
