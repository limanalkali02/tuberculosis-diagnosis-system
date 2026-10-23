<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

if (is_logged_in()) {
    redirect('dashboard.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id']   = $user['user_id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role']      = $user['role'];
        redirect($user['role'] === 'admin' ? 'admin/index.php' : 'dashboard.php');
    } else {
        $errors[] = 'Incorrect email or password.';
    }
}

$pageTitle = 'Log in';
require 'includes/header.php';
?>

<section class="section">
  <div class="container form-narrow">
    <h1>Log in</h1>
    <p>Access your TB screening history and appointments.</p>

    <?php if ($msg = flash('flash_error')): ?>
      <div class="alert alert-error"><?= h($msg) ?></div>
    <?php endif; ?>
    <?php if ($errors): ?>
      <div class="alert alert-error">
        <?php foreach ($errors as $e) echo '<div>' . h($e) . '</div>'; ?>
      </div>
    <?php endif; ?>

    <form method="post" action="login.php" novalidate>
      <label for="email">Email address</label>
      <input type="email" id="email" name="email" value="<?= h($_POST['email'] ?? '') ?>" required>

      <label for="password">Password</label>
      <input type="password" id="password" name="password" required>

      <button type="submit" class="btn btn-primary btn-block">Log in</button>
    </form>

    <p style="margin-top:16px;">Don't have an account? <a href="register.php">Register</a></p>
  </div>
</section>

<?php require 'includes/footer.php'; ?>
